<?php

namespace App\Services;

use App\Models\Shareholder;
use App\Models\ShareTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShareTransferService
{
    public function create(array $data): ShareTransaction
    {
        return DB::transaction(function () use ($data) {
            $fromId = (int) $data['shareholder_id'];
            $toId = (int) $data['to_shareholder_id'];

            if ($fromId === $toId) {
                throw new RuntimeException(
                    'Shareholder cannot transfer share to the same shareholder.'
                );
            }

            $shareholders = Shareholder::query()
                ->whereIn('id', [$fromId, $toId])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $fromShareholder = $shareholders->get($fromId);
            $toShareholder = $shareholders->get($toId);

            if (
                ! $fromShareholder?->is_active
                || ! $toShareholder?->is_active
            ) {
                throw new RuntimeException(
                    'Both shareholders must be active.'
                );
            }

            $kitta = (int) $data['kitta'];

            if ($kitta <= 0) {
                throw new RuntimeException(
                    'Kitta must be greater than zero.'
                );
            }

            if ((int) $fromShareholder->kitta < $kitta) {
                throw new RuntimeException(
                    'Shareholder does not have enough Kitta to transfer.'
                );
            }

            $perKittaValue =
                $this->decimalValue($data['per_kitta_value'] ?? 0);

            if ((float) $perKittaValue <= 0) {
                throw new RuntimeException(
                    'Invalid per Kitta value.'
                );
            }

            $totalAmount =
                $this->multiplyByKitta($perKittaValue, $kitta);

            if (
                (int) $fromShareholder->total_investment
                < $totalAmount
            ) {
                throw new RuntimeException(
                    'Shareholder investment cannot become negative.'
                );
            }

            $transaction = ShareTransaction::create([
                'transaction_number' =>
                    TransactionNumberService::temporary(),

                'transaction_type' =>
                    ShareTransaction::TYPE_TRANSFER,

                'shareholder_id' =>
                    $fromShareholder->id,

                'to_shareholder_id' =>
                    $toShareholder->id,

                'account_id' => null,

                'date_ad' =>
                    $data['date_ad'],

                'date_bs' =>
                    $data['date_bs'],

                'financial_year' =>
                    $data['financial_year'],

                'kitta' =>
                    $kitta,

                'per_kitta_value' =>
                    $perKittaValue,

                'total_amount' =>
                    $totalAmount,

                'reference' =>
                    $data['reference'] ?? null,

                'attachment' =>
                    $data['attachment'] ?? null,

                'note' =>
                    $data['note'] ?? null,

                'status' =>
                    'active',

                'created_by' =>
                    $data['created_by'],
            ]);

            $transaction->transaction_number =
                TransactionNumberService::fromId(
                    'SHR-',
                    $transaction->id
                );

            $transaction->save();

            /*
            |--------------------------------------------------------------------------
            | FROM SHAREHOLDER
            |--------------------------------------------------------------------------
            */

            $fromShareholder->kitta =
                (int) $fromShareholder->kitta
                - $kitta;

            if ($fromShareholder->kitta < 0) {
                throw new RuntimeException(
                    'Shareholder Kitta cannot become negative.'
                );
            }

            $fromShareholder->total_investment =
                (int) $fromShareholder->total_investment
                - $totalAmount;

            if ($fromShareholder->total_investment < 0) {
                throw new RuntimeException(
                    'Shareholder investment cannot become negative.'
                );
            }

            $fromShareholder->updated_by =
                $data['created_by'];

            $fromShareholder->save();

            /*
            |--------------------------------------------------------------------------
            | TO SHAREHOLDER
            |--------------------------------------------------------------------------
            */

            $toShareholder->kitta =
                (int) $toShareholder->kitta
                + $kitta;

            $toShareholder->total_investment =
                (int) $toShareholder->total_investment
                + $totalAmount;

            $toShareholder->updated_by =
                $data['created_by'];

            $toShareholder->save();

            return $transaction->fresh([
                'shareholder',
                'toShareholder',
                'creator',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TRANSFER METADATA
    |--------------------------------------------------------------------------
    |
    | Date + Note मात्र परिवर्तन हुन्छ।
    |
    */

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
                !== ShareTransaction::TYPE_TRANSFER
            ) {
                throw new RuntimeException(
                    'This transaction is not a share transfer.'
                );
            }

            if ($transaction->status !== 'active') {
                throw new RuntimeException(
                    'Only active share transfers can be edited.'
                );
            }

            $transaction->date_ad =
                $data['date_ad'];

            $transaction->date_bs =
                $data['date_bs'];

            $transaction->financial_year =
                $data['financial_year'];

            $transaction->note =
                $data['note'] ?? null;

            $transaction->save();

            return $transaction->fresh([
                'shareholder',
                'toShareholder',
                'creator',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL SHARE TRANSFER
    |--------------------------------------------------------------------------
    |
    | Original:
    | FROM shareholder  - Kitta
    | TO shareholder    + Kitta
    |
    | Cancellation:
    | FROM shareholder  + Kitta
    | TO shareholder    - Kitta
    |
    | Cash / Bank / Financial Ledger मा कुनै असर हुँदैन।
    |
    */

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

            if (
                $transaction->transaction_type
                !== ShareTransaction::TYPE_TRANSFER
            ) {
                throw new RuntimeException(
                    'This transaction is not a share transfer.'
                );
            }

            if ($transaction->status === 'cancelled') {
                throw new RuntimeException(
                    'This share transfer is already cancelled.'
                );
            }

            if ($transaction->status !== 'active') {
                throw new RuntimeException(
                    'Only active share transfers can be cancelled.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            $fromId =
                (int) $transaction->shareholder_id;

            $toId =
                (int) $transaction->to_shareholder_id;

            if (
                $fromId <= 0
                || $toId <= 0
                || $fromId === $toId
            ) {
                throw new RuntimeException(
                    'Invalid share transfer shareholders.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Lock both shareholders in consistent order
            |--------------------------------------------------------------------------
            */

            $shareholders = Shareholder::query()
                ->whereIn(
                    'id',
                    [$fromId, $toId]
                )
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $fromShareholder =
                $shareholders->get($fromId);

            $toShareholder =
                $shareholders->get($toId);

            if (
                ! $fromShareholder
                || ! $toShareholder
            ) {
                throw new RuntimeException(
                    'Shareholder record was not found.'
                );
            }

            $kitta =
                (int) $transaction->kitta;

            $totalAmount =
                (float) $transaction->total_amount;

            if ($kitta <= 0) {
                throw new RuntimeException(
                    'Invalid transfer Kitta.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Negative Kitta Protection
            |--------------------------------------------------------------------------
            |
            | TO shareholder बाट Kitta फिर्ता लिनुपर्छ।
            | अहिले उसको Kitta transfer quantity भन्दा कम भए Cancel हुँदैन।
            |
            */

            if (
                (int) $toShareholder->kitta
                < $kitta
            ) {
                throw new RuntimeException(
                    'Share transfer cannot be cancelled because the receiving shareholder does not have enough Kitta to return.'
                );
            }

            if (
                (int) $toShareholder->total_investment
                < $totalAmount
            ) {
                throw new RuntimeException(
                    'Share transfer cannot be cancelled because the receiving shareholder investment is too low.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | RETURN KITTA TO ORIGINAL FROM SHAREHOLDER
            |--------------------------------------------------------------------------
            */

            $fromShareholder->kitta =
                (int) $fromShareholder->kitta
                + $kitta;

            $fromShareholder->total_investment =
                (int) $fromShareholder->total_investment
                + $totalAmount;

            if (
                $fromShareholder->total_investment
                < 0
            ) {
                throw new RuntimeException(
                    'Shareholder investment cannot become negative.'
                );
            }

            $fromShareholder->updated_by =
                $userId;

            $fromShareholder->save();

            /*
            |--------------------------------------------------------------------------
            | REMOVE KITTA FROM ORIGINAL TO SHAREHOLDER
            |--------------------------------------------------------------------------
            */

            $toShareholder->kitta =
                (int) $toShareholder->kitta
                - $kitta;

            if ($toShareholder->kitta < 0) {
                throw new RuntimeException(
                    'Shareholder Kitta cannot become negative.'
                );
            }

            $toShareholder->total_investment =
                (int) $toShareholder->total_investment
                - $totalAmount;

            if (
                $toShareholder->total_investment
                < 0
            ) {
                throw new RuntimeException(
                    'Shareholder investment cannot become negative.'
                );
            }

            $toShareholder->updated_by =
                $userId;

            $toShareholder->save();

            /*
            |--------------------------------------------------------------------------
            | Mark Transaction Cancelled
            |--------------------------------------------------------------------------
            */

            $transaction->status =
                'cancelled';

            $transaction->cancelled_by =
                $userId;

            $transaction->cancelled_at =
                now();

            $transaction->cancellation_reason =
                $reason;

            $transaction->save();

            return $transaction->fresh([
                'shareholder',
                'toShareholder',
                'creator',
                'canceller',
            ]);
        });
    }

    private function decimalValue(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function multiplyByKitta(string $perKittaValue, int $kitta): string
    {
        $perKittaCents = (int) round(((float) $perKittaValue) * 100);

        return number_format(($perKittaCents * $kitta) / 100, 2, '.', '');
    }
}
