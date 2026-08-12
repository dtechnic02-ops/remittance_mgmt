<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\OpeningBalance;
use App\Models\User;
use App\Services\OpeningBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class OpeningBalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_balance_creation_rejects_negative_amount(): void
    {
        $user = User::factory()->create();
        $account = $this->account($user, 0, 0, 'OB01');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Opening balance cannot be negative.');

        app(OpeningBalanceService::class)->create([
            'account_id' => $account->id,
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'amount' => -1,
            'created_by' => $user->id,
        ]);
    }

    public function test_opening_balance_edit_cannot_make_current_balance_negative(): void
    {
        $user = User::factory()->create();
        $account = $this->account($user, 10000, 3000, 'OB02');
        $openingBalance = OpeningBalance::create([
            'account_id' => $account->id,
            'date_ad' => '2026-08-08',
            'date_bs' => '2083-04-23',
            'financial_year' => '2083/84',
            'amount' => 10000,
            'created_by' => $user->id,
        ]);

        try {
            app(OpeningBalanceService::class)->update($openingBalance, [
                'date_ad' => '2026-08-08',
                'date_bs' => '2083-04-23',
                'financial_year' => '2083/84',
                'amount' => 5000,
                'updated_by' => $user->id,
            ]);
            $this->fail('Expected unsafe opening balance edit to be blocked.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Opening balance change would make the account balance negative.',
                $exception->getMessage()
            );
        }

        $this->assertSame(10000, $openingBalance->fresh()->amount);
        $this->assertSame(3000, $account->fresh()->current_balance);
    }

    private function account(
        User $user,
        int $openingBalance,
        int $currentBalance,
        string $code
    ): Account {
        return Account::create([
            'name' => 'Opening Balance Test',
            'code' => $code,
            'type' => Account::TYPE_CASH,
            'opening_balance' => $openingBalance,
            'current_balance' => $currentBalance,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}
