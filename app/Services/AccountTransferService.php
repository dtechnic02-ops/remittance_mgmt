<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountTransfer;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountTransferService
{
    public function __construct(
        private readonly FinancialLedgerService $ledgerService
    ) {
    }

    public function create(array $data): AccountTransfer
    {
        return DB::transaction(function () use ($data) {
            $fromAccount = Account::query()
                ->whereKey($data['from_account_id'])
                ->where('is_active', true)
                ->firstOrFail();

            $toAccount = Account::query()
                ->whereKey($data['to_account_id'])
                ->where('is_active', true)
                ->firstOrFail();

            if ($fromAccount->id === $toAccount->id) {
                throw new RuntimeException(
                    'From Account and To Account cannot be the same.'
                );
            }

            if (
                $fromAccount->type === Account::TYPE_FIXED_DEPOSIT
                || $toAccount->type === Account::TYPE_FIXED_DEPOSIT
            ) {
                throw new RuntimeException(
                    'Fixed Deposit account cannot be used in account transfer.'
                );
            }

            $amount = (int) $data['amount'];

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Transfer amount must be greater than zero.'
                );
            }

            $transferNumber = $this->nextTransferNumber();

            $transfer = AccountTransfer::create([
                'transfer_number' => $transferNumber,
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'date_ad' => $data['date_ad'],
                'date_bs' => $data['date_bs'],
                'financial_year' => $data['financial_year'],
                'amount' => $amount,
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
                'status' => 'active',
                'created_by' => $data['created_by'],
            ]);

            $commonLedgerData = [
                'transaction_type' => 'account_transfer',
                'transaction_id' => $transfer->id,
                'transaction_number' => $transfer->transfer_number,
                'date_ad' => $transfer->date_ad->format('Y-m-d'),
                'date_bs' => $transfer->date_bs,
                'financial_year' => $transfer->financial_year,
                'component' => 'transfer',
                'note' => $transfer->note,
                'created_by' => $transfer->created_by,
            ];

            $this->ledgerService->post([
                ...$commonLedgerData,
                'account_id' => $fromAccount->id,
                'direction' => 'decrease',
                'amount' => $amount,
            ]);

            $this->ledgerService->post([
                ...$commonLedgerData,
                'account_id' => $toAccount->id,
                'direction' => 'increase',
                'amount' => $amount,
            ]);

            return $transfer->fresh();
        });
    }

    public function cancel(
        AccountTransfer $transfer,
        int $userId,
        string $reason
    ): AccountTransfer {
        return DB::transaction(function () use (
            $transfer,
            $userId,
            $reason
        ) {
            $transfer = AccountTransfer::query()
                ->lockForUpdate()
                ->findOrFail($transfer->id);

            if ($transfer->status === 'cancelled') {
                throw new RuntimeException(
                    'This account transfer is already cancelled.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            $entries = LedgerEntry::query()
                ->where('transaction_type', 'account_transfer')
                ->where('transaction_id', $transfer->id)
                ->where('is_reversal', false)
                ->orderBy('id')
                ->get();

            if ($entries->isEmpty()) {
                throw new RuntimeException(
                    'Original transfer ledger entries were not found.'
                );
            }

            foreach ($entries as $entry) {
                $this->ledgerService->reverse(
                    $entry,
                    $userId,
                    $reason
                );
            }

            $transfer->update([
                'status' => 'cancelled',
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $transfer->fresh();
        });
    }

    private function nextTransferNumber(): string
    {
        $lastId = (int) AccountTransfer::query()
            ->lockForUpdate()
            ->max('id');

        return 'TRF-'.str_pad(
            (string) ($lastId + 1),
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}