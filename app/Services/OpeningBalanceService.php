<?php

namespace App\Services;

use App\Models\Account;
use App\Models\OpeningBalance;
use App\Models\OpeningBalanceAdjustment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OpeningBalanceService
{
    public function create(array $data): OpeningBalance
    {
        return DB::transaction(function () use ($data) {
            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($data['account_id']);

            $amount = (int) $data['amount'];
            $currentBalanceBefore = (int) $account->current_balance;

            if ($amount < 0) {
                throw new RuntimeException(
                    'Opening balance cannot be negative.'
                );
            }

            $duplicateExists = OpeningBalance::query()
                ->where('account_id', $account->id)
                ->where('financial_year', $data['financial_year'])
                ->exists();

            if ($duplicateExists) {
                throw new RuntimeException(
                    'Opening Balance already exists for this account in the selected financial year.'
                );
            }

            $openingBalance = OpeningBalance::create($data);

            $account->opening_balance = $openingBalance->amount;

            // At this stage no transaction ledger exists yet.
            // Opening balance becomes the initial current balance.
            $account->current_balance = $openingBalance->amount;

            $account->updated_by = $data['created_by'];
            $account->save();

            OpeningBalanceAdjustment::create([
                'opening_balance_id' => $openingBalance->id,
                'account_id' => $account->id,
                'action' => 'created',
                'opening_balance_before' => 0,
                'opening_balance_after' => $amount,
                'current_balance_before' => $currentBalanceBefore,
                'current_balance_after' => $amount,
                'created_by' => $data['created_by'],
            ]);

            return $openingBalance;
        });
    }

    public function update(
        OpeningBalance $openingBalance,
        array $data
    ): OpeningBalance {
        return DB::transaction(function () use ($openingBalance, $data) {
            $openingBalance = OpeningBalance::query()
                ->lockForUpdate()
                ->findOrFail($openingBalance->id);

            $oldAmount = (int) $openingBalance->amount;

            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($openingBalance->account_id);

            $oldCurrentBalance = (int) $account->current_balance;

            $duplicateExists = OpeningBalance::query()
                ->where('account_id', $account->id)
                ->where('financial_year', $data['financial_year'])
                ->whereKeyNot($openingBalance->id)
                ->exists();

            if ($duplicateExists) {
                throw new RuntimeException(
                    'Opening Balance already exists for this account in that financial year.'
                );
            }

            $newAmount = (int) $data['amount'];

            if ($newAmount < 0) {
                throw new RuntimeException(
                    'Opening balance cannot be negative.'
                );
            }

            $difference = $newAmount - $oldAmount;

            $newCurrentBalance =
                (int) $account->current_balance + $difference;

            if ($newCurrentBalance < 0) {
                throw new RuntimeException(
                    'Opening balance change would make the account balance negative.'
                );
            }

            $openingBalance->update($data);

            $account->opening_balance = $openingBalance->amount;

            // Preserve transactions already included in current balance.
            $account->current_balance = $newCurrentBalance;

            $account->updated_by = $data['updated_by'];
            $account->save();

            OpeningBalanceAdjustment::create([
                'opening_balance_id' => $openingBalance->id,
                'account_id' => $account->id,
                'action' => 'updated',
                'opening_balance_before' => $oldAmount,
                'opening_balance_after' => $newAmount,
                'current_balance_before' => $oldCurrentBalance,
                'current_balance_after' => $newCurrentBalance,
                'created_by' => $data['updated_by'],
            ]);

            return $openingBalance->fresh();
        });
    }
}
