<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Borrowing;
use App\Models\LedgerEntry;
use App\Models\Lender;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BorrowingService
{
    public function __construct(
        private readonly FinancialLedgerService $ledgerService
    ) {
    }

    public function create(array $data): Borrowing
    {
        return DB::transaction(function () use ($data) {

            $lender = Lender::query()
                ->lockForUpdate()
                ->whereKey($data['lender_id'])
                ->where('is_active', true)
                ->firstOrFail();

            $account = Account::query()
                ->whereKey($data['account_id'])
                ->where('is_active', true)
                ->firstOrFail();

            if ($account->type === Account::TYPE_FIXED_DEPOSIT) {
                throw new RuntimeException(
                    'Fixed Deposit account cannot be used for borrowing transactions.'
                );
            }

            $transactionType =
                (string) $data['transaction_type'];

            if (! in_array(
                $transactionType,
                [
                    Borrowing::TYPE_BORROW,
                    Borrowing::TYPE_REPAY,
                ],
                true
            )) {
                throw new RuntimeException(
                    'Invalid borrowing transaction type.'
                );
            }

            $amount = (int) $data['amount'];

            if ($amount <= 0) {
                throw new RuntimeException(
                    'Amount must be greater than zero.'
                );
            }

            /*
             * Current outstanding:
             *
             * Active BORROW
             * minus
             * Active REPAY
             */
            $outstanding = $this->outstanding(
                $lender->id
            );

            /*
             * REPAY cannot exceed outstanding.
             */
            if (
                $transactionType === Borrowing::TYPE_REPAY
                && $amount > $outstanding
            ) {
                throw new RuntimeException(
                    'Repayment amount cannot exceed lender outstanding balance.'
                );
            }

            $transaction = Borrowing::create([
                'transaction_number' =>
                    $this->nextTransactionNumber(),

                'transaction_type' =>
                    $transactionType,

                'lender_id' =>
                    $lender->id,

                'account_id' =>
                    $account->id,

                'date_ad' =>
                    $data['date_ad'],

                'date_bs' =>
                    $data['date_bs'],

                'financial_year' =>
                    $data['financial_year'],

                'amount' =>
                    $amount,

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

            /*
             * BORROW
             *
             * Money received:
             * Selected account +
             */
            if (
                $transactionType
                === Borrowing::TYPE_BORROW
            ) {
                $this->ledgerService->post([
                    'account_id' =>
                        $account->id,

                    'transaction_type' =>
                        'borrowing',

                    'transaction_id' =>
                        $transaction->id,

                    'transaction_number' =>
                        $transaction->transaction_number,

                    'date_ad' =>
                        $transaction->date_ad->format('Y-m-d'),

                    'date_bs' =>
                        $transaction->date_bs,

                    'financial_year' =>
                        $transaction->financial_year,

                    'direction' =>
                        'increase',

                    'amount' =>
                        $amount,

                    'component' =>
                        'borrow',

                    'note' =>
                        $transaction->note,

                    'created_by' =>
                        $transaction->created_by,
                ]);
            }

            /*
             * REPAY
             *
             * Money paid:
             * Selected account -
             *
             * FinancialLedgerService will also
             * enforce account balance rules.
             */
            if (
                $transactionType
                === Borrowing::TYPE_REPAY
            ) {
                $this->ledgerService->post([
                    'account_id' =>
                        $account->id,

                    'transaction_type' =>
                        'borrowing',

                    'transaction_id' =>
                        $transaction->id,

                    'transaction_number' =>
                        $transaction->transaction_number,

                    'date_ad' =>
                        $transaction->date_ad->format('Y-m-d'),

                    'date_bs' =>
                        $transaction->date_bs,

                    'financial_year' =>
                        $transaction->financial_year,

                    'direction' =>
                        'decrease',

                    'amount' =>
                        $amount,

                    'component' =>
                        'repay',

                    'note' =>
                        $transaction->note,

                    'created_by' =>
                        $transaction->created_by,
                ]);
            }

            return $transaction->fresh([
                'lender',
                'account',
            ]);
        });
    }

    public function cancel(
        Borrowing $borrowing,
        int $userId,
        string $reason
    ): Borrowing {
        return DB::transaction(function () use (
            $borrowing,
            $userId,
            $reason
        ) {

            $borrowing = Borrowing::query()
                ->lockForUpdate()
                ->findOrFail($borrowing->id);

            if ($borrowing->status === 'cancelled') {
                throw new RuntimeException(
                    'This borrowing transaction is already cancelled.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            /*
             * Lock lender so another borrow/repay
             * cannot change the same lender while
             * cancellation is being processed.
             */
            Lender::query()
                ->lockForUpdate()
                ->findOrFail($borrowing->lender_id);

            /*
             * Important:
             * Cancelling a BORROW reduces outstanding.
             *
             * It must not make lender outstanding
             * negative if later repayments already exist.
             */
            if (
                $borrowing->transaction_type
                === Borrowing::TYPE_BORROW
            ) {
                $currentOutstanding =
                    $this->outstanding(
                        $borrowing->lender_id
                    );

                if (
                    $currentOutstanding
                    < (int) $borrowing->amount
                ) {
                    throw new RuntimeException(
                        'Borrow transaction cannot be cancelled because part of this borrowing has already been repaid.'
                    );
                }
            }

            $entries = LedgerEntry::query()
                ->where(
                    'transaction_type',
                    'borrowing'
                )
                ->where(
                    'transaction_id',
                    $borrowing->id
                )
                ->where(
                    'is_reversal',
                    false
                )
                ->orderBy('id')
                ->get();

            if ($entries->isEmpty()) {
                throw new RuntimeException(
                    'Original borrowing ledger entry was not found.'
                );
            }

            foreach ($entries as $entry) {
                $this->ledgerService->reverse(
                    $entry,
                    $userId,
                    $reason
                );
            }

            $borrowing->update([
                'status' =>
                    'cancelled',

                'cancelled_by' =>
                    $userId,

                'cancelled_at' =>
                    now(),

                'cancellation_reason' =>
                    $reason,
            ]);

            return $borrowing->fresh([
                'lender',
                'account',
            ]);
        });
    }

    public function outstanding(int $lenderId): int
    {
        $borrowed = (int) Borrowing::query()
            ->where('lender_id', $lenderId)
            ->where('status', 'active')
            ->where(
                'transaction_type',
                Borrowing::TYPE_BORROW
            )
            ->sum('amount');

        $repaid = (int) Borrowing::query()
            ->where('lender_id', $lenderId)
            ->where('status', 'active')
            ->where(
                'transaction_type',
                Borrowing::TYPE_REPAY
            )
            ->sum('amount');

        return $borrowed - $repaid;
    }

    private function nextTransactionNumber(): string
    {
        $lastId = (int) Borrowing::query()
            ->lockForUpdate()
            ->max('id');

        return 'BOR-'.str_pad(
            (string) ($lastId + 1),
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}