<?php

namespace App\Services\DatabaseIntegrityRepair;

use Illuminate\Support\Facades\DB;

class MovementIntegrityInspector
{
    public function inspect(): array
    {
        $movementIds = DB::table('product_stock_movements AS movement')
            ->leftJoin('product_units AS unit', function ($join) {
                $join->on('unit.product_id', '=', 'movement.product_id')
                    ->on('unit.unit_name', '=', 'movement.unit')
                    ->on(
                        'unit.unit_quantity',
                        '=',
                        'movement.unit_quantity',
                    );
            })
            ->whereNotNull('movement.unit')
            ->whereNull('unit.id')
            ->orderBy('movement.id')
            ->pluck('movement.id');

        return $movementIds
            ->map(fn ($movementId) =>
                $this->inspectMovement((int) $movementId))
            ->filter()
            ->all();
    }

    private function inspectMovement(int $movementId): ?array
    {
        $movement = DB::table('product_stock_movements')
            ->where('id', $movementId)
            ->first();

        if (!$movement) {
            return null;
        }

        $product = DB::table('products')
            ->where('id', $movement->product_id)
            ->first([
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
            ]);
        $units = DB::table('product_units')
            ->where('product_id', $movement->product_id)
            ->orderBy('unit_quantity')
            ->orderBy('id')
            ->get([
                'id',
                'product_id',
                'unit_name',
                'unit_quantity',
                'price',
                'wholesale',
                'cost_price',
                'created_at',
                'updated_at',
            ]);
        $nearby = DB::table('product_stock_movements')
            ->whereBetween('id', [
                max(1, $movementId - 2),
                $movementId + 2,
            ])
            ->orderBy('id')
            ->get();
        $order = $movement->order_id === null
            ? null
            : DB::table('orders')
                ->where('id', $movement->order_id)
                ->first();
        $orderItems = $movement->order_id === null
            ? collect()
            : DB::table('order_items')
                ->where('order_id', $movement->order_id)
                ->orderBy('id')
                ->get();
        $nameCandidates = $units
            ->where('unit_name', $movement->unit)
            ->values();

        return [
            'key' => "movement.unit_signature.{$movementId}",
            'scope' => 'movements',
            'issue' => 'movement_unit_signature_mismatch',
            'subject_id' => $movementId,
            'status' => 'manual-only',
            'reason' => $movement->order_id === null
                ? 'No schema-backed transaction source proves which unit quantity was used.'
                : 'A movement is never changed automatically without a unique exact source allocation.',
            'before' => ['movement' => (array) $movement],
            'after' => null,
            'evidence' => [
                'product' => $product ? (array) $product : null,
                'current_product_units' =>
                    $units->map(fn ($row) => (array) $row)->all(),
                'same_name_candidates' =>
                    $nameCandidates->map(fn ($row) => (array) $row)->all(),
                'nearby_movements' =>
                    $nearby->map(fn ($row) => (array) $row)->all(),
                'order' => $order ? (array) $order : null,
                'order_items' =>
                    $orderItems->map(fn ($row) => (array) $row)->all(),
                'mismatch' => [
                    'movement_unit_name' => $movement->unit,
                    'movement_unit_quantity' =>
                        (int) $movement->unit_quantity,
                    'exact_signature_count' => $units->filter(
                        fn ($unit) =>
                            $unit->unit_name === $movement->unit &&
                            (int) $unit->unit_quantity ===
                                (int) $movement->unit_quantity
                    )->count(),
                ],
            ],
            'operation' => null,
            'fingerprint' => null,
        ];
    }
}
