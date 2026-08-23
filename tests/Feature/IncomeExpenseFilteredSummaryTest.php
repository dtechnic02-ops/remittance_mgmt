<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeExpenseFilteredSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_and_expense_labels_summarize_the_full_filtered_result_set(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $account = Account::create([
            'name' => 'Cash', 'code' => 'CASH', 'type' => Account::TYPE_CASH,
            'opening_balance' => 0, 'current_balance' => 0, 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $incomeCategory = IncomeCategory::create([
            'name' => 'Selected Income', 'code' => 'SI', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $expenseCategory = ExpenseCategory::create([
            'name' => 'Selected Expense', 'code' => 'SE', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        foreach ([1000, 2500] as $index => $amount) {
            Income::create([
                'income_number' => 'INC-'.$index, 'income_category_id' => $incomeCategory->id,
                'account_id' => $account->id, 'date_ad' => now(), 'date_bs' => '2083-04-01',
                'financial_year' => '2083/84', 'amount' => $amount, 'status' => 'active',
                'created_by' => $admin->id,
            ]);
            Expense::create([
                'expense_number' => 'EXP-'.$index, 'expense_category_id' => $expenseCategory->id,
                'account_id' => $account->id, 'date_ad' => now(), 'date_bs' => '2083-04-01',
                'financial_year' => '2083/84', 'amount' => $amount + 500, 'status' => 'active',
                'created_by' => $admin->id,
            ]);
        }

        $this->actingAs($admin)->get(route('incomes.index', [
            'financial_year' => 'all', 'status' => 'active',
            'income_category_id' => $incomeCategory->id,
        ]))->assertOk()
            ->assertSee('Total Records:')
            ->assertSee('Total Amount:')
            ->assertSee('3,500');

        $this->actingAs($admin)->get(route('expenses.index', [
            'financial_year' => 'all', 'status' => 'active',
            'expense_category_id' => $expenseCategory->id,
        ]))->assertOk()
            ->assertSee('Total Records:')
            ->assertSee('Total Amount:')
            ->assertSee('4,500');
    }
}
