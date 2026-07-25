<?php

namespace Tests\Feature;

use App\Models\CreditSale;
use App\Models\Customer;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\User;
use App\Services\StockDeductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class CreditSaleFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_sale_writes_server_totals_items_debt_stock_and_shift_sale(): void
    {
        [$staff, $shiftId] = $this->staffWithOpenShift();
        $customer = $this->customer(['current_debt' => 5, 'credit_limit' => 1000]);
        [$firstUnit, $firstStock] = $this->productUnit('Milk', 10.50, 10);
        [$secondUnit, $secondStock] = $this->productUnit('Bread', 7.25, 10);

        $response = $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                [
                    'product_unit_id' => $firstUnit->id,
                    'qty' => 2,
                    'price' => 0.01,
                ],
                [
                    'product_unit_id' => $secondUnit->id,
                    'qty' => 3,
                    'price' => 99999,
                ],
            ], [
                'total_price' => 0.02,
            ])
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total' => 42.75,
            ]);

        $sale = Sale::with('items')->sole();
        $creditSale = CreditSale::with('items')->sole();

        $this->assertSame($staff->id, $sale->staff_id);
        $this->assertSame($shiftId, $sale->shift_id);
        $this->assertSame('credit', $sale->sale_type);
        $this->assertSame('credit', $sale->payment_method);
        $this->assertEquals(42.75, (float) $sale->total_price);
        $this->assertCount(2, $sale->items);

        $this->assertSame($customer->id, $creditSale->customer_id);
        $this->assertEquals(42.75, (float) $creditSale->total_amount);
        $this->assertEquals(42.75, (float) $creditSale->remaining_amount);
        $this->assertEquals(0, (float) $creditSale->paid_amount);
        $this->assertSame('unpaid', $creditSale->status);
        $this->assertCount(2, $creditSale->items);

        $firstCreditItem = $creditSale->items
            ->firstWhere('product_unit_id', $firstUnit->id);
        $secondCreditItem = $creditSale->items
            ->firstWhere('product_unit_id', $secondUnit->id);

        $this->assertEquals(10.50, (float) $firstCreditItem->price);
        $this->assertEquals(21, (float) $firstCreditItem->total);
        $this->assertEquals(7.25, (float) $secondCreditItem->price);
        $this->assertEquals(21.75, (float) $secondCreditItem->total);

        $this->assertEquals(47.75, (float) $customer->fresh()->current_debt);
        $this->assertSame(8, (int) $firstStock->fresh()->store_stock);
        $this->assertSame(7, (int) $secondStock->fresh()->store_stock);
        $this->assertSame(2, ProductStockMovement::where('type', 'out')->count());
    }

    public function test_credit_sale_converts_a_larger_unit_and_records_every_stock_change(): void
    {
        [$staff] = $this->staffWithOpenShift();
        $customer = $this->customer();
        $productId = $this->product('Cooking Oil');
        [$pieceUnit, $pieceStock] = $this->unitForProduct(
            $productId,
            'piece',
            1,
            5,
            2
        );
        [$boxUnit, $boxStock] = $this->unitForProduct(
            $productId,
            'box',
            12,
            50,
            1
        );

        $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                [
                    'product_unit_id' => $pieceUnit->id,
                    'qty' => 5,
                    'price' => 5000,
                ],
            ])
        )->assertOk()
            ->assertJsonPath('total', 25);

        $this->assertSame(9, (int) $pieceStock->fresh()->store_stock);
        $this->assertSame(0, (int) $boxStock->fresh()->store_stock);

        $movements = ProductStockMovement::orderBy('id')->get();

        $this->assertCount(4, $movements);
        $this->assertSame(['out', 'convert', 'convert', 'out'], $movements->pluck('type')->all());
        $this->assertEquals([2, 1, 12, 3], $movements->pluck('quantity')->all());
    }

    public function test_credit_sale_is_rejected_without_an_open_shift(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = $this->customer();
        [$unit, $stock] = $this->productUnit('Coffee', 15, 5);

        $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                ['product_unit_id' => $unit->id, 'qty' => 1],
            ])
        )->assertForbidden();

        $this->assertNoCreditSaleWrites($customer, [$stock->id => 5]);
    }

    public function test_insufficient_stock_rolls_back_sale_items_debt_and_movements(): void
    {
        [$staff] = $this->staffWithOpenShift();
        $customer = $this->customer(['current_debt' => 10]);
        [$unit, $stock] = $this->productUnit('Sugar', 20, 1);

        $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                ['product_unit_id' => $unit->id, 'qty' => 5],
            ])
        )->assertUnprocessable()
            ->assertJsonValidationErrors('products');

        $this->assertNoCreditSaleWrites($customer, [$stock->id => 1], 10);
    }

    public function test_exception_mid_transaction_rolls_back_every_written_table(): void
    {
        [$staff] = $this->staffWithOpenShift();
        $customer = $this->customer(['current_debt' => 3]);
        [$firstUnit, $firstStock] = $this->productUnit('Item A', 10, 5);
        [$secondUnit, $secondStock] = $this->productUnit('Item B', 20, 5);
        $calls = 0;

        $this->mock(
            StockDeductionService::class,
            function (MockInterface $mock) use (&$calls): void {
                $mock->shouldReceive('deduct')
                    ->twice()
                    ->andReturnUsing(function (ProductUnit $unit) use (&$calls): void {
                        $calls++;

                        ProductStocks::where('unit_id', $unit->id)
                            ->decrement('store_stock');

                        ProductStockMovement::create([
                            'product_id' => $unit->product_id,
                            'type' => 'out',
                            'quantity' => 1,
                            'unit_quantity' => $unit->unit_quantity,
                            'unit' => $unit->unit_name,
                            'location' => 'store',
                            'note' => 'test transaction rollback',
                        ]);

                        if ($calls === 2) {
                            throw new RuntimeException('forced mid-transaction failure');
                        }
                    });
            }
        );

        $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                ['product_unit_id' => $firstUnit->id, 'qty' => 1],
                ['product_unit_id' => $secondUnit->id, 'qty' => 1],
            ])
        )->assertServerError();

        $this->assertNoCreditSaleWrites(
            $customer,
            [
                $firstStock->id => 5,
                $secondStock->id => 5,
            ],
            3
        );
    }

    public function test_member_cannot_call_staff_credit_sale_endpoint(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $customer = $this->customer();
        [$unit] = $this->productUnit('Rice', 30, 5);

        $this->actingAs($member)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                ['product_unit_id' => $unit->id, 'qty' => 1],
            ])
        )->assertForbidden();
    }

    public function test_zero_negative_and_non_numeric_quantities_are_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();
        $customer = $this->customer();
        [$unit] = $this->productUnit('Soap', 12, 10);

        foreach ([0, -1, 'not-a-number'] as $quantity) {
            $this->actingAs($staff)->postJson(
                '/staff/credit-sale',
                $this->creditPayload($customer, [
                    ['product_unit_id' => $unit->id, 'qty' => $quantity],
                ])
            )->assertUnprocessable()
                ->assertJsonValidationErrors('products.0.qty');
        }

        $this->assertDatabaseCount('credit_sales', 0);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_empty_product_list_is_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();
        $customer = $this->customer();

        $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [])
        )->assertUnprocessable()
            ->assertJsonValidationErrors('products');
    }

    public function test_nonexistent_product_unit_is_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();
        $customer = $this->customer();

        $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                ['product_unit_id' => 999999, 'qty' => 1],
            ])
        )->assertUnprocessable()
            ->assertJsonValidationErrors('products.0.product_unit_id');
    }

    public function test_nonexistent_customer_is_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();
        [$unit] = $this->productUnit('Water', 8, 10);

        $this->actingAs($staff)->postJson('/staff/credit-sale', [
            'customer_id' => 999999,
            'due_date' => now()->addDay()->toDateString(),
            'products' => [
                ['product_unit_id' => $unit->id, 'qty' => 1],
            ],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('customer_id');
    }

    public function test_inactive_customer_is_rejected(): void
    {
        [$staff] = $this->staffWithOpenShift();
        $customer = $this->customer(['is_active' => false]);
        [$unit, $stock] = $this->productUnit('Tea', 10, 5);

        $this->actingAs($staff)->postJson(
            '/staff/credit-sale',
            $this->creditPayload($customer, [
                ['product_unit_id' => $unit->id, 'qty' => 1],
            ])
        )->assertForbidden();

        $this->assertNoCreditSaleWrites($customer, [$stock->id => 5]);
    }

    public function test_cash_sale_uses_the_shared_stock_deduction_service(): void
    {
        [$staff, $shiftId] = $this->staffWithOpenShift();
        [$unit, $stock] = $this->productUnit('Cash Item', 11.50, 4);

        $this->actingAs($staff)->postJson('/update-stock', [
            'request_token' => (string) \Illuminate\Support\Str::uuid(),
            'payment_method' => 'cash',
            'price_type' => 'retail',
            'products' => [
                ['product_unit_id' => $unit->id, 'qty' => 2],
            ],
        ])->assertOk()
            ->assertJson(['success' => true]);

        $sale = Sale::sole();

        $this->assertSame($shiftId, $sale->shift_id);
        $this->assertSame('cash', $sale->payment_method);
        $this->assertEquals(23, (float) $sale->total_price);
        $this->assertSame(2, (int) $stock->fresh()->store_stock);
        $this->assertDatabaseCount('product_stock_movements', 1);
        $this->assertDatabaseCount('credit_sales', 0);
    }

    private function staffWithOpenShift(): array
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $shiftTypeId = DB::table('shift_types')->insertGetId([
            'name' => 'Test Shift',
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

    private function customer(array $attributes = []): Customer
    {
        return Customer::create(array_merge([
            'name' => 'Credit Customer',
            'phone' => '0812345678',
            'credit_limit' => 0,
            'current_debt' => 0,
            'is_active' => true,
        ], $attributes));
    }

    private function productUnit(
        string $productName,
        float $price,
        int $storeStock
    ): array {
        $productId = $this->product($productName);

        return $this->unitForProduct(
            $productId,
            'piece',
            1,
            $price,
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
        float $price,
        int $storeStock
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

    private function creditPayload(
        Customer $customer,
        array $products,
        array $attributes = []
    ): array {
        return array_merge([
            'customer_id' => $customer->id,
            'due_date' => now()->addDay()->toDateString(),
            'products' => $products,
        ], $attributes);
    }

    private function assertNoCreditSaleWrites(
        Customer $customer,
        array $stockQuantities,
        float $expectedDebt = 0
    ): void {
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
        $this->assertDatabaseCount('credit_sales', 0);
        $this->assertDatabaseCount('credit_sale_items', 0);
        $this->assertDatabaseCount('product_stock_movements', 0);
        $this->assertEquals($expectedDebt, (float) $customer->fresh()->current_debt);

        foreach ($stockQuantities as $stockId => $quantity) {
            $this->assertSame(
                $quantity,
                (int) ProductStocks::findOrFail($stockId)->store_stock
            );
        }
    }
}
