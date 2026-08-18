<?php

namespace Tests\Feature;

use App\Models\Shareholder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveUserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_logged_in_staff_loses_access_on_next_request(): void
    {
        $staff = $this->user('staff');
        $this->actingAs($staff)->get(route('staff.dashboard'))->assertOk();

        $staff->update(['is_active' => false]);

        $this->get(route('staff.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_blocked_logged_in_shareholder_loses_access_on_next_request(): void
    {
        $user = $this->user('shareholder');
        Shareholder::create([
            'user_id' => $user->id,
            'code' => 'ACTIVE-OWNER',
            'name' => 'Active Owner',
            'kitta' => 1,
            'per_kitta_value' => 1000,
            'total_investment' => 1000,
            'is_active' => true,
        ]);
        $this->actingAs($user)->get(route('shareholder.dashboard'))->assertOk();

        $user->update(['is_active' => false]);

        $this->get(route('shareholder.dashboard'))
            ->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_unblock_restores_future_login(): void
    {
        $staff = $this->user('staff', false);

        $this->post('/login', [
            'email' => $staff->email,
            'password' => 'password',
        ]);
        $this->assertGuest();

        $staff->update(['is_active' => true]);

        $this->post('/login', [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($staff);
    }

    private function user(string $role, bool $active = true): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => $active,
            'email_verified_at' => now(),
        ]);
    }
}
