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

            if (! $fromShareholder?->is_active || ! $toShareholder?->is_active) {
                throw new RuntimeException('Both shareholders must be active.');
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

            $perKittaValue = (int) $fromShareholder->per_kitta_value;

            if ($perKittaValue <= 0) {
                throw new RuntimeException(
                    'Invalid per Kitta value.'
                );
            }

            if (
                (int) $toShareholder->per_kitta_value
                !== $perKittaValue
            ) {
                throw new RuntimeException(
                    'Both shareholders must have the same per Kitta value.'
                );
            }

            $totalAmount = $kitta * $perKittaValue;

            $transaction = ShareTransaction::create([
                'transaction_number' => TransactionNumberService::temporary(),
                'transaction_type' => ShareTransaction::TYPE_TRANSFER,
                'shareholder_id' => $fromShareholder->id,
                'to_shareholder_id' => $toShareholder->id,
                'account_id' => null,
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

            $fromShareholder->kitta =
                (int) $fromShareholder->kitta - $kitta;

            if ($fromShareholder->kitta < 0) {
                throw new RuntimeException(
                    'Shareholder Kitta cannot become negative.'
                );
            }

            $fromShareholder->total_investment =
                (int) $fromShareholder->kitta
                * (int) $fromShareholder->per_kitta_value;

            $fromShareholder->updated_by = $data['created_by'];
            $fromShareholder->save();

            $toShareholder->kitta =
                (int) $toShareholder->kitta + $kitta;

            $toShareholder->total_investment =
                (int) $toShareholder->kitta
                * (int) $toShareholder->per_kitta_value;

            $toShareholder->updated_by = $data['created_by'];
            $toShareholder->save();

            return $transaction->fresh([
                'shareholder',
                'toShareholder',
                'creator',
            ]);
        });
    }

}
