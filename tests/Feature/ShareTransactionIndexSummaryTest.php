<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareTransactionIndexSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_print_uses_net_kitta_and_amount_for_raj_kumar(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin', 'is_active' => true, 'email_verified_at' => now(),
        ]);
        $account = Account::create([
            'name' => 'Cash', 'code' => 'RAJ-CASH', 'type' => Account::TYPE_CASH,
            'opening_balance' => 0, 'current_balance' => 0, 'is_active' => true,
        ]);
        $raj = $this->shareholder('RAJ', 'Raj Kumar');
        $receiver = $this->shareholder('RECEIVER', 'Receiver Only');

        $this->transaction('RAJ-BUY-1', ShareTransaction::TYPE_BUY, $raj, null, $account, 50000, 50);
        $this->transaction('RAJ-BUY-2', ShareTransaction::TYPE_BUY, $raj, null, $account, 1500, 1);
        $this->transaction('RAJ-WITHDRAW-1', ShareTransaction::TYPE_WITHDRAW, $raj, null, $account, 29450, 31);
        $this->transaction('RAJ-WITHDRAW-2', ShareTransaction::TYPE_WITHDRAW, $raj, null, $account, 9980, 10);
        $this->transaction('RAJ-TRANSFER', ShareTransaction::TYPE_TRANSFER, $raj, $receiver, null, 10000, 10);

        $response = $this->actingAs($admin)->get(route('share-transactions.index', [
            'search' => 'Raj Kumar', 'financial_year' => 'all', 'output' => 'print',
        ]))->assertOk()->assertSee('Total Kitta')->assertSee('Rs. +2,070');

        $this->assertSame(0, $response->viewData('totalKitta'));
        $this->assertSame(2070.0, $response->viewData('totalAmount'));
    }

    public function test_shareholder_search_uses_directional_net_share_value(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $account = Account::create([
            'name' => 'Cash',
            'code' => 'CASH-SUMMARY',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active' => true,
        ]);
        $ram = $this->shareholder('RAM', 'Ram Dhan Sunar');
        $receiver = $this->shareholder('RECEIVER', 'Transfer Receiver');
        $sumit = $this->shareholder('SUMIT', 'Sumit Budha');

        $this->transaction('BUY-RAM', ShareTransaction::TYPE_BUY, $ram, null, $account, 25000);
        $this->transaction('OUT-RAM-1', ShareTransaction::TYPE_TRANSFER, $ram, $receiver, null, 15000);
        $this->transaction('OUT-RAM-2', ShareTransaction::TYPE_TRANSFER, $ram, $receiver, null, 10000);
        $this->transaction('BUY-SUMIT', ShareTransaction::TYPE_BUY, $sumit, null, $account, 500000);
        $this->transaction('WITHDRAW-SUMIT', ShareTransaction::TYPE_WITHDRAW, $sumit, null, $account, 492000);

        $this->actingAs($admin)
            ->get(route('share-transactions.index', [
                'search' => 'RAM',
                'financial_year' => 'all',
            ]))
            ->assertOk()
            ->assertSee('Total Amount / Value:')
            ->assertSee('Rs. 0');

        $this->actingAs($admin)
            ->get(route('share-transactions.index', [
                'search' => 'SUMIT',
                'financial_year' => 'all',
            ]))
            ->assertOk()
            ->assertSee('Rs. +8,000');

        $this->actingAs($admin)
            ->get(route('share-transactions.index', [
                'financial_year' => 'all',
            ]))
            ->assertOk()
            ->assertSee('Rs. +33,000');
    }

    private function shareholder(string $code, string $name): Shareholder
    {
        return Shareholder::create([
            'code' => $code,
            'name' => $name,
            'kitta' => 0,
            'per_kitta_value' => 1000,
            'total_investment' => 0,
            'is_active' => true,
        ]);
    }

    private function transaction(
        string $number,
        string $type,
        Shareholder $from,
        ?Shareholder $to,
        ?Account $account,
        int $amount,
        int $kitta = 1
    ): void {
        ShareTransaction::create([
            'transaction_number' => $number,
            'transaction_type' => $type,
            'shareholder_id' => $from->id,
            'to_shareholder_id' => $to?->id,
            'account_id' => $account?->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'kitta' => $kitta,
            'per_kitta_value' => $amount,
            'total_amount' => $amount,
            'status' => 'active',
        ]);
    }
}
