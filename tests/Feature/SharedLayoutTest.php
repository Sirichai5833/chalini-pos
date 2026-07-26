<?php

namespace Tests\Feature;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class SharedLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_layout_renders_for_an_authorized_user(): void
    {
        $response = $this->actingAs($this->user('staff'))
            ->get(route('staff.index'));

        $response->assertOk()
            ->assertSee('id="sidebar"', false)
            ->assertSee('id="sidebarToggle"', false)
            ->assertSee('id="mainContentWrapper"', false);
    }

    public function test_admin_sees_authorized_navigation_links(): void
    {
        $response = $this->actingAs($this->user('admin'))
            ->get(route('staff.index'));

        $response->assertOk()
            ->assertSee(route('staff.index'), false)
            ->assertSee(route('members.index'), false)
            ->assertSee(route('reports.daily'), false)
            ->assertSee(route('staff.settings'), false)
            ->assertSee(route('manager.dashboard'), false);
    }

    public function test_member_does_not_see_staff_or_admin_navigation(): void
    {
        $this->actingAs($this->user('member'));

        $html = view('layouts.layout')->render();

        $this->assertStringNotContainsString(route('staff.index'), $html);
        $this->assertStringNotContainsString(route('members.index'), $html);
        $this->assertStringNotContainsString(route('product.product.index'), $html);
        $this->assertStringNotContainsString(route('reports.daily'), $html);
        $this->assertStringNotContainsString(route('staff.settings'), $html);
    }

    public function test_shared_flash_success_is_rendered_once(): void
    {
        $message = 'Shared layout success message';

        $response = $this->actingAs($this->user('admin'))
            ->withSession(['success' => $message])
            ->get(route('staff.index'));

        $response->assertOk()->assertSee($message);
        $this->assertSame(1, substr_count($response->getContent(), $message));
    }

    public function test_shared_validation_errors_are_rendered_on_shift_close(): void
    {
        $user = $this->user('staff');
        $this->openShiftFor($user);
        $errors = new ViewErrorBag();
        $errors->put('default', new MessageBag([
            'closing_cash' => ['Closing cash is required'],
        ]));

        $this->actingAs($user)
            ->withSession(['errors' => $errors])
            ->get(route('shift.close'))
            ->assertOk()
            ->assertSee('Closing cash is required');
    }

    public function test_migrated_pages_render_for_authorized_roles(): void
    {
        $admin = $this->user('admin');
        $this->openShiftFor($admin);

        $this->actingAs($admin)
            ->get(route('staff.index'))
            ->assertOk();

        $this->get(route('members.index'))
            ->assertOk();

        $this->get(route('product.product.index'))
            ->assertOk();

        $this->get(route('reports.daily'))
            ->assertOk();

        $this->get(route('shift.close'))
            ->assertOk();
    }

    public function test_unauthorized_member_access_is_unchanged(): void
    {
        $this->actingAs($this->user('member'))
            ->get(route('staff.index'))
            ->assertForbidden();

        $this->get(route('members.index'))
            ->assertForbidden();

        $this->get(route('product.product.index'))
            ->assertForbidden();

        $this->get(route('reports.daily'))
            ->assertForbidden();

        $this->get(route('shift.close'))
            ->assertForbidden();
    }

    public function test_page_styles_use_the_shared_head_stack(): void
    {
        $content = $this->actingAs($this->user('admin'))
            ->get(route('product.product.index'))
            ->assertOk()
            ->getContent();

        $stylePosition = strpos($content, '.btn-edit-custom');
        $headEndPosition = strpos($content, '</head>');

        $this->assertNotFalse($stylePosition);
        $this->assertNotFalse($headEndPosition);
        $this->assertLessThan($headEndPosition, $stylePosition);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function openShiftFor(User $user): Shift
    {
        $shiftTypeId = DB::table('shift_types')->insertGetId([
            'name' => 'Test Shift',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Shift::create([
            'user_id' => $user->id,
            'shift_type_id' => $shiftTypeId,
            'opening_cash' => 1000,
            'opened_at' => now(),
            'status' => 'open',
        ]);
    }
}
