<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CompanyInfo;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\LedgerEntry;
use App\Models\Permission;
use App\Models\RemittanceTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profit_formula_includes_active_income_charge_and_expense(): void
    {
        $admin = $this->admin();
        $this->seedTransactions($admin);

        $summary = $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
            ]))
            ->assertOk()
            ->assertSee('Profit Ledger')
            ->assertSee('Period: 2026-08-01 to 2026-09-17')
            ->assertDontSee('income_number')
            ->viewData();

        $this->assertSame(30072, $summary['income']);
        $this->assertSame(20, $summary['remittanceCharge']);
        $this->assertSame(26000, $summary['expense']);
        $this->assertSame(4092, $summary['profit']);
        $this->assertSame(30072 + 20 - 26000, $summary['profit']);
    }

    public function test_cancelled_and_out_of_range_transactions_are_excluded(): void
    {
        $admin = $this->admin();
        $this->seedTransactions($admin);

        $summary = $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
            ]))
            ->assertOk()
            ->viewData();

        $this->assertSame(30072, $summary['income']);
        $this->assertSame(20, $summary['remittanceCharge']);
        $this->assertSame(26000, $summary['expense']);
        $this->assertSame(4092, $summary['profit']);
    }

    public function test_start_and_end_dates_filter_all_three_sources(): void
    {
        $admin = $this->admin();
        $this->seedTransactions($admin);

        $fromStart = $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-09-01',
                'date_to' => '2026-09-17',
            ]))
            ->assertOk()
            ->viewData();

        $this->assertSame(0, $fromStart['income']);
        $this->assertSame(20, $fromStart['remittanceCharge']);
        $this->assertSame(26000, $fromStart['expense']);
        $this->assertSame(20 - 26000, $fromStart['profit']);

        $untilAugust = $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-08-31',
            ]))
            ->assertOk()
            ->viewData();

        $this->assertSame(30072, $untilAugust['income']);
        $this->assertSame(0, $untilAugust['remittanceCharge']);
        $this->assertSame(0, $untilAugust['expense']);
        $this->assertSame(30072, $untilAugust['profit']);
    }

    public function test_viewing_the_report_does_not_write_financial_data(): void
    {
        $admin = $this->admin();
        $accounts = $this->seedTransactions($admin);
        $incomeCount = Income::query()->count();
        $expenseCount = Expense::query()->count();
        $remittanceCount = RemittanceTransaction::query()->count();
        $ledgerCount = LedgerEntry::query()->count();
        $balances = Account::query()->orderBy('id')->pluck('current_balance', 'id')->all();

        $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
            ]))
            ->assertOk();

        $this->assertSame($incomeCount, Income::query()->count());
        $this->assertSame($expenseCount, Expense::query()->count());
        $this->assertSame($remittanceCount, RemittanceTransaction::query()->count());
        $this->assertSame($ledgerCount, LedgerEntry::query()->count());
        $this->assertSame(
            $balances,
            Account::query()->orderBy('id')->pluck('current_balance', 'id')->all()
        );
        $this->assertSame(5000, $accounts['cash']->fresh()->current_balance);
        $this->assertSame(1, Income::query()->where('amount', 30072)->count());
        $this->assertSame(0, Income::query()->where('amount', 20)->count());
    }

    public function test_negative_profit_is_shown_and_permissions_follow_ledger_view(): void
    {
        $admin = $this->admin();
        $this->seedTransactions($admin, includeInRangeExpense: 40000);

        $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
            ]))
            ->assertOk()
            ->assertSee('-Rs. 9,908');

        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $this->actingAs($staff)->get(route('profit-ledger.index'))->assertForbidden();

        $permission = Permission::query()->where('code', 'ledger.view')->firstOrFail();
        $staff->permissions()->attach($permission->id, ['assigned_at' => now()]);
        $this->actingAs($staff)->get(route('profit-ledger.index'))->assertOk();
    }

    public function test_print_a4_reuses_the_same_filtered_profit_ledger_figures(): void
    {
        $admin = $this->admin();
        $this->seedTransactions($admin);
        CompanyInfo::create(['company_name' => 'Asha Enterprises']);
        $filters = [
            'date_from' => '2026-08-01',
            'date_to' => '2026-09-17',
        ];

        $screen = $this->actingAs($admin)
            ->get(route('profit-ledger.index', $filters))
            ->assertOk()
            ->assertSee('Print A4')
            ->viewData();

        $printResponse = $this->actingAs($admin)
            ->get(route('profit-ledger.index', $filters + ['output' => 'print']))
            ->assertOk()
            ->assertViewIs('profit-ledger.print')
            ->assertSee('Asha Enterprises')
            ->assertSee('PROFIT LEDGER')
            ->assertSee('Start Date: 2026-08-01')
            ->assertSee('End Date: 2026-09-17')
            ->assertSee('TOTAL PROFIT')
            ->assertSee('Formula: Income + Remittance Charge - Expenses')
            ->assertDontSee('income_number');

        $print = $printResponse->viewData();

        $this->assertSame($screen['income'], $print['income']);
        $this->assertSame($screen['remittanceCharge'], $print['remittanceCharge']);
        $this->assertSame($screen['expense'], $print['expense']);
        $this->assertSame($screen['profit'], $print['profit']);
        $this->assertSame($filters['date_from'], $print['dateFrom']);
        $this->assertSame($filters['date_to'], $print['dateTo']);
        $this->assertSame(30072, $print['income']);
        $this->assertSame(20, $print['remittanceCharge']);
        $this->assertSame(26000, $print['expense']);
        $this->assertSame(30072 + 20 - 26000, $print['profit']);
    }

    public function test_print_excludes_cancelled_and_out_of_range_and_writes_nothing(): void
    {
        $admin = $this->admin();
        $this->seedTransactions($admin);
        $incomeCount = Income::query()->count();
        $expenseCount = Expense::query()->count();
        $remittanceCount = RemittanceTransaction::query()->count();
        $ledgerCount = LedgerEntry::query()->count();
        $balances = Account::query()->orderBy('id')->pluck('current_balance', 'id')->all();

        $print = $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
                'output' => 'print',
            ]))
            ->assertOk()
            ->viewData();

        $this->assertSame(30072, $print['income']);
        $this->assertSame(20, $print['remittanceCharge']);
        $this->assertSame(26000, $print['expense']);
        $this->assertSame(4092, $print['profit']);
        $this->assertSame($incomeCount, Income::query()->count());
        $this->assertSame($expenseCount, Expense::query()->count());
        $this->assertSame($remittanceCount, RemittanceTransaction::query()->count());
        $this->assertSame($ledgerCount, LedgerEntry::query()->count());
        $this->assertSame(
            $balances,
            Account::query()->orderBy('id')->pluck('current_balance', 'id')->all()
        );
        $this->assertSame(0, Income::query()->where('amount', 20)->count());
    }

    public function test_print_requires_ledger_view_and_shows_loss_label(): void
    {
        $admin = $this->admin();
        $this->seedTransactions($admin, includeInRangeExpense: 40000);

        $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
                'output' => 'print',
            ]))
            ->assertOk()
            ->assertSee('TOTAL LOSS')
            ->assertSee('-9,908');

        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $this->actingAs($staff)
            ->get(route('profit-ledger.index', ['output' => 'print']))
            ->assertForbidden();

        $permission = Permission::query()->where('code', 'ledger.view')->firstOrFail();
        $staff->permissions()->attach($permission->id, ['assigned_at' => now()]);
        $this->actingAs($staff)
            ->get(route('profit-ledger.index', ['output' => 'print']))
            ->assertOk()
            ->assertViewIs('profit-ledger.print');
    }

    public function test_financial_year_and_date_filters_apply_together_and_alone(): void
    {
        $admin = $this->admin();
        $seed = $this->seedTransactions($admin);
        $this->income($admin, $seed['incomeCategory'], $seed['cash'], '2026-08-10', 7777, 'active', '2082/83');
        $this->expense($admin, $seed['expenseCategory'], $seed['cash'], '2026-09-10', 333, 'active', '2082/83');
        $this->remittance($admin, $seed['customer'], $seed['provider'], $seed['cash'], '2026-09-05', 15, 'active', '2082/83');
        $this->income($admin, $seed['incomeCategory'], $seed['cash'], '2026-08-10', 111, 'cancelled', '2082/83');

        $fyOnly = $this->actingAs($admin)
            ->get(route('profit-ledger.index', ['financial_year' => '2082/83']))
            ->assertOk()
            ->assertSee('FY 2082/83')
            ->viewData();
        $this->assertSame(7777, $fyOnly['income']);
        $this->assertSame(15, $fyOnly['remittanceCharge']);
        $this->assertSame(333, $fyOnly['expense']);
        $this->assertSame(7777 + 15 - 333, $fyOnly['profit']);
        $this->assertSame('2082/83', $fyOnly['financialYear']);

        $fyCurrent = $this->actingAs($admin)
            ->get(route('profit-ledger.index', ['financial_year' => '2083/84']))
            ->assertOk()
            ->viewData();
        $this->assertSame(35072, $fyCurrent['income']);
        $this->assertSame(60, $fyCurrent['remittanceCharge']);
        $this->assertSame(27000, $fyCurrent['expense']);
        $this->assertSame(35072 + 60 - 27000, $fyCurrent['profit']);

        $datesOnly = $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
            ]))
            ->assertOk()
            ->viewData();
        $this->assertSame('all', $datesOnly['financialYear']);
        $this->assertSame(30072 + 7777, $datesOnly['income']);
        $this->assertSame(20 + 15, $datesOnly['remittanceCharge']);
        $this->assertSame(26000 + 333, $datesOnly['expense']);

        $fyAndDates = $this->actingAs($admin)
            ->get(route('profit-ledger.index', [
                'financial_year' => '2083/84',
                'date_from' => '2026-08-01',
                'date_to' => '2026-09-17',
            ]))
            ->assertOk()
            ->viewData();
        $this->assertSame(30072, $fyAndDates['income']);
        $this->assertSame(20, $fyAndDates['remittanceCharge']);
        $this->assertSame(26000, $fyAndDates['expense']);
        $this->assertSame(30072 + 20 - 26000, $fyAndDates['profit']);

        $reset = $this->actingAs($admin)
            ->get(route('profit-ledger.index'))
            ->assertOk()
            ->viewData();
        $this->assertSame('all', $reset['financialYear']);
        $this->assertSame('', $reset['dateFrom']);
        $this->assertSame('', $reset['dateTo']);
        $this->assertSame(35072 + 7777, $reset['income']);
        $this->assertSame(60 + 15, $reset['remittanceCharge']);
        $this->assertSame(27000 + 333, $reset['expense']);
        $this->assertSame((35072 + 7777) + (60 + 15) - (27000 + 333), $reset['profit']);
    }

    public function test_print_preserves_financial_year_and_dates_and_writes_nothing(): void
    {
        $admin = $this->admin();
        $seed = $this->seedTransactions($admin);
        $this->income($admin, $seed['incomeCategory'], $seed['cash'], '2026-08-10', 7777, 'active', '2082/83');
        CompanyInfo::create(['company_name' => 'Asha Enterprises']);
        $filters = [
            'financial_year' => '2083/84',
            'date_from' => '2026-08-01',
            'date_to' => '2026-09-17',
        ];
        $incomeCount = Income::query()->count();
        $ledgerCount = LedgerEntry::query()->count();
        $balances = Account::query()->orderBy('id')->pluck('current_balance', 'id')->all();

        $screen = $this->actingAs($admin)
            ->get(route('profit-ledger.index', $filters))
            ->assertOk()
            ->viewData();

        $print = $this->actingAs($admin)
            ->get(route('profit-ledger.index', $filters + ['output' => 'print']))
            ->assertOk()
            ->assertViewIs('profit-ledger.print')
            ->assertSee('Financial Year: 2083/84')
            ->assertSee('Start Date: 2026-08-01')
            ->assertSee('End Date: 2026-09-17')
            ->viewData();

        $this->assertSame($screen['income'], $print['income']);
        $this->assertSame($screen['remittanceCharge'], $print['remittanceCharge']);
        $this->assertSame($screen['expense'], $print['expense']);
        $this->assertSame($screen['profit'], $print['profit']);
        $this->assertSame('2083/84', $print['financialYear']);
        $this->assertSame('2026-08-01', $print['dateFrom']);
        $this->assertSame('2026-09-17', $print['dateTo']);
        $this->assertSame(30072, $print['income']);
        $this->assertSame(20, $print['remittanceCharge']);
        $this->assertSame(26000, $print['expense']);
        $this->assertSame(4092, $print['profit']);
        $this->assertSame($incomeCount, Income::query()->count());
        $this->assertSame($ledgerCount, LedgerEntry::query()->count());
        $this->assertSame($balances, Account::query()->orderBy('id')->pluck('current_balance', 'id')->all());
        $this->assertSame(0, Income::query()->where('amount', 20)->count());
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * @return array{cash: Account}
     */
    private function seedTransactions(User $user, int $includeInRangeExpense = 26000): array
    {
        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH-PL',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 5000,
            'current_balance' => 5000,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $provider = Account::create([
            'name' => 'Provider',
            'code' => 'PROV-PL',
            'type' => Account::TYPE_REMITTANCE,
            'opening_balance' => 10000,
            'current_balance' => 10000,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $customer = Customer::create([
            'customer_code' => 'CUS-PL',
            'name' => 'Profit Customer',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $incomeCategory = IncomeCategory::create([
            'name' => 'Fee',
            'code' => 'FEE',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $expenseCategory = ExpenseCategory::create([
            'name' => 'Office',
            'code' => 'OFF',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->income($user, $incomeCategory, $cash, '2026-08-10', 30072, 'active');
        $this->income($user, $incomeCategory, $cash, '2026-08-10', 9999, 'cancelled');
        $this->income($user, $incomeCategory, $cash, '2026-07-01', 5000, 'active');

        $this->expense($user, $expenseCategory, $cash, '2026-09-10', $includeInRangeExpense, 'active');
        $this->expense($user, $expenseCategory, $cash, '2026-09-10', 8000, 'cancelled');
        $this->expense($user, $expenseCategory, $cash, '2026-10-01', 1000, 'active');

        $this->remittance($user, $customer, $provider, $cash, '2026-09-05', 20, 'active');
        $this->remittance($user, $customer, $provider, $cash, '2026-09-05', 75, 'cancelled');
        $this->remittance($user, $customer, $provider, $cash, '2026-07-15', 40, 'active');

        return [
            'cash' => $cash,
            'provider' => $provider,
            'customer' => $customer,
            'incomeCategory' => $incomeCategory,
            'expenseCategory' => $expenseCategory,
        ];
    }

    private function income(
        User $user,
        IncomeCategory $category,
        Account $account,
        string $dateAd,
        int $amount,
        string $status,
        string $fy = '2083/84'
    ): void {
        Income::create([
            'income_number' => uniqid('INC-'),
            'income_category_id' => $category->id,
            'account_id' => $account->id,
            'date_ad' => $dateAd,
            'date_bs' => '2083-04-01',
            'financial_year' => $fy,
            'amount' => $amount,
            'status' => $status,
            'created_by' => $user->id,
        ]);
    }

    private function expense(
        User $user,
        ExpenseCategory $category,
        Account $account,
        string $dateAd,
        int $amount,
        string $status,
        string $fy = '2083/84'
    ): void {
        Expense::create([
            'expense_number' => uniqid('EXP-'),
            'expense_category_id' => $category->id,
            'account_id' => $account->id,
            'date_ad' => $dateAd,
            'date_bs' => '2083-04-01',
            'financial_year' => $fy,
            'amount' => $amount,
            'status' => $status,
            'created_by' => $user->id,
        ]);
    }

    private function remittance(
        User $user,
        Customer $customer,
        Account $provider,
        Account $cash,
        string $dateAd,
        int $charge,
        string $status,
        string $fy = '2083/84'
    ): void {
        RemittanceTransaction::create([
            'transaction_number' => uniqid('REM-'),
            'direction' => 'send',
            'customer_id' => $customer->id,
            'provider_account_id' => $provider->id,
            'cash_account_id' => $cash->id,
            'date_ad' => $dateAd,
            'date_bs' => '2083-04-01',
            'financial_year' => $fy,
            'principal_amount' => 100,
            'service_charge' => $charge,
            'total_cash_received' => 100 + $charge,
            'status' => $status,
            'created_by' => $user->id,
        ]);
    }
}
