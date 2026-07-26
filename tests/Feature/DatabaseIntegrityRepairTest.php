<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DatabaseIntegrityRepair\RepairPreconditionChanged;
use App\Services\DatabaseIntegrityAuditor;
use App\Services\DatabaseIntegrityRepairService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class DatabaseIntegrityRepairTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_command_is_dry_run_and_changes_no_data(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $first = $this->shift($staff, $shiftTypeId);
        $second = $this->shift($staff, $shiftTypeId);
        $before = $this->shiftSnapshot([$first, $second]);

        [$exitCode, $report] = $this->command();

        $this->assertSame(0, $exitCode);
        $this->assertSame('dry-run', $report['summary']['mode']);
        $this->assertSame(1, $report['summary']['planned']);
        $this->assertSame($before, $this->shiftSnapshot([$first, $second]));
    }

    public function test_apply_option_is_required_for_writes(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $first = $this->shift($staff, $shiftTypeId);
        $second = $this->shift($staff, $shiftTypeId);

        $this->command(['--only' => 'shifts']);
        $this->assertSame('open', $this->shiftStatus($first));
        $this->assertSame('open', $this->shiftStatus($second));

        [, $report] = $this->command([
            '--only' => 'shifts',
            '--apply' => true,
        ]);

        $this->assertSame('apply', $report['summary']['mode']);
        $this->assertSame(1, $report['summary']['applied']);
        $this->assertSame('closed', $this->shiftStatus($first));
        $this->assertSame('open', $this->shiftStatus($second));
    }

    public function test_deterministic_shift_status_mismatch_is_repaired(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $closedAt = now()->subMinutes(30)->format('Y-m-d H:i:s');
        $shiftId = $this->shift($staff, $shiftTypeId, [
            'opened_at' => now()->subHour(),
            'closed_at' => $closedAt,
            'status' => 'open',
        ]);

        [, $report] = $this->command([
            '--only' => 'shifts',
            '--apply' => true,
        ]);
        $shift = DB::table('shifts')->find($shiftId);

        $this->assertSame('closed', $shift->status);
        $this->assertSame($closedAt, $shift->closed_at);
        $this->assertSame(1, $report['summary']['applied']);
    }

    public function test_ambiguous_shift_status_mismatch_is_not_repaired(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $closedAt = now()->subMinutes(30)->format('Y-m-d H:i:s');
        $shiftId = $this->shift($staff, $shiftTypeId, [
            'opened_at' => now()->subHour(),
            'closed_at' => $closedAt,
            'status' => 'open',
        ]);
        $this->sale($staff, $shiftId, now()->subMinutes(10));

        [, $report] = $this->command([
            '--only' => 'shifts',
            '--apply' => true,
        ]);
        $item = collect($report['items'])
            ->firstWhere('key', "shift.status_timestamp.{$shiftId}");

        $this->assertSame('manual-only', $item['status']);
        $this->assertSame('open', $this->shiftStatus($shiftId));
        $this->assertSame(0, $report['summary']['applied']);
    }

    public function test_overlapping_active_shifts_are_manual_only(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $first = $this->shift($staff, $shiftTypeId);
        $second = $this->shift($staff, $shiftTypeId);
        $this->sale($staff, $first, now()->subMinutes(5));
        $this->sale($staff, $second, now()->subMinutes(4));

        [, $report] = $this->command([
            '--only' => 'shifts',
            '--apply' => true,
        ]);
        $item = collect($report['items'])->firstWhere(
            'key',
            "shift.multiple_open.user.{$staff->id}",
        );

        $this->assertSame('manual-only', $item['status']);
        $this->assertSame('open', $this->shiftStatus($first));
        $this->assertSame('open', $this->shiftStatus($second));
    }

    public function test_deterministic_multiple_open_shifts_are_repaired(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $active = $this->shift($staff, $shiftTypeId, [
            'opened_at' => now()->subMinutes(20),
        ]);
        $unused = $this->shift($staff, $shiftTypeId, [
            'opened_at' => now()->subMinutes(19),
        ]);
        $this->sale($staff, $active, now()->subMinutes(5));

        [, $report] = $this->command([
            '--only' => 'shifts',
            '--apply' => true,
        ]);

        $this->assertSame('open', $this->shiftStatus($active));
        $this->assertSame('closed', $this->shiftStatus($unused));
        $this->assertSame(1, $report['summary']['applied']);
    }

    public function test_newer_unused_shift_after_last_sale_is_manual_only(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $active = $this->shift($staff, $shiftTypeId, [
            'opened_at' => now()->subMinutes(30),
        ]);
        $this->sale($staff, $active, now()->subMinutes(25));
        $newer = $this->shift($staff, $shiftTypeId, [
            'opened_at' => now()->subMinutes(10),
        ]);

        [, $report] = $this->command([
            '--only' => 'shifts',
            '--apply' => true,
        ]);

        $this->assertSame('open', $this->shiftStatus($active));
        $this->assertSame('open', $this->shiftStatus($newer));
        $this->assertSame('manual-only', $report['items'][0]['status']);
    }

    public function test_role_inconsistency_is_never_auto_fixed(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $shiftTypeId = $this->shiftType();
        $shiftId = $this->shift($member, $shiftTypeId, [
            'opened_at' => now()->subHour(),
            'closed_at' => now()->subMinutes(30),
            'status' => 'closed',
        ]);

        [, $report] = $this->command([
            '--only' => 'roles',
            '--apply' => true,
        ]);

        $this->assertSame('member', $member->fresh()->role);
        $this->assertSame('closed', $this->shiftStatus($shiftId));
        $this->assertSame(0, $report['summary']['applied']);
        $this->assertSame('manual-only', $report['items'][0]['status']);
    }

    public function test_movement_mismatch_is_never_fuzzy_fixed(): void
    {
        [$productId] = $this->productUnit('pack', 6);
        $movementId = DB::table('product_stock_movements')->insertGetId([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => 1,
            'unit_quantity' => 1,
            'unit' => 'pack',
            'location' => 'store',
            'note' => '',
            'is_free' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        [, $report] = $this->command([
            '--only' => 'movements',
            '--apply' => true,
        ]);
        $movement = DB::table('product_stock_movements')->find($movementId);

        $this->assertSame(1, (int) $movement->unit_quantity);
        $this->assertSame(0, $report['summary']['applied']);
        $this->assertSame('manual-only', $report['items'][0]['status']);
    }

    public function test_transaction_failure_rolls_back_all_shift_writes(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $first = $this->shift($staff, $shiftTypeId);
        $second = $this->shift($staff, $shiftTypeId);
        $third = $this->shift($staff, $shiftTypeId);
        $updates = 0;

        DB::listen(function ($query) use (&$updates): void {
            if (preg_match('/^\s*update\s+["`]?shifts/i', $query->sql)) {
                $updates++;

                if ($updates === 1) {
                    throw new RuntimeException('Injected repair failure.');
                }
            }
        });

        try {
            Artisan::call('repair:database-integrity', [
                '--only' => 'shifts',
                '--apply' => true,
                '--json' => true,
            ]);
            $this->fail('The injected update failure was not raised.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Injected repair failure.',
                $exception->getMessage(),
            );
        }

        $this->assertSame(
            ['open', 'open', 'open'],
            collect([$first, $second, $third])
                ->map(fn ($id) => $this->shiftStatus($id))
                ->all(),
        );
    }

    public function test_changed_precondition_aborts_apply(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $first = $this->shift($staff, $shiftTypeId);
        $second = $this->shift($staff, $shiftTypeId);
        $service = app(DatabaseIntegrityRepairService::class);
        $plan = $service->plan(['shifts']);

        DB::table('shifts')->where('id', $first)->update([
            'opened_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        try {
            $service->apply($plan);
            $this->fail('A stale repair plan was applied.');
        } catch (RepairPreconditionChanged $exception) {
            $this->assertStringContainsString(
                'precondition changed',
                $exception->getMessage(),
            );
        }

        $this->assertSame('open', $this->shiftStatus($first));
        $this->assertSame('open', $this->shiftStatus($second));
    }

    public function test_json_output_is_parseable(): void
    {
        [$exitCode, $report, $output] = $this->command();

        $this->assertSame(0, $exitCode);
        $this->assertJson($output);
        $this->assertArrayHasKey('summary', $report);
        $this->assertArrayHasKey('items', $report);
    }

    public function test_only_option_limits_inspection_scope(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $shiftTypeId = $this->shiftType();
        $this->shift($member, $shiftTypeId, [
            'closed_at' => now(),
            'status' => 'closed',
        ]);
        [$productId] = $this->productUnit('pack', 6);
        DB::table('product_stock_movements')->insert([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => 1,
            'unit_quantity' => 1,
            'unit' => 'pack',
            'location' => 'store',
            'is_free' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        [, $report] = $this->command(['--only' => 'roles']);

        $this->assertNotEmpty($report['items']);
        $this->assertSame(
            ['roles'],
            collect($report['items'])->pluck('scope')->unique()->values()->all()
        );
    }

    public function test_deterministic_repair_reduces_audit_critical_count(): void
    {
        [$staff, $shiftTypeId] = $this->staffAndShiftType();
        $this->shift($staff, $shiftTypeId);
        $this->shift($staff, $shiftTypeId);
        $auditor = app(DatabaseIntegrityAuditor::class);
        $before = $auditor->audit(1)['summary']['critical_issues'];

        $this->command([
            '--only' => 'shifts',
            '--apply' => true,
        ]);

        $after = $auditor->audit(1)['summary']['critical_issues'];

        $this->assertSame(1, $before - $after);
    }

    private function command(array $options = []): array
    {
        $exitCode = Artisan::call(
            'repair:database-integrity',
            array_merge(['--json' => true], $options),
        );
        $output = trim(Artisan::output());
        $report = json_decode(
            $output,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        return [$exitCode, $report, $output];
    }

    private function staffAndShiftType(): array
    {
        return [
            User::factory()->create(['role' => 'staff']),
            $this->shiftType(),
        ];
    }

    private function shiftType(): int
    {
        return DB::table('shift_types')->insertGetId([
            'name' => 'Repair Shift ' . Str::uuid(),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function shift(
        User $user,
        int $shiftTypeId,
        array $attributes = [],
    ): int {
        return DB::table('shifts')->insertGetId(array_merge([
            'user_id' => $user->id,
            'shift_type_id' => $shiftTypeId,
            'opening_cash' => 1000,
            'opened_at' => now(),
            'closed_at' => null,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }

    private function shiftStatus(int $shiftId): string
    {
        return DB::table('shifts')->where('id', $shiftId)->value('status');
    }

    private function shiftSnapshot(array $ids): array
    {
        return DB::table('shifts')
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function productUnit(
        string $unitName = 'piece',
        int $unitQuantity = 1,
    ): array {
        $now = now();
        $productId = DB::table('products')->insertGetId([
            'name' => 'Repair Product ' . Str::uuid(),
            'is_active' => true,
            'is_online' => true,
            'min_stock' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $unitId = DB::table('product_units')->insertGetId([
            'product_id' => $productId,
            'unit_name' => $unitName,
            'unit_quantity' => $unitQuantity,
            'price' => 10,
            'wholesale' => 8,
            'cost_price' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return [$productId, $unitId];
    }

    private function sale(
        User $staff,
        int $shiftId,
        $saleDate,
    ): int {
        [, $unitId] = $this->productUnit();
        $now = now();
        $saleId = DB::table('sales')->insertGetId([
            'staff_id' => $staff->id,
            'shift_id' => $shiftId,
            'sale_date' => $saleDate,
            'sale_type' => 'retail',
            'payment_method' => 'cash',
            'total_price' => 10,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('sale_items')->insert([
            'sale_id' => $saleId,
            'product_unit_id' => $unitId,
            'quantity' => 1,
            'unit_quantity' => 1,
            'price' => 10,
            'cost' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $saleId;
    }
}
