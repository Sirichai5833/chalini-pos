<?php

namespace App\Services\DatabaseIntegrity;

use Illuminate\Support\Facades\DB;

class StockIntegrityChecks extends IntegrityCheckProvider
{
    public function definitions(): array
    {
        return [
            $this->check(
                'stock.duplicate_rows',
                'Duplicate product stock rows',
                'critical',
                'Stock deductions cannot select a unique balance row.',
                fn () => DB::table('product_stocks')
                    ->selectRaw('MIN(id) AS record_id')
                    ->groupBy('product_id', 'unit_id')
                    ->havingRaw('COUNT(*) > 1'),
                ['product_stocks' => ['id', 'product_id', 'unit_id']],
            ),
            $this->check(
                'stock.negative_quantity',
                'Negative product stock quantity',
                'critical',
                'Available stock and overselling decisions are unreliable.',
                fn () => DB::table('product_stocks')
                    ->where(fn ($query) => $query
                        ->where('store_stock', '<', 0)
                        ->orWhere('warehouse_stock', '<', 0))
                    ->select('id AS record_id'),
                [
                    'product_stocks' => [
                        'id',
                        'store_stock',
                        'warehouse_stock',
                    ],
                ],
            ),
            $this->check(
                'stock.missing_product',
                'Product stock references a missing product',
                'critical',
                'The stock balance has no valid product owner.',
                fn () => DB::table('product_stocks AS stock')
                    ->leftJoin('products AS product', 'product.id', '=', 'stock.product_id')
                    ->whereNull('product.id')
                    ->select('stock.id AS record_id'),
                [
                    'product_stocks' => ['id', 'product_id'],
                    'products' => ['id'],
                ],
            ),
            $this->check(
                'stock.missing_product_unit',
                'Product stock references a missing product unit',
                'critical',
                'The unit-specific balance cannot be priced or converted.',
                fn () => DB::table('product_stocks AS stock')
                    ->leftJoin('product_units AS unit', 'unit.id', '=', 'stock.unit_id')
                    ->whereNull('unit.id')
                    ->select('stock.id AS record_id'),
                [
                    'product_stocks' => ['id', 'unit_id'],
                    'product_units' => ['id'],
                ],
            ),
            $this->check(
                'stock.unit_product_mismatch',
                'Product stock unit belongs to another product',
                'critical',
                'A deduction may change stock for the wrong product.',
                fn () => DB::table('product_stocks AS stock')
                    ->join('product_units AS unit', 'unit.id', '=', 'stock.unit_id')
                    ->whereColumn('unit.product_id', '<>', 'stock.product_id')
                    ->select('stock.id AS record_id'),
                [
                    'product_stocks' => ['id', 'product_id', 'unit_id'],
                    'product_units' => ['id', 'product_id'],
                ],
            ),
            $this->check(
                'stock.active_product_unit_without_stock',
                'Active product unit has no stock row',
                'warning',
                'The product may be unsellable or rely on an implicit zero balance.',
                fn () => DB::table('products AS product')
                    ->join('product_units AS unit', 'unit.product_id', '=', 'product.id')
                    ->leftJoin('product_stocks AS stock', function ($join) {
                        $join->on('stock.product_id', '=', 'product.id')
                            ->on('stock.unit_id', '=', 'unit.id');
                    })
                    ->where('product.is_active', true)
                    ->whereNull('stock.id')
                    ->selectRaw('MIN(product.id) AS record_id')
                    ->groupBy('product.id'),
                [
                    'products' => ['id', 'is_active'],
                    'product_units' => ['id', 'product_id'],
                    'product_stocks' => ['id', 'product_id', 'unit_id'],
                ],
            ),
            $this->check(
                'movement.missing_product',
                'Stock movement references a missing product',
                'critical',
                'The movement cannot be reconciled to a current product.',
                fn () => DB::table('product_stock_movements AS movement')
                    ->leftJoin('products AS product', 'product.id', '=', 'movement.product_id')
                    ->whereNull('product.id')
                    ->select('movement.id AS record_id'),
                [
                    'product_stock_movements' => ['id', 'product_id'],
                    'products' => ['id'],
                ],
            ),
            $this->check(
                'movement.missing_product_unit_signature',
                'Stock movement has no matching product unit signature',
                'critical',
                'The recorded quantity cannot be mapped to a known unit safely.',
                fn () => DB::table('product_stock_movements AS movement')
                    ->leftJoin('product_units AS unit', function ($join) {
                        $join->on('unit.product_id', '=', 'movement.product_id')
                            ->on('unit.unit_name', '=', 'movement.unit')
                            ->on('unit.unit_quantity', '=', 'movement.unit_quantity');
                    })
                    ->whereNotNull('movement.unit')
                    ->whereNull('unit.id')
                    ->select('movement.id AS record_id'),
                [
                    'product_stock_movements' => [
                        'id',
                        'product_id',
                        'unit',
                        'unit_quantity',
                    ],
                    'product_units' => [
                        'id',
                        'product_id',
                        'unit_name',
                        'unit_quantity',
                    ],
                ],
            ),
            $this->check(
                'movement.invalid_quantity',
                'Stock movement has a non-positive quantity or unit quantity',
                'critical',
                'The movement direction or converted quantity is ambiguous.',
                fn () => DB::table('product_stock_movements')
                    ->where(fn ($query) => $query
                        ->where('quantity', '<=', 0)
                        ->orWhere('unit_quantity', '<=', 0))
                    ->select('id AS record_id'),
                [
                    'product_stock_movements' => [
                        'id',
                        'quantity',
                        'unit_quantity',
                    ],
                ],
            ),
            $this->unsupported(
                'movement.invalid_balance',
                'Stock movement has an unreasonable running balance',
                'warning',
                'A corrupt running balance can conceal missing movements.',
                'product_stock_movements has no balance column.',
            ),
            $this->unsupported(
                'movement.multiple_transaction_references',
                'Stock movement references both a sale and an order',
                'critical',
                'The movement would have two conflicting source transactions.',
                'product_stock_movements has order_id but no sale_id.',
            ),
            $this->check(
                'movement.untraceable_source',
                'Stock movement has no schema-backed transaction source',
                'warning',
                'The movement cannot be traced without relying on free-form notes.',
                fn () => DB::table('product_stock_movements')
                    ->whereNull('order_id')
                    ->select('id AS record_id'),
                ['product_stock_movements' => ['id', 'order_id']],
            ),
            $this->check(
                'unit.duplicate_product_name',
                'Duplicate product units for the same product and unit name',
                'critical',
                'Unit selection and movement signature matching are ambiguous.',
                fn () => DB::table('product_units')
                    ->selectRaw('MIN(id) AS record_id')
                    ->groupBy('product_id', 'unit_name')
                    ->havingRaw('COUNT(*) > 1'),
                [
                    'product_units' => [
                        'id',
                        'product_id',
                        'unit_name',
                    ],
                ],
            ),
            $this->check(
                'unit.non_positive_conversion',
                'Product unit has a non-positive unit quantity',
                'critical',
                'Stock conversion would divide by or multiply with an invalid factor.',
                fn () => DB::table('product_units')
                    ->where('unit_quantity', '<=', 0)
                    ->select('id AS record_id'),
                ['product_units' => ['id', 'unit_quantity']],
            ),
            $this->check(
                'unit.negative_price',
                'Product unit has a negative retail, wholesale, or cost price',
                'critical',
                'Server-side sale totals or margins can become negative.',
                fn () => DB::table('product_units')
                    ->where(fn ($query) => $query
                        ->where('price', '<', 0)
                        ->orWhere('wholesale', '<', 0)
                        ->orWhere('cost_price', '<', 0))
                    ->select('id AS record_id'),
                [
                    'product_units' => [
                        'id',
                        'price',
                        'wholesale',
                        'cost_price',
                    ],
                ],
            ),
            $this->check(
                'unit.missing_product',
                'Product unit references a missing product',
                'critical',
                'The unit cannot be sold, stocked, or converted.',
                fn () => DB::table('product_units AS unit')
                    ->leftJoin('products AS product', 'product.id', '=', 'unit.product_id')
                    ->whereNull('product.id')
                    ->select('unit.id AS record_id'),
                [
                    'product_units' => ['id', 'product_id'],
                    'products' => ['id'],
                ],
            ),
            $this->unsupported(
                'unit.missing_unit_record',
                'Product unit references a missing unit record',
                'critical',
                'A missing canonical unit would make conversion ambiguous.',
                'product_units stores unit_name text and has no unit_id relation.',
            ),
            $this->unsupported(
                'unit.conversion_cycle',
                'Product unit conversion is cyclic or self-referencing',
                'critical',
                'Cyclic conversion can multiply stock repeatedly.',
                'The schema stores an absolute unit_quantity and no conversion edge relation.',
            ),
        ];
    }
}
