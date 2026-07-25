<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonException;
use Tests\TestCase;

class DatabaseIntegrityCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_clean_database_returns_success(): void
    {
        [$exitCode, $report] = $this->report();

        $this->assertSame(0, $exitCode);
        $this->assertSame(0, $report['summary']['critical_issues']);
        $this->assertGreaterThan(0, $report['summary']['checked']);
        $this->assertGreaterThan(0, $report['summary']['unsupported']);
    }

    public function test_duplicate_product_stock_is_detected(): void
    {
        [$productId, $unitId] = $this->productUnit();
        $this->stock($productId, $unitId);
        $this->stock($productId, $unitId);

        [$exitCode, $report] = $this->report();

        $this->assertSame(1, $exitCode);
        $this->assertSame(1, $this->check(
            $report,
            'stock.duplicate_rows'
        )['count']);
    }

    public function test_negative_stock_is_detected(): void
    {
        [$productId, $unitId] = $this->productUnit();
        $stockId = $this->stock($productId, $unitId, -1);

        [, $report] = $this->report();
        $check = $this->check($report, 'stock.negative_quantity');

        $this->assertSame(1, $check['count']);
        $this->assertSame([$stockId], $check['sample_ids']);
        $this->assertSame('critical', $check['severity']);
    }

    public function test_sale_total_mismatch_is_detected(): void
    {
        [$productId, $unitId] = $this->productUnit();
        $staff = User::factory()->create(['role' => 'staff']);
        $saleId = $this->sale($staff, $unitId, 99, 10);

        [, $report] = $this->report();

        $this->assertSame(
            [$saleId],
            $this->check($report, 'sale.total_mismatch')['sample_ids']
        );
    }

    public function test_credit_sale_balance_mismatch_is_detected(): void
    {
        $customerId = $this->customer(80);
        $creditSaleId = $this->creditSale(
            $customerId,
            total: 100,
            paid: 30,
            remaining: 80,
            status: 'partial',
        );

        [, $report] = $this->report();

        $this->assertSame(
            [$creditSaleId],
            $this->check(
                $report,
                'credit_sale.balance_mismatch'
            )['sample_ids']
        );
    }

    public function test_customer_debt_mismatch_is_detected(): void
    {
        $customerId = $this->customer(90);
        $this->creditSale($customerId, remaining: 100);

        [, $report] = $this->report();

        $this->assertSame(
            [$customerId],
            $this->check($report, 'customer.debt_mismatch')['sample_ids']
        );
    }

    public function test_duplicate_pos_token_is_detected(): void
    {
        [, $unitId] = $this->productUnit();
        $staff = User::factory()->create(['role' => 'staff']);
        $token = 'pos-request:' . Str::uuid();
        $this->sale($staff, $unitId, 10, 10, $token);
        $this->sale($staff, $unitId, 10, 10, $token);

        [, $report] = $this->report();

        $this->assertSame(
            1,
            $this->check($report, 'sale.duplicate_pos_token')['count']
        );
    }

    public function test_duplicate_credit_payment_token_is_detected(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customerId = $this->customer(80);
        $creditSaleId = $this->creditSale(
            $customerId,
            total: 100,
            paid: 20,
            remaining: 80,
            status: 'partial',
        );
        $note = 'credit-payment:' . Str::uuid() . ':bill';

        $this->payment(
            $staff,
            $customerId,
            $creditSaleId,
            10,
            $note,
        );
        $this->payment(
            $staff,
            $customerId,
            $creditSaleId,
            10,
            $note,
        );

        [, $report] = $this->report();

        $this->assertSame(
            1,
            $this->check(
                $report,
                'payment.duplicate_token_allocation'
            )['count']
        );
    }

    public function test_non_hex_idempotency_tokens_are_detected(): void
    {
        [, $unitId] = $this->productUnit();
        $staff = User::factory()->create(['role' => 'staff']);
        $invalidUuid = 'zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz';
        $saleId = $this->sale(
            $staff,
            $unitId,
            10,
            10,
            'pos-request:' . $invalidUuid,
        );
        $customerId = $this->customer(100);
        $creditSaleId = $this->creditSale($customerId);
        $paymentId = $this->payment(
            $staff,
            $customerId,
            $creditSaleId,
            10,
            'credit-payment:' . $invalidUuid . ':bill',
        );

        [, $report] = $this->report();

        $this->assertSame(
            [$saleId],
            $this->check($report, 'sale.invalid_pos_token')['sample_ids']
        );
        $this->assertSame(
            [$paymentId],
            $this->check($report, 'payment.invalid_token')['sample_ids']
        );
    }

    public function test_multiple_open_shifts_are_detected(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $shiftTypeId = $this->shiftType();
        $firstShiftId = $this->shift($staff, $shiftTypeId);
        $this->shift($staff, $shiftTypeId);

        [, $report] = $this->report();
        $check = $this->check($report, 'shift.multiple_open');

        $this->assertSame(1, $check['count']);
        $this->assertSame([$firstShiftId], $check['sample_ids']);
    }

    public function test_json_output_is_parseable(): void
    {
        [$exitCode, $report, $output] = $this->report();

        $this->assertSame(0, $exitCode);
        $this->assertJson($output);
        $this->assertArrayHasKey('summary', $report);
        $this->assertArrayHasKey('checks', $report);
    }

    public function test_limit_caps_sample_ids_without_changing_count(): void
    {
        [$productId, $unitId] = $this->productUnit();

        foreach ([-1, -2, -3] as $quantity) {
            $this->stock($productId, $unitId, $quantity);
        }

        [, $report] = $this->report(['--limit' => 2]);
        $check = $this->check($report, 'stock.negative_quantity');

        $this->assertSame(3, $check['count']);
        $this->assertCount(2, $check['sample_ids']);
    }

    public function test_command_executes_only_read_queries_and_changes_no_data(): void
    {
        [$productId, $unitId] = $this->productUnit();
        $this->stock($productId, $unitId, -1);
        $before = $this->databaseSnapshot();
        $queries = [];

        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        $this->report();

        $this->assertSame($before, $this->databaseSnapshot());
        $this->assertNotEmpty($queries);

        foreach ($queries as $sql) {
            $this->assertDoesNotMatchRegularExpression(
                '/^\s*(insert|update|delete|replace|alter|create|drop|truncate)\b/i',
                $sql,
            );
        }
    }

    private function report(array $options = []): array
    {
        $exitCode = Artisan::call(
            'audit:database-integrity',
            array_merge(['--json' => true], $options),
        );
        $output = trim(Artisan::output());

        try {
            $report = json_decode(
                $output,
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $exception) {
            $this->fail(
                "Command did not emit valid JSON: {$exception->getMessage()}\n" .
                $output
            );
        }

        return [$exitCode, $report, $output];
    }

    private function check(array $report, string $key): array
    {
        $check = collect($report['checks'])->firstWhere('key', $key);

        $this->assertNotNull($check, "Integrity check [{$key}] is missing.");

        return $check;
    }

    private function productUnit(): array
    {
        $now = now();
        $productId = DB::table('products')->insertGetId([
            'name' => 'Audit Product ' . Str::uuid(),
            'is_active' => true,
            'is_online' => true,
            'min_stock' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $unitId = DB::table('product_units')->insertGetId([
            'product_id' => $productId,
            'unit_name' => 'piece',
            'unit_quantity' => 1,
            'wholesale' => 8,
            'price' => 10,
            'cost_price' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return [$productId, $unitId];
    }

    private function stock(
        int $productId,
        int $unitId,
        int $storeStock = 10,
    ): int {
        return DB::table('product_stocks')->insertGetId([
            'product_id' => $productId,
            'unit_id' => $unitId,
            'warehouse_stock' => 0,
            'store_stock' => $storeStock,
            'track_stock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function sale(
        User $staff,
        int $unitId,
        float $saleTotal,
        float $itemPrice,
        ?string $slip = null,
    ): int {
        $now = now();
        $saleId = DB::table('sales')->insertGetId([
            'staff_id' => $staff->id,
            'shift_id' => null,
            'sale_date' => $now,
            'sale_type' => 'retail',
            'payment_method' => 'cash',
            'total_price' => $saleTotal,
            'slip' => $slip,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            'sale_id' => $saleId,
            'product_unit_id' => $unitId,
            'quantity' => 1,
            'unit_quantity' => 1,
            'price' => $itemPrice,
            'cost' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $saleId;
    }

    private function customer(float $debt): int
    {
        return DB::table('customers')->insertGetId([
            'name' => 'Audit Customer ' . Str::uuid(),
            'credit_limit' => 1000,
            'current_debt' => $debt,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function creditSale(
        int $customerId,
        float $total = 100,
        float $paid = 0,
        float $remaining = 100,
        string $status = 'unpaid',
    ): int {
        return DB::table('credit_sales')->insertGetId([
            'customer_id' => $customerId,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function payment(
        User $staff,
        int $customerId,
        int $creditSaleId,
        float $amount,
        string $note,
    ): int {
        return DB::table('credit_payment_histories')->insertGetId([
            'customer_id' => $customerId,
            'credit_sale_id' => $creditSaleId,
            'payment_method' => 'cash',
            'amount' => $amount,
            'user_id' => $staff->id,
            'note' => $note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function shiftType(): int
    {
        return DB::table('shift_types')->insertGetId([
            'name' => 'Audit Shift',
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function shift(User $staff, int $shiftTypeId): int
    {
        return DB::table('shifts')->insertGetId([
            'user_id' => $staff->id,
            'shift_type_id' => $shiftTypeId,
            'opening_cash' => 0,
            'opened_at' => now(),
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function databaseSnapshot(): string
    {
        return json_encode([
            'products' => DB::table('products')->orderBy('id')->get()->all(),
            'product_units' =>
                DB::table('product_units')->orderBy('id')->get()->all(),
            'product_stocks' =>
                DB::table('product_stocks')->orderBy('id')->get()->all(),
            'movements' => DB::table('product_stock_movements')
                ->orderBy('id')
                ->get()
                ->all(),
            'sales' => DB::table('sales')->orderBy('id')->get()->all(),
            'orders' => DB::table('orders')->orderBy('id')->get()->all(),
            'credit_sales' =>
                DB::table('credit_sales')->orderBy('id')->get()->all(),
            'payments' => DB::table('credit_payment_histories')
                ->orderBy('id')
                ->get()
                ->all(),
            'customers' =>
                DB::table('customers')->orderBy('id')->get()->all(),
            'shifts' => DB::table('shifts')->orderBy('id')->get()->all(),
            'users' => DB::table('users')->orderBy('id')->get()->all(),
        ], JSON_THROW_ON_ERROR);
    }
}
