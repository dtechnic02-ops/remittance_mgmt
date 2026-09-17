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

    public function test_create_form_provider_dropdown_lists_active_remittance_blb_and_bank_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->account($admin, 'CITIZEN REMIT', 'CR01', Account::TYPE_REMITTANCE);
        $this->account($admin, 'Other Remit', 'OR01', Account::TYPE_REMITTANCE);
        $this->account($admin, 'BLB One', 'BLB01', Account::TYPE_BLB);
        $this->account($admin, 'Bank One', 'BNK01', Account::TYPE_BANK);
        $this->account($admin, 'Cash Desk', 'CASHX', Account::TYPE_CASH);
        $this->account($admin, 'FD One', 'FD01', Account::TYPE_FIXED_DEPOSIT);
        $this->account($admin, 'Inactive Remit', 'IR01', Account::TYPE_REMITTANCE, false);
        $this->account($admin, 'Inactive Bank', 'IB01', Account::TYPE_BANK, false);

        $providers = $this->actingAs($admin)
            ->get(route('remittances.create'))
            ->assertOk()
            ->viewData('providers');

        $this->assertEqualsCanonicalizing(
            ['CITIZEN REMIT', 'Other Remit', 'BLB One', 'Bank One'],
            $providers->pluck('name')->values()->all()
        );
    }

    public function test_send_and_receive_use_existing_rules_for_bank_and_blb_provider_accounts(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUS-000002',
            'name' => 'Second Customer',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $cash = $this->account($user, 'Main Cash', 'CASH02', Account::TYPE_CASH, true, 5000);
        $bank = $this->account($user, 'Bank Provider', 'BNK02', Account::TYPE_BANK, true, 20000);
        $blb = $this->account($user, 'BLB Provider', 'BLB02', Account::TYPE_BLB, true, 8000);

        $service = app(RemittanceTransactionService::class);

        $send = $service->create([
            'direction' => 'send',
            'customer_id' => $customer->id,
            'provider_account_id' => $bank->id,
            'cash_account_id' => $cash->id,
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'principal_amount' => 10000,
            'service_charge' => 100,
            'created_by' => $user->id,
        ]);

        $this->assertSame(15100, $cash->fresh()->current_balance);
        $this->assertSame(10000, $bank->fresh()->current_balance);
        $this->assertSame(10100, $send->total_cash_received);

        $receive = $service->create([
            'direction' => 'receive',
            'customer_id' => $customer->id,
            'provider_account_id' => $blb->id,
            'cash_account_id' => $cash->id,
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'principal_amount' => 1000,
            'service_charge' => 50,
            'created_by' => $user->id,
        ]);

        $this->assertSame(14150, $cash->fresh()->current_balance);
        $this->assertSame(9000, $blb->fresh()->current_balance);
        $this->assertSame(950, $receive->total_cash_received);
    }

    public function test_cash_fixed_deposit_and_inactive_accounts_cannot_be_used_as_provider(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUS-000003',
            'name' => 'Third Customer',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $cash = $this->account($user, 'Till', 'CASH03', Account::TYPE_CASH);
        $fd = $this->account($user, 'FD Desk', 'FD03', Account::TYPE_FIXED_DEPOSIT);
        $inactive = $this->account($user, 'Inactive Remit', 'IR03', Account::TYPE_REMITTANCE, false);
        $service = app(RemittanceTransactionService::class);
        $payload = [
            'direction' => 'send',
            'customer_id' => $customer->id,
            'cash_account_id' => $cash->id,
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'principal_amount' => 1000,
            'service_charge' => 0,
            'created_by' => $user->id,
        ];

        foreach ([$cash->id, $fd->id, $inactive->id] as $providerId) {
            try {
                $service->create($payload + ['provider_account_id' => $providerId]);
                $this->fail('Expected provider account lookup to fail.');
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
                $this->assertTrue(true);
            }
        }
    }

    public function test_create_form_uses_searchable_customer_field_instead_of_native_dropdown(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $active = Customer::create([
            'customer_code' => 'CUS-SEARCH',
            'name' => 'Searchable Customer',
            'mobile' => '9800000001',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        Customer::create([
            'customer_code' => 'CUS-INACTIVE',
            'name' => 'Inactive Customer',
            'mobile' => '9800000002',
            'is_active' => false,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('remittances.create'))
            ->assertOk();

        $html = $response->getContent();
        $this->assertStringNotContainsString('<select name="customer_id"', $html);
        $this->assertStringContainsString('id="customer_search"', $html);
        $this->assertStringContainsString('id="customer_results"', $html);
        $this->assertStringContainsString('id="customer_search_data"', $html);

        preg_match(
            '/id="customer_search_data">(?P<json>.*?)<\/script>/s',
            $html,
            $matches
        );
        $this->assertNotEmpty($matches['json'] ?? null);
        $payload = json_decode($matches['json'], true);
        $this->assertIsArray($payload);
        $this->assertCount(1, $payload);
        $this->assertSame($active->id, $payload[0]['id']);
        $this->assertSame('Searchable Customer', $payload[0]['name']);
        $this->assertSame('CUS-SEARCH', $payload[0]['customer_code']);
        $this->assertSame('9800000001', $payload[0]['mobile']);
        $this->assertArrayNotHasKey('email', $payload[0]);
        $this->assertArrayNotHasKey('address', $payload[0]);
    }

    public function test_store_requires_customer_and_saves_selected_customer_id(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $customer = Customer::create([
            'customer_code' => 'CUS-SAVE',
            'name' => 'Save Customer',
            'is_active' => true,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        $provider = $this->account($admin, 'Provider', 'PROV-S', Account::TYPE_REMITTANCE, true, 20000);
        $cash = $this->account($admin, 'Cash', 'CASH-S', Account::TYPE_CASH, true, 5000);
        $payload = [
            'direction' => 'send',
            'provider_account_id' => $provider->id,
            'cash_account_id' => $cash->id,
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'principal_amount' => 1000,
            'service_charge' => 10,
        ];

        $this->actingAs($admin)
            ->from(route('remittances.create'))
            ->post(route('remittances.store'), $payload)
            ->assertRedirect(route('remittances.create'))
            ->assertSessionHasErrors('customer_id');

        $response = $this->actingAs($admin)
            ->post(route('remittances.store'), $payload + ['customer_id' => $customer->id]);
        $response->assertRedirect();
        $this->assertDatabaseHas('remittance_transactions', [
            'customer_id' => $customer->id,
            'provider_account_id' => $provider->id,
            'cash_account_id' => $cash->id,
            'principal_amount' => 1000,
            'service_charge' => 10,
            'direction' => 'send',
        ]);
    }

    private function account(
        User $user,
        string $name,
        string $code,
        string $type,
        bool $active = true,
        int $balance = 0
    ): Account {
        return Account::create([
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'opening_balance' => $balance,
            'current_balance' => $balance,
            'is_active' => $active,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}