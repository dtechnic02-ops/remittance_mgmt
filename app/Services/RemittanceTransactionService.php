<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LedgerEntry;
use App\Models\RemittanceTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RemittanceTransactionService
{
    public function __construct(
        private readonly FinancialLedgerService $ledgerService
    ) {
    }

    public function create(array $data): RemittanceTransaction
    {
        return DB::transaction(function () use ($data) {
            $cashAccount = Account::query()
                ->whereKey($data['cash_account_id'])
                ->where('type', Account::TYPE_CASH)
                ->where('is_active', true)
                ->firstOrFail();

            $providerAccount = Account::query()
                ->whereKey($data['provider_account_id'])
                ->where('type', Account::TYPE_REMITTANCE)
                ->where('is_active', true)
                ->firstOrFail();

            if ($cashAccount->id === $providerAccount->id) {
                throw new RuntimeException(
                    'Cash Account and Provider Account cannot be the same.'
                );
            }

            $direction = strtolower(
                trim((string) ($data['direction'] ?? 'send'))
            );

            if (! in_array($direction, ['send', 'receive'], true)) {
                throw new RuntimeException(
                    'Invalid remittance direction.'
                );
            }

            $principal = (int) $data['principal_amount'];
            $serviceCharge = (int) ($data['service_charge'] ?? 0);

            if ($principal <= 0) {
                throw new RuntimeException(
                    'Principal amount must be greater than zero.'
                );
            }

            if ($serviceCharge < 0) {
                throw new RuntimeException(
                    'Service Charge cannot be negative.'
                );
            }

            if (
                $direction === 'receive'
                && $serviceCharge > $principal
            ) {
                throw new RuntimeException(
                    'Service Charge cannot be greater than the received amount.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Customer Cash
            |--------------------------------------------------------------------------
            |
            | SEND:
            | Principal 5,000 + Charge 50 = Customer pays 5,050
            |
            | RECEIVE:
            | Principal 5,000 - Charge 50 = Customer receives 4,950
            |
            */

            $customerCash = $direction === 'send'
                ? $principal + $serviceCharge
                : $principal - $serviceCharge;

            $transaction = RemittanceTransaction::create([
                'transaction_number' => TransactionNumberService::temporary(),
                'direction' => $direction,
                'customer_id' => $data['customer_id'],
                'provider_account_id' => $providerAccount->id,
                'cash_account_id' => $cashAccount->id,
                'date_ad' => $data['date_ad'],
                'date_bs' => $data['date_bs'],
                'financial_year' => $data['financial_year'],
                'principal_amount' => $principal,
                'service_charge' => $serviceCharge,

                // Existing column retained.
                // SEND = customer cash received
                // RECEIVE = customer cash paid
                'total_cash_received' => $customerCash,

                'provider_reference' => $data['provider_reference'] ?? null,
                'note' => $data['note'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'status' => 'active',
                'created_by' => $data['created_by'],
            ]);

            $transaction->transaction_number = TransactionNumberService::fromId('REM-', $transaction->id);
            $transaction->save();

            $commonLedgerData = [
                'transaction_type' => 'remittance',
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
                'date_ad' => $transaction->date_ad->format('Y-m-d'),
                'date_bs' => $transaction->date_bs,
                'financial_year' => $transaction->financial_year,
                'note' => $transaction->note,
                'created_by' => $transaction->created_by,
            ];

            if ($direction === 'send') {
                /*
                |--------------------------------------------------------------------------
                | SEND
                |--------------------------------------------------------------------------
                |
                | Cash     + Principal
                | Cash     + Service Charge
                | Provider - Principal
                |
                */

                $this->ledgerService->post([
                    ...$commonLedgerData,
                    'account_id' => $cashAccount->id,
                    'direction' => 'increase',
                    'amount' => $principal,
                    'component' => 'principal',
                ]);

                if ($serviceCharge > 0) {
                    $this->ledgerService->post([
                        ...$commonLedgerData,
                        'account_id' => $cashAccount->id,
                        'direction' => 'increase',
                        'amount' => $serviceCharge,
                        'component' => 'service_charge',
                    ]);
                }

                $this->ledgerService->post([
                    ...$commonLedgerData,
                    'account_id' => $providerAccount->id,
                    'direction' => 'decrease',
                    'amount' => $principal,
                    'component' => 'principal',
                ]);
                       } else {
                /*
                |--------------------------------------------------------------------------
                | RECEIVE
                |--------------------------------------------------------------------------
                |
                | Provider + Principal
                | Cash     - (Principal - Service Charge)
                |
                | Net Cash decrease:
                | Principal - Service Charge
                |
                */

                $this->ledgerService->post([
                    ...$commonLedgerData,
                    'account_id' => $providerAccount->id,
                    'direction' => 'increase',
                    'amount' => $principal,
                    'component' => 'principal',
                ]);

                if ($customerCash > 0) {
                    $this->ledgerService->post([
                        ...$commonLedgerData,
                        'account_id' => $cashAccount->id,
                        'direction' => 'decrease',
                        'amount' => $customerCash,
                        'component' => 'principal',
                    ]);
                }
            }
            return $transaction->fresh();
        });
    }

    public function cancel(

        RemittanceTransaction $transaction,
        int $userId,
        string $reason
    ): RemittanceTransaction {
        return DB::transaction(function () use (
            $transaction,
            $userId,
            $reason
        ) {
            $transaction = RemittanceTransaction::query()
                ->lockForUpdate()
                ->findOrFail($transaction->id);

            if ($transaction->status === 'cancelled') {
                throw new RuntimeException(
                    'This remittance transaction is already cancelled.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new RuntimeException(
                    'Cancellation reason is required.'
                );
            }

            $entries = LedgerEntry::query()
                ->where('transaction_type', 'remittance')
                ->where('transaction_id', $transaction->id)
                ->where('is_reversal', false)
                ->orderBy('id')
                ->get();

            if ($entries->isEmpty()) {
                throw new RuntimeException(
                    'Original ledger entries were not found.'
                );
            }

            foreach ($entries as $entry) {
                $this->ledgerService->reverse(
                    $entry,
                    $userId,
                    $reason
                );
            }

            $transaction->update([
                'status' => 'cancelled',
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $transaction->fresh();
        });
    }

}
