<?php

namespace App\Services\DatabaseIntegrityRepair;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShiftIntegrityRepairer
{
    public function inspect(): array
    {
        $duplicateUsers = DB::table('shifts')
            ->where('status', 'open')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('user_id')
            ->pluck('user_id');
        $mismatchIds = DB::table('shifts')
            ->where(function ($query) {
                $query
                    ->where(function ($open) {
                        $open->where('status', 'open')
                            ->whereNotNull('closed_at');
                    })
                    ->orWhere(function ($closed) {
                        $closed->where('status', 'closed')
                            ->whereNull('closed_at');
                    });
            })
            ->orderBy('id')
            ->pluck('id');

        return [
            ...$duplicateUsers
                ->map(fn ($userId) =>
                    $this->inspectOpenGroup((int) $userId))
                ->filter()
                ->all(),
            ...$mismatchIds
                ->map(fn ($shiftId) =>
                    $this->inspectStatusMismatch((int) $shiftId))
                ->filter()
                ->all(),
        ];
    }

    public function apply(array $plan): array
    {
        $current = match ($plan['issue']) {
            'multiple_open_shifts' => $this->inspectOpenGroup(
                (int) $plan['subject_id'],
                true,
            ),
            'shift_status_timestamp_mismatch' =>
                $this->inspectStatusMismatch(
                    (int) $plan['subject_id'],
                    true,
                ),
            default => null,
        };

        if (
            $current === null ||
            $current['fingerprint'] !== $plan['fingerprint'] ||
            $current['status'] !== 'planned'
        ) {
            throw new RepairPreconditionChanged(
                "Repair precondition changed for [{$plan['key']}]."
            );
        }

        foreach ($current['operation']['updates'] as $update) {
            DB::table('shifts')
                ->where('id', $update['id'])
                ->update([
                    'status' => $update['status'],
                    'closed_at' => $update['closed_at'],
                    'updated_at' => now(),
                ]);
        }

        $after = DB::table('shifts')
            ->whereIn(
                'id',
                collect($current['before']['shifts'])->pluck('id'),
            )
            ->orderBy('id')
            ->get($this->shiftColumns())
            ->map(fn ($shift) => (array) $shift)
            ->all();

        return [
            'key' => $current['key'],
            'scope' => 'shifts',
            'before' => $current['before'],
            'after' => ['shifts' => $after],
        ];
    }

    private function inspectOpenGroup(
        int $userId,
        bool $lock = false,
    ): ?array {
        $userQuery = DB::table('users')->where('id', $userId);
        $shiftQuery = DB::table('shifts')
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->orderBy('id');

        if ($lock) {
            $userQuery->lockForUpdate();
            $shiftQuery->lockForUpdate();
        }

        $user = $userQuery->first(['id', 'name', 'role', 'updated_at']);
        $shifts = $shiftQuery->get($this->shiftColumns());

        if (!$user || $shifts->count() < 2) {
            return null;
        }

        $salesQuery = DB::table('sales')
            ->whereIn('shift_id', $shifts->pluck('id'))
            ->orderBy('id');

        if ($lock) {
            $salesQuery->lockForUpdate();
        }

        $sales = $salesQuery->get([
            'id',
            'shift_id',
            'sale_date',
            'total_price',
            'updated_at',
        ]);
        $salesByShift = $sales->groupBy('shift_id');
        $activity = $shifts->map(function ($shift) use ($salesByShift) {
            $shiftSales = $salesByShift->get($shift->id, collect());

            return [
                'shift_id' => (int) $shift->id,
                'sale_count' => $shiftSales->count(),
                'sale_ids' => $shiftSales->pluck('id')->map(
                    fn ($id) => (int) $id
                )->all(),
                'first_sale_at' => $shiftSales->min('sale_date'),
                'last_sale_at' => $shiftSales->max('sale_date'),
            ];
        })->values();
        $activeShiftIds = $activity
            ->where('sale_count', '>', 0)
            ->pluck('shift_id');
        $manualReason = null;

        if ($shifts->contains(fn ($shift) => $shift->closed_at !== null)) {
            $manualReason =
                'An open shift already has closed_at; repair that mismatch first.';
        } elseif ($activeShiftIds->count() > 1) {
            $manualReason =
                'More than one open shift has sale activity.';
        } elseif ($activeShiftIds->count() === 1) {
            $activeShiftId = $activeShiftIds->first();
            $lastSaleAt = $activity
                ->firstWhere('shift_id', $activeShiftId)['last_sale_at'];
            $newerUnusedExists = $shifts
                ->reject(fn ($shift) => $shift->id === $activeShiftId)
                ->contains(fn ($shift) =>
                    $shift->opened_at > $lastSaleAt);

            if ($newerUnusedExists) {
                $manualReason =
                    'A zero-activity shift opened after the active shift’s last sale; the intended keeper is ambiguous.';
            }
        }

        $keeper = $activeShiftIds->count() === 1
            ? $shifts->firstWhere('id', $activeShiftIds->first())
            : $shifts
                ->sortBy(fn ($shift) =>
                    "{$shift->opened_at}|" . str_pad(
                        (string) $shift->id,
                        20,
                        '0',
                        STR_PAD_LEFT,
                    ))
                ->last();
        $updates = $manualReason === null
            ? $shifts
                ->reject(fn ($shift) => $shift->id === $keeper->id)
                ->map(fn ($shift) => [
                    'id' => (int) $shift->id,
                    'status' => 'closed',
                    'closed_at' => max(
                        $shift->opened_at,
                        $keeper->opened_at,
                    ),
                ])
                ->values()
                ->all()
            : [];
        $before = [
            'user' => (array) $user,
            'shifts' => $shifts->map(fn ($shift) => (array) $shift)->all(),
        ];

        return [
            'key' => "shift.multiple_open.user.{$userId}",
            'scope' => 'shifts',
            'issue' => 'multiple_open_shifts',
            'subject_id' => $userId,
            'status' => $manualReason === null
                ? 'planned'
                : 'manual-only',
            'reason' => $manualReason
                ?? 'Keep the only active shift, or the newest shift when none has activity; close zero-activity duplicates.',
            'before' => $before,
            'after' => $manualReason === null
                ? ['updates' => $updates, 'keeper_id' => (int) $keeper->id]
                : null,
            'evidence' => [
                'activity' => $activity->all(),
                'keeper_id' => (int) $keeper->id,
            ],
            'operation' => ['updates' => $updates],
            'fingerprint' => $this->fingerprint(
                $before,
                $sales,
            ),
        ];
    }

