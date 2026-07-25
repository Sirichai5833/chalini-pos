<?php

namespace Tests\Feature;

use App\Http\Middleware\RoleMiddleware;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_a_protected_page(): void
    {
        $this->get(route('product.product.index'))
            ->assertRedirect(route('login'));
    }

    public function test_member_cannot_access_product_management(): void
    {
        $this->actingAs($this->user('member'))
            ->get(route('product.product.index'))
            ->assertForbidden();
    }

    public function test_member_cannot_access_stock_management(): void
    {
        $this->actingAs($this->user('member'))
            ->get(route('product.indexstock'))
            ->assertForbidden();
    }

    public function test_member_cannot_access_reports(): void
    {
        $this->actingAs($this->user('member'))
            ->get(route('reports.daily'))
            ->assertForbidden();
    }

    public function test_member_cannot_access_staff_or_member_management(): void
    {
        $member = $this->user('member');

        $this->actingAs($member)
            ->get(route('staff.index'))
            ->assertForbidden();

        $this->actingAs($member)
            ->get(route('members.index'))
            ->assertForbidden();
    }

    public function test_member_cannot_create_an_admin_even_if_route_middleware_is_bypassed(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs($this->user('member'))
            ->post(route('members.store'), $this->memberPayload([
                'email' => 'member-attempt@example.test',
                'role' => 'admin',
                'room_number' => '101',
            ]))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'email' => 'member-attempt@example.test',
            'role' => 'admin',
        ]);
    }

    public function test_staff_cannot_create_an_admin(): void
    {
        $this->actingAs($this->user('staff'))
            ->post(route('staff.store'), [
                'name' => 'Unauthorized Admin',
                'email' => 'staff-attempt@example.test',
                'role' => 'admin',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'email' => 'staff-attempt@example.test',
            'role' => 'admin',
        ]);
    }

    public function test_member_cannot_update_another_users_profile(): void
    {
        $member = $this->user('member');
        $otherMember = $this->user('member', [
            'name' => 'Original Name',
            'first_name' => 'Original',
            'last_name' => 'Name',
            'phone' => '0811111111',
        ]);

        $this->actingAs($member)
            ->put(route('online.update', $otherMember), [
                'first_name' => 'Changed',
                'last_name' => 'Name',
                'phone' => '0822222222',
                'email' => $otherMember->email,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $otherMember->id,
            'name' => 'Original Name',
            'phone' => '0811111111',
        ]);
    }

    public function test_admin_can_update_another_users_profile(): void
    {
        $admin = $this->user('admin');
        $member = $this->user('member', [
            'first_name' => 'Original',
            'last_name' => 'Member',
            'phone' => '0811111111',
        ]);

        $this->actingAs($admin)
            ->put(route('online.update', $member), [
                'first_name' => 'Updated',
                'last_name' => 'Member',
                'phone' => '0822222222',
                'email' => $member->email,
            ])
            ->assertRedirect(route('online.index'));

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Updated Member',
            'phone' => '0822222222',
        ]);
    }

    public function test_member_cannot_change_order_status(): void
    {
        $member = $this->user('member');
        $order = $this->orderFor($member);

        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs($member)
            ->patch(route('orders.updateStatus', $order), [
                'status' => 'processing',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_staff_cannot_change_an_order_assigned_to_another_staff_member(): void
    {
        $staff = $this->user('staff');
        $assignedStaff = $this->user('staff');
        $order = $this->orderFor($this->user('member'), $assignedStaff);

        $this->actingAs($staff)
            ->patch(route('orders.updateStatus', $order), [
                'status' => 'processing',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
            'assigned_to' => $assignedStaff->id,
        ]);
    }

    public function test_staff_can_change_an_order_assigned_to_them(): void
    {
        $staff = $this->user('staff');
        $order = $this->orderFor($this->user('member'), $staff);

        $this->actingAs($staff)
            ->patch(route('orders.updateStatus', $order), [
                'status' => 'processing',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
            'assigned_to' => $staff->id,
        ]);
    }

    public function test_staff_can_access_pos(): void
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckShopOpen::class,
            \App\Http\Middleware\CheckOpenShift::class,
        ])->actingAs($this->user('staff'))
            ->get(route('sale'))
            ->assertOk();
    }

    public function test_manager_can_access_manager_dashboard(): void
    {
        $this->actingAs($this->user('manager'))
            ->get(route('manager.dashboard'))
            ->assertOk();
    }

    public function test_manager_login_redirects_to_manager_dashboard(): void
    {
        $manager = $this->user('manager');

        $this->post(route('login'), [
            'email' => $manager->email,
            'password' => 'password',
        ])->assertRedirect(route('manager.dashboard'));
    }

    public function test_admin_can_access_system_management(): void
    {
        $this->actingAs($this->user('admin'))
            ->get(route('product.product.index'))
            ->assertOk();
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
        ], $attributes));
    }

    private function memberPayload(array $attributes = []): array
    {
        return array_merge([
            'first_name' => 'Test',
            'last_name' => 'Member',
            'phone' => '0812345678',
            'email' => 'new-member@example.test',
            'role' => 'member',
            'room_number' => '100',
        ], $attributes);
    }

    private function orderFor(User $member, ?User $assignedStaff = null): Order
    {
        return Order::create([
            'order_code' => 'ORD-' . fake()->unique()->numerify('######'),
            'user_id' => $member->id,
            'assigned_to' => $assignedStaff?->id,
            'payment_method' => 'cash',
            'status' => 'pending',
            'total_amount' => 100,
        ]);
    }
}
