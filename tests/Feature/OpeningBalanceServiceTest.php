<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\OpeningBalance;
use App\Models\User;
use App\Services\OpeningBalanceService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class OpeningBalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_positive_opening_balance_creation_succeeds_without_double_counting(): void
    {
        [$user, $account] = $this->context('OB01');

        $openingBalance = $this->service()->create(
            $this->data($account, $user, 10000)
        );

        $account = $account->fresh();

        $this->assertSame(10000, $openingBalance->amount);
        $this->assertSame(10000, $account->opening_balance);
        $this->assertSame(10000, $account->current_balance);
        $this->assertDatabaseCount('ledger_entries', 0);
        $this->assertDatabaseHas('opening_balance_adjustments', [
            'opening_balance_id' => $openingBalance->id,
            'action' => 'created',
            'opening_balance_before' => 0,
            'opening_balance_after' => 10000,
            'current_balance_before' => 0,
            'current_balance_after' => 10000,
        ]);
    }

    public function test_zero_opening_balance_creation_succeeds(): void
    {
        [$user, $account] = $this->context('OB02');

        $openingBalance = $this->service()->create(
            $this->data($account, $user, 0)
        );

        $this->assertSame(0, $openingBalance->amount);
        $this->assertSame(0, $account->fresh()->current_balance);
    }

    public function test_opening_balance_creation_rejects_negative_amount(): void
    {
        [$user, $account] = $this->context('OB03');

        try {
            $this->service()->create($this->data($account, $user, -1));
            $this->fail('Expected negative opening balance to be blocked.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Opening balance cannot be negative.',
                $exception->getMessage()
            );
        }

        $this->assertDatabaseCount('opening_balances', 0);
        $this->assertSame(0, $account->fresh()->current_balance);
    }

    public function test_duplicate_account_and_financial_year_is_blocked(): void
    {
        [$user, $account] = $this->context('OB04');
        $this->service()->create($this->data($account, $user, 1000));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Opening Balance already exists for this account in the selected financial year.'
        );

        $this->service()->create($this->data($account, $user, 2000));
    }

    public function test_database_unique_constraint_also_blocks_duplicate(): void
    {
        [$user, $account] = $this->context('OB05');
        OpeningBalance::create($this->data($account, $user, 1000));

        $this->expectException(QueryException::class);
        OpeningBalance::create($this->data($account, $user, 2000));
    }

    public function test_edit_upward_adjusts_current_balance_by_difference(): void
    {
        [$user, $account, $openingBalance] =
            $this->existing('OB06', 10000, 8000);

        $updated = $this->service()->update(
            $openingBalance,
            $this->updateData($user, 12000)
        );

        $account = $account->fresh();

        $this->assertSame(12000, $updated->amount);
        $this->assertSame(12000, $account->opening_balance);
        $this->assertSame(10000, $account->current_balance);
        $this->assertDatabaseHas('opening_balance_adjustments', [
            'opening_balance_id' => $openingBalance->id,
            'action' => 'updated',
            'opening_balance_before' => 10000,
            'opening_balance_after' => 12000,
            'current_balance_before' => 8000,
            'current_balance_after' => 10000,
        ]);
    }

    public function test_edit_downward_adjusts_current_balance_by_difference(): void
    {
        [$user, $account, $openingBalance] =
            $this->existing('OB07', 10000, 8000);

        $updated = $this->service()->update(
            $openingBalance,
            $this->updateData($user, 7000)
        );

        $account = $account->fresh();

        $this->assertSame(7000, $updated->amount);
        $this->assertSame(7000, $account->opening_balance);
        $this->assertSame(5000, $account->current_balance);
    }

    public function test_unsafe_edit_rolls_back_opening_and_account_changes(): void
    {
        [$user, $account, $openingBalance] =
            $this->existing('OB08', 10000, 3000);

        try {
            $this->service()->update(
                $openingBalance,
                $this->updateData($user, 4000)
            );
            $this->fail('Expected unsafe opening balance edit to be blocked.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Opening balance change would make the account balance negative.',
                $exception->getMessage()
            );
        }

        $account = $account->fresh();

        $this->assertSame(10000, $openingBalance->fresh()->amount);
        $this->assertSame(10000, $account->opening_balance);
        $this->assertSame(3000, $account->current_balance);
        $this->assertDatabaseCount('ledger_entries', 0);
        $this->assertDatabaseCount('opening_balance_adjustments', 0);
    }

    public function test_duplicate_submission_returns_user_friendly_validation_error(): void
    {
        [$user, $account] = $this->context('OB09');
        $user->update(['role' => 'admin', 'is_active' => true]);
        $this->service()->create($this->data($account, $user, 1000));

        $response = $this->actingAs($user)->post(
            route('opening-balances.store'),
            [
                'account_id' => $account->id,
                'date_ad' => '2026-08-08',
                'amount' => 2000,
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors('account_id');
        $this->assertDatabaseCount('opening_balances', 1);
    }

    public function test_failed_http_edit_keeps_old_attachment_and_returns_error(): void
    {
        Storage::fake('public');
        [$user, $account, $openingBalance] =
            $this->existing('OB10', 10000, 3000);
        $user->update(['role' => 'admin', 'is_active' => true]);
        Storage::disk('public')->put('opening-balances/original.pdf', 'original');
        $openingBalance->update([
            'attachment' => 'opening-balances/original.pdf',
        ]);

        $response = $this->actingAs($user)->put(
            route('opening-balances.update', $openingBalance),
            [
                'date_ad' => '2026-08-08',
                'amount' => 4000,
                'attachment' => UploadedFile::fake()->create(
                    'replacement.pdf',
                    10,
                    'application/pdf'
                ),
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors('opening_balance');
        Storage::disk('public')->assertExists('opening-balances/original.pdf');
        $this->assertSame(
            ['opening-balances/original.pdf'],
            Storage::disk('public')->allFiles('opening-balances')
        );
        $this->assertSame(10000, $openingBalance->fresh()->amount);
        $this->assertSame(3000, $account->fresh()->current_balance);
    }

    private function service(): OpeningBalanceService
    {
        return app(OpeningBalanceService::class);
    }

    private function context(string $code): array
    {
        $user = User::factory()->create();
        $account = Account::create([
            'name' => 'Opening Balance Test',
            'code' => $code,
            'type' => Account::TYPE_CASH,
            'opening_balance' => 0,
            'current_balance' => 0,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return [$user, $account];
    }

    private function existing(
        string $code,
        int $openingAmount,
        int $currentBalance
    ): array {
        [$user, $account] = $this->context($code);
        $account->update([
            'opening_balance' => $openingAmount,
            'current_balance' => $currentBalance,
        ]);
        $openingBalance = OpeningBalance::create(
            $this->data($account, $user, $openingAmount)
        );

        return [$user, $account, $openingBalance];
    }

    private function data(
        Account $account,
        User $user,
        int $amount
    ): array {
        return [
            'account_id' => $account->id,
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'amount' => $amount,
            'created_by' => $user->id,
        ];
    }

    private function updateData(User $user, int $amount): array
    {
        return [
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'amount' => $amount,
            'updated_by' => $user->id,
        ];
    }
}
