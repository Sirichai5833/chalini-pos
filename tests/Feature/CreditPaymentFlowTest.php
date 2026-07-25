<?php

namespace Tests\Feature;

use App\Models\CreditPaymentHistory;
use App\Models\CreditSale;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class CreditPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_payment_succeeds(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);

        $this->paySelected(
            $staff,
            $customer,
            [$sale->id]
        )->assertRedirect()
            ->assertSessionHas('success');

        $sale->refresh();

        $this->assertEquals(100, (float) $sale->paid_amount);
        $this->assertEquals(0, (float) $sale->remaining_amount);
        $this->assertSame('paid', $sale->status);
        $this->assertEquals(
            0,
            (float) $customer->fresh()->current_debt
        );
    }

    public function test_partial_payment_succeeds(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);

        $this->payBill(
            $staff,
            $sale,
            $this->billPayload('partial', 35)
        )->assertRedirect()
            ->assertSessionHas('success');

        $sale->refresh();

        $this->assertEquals(35, (float) $sale->paid_amount);
        $this->assertEquals(65, (float) $sale->remaining_amount);
        $this->assertSame('partial', $sale->status);
    }

    public function test_current_debt_is_recalculated_from_locked_sales(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(999);
        $first = $this->creditSale($customer, 70);
        $second = $this->creditSale($customer, 30);

        $this->payBill(
            $staff,
            $first,
            $this->billPayload('partial', 20)
        )->assertRedirect();

        $this->assertEquals(
            80,
            (float) $customer->fresh()->current_debt
        );
        $this->assertEquals(50, (float) $first->fresh()->remaining_amount);
        $this->assertEquals(30, (float) $second->fresh()->remaining_amount);
    }

    public function test_payment_history_is_created_with_server_actor(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(50);
        $sale = $this->creditSale($customer, 50);
        $token = (string) Str::uuid();

        $this->payBill(
            $staff,
            $sale,
            $this->billPayload('partial', 20, [
                'request_token' => $token,
                'payment_method' => 'qr',
            ])
        )->assertRedirect();

        $history = CreditPaymentHistory::sole();

        $this->assertSame($customer->id, $history->customer_id);
        $this->assertSame($sale->id, $history->credit_sale_id);
        $this->assertSame($staff->id, $history->user_id);
        $this->assertSame('qr', $history->payment_method);
        $this->assertEquals(20, (float) $history->amount);
        $this->assertStringStartsWith(
            "credit-payment:{$token}:",
            $history->note
        );
    }

    public function test_payment_is_allocated_oldest_bill_first(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(200);
        $first = $this->creditSale($customer, 100);
        $second = $this->creditSale($customer, 100);

        $this->payAll(
            $staff,
            $customer,
            $this->amountPayload(120)
        )->assertRedirect();

        $this->assertEquals(0, (float) $first->fresh()->remaining_amount);
        $this->assertSame('paid', $first->fresh()->status);
        $this->assertEquals(80, (float) $second->fresh()->remaining_amount);
        $this->assertSame('partial', $second->fresh()->status);
        $this->assertEquals(
            [$first->id, $second->id],
            CreditPaymentHistory::orderBy('id')
                ->pluck('credit_sale_id')
                ->all()
        );
        $this->assertEquals(
            [100.0, 20.0],
            CreditPaymentHistory::orderBy('id')
                ->get()
                ->map(fn ($history) => (float) $history->amount)
                ->all()
        );
    }

    public function test_zero_negative_and_text_amounts_are_rejected(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(100);
        $this->creditSale($customer, 100);

        foreach ([0, -1, 'not-a-number'] as $amount) {
            $this->payAll(
                $staff,
                $customer,
                $this->amountPayload($amount)
            )->assertSessionHasErrors('pay_amount');
        }

        $this->assertDatabaseCount('credit_payment_histories', 0);
        $this->assertEquals(
            100,
            (float) $customer->fresh()->current_debt
        );
    }

    public function test_overpayment_is_rejected(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);

        $this->payAll(
            $staff,
            $customer,
            $this->amountPayload(100.01)
        )->assertSessionHasErrors('pay_amount');

        $this->assertPaymentState($customer, $sale, 100);
    }

    public function test_fake_customer_is_rejected(): void
    {
        $staff = $this->staff();

        $this->actingAs($staff)
            ->post(
                route('staff.credit.pay.all', 999999),
                $this->amountPayload(10)
            )->assertNotFound();

        $this->assertDatabaseCount('credit_payment_histories', 0);
    }

    public function test_credit_sale_from_another_customer_is_rejected(): void
    {
        $staff = $this->staff();
        $firstCustomer = $this->customer(100);
        $secondCustomer = $this->customer(50);
        $firstSale = $this->creditSale($firstCustomer, 100);
        $otherSale = $this->creditSale($secondCustomer, 50);

        $this->paySelected(
            $staff,
            $firstCustomer,
            [$otherSale->id]
        )->assertSessionHasErrors('sales');

        $this->assertPaymentState(
            $firstCustomer,
            $firstSale,
            100
        );
        $this->assertPaymentState(
            $secondCustomer,
            $otherSale,
            50
        );
    }

    public function test_double_submit_does_not_reduce_debt_twice(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);
        $token = (string) Str::uuid();
        $payload = [
            'request_token' => $token,
            'sales' => [$sale->id],
            'payment_method' => 'cash',
        ];

        $this->paySelected(
            $staff,
            $customer,
            [$sale->id],
            $payload
        )->assertSessionHas('success');
        $this->paySelected(
            $staff,
            $customer,
            [$sale->id],
            $payload
        )->assertSessionHas('success');

        $this->assertEquals(
            0,
            (float) $customer->fresh()->current_debt
        );
        $this->assertDatabaseCount('credit_payment_histories', 1);
        $this->assertEquals(100, (float) CreditPaymentHistory::sole()->amount);
    }

    public function test_exception_rolls_back_payment_and_debt(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);

        CreditPaymentHistory::creating(function (): void {
            throw new RuntimeException('forced payment history failure');
        });

        try {
            $this->payBill(
                $staff,
                $sale,
                $this->billPayload('partial', 30)
            )->assertServerError();
        } finally {
            CreditPaymentHistory::flushEventListeners();
        }

        $this->assertPaymentState($customer, $sale, 100);
    }

    public function test_second_payment_cannot_make_debt_negative(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);

        $this->payAll(
            $staff,
            $customer,
            $this->amountPayload(60)
        )->assertSessionHas('success');
        $this->payAll(
            $staff,
            $customer,
            $this->amountPayload(60)
        )->assertSessionHasErrors('pay_amount');

        $this->assertEquals(
            40,
            (float) $customer->fresh()->current_debt
        );
        $this->assertEquals(40, (float) $sale->fresh()->remaining_amount);
        $this->assertEquals(
            60,
            (float) CreditPaymentHistory::sum('amount')
        );
    }

    public function test_member_cannot_receive_credit_payment(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);

        $this->paySelected(
            $member,
            $customer,
            [$sale->id]
        )->assertForbidden();

        $this->assertPaymentState($customer, $sale, 100);
    }

    public function test_client_actor_and_shift_have_no_effect(): void
    {
        $staff = $this->staff();
        $spoofed = $this->staff();
        $customer = $this->customer(100);
        $sale = $this->creditSale($customer, 100);

        $this->payBill(
            $staff,
            $sale,
            $this->billPayload('partial', 25, [
                'user_id' => $spoofed->id,
                'actor_id' => $spoofed->id,
                'shift_id' => 999999,
            ])
        )->assertSessionHas('success');

        $this->assertSame(
            $staff->id,
            CreditPaymentHistory::sole()->user_id
        );
    }

    public function test_paid_credit_sale_cannot_be_paid_again(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(0);
        $sale = $this->creditSale($customer, 100, [
            'paid_amount' => 100,
            'remaining_amount' => 0,
            'status' => 'paid',
        ]);

        $this->payBill(
            $staff,
            $sale,
            $this->billPayload('full')
        )->assertSessionHasErrors('credit_sale');

        $this->assertDatabaseCount('credit_payment_histories', 0);
        $this->assertEquals(0, (float) $customer->fresh()->current_debt);
    }

    public function test_allocation_never_exceeds_outstanding_balance(): void
    {
        $staff = $this->staff();
        $customer = $this->customer(125);
        $first = $this->creditSale($customer, 50);
        $second = $this->creditSale($customer, 75);

        $this->payAll(
            $staff,
            $customer,
            $this->amountPayload(125)
        )->assertSessionHas('success');

        $histories = CreditPaymentHistory::all();

        $this->assertEquals(125, (float) $histories->sum('amount'));
        $this->assertEquals(
            50,
            (float) $histories
                ->where('credit_sale_id', $first->id)
                ->sum('amount')
        );
        $this->assertEquals(
            75,
            (float) $histories
                ->where('credit_sale_id', $second->id)
                ->sum('amount')
        );
        $this->assertEquals(0, (float) $customer->fresh()->current_debt);
    }

    private function paySelected(
        User $actor,
        Customer $customer,
        array $saleIds,
        array $payload = []
    ) {
        return $this->actingAs($actor)
            ->from(route('staff.credit.customer', $customer->id))
            ->post(
                route('staff.credit.pay.selected', $customer->id),
                array_merge([
                    'request_token' => (string) Str::uuid(),
                    'sales' => $saleIds,
                    'payment_method' => 'cash',
                ], $payload)
            );
    }

    private function payAll(
        User $actor,
        Customer $customer,
        array $payload
    ) {
        return $this->actingAs($actor)
            ->from(route('staff.credit.customer', $customer->id))
            ->post(
                route('staff.credit.pay.all', $customer->id),
                $payload
            );
    }

    private function payBill(
        User $actor,
        CreditSale $sale,
        array $payload
    ) {
        return $this->actingAs($actor)
            ->from(route('staff.credit.customer', $sale->customer_id))
            ->post(
                route('staff.credit.pay.bill', $sale->id),
                $payload
            );
    }

    private function amountPayload(float|int|string $amount): array
    {
        return [
            'request_token' => (string) Str::uuid(),
            'pay_amount' => $amount,
            'payment_method' => 'cash',
        ];
    }

    private function billPayload(
        string $payType,
        float|int|null $amount = null,
        array $overrides = []
    ): array {
        return array_merge([
            'request_token' => (string) Str::uuid(),
            'pay_type' => $payType,
            'amount' => $amount,
            'payment_method' => 'cash',
        ], $overrides);
    }

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    private function customer(float $debt): Customer
    {
        return Customer::create([
            'name' => 'Payment Customer ' . Str::uuid(),
            'phone' => '0812345678',
            'credit_limit' => 10000,
            'current_debt' => $debt,
            'is_active' => true,
        ]);
    }

    private function creditSale(
        Customer $customer,
        float $total,
        array $attributes = []
    ): CreditSale {
        return CreditSale::create(array_merge([
            'customer_id' => $customer->id,
            'total_amount' => $total,
            'paid_amount' => 0,
            'remaining_amount' => $total,
            'due_date' => now()->addMonth()->toDateString(),
            'status' => 'unpaid',
        ], $attributes));
    }

    private function assertPaymentState(
        Customer $customer,
        CreditSale $sale,
        float $outstanding
    ): void {
        $this->assertEquals(
            $outstanding,
            (float) $customer->fresh()->current_debt
        );
        $this->assertEquals(
            $outstanding,
            (float) $sale->fresh()->remaining_amount
        );
        $this->assertDatabaseCount('credit_payment_histories', 0);
    }
}
