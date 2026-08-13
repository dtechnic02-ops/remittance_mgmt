<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_access_dashboard_and_no_permissions_show_safe_empty_state(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)
            ->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('Staff Dashboard')
            ->assertSee('No operational permissions have been assigned');
    }

    public function test_shareholder_and_guest_cannot_access_staff_dashboard(): void
    {
        $this->actingAs($this->user('shareholder'))
            ->get(route('staff.dashboard'))
            ->assertForbidden();

        auth()->logout();
        $this->get(route('staff.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_module_and_create_action_follow_exact_permissions(): void
    {
        $staff = $this->user('staff');
        $this->grant($staff, 'customer.view');

        $viewOnly = $this->actingAs($staff)->get(route('staff.dashboard'));
        $viewOnly->assertSee('Customers')->assertSee('View Customers')->assertDontSee('New Customer');

        $this->grant($staff, 'customer.create');
        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertSee('New Customer');
    }

    public function test_remittance_module_and_create_action_follow_exact_permissions(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertDontSee('Remittances');

        $this->grant($staff, 'remittance.view');
        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertSee('Remittances')
            ->assertDontSee('New Remittance');

        $this->grant($staff, 'remittance.create');
        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertSee('New Remittance');
    }

    public function test_staff_cannot_open_protected_url_without_permission(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)
            ->get(route('customers.index'))
            ->assertForbidden();

        $this->actingAs($staff)
            ->get(route('remittances.create'))
            ->assertForbidden();
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function grant(User $user, string $code): void
    {
        $permission = Permission::query()->where('code', $code)->firstOrFail();
        $user->permissions()->syncWithoutDetaching([
            $permission->id => ['assigned_at' => now()],
        ]);
    }
}
