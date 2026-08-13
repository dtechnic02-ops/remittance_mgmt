<?php

namespace Tests\Feature;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\AccountTransfer;
use App\Models\Borrowing;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Lender;
use App\Models\RemittanceTransaction;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Models\User;
use App\Services\ShareTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_authoritative_balances_and_performance_formulas(): void
    {
        $admin = $this->user('admin');
        $fy = $this->financialYear();
        $cash = $this->account($admin, 'CASH', Account::TYPE_CASH, 100000);
        $bank = $this->account($admin, 'BANK', Account::TYPE_BANK, 40000);
        $remittanceAccount = $this->account($admin, 'REM', Account::TYPE_REMITTANCE, -7000);
        $this->account($admin, 'FD', Account::TYPE_FIXED_DEPOSIT, 9000);
        $this->account($admin, 'INACTIVE', Account::TYPE_CASH, 5000, false);
        $shareholder = Shareholder::create([
            'code' => 'SH-001', 'name' => 'Owner', 'kitta' => 0,
            'per_kitta_value' => 1000, 'total_investment' => 0,
            'is_active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $shareService = app(ShareTransactionService::class);
        $buy = $shareService->create($this->sharePayload($admin, $shareholder, $cash, $fy, ShareTransaction::TYPE_BUY, 100));
        $this->assertSame(100000, $this->summary($admin)['share_capital']);
        $shareService->create($this->sharePayload($admin, $shareholder, $cash, $fy, ShareTransaction::TYPE_WITHDRAW, 6));
        $this->assertSame(94000, $this->summary($admin)['share_capital']);

        // Set authoritative current positions for the critical regression example.
        $cash->update(['current_balance' => 54000]);
        $bank->update(['current_balance' => 40000]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Income', 'code' => 'INC', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $expenseCategory = ExpenseCategory::create([
            'name' => 'Expense', 'code' => 'EXP', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        Income::create($this->incomeData($admin, $incomeCategory, $cash, $fy, 2000, 'active'));
        Income::create($this->incomeData($admin, $incomeCategory, $cash, $fy, 9999, 'cancelled'));
        Expense::create($this->expenseData($admin, $expenseCategory, $cash, $fy, 6000, 'active'));
        Expense::create($this->expenseData($admin, $expenseCategory, $cash, $fy, 9999, 'cancelled'));

        $customer = Customer::create([
            'customer_code' => 'CUS-001', 'name' => 'Customer', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        RemittanceTransaction::create([
            'transaction_number' => 'REM-1', 'direction' => 'send',
            'customer_id' => $customer->id, 'provider_account_id' => $remittanceAccount->id,
            'cash_account_id' => $cash->id, 'date_ad' => now(), 'date_bs' => '2083-04-29',
            'financial_year' => $fy, 'principal_amount' => 1000, 'service_charge' => 500,
            'total_cash_received' => 1500, 'status' => 'active', 'created_by' => $admin->id,
        ]);
        RemittanceTransaction::create([
            'transaction_number' => 'REM-2', 'direction' => 'send',
            'customer_id' => $customer->id, 'provider_account_id' => $remittanceAccount->id,
            'cash_account_id' => $cash->id, 'date_ad' => now(), 'date_bs' => '2083-04-29',
            'financial_year' => $fy, 'principal_amount' => 1000, 'service_charge' => 900,
            'total_cash_received' => 1900, 'status' => 'cancelled', 'created_by' => $admin->id,
        ]);

        $lender = Lender::create([
            'code' => 'LND-1', 'name' => 'Lender', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        Borrowing::create($this->borrowingData($admin, $lender, $cash, $fy, 'BOR-1', Borrowing::TYPE_BORROW, 10000, 'active'));
        Borrowing::create($this->borrowingData($admin, $lender, $cash, $fy, 'BOR-2', Borrowing::TYPE_REPAY, 3000, 'active'));
        Borrowing::create($this->borrowingData($admin, $lender, $cash, $fy, 'BOR-3', Borrowing::TYPE_BORROW, 8000, 'cancelled'));
        AccountTransfer::create([
            'transfer_number' => 'TRF-1', 'from_account_id' => $cash->id,
            'to_account_id' => $bank->id, 'date_ad' => now(), 'date_bs' => '2083-04-29',
            'financial_year' => $fy, 'amount' => 5000, 'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $summary = $this->summary($admin);
        $this->assertSame(94000, $summary['share_capital']);
        $this->assertSame(54000, $summary['cash']);
        $this->assertSame(40000, $summary['bank']);
        $this->assertSame(94000, $summary['available']);
        $this->assertSame(-7000, $summary['remittance']);
        $this->assertSame(2000, $summary['income']);
        $this->assertSame(500, $summary['commission']);
        $this->assertSame(6000, $summary['expense']);
        $this->assertSame(-3500, $summary['profit_loss']);
        $this->assertSame(7000, $summary['borrowing_outstanding']);
        $this->assertNotSame($summary['share_capital'] + $summary['available'], $summary['available']);
        $this->assertSame($fy, $this->actingAs($admin)->get(route('admin.dashboard'))->viewData('financialYear'));
        $this->assertSame('active', $buy->status);
    }

    public function test_dashboard_is_denied_to_staff_and_shareholders(): void
    {
        $this->actingAs($this->user('staff'))->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->user('shareholder'))->get(route('admin.dashboard'))->assertForbidden();
        auth()->logout();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    private function summary(User $admin): array
    {
        return $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()->viewData('summary');
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true]);
    }

    private function account(User $user, string $code, string $type, int $balance, bool $active = true): Account
    {
        return Account::create([
            'name' => $code, 'code' => $code, 'type' => $type,
            'opening_balance' => $balance, 'current_balance' => $balance,
            'is_active' => $active, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
    }

    private function financialYear(): string
    {
        [$year, $month] = array_map('intval', explode('-', LaravelNepaliDate::from(now()->toDateString())->toNepaliDate()));
        $start = $month >= 4 ? $year : $year - 1;
        return $start.'/'.substr((string) ($start + 1), -2);
    }

    private function sharePayload(User $user, Shareholder $shareholder, Account $account, string $fy, string $type, int $kitta): array
    {
        return ['transaction_type' => $type, 'shareholder_id' => $shareholder->id, 'account_id' => $account->id,
            'date_ad' => now()->toDateString(), 'date_bs' => '2083-04-29', 'financial_year' => $fy,
            'kitta' => $kitta, 'created_by' => $user->id];
    }

    private function incomeData(User $user, IncomeCategory $category, Account $account, string $fy, int $amount, string $status): array
    {
        return ['income_number' => uniqid('INC-'), 'income_category_id' => $category->id, 'account_id' => $account->id,
            'date_ad' => now(), 'date_bs' => '2083-04-29', 'financial_year' => $fy,
            'amount' => $amount, 'status' => $status, 'created_by' => $user->id];
    }

    private function expenseData(User $user, ExpenseCategory $category, Account $account, string $fy, int $amount, string $status): array
    {
        return ['expense_number' => uniqid('EXP-'), 'expense_category_id' => $category->id, 'account_id' => $account->id,
            'date_ad' => now(), 'date_bs' => '2083-04-29', 'financial_year' => $fy,
            'amount' => $amount, 'status' => $status, 'created_by' => $user->id];
    }

    private function borrowingData(User $user, Lender $lender, Account $account, string $fy, string $number, string $type, int $amount, string $status): array
    {
        return ['transaction_number' => $number, 'transaction_type' => $type, 'lender_id' => $lender->id,
            'account_id' => $account->id, 'date_ad' => now(), 'date_bs' => '2083-04-29',
            'financial_year' => $fy, 'amount' => $amount, 'status' => $status, 'created_by' => $user->id];
    }
}
