<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountViewPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_and_can_access_accounts_without_assignment(): void
    {
        $admin = $this->user('admin', 'admin@example.com');

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Accounts');

        $this->actingAs($admin)->get(route('accounts.index'))->assertOk();
    }

    public function test_account_view_appears_in_admin_staff_permissions_screen(): void
    {
        $admin = $this->user('admin', 'admin@example.com');
        $staff = $this->user('staff', 'staff@example.com');

        $this->actingAs($admin)->get(route('staff-permissions.edit', $staff))
            ->assertOk()
            ->assertSee('Account View')
            ->assertSee('account.view');
    }

    public function test_staff_with_account_view_sees_menu_and_can_open_index(): void
    {
        $staff = $this->user('staff', 'allowed@example.com');
        $this->grant($staff, 'account.view');

        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('Accounts')
            ->assertDontSee('+ New Account');

        $this->actingAs($staff)->get(route('accounts.index'))
            ->assertOk()
            ->assertDontSee('+ New Account');
    }

    public function test_staff_without_account_view_has_no_menu_and_gets_forbidden(): void
    {
        $staff = $this->user('staff', 'denied@example.com');

        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertOk()
            ->assertDontSee('href="'.route('accounts.index').'"', false);

        $this->actingAs($staff)->get(route('accounts.index'))->assertForbidden();
    }

    public function test_accounts_index_shows_total_of_displayed_current_balances(): void
    {
        $admin = $this->user('admin', 'admin-total@example.com');
        $this->account($admin, 'ALPHA', 1000000);
        $this->account($admin, 'BETA', 572994);
        $this->account($admin, 'GAMMA', 0);

        $this->actingAs($admin)
            ->get(route('accounts.index'))
            ->assertOk()
            ->assertSee('TOTAL')
            ->assertSee('1,572,994');
    }

    private function account(User $user, string $code, int $balance): void
    {
        Account::create([
            'name' => $code,
            'code' => $code,
            'type' => Account::TYPE_CASH,
            'opening_balance' => $balance,
            'current_balance' => $balance,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    public function test_account_transfer_permission_remains_independent(): void
    {
        $staff = $this->user('staff', 'transfer@example.com');
        $this->grant($staff, 'account-transfer.view');

        $this->actingAs($staff)->get(route('account-transfers.index'))->assertOk();
        $this->actingAs($staff)->get(route('accounts.index'))->assertForbidden();

        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('Account Transfers')
            ->assertDontSee('href="'.route('accounts.index').'"', false);
    }

    private function user(string $role, string $email): User
    {
        return User::factory()->create([
            'role' => $role,
            'email' => $email,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function grant(User $user, string $code): void
    {
        $permission = Permission::query()->where('code', $code)->firstOrFail();

        $user->permissions()->attach($permission->id, [
            'assigned_at' => now(),
        ]);
    }
}
