<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ExpenseServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_decreases_account_balance_and_creates_ledger_entry(): void
    {
        $user = User::factory()->create();

        $category = ExpenseCategory::create([
            'name' => 'Office Expense',
            'code' => 'OFFICE',
            'description' => 'Office related expense',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 5000,
            'current_balance' => 5000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(ExpenseService::class);

        $expense = $service->create([
            'expense_category_id' => $category->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-11',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 1000,
            'reference' => 'EXP-TEST',
            'note' => 'Expense test',
            'created_by' => $user->id,
        ]);

        $this->assertSame(
            4000,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            'EXP-000001',
            $expense->expense_number
        );

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $cash->id,
            'transaction_type' => 'expense',
            'transaction_id' => $expense->id,
            'direction' => 'decrease',
            'amount' => 1000,
            'component' => 'expense',
        ]);
    }

    public function test_expense_cancellation_restores_account_balance(): void
    {
        $user = User::factory()->create();

        $category = ExpenseCategory::create([
            'name' => 'Fuel Expense',
            'code' => 'FUEL',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 5000,
            'current_balance' => 5000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(ExpenseService::class);

        $expense = $service->create([
            'expense_category_id' => $category->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-11',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 1000,
            'created_by' => $user->id,
        ]);

        $service->cancel(
            $expense,
            $user->id,
            'Wrong expense entry'
        );

        $this->assertSame(
            5000,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            'cancelled',
            $expense->fresh()->status
        );

        $this->assertDatabaseCount(
            'ledger_entries',
            2
        );
    }

    public function test_fixed_deposit_cannot_be_used_for_expense(): void
    {
        $this->expectException(RuntimeException::class);

        $user = User::factory()->create();

        $category = ExpenseCategory::create([
            'name' => 'Other Expense',
            'code' => 'OTHER',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $fixedDeposit = Account::create([
            'name' => 'Fixed Deposit',
            'code' => 'FD01',
            'type' => Account::TYPE_FIXED_DEPOSIT,
            'opening_balance' => 10000,
            'current_balance' => 10000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(ExpenseService::class);

        $service->create([
            'expense_category_id' => $category->id,
            'account_id' => $fixedDeposit->id,
            'date_ad' => '2026-08-11',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 1000,
            'created_by' => $user->id,
        ]);
    }
}