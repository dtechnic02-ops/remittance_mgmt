<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Income;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IncomeService
{
    public function __construct(
        private readonly FinancialLedgerService $ledgerService
    ) {
    }

    public function create(array $data): Income
    {
        return DB::transaction(function () use ($data) {
            $account = Account::query()
                ->whereKey($data['account_id'])
                ->where('is_active', true)
                ->firstOrFail();

            if ($account->type === Account::TYPE_FIXED_DEPOSIT) {
                throw new RuntimeException(
                    'Fixed Deposit account cannot be used for income transactions.'
                );
            }

            $amount = (int) $data['amount'];

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Income amount must be greater than zero.'
                );
            }

            $income = Income::create([
                'income_number' => TransactionNumberService::temporary(),
                'income_category_id' => $data['income_category_id'],
                'account_id' => $account->id,
                'date_ad' => $data['date_ad'],
                'date_bs' => $data['date_bs'],
                'financial_year' => $data['financial_year'],
                'amount' => $amount,
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'status' => 'active',
                'created_by' => $data['created_by'],
            ]);

            $income->income_number = TransactionNumberService::fromId('INC-', $income->id);
            $income->save();

            $this->ledgerService->post([
                'account_id' => $account->id,
                'transaction_type' => 'income',
                'transaction_id' => $income->id,
                'transaction_number' => $income->income_number,
                'date_ad' => $income->date_ad->format('Y-m-d'),
                'date_bs' => $income->date_bs,
                'financial_year' => $income->financial_year,
                'direction' => 'increase',
                'amount' => $amount,
                'component' => 'income',
                'note' => $income->note,
                'created_by' => $income->created_by,
            ]);

            return $income->fresh();
        });
    }

    public function cancel(
        Income $income,
        int $userId,
        string $reason
    ): Income {
        return DB::transaction(function () use (
            $income,
            $userId,
            $reason
        ) {
            $income = Income::query()
                ->lockForUpdate()
                ->findOrFail($income->id);

            if ($income->status === 'cancelled') {
                throw new RuntimeException(
                    'This income transaction is already cancelled.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            $entries = LedgerEntry::query()
                ->where('transaction_type', 'income')
                ->where('transaction_id', $income->id)
                ->where('is_reversal', false)
                ->orderBy('id')
                ->get();

            if ($entries->isEmpty()) {
                throw new RuntimeException(
                    'Original income ledger entry was not found.'
                );
            }

            foreach ($entries as $entry) {
                $this->ledgerService->reverse(
                    $entry,
                    $userId,
                    $reason
                );
            }

            $income->update([
                'status' => 'cancelled',
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $income->fresh();
        });
    }

    public function updateMetadata(Income $income, array $data): Income
    {
        return DB::transaction(function () use ($income, $data) {
            $income = Income::query()
                ->lockForUpdate()
                ->findOrFail($income->id);

            if ($income->status !== 'active') {
                throw new RuntimeException(
                    'Only active income transactions can be edited.'
                );
            }

            $entries = LedgerEntry::query()
                ->where('transaction_type', 'income')
                ->where('transaction_id', $income->id)
                ->where('is_reversal', false)
                ->lockForUpdate()
                ->get();

            if ($entries->isEmpty()) {
                throw new RuntimeException(
                    'Original income ledger entry was not found.'
                );
            }

            $income->date_ad = $data['date_ad'];
            $income->date_bs = $data['date_bs'];
            $income->financial_year = $data['financial_year'];
            $income->note = $data['note'] ?? null;
            $income->save();

            foreach ($entries as $entry) {
                $entry->date_ad = $data['date_ad'];
                $entry->date_bs = $data['date_bs'];
                $entry->financial_year = $data['financial_year'];
                $entry->note = $data['note'] ?? null;
                $entry->save();
            }

            return $income->fresh([
                'category',
                'account',
                'creator',
            ]);
        });
    }

}
