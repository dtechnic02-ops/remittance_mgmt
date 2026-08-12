<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use App\Services\RemittanceTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemittanceTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_remittance_posts_correct_account_movements(): void
    {
        $user = User::factory()->create();

        $customer = Customer::create([
            'customer_code' => 'CUS-000001',
            'name' => 'Test Customer',
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

        $provider = Account::create([
            'name' => 'City Express',
            'code' => 'CITY01',
            'type' => Account::TYPE_REMITTANCE,
            'opening_balance' => 20000,
            'current_balance' => 20000,
            'allow_negative' => true,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(RemittanceTransactionService::class);

        $transaction = $service->create([
            'customer_id' => $customer->id,
            'provider_account_id' => $provider->id,
            'cash_account_id' => $cash->id,

            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',

            'principal_amount' => 10000,
            'service_charge' => 100,

            'provider_reference' => 'CITY-TEST-001',
            'note' => 'Test remittance',

            'created_by' => $user->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        $this->assertSame(
            'REM-000001',
            $transaction->transaction_number
        );

        $this->assertSame(
            10000,
            $transaction->principal_amount
        );

        $this->assertSame(
            100,
            $transaction->service_charge
        );

        $this->assertSame(
            10100,
            $transaction->total_cash_received
        );

        /*
        |--------------------------------------------------------------------------
        | Account Balances
        |--------------------------------------------------------------------------
        |
        | Cash:
        | Opening 5,000
        | + Principal 10,000
        | + Service Charge 100
        | = 15,100
        |
        | Provider:
        | Opening 20,000
        | - Principal 10,000
        | = 10,000
        |
        */

        $this->assertSame(
            15100,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            10000,
            $provider->fresh()->current_balance
        );

        /*
        |--------------------------------------------------------------------------
        | Cash Principal Ledger
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $cash->id,
            'transaction_type' => 'remittance',
            'transaction_id' => $transaction->id,
            'transaction_number' => 'REM-000001',
            'direction' => 'increase',
            'amount' => 10000,
            'component' => 'principal',
            'balance_after' => 15000,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Service Charge Ledger
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $cash->id,
            'transaction_type' => 'remittance',
            'transaction_id' => $transaction->id,
            'transaction_number' => 'REM-000001',
            'direction' => 'increase',
            'amount' => 100,
            'component' => 'service_charge',
            'balance_after' => 15100,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Provider Ledger
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $provider->id,
            'transaction_type' => 'remittance',
            'transaction_id' => $transaction->id,
            'transaction_number' => 'REM-000001',
            'direction' => 'decrease',
            'amount' => 10000,
            'component' => 'principal',
            'balance_after' => 10000,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Exactly Three Ledger Entries
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseCount(
            'ledger_entries',
            3
        );
    }
}