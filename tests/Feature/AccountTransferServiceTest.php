<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Services\AccountTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class AccountTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_transfer_moves_balance_correctly(): void
    {
        $user = User::factory()->create();

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

        $bank = Account::create([
            'name' => 'Main Bank',
            'code' => 'BANK01',
            'type' => Account::TYPE_BANK,
            'opening_balance' => 1000,
            'current_balance' => 1000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(AccountTransferService::class);

        $transfer = $service->create([
            'from_account_id' => $cash->id,
            'to_account_id' => $bank->id,
            'date_ad' => '2026-08-09',
            'date_bs' => '2083-04-24',
            'financial_year' => '2083/84',
            'amount' => 1000,
            'reference' => 'TEST-TRANSFER',
            'note' => 'Transfer test',
            'created_by' => $user->id,
        ]);

        $this->assertSame(
            4000,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            2000,
            $bank->fresh()->current_balance
        );

        $this->assertSame(
            'TRF-000001',
            $transfer->transfer_number
        );

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $cash->id,
            'transaction_type' => 'account_transfer',
            'transaction_id' => $transfer->id,
            'direction' => 'decrease',
            'amount' => 1000,
            'component' => 'transfer',
        ]);

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $bank->id,
            'transaction_type' => 'account_transfer',
            'transaction_id' => $transfer->id,
            'direction' => 'increase',
            'amount' => 1000,
            'component' => 'transfer',
        ]);
    }

    public function test_account_transfer_cancellation_restores_balances(): void
    {
        $user = User::factory()->create();

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

        $bank = Account::create([
            'name' => 'Main Bank',
            'code' => 'BANK01',
            'type' => Account::TYPE_BANK,
            'opening_balance' => 1000,
            'current_balance' => 1000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(AccountTransferService::class);

        $transfer = $service->create([
            'from_account_id' => $cash->id,
            'to_account_id' => $bank->id,
            'date_ad' => '2026-08-09',
            'date_bs' => '2083-04-24',
            'financial_year' => '2083/84',
            'amount' => 1000,
            'created_by' => $user->id,
        ]);

        $service->cancel(
            $transfer,
            $user->id,
            'Wrong transfer'
        );

        $this->assertSame(
            5000,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            1000,
            $bank->fresh()->current_balance
        );

        $this->assertSame(
            'cancelled',
            $transfer->fresh()->status
        );

        $this->assertDatabaseCount(
            'ledger_entries',
            4
        );
    }

    public function test_fixed_deposit_cannot_be_used_in_transfer(): void
    {
        $this->expectException(RuntimeException::class);

        $user = User::factory()->create();

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

        $service = app(AccountTransferService::class);

        $service->create([
            'from_account_id' => $cash->id,
            'to_account_id' => $fixedDeposit->id,
            'date_ad' => '2026-08-09',
            'date_bs' => '2083-04-24',
            'financial_year' => '2083/84',
            'amount' => 1000,
            'created_by' => $user->id,
        ]);
    }
}