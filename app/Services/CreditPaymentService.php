<?php

namespace App\Services;

use App\Models\CreditPaymentHistory;
use App\Models\CreditSale;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreditPaymentService
{
    public function paySelected(
        User $actor,
        int $customerId,
        array $saleIds,
        string $paymentMethod,
        string $requestToken
    ): array {
        return DB::transaction(function () use (
            $actor,
            $customerId,
            $saleIds,
            $paymentMethod,
            $requestToken
        ) {
            $actor = $this->lockActor($actor);
            $customer = $this->lockCustomer($customerId);
            $saleIds = collect($saleIds)
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();
            $existing = $this->lockExistingPayment(
                $actor,
                $requestToken
            );

            if ($existing->isNotEmpty()) {
                $this->assertExistingPayment(
                    $existing,
                    $customer,
                    $saleIds,
                    $paymentMethod
                );

                return $this->result($existing, true);
            }

            $sales = $this->lockCustomerSales($customer);
            $selected = $sales
                ->whereIn('id', $saleIds)
                ->sortBy('id')
                ->values();

            if ($selected->count() !== $saleIds->count()) {
                throw ValidationException::withMessages([
                    'sales' =>
                        'A selected credit sale belongs to another customer.',
                ]);
            }

            $histories = collect();

            foreach ($selected as $sale) {
                $amount = $this->outstandingCents($sale);
                $this->applyAllocation($sale, $amount);
                $histories->push($this->recordAllocation(
                    $actor,
                    $customer,
                    $sale,
                    $amount,
                    $paymentMethod,
                    $requestToken,
                    'selected'
                ));
            }

            $this->syncCustomerDebt($customer, $sales);

            return $this->result($histories, false);
        });
    }

    public function payAmount(
        User $actor,
        int $customerId,
        float $amount,
        string $paymentMethod,
        string $requestToken
    ): array {
        return DB::transaction(function () use (
            $actor,
            $customerId,
            $amount,
            $paymentMethod,
            $requestToken
        ) {
            $actor = $this->lockActor($actor);
            $customer = $this->lockCustomer($customerId);
            $amountCents = $this->cents($amount);
            $existing = $this->lockExistingPayment(
                $actor,
                $requestToken
            );

            if ($existing->isNotEmpty()) {
                $this->assertExistingPayment(
                    $existing,
                    $customer,
                    $existing->pluck('credit_sale_id')->sort()->values(),
                    $paymentMethod,
                    $amountCents
                );

                return $this->result($existing, true);
            }

            $sales = $this->lockCustomerSales($customer);
            $outstanding = $sales->sum(
                fn (CreditSale $sale) => $this->outstandingCents(
                    $sale,
                    false
                )
            );

            if ($amountCents > $outstanding) {
                throw ValidationException::withMessages([
                    'pay_amount' =>
                        'Payment exceeds the customer outstanding balance.',
                ]);
            }

            if ($outstanding < 1) {
                throw ValidationException::withMessages([
                    'pay_amount' => 'The customer has no outstanding balance.',
                ]);
            }

            $remainingPayment = $amountCents;
            $histories = collect();

            foreach ($sales as $sale) {
                if ($remainingPayment < 1) {
                    break;
                }

                $outstandingSale = $this->outstandingCents($sale, false);

                if ($outstandingSale < 1) {
                    continue;
                }

                $allocation = min($remainingPayment, $outstandingSale);
                $this->applyAllocation($sale, $allocation);
                $histories->push($this->recordAllocation(
                    $actor,
                    $customer,
                    $sale,
                    $allocation,
                    $paymentMethod,
                    $requestToken,
                    'oldest-first'
                ));
                $remainingPayment -= $allocation;
            }

            if ($remainingPayment !== 0) {
                throw ValidationException::withMessages([
                    'pay_amount' => 'Payment could not be fully allocated.',
                ]);
            }

            $this->syncCustomerDebt($customer, $sales);

            return $this->result($histories, false);
        });
    }

    public function payBill(
        User $actor,
        int $saleId,
        ?float $requestedAmount,
        string $paymentMethod,
        string $requestToken
    ): array {
        return DB::transaction(function () use (
            $actor,
            $saleId,
            $requestedAmount,
            $paymentMethod,
            $requestToken
        ) {
            $actor = $this->lockActor($actor);
            $customerId = CreditSale::query()
                ->whereKey($saleId)
                ->value('customer_id');

            if (!$customerId) {
                throw ValidationException::withMessages([
                    'credit_sale' => 'Credit sale was not found.',
                ]);
            }

            $customer = $this->lockCustomer((int) $customerId);
            $existing = $this->lockExistingPayment(
                $actor,
                $requestToken
            );

            if ($existing->isNotEmpty()) {
                $expectedAmount = $requestedAmount === null
                    ? null
                    : $this->cents($requestedAmount);
                $this->assertExistingPayment(
                    $existing,
                    $customer,
                    collect([$saleId]),
                    $paymentMethod,
                    $expectedAmount
                );

                return $this->result($existing, true);
            }

            $sales = $this->lockCustomerSales($customer);
            $sale = $sales->firstWhere('id', $saleId);

            if (!$sale) {
                throw ValidationException::withMessages([
                    'credit_sale' =>
                        'Credit sale belongs to another customer.',
                ]);
            }

            $outstanding = $this->outstandingCents($sale);
            $amount = $requestedAmount === null
                ? $outstanding
                : $this->cents($requestedAmount);

            if ($amount > $outstanding) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment exceeds the bill outstanding balance.',
                ]);
            }

            $this->applyAllocation($sale, $amount);
            $history = $this->recordAllocation(
                $actor,
                $customer,
                $sale,
                $amount,
                $paymentMethod,
                $requestToken,
                $requestedAmount === null ? 'full' : 'partial'
            );
            $this->syncCustomerDebt($customer, $sales);

            return $this->result(collect([$history]), false);
        });
    }

    private function lockActor(User $actor): User
    {
        $actor = User::query()
            ->lockForUpdate()
            ->findOrFail($actor->id);

        abort_unless(
            in_array(
                $actor->role,
                ['admin', 'manager', 'owner', 'staff'],
                true
            ),
            403
        );

        return $actor;
    }

    private function lockCustomer(int $customerId): Customer
    {
        return Customer::query()
            ->lockForUpdate()
            ->findOrFail($customerId);
    }

    private function lockCustomerSales(Customer $customer): Collection
    {
        return CreditSale::query()
            ->where('customer_id', $customer->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function lockExistingPayment(
        User $actor,
        string $requestToken
    ): Collection {
        return CreditPaymentHistory::query()
            ->where('user_id', $actor->id)
            ->where(
                'note',
                'like',
                $this->requestPrefix($requestToken) . '%'
            )
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    private function assertExistingPayment(
        Collection $histories,
        Customer $customer,
        Collection $saleIds,
        string $paymentMethod,
        ?int $amountCents = null
    ): void {
        $historySaleIds = $histories
            ->pluck('credit_sale_id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values();
        $expectedSaleIds = $saleIds
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values();
        $matches = $histories->every(
            fn (CreditPaymentHistory $history) =>
                (int) $history->customer_id === (int) $customer->id &&
                $history->payment_method === $paymentMethod
        ) && $historySaleIds->all() === $expectedSaleIds->all();

        if ($amountCents !== null) {
            $matches = $matches &&
                $histories->sum(
                    fn (CreditPaymentHistory $history) =>
                        $this->cents($history->amount)
                ) === $amountCents;
        }

        if (!$matches) {
            throw ValidationException::withMessages([
                'request_token' =>
                    'Payment request token was already used for another payment.',
            ]);
        }
    }

    private function outstandingCents(
        CreditSale $sale,
        bool $requirePayable = true
    ): int {
        $remaining = $this->cents($sale->remaining_amount);

        if (
            !in_array($sale->status, ['unpaid', 'partial', 'paid'], true) ||
            $remaining < 0 ||
            ($sale->status === 'paid' && $remaining !== 0)
        ) {
            throw ValidationException::withMessages([
                'credit_sale' => "Credit sale {$sale->id} is invalid.",
            ]);
        }

        if (
            $requirePayable &&
            ($sale->status === 'paid' || $remaining < 1)
        ) {
            throw ValidationException::withMessages([
                'credit_sale' =>
                    "Credit sale {$sale->id} has already been paid.",
            ]);
        }

        return $sale->status === 'paid' ? 0 : $remaining;
    }

    private function applyAllocation(
        CreditSale $sale,
        int $amountCents
    ): void {
        $outstanding = $this->outstandingCents($sale);

        if ($amountCents < 1 || $amountCents > $outstanding) {
            throw ValidationException::withMessages([
                'amount' => 'Payment allocation is invalid.',
            ]);
        }

        $remaining = $outstanding - $amountCents;
        $total = $this->cents($sale->total_amount);

        if ($remaining > $total) {
            throw ValidationException::withMessages([
                'credit_sale' =>
                    "Credit sale {$sale->id} balances are inconsistent.",
            ]);
        }

        $sale->remaining_amount = $this->money($remaining);
        $sale->paid_amount = $this->money($total - $remaining);
        $sale->status = $remaining === 0 ? 'paid' : 'partial';
        $sale->save();
    }

    private function recordAllocation(
        User $actor,
        Customer $customer,
        CreditSale $sale,
        int $amountCents,
        string $paymentMethod,
        string $requestToken,
        string $allocationType
    ): CreditPaymentHistory {
        return CreditPaymentHistory::create([
            'customer_id' => $customer->id,
            'credit_sale_id' => $sale->id,
            'amount' => $this->money($amountCents),
            'payment_method' => $paymentMethod,
            'user_id' => $actor->id,
            'note' => $this->requestPrefix($requestToken) .
                $allocationType,
        ]);
    }

    private function syncCustomerDebt(
        Customer $customer,
        Collection $sales
    ): void {
        $outstanding = $sales->sum(
            fn (CreditSale $sale) => $this->outstandingCents(
                $sale,
                false
            )
        );
        $customer->current_debt = $this->money($outstanding);
        $customer->save();
    }

    private function result(
        Collection $histories,
        bool $duplicate
    ): array {
        return [
            'duplicate' => $duplicate,
            'amount' => $this->money($histories->sum(
                fn (CreditPaymentHistory $history) =>
                    $this->cents($history->amount)
            )),
            'history_ids' => $histories->pluck('id')->all(),
        ];
    }

    private function requestPrefix(string $requestToken): string
    {
        return "credit-payment:{$requestToken}:";
    }

    private function cents(float|string|int $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function money(int $cents): float
    {
        return round($cents / 100, 2);
    }
}
