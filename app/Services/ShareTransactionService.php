<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LedgerEntry;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShareTransactionService
{
    public function __construct(
        private readonly FinancialLedgerService $ledgerService
    ) {
    }

    public function create(array $data): ShareTransaction
    {
        return DB::transaction(function () use ($data) {
            $shareholder = Shareholder::query()
                ->lockForUpdate()
                ->whereKey($data['shareholder_id'])
                ->where('is_active', true)
                ->firstOrFail();

            $account = Account::query()
                ->whereKey($data['account_id'])
                ->where('is_active', true)
                ->firstOrFail();

            if ($account->type === Account::TYPE_FIXED_DEPOSIT) {
                throw new RuntimeException(
                    'Fixed Deposit account cannot be used for share transactions.'
                );
            }

            $transactionType =
                (string) $data['transaction_type'];

            if (! in_array(
                $transactionType,
                [
                    ShareTransaction::TYPE_BUY,
                    ShareTransaction::TYPE_WITHDRAW,
                ],
                true
            )) {
                throw new RuntimeException(
                    'Invalid share transaction type.'
                );
            }

            $kitta = (int) $data['kitta'];

            if ($kitta <= 0) {
                throw new RuntimeException(
                    'Kitta must be greater than zero.'
                );
            }

            /*
             * Per Kitta Value shareholder master बाट लिने।
             * User ले transaction form बाट value परिवर्तन गर्न पाउँदैन।
             */
            $perKittaValue =
                (int) $shareholder->per_kitta_value;

            if ($perKittaValue <= 0) {
                throw new RuntimeException(
                    'Invalid per Kitta value.'
                );
            }

            $totalAmount =
                $kitta * $perKittaValue;

            if ($transactionType === ShareTransaction::TYPE_WITHDRAW) {
                if ((int) $shareholder->kitta < $kitta) {
                    throw new RuntimeException(
                        'Shareholder does not have enough Kitta to withdraw.'
                    );
                }
            }

            $transaction = ShareTransaction::create([
                'transaction_number' => TransactionNumberService::temporary(),
                'transaction_type' => $transactionType,
                'shareholder_id' => $shareholder->id,
                'account_id' => $account->id,
                'date_ad' => $data['date_ad'],
                'date_bs' => $data['date_bs'],
                'financial_year' => $data['financial_year'],
                'kitta' => $kitta,
                'per_kitta_value' => $perKittaValue,
                'total_amount' => $totalAmount,
                'reference' => $data['reference'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'note' => $data['note'] ?? null,
                'status' => 'active',
                'created_by' => $data['created_by'],
            ]);

            $transaction->transaction_number = TransactionNumberService::fromId('SHR-', $transaction->id);
            $transaction->save();

            /*
             * BUY
             * Shareholder Kitta +
             * Investment +
             * Selected Account +
             */
            if ($transactionType === ShareTransaction::TYPE_BUY) {
                $shareholder->kitta =
                    (int) $shareholder->kitta + $kitta;

                $shareholder->total_investment =
                    (int) $shareholder->total_investment
                    + $totalAmount;

                $shareholder->updated_by =
                    $data['created_by'];

                $shareholder->save();

                $this->ledgerService->post([
                    'account_id' => $account->id,
                    'transaction_type' => 'share_transaction',
                    'transaction_id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'date_ad' => $transaction->date_ad->format('Y-m-d'),
                    'date_bs' => $transaction->date_bs,
                    'financial_year' => $transaction->financial_year,
                    'direction' => 'increase',
                    'amount' => $totalAmount,
                    'component' => 'share_buy',
                    'note' => $transaction->note,
                    'created_by' => $transaction->created_by,
                ]);
            }

            /*
             * WITHDRAW
             * Shareholder Kitta -
             * Investment -
             * Selected Account -
             */
            if ($transactionType === ShareTransaction::TYPE_WITHDRAW) {
                $shareholder->kitta =
                    (int) $shareholder->kitta - $kitta;

                $shareholder->total_investment =
                    (int) $shareholder->total_investment
                    - $totalAmount;

                if ($shareholder->total_investment < 0) {
                    throw new RuntimeException(
                        'Shareholder investment cannot become negative.'
                    );
                }

                $shareholder->updated_by =
                    $data['created_by'];

                $shareholder->save();

                $this->ledgerService->post([
                    'account_id' => $account->id,
                    'transaction_type' => 'share_transaction',
                    'transaction_id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                    'date_ad' => $transaction->date_ad->format('Y-m-d'),
                    'date_bs' => $transaction->date_bs,
                    'financial_year' => $transaction->financial_year,
                    'direction' => 'decrease',
                    'amount' => $totalAmount,
                    'component' => 'share_withdraw',
                    'note' => $transaction->note,
                    'created_by' => $transaction->created_by,
                ]);
            }

            return $transaction->fresh([
                'shareholder',
                'account',
            ]);
        });
    }
public function updateMetadata(
    ShareTransaction $transaction,
    array $data
): ShareTransaction {
    return DB::transaction(function () use (
        $transaction,
        $data
    ) {
        $transaction = ShareTransaction::query()
            ->lockForUpdate()
            ->findOrFail($transaction->id);

        if (
            $transaction->transaction_type
            === ShareTransaction::TYPE_TRANSFER
        ) {
            throw new RuntimeException(
                'Share transfers cannot be edited from Share Transactions.'
            );
        }

        if ($transaction->status !== 'active') {
            throw new RuntimeException(
                'Only active share transactions can be edited.'
            );
        }

        $entries = LedgerEntry::query()
            ->where(
                'transaction_type',
                'share_transaction'
            )
            ->where(
                'transaction_id',
                $transaction->id
            )
            ->where(
                'is_reversal',
                false
            )
            ->lockForUpdate()
            ->get();

        if ($entries->isEmpty()) {
            throw new RuntimeException(
                'Original share ledger entry was not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Transaction Metadata Only
        |--------------------------------------------------------------------------
        |
        | Amount, Account, Kitta, Shareholder and Type are NOT changed.
        |
        */

        $transaction->date_ad =
            $data['date_ad'];

        $transaction->date_bs =
            $data['date_bs'];

        $transaction->financial_year =
            $data['financial_year'];

        $transaction->note =
            $data['note'] ?? null;

        $transaction->save();

        /*
        |--------------------------------------------------------------------------
        | Keep Ledger Metadata Synchronized
        |--------------------------------------------------------------------------
        |
        | Balance, amount and direction remain unchanged.
        |
        */

        foreach ($entries as $entry) {
            $entry->date_ad =
                $data['date_ad'];

            $entry->date_bs =
                $data['date_bs'];

            $entry->financial_year =
                $data['financial_year'];

            $entry->note =
                $data['note'] ?? null;

            $entry->save();
        }

        return $transaction->fresh([
            'shareholder',
            'account',
        ]);
    });
}


    public function cancel(
        ShareTransaction $transaction,
        int $userId,
        string $reason
    ): ShareTransaction {
        return DB::transaction(function () use (
            $transaction,
            $userId,
            $reason
        ) {
            $transaction = ShareTransaction::query()
                ->lockForUpdate()
                ->findOrFail($transaction->id);

            if ($transaction->transaction_type === ShareTransaction::TYPE_TRANSFER) {
                throw new RuntimeException(
                    'Share transfers cannot be cancelled. Contact the administrator if a correction is required.'
                );
            }

            if ($transaction->status === 'cancelled') {
                throw new RuntimeException(
                    'This share transaction is already cancelled.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            $shareholder = Shareholder::query()
                ->lockForUpdate()
                ->findOrFail($transaction->shareholder_id);

            /*
             * पहिले financial ledger reverse गर्ने।
             */
            $entries = LedgerEntry::query()
                ->where('transaction_type', 'share_transaction')
                ->where('transaction_id', $transaction->id)
                ->where('is_reversal', false)
                ->orderBy('id')
                ->get();

            if ($entries->isEmpty()) {
                throw new RuntimeException(
                    'Original share ledger entry was not found.'
                );
            }

            foreach ($entries as $entry) {
                $this->ledgerService->reverse(
                    $entry,
                    $userId,
                    $reason
                );
            }

            /*
             * BUY cancel:
             * Kitta -
             * Investment -
             */
            if (
                $transaction->transaction_type
                === ShareTransaction::TYPE_BUY
            ) {
                if (
                    (int) $shareholder->kitta
                    < (int) $transaction->kitta
                ) {
                    throw new RuntimeException(
                        'Buy transaction cannot be cancelled because current Kitta is too low.'
                    );
                }

                $shareholder->kitta =
                    (int) $shareholder->kitta
                    - (int) $transaction->kitta;

                $shareholder->total_investment =
                    (int) $shareholder->total_investment
                    - (int) $transaction->total_amount;

                if ($shareholder->total_investment < 0) {
                    throw new RuntimeException(
                        'Shareholder investment cannot become negative.'
                    );
                }
            }

            /*
             * WITHDRAW cancel:
             * Kitta +
             * Investment +
             */
            if (
                $transaction->transaction_type
                === ShareTransaction::TYPE_WITHDRAW
            ) {
                $shareholder->kitta =
                    (int) $shareholder->kitta
                    + (int) $transaction->kitta;

                $shareholder->total_investment =
                    (int) $shareholder->total_investment
                    + (int) $transaction->total_amount;
            }

            $shareholder->updated_by = $userId;
            $shareholder->save();

            $transaction->update([
                'status' => 'cancelled',
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $transaction->fresh([
                'shareholder',
                'account',
            ]);
        });
    }

}
