<?php

namespace Tests\Feature;

use App\Http\Controllers\CreditSaleController;
use App\Models\CreditPaymentHistory;
use App\Models\CreditSale;
use App\Models\Customer;
use App\Models\User;
use App\Services\CreditPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreditPaymentRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_broken_legacy_payment_route_is_not_registered(): void
    {
        $this->assertNull(
            Route::getRoutes()->getByName('staff.credit.sales.pay')
        );
        $this->assertNotContains(
            CreditSaleController::class . '@pay',
            $this->creditPaymentActions()
        );

        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->post('/staff/credit-sales/999999/pay')
            ->assertNotFound();
    }

    public function test_member_cannot_use_removed_legacy_endpoint(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
            ->post('/staff/credit-sales/999999/pay')
            ->assertNotFound();
    }

    public function test_active_payment_routes_are_registered(): void
    {
        $expected = [
            'staff.credit.pay.selected' => 'paySelected',
            'staff.credit.pay.bill' => 'payBill',
            'staff.credit.pay.all' => 'payAll',
        ];

        foreach ($expected as $name => $method) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] is missing.");
            $this->assertSame(
                CreditSaleController::class . '@' . $method,
                $route->getActionName()
            );
        }
    }

    public function test_active_payment_routes_keep_role_authorization(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        foreach ([
            ['staff.credit.pay.selected', 999999],
            ['staff.credit.pay.bill', 999999],
            ['staff.credit.pay.all', 999999],
        ] as [$name, $id]) {
            $this->actingAs($member)
                ->post(route($name, $id))
                ->assertForbidden();
        }
    }

    public function test_active_http_payment_writers_delegate_to_service(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = $this->customer();
        $sale = $this->creditSale($customer);

        $this->mock(CreditPaymentService::class, function ($mock): void {
            $mock->shouldReceive('paySelected')
                ->once()
                ->andReturn(['duplicate' => false]);
            $mock->shouldReceive('payBill')
                ->once()
                ->andReturn(['duplicate' => false]);
            $mock->shouldReceive('payAmount')
                ->once()
                ->andReturn(['duplicate' => false]);
        });

        $this->actingAs($staff)
            ->post(route('staff.credit.pay.selected', $customer->id), [
                'request_token' => (string) Str::uuid(),
                'sales' => [$sale->id],
                'payment_method' => 'cash',
            ])
            ->assertRedirect();

        $this->actingAs($staff)
            ->post(route('staff.credit.pay.bill', $sale->id), [
                'request_token' => (string) Str::uuid(),
                'pay_type' => 'full',
                'payment_method' => 'cash',
            ])
            ->assertRedirect();

        $this->actingAs($staff)
            ->post(route('staff.credit.pay.all', $customer->id), [
                'request_token' => (string) Str::uuid(),
                'pay_amount' => 100,
                'payment_method' => 'cash',
            ])
            ->assertRedirect();

        $this->assertEquals(100, (float) $customer->fresh()->current_debt);
        $this->assertEquals(100, (float) $sale->fresh()->remaining_amount);
        $this->assertDatabaseCount('credit_payment_histories', 0);
    }

    private function creditPaymentActions(): array
    {
        return collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getActionName())
            ->filter(fn ($action) => str_starts_with(
                $action,
                CreditSaleController::class . '@pay'
            ))
            ->values()
            ->all();
    }

    private function customer(): Customer
    {
        return Customer::create([
            'name' => 'Route Audit Customer',
            'phone' => '0812345678',
            'credit_limit' => 1000,
            'current_debt' => 100,
            'is_active' => true,
        ]);
    }

    private function creditSale(Customer $customer): CreditSale
    {
        return CreditSale::create([
            'customer_id' => $customer->id,
            'total_amount' => 100,
            'paid_amount' => 0,
            'remaining_amount' => 100,
            'due_date' => now()->addMonth()->toDateString(),
            'status' => 'unpaid',
        ]);
    }
}
