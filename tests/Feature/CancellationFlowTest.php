<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\SafeStockReversalService;
use App\Livewire\OrdersList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class CancellationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_direct_unit_order_cancellation_restores_exact_stock(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Direct Item', 'piece', 1, 7);
        $order = $this->stockedOrder($staff, $unit, 3);

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'ยกเลิก',
                'cancel_reason' => 'customer request',
            ]
        )->assertRedirect();

        $this->assertSame(10, (int) $stock->fresh()->store_stock);
        $this->assertSame('ยกเลิก', $order->fresh()->status);
        $this->assertDatabaseHas('product_stock_movements', [
            'order_id' => $order->id,
            'product_id' => $unit->product_id,
            'type' => 'in',
            'quantity' => 3,
            'unit_quantity' => 1,
            'unit' => 'piece',
        ]);
    }

    public function test_one_box_returns_one_box_not_twelve_boxes(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Box Item', 'box', 12, 4);
        $order = $this->stockedOrder(
            $staff,
            $unit,
            1,
            'processing'
        );

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'wrong order',
            ]
        )->assertRedirect();

        $this->assertSame(5, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseHas('product_stock_movements', [
            'order_id' => $order->id,
            'type' => 'in',
            'quantity' => 1,
            'unit_quantity' => 12,
            'unit' => 'box',
        ]);
    }

    public function test_second_cancellation_does_not_increase_stock(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Repeat Item', 'piece', 1, 8);
        $order = $this->stockedOrder(
            $staff,
            $unit,
            2,
            'processing'
        );

        $payload = [
            'status' => 'cancelled',
            'cancel_reason' => 'cancel once',
        ];

        $this->actingAs($staff)
            ->patchJson(route('orders.updateStatus', $order), $payload)
            ->assertRedirect();

        $this->actingAs($staff)
            ->patchJson(route('orders.updateStatus', $order), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame(10, (int) $stock->fresh()->store_stock);
        $this->assertSame(
            1,
            ProductStockMovement::where('order_id', $order->id)
                ->where('type', 'in')
                ->count()
        );
    }

    public function test_exception_during_reversal_rolls_back_stock_movement_and_status(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Rollback Item', 'piece', 1, 9);
        $order = $this->stockedOrder(
            $staff,
            $unit,
            1,
            'processing'
        );

        $this->mock(
            SafeStockReversalService::class,
            function (MockInterface $mock) use ($stock, $unit): void {
                $mock->shouldReceive('reverseOrder')
                    ->once()
                    ->andReturnUsing(function (Order $order) use ($stock, $unit): void {
                        ProductStocks::whereKey($stock->id)
                            ->increment('store_stock');

                        ProductStockMovement::create([
                            'order_id' => $order->id,
                            'product_id' => $unit->product_id,
                            'type' => 'in',
                            'quantity' => 1,
                            'unit_quantity' => 1,
                            'unit' => 'piece',
                            'location' => 'store',
                            'note' => 'forced rollback',
                        ]);

                        throw new RuntimeException('forced reversal failure');
                    });
            }
        );

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'rollback test',
            ]
        )->assertServerError();

        $this->assertSame(9, (int) $stock->fresh()->store_stock);
        $this->assertSame('processing', $order->fresh()->status);
        $this->assertSame(
            0,
            ProductStockMovement::where('order_id', $order->id)
                ->where('type', 'in')
                ->count()
        );
    }

    public function test_member_cannot_cancel_sale_or_order(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit] = $this->productUnit('Protected Item', 'piece', 1, 10);
        $sale = $this->sale($staff, $unit);
        $order = $this->order($member, $unit, 'processing', $staff);

        $this->actingAs($member)->deleteJson(
            route('staff.sales.destroy', $sale),
            ['cancel_reason' => 'not allowed']
        )->assertForbidden();

        $this->actingAs($member)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'not allowed',
            ]
        )->assertForbidden();
    }

    public function test_staff_can_only_cancel_records_assigned_to_or_owned_by_them(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $otherStaff = User::factory()->create(['role' => 'staff']);
        [$unit] = $this->productUnit('Owned Item', 'piece', 1, 10);
        $otherSale = $this->sale($otherStaff, $unit);
        $otherOrder = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'processing',
            $otherStaff
        );
        $ownOrder = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'processing',
            $staff
        );

        $this->actingAs($staff)->deleteJson(
            route('staff.sales.destroy', $otherSale),
            ['cancel_reason' => 'not mine']
        )->assertForbidden();

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $otherOrder),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'not mine',
            ]
        )->assertForbidden();

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $ownOrder),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'assigned to me',
            ]
        )->assertRedirect();

        $this->assertSame('cancelled', $ownOrder->fresh()->status);
    }

    public function test_conversion_order_is_blocked_when_reversal_cannot_be_proven(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $productId = $this->product('Converted Item');
        [$piece, $pieceStock] = $this->unitForProduct(
            $productId,
            'piece',
            1,
            9
        );
        [$box, $boxStock] = $this->unitForProduct(
            $productId,
            'box',
            12,
            0
        );
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $piece,
            'processing',
            $staff,
            5
        );

        $this->movement($order, $piece, 2, 'out');
        $this->movement($order, $box, 1, 'out');
        $this->movement($order, $piece, 3, 'out');

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'unsafe conversion',
            ]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame(9, (int) $pieceStock->fresh()->store_stock);
        $this->assertSame(0, (int) $boxStock->fresh()->store_stock);
        $this->assertSame('processing', $order->fresh()->status);
        $this->assertDatabaseMissing('product_stock_movements', [
            'order_id' => $order->id,
            'type' => 'in',
        ]);
    }

    public function test_legacy_sale_is_blocked_without_deleting_financial_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$unit, $stock] = $this->productUnit('Legacy Sale', 'piece', 1, 6);
        $sale = $this->sale($admin, $unit, 2);

        ProductStockMovement::create([
            'product_id' => $unit->product_id,
            'type' => 'out',
            'quantity' => 2,
            'unit_quantity' => 1,
            'unit' => 'piece',
            'location' => 'store',
            'note' => 'legacy sale without sale_id',
        ]);

        $this->actingAs($admin)->deleteJson(
            route('staff.sales.destroy', $sale),
            ['cancel_reason' => 'legacy data']
        )->assertUnprocessable()
            ->assertJsonValidationErrors('cancel_reason');

        $this->assertDatabaseHas('sales', ['id' => $sale->id]);
        $this->assertDatabaseHas('sale_items', ['sale_id' => $sale->id]);
        $this->assertSame(6, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseMissing('product_stock_movements', [
            'type' => 'in',
            'product_id' => $unit->product_id,
        ]);
    }

    public function test_online_cancellation_does_not_create_duplicate_reversal_movements(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Online Item', 'piece', 1, 3);
        $order = $this->stockedOrder(
            $staff,
            $unit,
            1,
            'processing'
        );

        $first = [
            'status' => 'cancelled',
            'cancel_reason' => 'online cancellation',
        ];

        $this->actingAs($staff)
            ->patchJson(route('orders.updateStatus', $order), $first)
            ->assertRedirect();

        $this->actingAs($staff)
            ->patchJson(route('orders.updateStatus', $order), $first)
            ->assertUnprocessable();

        $this->assertSame(4, (int) $stock->fresh()->store_stock);
        $this->assertSame(
            1,
            ProductStockMovement::where('order_id', $order->id)
                ->where('type', 'in')
                ->count()
        );
    }

    public function test_order_without_stock_deduction_is_cancelled_without_returning_stock(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Reserved Item', 'piece', 1, 10);
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'processing',
            $staff,
            2
        );

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'never deducted',
            ]
        )->assertRedirect();

        $this->assertSame(10, (int) $stock->fresh()->store_stock);
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit] = $this->productUnit('Final Item', 'piece', 1, 10);
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'completed',
            $staff
        );

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'too late',
            ]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame('completed', $order->fresh()->status);
    }

    public function test_cancelled_order_cannot_be_reopened_by_accept_endpoint(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit] = $this->productUnit('Terminal Item', 'piece', 1, 10);
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'cancelled'
        );

        $this->actingAs($staff)->postJson(
            route('orders.accept', $order)
        )->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertNull($order->fresh()->assigned_to);
    }

    public function test_cancelled_order_cannot_be_reassigned_by_livewire_action(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit] = $this->productUnit('Livewire Terminal Item', 'piece', 1, 10);
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'ยกเลิก'
        );

        Livewire::actingAs($staff)
            ->test(OrdersList::class)
            ->call('assignOrder', $order->id)
            ->assertHasErrors('status');

        $this->assertSame('ยกเลิก', $order->fresh()->status);
        $this->assertNull($order->fresh()->assigned_to);
    }

    public function test_controller_opens_a_transaction_before_stock_reversal(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit] = $this->productUnit('Lock Item', 'piece', 1, 10);
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'processing',
            $staff
        );
        $baselineLevel = DB::transactionLevel();
        $reversalLevel = 0;

        $this->mock(
            SafeStockReversalService::class,
            function (MockInterface $mock) use (&$reversalLevel): void {
                $mock->shouldReceive('reverseOrder')
                    ->once()
                    ->andReturnUsing(function () use (&$reversalLevel): void {
                        $reversalLevel = DB::transactionLevel();
                    });
            }
        );

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'transaction check',
            ]
        )->assertRedirect();

        $this->assertGreaterThan($baselineLevel, $reversalLevel);
    }

    public function test_duplicate_stock_rows_block_reversal_before_any_change(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Duplicate Stock', 'piece', 1, 4);
        $order = $this->stockedOrder(
            $staff,
            $unit,
            1,
            'processing'
        );

        $duplicateId = DB::table('product_stocks')->insertGetId([
            'product_id' => $unit->product_id,
            'unit_id' => $unit->id,
            'warehouse_stock' => 0,
            'store_stock' => 20,
            'track_stock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($staff)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'ambiguous stock',
            ]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame(4, (int) $stock->fresh()->store_stock);
        $this->assertSame(
            20,
            (int) ProductStocks::findOrFail($duplicateId)->store_stock
        );
        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_cancelled_order_status_and_stock_remain_consistent(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit('Consistent Item', 'piece', 1, 5);
        $order = $this->stockedOrder(
            $staff,
            $unit,
            2,
            'processing'
        );

        $this->actingAs($manager)->patchJson(
            route('orders.updateStatus', $order),
            [
                'status' => 'cancelled',
                'cancel_reason' => 'manager approved',
            ]
        )->assertRedirect();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('manager approved', $order->fresh()->cancel_reason);
        $this->assertSame(7, (int) $stock->fresh()->store_stock);
        $this->assertSame(1, ProductStockMovement::where([
            'order_id' => $order->id,
            'type' => 'in',
        ])->count());
    }

    public function test_cancelled_online_order_is_not_included_in_sales_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$unit] = $this->productUnit('Report Item', 'piece', 1, 10);
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            'ยกเลิก',
            $admin
        );

        $this->actingAs($admin)
            ->get(route('reports.daily'))
            ->assertOk()
            ->assertViewHas(
                'onlineOrders',
                fn ($orders) => !$orders->contains('id', $order->id)
            )
            ->assertViewHas('onlineSalesTotal', 0);
    }

    private function stockedOrder(
        User $staff,
        ProductUnit $unit,
        int $quantity,
        string $status = 'กำลังจัดสินค้า'
    ): Order {
        $order = $this->order(
            User::factory()->create(['role' => 'member']),
            $unit,
            $status,
            $staff,
            $quantity
        );

        $this->movement($order, $unit, $quantity, 'out');

        return $order;
    }

    private function order(
        User $member,
        ProductUnit $unit,
        string $status,
        ?User $staff = null,
        int $quantity = 1
    ): Order {
        $order = Order::create([
            'order_code' => 'ORD-' . fake()->unique()->numerify('######'),
            'user_id' => $member->id,
            'assigned_to' => $staff?->id,
            'payment_method' => 'cash',
            'status' => $status,
            'total_amount' => 10 * $quantity,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_unit_id' => $unit->id,
            'quantity' => $quantity,
            'price' => 10,
            'total' => 10 * $quantity,
        ]);

        return $order;
    }

    private function sale(
        User $staff,
        ProductUnit $unit,
        int $quantity = 1
    ): Sale {
        $sale = Sale::create([
            'staff_id' => $staff->id,
            'sale_date' => now(),
            'sale_type' => 'retail',
            'total_price' => 10 * $quantity,
            'payment_method' => 'cash',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_unit_id' => $unit->id,
            'quantity' => $quantity,
            'unit_quantity' => $unit->unit_quantity,
            'price' => 10,
            'cost' => 1,
        ]);

        return $sale;
    }

    private function movement(
        Order $order,
        ProductUnit $unit,
        int $quantity,
        string $type
    ): ProductStockMovement {
        return ProductStockMovement::create([
            'order_id' => $order->id,
            'product_id' => $unit->product_id,
            'type' => $type,
            'quantity' => $quantity,
            'unit_quantity' => $unit->unit_quantity,
            'unit' => $unit->unit_name,
            'location' => 'store',
            'note' => 'test stock movement',
        ]);
    }

    private function productUnit(
        string $productName,
        string $unitName,
        int $unitQuantity,
        int $storeStock
    ): array {
        return $this->unitForProduct(
            $this->product($productName),
            $unitName,
            $unitQuantity,
            $storeStock
        );
    }

    private function product(string $name): int
    {
        return DB::table('products')->insertGetId([
            'name' => $name,
            'is_active' => true,
            'is_online' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function unitForProduct(
        int $productId,
        string $unitName,
        int $unitQuantity,
        int $storeStock
    ): array {
        $unitId = DB::table('product_units')->insertGetId([
            'product_id' => $productId,
            'unit_name' => $unitName,
            'unit_quantity' => $unitQuantity,
            'price' => 10,
            'cost_price' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $stockId = DB::table('product_stocks')->insertGetId([
            'product_id' => $productId,
            'unit_id' => $unitId,
            'warehouse_stock' => 0,
            'store_stock' => $storeStock,
            'track_stock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            ProductUnit::findOrFail($unitId),
            ProductStocks::findOrFail($stockId),
        ];
    }
}
