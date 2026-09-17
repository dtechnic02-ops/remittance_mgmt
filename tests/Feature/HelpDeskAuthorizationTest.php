<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Permission;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpDeskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_desk_can_log_in_and_reaches_own_dashboard(): void
    {
        $user = $this->user('help_desk');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('dashboard'))
            ->assertRedirect(route('help-desk.dashboard'));
        $this->get(route('help-desk.dashboard'))
            ->assertOk()
            ->assertSee('Help Desk Dashboard')
            ->assertSee('Fixed Deposit Balance');
    }

    public function test_help_desk_can_access_all_intended_read_indexes(): void
    {
        $helpDesk = $this->user('help_desk');

        foreach ([
            'customers.index',
            'remittances.index',
            'accounts.index',
            'account-transfers.index',
            'ledger.index',
            'profit-ledger.index',
            'incomes.index',
            'expenses.index',
            'lenders.index',
            'borrowings.index',
            'shareholders.index',
            'share-transactions.index',
        ] as $routeName) {
            $this->actingAs($helpDesk)->get(route($routeName))->assertOk();
        }
    }

    public function test_help_desk_dashboard_excludes_fixed_deposit_from_account_summaries(): void
    {
        Account::query()->create([
            'name' => 'Help Desk Cash',
            'code' => 'HD-CASH',
            'type' => Account::TYPE_CASH,
            'current_balance' => 1200,
            'is_active' => true,
        ]);
        Account::query()->create([
            'name' => 'Help Desk Fixed Deposit',
            'code' => 'HD-FD',
            'type' => Account::TYPE_FIXED_DEPOSIT,
            'current_balance' => 9000,
            'is_active' => true,
        ]);

        $summary = $this->actingAs($this->user('help_desk'))
            ->get(route('help-desk.dashboard'))
            ->assertOk()
            ->viewData('summary');

        $this->assertSame(1200, $summary['cash']);
        $this->assertSame(0, $summary['bank']);
        $this->assertSame(0, $summary['remittance']);
        $this->assertSame(9000, $summary['fixed_deposit']);
    }

    public function test_help_desk_read_show_routes_are_authorized_before_model_binding(): void
    {
        $helpDesk = $this->user('help_desk');

        foreach ([
            'customers.show',
            'remittances.show',
            'accounts.show',
            'account-transfers.show',
            'incomes.show',
            'expenses.show',
            'lenders.show',
            'shareholders.show',
            'share-transactions.show',
        ] as $routeName) {
            $this->actingAs($helpDesk)
                ->get(route($routeName, 999999))
                ->assertNotFound();
        }
    }

    public function test_help_desk_sidebar_contains_only_read_only_business_navigation(): void
    {
        $response = $this->actingAs($this->user('help_desk'))
            ->get(route('help-desk.dashboard'))
            ->assertOk();

        foreach ([
            'Customers', 'Remittances', 'Accounts', 'Account Transfers',
            'Financial Ledger', 'Profit Ledger', 'Income', 'Expenses', 'Lenders',
            'Borrowings', 'Shareholders', 'Share Transactions',
        ] as $label) {
            $response->assertSee($label);
        }

        $response
            ->assertDontSee('Administration')
            ->assertDontSee('Staff Permissions')
            ->assertDontSee('+ New');
    }

    public function test_help_desk_cannot_open_create_or_edit_pages(): void
    {
        $helpDesk = $this->user('help_desk');
        $account = $this->account();
        $shareTransaction = $this->shareTransaction($account);

        foreach ([
            route('customers.create'),
            route('remittances.create'),
            route('accounts.create'),
            route('account-transfers.create'),
            route('incomes.create'),
            route('expenses.create'),
            route('lenders.create'),
            route('borrowings.create'),
            route('shareholders.create'),
            route('share-transactions.create'),
        ] as $url) {
            $this->actingAs($helpDesk)->get($url)->assertForbidden();
        }

        foreach ([
            route('customers.edit', 999999),
            route('remittances.edit', 999999),
            route('accounts.edit', $account),
            route('account-transfers.edit', 999999),
            route('incomes.edit', 999999),
            route('expenses.edit', 999999),
            route('lenders.edit', 999999),
            route('shareholders.edit', 999999),
            route('share-transactions.edit', $shareTransaction),
        ] as $url) {
            $this->actingAs($helpDesk)->get($url)->assertForbidden();
        }
    }

    public function test_help_desk_write_methods_and_cancellations_are_forbidden(): void
    {
        $helpDesk = $this->user('help_desk');
        $shareTransaction = $this->shareTransaction($this->account());

        $this->actingAs($helpDesk)->post(route('customers.store'), [])->assertForbidden();
        $this->actingAs($helpDesk)->put(route('customers.update', 999999), [])->assertForbidden();
        $this->actingAs($helpDesk)->patch(route('customers.update', 999999), [])->assertForbidden();
        $this->actingAs($helpDesk)->delete(route('customers.destroy', 999999))->assertForbidden();

        foreach ([
            route('customers.cancel', 999999),
            route('remittances.cancel', 999999),
            route('account-transfers.cancel', 999999),
            route('incomes.cancel', 999999),
            route('expenses.cancel', 999999),
            route('borrowings.cancel', 999999),
            route('shareholders.cancel', 999999),
            route('share-transactions.cancel', $shareTransaction),
        ] as $url) {
            $this->actingAs($helpDesk)->post($url, [])->assertForbidden();
        }
    }

    public function test_help_desk_cannot_access_administration_or_other_role_dashboards(): void
    {
        $helpDesk = $this->user('help_desk');

        foreach ([
            route('admin.dashboard'),
            route('staff.dashboard'),
            route('shareholder.dashboard'),
            route('users.index'),
            route('staff.index'),
            route('staff-permissions.index'),
            route('company-info.edit'),
            route('company-links.index'),
            route('opening-balances.index'),
        ] as $url) {
            $this->actingAs($helpDesk)->get($url)->assertForbidden();
        }
    }

    public function test_existing_admin_staff_and_shareholder_boundaries_remain_intact(): void
    {
        $admin = $this->user('admin');
        $staff = $this->user('staff');
        $shareholder = $this->user('shareholder');
        $permission = Permission::query()->where('code', 'ledger.view')->firstOrFail();
        $staff->permissions()->attach($permission->id, ['assigned_at' => now()]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($staff)->get(route('staff.dashboard'))->assertOk();
        $this->actingAs($staff)->get(route('ledger.index'))->assertOk();
        $this->actingAs($shareholder)->get(route('customers.index'))->assertForbidden();

        $this->actingAs($admin)->get(route('help-desk.dashboard'))->assertForbidden();
        $this->actingAs($staff)->get(route('help-desk.dashboard'))->assertForbidden();
        $this->actingAs($shareholder)->get(route('help-desk.dashboard'))->assertForbidden();
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function account(): Account
    {
        return Account::query()->create([
            'name' => 'Help Desk Test Cash',
            'code' => 'HD-'.str()->random(10),
            'type' => Account::TYPE_CASH,
            'current_balance' => 0,
            'is_active' => true,
        ]);
    }

    private function shareTransaction(Account $account): ShareTransaction
    {
        $shareholder = Shareholder::query()->create([
            'code' => 'HD-SH-'.str()->random(8),
            'name' => 'Help Desk Test Shareholder',
            'kitta' => 1,
            'per_kitta_value' => 1000,
            'total_investment' => 1000,
            'is_active' => true,
        ]);

        return ShareTransaction::query()->create([
            'transaction_number' => 'HD-TX-'.str()->random(8),
            'transaction_type' => ShareTransaction::TYPE_BUY,
            'shareholder_id' => $shareholder->id,
            'account_id' => $account->id,
            'date_ad' => '2026-08-20',
            'date_bs' => '2083-05-04',
            'financial_year' => '2083/84',
            'kitta' => 1,
            'per_kitta_value' => 1000,
            'total_amount' => 1000,
            'status' => 'active',
        ]);
    }
}
