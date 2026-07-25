<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\User;
use App\Services\StockDeductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class OnlineCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_or_mutate_cart(): void
    {
        $this->get(route('online.cart'))
            ->assertRedirect(route('login'));
        $this->postJson(route('online.add'))
            ->assertUnauthorized();
    }

    public function test_checkout_creates_order_and_every_order_item(): void
    {
        $member = $this->member();
        [$firstUnit, $firstStock] = $this->productUnit(
            'Online Milk',
            'piece',
            1,
            10,
            10.50
        );
        [$secondUnit, $secondStock] = $this->productUnit(
            'Online Bread',
            'piece',
            1,
            10,
            7.25
        );
        $token = (string) Str::uuid();

        $response = $this->checkout($member, $token, [
            $this->cartItem($firstUnit, 2, 0.01),
            $this->cartItem($secondUnit, 3, 99999),
        ]);

        $response->assertRedirect('/online/track');

        $order = Order::with('items')->sole();

        $this->assertSame($member->id, $order->user_id);
        $this->assertSame($token, $order->order_token);
        $this->assertSame('0000000000', $order->tracking_number);
        $this->assertSame('รอเจ้าหน้าที่รับคำสั่งซื้อ', $order->status);
        $this->assertCount(2, $order->items);
        $this->assertSame(8, (int) $firstStock->fresh()->store_stock);
        $this->assertSame(7, (int) $secondStock->fresh()->store_stock);
        $this->assertDatabaseCount('product_stock_movements', 2);
    }

    public function test_prices_and_total_are_loaded_from_database(): void
    {
        $member = $this->member();
        [$unit] = $this->productUnit(
            'Server Price',
            'piece',
            1,
            10,
            12.75
        );

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [$this->cartItem($unit, 2, 0.01)],
            [
                'total_amount' => 0.02,
                'items' => [
                    [
                        'product_unit_id' => $unit->id,
                        'quantity' => 2,
                        'price' => 0.01,
                    ],
                ],
            ]
        )->assertRedirect('/online/track');

        $order = Order::sole();
        $item = OrderItem::sole();

        $this->assertEquals(25.50, (float) $order->total_amount);
        $this->assertEquals(12.75, (float) $item->price);
        $this->assertEquals(25.50, (float) $item->total);
    }

    public function test_direct_unit_stock_deduction_is_exact(): void
    {
        $member = $this->member();
        [$unit, $stock] = $this->productUnit(
            'Direct Online',
            'box',
            12,
            5,
            120
        );

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [$this->cartItem($unit, 1)]
        )->assertRedirect('/online/track');

        $this->assertSame(4, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseHas('product_stock_movements', [
            'product_id' => $unit->product_id,
            'type' => 'out',
            'quantity' => 1,
            'unit_quantity' => 12,
            'unit' => 'box',
        ]);
    }

    public function test_unit_conversion_matches_shared_pos_stock_service(): void
    {
        $member = $this->member();
        $productId = $this->product('Conversion Online');
        [$piece, $pieceStock] = $this->unitForProduct(
            $productId,
            'piece',
            1,
            2,
            5
        );
        [, $boxStock] = $this->unitForProduct(
            $productId,
            'box',
            12,
            1,
            50
        );

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [$this->cartItem($piece, 5)]
        )->assertRedirect('/online/track');

        $this->assertSame(9, (int) $pieceStock->fresh()->store_stock);
        $this->assertSame(0, (int) $boxStock->fresh()->store_stock);

        $movements = ProductStockMovement::orderBy('id')->get();

        $this->assertSame(
            ['out', 'convert', 'convert', 'out'],
            $movements->pluck('type')->all()
        );
        $this->assertEquals(
            [2, 1, 12, 3],
            $movements->pluck('quantity')->all()
        );
        $this->assertTrue(
            $movements->every(fn ($movement) => $movement->order_id !== null)
        );
    }

    public function test_insufficient_stock_rolls_back_order_items_and_movements(): void
    {
        $member = $this->member();
        [$unit, $stock] = $this->productUnit(
            'Insufficient Online',
            'piece',
            1,
            1,
            10
        );

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [$this->cartItem($unit, 2)]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('products');

        $this->assertNoCheckoutWrites();
        $this->assertSame(1, (int) $stock->fresh()->store_stock);
    }

    public function test_later_item_failure_rolls_back_an_earlier_deduction(): void
    {
        $member = $this->member();
        [$firstUnit, $firstStock] = $this->productUnit(
            'First Online',
            'piece',
            1,
            5,
            10
        );
        [$secondUnit, $secondStock] = $this->productUnit(
            'Second Online',
            'piece',
            1,
            0,
            20
        );

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [
                $this->cartItem($firstUnit, 1),
                $this->cartItem($secondUnit, 1),
            ]
        )->assertUnprocessable();

        $this->assertNoCheckoutWrites();
        $this->assertSame(5, (int) $firstStock->fresh()->store_stock);
        $this->assertSame(0, (int) $secondStock->fresh()->store_stock);
    }

    public function test_exception_after_order_creation_rolls_back_stock_and_order(): void
    {
        $member = $this->member();
        [$unit, $stock] = $this->productUnit(
            'Rollback Online',
            'piece',
            1,
            5,
            10
        );

        $this->mock(
            StockDeductionService::class,
            function (MockInterface $mock) use ($stock, $unit): void {
                $mock->shouldReceive('deduct')
                    ->once()
                    ->andReturnUsing(function (
                        ProductUnit $selectedUnit,
                        int $quantity,
                        string $reason,
                        int $orderId
                    ) use ($stock, $unit): void {
                        ProductStocks::whereKey($stock->id)
                            ->decrement('store_stock', $quantity);
                        ProductStockMovement::create([
                            'order_id' => $orderId,
                            'product_id' => $unit->product_id,
                            'type' => 'out',
                            'quantity' => $quantity,
                            'unit_quantity' => 1,
                            'unit' => 'piece',
                            'location' => 'store',
                            'note' => "{$reason}: forced rollback",
                        ]);

                        throw new RuntimeException('forced checkout failure');
                    });
            }
        );

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [$this->cartItem($unit, 1)]
        )->assertServerError();

        $this->assertNoCheckoutWrites();
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
    }

    public function test_invalid_or_empty_cart_is_rejected(): void
    {
        $member = $this->member();

        $this->actingAs($member)
            ->withSession([
                'cart' => [
                    'invalid' => [
                        'product_unit_id' => 'not-an-id',
                        'quantity' => 0,
                    ],
                ],
                'cart_owner_id' => $member->id,
            ])
            ->postJson(
                route('online.checkout.store'),
                $this->checkoutPayload((string) Str::uuid())
            )->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $this->assertNoCheckoutWrites();
    }

    public function test_duplicate_stock_rows_are_rejected(): void
    {
        $member = $this->member();
        [$unit, $stock] = $this->productUnit(
            'Duplicate Online',
            'piece',
            1,
            5,
            10
        );

        DB::table('product_stocks')->insert([
            'product_id' => $unit->product_id,
            'unit_id' => $unit->id,
            'warehouse_stock' => 0,
            'store_stock' => 20,
            'track_stock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [$this->cartItem($unit, 1)]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('products');

        $this->assertNoCheckoutWrites();
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
    }

    public function test_cart_and_existing_order_ownership_are_enforced(): void
    {
        $owner = $this->member();
        $otherMember = $this->member();
        [$unit] = $this->productUnit(
            'Owned Online',
            'piece',
            1,
            5,
            10
        );

        $this->checkout(
            $otherMember,
            (string) Str::uuid(),
            [$this->cartItem($unit, 1)],
            [],
            $owner->id
        )->assertUnprocessable()
            ->assertJsonValidationErrors('cart');

        $token = (string) Str::uuid();
        Order::create([
            'order_token' => $token,
            'order_code' => 'ORD-OWNED',
            'user_id' => $owner->id,
            'payment_method' => 'เก็บเงินปลายทาง',
            'status' => 'รอเจ้าหน้าที่รับคำสั่งซื้อ',
            'total_amount' => 10,
        ]);

        $this->checkout(
            $otherMember,
            $token,
            []
        )->assertForbidden();
    }

    public function test_double_submission_uses_existing_order_without_rededuction(): void
    {
        $member = $this->member();
        [$unit, $stock] = $this->productUnit(
            'Idempotent Online',
            'piece',
            1,
            3,
            10
        );
        $token = (string) Str::uuid();
        $cart = [$this->cartItem($unit, 1)];

        $this->checkout($member, $token, $cart)
            ->assertRedirect('/online/track');
        $this->checkout($member, $token, [])
            ->assertRedirect('/online/track');

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertSame(2, (int) $stock->fresh()->store_stock);
        $this->assertSame(
            1,
            ProductStockMovement::where('type', 'out')->count()
        );
    }

    public function test_sequential_checkout_contention_does_not_oversell_stock(): void
    {
        $firstMember = $this->member();
        $secondMember = $this->member();
        [$unit, $stock] = $this->productUnit(
            'Contended Online',
            'piece',
            1,
            1,
            10
        );

        $this->checkout(
            $firstMember,
            (string) Str::uuid(),
            [$this->cartItem($unit, 1)]
        )->assertRedirect('/online/track');

        $this->checkout(
            $secondMember,
            (string) Str::uuid(),
            [$this->cartItem($unit, 1)]
        )->assertUnprocessable();

        $this->assertSame(0, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_checkout_and_cart_mutations_use_session_locks(): void
    {
        foreach ([
            'online.add',
            'online.updateQuantity',
            'online.online.updateQuantity',
            'online.online.remove',
            'online.checkout.store',
        ] as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route);
            $this->assertSame(15, $route->locksFor());
            $this->assertSame(15, $route->waitsFor());
        }
    }

    public function test_stock_movements_reference_the_created_order(): void
    {
        $member = $this->member();
        [$unit] = $this->productUnit(
            'Movement Online',
            'piece',
            1,
            5,
            10
        );

        $this->checkout(
            $member,
            (string) Str::uuid(),
            [$this->cartItem($unit, 2)]
        )->assertRedirect('/online/track');

        $order = Order::sole();
        $movement = ProductStockMovement::sole();

        $this->assertSame($order->id, $movement->order_id);
        $this->assertSame($unit->product_id, $movement->product_id);
        $this->assertSame('out', $movement->type);
        $this->assertSame(2, (int) $movement->quantity);
    }

    public function test_cancelled_order_token_cannot_checkout_or_reopen(): void
    {
        $member = $this->member();
        [$unit, $stock] = $this->productUnit(
            'Cancelled Online',
            'piece',
            1,
            5,
            10
        );
        $token = (string) Str::uuid();
        $order = Order::create([
            'order_token' => $token,
            'order_code' => 'ORD-CANCELLED',
            'user_id' => $member->id,
            'payment_method' => 'เก็บเงินปลายทาง',
            'status' => 'ยกเลิก',
            'total_amount' => 10,
        ]);

        $this->checkout(
            $member,
            $token,
            [$this->cartItem($unit, 1)]
        )->assertUnprocessable()
            ->assertJsonValidationErrors('order_token');

        $this->assertSame('ยกเลิก', $order->fresh()->status);
        $this->assertSame(5, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_cart_quantity_endpoint_rejects_zero_negative_and_text(): void
    {
        $member = $this->member();
        [$unit] = $this->productUnit(
            'Quantity Online',
            'piece',
            1,
            5,
            10
        );
        $key = "{$unit->product_id}_{$unit->id}";
        $cart = [$key => $this->cartItem($unit, 1)];

        foreach ([0, -1, 'not-a-number'] as $quantity) {
            $this->actingAs($member)
                ->withSession([
                    'cart' => $cart,
                    'cart_owner_id' => $member->id,
                ])
                ->putJson(
                    route('online.updateQuantity', $key),
                    ['quantity' => $quantity]
                )->assertUnprocessable()
                ->assertJsonValidationErrors('quantity');
        }
    }

    private function checkout(
        User $member,
        string $token,
        array $cart,
        array $payload = [],
        ?int $cartOwnerId = null
    ) {
        return $this->actingAs($member)
            ->withSession([
                'cart' => $cart,
                'cart_owner_id' => $cartOwnerId ?? $member->id,
            ])
            ->postJson(
                route('online.checkout.store'),
                array_merge($this->checkoutPayload($token), $payload)
            );
    }

    private function checkoutPayload(string $token): array
    {
        return [
            'order_token' => $token,
            'phone' => '0000000000',
            'payment_method' => 'เก็บเงินปลายทาง',
            'total_amount' => 0.01,
            'items' => [],
        ];
    }

    private function cartItem(
        ProductUnit $unit,
        int $quantity,
        ?float $price = null
    ): array {
        return [
            'product_id' => $unit->product_id,
            'name' => 'Client Cart Name',
            'product_unit_id' => $unit->id,
            'unit_name' => $unit->unit_name,
            'price' => $price ?? (float) $unit->price,
            'quantity' => $quantity,
        ];
    }

    private function member(): User
    {
        return User::factory()->create([
            'role' => 'member',
            'phone' => '0812345678',
        ]);
    }

    private function assertNoCheckoutWrites(): void
    {
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    private function productUnit(
        string $productName,
        string $unitName,
        int $unitQuantity,
        int $storeStock,
        float $price
    ): array {
        return $this->unitForProduct(
            $this->product($productName),
            $unitName,
            $unitQuantity,
            $storeStock,
            $price
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
        int $storeStock,
        float $price
    ): array {
        $unitId = DB::table('product_units')->insertGetId([
            'product_id' => $productId,
            'unit_name' => $unitName,
            'unit_quantity' => $unitQuantity,
            'price' => $price,
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
