<?php

namespace App\Services\DatabaseIntegrity;

use Illuminate\Support\Facades\DB;

class CommerceIntegrityChecks extends IntegrityCheckProvider
{
    public function definitions(): array
    {
        return [
            ...$this->saleChecks(),
            ...$this->orderChecks(),
        ];
    }

    private function saleChecks(): array
    {
        return [
            $this->check(
                'sale.without_items',
                'Sale has no sale items',
                'critical',
                'A financial total exists without an item-level audit trail.',
                fn () => DB::table('sales AS sale')
                    ->leftJoin('sale_items AS item', 'item.sale_id', '=', 'sale.id')
                    ->whereNull('item.id')
                    ->select('sale.id AS record_id'),
                [
                    'sales' => ['id'],
                    'sale_items' => ['id', 'sale_id'],
                ],
            ),
            $this->check(
                'sale_item.missing_sale',
                'Sale item references a missing sale',
                'critical',
                'The item has no financial transaction owner.',
                fn () => DB::table('sale_items AS item')
                    ->leftJoin('sales AS sale', 'sale.id', '=', 'item.sale_id')
                    ->whereNull('sale.id')
                    ->select('item.id AS record_id'),
                [
                    'sale_items' => ['id', 'sale_id'],
                    'sales' => ['id'],
                ],
            ),
            $this->check(
                'sale_item.missing_product_unit',
                'Sale item references a missing product unit',
                'critical',
                'The sold product, price source, and conversion cannot be proven.',
                fn () => DB::table('sale_items AS item')
                    ->leftJoin('product_units AS unit', 'unit.id', '=', 'item.product_unit_id')
                    ->whereNull('unit.id')
                    ->select('item.id AS record_id'),
                [
                    'sale_items' => ['id', 'product_unit_id'],
                    'product_units' => ['id'],
                ],
            ),
            $this->check(
                'sale_item.missing_product',
                'Sale item product unit references a missing product',
                'critical',
                'The historical line cannot be attributed to a product.',
                fn () => DB::table('sale_items AS item')
                    ->join('product_units AS unit', 'unit.id', '=', 'item.product_unit_id')
                    ->leftJoin('products AS product', 'product.id', '=', 'unit.product_id')
                    ->whereNull('product.id')
                    ->select('item.id AS record_id'),
                [
                    'sale_items' => ['id', 'product_unit_id'],
                    'product_units' => ['id', 'product_id'],
                    'products' => ['id'],
                ],
            ),
            $this->check(
                'sale_item.non_positive_quantity',
                'Sale item has a non-positive quantity',
                'critical',
                'The line cannot represent a valid sale deduction.',
                fn () => DB::table('sale_items')
                    ->where('quantity', '<=', 0)
                    ->select('id AS record_id'),
                ['sale_items' => ['id', 'quantity']],
            ),
            $this->check(
                'sale_item.negative_price',
                'Sale item has a negative price or subtotal',
                'critical',
                'The recorded sale total and revenue can be reduced below zero.',
                fn () => DB::table('sale_items')
                    ->where(fn ($query) => $query
                        ->where('price', '<', 0)
                        ->orWhereRaw('(quantity * price) < 0'))
                    ->select('id AS record_id'),
                ['sale_items' => ['id', 'quantity', 'price']],
            ),
            $this->unsupported(
                'sale_item.negative_stored_subtotal',
                'Sale item has a negative stored subtotal',
                'critical',
                'A negative stored subtotal can disagree with quantity and price.',
                'sale_items has no subtotal or total column.',
            ),
            $this->check(
                'sale.total_mismatch',
                'Sale total does not equal the sum of sale items',
                'critical',
                'Financial reporting disagrees with the immutable sale lines.',
                function () {
                    $totals = DB::table('sale_items')
                        ->select('sale_id')
                        ->selectRaw('SUM(quantity * price) AS item_total')
                        ->groupBy('sale_id');

                    return DB::table('sales AS sale')
                        ->leftJoinSub($totals, 'items', 'items.sale_id', '=', 'sale.id')
                        ->whereRaw(
                            'ABS(sale.total_price - COALESCE(items.item_total, 0)) > 0.01',
                        )
                        ->select('sale.id AS record_id');
                },
                [
                    'sales' => ['id', 'total_price'],
                    'sale_items' => ['sale_id', 'quantity', 'price'],
                ],
            ),
            $this->check(
                'sale.missing_staff',
                'Sale references a missing staff user',
                'critical',
                'The accountable actor for the financial transaction is missing.',
                fn () => DB::table('sales AS sale')
                    ->leftJoin('users AS user', 'user.id', '=', 'sale.staff_id')
                    ->whereNull('user.id')
                    ->select('sale.id AS record_id'),
                [
                    'sales' => ['id', 'staff_id'],
                    'users' => ['id'],
                ],
            ),
            $this->check(
                'sale.missing_shift',
                'Sale references a missing shift',
                'critical',
                'Cash attribution cannot be reconciled to a shift.',
                fn () => DB::table('sales AS sale')
                    ->leftJoin('shifts AS shift', 'shift.id', '=', 'sale.shift_id')
                    ->whereNotNull('sale.shift_id')
                    ->whereNull('shift.id')
                    ->select('sale.id AS record_id'),
                [
                    'sales' => ['id', 'shift_id'],
                    'shifts' => ['id'],
                ],
            ),
            $this->check(
                'sale.shift_staff_mismatch',
                'Sale is attached to another staff user’s shift',
                'critical',
                'Cash and accountability are attributed to different users.',
                fn () => DB::table('sales AS sale')
                    ->join('shifts AS shift', 'shift.id', '=', 'sale.shift_id')
                    ->whereColumn('sale.staff_id', '<>', 'shift.user_id')
                    ->select('sale.id AS record_id'),
                [
                    'sales' => ['id', 'staff_id', 'shift_id'],
                    'shifts' => ['id', 'user_id'],
                ],
            ),
            $this->check(
                'sale.duplicate_pos_token',
                'Duplicate POS request token',
                'critical',
                'A retried POS request may have created and deducted stock twice.',
                fn () => DB::table('sales')
                    ->where('slip', 'like', 'pos-request:%')
                    ->selectRaw('MIN(id) AS record_id')
                    ->groupBy('slip')
                    ->havingRaw('COUNT(*) > 1'),
                ['sales' => ['id', 'slip']],
            ),
            $this->check(
                'sale.invalid_pos_token',
                'Malformed POS request token',
                'warning',
                'Idempotency lookup cannot reliably identify the original request.',
                function () {
                    $query = DB::table('sales')
                        ->where('slip', 'like', 'pos-request:%');
                    $this->whereMalformedUuid(
                        $query,
                        'slip',
                        'pos-request:',
                    );

                    return $query->select('id AS record_id');
                },
                ['sales' => ['id', 'slip']],
            ),
        ];
    }

