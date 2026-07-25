<?php

namespace Tests\Feature;

use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class PosSaleFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_sale_succeeds(): void
    {
        [$staff, $shiftId] = $this->staffWithOpenShift();
        [$unit, $stock] = $this->productUnit(
            'Cash Product',
            12.50,
            9,
            10
        );

        $response = $this->postSale(
            $staff,
            $this->payload($unit, 2)
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'duplicate' => false,
                'total' => 25,
            ]);

        $sale = Sale::with('items')->sole();

        $this->assertSame($staff->id, $sale->staff_id);
        $this->assertSame($shiftId, $sale->shift_id);
        $this->assertSame('cash', $sale->payment_method);
        $this->assertSame('retail', $sale->sale_type);
        $this->assertCount(1, $sale->items);
        $this->assertSame(8, (int) $stock->fresh()->store_stock);
    }

    public function test_wholesale_sale_succeeds(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit(
            'Wholesale Product',
            20,
            14,
            10
        );

        $this->postSale(
            $staff,
            $this->payload($unit, 3, [
                'price_type' => 'wholesale',
            ])
        )->assertOk()
            ->assertJsonPath('total', 42);

        $sale = Sale::with('items')->sole();

        $this->assertSame('wholesale', $sale->sale_type);
        $this->assertEquals(42, (float) $sale->total_price);
        $this->assertEquals(14, (float) $sale->items->sole()->price);
    }

    public function test_retail_price_comes_from_database(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit(
            'Retail Source',
            17.75,
            8,
            5
        );

        $response = $this->postSale(
            $staff,
            $this->payload($unit, 2, [
                'total_price' => 0.02,
                'products' => [[
                    'product_unit_id' => $unit->id,
                    'qty' => 2,
                    'price' => 0.01,
                    'cost' => 0,
                ]],
            ])
        );

        $response->assertOk()
            ->assertJsonPath('total', 35.5)
            ->assertJsonPath('items.0.price', 17.75);

        $item = SaleItem::sole();

        $this->assertEquals(17.75, (float) $item->price);
        $this->assertEquals(2.50, (float) $item->cost);
        $this->assertEquals(35.50, (float) Sale::sole()->total_price);
    }

    public function test_wholesale_price_comes_from_database(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit(
            'Wholesale Source',
            30,
            21.25,
            5
        );

        $this->postSale(
            $staff,
            $this->payload($unit, 2, [
                'price_type' => 'wholesale',
                'total_price' => 2,
                'products' => [[
                    'product_unit_id' => $unit->id,
                    'qty' => 2,
                    'price' => 1,
                ]],
            ])
        )->assertOk()
            ->assertJsonPath('total', 42.5)
            ->assertJsonPath('items.0.price', 21.25);

        $this->assertEquals(21.25, (float) SaleItem::sole()->price);
    }

    public function test_client_prices_and_total_have_no_effect(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit(
            'Tampered Client',
            15,
            10,
            4
        );

        $this->postSale(
            $staff,
            $this->payload($unit, 2, [
                'staff_id' => User::factory()->create()->id,
                'shift_id' => 999999,
                'total_price' => 999999,
                'products' => [[
                    'product_unit_id' => $unit->id,
                    'qty' => 2,
                    'price' => 999999,
                    'subtotal' => 999999,
                ]],
            ])
        )->assertOk()
            ->assertJsonPath('total', 30);

        $this->assertEquals(30, (float) Sale::sole()->total_price);
        $this->assertEquals(15, (float) SaleItem::sole()->price);
    }

    public function test_invalid_quantities_are_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit, $stock] = $this->productUnit(
            'Invalid Quantity',
            10,
            8,
            5
        );

        foreach ([0, -1, 'not-a-number', 1.5] as $quantity) {
            $payload = $this->payload($unit, 1);
            $payload['request_token'] = (string) Str::uuid();
            $payload['products'][0]['qty'] = $quantity;

            $this->postSale($staff, $payload)
                ->assertUnprocessable()
                ->assertJsonValidationErrors('products.0.qty');
        }

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
    }

    public function test_invalid_product_unit_is_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();

        $this->postSale($staff, [
            'request_token' => (string) Str::uuid(),
            'payment_method' => 'cash',
            'price_type' => 'retail',
            'products' => [[
                'product_unit_id' => 999999,
                'qty' => 1,
            ]],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(
                'products.0.product_unit_id'
            );

        $this->assertDatabaseCount('sales', 0);
    }

    public function test_sale_is_rejected_without_an_open_shift(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        [$unit, $stock] = $this->productUnit(
            'No Shift',
            10,
            8,
            5
        );

        $this->postSale($staff, $this->payload($unit))
            ->assertForbidden();

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
    }

    public function test_sale_uses_authenticated_staff_and_their_shift(): void
    {
        [$staff, $shiftId] = $this->staffWithOpenShift();
        [$otherStaff, $otherShiftId] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit(
            'Ownership',
            10,
            8,
            5
        );

        $this->postSale(
            $staff,
            $this->payload($unit, 1, [
                'staff_id' => $otherStaff->id,
                'shift_id' => $otherShiftId,
            ])
        )->assertOk();

        $sale = Sale::sole();

        $this->assertSame($staff->id, $sale->staff_id);
        $this->assertSame($shiftId, $sale->shift_id);
    }

    public function test_insufficient_stock_rolls_back_everything(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit, $stock] = $this->productUnit(
            'Insufficient POS',
            10,
            8,
            1
        );

        $this->postSale($staff, $this->payload($unit, 2))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('products');

        $this->assertNoSaleWrites();
        $this->assertSame(1, (int) $stock->fresh()->store_stock);
    }

    public function test_sale_item_exception_rolls_back_sale_and_stock(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit, $stock] = $this->productUnit(
            'Item Exception',
            10,
            8,
            5
        );

        SaleItem::creating(function (): void {
            throw new RuntimeException('forced sale item failure');
        });

        try {
            $this->postSale($staff, $this->payload($unit))
                ->assertServerError();
        } finally {
            SaleItem::flushEventListeners();
        }

        $this->assertNoSaleWrites();
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
    }

    public function test_same_request_token_creates_only_one_sale(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit(
            'Idempotent Sale',
            10,
            8,
            5
        );
        $payload = $this->payload($unit);

        $first = $this->postSale($staff, $payload);
        $second = $this->postSale($staff, $payload);

        $first->assertOk()->assertJsonPath('duplicate', false);
        $second->assertOk()->assertJsonPath('duplicate', true);
        $this->assertSame(
            $first->json('sale_id'),
            $second->json('sale_id')
        );
        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseCount('sale_items', 1);
    }

    public function test_same_request_token_does_not_deduct_stock_twice(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit, $stock] = $this->productUnit(
            'Idempotent Stock',
            10,
            8,
            5
        );
        $payload = $this->payload($unit, 2);

        $this->postSale($staff, $payload)->assertOk();
        $this->postSale($staff, $payload)->assertOk();

        $this->assertSame(3, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseCount('product_stock_movements', 1);
    }

    public function test_duplicate_stock_rows_are_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit, $stock] = $this->productUnit(
            'Duplicate Stock',
            10,
            8,
            5
        );
        ProductStocks::create([
            'product_id' => $unit->product_id,
            'unit_id' => $unit->id,
            'store_stock' => 9,
            'warehouse_stock' => 0,
        ]);

        $this->postSale($staff, $this->payload($unit))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('products');

        $this->assertNoSaleWrites();
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
    }

    public function test_member_cannot_call_pos_sale_endpoint(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        [$unit] = $this->productUnit(
            'Member Forbidden',
            10,
            8,
            5
        );

        $this->postSale($member, $this->payload($unit))
            ->assertForbidden();

        $this->assertNoSaleWrites();
    }

    public function test_stock_movement_matches_server_sale_item(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit(
            'Movement POS',
            11,
            9,
            5
        );

        $this->postSale($staff, $this->payload($unit, 2))
            ->assertOk();

        $item = SaleItem::sole();
        $movement = ProductStockMovement::sole();

        $this->assertSame($unit->id, $item->product_unit_id);
        $this->assertSame(2, $item->quantity);
        $this->assertSame($unit->product_id, $movement->product_id);
        $this->assertSame('out', $movement->type);
        $this->assertSame(2, (int) $movement->quantity);
        $this->assertSame(1, (int) $movement->unit_quantity);
    }

    public function test_pos_page_requires_the_authenticated_users_shift(): void
    {
        $staffWithoutShift = User::factory()->create([
            'role' => 'staff',
        ]);
        $this->staffWithOpenShift();

        $this->actingAs($staffWithoutShift)
            ->get(route('sale'))
            ->assertRedirect(route('shift.open'));
    }

    private function postSale(User $actor, array $payload)
    {
        return $this->actingAs($actor)
            ->postJson(route('update.stock'), $payload);
    }

    private function payload(
        ProductUnit $unit,
        int $quantity = 1,
        array $overrides = []
    ): array {
        return array_replace_recursive([
            'request_token' => (string) Str::uuid(),
            'payment_method' => 'cash',
            'price_type' => 'retail',
            'products' => [[
                'product_unit_id' => $unit->id,
                'qty' => $quantity,
            ]],
        ], $overrides);
    }

    private function staffWithOpenShift(): array
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $shiftTypeId = DB::table('shift_types')->insertGetId([
            'name' => 'POS Test Shift ' . Str::uuid(),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $shiftId = DB::table('shifts')->insertGetId([
            'user_id' => $staff->id,
            'shift_type_id' => $shiftTypeId,
            'opening_cash' => 0,
            'opened_at' => now(),
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$staff, $shiftId];
    }

    private function productUnit(
        string $name,
        float $retail,
        float $wholesale,
        int $storeStock
    ): array {
        $productId = DB::table('products')->insertGetId([
            'name' => $name,
            'is_active' => true,
            'is_online' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $unit = ProductUnit::create([
            'product_id' => $productId,
            'unit_name' => 'piece',
            'unit_quantity' => 1,
            'price' => $retail,
            'wholesale' => $wholesale,
            'cost_price' => 2.50,
            'barcode' => 'POS-' . Str::uuid(),
        ]);
        $stock = ProductStocks::create([
            'product_id' => $productId,
            'unit_id' => $unit->id,
            'store_stock' => $storeStock,
            'warehouse_stock' => 0,
        ]);

        return [$unit, $stock];
    }

    private function assertNoSaleWrites(): void
    {
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }
}
