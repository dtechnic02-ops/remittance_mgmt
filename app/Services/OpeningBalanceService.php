<?php

namespace App\Services;

use App\Models\Account;
use App\Models\OpeningBalance;
use Illuminate\Support\Facades\DB;

class OpeningBalanceService
{
    public function create(array $data): OpeningBalance
    {
        return DB::transaction(function () use ($data) {
            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($data['account_id']);

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

            $account->opening_balance = $openingBalance->amount;

            // Preserve transactions already included in current balance.
            $account->current_balance += $difference;

            $account->updated_by = $data['updated_by'];
            $account->save();

            return $openingBalance->fresh();
        });
    }
}