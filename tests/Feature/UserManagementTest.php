<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_all_supported_login_roles_from_user_management(): void
    {
        $admin = $this->user('admin');

        foreach (['staff', 'shareholder', 'help_desk'] as $role) {
            $email = $role.'@example.test';

            $this->actingAs($admin)->post(route('users.store'), [
                'name' => ucfirst(str_replace('_', ' ', $role)),
                'email' => $email,
                'role' => $role,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'is_active' => '1',
            ])->assertRedirect();

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => $role,
                'is_active' => true,
            ]);
        }

        $this->actingAs($admin)->get(route('users.index'))
            ->assertOk()
            ->assertSee('Manage Staff, Shareholder and Help Desk login access')
            ->assertSee('Help Desk');
    }

    public function test_admin_can_edit_help_desk_without_allowing_role_change(): void
    {
        $admin = $this->user('admin');
        $helpDesk = $this->user('help_desk');

        $this->actingAs($admin)->put(route('users.update', $helpDesk), [
            'name' => 'Updated Help Desk',
            'email' => 'updated-helpdesk@example.test',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'is_active' => '0',
            'role' => 'admin',
        ])->assertRedirect(route('users.index'));

        $helpDesk->refresh();
        $this->assertSame('help_desk', $helpDesk->role);
        $this->assertFalse($helpDesk->is_active);
        $this->assertTrue(Hash::check('newpassword123', $helpDesk->password));
    }

    public function test_non_admins_and_guest_cannot_manage_users(): void
    {
        foreach (['help_desk', 'staff', 'shareholder'] as $role) {
            $this->actingAs($this->user($role))
                ->get(route('users.index'))
                ->assertForbidden();
            $this->actingAs($this->user($role))
                ->post(route('users.store'), [])
                ->assertForbidden();
        }

        auth()->logout();
        $this->get(route('users.index'))->assertRedirect(route('login'));
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
