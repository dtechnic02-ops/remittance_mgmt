<?php

namespace Tests\Feature;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\Borrowing;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Lender;
use App\Models\RemittanceTransaction;
use App\Models\Shareholder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StaffDashboardFinancialTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_summary_matches_admin_formulas_without_double_counting_funding_or_expense(): void
    {
        [$admin, $staff, $financialYear] = $this->seedDashboardData();

        $adminSummary = $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->viewData('summary');
        $staffResponse = $this->actingAs($staff)->get(route('staff.dashboard'))->assertOk();
        $staffSummary = $staffResponse->viewData('summary');

        $this->assertSame(
    $adminSummary,
    collect($staffSummary)
        ->except('net_amount')
        ->all()
);

$this->assertSame(
    $staffSummary['available'] + $staffSummary['remittance'],
    $staffSummary['net_amount']
);

$this->assertSame(
    81000,
    $staffSummary['net_amount']
);
        $this->assertSame(50000, $staffSummary['share_capital']);
        $this->assertSame(60000, $staffSummary['cash']);
        $this->assertSame(25000, $staffSummary['bank']);
        $this->assertSame(85000, $staffSummary['available']);
        $this->assertSame(-4000, $staffSummary['remittance']);
        $this->assertSame(9600, $staffSummary['income']);
        $this->assertSame(1500, $staffSummary['commission']);
        $this->assertSame(12000, $staffSummary['expense']);
        $this->assertSame(-900, $staffSummary['profit_loss']);
        $this->assertSame(7000, $staffSummary['borrowing_outstanding']);
        $this->assertSame($financialYear, $staffResponse->viewData('financialYear'));
        $this->assertSame($staffSummary['cash'] + $staffSummary['bank'], $staffSummary['available']);
        $this->assertSame(
            $staffSummary['income'] + $staffSummary['commission'] - $staffSummary['expense'],
            $staffSummary['profit_loss']
        );
    }

    public function test_monthly_chart_uses_all_nepali_fy_months_and_reconciles_to_annual_totals(): void
    {
        [, $staff] = $this->seedDashboardData();

        DB::enableQueryLog();
        $response = $this->actingAs($staff)->get(route('staff.dashboard'))->assertOk();
        $queries = collect(DB::getQueryLog())->pluck('query');
        $summary = $response->viewData('summary');
        $monthly = $response->viewData('monthly');

        $this->assertSame(
            ['Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra', 'Baisakh', 'Jestha', 'Asar'],
            array_column($monthly, 'label')
        );
        $this->assertCount(12, $monthly);
        $this->assertSame($summary['income'], array_sum(array_column($monthly, 'income')));
        $this->assertSame($summary['commission'], array_sum(array_column($monthly, 'commission')));
        $this->assertSame($summary['expense'], array_sum(array_column($monthly, 'expense')));
        $this->assertSame($summary['profit_loss'], array_sum(array_column($monthly, 'profit_loss')));

        $this->assertSame(4600, $monthly[0]['income']);
        $this->assertSame(1000, $monthly[0]['commission']);
        $this->assertSame(2000, $monthly[0]['expense']);
        $this->assertSame(3600, $monthly[0]['profit_loss']);
        $this->assertSame(-4500, $monthly[1]['profit_loss']);
        $this->assertSame(0, $monthly[2]['income']);
        $this->assertSame(0, $monthly[2]['commission']);
        $this->assertSame(0, $monthly[2]['expense']);
        $this->assertSame(0, $monthly[2]['profit_loss']);
        $this->assertFalse($queries->contains(
            fn (string $query) => str_contains(strtolower($query), 'cast(substr')
        ));

        $response
            ->assertSee('Monthly Financial Chart')
            ->assertSeeInOrder(array_column($monthly, 'label'))
            ->assertSee('Profit/Loss')
            ->assertSee('-4,500');
    }

    public function test_staff_sees_financial_sections_without_dashboard_operations_or_admin_actions(): void
    {
        $staff = $this->user('staff');

        $this->actingAs($staff)->get(route('staff.dashboard'))
            ->assertOk()
            ->assertSee('Current Position')
            ->assertSee('Total Share Capital')
            ->assertSee('Cash Balance')
            ->assertSee('Bank Balance')
            ->assertSee('Available Cash &amp; Bank', false)
            ->assertSee('Total Income')
            ->assertSee('Total Commission')
            ->assertSee('Total Expense')
            ->assertSee('Net Profit / Loss')
            ->assertSee('Remittance Position')
            ->assertSee('Borrowing Outstanding')
            ->assertSee('Monthly Financial Chart')
            ->assertDontSee('Your Operations')
            ->assertDontSee('No operational permissions have been assigned')
            ->assertDontSee('New Customer')
            ->assertDontSee('New Remittance')
            ->assertDontSee('Staff Permissions')
            ->assertDontSee('Manage Staff');
    }

    public function test_staff_dashboard_route_keeps_required_middleware(): void
    {
        $middleware = app('router')->getRoutes()->getByName('staff.dashboard')->gatherMiddleware();

        $this->assertContains('auth', $middleware);
        $this->assertContains('verified', $middleware);
        $this->assertContains('staff', $middleware);
        $this->assertNotContains('permission:dashboard.view', $middleware);
        $this->assertNotContains('permission:financial-dashboard.view', $middleware);
    }

    private function seedDashboardData(): array
    {
        $admin = $this->user('admin');
        $staff = $this->user('staff');
        [$financialYear, $startYear] = $this->financialYear();
        $cash = $this->account($admin, 'CASH', Account::TYPE_CASH, 60000);
        $bank = $this->account($admin, 'BANK', Account::TYPE_BANK, 25000);
        $remittance = $this->account($admin, 'REM', Account::TYPE_REMITTANCE, -4000);
        $this->account($admin, 'INACTIVE', Account::TYPE_CASH, 99999, false);

        Shareholder::create([
            'code' => 'SH-1', 'name' => 'Owner', 'kitta' => 50, 'per_kitta_value' => 1000,
            'total_investment' => 50000, 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Income', 'code' => 'INC', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $expenseCategory = ExpenseCategory::create([
            'name' => 'Expense', 'code' => 'EXP', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $customer = Customer::create([
            'customer_code' => 'CUS-1', 'name' => 'Customer', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $this->income($admin, $incomeCategory, $cash, $financialYear, "{$startYear}-04-10", 4000, 'active');
        $this->income($admin, $incomeCategory, $cash, $financialYear, "{$startYear}-04-20", 600, 'active');
        $this->income($admin, $incomeCategory, $cash, $financialYear, "{$startYear}-05-10", 5000, 'active');
        $this->income($admin, $incomeCategory, $cash, $financialYear, "{$startYear}-04-11", 8000, 'cancelled');
        $this->expense($admin, $expenseCategory, $cash, $financialYear, "{$startYear}-04-12", 2000, 'active');
        $this->expense($admin, $expenseCategory, $cash, $financialYear, "{$startYear}-05-12", 10000, 'active');
        $this->expense($admin, $expenseCategory, $cash, $financialYear, "{$startYear}-05-13", 9000, 'cancelled');
        $this->remittance($admin, $customer, $remittance, $cash, $financialYear, "{$startYear}-04-15", 1000, 'active');
        $this->remittance($admin, $customer, $remittance, $cash, $financialYear, "{$startYear}-05-15", 500, 'active');
        $this->remittance($admin, $customer, $remittance, $cash, $financialYear, "{$startYear}-05-16", 700, 'cancelled');

        $lender = Lender::create([
            'code' => 'LND-1', 'name' => 'Lender', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $this->borrowing($admin, $lender, $cash, $financialYear, 'BOR-1', Borrowing::TYPE_BORROW, 10000, 'active');
        $this->borrowing($admin, $lender, $cash, $financialYear, 'BOR-2', Borrowing::TYPE_REPAY, 3000, 'active');
        $this->borrowing($admin, $lender, $cash, $financialYear, 'BOR-3', Borrowing::TYPE_BORROW, 9000, 'cancelled');

        return [$admin, $staff, $financialYear];
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true, 'email_verified_at' => now()]);
    }

    private function account(User $user, string $code, string $type, int $balance, bool $active = true): Account
    {
        return Account::create(['name' => $code, 'code' => $code, 'type' => $type,
            'opening_balance' => $balance, 'current_balance' => $balance, 'is_active' => $active,
            'created_by' => $user->id, 'updated_by' => $user->id]);
    }

    private function financialYear(): array
    {
        [$year, $month] = array_map('intval', explode('-', LaravelNepaliDate::from(now()->toDateString())->toNepaliDate()));
        $start = $month >= 4 ? $year : $year - 1;

        return [$start.'/'.substr((string) ($start + 1), -2), $start];
    }

    private function income(User $user, IncomeCategory $category, Account $account, string $fy, string $dateBs, int $amount, string $status): void
    {
        Income::create(['income_number' => uniqid('INC-'), 'income_category_id' => $category->id,
            'account_id' => $account->id, 'date_ad' => now(), 'date_bs' => $dateBs,
            'financial_year' => $fy, 'amount' => $amount, 'status' => $status, 'created_by' => $user->id]);
    }

    private function expense(User $user, ExpenseCategory $category, Account $account, string $fy, string $dateBs, int $amount, string $status): void
    {
        Expense::create(['expense_number' => uniqid('EXP-'), 'expense_category_id' => $category->id,
            'account_id' => $account->id, 'date_ad' => now(), 'date_bs' => $dateBs,
            'financial_year' => $fy, 'amount' => $amount, 'status' => $status, 'created_by' => $user->id]);
    }

    private function remittance(User $user, Customer $customer, Account $provider, Account $cash, string $fy, string $dateBs, int $charge, string $status): void
    {
        RemittanceTransaction::create(['transaction_number' => uniqid('REM-'), 'direction' => 'send',
            'customer_id' => $customer->id, 'provider_account_id' => $provider->id, 'cash_account_id' => $cash->id,
            'date_ad' => now(), 'date_bs' => $dateBs, 'financial_year' => $fy, 'principal_amount' => 100,
            'service_charge' => $charge, 'total_cash_received' => 100 + $charge, 'status' => $status,
            'created_by' => $user->id]);
    }

    private function borrowing(User $user, Lender $lender, Account $account, string $fy, string $number, string $type, int $amount, string $status): void
    {
        Borrowing::create(['transaction_number' => $number, 'transaction_type' => $type,
            'lender_id' => $lender->id, 'account_id' => $account->id, 'date_ad' => now(),
            'date_bs' => '2083-04-01', 'financial_year' => $fy, 'amount' => $amount,
            'status' => $status, 'created_by' => $user->id]);
    }
}
