<?php

namespace App\Services;

use App\Models\Account;
use App\Models\OpeningBalance;
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

            if ($amount < 0) {
                throw new RuntimeException(
                    'Opening balance cannot be negative.'
                );
            }

            $openingBalance = OpeningBalance::create($data);

            $account->opening_balance = $openingBalance->amount;

            // At this stage no transaction ledger exists yet.
            // Opening balance becomes the initial current balance.
            $account->current_balance = $openingBalance->amount;

            $account->updated_by = $data['created_by'];
            $account->save();

            return $openingBalance;
        });
    }

    public function update(
        OpeningBalance $openingBalance,
        array $data
    ): OpeningBalance {
        return DB::transaction(function () use ($openingBalance, $data) {
            $oldAmount = $openingBalance->amount;

            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($openingBalance->account_id);

            $openingBalance->update($data);

            $difference = $openingBalance->amount - $oldAmount;

            $newCurrentBalance =
                (int) $account->current_balance + $difference;

            if ($openingBalance->amount < 0) {
                throw new RuntimeException(
                    'Opening balance cannot be negative.'
                );
            }

            if ($newCurrentBalance < 0) {
                throw new RuntimeException(
                    'Opening balance change would make the account balance negative.'
                );
            }

            $account->opening_balance = $openingBalance->amount;

            // Preserve transactions already included in current balance.
            $account->current_balance = $newCurrentBalance;

            $account->updated_by = $data['updated_by'];
            $account->save();

            return $openingBalance->fresh();
        });
    }
}
