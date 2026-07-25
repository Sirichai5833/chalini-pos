<?php

namespace App\Services\DatabaseIntegrity;

use Illuminate\Support\Facades\DB;

class CreditIntegrityChecks extends IntegrityCheckProvider
{
    public function definitions(): array
    {
        return [
            ...$this->creditSaleChecks(),
            ...$this->customerDebtChecks(),
            ...$this->paymentChecks(),
        ];
    }

    private function creditSaleChecks(): array
    {
        return [
            $this->check(
                'credit_sale.balance_mismatch',
                'Credit sale paid and remaining balances do not equal total',
                'critical',
                'Customer debt and allocation calculations start from inconsistent balances.',
                fn () => DB::table('credit_sales')
                    ->whereRaw(
                        'ABS((paid_amount + remaining_amount) - ' .
                        'total_amount) > 0.01',
                    )
                    ->select('id AS record_id'),
                [
                    'credit_sales' => [
                        'id',
                        'total_amount',
                        'paid_amount',
                        'remaining_amount',
                    ],
                ],
            ),
            $this->check(
                'credit_sale.negative_paid',
                'Credit sale has a negative paid amount',
                'critical',
                'Payment history and outstanding debt can be overstated.',
                fn () => DB::table('credit_sales')
                    ->where('paid_amount', '<', 0)
                    ->select('id AS record_id'),
                ['credit_sales' => ['id', 'paid_amount']],
            ),
            $this->check(
                'credit_sale.negative_remaining',
                'Credit sale has a negative remaining amount',
                'critical',
                'Concurrent or repeated payment may have overpaid the bill.',
                fn () => DB::table('credit_sales')
                    ->where('remaining_amount', '<', 0)
                    ->select('id AS record_id'),
                ['credit_sales' => ['id', 'remaining_amount']],
            ),
            $this->check(
                'credit_sale.paid_status_mismatch',
                'Paid credit sale has a remaining balance',
                'critical',
                'The bill is closed while debt remains outstanding.',
                fn () => DB::table('credit_sales')
                    ->where('status', 'paid')
                    ->where('remaining_amount', '>', 0.01)
                    ->select('id AS record_id'),
                ['credit_sales' => ['id', 'status', 'remaining_amount']],
            ),
            $this->check(
                'credit_sale.unpaid_status_mismatch',
                'Unpaid credit sale has a paid amount',
                'critical',
                'The status hides an allocation that has already changed balances.',
                fn () => DB::table('credit_sales')
                    ->where('status', 'unpaid')
                    ->where('paid_amount', '>', 0.01)
                    ->select('id AS record_id'),
                ['credit_sales' => ['id', 'status', 'paid_amount']],
            ),
            $this->check(
                'credit_sale.partial_status_mismatch',
                'Partial credit sale has impossible balances',
                'critical',
                'The partial-payment state cannot be reconciled.',
                fn () => DB::table('credit_sales')
                    ->where('status', 'partial')
                    ->where(fn ($query) => $query
                        ->where('paid_amount', '<=', 0)
                        ->orWhere('remaining_amount', '<=', 0)
                        ->orWhereColumn('paid_amount', '>=', 'total_amount')
                        ->orWhereColumn('remaining_amount', '>=', 'total_amount'))
                    ->select('id AS record_id'),
                [
                    'credit_sales' => [
                        'id',
                        'status',
                        'total_amount',
                        'paid_amount',
                        'remaining_amount',
                    ],
                ],
            ),
            $this->check(
                'credit_sale.missing_customer',
                'Credit sale references a missing customer',
                'critical',
                'The debt has no accountable customer.',
                fn () => DB::table('credit_sales AS credit_sale')
                    ->leftJoin('customers AS customer', 'customer.id', '=', 'credit_sale.customer_id')
                    ->whereNull('customer.id')
                    ->select('credit_sale.id AS record_id'),
                [
                    'credit_sales' => ['id', 'customer_id'],
                    'customers' => ['id'],
                ],
            ),
            $this->unsupported(
                'credit_sale.missing_sale',
                'Credit sale references a missing sale',
                'critical',
                'The credit ledger cannot be reconciled to the original POS sale.',
                'credit_sales has no sale_id or other sale reference.',
            ),
            $this->check(
                'credit_sale.without_origin_items',
                'Credit sale has no credit sale items',
                'warning',
                'The schema has no sale_id, so item rows are the only business-origin evidence.',
                fn () => DB::table('credit_sales AS credit_sale')
                    ->leftJoin(
                        'credit_sale_items AS item',
                        'item.credit_sale_id',
                        '=',
                        'credit_sale.id',
                    )
                    ->whereNull('item.id')
                    ->select('credit_sale.id AS record_id'),
                [
                    'credit_sales' => ['id'],
                    'credit_sale_items' => ['id', 'credit_sale_id'],
                ],
            ),
            $this->check(
                'credit_sale_item.missing_credit_sale',
                'Credit sale item references a missing credit sale',
                'critical',
                'The debt line has no owning credit ledger entry.',
                fn () => DB::table('credit_sale_items AS item')
                    ->leftJoin(
                        'credit_sales AS credit_sale',
                        'credit_sale.id',
                        '=',
                        'item.credit_sale_id',
                    )
                    ->whereNull('credit_sale.id')
                    ->select('item.id AS record_id'),
                [
                    'credit_sale_items' => ['id', 'credit_sale_id'],
                    'credit_sales' => ['id'],
                ],
            ),
        ];
    }

