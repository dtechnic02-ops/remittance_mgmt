<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\User;
use App\Services\RemittanceTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class RemittanceCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_remittance_cancellation_reverses_all_financial_effects(): void
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
            'created_by' => $user->id,
        ]);

        $this->assertSame(15100, $cash->fresh()->current_balance);
        $this->assertSame(10000, $provider->fresh()->current_balance);

        $service->cancel(
            $transaction,
            $user->id,
            'Wrong transaction'
        );

        $this->assertSame(5000, $cash->fresh()->current_balance);
        $this->assertSame(20000, $provider->fresh()->current_balance);

        $this->assertSame(
            'cancelled',
            $transaction->fresh()->status
        );

        $this->assertSame(
            3,
            LedgerEntry::query()
                ->where('transaction_type', 'remittance')
                ->where('transaction_id', $transaction->id)
                ->where('is_reversal', false)
                ->count()
        );

        $this->assertSame(
            3,
            LedgerEntry::query()
                ->where('transaction_type', 'remittance')
                ->where('transaction_id', $transaction->id)
                ->where('is_reversal', true)
                ->count()
        );

        $this->assertSame(
            6,
            LedgerEntry::query()
                ->where('transaction_type', 'remittance')
                ->where('transaction_id', $transaction->id)
                ->count()
        );
    }

    public function test_same_remittance_cannot_be_cancelled_twice(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'This remittance transaction is already cancelled.'
        );

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
            'created_by' => $user->id,
        ]);

        $service->cancel(
            $transaction,
            $user->id,
            'First cancellation'
        );

        $service->cancel(
            $transaction->fresh(),
            $user->id,
            'Second cancellation'
        );
    }
}