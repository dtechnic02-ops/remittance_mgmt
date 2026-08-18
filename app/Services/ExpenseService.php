<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ExpenseService
{
    public function __construct(
        private readonly FinancialLedgerService $ledgerService
    ) {
    }

    public function create(array $data): Expense
    {
        return DB::transaction(function () use ($data) {

            $category = ExpenseCategory::query()
                ->whereKey($data['expense_category_id'])
                ->where('is_active', true)
                ->firstOrFail();

            $account = Account::query()
                ->whereKey($data['account_id'])
                ->where('is_active', true)
                ->firstOrFail();

            /*
             * Fixed Deposit is storage-only.
             * It cannot be used to pay expenses.
             */
            if ($account->type === Account::TYPE_FIXED_DEPOSIT) {
                throw new RuntimeException(
                    'Fixed Deposit account cannot be used for expense payment.'
                );
            }

            $amount = (int) $data['amount'];

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Expense amount must be greater than zero.'
                );
            }

            $expense = Expense::create([
                'expense_number' => TransactionNumberService::temporary(),
                'expense_category_id' => $category->id,
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

            $expense->expense_number = TransactionNumberService::fromId('EXP-', $expense->id);
            $expense->save();

            $this->ledgerService->post([
                'transaction_type' => 'expense',
                'transaction_id' => $expense->id,
                'transaction_number' => $expense->expense_number,
                'account_id' => $account->id,
                'date_ad' => $expense->date_ad->format('Y-m-d'),
                'date_bs' => $expense->date_bs,
                'financial_year' => $expense->financial_year,
                'direction' => 'decrease',
                'amount' => $amount,
                'component' => 'expense',
                'note' => $expense->note,
                'created_by' => $expense->created_by,
            ]);

            return $expense->fresh();
        });
    }

    public function cancel(
        Expense $expense,
        int $userId,
        string $reason
    ): Expense {
        return DB::transaction(function () use (
            $expense,
            $userId,
            $reason
        ) {
            $expense = Expense::query()
                ->lockForUpdate()
                ->findOrFail($expense->id);

            if ($expense->status === 'cancelled') {
                throw new RuntimeException(
                    'This expense is already cancelled.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            $entries = LedgerEntry::query()
                ->where('transaction_type', 'expense')
                ->where('transaction_id', $expense->id)
                ->where('is_reversal', false)
                ->orderBy('id')
                ->get();

            if ($entries->isEmpty()) {
                throw new RuntimeException(
                    'Original expense ledger entry was not found.'
                );
            }

            foreach ($entries as $entry) {
                $this->ledgerService->reverse(
                    $entry,
                    $userId,
                    $reason
                );
            }

            $expense->update([
                'status' => 'cancelled',
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $expense->fresh();
        });
    }

}