    private function inspectStatusMismatch(
        int $shiftId,
        bool $lock = false,
    ): ?array {
        $shift = DB::table('shifts')
            ->where('id', $shiftId)
            ->first($this->shiftColumns());

        if (!$shift) {
            return null;
        }

        $userQuery = DB::table('users')->where('id', $shift->user_id);

        if ($lock) {
            $userQuery->lockForUpdate();
            $user = $userQuery->first([
                'id',
                'name',
                'role',
                'updated_at',
            ]);
            $shift = DB::table('shifts')
                ->where('id', $shiftId)
                ->lockForUpdate()
                ->first($this->shiftColumns());
        } else {
            $user = $userQuery->first([
                'id',
                'name',
                'role',
                'updated_at',
            ]);
        }

        if (
            !$shift ||
            !(($shift->status === 'open' && $shift->closed_at !== null) ||
                ($shift->status === 'closed' && $shift->closed_at === null))
        ) {
            return null;
        }

        $salesQuery = DB::table('sales')
            ->where('shift_id', $shift->id)
            ->orderBy('id');

        if ($lock) {
            $salesQuery->lockForUpdate();
        }

        $sales = $salesQuery->get([
            'id',
            'shift_id',
            'sale_date',
            'total_price',
            'updated_at',
        ]);
        $salesAfterClose = $shift->closed_at === null
            ? collect()
            : $sales->filter(fn ($sale) =>
                $sale->sale_date > $shift->closed_at);
        $planned = $shift->status === 'open'
            && $shift->closed_at !== null
            && $salesAfterClose->isEmpty();
        $reason = $planned
            ? 'closed_at is present and no sale occurred after it; status must be closed.'
            : ($shift->status === 'closed' && $shift->closed_at === null
                ? 'The shift is closed but no evidence determines its closing timestamp.'
                : 'Sales exist after closed_at, so the timestamp or status is ambiguous.');
        $updates = $planned
            ? [[
                'id' => (int) $shift->id,
                'status' => 'closed',
                'closed_at' => $shift->closed_at,
            ]]
            : [];
        $before = [
            'user' => $user ? (array) $user : null,
            'shifts' => [(array) $shift],
        ];

        return [
            'key' => "shift.status_timestamp.{$shiftId}",
            'scope' => 'shifts',
            'issue' => 'shift_status_timestamp_mismatch',
            'subject_id' => $shiftId,
            'status' => $planned ? 'planned' : 'manual-only',
            'reason' => $reason,
            'before' => $before,
            'after' => $planned ? ['updates' => $updates] : null,
            'evidence' => [
                'sale_count' => $sales->count(),
                'sale_ids' => $sales->pluck('id')->map(
                    fn ($id) => (int) $id
                )->all(),
                'first_sale_at' => $sales->min('sale_date'),
                'last_sale_at' => $sales->max('sale_date'),
                'sales_after_closed_at' =>
                    $salesAfterClose->pluck('id')->all(),
            ],
            'operation' => ['updates' => $updates],
            'fingerprint' => $this->fingerprint($before, $sales),
        ];
    }

    private function fingerprint(array $before, Collection $sales): string
    {
        return hash('sha256', json_encode([
            'before' => $before,
            'sales' => $sales->map(fn ($sale) => (array) $sale)->all(),
        ], JSON_THROW_ON_ERROR));
    }

    private function shiftColumns(): array
    {
        return [
            'id',
            'user_id',
            'shift_type_id',
            'opening_cash',
            'expected_cash',
            'closing_cash',
            'cash_difference',
            'opened_at',
            'closed_at',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
