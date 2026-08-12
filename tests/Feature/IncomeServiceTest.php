<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\IncomeCategory;
use App\Models\User;
use App\Services\IncomeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class IncomeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_increases_selected_account_balance(): void
    {
        $user = User::factory()->create();

        $category = IncomeCategory::create([
            'name' => 'Interest Income',
            'code' => 'INT',
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

        $service = app(IncomeService::class);

        $income = $service->create([
            'income_category_id' => $category->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-11',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 500,
            'reference' => 'TEST-INCOME',
            'note' => 'Income test',
            'created_by' => $user->id,
        ]);

        $this->assertSame(
            5500,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            'INC-000001',
            $income->income_number
        );

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $cash->id,
            'transaction_type' => 'income',
            'transaction_id' => $income->id,
            'direction' => 'increase',
            'amount' => 500,
            'component' => 'income',
        ]);
    }

    public function test_income_cancellation_restores_balance(): void
    {
        $user = User::factory()->create();

        $category = IncomeCategory::create([
            'name' => 'Commission Income',
            'code' => 'COM',
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

        $service = app(IncomeService::class);

        $income = $service->create([
            'income_category_id' => $category->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-11',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 500,
            'created_by' => $user->id,
        ]);

        $service->cancel(
            $income,
            $user->id,
            'Wrong income entry'
        );

        $this->assertSame(
            5000,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            'cancelled',
            $income->fresh()->status
        );

        $this->assertDatabaseCount(
            'ledger_entries',
            2
        );
    }

    public function test_fixed_deposit_cannot_be_used_for_income(): void
    {
        $this->expectException(RuntimeException::class);

        $user = User::factory()->create();

        $category = IncomeCategory::create([
            'name' => 'Other Income',
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

        $service = app(IncomeService::class);

        $service->create([
            'income_category_id' => $category->id,
            'account_id' => $fixedDeposit->id,
            'date_ad' => '2026-08-11',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 500,
            'created_by' => $user->id,
        ]);
    }
}