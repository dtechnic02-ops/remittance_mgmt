<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Services\FinancialLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class FinancialLedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_increases_account_balance_and_creates_ledger_entry(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'name' => 'Test Cash',
            'code' => 'TC01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 1000,
            'current_balance' => 1000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(FinancialLedgerService::class);

        $entry = $service->post([
            'account_id' => $account->id,
            'transaction_type' => 'test',
            'transaction_id' => 1,
            'transaction_number' => 'TEST-000001',
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'direction' => 'increase',
            'amount' => 500,
            'component' => 'principal',
            'created_by' => $user->id,
        ]);

        $this->assertSame(1500, $account->fresh()->current_balance);
        $this->assertSame(1500, $entry->balance_after);

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $account->id,
            'transaction_number' => 'TEST-000001',
            'direction' => 'increase',
            'amount' => 500,
            'balance_after' => 1500,
        ]);
    }

    public function test_it_decreases_account_balance(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'name' => 'Test Bank',
            'code' => 'TB01',
            'type' => Account::TYPE_BANK,
            'opening_balance' => 1000,
            'current_balance' => 1000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(FinancialLedgerService::class);

        $service->post([
            'account_id' => $account->id,
            'transaction_type' => 'test',
            'transaction_id' => 2,
            'transaction_number' => 'TEST-000002',
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'direction' => 'decrease',
            'amount' => 400,
            'component' => 'principal',
            'created_by' => $user->id,
        ]);

        $this->assertSame(600, $account->fresh()->current_balance);
    }

    public function test_it_allows_decrease_to_exactly_zero(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'name' => 'Exact Zero Cash',
            'code' => 'EZ01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 500,
            'current_balance' => 500,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        app(FinancialLedgerService::class)->post([
            'account_id' => $account->id,
            'transaction_type' => 'test',
            'transaction_id' => 3,
            'transaction_number' => 'TEST-000003',
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'direction' => 'decrease',
            'amount' => 500,
            'component' => 'principal',
            'created_by' => $user->id,
        ]);

        $this->assertSame(0, $account->fresh()->current_balance);
    }

    public function test_it_blocks_decrease_below_zero(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'name' => 'Protected Cash',
            'code' => 'PC01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 500,
            'current_balance' => 500,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(FinancialLedgerService::class);

        try {
            $service->post([
                'account_id' => $account->id,
                'transaction_type' => 'test',
                'transaction_id' => 3,
                'transaction_number' => 'TEST-000003',
                'date_ad' => '2026-08-08',
                'date_bs' => '2083-04-23',
                'financial_year' => '2083/84',
                'direction' => 'decrease',
                'amount' => 600,
                'component' => 'principal',
                'created_by' => $user->id,
            ]);

            $this->fail('Expected negative balance protection exception.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Account balance cannot become negative.',
                $exception->getMessage()
            );
        }

        $this->assertSame(500, $account->fresh()->current_balance);

        $this->assertDatabaseMissing('ledger_entries', [
            'transaction_number' => 'TEST-000003',
        ]);
    }

    public function test_allow_negative_no_longer_permits_negative_balance(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'name' => 'City Express',
            'code' => 'CE01',
            'type' => Account::TYPE_REMITTANCE,
            'opening_balance' => 2000,
            'current_balance' => 2000,
            'allow_negative' => true,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(FinancialLedgerService::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Account balance cannot become negative.');

        $service->post([
            'account_id' => $account->id,
            'transaction_type' => 'remittance',
            'transaction_id' => 4,
            'transaction_number' => 'REM-000001',
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'direction' => 'decrease',
            'amount' => 10000,
            'component' => 'principal',
            'created_by' => $user->id,
        ]);
    }

    public function test_reversal_that_would_create_negative_balance_is_blocked(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'name' => 'Reversal Cash',
            'code' => 'RC01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 100,
            'current_balance' => 100,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $service = app(FinancialLedgerService::class);

        $original = $service->post([
            'account_id' => $account->id,
            'transaction_type' => 'income',
            'transaction_id' => 5,
            'transaction_number' => 'INC-000005',
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'direction' => 'increase',
            'amount' => 50,
            'component' => 'income',
            'created_by' => $user->id,
        ]);

        $service->post([
            'account_id' => $account->id,
            'transaction_type' => 'expense',
            'transaction_id' => 6,
            'transaction_number' => 'EXP-000006',
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'direction' => 'decrease',
            'amount' => 120,
            'component' => 'expense',
            'created_by' => $user->id,
        ]);

        try {
            $service->reverse($original, $user->id, 'Test reversal');
            $this->fail('Expected reversal to be blocked.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Account balance cannot become negative.',
                $exception->getMessage()
            );
        }

        $this->assertSame(30, $account->fresh()->current_balance);
        $this->assertDatabaseMissing('ledger_entries', [
            'reversal_of_id' => $original->id,
        ]);
    }
}
