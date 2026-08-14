<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_access_financial_dashboard(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)
            ->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('Staff Dashboard')
            ->assertSee('Monthly Financial Chart')
            ->assertDontSee('Your Operations');
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

}