    private function customerDebtChecks(): array
    {
        return [
            $this->check(
                'customer.debt_mismatch',
                'Customer debt does not equal outstanding credit sales',
                'critical',
                'The customer ledger and bill balances disagree.',
                function () {
                    $outstanding = DB::table('credit_sales')
                        ->select('customer_id')
                        ->selectRaw(
                            'SUM(remaining_amount) AS outstanding_amount'
                        )
                        ->groupBy('customer_id');

                    return DB::table('customers AS customer')
                        ->leftJoinSub(
                            $outstanding,
                            'credit',
                            'credit.customer_id',
                            '=',
                            'customer.id',
                        )
                        ->whereRaw(
                            'ABS(customer.current_debt - ' .
                            'COALESCE(credit.outstanding_amount, 0)) > 0.01',
                        )
                        ->select('customer.id AS record_id');
                },
                [
                    'customers' => ['id', 'current_debt'],
                    'credit_sales' => ['customer_id', 'remaining_amount'],
                ],
            ),
            $this->check(
                'customer.negative_debt',
                'Customer has negative current debt',
                'critical',
                'A payment or reconciliation may have reduced debt more than once.',
                fn () => DB::table('customers')
                    ->where('current_debt', '<', 0)
                    ->select('id AS record_id'),
                ['customers' => ['id', 'current_debt']],
            ),
            $this->check(
                'customer.debt_without_outstanding_sale',
                'Customer has debt but no outstanding credit sale',
                'critical',
                'The aggregate debt has no bill-level source.',
                fn () => DB::table('customers AS customer')
                    ->where('customer.current_debt', '>', 0.01)
                    ->whereNotExists(fn ($credit) => $credit
                        ->selectRaw('1')
                        ->from('credit_sales AS credit_sale')
                        ->whereColumn(
                            'credit_sale.customer_id',
                            'customer.id',
                        )
                        ->where('credit_sale.remaining_amount', '>', 0.01))
                    ->select('customer.id AS record_id'),
                [
                    'customers' => ['id', 'current_debt'],
                    'credit_sales' => ['customer_id', 'remaining_amount'],
                ],
            ),
            $this->check(
                'customer.outstanding_sale_without_debt',
                'Customer has outstanding credit sales but no current debt',
                'critical',
                'Collection screens may hide bills that still require payment.',
                fn () => DB::table('customers AS customer')
                    ->where('customer.current_debt', '<=', 0.01)
                    ->whereExists(fn ($credit) => $credit
                        ->selectRaw('1')
                        ->from('credit_sales AS credit_sale')
                        ->whereColumn(
                            'credit_sale.customer_id',
                            'customer.id',
                        )
                        ->where('credit_sale.remaining_amount', '>', 0.01))
                    ->select('customer.id AS record_id'),
                [
                    'customers' => ['id', 'current_debt'],
                    'credit_sales' => ['customer_id', 'remaining_amount'],
                ],
            ),
        ];
    }

