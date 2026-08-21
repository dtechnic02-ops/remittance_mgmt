<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Models\User;
use App\Services\ShareTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ShareTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_buy_kitta_increases_shareholder_and_account(): void
    {
        $user = User::factory()->create();

        $shareholder = Shareholder::create([
            'code' => 'SH-001',
            'name' => 'Test Shareholder',
            'kitta' => 0,
            'per_kitta_value' => 1000,
            'total_investment' => 0,
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

        $service = app(ShareTransactionService::class);

        $transaction = $service->create([
            'transaction_type' => ShareTransaction::TYPE_BUY,
            'shareholder_id' => $shareholder->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'kitta' => 2,
            'per_kitta_value' => 1250,
            'reference' => 'BUY-TEST',
            'created_by' => $user->id,
        ]);

        $shareholder->refresh();
        $cash->refresh();

        $this->assertSame(
            2,
            $shareholder->kitta
        );

        $this->assertSame(
            2500,
            $shareholder->total_investment
        );

        $this->assertSame(
            7500,
            $cash->current_balance
        );

        $this->assertSame(
            2500,
            $transaction->total_amount
        );

        $this->assertSame(1250, $transaction->per_kitta_value);

        $this->assertSame(
            'SHR-000001',
            $transaction->transaction_number
        );

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $cash->id,
            'transaction_type' => 'share_transaction',
            'transaction_id' => $transaction->id,
            'direction' => 'increase',
            'amount' => 2500,
            'component' => 'share_buy',
        ]);
    }

    public function test_withdraw_kitta_decreases_shareholder_and_account(): void
    {
        $user = User::factory()->create();

        $shareholder = Shareholder::create([
            'code' => 'SH-001',
            'name' => 'Test Shareholder',
            'kitta' => 5,
            'per_kitta_value' => 1000,
            'total_investment' => 5000,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $bank = Account::create([
            'name' => 'Main Bank',
            'code' => 'BANK01',
            'type' => Account::TYPE_BANK,
            'opening_balance' => 10000,
            'current_balance' => 10000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(ShareTransactionService::class);

        $transaction = $service->create([
            'transaction_type' => ShareTransaction::TYPE_WITHDRAW,
            'shareholder_id' => $shareholder->id,
            'account_id' => $bank->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'kitta' => 2,
            'per_kitta_value' => 750,
            'reference' => 'WITHDRAW-TEST',
            'created_by' => $user->id,
        ]);

        $shareholder->refresh();
        $bank->refresh();

        $this->assertSame(
            3,
            $shareholder->kitta
        );

        $this->assertSame(
            3500,
            $shareholder->total_investment
        );

        $this->assertSame(
            8500,
            $bank->current_balance
        );

        $this->assertSame(
            1500,
            $transaction->total_amount
        );

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $bank->id,
            'transaction_type' => 'share_transaction',
            'transaction_id' => $transaction->id,
            'direction' => 'decrease',
            'amount' => 1500,
            'component' => 'share_withdraw',
        ]);
    }

    public function test_withdraw_cancellation_restores_all_effects(): void
    {
        $user = User::factory()->create();

        $shareholder = Shareholder::create([
            'code' => 'SH-001',
            'name' => 'Test Shareholder',
            'kitta' => 5,
            'per_kitta_value' => 1000,
            'total_investment' => 5000,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 10000,
            'current_balance' => 10000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(ShareTransactionService::class);

        $transaction = $service->create([
            'transaction_type' => ShareTransaction::TYPE_WITHDRAW,
            'shareholder_id' => $shareholder->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'kitta' => 2,
            'per_kitta_value' => 750,
            'created_by' => $user->id,
        ]);

        $service->cancel(
            $transaction,
            $user->id,
            'Wrong share withdrawal'
        );

        $shareholder->refresh();
        $cash->refresh();
        $transaction->refresh();

        $this->assertSame(
            5,
            $shareholder->kitta
        );

        $this->assertSame(
            5000,
            $shareholder->total_investment
        );

        $this->assertSame(
            10000,
            $cash->current_balance
        );

        $this->assertSame(
            'cancelled',
            $transaction->status
        );

        $this->assertDatabaseCount(
            'ledger_entries',
            2
        );
    }

    public function test_shareholder_cannot_withdraw_more_kitta_than_owned(): void
    {
        $user = User::factory()->create();

        $shareholder = Shareholder::create([
            'code' => 'SH-001',
            'name' => 'Test Shareholder',
            'kitta' => 2,
            'per_kitta_value' => 1000,
            'total_investment' => 2000,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 10000,
            'current_balance' => 10000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(ShareTransactionService::class);

        try {
            $service->create([
                'transaction_type' => ShareTransaction::TYPE_WITHDRAW,
                'shareholder_id' => $shareholder->id,
                'account_id' => $cash->id,
                'date_ad' => '2026-08-12',
                'date_bs' => '2083-04-28',
                'financial_year' => '2083/84',
                'kitta' => 3,
                'per_kitta_value' => 1000,
                'created_by' => $user->id,
            ]);

            $this->fail(
                'Expected RuntimeException was not thrown.'
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Shareholder does not have enough Kitta to withdraw.',
                $exception->getMessage()
            );
        }

        $this->assertSame(
            2,
            $shareholder->fresh()->kitta
        );

        $this->assertSame(
            2000,
            $shareholder->fresh()->total_investment
        );

        $this->assertSame(
            10000,
            $cash->fresh()->current_balance
        );

        $this->assertDatabaseCount(
            'share_transactions',
            0
        );
    }

    public function test_withdraw_rolls_back_when_account_has_insufficient_balance(): void
    {
        $user = User::factory()->create();

        $shareholder = Shareholder::create([
            'code' => 'SH-001',
            'name' => 'Test Shareholder',
            'kitta' => 5,
            'per_kitta_value' => 1000,
            'total_investment' => 5000,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 1000,
            'current_balance' => 1000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(ShareTransactionService::class);

        $this->expectException(RuntimeException::class);

        try {
            $service->create([
                'transaction_type' => ShareTransaction::TYPE_WITHDRAW,
                'shareholder_id' => $shareholder->id,
                'account_id' => $cash->id,
                'date_ad' => '2026-08-12',
                'date_bs' => '2083-04-28',
                'financial_year' => '2083/84',
                'kitta' => 2,
                'per_kitta_value' => 1000,
                'created_by' => $user->id,
            ]);
        } finally {
            $this->assertSame(
                5,
                $shareholder->fresh()->kitta
            );

            $this->assertSame(
                5000,
                $shareholder->fresh()->total_investment
            );

            $this->assertSame(
                1000,
                $cash->fresh()->current_balance
            );

            $this->assertDatabaseCount(
                'share_transactions',
                0
            );
        }
    }

    public function test_fixed_deposit_cannot_be_used_for_share_transaction(): void
    {
        $user = User::factory()->create();

        $shareholder = Shareholder::create([
            'code' => 'SH-001',
            'name' => 'Test Shareholder',
            'kitta' => 0,
            'per_kitta_value' => 1000,
            'total_investment' => 0,
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

        $service = app(ShareTransactionService::class);

        $this->expectException(RuntimeException::class);

        $service->create([
            'transaction_type' => ShareTransaction::TYPE_BUY,
            'shareholder_id' => $shareholder->id,
            'account_id' => $fixedDeposit->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'kitta' => 1,
            'per_kitta_value' => 1000,
            'created_by' => $user->id,
        ]);
    }

    public function test_second_buy_can_use_a_different_manual_per_kitta_value(): void
    {
        $user = User::factory()->create();
        $shareholder = Shareholder::create([
            'code' => 'SH-002', 'name' => 'Variable Value', 'kitta' => 0,
            'per_kitta_value' => 9999, 'total_investment' => 0,
            'is_active' => true, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $cash = Account::create([
            'name' => 'Cash', 'code' => 'CASH02', 'type' => Account::TYPE_CASH,
            'opening_balance' => 0, 'current_balance' => 0, 'allow_negative' => false,
            'is_active' => true, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $service = app(ShareTransactionService::class);
        $base = [
            'transaction_type' => ShareTransaction::TYPE_BUY,
            'shareholder_id' => $shareholder->id, 'account_id' => $cash->id,
            'date_ad' => '2026-08-12', 'date_bs' => '2083-04-28',
            'financial_year' => '2083/84', 'created_by' => $user->id,
        ];

        $first = $service->create($base + ['kitta' => 2, 'per_kitta_value' => 600]);
        $second = $service->create($base + ['kitta' => 3, 'per_kitta_value' => 800]);

        $this->assertSame(1200, $first->total_amount);
        $this->assertSame(2400, $second->total_amount);
        $this->assertSame(5, $shareholder->fresh()->kitta);
        $this->assertSame(3600, $shareholder->fresh()->total_investment);
        $this->assertSame(3600, $cash->fresh()->current_balance);
    }

    public function test_withdraw_is_blocked_when_manual_value_would_make_investment_negative(): void
    {
        $user = User::factory()->create();
        $shareholder = Shareholder::create([
            'code' => 'SH-003', 'name' => 'Low Investment', 'kitta' => 5,
            'per_kitta_value' => 1000, 'total_investment' => 500,
            'is_active' => true, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $cash = Account::create([
            'name' => 'Cash', 'code' => 'CASH03', 'type' => Account::TYPE_CASH,
            'opening_balance' => 5000, 'current_balance' => 5000, 'allow_negative' => false,
            'is_active' => true, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->expectException(RuntimeException::class);

        try {
            app(ShareTransactionService::class)->create([
                'transaction_type' => ShareTransaction::TYPE_WITHDRAW,
                'shareholder_id' => $shareholder->id, 'account_id' => $cash->id,
                'date_ad' => '2026-08-12', 'date_bs' => '2083-04-28',
                'financial_year' => '2083/84', 'kitta' => 1,
                'per_kitta_value' => 600, 'created_by' => $user->id,
            ]);
        } finally {
            $this->assertSame(5, $shareholder->fresh()->kitta);
            $this->assertSame(500, $shareholder->fresh()->total_investment);
            $this->assertDatabaseCount('share_transactions', 0);
        }
    }
}
