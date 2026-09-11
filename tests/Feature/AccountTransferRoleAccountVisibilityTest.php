<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTransferRoleAccountVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_and_can_transfer_to_fixed_deposit(): void
    {
        $admin = $this->user('admin', 'admin@example.com');
        $cash = $this->account($admin, 'Admin Cash', 'ADM-CASH', Account::TYPE_CASH, 1000);
        $fixedDeposit = $this->account($admin, 'Admin Fixed Deposit', 'ADM-FD', Account::TYPE_FIXED_DEPOSIT, 0);

        $this->actingAs($admin)->get(route('account-transfers.index'))
            ->assertOk()
            ->assertSee('Admin Fixed Deposit');

        $this->actingAs($admin)->get(route('account-transfers.create'))
            ->assertOk()
            ->assertSee('Admin Fixed Deposit');

        $this->actingAs($admin)->post(route('account-transfers.store'), $this->payload($cash, $fixedDeposit, 400))
            ->assertRedirect();

        $this->actingAs($admin)->post(route('account-transfers.store'), $this->payload($fixedDeposit, $cash, 100))
            ->assertRedirect();

        $this->assertDatabaseHas('account_transfers', [
            'from_account_id' => $cash->id,
            'to_account_id' => $fixedDeposit->id,
            'amount' => 400,
        ]);
        $this->assertSame(700, $cash->fresh()->current_balance);
        $this->assertSame(300, $fixedDeposit->fresh()->current_balance);
    }

    public function test_staff_cannot_see_or_manually_post_fixed_deposit(): void
    {
        $staff = $this->staffWithTransferPermissions();
        $cash = $this->account($staff, 'Staff Cash', 'STF-CASH', Account::TYPE_CASH, 1000);
        $this->account($staff, 'Staff Bank', 'STF-BANK', Account::TYPE_BANK, 1000);
        $fixedDeposit = $this->account($staff, 'Hidden Fixed Deposit', 'STF-FD', Account::TYPE_FIXED_DEPOSIT, 0);

        $this->actingAs($staff)->get(route('account-transfers.index'))
            ->assertOk()
            ->assertDontSee('Hidden Fixed Deposit');

        $this->actingAs($staff)->get(route('account-transfers.create'))
            ->assertOk()
            ->assertSee('Staff Cash')
            ->assertSee('Staff Bank')
            ->assertDontSee('Hidden Fixed Deposit');

        $this->actingAs($staff)->post(route('account-transfers.store'), $this->payload($cash, $fixedDeposit, 100))
            ->assertNotFound();

        $this->assertDatabaseCount('account_transfers', 0);
        $this->assertSame(1000, $cash->fresh()->current_balance);
        $this->assertSame(0, $fixedDeposit->fresh()->current_balance);
    }

    public function test_staff_normal_account_transfer_still_works(): void
    {
        $staff = $this->staffWithTransferPermissions();
        $cash = $this->account($staff, 'Normal Cash', 'NORMAL-CASH', Account::TYPE_CASH, 1000);
        $bank = $this->account($staff, 'Normal Bank', 'NORMAL-BANK', Account::TYPE_BANK, 0);

        $this->actingAs($staff)->post(route('account-transfers.store'), $this->payload($cash, $bank, 250))
            ->assertRedirect();

        $this->assertSame(750, $cash->fresh()->current_balance);
        $this->assertSame(250, $bank->fresh()->current_balance);
    }

    private function staffWithTransferPermissions(): User
    {
        $staff = $this->user('staff', 'staff@example.com');

        $permissions = Permission::query()
            ->whereIn('code', ['account-transfer.view', 'account-transfer.create'])
            ->pluck('id')
            ->mapWithKeys(fn (int $id) => [$id => ['assigned_at' => now()]])
            ->all();

        $staff->permissions()->attach($permissions);

        return $staff;
    }

    private function user(string $role, string $email): User
    {
        return User::factory()->create([
            'role' => $role,
            'email' => $email,
            'is_active' => true,
        ]);
    }

    private function account(
        User $user,
        string $name,
        string $code,
        string $type,
        int $balance
    ): Account {
        return Account::query()->create([
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'opening_balance' => $balance,
            'current_balance' => $balance,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    private function payload(Account $from, Account $to, int $amount): array
    {
        return [
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
            'date_ad' => '2026-09-11',
            'date_bs' => 'tampered',
            'financial_year' => 'tampered',
            'amount' => $amount,
        ];
    }
}