    private function orderChecks(): array
    {
        return [
            $this->check(
                'order.without_items',
                'Order has no order items',
                'critical',
                'An order total exists without an item-level audit trail.',
                fn () => DB::table('orders AS order_record')
                    ->leftJoin('order_items AS item', 'item.order_id', '=', 'order_record.id')
                    ->whereNull('item.id')
                    ->select('order_record.id AS record_id'),
                [
                    'orders' => ['id'],
                    'order_items' => ['id', 'order_id'],
                ],
            ),
            $this->check(
                'order_item.missing_order',
                'Order item references a missing order',
                'critical',
                'The item has no owning online transaction.',
                fn () => DB::table('order_items AS item')
                    ->leftJoin('orders AS order_record', 'order_record.id', '=', 'item.order_id')
                    ->whereNull('order_record.id')
                    ->select('item.id AS record_id'),
                [
                    'order_items' => ['id', 'order_id'],
                    'orders' => ['id'],
                ],
            ),
            $this->check(
                'order_item.missing_product_unit',
                'Order item references a missing product unit',
                'critical',
                'The ordered product and server price source cannot be proven.',
                fn () => DB::table('order_items AS item')
                    ->leftJoin('product_units AS unit', 'unit.id', '=', 'item.product_unit_id')
                    ->whereNull('unit.id')
                    ->select('item.id AS record_id'),
                [
                    'order_items' => ['id', 'product_unit_id'],
                    'product_units' => ['id'],
                ],
            ),
            $this->check(
                'order_item.missing_product',
                'Order item product unit references a missing product',
                'critical',
                'The historical order line cannot be attributed to a product.',
                fn () => DB::table('order_items AS item')
                    ->join('product_units AS unit', 'unit.id', '=', 'item.product_unit_id')
                    ->leftJoin('products AS product', 'product.id', '=', 'unit.product_id')
                    ->whereNull('product.id')
                    ->select('item.id AS record_id'),
                [
                    'order_items' => ['id', 'product_unit_id'],
                    'product_units' => ['id', 'product_id'],
                    'products' => ['id'],
                ],
            ),
            $this->check(
                'order_item.invalid_amount',
                'Order item has an invalid quantity, price, cost, or total',
                'critical',
                'The line can corrupt stock, revenue, or margin reporting.',
                fn () => DB::table('order_items')
                    ->where(fn ($query) => $query
                        ->where('quantity', '<=', 0)
                        ->orWhere('price', '<', 0)
                        ->orWhere('cost', '<', 0)
                        ->orWhere('total', '<', 0)
                        ->orWhereRaw(
                            'ABS(total - (quantity * price)) > 0.01'
                        ))
                    ->select('id AS record_id'),
                [
                    'order_items' => [
                        'id',
                        'quantity',
                        'price',
                        'cost',
                        'total',
                    ],
                ],
            ),
            $this->check(
                'order.total_mismatch',
                'Order total does not equal the sum of order items',
                'critical',
                'Payment and reporting disagree with the order lines.',
                function () {
                    $totals = DB::table('order_items')
                        ->select('order_id')
                        ->selectRaw('SUM(total) AS item_total')
                        ->groupBy('order_id');

                    return DB::table('orders AS order_record')
                        ->leftJoinSub(
                            $totals,
                            'items',
                            'items.order_id',
                            '=',
                            'order_record.id',
                        )
                        ->whereRaw(
                            'ABS(order_record.total_amount - ' .
                            'COALESCE(items.item_total, 0)) > 0.01',
                        )
                        ->select('order_record.id AS record_id');
                },
                [
                    'orders' => ['id', 'total_amount'],
                    'order_items' => ['order_id', 'total'],
                ],
            ),
            $this->check(
                'order.duplicate_token',
                'Duplicate order token',
                'critical',
                'A checkout retry can create more than one order.',
                fn () => DB::table('orders')
                    ->whereNotNull('order_token')
                    ->selectRaw('MIN(id) AS record_id')
                    ->groupBy('order_token')
                    ->havingRaw('COUNT(*) > 1'),
                ['orders' => ['id', 'order_token']],
            ),
            $this->check(
                'order.invalid_token',
                'Empty or malformed non-null order token',
                'warning',
                'Checkout idempotency cannot reliably identify the original order.',
                function () {
                    $query = DB::table('orders')
                        ->whereNotNull('order_token');
                    $this->whereMalformedUuid(
                        $query,
                        'order_token',
                    );

                    return $query->select('id AS record_id');
                },
                ['orders' => ['id', 'order_token']],
            ),
            $this->check(
                'order.cancelled_stock_contradiction',
                'Cancelled order has contradictory stock movements',
                'critical',
                'Cancellation status and stock reversal history disagree.',
                fn () => DB::table('orders AS order_record')
                    ->whereIn('order_record.status', ['cancelled', 'ยกเลิก'])
                    ->where(function ($query) {
                        $query
                            ->where(function ($inner) {
                                $inner->whereExists(fn ($movement) => $movement
                                    ->selectRaw('1')
                                    ->from('product_stock_movements AS movement_in')
                                    ->whereColumn(
                                        'movement_in.order_id',
                                        'order_record.id',
                                    )
                                    ->where('movement_in.type', 'in'))
                                    ->whereNotExists(fn ($movement) => $movement
                                        ->selectRaw('1')
                                        ->from('product_stock_movements AS movement_out')
                                        ->whereColumn(
                                            'movement_out.order_id',
                                            'order_record.id',
                                        )
                                        ->where('movement_out.type', 'out'));
                            })
                            ->orWhere(function ($inner) {
                                $inner->whereExists(fn ($movement) => $movement
                                    ->selectRaw('1')
                                    ->from('product_stock_movements AS movement_out')
                                    ->whereColumn(
                                        'movement_out.order_id',
                                        'order_record.id',
                                    )
                                    ->where('movement_out.type', 'out'))
                                    ->whereNotExists(fn ($movement) => $movement
                                        ->selectRaw('1')
                                        ->from('product_stock_movements AS movement_in')
                                        ->whereColumn(
                                            'movement_in.order_id',
                                            'order_record.id',
                                        )
                                        ->where('movement_in.type', 'in'));
                            })
                            ->orWhereExists(fn ($movement) => $movement
                                ->selectRaw('1')
                                ->from('product_stock_movements AS movement_convert')
                                ->whereColumn(
                                    'movement_convert.order_id',
                                    'order_record.id',
                                )
                                ->where('movement_convert.type', 'convert'));
                    })
                    ->select('order_record.id AS record_id'),
                [
                    'orders' => ['id', 'status'],
                    'product_stock_movements' => [
                        'order_id',
                        'type',
                    ],
                ],
            ),
            $this->check(
                'order.owner_role_mismatch',
                'Order owner is not a member',
                'warning',
                'Online order ownership does not match the member checkout model.',
                fn () => DB::table('orders AS order_record')
                    ->join('users AS user', 'user.id', '=', 'order_record.user_id')
                    ->where('user.role', '<>', 'member')
                    ->select('order_record.id AS record_id'),
                [
                    'orders' => ['id', 'user_id'],
                    'users' => ['id', 'role'],
                ],
            ),
            $this->unsupported(
                'order.customer_owner_mismatch',
                'Order customer and user ownership disagree',
                'warning',
                'Two competing owner references would weaken authorization.',
                'orders has user_id but no customer_id or customer relation.',
            ),
            $this->check(
                'order.missing_owner',
                'Order has no user owner',
                'warning',
                'Ownership and authorization cannot be enforced for the order.',
                fn () => DB::table('orders')
                    ->whereNull('user_id')
                    ->select('id AS record_id'),
                ['orders' => ['id', 'user_id']],
            ),
            $this->check(
                'order.missing_assignee',
                'Order references a missing assigned user',
                'critical',
                'Server-side staff ownership checks cannot identify the assignee.',
                fn () => DB::table('orders AS order_record')
                    ->leftJoin('users AS user', 'user.id', '=', 'order_record.assigned_to')
                    ->whereNotNull('order_record.assigned_to')
                    ->whereNull('user.id')
                    ->select('order_record.id AS record_id'),
                [
                    'orders' => ['id', 'assigned_to'],
                    'users' => ['id'],
                ],
            ),
        ];
    }

    private function whereMalformedUuid(
        $query,
        string $column,
        string $prefix = '',
    ): void {
        $uuidRegex = '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-' .
            '[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}';

        if (DB::connection()->getDriverName() === 'mysql') {
            $query->whereRaw(
                "{$column} NOT REGEXP ?",
                ['^' . preg_quote($prefix, '/') . $uuidRegex . '$'],
            );

            return;
        }

        $hex = '[0-9A-Fa-f]';
        $uuidGlob = str_repeat($hex, 8) . '-' .
            str_repeat($hex, 4) . '-' .
            str_repeat($hex, 4) . '-' .
            str_repeat($hex, 4) . '-' .
            str_repeat($hex, 12);

        $query->whereRaw(
            "{$column} NOT GLOB ?",
            [$prefix . $uuidGlob],
        );
    }
}
