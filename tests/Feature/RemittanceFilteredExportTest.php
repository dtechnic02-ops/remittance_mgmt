<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\RemittanceTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class RemittanceFilteredExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_filtered_remittances_show_commission_total_and_support_print_and_excel(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin', 'is_active' => true, 'email_verified_at' => now(),
        ]);
        $provider = $this->account($admin, 'PROVIDER', Account::TYPE_REMITTANCE);
        $cash = $this->account($admin, 'CASH', Account::TYPE_CASH);
        $customer = Customer::create([
            'customer_code' => 'CUS-EXPORT', 'name' => 'Export Customer',
            'is_active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $active = $this->remittance($admin, $customer, $provider, $cash, 'REM-ACTIVE', 250, 'active');
        $this->remittance($admin, $customer, $provider, $cash, 'REM-CANCELLED', 900, 'cancelled');
        $filters = [
            'financial_year' => 'all', 'status' => 'active',
            'provider_account_id' => $provider->id,
        ];

        $index = $this->actingAs($admin)->get(route('remittances.index', $filters))->assertOk();
        $this->assertSame(1, $index->viewData('totalRecords'));
        $this->assertSame(250, $index->viewData('totalCommission'));
        $index->assertSee('Total Records:')->assertSee('Total Commission:')->assertSee('Print A4')->assertSee('Export Excel');

        $this->actingAs($admin)->get(route('remittances.index', $filters + ['output' => 'print']))
            ->assertOk()->assertSee('Filtered Remittances Report')
            ->assertSee($active->transaction_number)->assertDontSee('REM-CANCELLED')->assertSee('250');

        $response = $this->actingAs($admin)
            ->get(route('remittances.index', $filters + ['output' => 'excel']))
            ->assertOk();
        $this->assertStringContainsString('remittances-filtered-', (string) $response->headers->get('content-disposition'));
        $sheet = IOFactory::load($response->baseResponse->getFile()->getPathname())->getActiveSheet();
        $this->assertSame($active->transaction_number, $sheet->getCell('A2')->getValue());
        $this->assertSame(250, $sheet->getCell('J2')->getValue());
    }

    private function account(User $user, string $code, string $type): Account
    {
        return Account::create([
            'name' => $code, 'code' => $code, 'type' => $type,
            'opening_balance' => 0, 'current_balance' => 0, 'is_active' => true,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
    }

    private function remittance(
        User $user,
        Customer $customer,
        Account $provider,
        Account $cash,
        string $number,
        int $commission,
        string $status
    ): RemittanceTransaction {
        return RemittanceTransaction::create([
            'transaction_number' => $number, 'direction' => 'send',
            'customer_id' => $customer->id, 'provider_account_id' => $provider->id,
            'cash_account_id' => $cash->id, 'date_ad' => now(), 'date_bs' => '2083-04-01',
            'financial_year' => '2083/84', 'principal_amount' => 1000,
            'service_charge' => $commission, 'total_cash_received' => 1000 + $commission,
            'status' => $status, 'created_by' => $user->id,
        ]);
    }
}
