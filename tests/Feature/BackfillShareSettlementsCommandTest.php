<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackfillShareSettlementsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_backfills_gain_and_synchronizes_shareholder_without_touching_account_or_ledger(): void
    {
        [$user, $shareholder, $account] = $this->context(0, 8000, 8000);
        $this->buy($user, $shareholder, $account, 1, 500, 500000);
        $withdrawal = $this->withdraw($user, $shareholder, $account, 2, 500, 492000);

        $this->artisan('shares:backfill-settlements', ['--apply' => true])
            ->assertSuccessful();

        $settlement = ShareTransaction::query()->where('settlement_of_id', $withdrawal->id)->sole();
        $this->assertSame(ShareTransaction::TYPE_SETTLEMENT_GAIN, $settlement->transaction_type);
        $this->assertSame('8000.00', $settlement->total_amount);
        $this->assertSame(0, $shareholder->fresh()->kitta);
        $this->assertSame(0, $shareholder->fresh()->total_investment);
        $this->assertSame(8000, $account->fresh()->current_balance);
        $this->assertDatabaseCount('ledger_entries', 0);
        $this->assertSame(0.0, $this->netShareValue());
    }

    public function test_exact_price_full_withdrawal_needs_no_settlement(): void
    {
        [$user, $shareholder, $account] = $this->context(0, 0, 0);
        $this->buy($user, $shareholder, $account, 1, 5, 5000);
        $this->withdraw($user, $shareholder, $account, 2, 5, 5000);

        $this->artisan('shares:backfill-settlements', ['--apply' => true])->assertSuccessful();

        $this->assertDatabaseCount('share_transactions', 2);
        $this->assertSame(0, $shareholder->fresh()->total_investment);
    }

    public function test_opposite_difference_creates_loss_without_changing_account(): void
    {
        [$user, $shareholder, $account] = $this->context(0, 0, 500);
        $this->buy($user, $shareholder, $account, 1, 5, 4500);
        $withdrawal = $this->withdraw($user, $shareholder, $account, 2, 5, 5000);

        $this->artisan('shares:backfill-settlements', ['--apply' => true])->assertSuccessful();

        $settlement = ShareTransaction::query()->where('settlement_of_id', $withdrawal->id)->sole();
        $this->assertSame(ShareTransaction::TYPE_SETTLEMENT_LOSS, $settlement->transaction_type);
        $this->assertSame('500.00', $settlement->total_amount);
        $this->assertSame(0, $shareholder->fresh()->total_investment);
        $this->assertSame(500, $account->fresh()->current_balance);
        $this->assertDatabaseCount('ledger_entries', 0);
        $this->assertSame(0.0, $this->netShareValue());
    }

    public function test_cancelled_withdrawal_is_ignored(): void
    {
        [$user, $shareholder, $account] = $this->context(5, 5000, 5000);
        $this->buy($user, $shareholder, $account, 1, 5, 5000);
        $this->withdraw($user, $shareholder, $account, 2, 5, 4500, 'cancelled');

        $this->artisan('shares:backfill-settlements', ['--apply' => true])->assertSuccessful();

        $this->assertDatabaseCount('share_transactions', 2);
        $this->assertSame(5, $shareholder->fresh()->kitta);
        $this->assertSame(5000, $shareholder->fresh()->total_investment);
        $this->assertSame(5000, $account->fresh()->current_balance);
    }

    public function test_existing_settlement_is_not_duplicated(): void
    {
        [$user, $shareholder, $account] = $this->context(0, 0, 500);
        $this->buy($user, $shareholder, $account, 1, 5, 5000);
        $withdrawal = $this->withdraw($user, $shareholder, $account, 2, 5, 4500);
        $this->settlement($user, $shareholder, $account, $withdrawal, 3, 500);

        $this->artisan('shares:backfill-settlements', ['--apply' => true])->assertSuccessful();
        $this->artisan('shares:backfill-settlements', ['--apply' => true])->assertSuccessful();

        $this->assertSame(1, ShareTransaction::query()->where('settlement_of_id', $withdrawal->id)->count());
        $this->assertDatabaseCount('share_transactions', 3);
    }

    public function test_multiple_buy_withdraw_cycles_create_each_required_settlement(): void
    {
        [$user, $shareholder, $account] = $this->context(0, 900, 900);
        $this->buy($user, $shareholder, $account, 1, 5, 5000);
        $first = $this->withdraw($user, $shareholder, $account, 2, 5, 4500);
        $this->buy($user, $shareholder, $account, 3, 2, 2400);
        $second = $this->withdraw($user, $shareholder, $account, 4, 2, 2000);

        $this->artisan('shares:backfill-settlements', ['--apply' => true])->assertSuccessful();

        $this->assertDatabaseHas('share_transactions', [
            'settlement_of_id' => $first->id,
            'transaction_type' => ShareTransaction::TYPE_SETTLEMENT_GAIN,
            'total_amount' => 500,
        ]);
        $this->assertDatabaseHas('share_transactions', [
            'settlement_of_id' => $second->id,
            'transaction_type' => ShareTransaction::TYPE_SETTLEMENT_GAIN,
            'total_amount' => 400,
        ]);
        $this->assertSame(0, $shareholder->fresh()->total_investment);
        $this->assertSame(900, $account->fresh()->current_balance);
        $this->assertSame(0.0, $this->netShareValue());
    }

    public function test_dry_run_is_the_default_and_makes_no_database_changes(): void
    {
        [$user, $shareholder, $account] = $this->context(0, 500, 500);
        $this->buy($user, $shareholder, $account, 1, 5, 5000);
        $this->withdraw($user, $shareholder, $account, 2, 5, 4500);

        $this->artisan('shares:backfill-settlements')
            ->expectsOutputToContain('DRY RUN')
            ->assertSuccessful();

        $this->assertDatabaseCount('share_transactions', 2);
        $this->assertSame(500, $shareholder->fresh()->total_investment);
        $this->assertSame(500, $account->fresh()->current_balance);
    }

    private function context(int $kitta, int $investment, int $accountBalance): array
    {
        $user = User::factory()->create(['role' => 'admin']);
        $shareholder = Shareholder::query()->create([
            'code' => 'SH-'.uniqid(),
            'name' => 'Historical Shareholder',
            'kitta' => $kitta,
            'per_kitta_value' => 1000,
            'total_investment' => $investment,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $account = Account::query()->create([
            'name' => 'Historical Cash',
            'code' => 'CASH-'.uniqid(),
            'type' => Account::TYPE_CASH,
            'opening_balance' => 0,
            'current_balance' => $accountBalance,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return [$user, $shareholder, $account];
    }

    private function buy(User $user, Shareholder $shareholder, Account $account, int $day, int $kitta, int $amount): ShareTransaction
    {
        return $this->transaction($user, $shareholder, $account, $day, ShareTransaction::TYPE_BUY, $kitta, $amount);
    }

    private function withdraw(User $user, Shareholder $shareholder, Account $account, int $day, int $kitta, int $amount, string $status = 'active'): ShareTransaction
    {
        return $this->transaction($user, $shareholder, $account, $day, ShareTransaction::TYPE_WITHDRAW, $kitta, $amount, $status);
    }

    private function transaction(User $user, Shareholder $shareholder, Account $account, int $day, string $type, int $kitta, int $amount, string $status = 'active'): ShareTransaction
    {
        return ShareTransaction::query()->create([
            'transaction_number' => 'OLD-'.uniqid(),
            'transaction_type' => $type,
            'shareholder_id' => $shareholder->id,
            'account_id' => $account->id,
            'date_ad' => sprintf('2026-01-%02d', $day),
            'date_bs' => '2082-09-'.str_pad((string) $day, 2, '0', STR_PAD_LEFT),
            'financial_year' => '2082/83',
            'kitta' => $kitta,
            'per_kitta_value' => number_format($amount / $kitta, 2, '.', ''),
            'total_amount' => $amount,
            'status' => $status,
            'created_by' => $user->id,
        ]);
    }

    private function settlement(User $user, Shareholder $shareholder, Account $account, ShareTransaction $withdrawal, int $day, int $amount): ShareTransaction
    {
        return ShareTransaction::query()->create([
            'transaction_number' => 'SET-OLD-'.uniqid(),
            'transaction_type' => ShareTransaction::TYPE_SETTLEMENT_GAIN,
            'shareholder_id' => $shareholder->id,
            'account_id' => $account->id,
            'date_ad' => sprintf('2026-01-%02d', $day),
            'date_bs' => '2082-09-'.str_pad((string) $day, 2, '0', STR_PAD_LEFT),
            'financial_year' => '2082/83',
            'kitta' => 0,
            'per_kitta_value' => 0,
            'total_amount' => $amount,
            'investment_effect' => $amount,
            'settlement_of_id' => $withdrawal->id,
            'status' => 'active',
            'created_by' => $user->id,
        ]);
    }

    private function netShareValue(): float
    {
        return ShareTransaction::query()->where('status', 'active')->get()
            ->sum(fn (ShareTransaction $transaction): float => match ($transaction->transaction_type) {
                ShareTransaction::TYPE_BUY,
                ShareTransaction::TYPE_SETTLEMENT_LOSS => (float) $transaction->total_amount,
                ShareTransaction::TYPE_WITHDRAW,
                ShareTransaction::TYPE_SETTLEMENT_GAIN => -((float) $transaction->total_amount),
                default => 0.0,
            });
    }
}
