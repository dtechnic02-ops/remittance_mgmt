<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Lender;
use App\Models\Shareholder;
use App\Models\User;
use App\Services\BorrowingService;
use App\Services\IncomeService;
use App\Services\RemittanceTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_insufficient_expense_and_transfer_return_with_errors_without_changes(): void
    {
        $admin = $this->admin();
        $source = $this->account($admin, 'CASH01', 100);
        $target = $this->account($admin, 'BANK01', 50, Account::TYPE_BANK);
        $category = ExpenseCategory::create([
            'name' => 'Office', 'code' => 'OFF', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $expense = $this->actingAs($admin)->from(route('expenses.create'))->post(route('expenses.store'), [
            'expense_category_id' => $category->id, 'account_id' => $source->id,
            'date_ad' => '2026-08-13', 'date_bs' => '2083-04-29',
            'financial_year' => '2083/84', 'amount' => 101,
        ]);
        $expense->assertRedirect(route('expenses.create'))->assertSessionHasErrors('transaction');
        $this->assertDatabaseCount('expenses', 0);

        $transfer = $this->actingAs($admin)->from(route('account-transfers.create'))->post(route('account-transfers.store'), [
            'from_account_id' => $source->id, 'to_account_id' => $target->id,
            'date_ad' => '2026-08-13', 'date_bs' => '2083-04-29',
            'financial_year' => '2083/84', 'amount' => 101,
        ]);
        $transfer->assertRedirect(route('account-transfers.create'))->assertSessionHasErrors('transaction');
        $this->assertDatabaseCount('account_transfers', 0);
        $this->assertSame(100, $source->fresh()->current_balance);
        $this->assertSame(50, $target->fresh()->current_balance);
    }

    public function test_share_withdrawal_failures_are_user_friendly_and_atomic(): void
    {
        $admin = $this->admin();
        $shareholder = Shareholder::create([
            'code' => 'SH-001', 'name' => 'Owner', 'kitta' => 2,
            'per_kitta_value' => 1000, 'total_investment' => 2000,
            'is_active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $account = $this->account($admin, 'CASH01', 1500);
        $payload = [
            'transaction_type' => 'withdraw', 'shareholder_id' => $shareholder->id,
            'account_id' => $account->id, 'date_ad' => '2026-08-13',
            'date_bs' => '2083-04-29', 'financial_year' => '2083/84',
        ];

        $tooMany = $this->actingAs($admin)->from(route('share-transactions.create'))
            ->post(route('share-transactions.store'), $payload + ['kitta' => 3]);
        $tooMany->assertRedirect(route('share-transactions.create'))->assertSessionHasErrors('transaction');

        $insufficientCash = $this->actingAs($admin)->from(route('share-transactions.create'))
            ->post(route('share-transactions.store'), $payload + ['kitta' => 2]);
        $insufficientCash->assertRedirect(route('share-transactions.create'))->assertSessionHasErrors('transaction');

        $this->assertDatabaseCount('share_transactions', 0);
        $this->assertSame(2, $shareholder->fresh()->kitta);
        $this->assertSame(1500, $account->fresh()->current_balance);
    }

    public function test_borrowing_repayment_failures_are_user_friendly_and_atomic(): void
    {
        $admin = $this->admin();
        $lender = Lender::create([
            'code' => 'LND-001', 'name' => 'Lender', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $account = $this->account($admin, 'CASH01', 0);
        $service = app(BorrowingService::class);
        $service->create($this->borrowingPayload($admin, $lender, $account, 'borrow', 5000));

        $excessive = $this->actingAs($admin)->from(route('borrowings.create'))
            ->post(route('borrowings.store'), $this->borrowingPayload($admin, $lender, $account, 'repay', 5001));
        $excessive->assertRedirect(route('borrowings.create'))->assertSessionHasErrors('transaction');

        $account->update(['current_balance' => 100]);
        $insufficient = $this->actingAs($admin)->from(route('borrowings.create'))
            ->post(route('borrowings.store'), $this->borrowingPayload($admin, $lender, $account, 'repay', 1000));
        $insufficient->assertRedirect(route('borrowings.create'))->assertSessionHasErrors('transaction');

        $this->assertDatabaseCount('borrowings', 1);
        $this->assertSame(5000, $service->outstanding($lender->id));
        $this->assertSame(100, $account->fresh()->current_balance);
    }

    public function test_income_and_remittance_cancellation_failures_return_errors_without_partial_reversal(): void
    {
        $admin = $this->admin();
        $account = $this->account($admin, 'CASH01', 1000);
        $incomeCategory = IncomeCategory::create([
            'name' => 'Interest', 'code' => 'INT', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $income = app(IncomeService::class)->create([
            'income_category_id' => $incomeCategory->id, 'account_id' => $account->id,
            'date_ad' => '2026-08-13', 'date_bs' => '2083-04-29',
            'financial_year' => '2083/84', 'amount' => 500, 'created_by' => $admin->id,
        ]);
        $account->update(['current_balance' => 100]);

        $blocked = $this->actingAs($admin)->from(route('incomes.show', $income))
            ->post(route('incomes.cancel', $income), ['cancellation_reason' => 'Wrong']);
        $blocked->assertRedirect(route('incomes.show', $income))->assertSessionHasErrors('transaction');
        $this->assertSame('active', $income->fresh()->status);
        $this->assertSame(100, $account->fresh()->current_balance);

        $customer = \App\Models\Customer::create([
            'customer_code' => 'CUS-001', 'name' => 'Customer', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $cash = $this->account($admin, 'CASH02', 1000);
        $provider = $this->account($admin, 'REM01', 5000, Account::TYPE_REMITTANCE);
        $remittanceService = app(RemittanceTransactionService::class);
        $remittance = $remittanceService->create([
            'direction' => 'send', 'customer_id' => $customer->id,
            'provider_account_id' => $provider->id, 'cash_account_id' => $cash->id,
            'date_ad' => '2026-08-13', 'date_bs' => '2083-04-29',
            'financial_year' => '2083/84', 'principal_amount' => 500,
            'service_charge' => 0, 'created_by' => $admin->id,
        ]);
        $cash->update(['current_balance' => 0]);
        $remittanceBlocked = $this->actingAs($admin)->from(route('remittances.show', $remittance))
            ->post(route('remittances.cancel', $remittance), ['cancellation_reason' => 'Wrong']);
        $remittanceBlocked->assertRedirect(route('remittances.show', $remittance))->assertSessionHasErrors('transaction');
        $this->assertSame('active', $remittance->fresh()->status);

        $cash->update(['current_balance' => 2000]);
        $remittanceService->cancel($remittance->fresh(), $admin->id, 'First');
        $duplicate = $this->actingAs($admin)->from(route('remittances.show', $remittance))
            ->post(route('remittances.cancel', $remittance), ['cancellation_reason' => 'Again']);
        $duplicate->assertRedirect(route('remittances.show', $remittance))->assertSessionHasErrors('transaction');
        $this->assertSame('cancelled', $remittance->fresh()->status);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function account(User $user, string $code, int $balance, string $type = Account::TYPE_CASH): Account
    {
        return Account::create([
            'name' => $code, 'code' => $code, 'type' => $type,
            'opening_balance' => $balance, 'current_balance' => $balance,
            'is_active' => true, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
    }

    private function borrowingPayload(User $user, Lender $lender, Account $account, string $type, int $amount): array
    {
        return [
            'transaction_type' => $type, 'lender_id' => $lender->id,
            'account_id' => $account->id, 'date_ad' => '2026-08-13',
            'date_bs' => '2083-04-29', 'financial_year' => '2083/84',
            'amount' => $amount, 'created_by' => $user->id,
        ];
    }
}
