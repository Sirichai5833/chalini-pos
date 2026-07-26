<?php

namespace Tests\Feature;

use App\Models\Shift;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ShiftOpenLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_staff_can_open_shift_open_page(): void
    {
        $this->createShiftType();
        Setting::set('opening_cash', 500);

        $this->actingAs($this->user('staff'))
            ->get(route('shift.open'))
            ->assertOk()
            ->assertSee('เปิดกะการขาย');
    }

    public function test_shift_open_renders_the_shared_staff_shell(): void
    {
        $response = $this->actingAs($this->user('staff'))
            ->get(route('shift.open'));

        $response->assertOk()
            ->assertSee('id="sidebar"', false)
            ->assertSee('id="sidebarToggle"', false)
            ->assertSee('id="mainContentWrapper"', false);
    }

    public function test_shift_open_has_only_one_html_document(): void
    {
        $content = strtolower(
            $this->actingAs($this->user('staff'))
                ->get(route('shift.open'))
                ->assertOk()
                ->getContent()
        );

        $this->assertSame(1, substr_count($content, '<!doctype html>'));
        $this->assertSame(1, substr_count($content, '<html'));
        $this->assertSame(1, substr_count($content, '<head'));
        $this->assertSame(1, substr_count($content, '<body'));
        $this->assertSame(1, substr_count($content, 'bootstrap@5.3.5/dist/css/bootstrap.min.css'));
        $this->assertStringNotContainsString('bootstrap@5.3.0', $content);
        $this->assertStringNotContainsString('bootstrap-icons@1.11.1', $content);
    }

    public function test_validation_errors_render_through_the_shared_partial(): void
    {
        $errors = new ViewErrorBag();
        $errors->put('default', new MessageBag([
            'shift_type_id' => ['Please select a shift'],
        ]));

        $this->actingAs($this->user('staff'))
            ->withSession(['errors' => $errors])
            ->get(route('shift.open'))
            ->assertOk()
            ->assertSee('Please select a shift');
    }

    public function test_flash_messages_render_through_the_shared_partial(): void
    {
        $message = 'Shift open flash message';

        $response = $this->actingAs($this->user('staff'))
            ->withSession(['success' => $message])
            ->get(route('shift.open'));

        $response->assertOk()->assertSee($message);
        $this->assertSame(1, substr_count($response->getContent(), $message));
    }

    public function test_form_action_csrf_and_request_field_are_unchanged(): void
    {
        $shiftTypeId = $this->createShiftType();

        $response = $this->actingAs($this->user('staff'))
            ->get(route('shift.open'));

        $response->assertOk()
            ->assertSee('action="' . route('shift.store') . '"', false)
            ->assertSee('method="POST"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="shift_type_id"', false)
            ->assertSee('value="' . $shiftTypeId . '"', false);
    }

    public function test_member_cannot_access_shift_open(): void
    {
        $this->actingAs($this->user('member'))
            ->get(route('shift.open'))
            ->assertForbidden();
    }

    public function test_shift_close_still_renders_the_shared_layout(): void
    {
        $user = $this->user('staff');
        $this->openShiftFor($user);

        $this->actingAs($user)
            ->get(route('shift.close'))
            ->assertOk()
            ->assertSee('id="sidebar"', false)
            ->assertSee('action="' . route('shift.update') . '"', false)
            ->assertSee('name="closing_cash"', false);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function createShiftType(): int
    {
        return DB::table('shift_types')->insertGetId([
            'name' => 'Test Shift',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function openShiftFor(User $user): Shift
    {
        return Shift::create([
            'user_id' => $user->id,
            'shift_type_id' => $this->createShiftType(),
            'opening_cash' => 500,
            'opened_at' => now(),
            'status' => 'open',
        ]);
    }
}
