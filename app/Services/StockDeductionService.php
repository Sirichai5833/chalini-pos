<?php

namespace App\Services;

use App\Models\ProductStockMovement;
use App\Models\ProductStocks;
use App\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use LogicException;

class StockDeductionService
{
    public function deduct(
        ProductUnit $selectedUnit,
        int $quantity,
        string $reason,
        ?int $orderId = null
    ): void {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('Stock deduction must run inside a database transaction.');
        }

        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'products' => 'Quantity must be greater than zero.',
            ]);
        }

        $units = ProductUnit::query()
            ->where('product_id', $selectedUnit->product_id)
            ->orderBy('unit_quantity')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $selectedUnit = $units->firstWhere('id', $selectedUnit->id);
        $baseUnit = $units->first();

        if (!$selectedUnit || !$baseUnit) {
            throw ValidationException::withMessages([
                'products' => 'Product unit is not available.',
            ]);
        }

        $this->ensureValidUnitQuantity($selectedUnit);
        $this->ensureValidUnitQuantity($baseUnit);

        $stocks = ProductStocks::query()
            ->where('product_id', $selectedUnit->product_id)
            ->orderBy('unit_id')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $this->ensureUniqueStockRows($stocks);

        $stocksByUnit = $stocks->keyBy('unit_id');
        $selectedStock = $stocksByUnit->get($selectedUnit->id);
        $baseStock = $stocksByUnit->get($baseUnit->id);

        $directQuantity = min(
            $quantity,
            max(0, (int) ($selectedStock?->store_stock ?? 0))
        );

        $remainingQuantity = $quantity - $directQuantity;
        $requiredBaseQuantity = $this->baseQuantity(
            $selectedUnit,
            $remainingQuantity,
            $baseUnit
        );

        $baseAvailableAfterDirect = max(
            0,
            (int) ($baseStock?->store_stock ?? 0) -
                ($selectedUnit->is($baseUnit) ? $directQuantity : 0)
        );

        $conversionPlans = $this->planConversions(
            $units,
            $stocksByUnit,
            $selectedUnit,
            $baseUnit,
            max(0, $requiredBaseQuantity - $baseAvailableAfterDirect)
        );

        if ($directQuantity > 0 && $selectedStock) {
            $this->decrementStock($selectedStock, $directQuantity);
            $this->movement(
                $selectedUnit,
                'out',
                $directQuantity,
                $reason,
                $orderId
            );
        }

        if ($requiredBaseQuantity === 0) {
            return;
        }

        $baseStock ??= ProductStocks::create([
            'product_id' => $selectedUnit->product_id,
            'unit_id' => $baseUnit->id,
            'store_stock' => 0,
            'warehouse_stock' => 0,
        ]);

        foreach ($conversionPlans as $plan) {
            $this->decrementStock($plan['stock'], $plan['source_quantity']);
            $this->movement(
                $plan['unit'],
                'convert',
                $plan['source_quantity'],
                "{$reason}: ลดหน่วยเพื่อแตกเป็น {$baseUnit->unit_name}",
                $orderId
            );

            $this->incrementStock($baseStock, $plan['base_quantity']);
            $this->movement(
                $baseUnit,
                'convert',
                $plan['base_quantity'],
                "{$reason}: เพิ่มจากการแตกหน่วย {$plan['unit']->unit_name}",
                $orderId
            );
        }

        $this->decrementStock($baseStock, $requiredBaseQuantity);
        $this->movement(
            $baseUnit,
            'out',
            $requiredBaseQuantity,
            "{$reason}: ตัดหลังแตกหน่วย",
            $orderId
        );
    }

    private function planConversions(
        Collection $units,
        Collection $stocksByUnit,
        ProductUnit $selectedUnit,
        ProductUnit $baseUnit,
        int $shortfall
    ): array {
        $plans = [];

        foreach ($units as $unit) {
            if ($shortfall === 0) {
                break;
            }

            if ($unit->is($selectedUnit) || $unit->is($baseUnit)) {
                continue;
            }

            $stock = $stocksByUnit->get($unit->id);
            $available = max(0, (int) ($stock?->store_stock ?? 0));

            if (!$stock || $available === 0) {
                continue;
            }

            $basePerUnit = $this->baseQuantity($unit, 1, $baseUnit);
            $sourceQuantity = min($available, (int) ceil($shortfall / $basePerUnit));
            $convertedBaseQuantity = $sourceQuantity * $basePerUnit;

            $plans[] = [
                'unit' => $unit,
                'stock' => $stock,
                'source_quantity' => $sourceQuantity,
                'base_quantity' => $convertedBaseQuantity,
            ];

            $shortfall = max(0, $shortfall - $convertedBaseQuantity);
        }

        if ($shortfall > 0) {
            throw ValidationException::withMessages([
                'products' => "Insufficient stock for product {$selectedUnit->product_id}.",
            ]);
        }

        return $plans;
    }

    private function baseQuantity(
        ProductUnit $unit,
        int $quantity,
        ProductUnit $baseUnit
    ): int {
        $this->ensureValidUnitQuantity($unit);

        $absoluteQuantity = $quantity * (int) $unit->unit_quantity;
        $baseUnitQuantity = (int) $baseUnit->unit_quantity;

        if ($absoluteQuantity % $baseUnitQuantity !== 0) {
            throw ValidationException::withMessages([
                'products' => "Unit conversion is invalid for product {$unit->product_id}.",
            ]);
        }

        return intdiv($absoluteQuantity, $baseUnitQuantity);
    }

    private function ensureValidUnitQuantity(ProductUnit $unit): void
    {
        if ((int) $unit->unit_quantity < 1) {
            throw ValidationException::withMessages([
                'products' => "Unit quantity is invalid for product {$unit->product_id}.",
            ]);
        }
    }

    private function ensureUniqueStockRows(Collection $stocks): void
    {
        if ($stocks->groupBy('unit_id')->contains(fn (Collection $rows) => $rows->count() > 1)) {
            throw ValidationException::withMessages([
                'products' => 'Duplicate stock rows were found for a product unit.',
            ]);
        }
    }

    private function decrementStock(ProductStocks $stock, int $quantity): void
    {
        if ((int) $stock->store_stock < $quantity) {
            throw ValidationException::withMessages([
                'products' => "Insufficient stock for product {$stock->product_id}.",
            ]);
        }

        $stock->store_stock = (int) $stock->store_stock - $quantity;
        $stock->save();
    }

    private function incrementStock(ProductStocks $stock, int $quantity): void
    {
        $stock->store_stock = (int) $stock->store_stock + $quantity;
        $stock->save();
    }

    private function movement(
        ProductUnit $unit,
        string $type,
        int $quantity,
        string $note,
        ?int $orderId
    ): void {
        ProductStockMovement::create([
            'product_id' => $unit->product_id,
            'order_id' => $orderId,
            'type' => $type,
            'quantity' => $quantity,
            'unit_quantity' => $unit->unit_quantity,
            'unit' => $unit->unit_name,
            'location' => 'store',
            'note' => $note,
        ]);
    }
}
