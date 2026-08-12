<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class FinancialLedgerService
{
    public function post(array $data): LedgerEntry
    {
        return DB::transaction(function () use ($data) {
            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($data['account_id']);

            $amount = (int) $data['amount'];

            if ($amount <= 0) {
                throw new InvalidArgumentException(
                    'Ledger amount must be greater than zero.'
                );
            }

            if (! in_array(
                $data['direction'],
                ['increase', 'decrease'],
                true
            )) {
                throw new InvalidArgumentException(
                    'Invalid ledger direction.'
                );
            }

            $newBalance = $account->current_balance;

            if ($data['direction'] === 'increase') {
                $newBalance += $amount;
            } else {
                $newBalance -= $amount;
            }

            if (
                $newBalance < 0
                && ! $account->allow_negative
            ) {
                throw new RuntimeException(
                    'This account does not allow a negative balance.'
                );
            }

            $account->current_balance = $newBalance;
            $account->updated_by = $data['created_by'] ?? null;
            $account->save();

            return LedgerEntry::create([
                'account_id' => $account->id,
                'transaction_type' => $data['transaction_type'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'transaction_number' => $data['transaction_number'],
                'date_ad' => $data['date_ad'],
                'date_bs' => $data['date_bs'],
                'financial_year' => $data['financial_year'],
                'direction' => $data['direction'],
                'amount' => $amount,
                'balance_after' => $newBalance,
                'component' => $data['component'] ?? null,
                'note' => $data['note'] ?? null,
                'created_by' => $data['created_by'] ?? null,
                'is_reversal' => $data['is_reversal'] ?? false,
                'reversal_of_id' => $data['reversal_of_id'] ?? null,
            ]);
        });
    }

    public function reverse(
        LedgerEntry $originalEntry,
        int $userId,
        ?string $reason = null
    ): LedgerEntry {
        if ($originalEntry->is_reversal) {
            throw new RuntimeException(
                'A reversal entry cannot be reversed using this method.'
            );
        }

        $alreadyReversed = LedgerEntry::query()
            ->where('reversal_of_id', $originalEntry->id)
            ->exists();

        if ($alreadyReversed) {
            throw new RuntimeException(
                'This ledger entry has already been reversed.'
            );
        }

        $reverseDirection = $originalEntry->direction === 'increase'
            ? 'decrease'
            : 'increase';

        $note = trim(
            'Reversal of ledger entry #'
            .$originalEntry->id
            .($reason ? '. Reason: '.$reason : '')
        );

        return $this->post([
            'account_id' => $originalEntry->account_id,
            'transaction_type' => $originalEntry->transaction_type,
            'transaction_id' => $originalEntry->transaction_id,
            'transaction_number' => $originalEntry->transaction_number,
            'date_ad' => $originalEntry->date_ad->format('Y-m-d'),
            'date_bs' => $originalEntry->date_bs,
            'financial_year' => $originalEntry->financial_year,
            'direction' => $reverseDirection,
            'amount' => $originalEntry->amount,
            'component' => $originalEntry->component,
            'note' => $note,
            'created_by' => $userId,
            'is_reversal' => true,
            'reversal_of_id' => $originalEntry->id,
        ]);
    }
}