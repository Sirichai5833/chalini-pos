<?php

namespace App\Services\DatabaseIntegrity;

use Illuminate\Support\Facades\DB;

class AccessIntegrityChecks extends IntegrityCheckProvider
{
    private const OPEN_SHIFT_WARNING_HOURS = 24;

    public function definitions(): array
    {
        return [
            $this->check(
                'shift.multiple_open',
                'User has multiple open shifts',
                'critical',
                'Sales can be assigned to an arbitrary shift and cash totals can diverge.',
                fn () => DB::table('shifts')
                    ->where('status', 'open')
                    ->selectRaw('MIN(id) AS record_id')
                    ->groupBy('user_id')
                    ->havingRaw('COUNT(*) > 1'),
                ['shifts' => ['id', 'user_id', 'status']],
            ),
            $this->check(
                'shift.closed_before_opened',
                'Shift closes before it opens',
                'critical',
                'Time-based sale and cash reconciliation is impossible.',
                fn () => DB::table('shifts')
                    ->whereNotNull('closed_at')
                    ->whereColumn('closed_at', '<', 'opened_at')
                    ->select('id AS record_id'),
                ['shifts' => ['id', 'opened_at', 'closed_at']],
            ),
            $this->check(
                'shift.missing_user',
                'Shift references a missing user',
                'critical',
                'The shift has no accountable staff member.',
                fn () => DB::table('shifts AS shift')
                    ->leftJoin('users AS user', 'user.id', '=', 'shift.user_id')
                    ->whereNull('user.id')
                    ->select('shift.id AS record_id'),
                [
                    'shifts' => ['id', 'user_id'],
                    'users' => ['id'],
                ],
            ),
            $this->check(
                'shift.sale_outside_closed_window',
                'Sale timestamp falls outside its closed shift',
                'critical',
                'The sale is included in a shift that was not open at that time.',
                fn () => DB::table('sales AS sale')
                    ->join('shifts AS shift', 'shift.id', '=', 'sale.shift_id')
                    ->where('shift.status', 'closed')
                    ->whereNotNull('shift.closed_at')
                    ->where(fn ($query) => $query
                        ->whereColumn('sale.sale_date', '<', 'shift.opened_at')
                        ->orWhereColumn(
                            'sale.sale_date',
                            '>',
                            'shift.closed_at',
                        ))
                    ->select('sale.id AS record_id'),
                [
                    'sales' => ['id', 'shift_id', 'sale_date'],
                    'shifts' => [
                        'id',
                        'status',
                        'opened_at',
                        'closed_at',
                    ],
                ],
            ),
            $this->check(
                'shift.stale_open',
                'Open shift has remained open for more than 24 hours',
                'warning',
                'A stale shift may continue receiving unrelated sales.',
                fn () => DB::table('shifts')
                    ->where('status', 'open')
                    ->where(
                        'opened_at',
                        '<',
                        now()->subHours(self::OPEN_SHIFT_WARNING_HOURS),
                    )
                    ->select('id AS record_id'),
                ['shifts' => ['id', 'status', 'opened_at']],
            ),
            $this->check(
                'shift.status_timestamp_mismatch',
                'Shift status and close timestamp disagree',
                'critical',
                'Open-shift authorization and cash closing state disagree.',
                fn () => DB::table('shifts')
                    ->where(fn ($query) => $query
                        ->where(fn ($open) => $open
                            ->where('status', 'open')
                            ->whereNotNull('closed_at'))
                        ->orWhere(fn ($closed) => $closed
                            ->where('status', 'closed')
                            ->whereNull('closed_at')))
                    ->select('id AS record_id'),
                ['shifts' => ['id', 'status', 'closed_at']],
            ),
            $this->check(
                'user.invalid_role',
                'User role is outside the system whitelist',
                'critical',
                'Route authorization has undefined behavior for the user.',
                fn () => DB::table('users')
                    ->whereNotIn(
                        'role',
                        ['admin', 'manager', 'owner', 'staff', 'member'],
                    )
                    ->select('id AS record_id'),
                ['users' => ['id', 'role']],
            ),
            $this->check(
                'user.member_has_staff_relations',
                'Member has staff-only shift, sale, or assignment relations',
                'critical',
                'Stored relationships conflict with role-based authorization.',
                fn () => DB::table('users AS user')
                    ->where('user.role', 'member')
                    ->where(function ($query) {
                        $query
                            ->whereExists(fn ($shift) => $shift
                                ->selectRaw('1')
                                ->from('shifts AS shift')
                                ->whereColumn('shift.user_id', 'user.id'))
                            ->orWhereExists(fn ($sale) => $sale
                                ->selectRaw('1')
                                ->from('sales AS sale')
                                ->whereColumn('sale.staff_id', 'user.id'))
                            ->orWhereExists(fn ($order) => $order
                                ->selectRaw('1')
                                ->from('orders AS order_record')
                                ->whereColumn(
                                    'order_record.assigned_to',
                                    'user.id',
                                ));
                    })
                    ->select('user.id AS record_id'),
                [
                    'users' => ['id', 'role'],
                    'shifts' => ['user_id'],
                    'sales' => ['staff_id'],
                    'orders' => ['assigned_to'],
                ],
            ),
            $this->check(
                'sale.pos_missing_shift',
                'Idempotent POS sale has no shift owner',
                'critical',
                'A current POS transaction cannot be reconciled to an open shift.',
                fn () => DB::table('sales')
                    ->where('slip', 'like', 'pos-request:%')
                    ->whereNull('shift_id')
                    ->select('id AS record_id'),
                ['sales' => ['id', 'slip', 'shift_id']],
            ),
            $this->check(
                'order.assignee_role_mismatch',
                'Order is assigned to a member',
                'critical',
                'A member is recorded as the staff actor allowed to manage the order.',
                fn () => DB::table('orders AS order_record')
                    ->join(
                        'users AS user',
                        'user.id',
                        '=',
                        'order_record.assigned_to',
                    )
                    ->where('user.role', 'member')
                    ->select('order_record.id AS record_id'),
                [
                    'orders' => ['id', 'assigned_to'],
                    'users' => ['id', 'role'],
                ],
            ),
        ];
    }
}
