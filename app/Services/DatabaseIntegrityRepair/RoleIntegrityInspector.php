<?php

namespace App\Services\DatabaseIntegrityRepair;

use Illuminate\Support\Facades\DB;

class RoleIntegrityInspector
{
    public function inspect(): array
    {
        $userIds = DB::table('users AS user')
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
            ->orderBy('user.id')
            ->pluck('user.id');

        return $userIds
            ->map(fn ($userId) => $this->inspectUser((int) $userId))
            ->filter()
            ->all();
    }

    private function inspectUser(int $userId): ?array
    {
        $user = DB::table('users')
            ->where('id', $userId)
            ->first(['id', 'name', 'role', 'created_at', 'updated_at']);

        if (!$user) {
            return null;
        }

        $shifts = DB::table('shifts')
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get([
                'id',
                'opened_at',
                'closed_at',
                'status',
            ]);
        $sales = DB::table('sales')
            ->where('staff_id', $userId)
            ->orderBy('id')
            ->get([
                'id',
                'shift_id',
                'sale_date',
                'sale_type',
                'total_price',
            ]);
        $orders = DB::table('orders')
            ->where('assigned_to', $userId)
            ->orderBy('id')
            ->get([
                'id',
                'user_id',
                'status',
                'order_date',
                'total_amount',
            ]);
        $payments = DB::table('credit_payment_histories')
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get([
                'id',
                'customer_id',
                'credit_sale_id',
                'amount',
                'payment_method',
                'created_at',
            ]);
        $audits = DB::table('audits')
            ->where(function ($query) use ($userId) {
                $query
                    ->where('user_id', $userId)
                    ->orWhere(function ($subject) use ($userId) {
                        $subject
                            ->where('auditable_type', 'App\\Models\\User')
                            ->where('auditable_id', $userId);
                    });
            })
            ->orderBy('id')
            ->get([
                'id',
                'user_id',
                'event',
                'auditable_type',
                'auditable_id',
                'created_at',
            ]);
        $session = DB::table('sessions')
            ->where('user_id', $userId)
            ->selectRaw(
                'COUNT(*) AS session_count, MAX(last_activity) AS last_activity'
            )
            ->first();

        return [
            'key' => "role.member_staff_relations.{$userId}",
            'scope' => 'roles',
            'issue' => 'member_has_staff_relations',
            'subject_id' => $userId,
            'status' => 'manual-only',
            'reason' =>
                'Relations do not prove whether the role or legacy relations are wrong; no role or relation is changed automatically.',
            'before' => ['user' => (array) $user],
            'after' => null,
            'evidence' => [
                'shifts' => $shifts->map(fn ($row) => (array) $row)->all(),
                'sales' => $sales->map(fn ($row) => (array) $row)->all(),
                'assigned_orders' =>
                    $orders->map(fn ($row) => (array) $row)->all(),
                'payment_histories' =>
                    $payments->map(fn ($row) => (array) $row)->all(),
                'audits' => $audits->map(fn ($row) => (array) $row)->all(),
                'login' => [
                    'session_count' => (int) $session->session_count,
                    'last_activity' => $session->last_activity === null
                        ? null
                        : (int) $session->last_activity,
                ],
            ],
            'operation' => null,
            'fingerprint' => null,
        ];
    }
}
