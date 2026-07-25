<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class SafeStockReversalService
{
    public function reverseOrder(Order $order, string $reason): void
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException(
                'Stock reversal must run inside a database transaction.'
            );
        }

        $items = OrderItem::query()
            ->where('order_id', $order->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $movements = ProductStockMovement::query()
            ->where('order_id', $order->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($movements->contains(fn ($movement) => $movement->type === 'in')) {
            $this->reject(
                'This order already has a stock reversal movement.'
            );
        }

        $outMovements = $movements->where('type', 'out');

        if ($outMovements->isEmpty()) {
            if ($movements->isNotEmpty()) {
                $this->reject(
                    'Stock movements are incomplete; manual reconciliation is required.'
                );
            }

            return;
        }

        if (
            $items->isEmpty() ||
            $movements->count() !== $outMovements->count()
        ) {
            $this->reject(
                'Stock deduction cannot be proven; manual reconciliation is required.'
            );
        }

        $units = ProductUnit::query()
            ->whereIn('id', $items->pluck('product_unit_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($units->count() !== $items->pluck('product_unit_id')->unique()->count()) {
            $this->reject(
                'An order product unit is missing; manual reconciliation is required.'
            );
        }

        $productUnits = ProductUnit::query()
            ->whereIn('product_id', $units->pluck('product_id')->unique())
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $expected = $this->expectedDeductions(
            $items,
            $units,
            $productUnits
        );
        $actual = $this->actualDeductions($outMovements);

        if ($this->quantities($expected) !== $this->quantities($actual)) {
            $this->reject(
                'Conversion or incomplete stock movements were detected; manual reconciliation is required.'
            );
        }

        $unitIds = collect($expected)
            ->pluck('unit.id')
            ->sort()
            ->values();

        $stocks = ProductStocks::query()
            ->whereIn('unit_id', $unitIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->groupBy(fn ($stock) => $this->stockKey(
                (int) $stock->product_id,
                (int) $stock->unit_id
            ));

        foreach ($expected as $plan) {
            $unit = $plan['unit'];
            $stockRows = $stocks->get(
                $this->stockKey((int) $unit->product_id, (int) $unit->id),
                collect()
            );

            if ($stockRows->count() !== 1) {
                $this->reject(
                    'A unique stock row cannot be proven; manual reconciliation is required.'
                );
            }
        }

        foreach ($expected as $plan) {
            $unit = $plan['unit'];
            $quantity = $plan['quantity'];
            $stock = $stocks
                ->get($this->stockKey((int) $unit->product_id, (int) $unit->id))
                ->sole();

            $stock->store_stock = (int) $stock->store_stock + $quantity;
            $stock->save();

            ProductStockMovement::create([
                'order_id' => $order->id,
                'product_id' => $unit->product_id,
                'type' => 'in',
                'quantity' => $quantity,
                'unit_quantity' => $unit->unit_quantity,
                'unit' => $unit->unit_name,
                'location' => 'store',
                'note' => "คืนสต็อกจากการยกเลิกออเดอร์: {$reason}",
            ]);
        }
    }

    private function expectedDeductions(
        Collection $items,
        Collection $units,
        Collection $productUnits
    ): array {
        $unitSignatureCounts = $productUnits
            ->groupBy(fn (ProductUnit $unit) => $this->movementKey(
                (int) $unit->product_id,
                (string) $unit->unit_name,
                (int) $unit->unit_quantity
            ))
            ->map->count();
        $expected = [];

        foreach ($items as $item) {
            $unit = $units->get($item->product_unit_id);
            $quantity = (int) $item->quantity;
            $key = $this->movementKey(
                (int) $unit->product_id,
                (string) $unit->unit_name,
                (int) $unit->unit_quantity
            );

            if (
                $quantity < 1 ||
                (int) $unit->unit_quantity < 1 ||
                $unitSignatureCounts->get($key) !== 1
            ) {
                $this->reject(
                    'An order item unit is ambiguous; manual reconciliation is required.'
                );
            }

            $expected[$key] ??= [
                'unit' => $unit,
                'quantity' => 0,
            ];
            $expected[$key]['quantity'] += $quantity;
        }

        ksort($expected);

        return $expected;
    }

    private function actualDeductions(Collection $movements): array
    {
        $actual = [];

        foreach ($movements as $movement) {
            if (
                $movement->location !== 'store' ||
                (int) $movement->quantity < 1 ||
                (int) $movement->unit_quantity < 1 ||
                blank($movement->unit)
            ) {
                $this->reject(
                    'A stock movement is ambiguous; manual reconciliation is required.'
                );
            }

            $key = $this->movementKey(
                (int) $movement->product_id,
                (string) $movement->unit,
                (int) $movement->unit_quantity
            );
            $actual[$key] = ($actual[$key] ?? 0) +
                (int) $movement->quantity;
        }

        ksort($actual);

        return $actual;
    }

    private function quantities(array $plans): array
    {
        return array_map(
            fn ($plan) => is_array($plan) ? $plan['quantity'] : $plan,
            $plans
        );
    }

    private function movementKey(
        int $productId,
        string $unit,
        int $unitQuantity
    ): string {
        return "{$productId}|{$unit}|{$unitQuantity}";
    }

    private function stockKey(int $productId, int $unitId): string
    {
        return "{$productId}|{$unitId}";
    }

    private function reject(string $message): never
    {
        throw ValidationException::withMessages([
            'status' => $message,
        ]);
    }
}
