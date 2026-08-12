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

    public function test_it_blocks_negative_balance_when_account_does_not_allow_it(): void
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
                'This account does not allow a negative balance.',
                $exception->getMessage()
            );
        }

        $this->assertSame(500, $account->fresh()->current_balance);

        $this->assertDatabaseMissing('ledger_entries', [
            'transaction_number' => 'TEST-000003',
        ]);
    }

    public function test_it_allows_negative_balance_when_account_is_configured_for_it(): void
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

        $entry = $service->post([
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

        $this->assertSame(-8000, $account->fresh()->current_balance);
        $this->assertSame(-8000, $entry->balance_after);
    }
}