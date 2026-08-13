<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Permission;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareholderIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_shareholder_sees_only_own_profile_and_history(): void
    {
        $user = $this->user('shareholder', 'owner@example.test');
        $otherUser = $this->user('shareholder', 'other@example.test');
        $own = $this->shareholder($user, 'OWN', 7, 7000);
        $other = $this->shareholder($otherUser, 'OTHER', 99, 99000);
        $account = $this->account();

        $this->transaction($own, $account, 'SHR-OWN-BUY', 'buy');
        $this->transaction($own, $account, 'SHR-OWN-WITHDRAW', 'withdraw');
        $this->transaction($other, $account, 'SHR-OTHER', 'buy');

        $response = $this->actingAs($user)->get(route('shareholder.dashboard'));

        $response->assertOk()
            ->assertSee('OWN')
            ->assertSee('7')
            ->assertSee('7,000')
            ->assertSee('SHR-OWN-BUY')
            ->assertSee('SHR-OWN-WITHDRAW')
            ->assertDontSee('OTHER')
            ->assertDontSee('SHR-OTHER')
            ->assertDontSee('99,000');
    }

    public function test_shareholder_cannot_access_other_or_operational_urls(): void
    {
        $user = $this->user('shareholder', 'isolated@example.test');
        $other = $this->shareholder(null, 'TARGET', 5, 5000);

        $this->actingAs($user)
            ->get(route('shareholders.show', $other))
            ->assertForbidden();
        $this->actingAs($user)
            ->get(route('shareholders.index'))
            ->assertForbidden();
        $this->actingAs($user)
            ->get(route('share-transactions.create'))
            ->assertForbidden();
        $this->actingAs($user)
            ->post(route('share-transactions.store'), [])
            ->assertForbidden();
        $this->actingAs($user)
            ->post('/share-transactions/1/cancel', ['cancellation_reason' => 'No'])
            ->assertForbidden();
    }

    public function test_unlinked_shareholder_receives_safe_message(): void
    {
        $user = $this->user('shareholder', 'unlinked@example.test');
        $this->shareholder(null, 'UNRELATED', 12, 12000);

        $this->actingAs($user)
            ->get(route('shareholder.dashboard'))
            ->assertOk()
            ->assertSee('Your shareholder profile is not linked yet')
            ->assertDontSee('UNRELATED');
    }

    public function test_admin_can_link_only_unique_shareholder_role_users(): void
    {
        $admin = $this->user('admin', 'admin-link@example.test');
        $login = $this->user('shareholder', 'login@example.test');
        $staff = $this->user('staff', 'staff-link@example.test');

        $response = $this->actingAs($admin)->post(route('shareholders.store'),
            $this->shareholderPayload('LINK1', $login->id));
        $response->assertRedirect();
        $this->assertDatabaseHas('shareholders', ['code' => 'LINK1', 'user_id' => $login->id]);

        $this->actingAs($admin)->post(route('shareholders.store'),
            $this->shareholderPayload('LINK2', $login->id))
            ->assertSessionHasErrors('user_id');
        $this->actingAs($admin)->post(route('shareholders.store'),
            $this->shareholderPayload('LINK3', $staff->id))
            ->assertSessionHasErrors('user_id');
    }

    public function test_authorized_staff_remains_able_to_view_shareholder_management(): void
    {
        $staff = $this->user('staff', 'authorized@example.test');
        $permission = Permission::where('code', 'shareholder.view')->firstOrFail();
        $staff->permissions()->attach($permission->id, ['assigned_at' => now()]);

        $this->actingAs($staff)->get(route('shareholders.index'))->assertOk();
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

    private function shareholder(?User $user, string $code, int $kitta, int $investment): Shareholder
    {
        return Shareholder::create([
            'user_id' => $user?->id,
            'code' => $code,
            'name' => $code.' Name',
            'kitta' => $kitta,
            'per_kitta_value' => 1000,
            'total_investment' => $investment,
            'is_active' => true,
        ]);
    }

    private function account(): Account
    {
        return Account::create([
            'name' => 'Portal Test Cash', 'code' => 'PORTAL-CASH',
            'type' => Account::TYPE_CASH, 'is_active' => true,
        ]);
    }

    private function transaction(Shareholder $shareholder, Account $account, string $number, string $type): void
    {
        ShareTransaction::create([
            'transaction_number' => $number, 'transaction_type' => $type,
            'shareholder_id' => $shareholder->id, 'account_id' => $account->id,
            'date_ad' => '2026-08-13', 'date_bs' => '2083-04-28',
            'financial_year' => '2083/84', 'kitta' => 1,
            'per_kitta_value' => 1000, 'total_amount' => 1000,
            'status' => 'active',
        ]);
    }

    private function shareholderPayload(string $code, int $userId): array
    {
        return [
            'user_id' => $userId, 'code' => $code, 'name' => $code.' Name',
            'type' => 'permanent', 'is_active' => '1',
        ];
    }
}