    private function paymentChecks(): array
    {
        return [
            $this->check(
                'payment.non_positive_amount',
                'Credit payment history has a non-positive amount',
                'critical',
                'The allocation cannot represent money received.',
                fn () => DB::table('credit_payment_histories')
                    ->where('amount', '<=', 0)
                    ->select('id AS record_id'),
                ['credit_payment_histories' => ['id', 'amount']],
            ),
            $this->check(
                'payment.missing_customer',
                'Credit payment references a missing customer',
                'critical',
                'The payment cannot be reconciled to a customer ledger.',
                fn () => DB::table('credit_payment_histories AS payment')
                    ->leftJoin('customers AS customer', 'customer.id', '=', 'payment.customer_id')
                    ->whereNull('customer.id')
                    ->select('payment.id AS record_id'),
                [
                    'credit_payment_histories' => ['id', 'customer_id'],
                    'customers' => ['id'],
                ],
            ),
            $this->check(
                'payment.missing_credit_sale',
                'Credit payment references no valid credit sale',
                'critical',
                'The payment has no bill allocation.',
                fn () => DB::table('credit_payment_histories AS payment')
                    ->leftJoin(
                        'credit_sales AS credit_sale',
                        'credit_sale.id',
                        '=',
                        'payment.credit_sale_id',
                    )
                    ->whereNull('credit_sale.id')
                    ->select('payment.id AS record_id'),
                [
                    'credit_payment_histories' => [
                        'id',
                        'credit_sale_id',
                    ],
                    'credit_sales' => ['id'],
                ],
            ),
            $this->check(
                'payment.missing_actor',
                'Credit payment references no valid actor',
                'critical',
                'There is no accountable user who received the payment.',
                fn () => DB::table('credit_payment_histories AS payment')
                    ->leftJoin('users AS user', 'user.id', '=', 'payment.user_id')
                    ->whereNull('user.id')
                    ->select('payment.id AS record_id'),
                [
                    'credit_payment_histories' => ['id', 'user_id'],
                    'users' => ['id'],
                ],
            ),
            $this->check(
                'payment.customer_mismatch',
                'Payment customer differs from the credit sale customer',
                'critical',
                'Money can be allocated to another customer’s bill.',
                fn () => DB::table('credit_payment_histories AS payment')
                    ->join(
                        'credit_sales AS credit_sale',
                        'credit_sale.id',
                        '=',
                        'payment.credit_sale_id',
                    )
                    ->whereColumn(
                        'payment.customer_id',
                        '<>',
                        'credit_sale.customer_id',
                    )
                    ->select('payment.id AS record_id'),
                [
                    'credit_payment_histories' => [
                        'id',
                        'customer_id',
                        'credit_sale_id',
                    ],
                    'credit_sales' => ['id', 'customer_id'],
                ],
            ),
            $this->check(
                'payment.overallocated_credit_sale',
                'Credit payment history exceeds the credit sale total',
                'critical',
                'The bill has received more money than its original amount.',
                fn () => DB::table('credit_sales AS credit_sale')
                    ->join(
                        'credit_payment_histories AS payment',
                        'payment.credit_sale_id',
                        '=',
                        'credit_sale.id',
                    )
                    ->selectRaw('credit_sale.id AS record_id')
                    ->groupBy('credit_sale.id', 'credit_sale.total_amount')
                    ->havingRaw(
                        'SUM(payment.amount) - credit_sale.total_amount > 0.01'
                    ),
                [
                    'credit_sales' => ['id', 'total_amount'],
                    'credit_payment_histories' => [
                        'credit_sale_id',
                        'amount',
                    ],
                ],
            ),
            $this->check(
                'payment.duplicate_token_allocation',
                'Duplicate credit payment token allocation',
                'critical',
                'The same request token allocated money to the same bill more than once.',
                fn () => DB::table('credit_payment_histories')
                    ->where('note', 'like', 'credit-payment:%')
                    ->selectRaw('MIN(id) AS record_id')
                    ->groupBy(
                        DB::raw('SUBSTR(note, 1, 51)'),
                        'credit_sale_id',
                    )
                    ->havingRaw('COUNT(*) > 1'),
                [
                    'credit_payment_histories' => [
                        'id',
                        'credit_sale_id',
                        'note',
                    ],
                ],
            ),
            $this->check(
                'payment.invalid_token',
                'Malformed credit payment idempotency token',
                'warning',
                'Retry detection cannot reliably identify the original payment.',
                function () {
                    $query = DB::table('credit_payment_histories')
                        ->where('note', 'like', 'credit-payment:%')
                        ->where(function ($invalid) {
                            $invalid->whereRaw(
                                "SUBSTR(note, 52, 1) <> ':'"
                            );
                            $this->orWhereMalformedPaymentUuid($invalid);
                        });

                    return $query->select('id AS record_id');
                },
                ['credit_payment_histories' => ['id', 'note']],
            ),
            $this->check(
                'payment.missing_note_payload',
                'Credit payment note is truncated or has no recognized payload',
                'warning',
                'The idempotency service cannot verify the original allocation type.',
                fn () => DB::table('credit_payment_histories')
                    ->where('note', 'like', 'credit-payment:%')
                    ->whereRaw(
                        "(LENGTH(note) < 53 OR SUBSTR(note, 52, 1) <> ':' OR " .
                        "SUBSTR(note, 53) NOT IN ('selected', 'bill', 'amount'))"
                    )
                    ->select('id AS record_id'),
                ['credit_payment_histories' => ['id', 'note']],
            ),
            $this->check(
                'payment.paid_sale_without_history',
                'Paid credit sale has no payment history',
                'warning',
                'The paid state has no receipt-level evidence in the current ledger.',
                fn () => DB::table('credit_sales AS credit_sale')
                    ->where('credit_sale.status', 'paid')
                    ->where('credit_sale.paid_amount', '>', 0.01)
                    ->whereNotExists(fn ($payment) => $payment
                        ->selectRaw('1')
                        ->from('credit_payment_histories AS payment')
                        ->whereColumn(
                            'payment.credit_sale_id',
                            'credit_sale.id',
                        ))
                    ->select('credit_sale.id AS record_id'),
                [
                    'credit_sales' => ['id', 'status', 'paid_amount'],
                    'credit_payment_histories' => ['credit_sale_id'],
                ],
            ),
        ];
    }

    private function orWhereMalformedPaymentUuid($query): void
    {
        $uuidRegex = '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-' .
            '[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}';

        if (DB::connection()->getDriverName() === 'mysql') {
            $query->orWhereRaw(
                'SUBSTR(note, 1, 51) NOT REGEXP ?',
                ['^credit-payment:' . $uuidRegex . '$'],
            );

            return;
        }

        $hex = '[0-9A-Fa-f]';
        $uuidGlob = str_repeat($hex, 8) . '-' .
            str_repeat($hex, 4) . '-' .
            str_repeat($hex, 4) . '-' .
            str_repeat($hex, 4) . '-' .
            str_repeat($hex, 12);

        $query->orWhereRaw(
            'SUBSTR(note, 1, 51) NOT GLOB ?',
            ['credit-payment:' . $uuidGlob],
        );
    }
}
