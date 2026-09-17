<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\RemittanceTransaction;
use Illuminate\Database\Eloquent\Builder;

class ProfitLedgerService
{
    /**
     * @return array{
     *     income: int,
     *     remittance_charge: int,
     *     expense: int,
     *     profit: int
     * }
     */
    public function summarize(?string $dateFrom, ?string $dateTo): array
    {
        $income = $this->sumActive(Income::query(), 'amount', $dateFrom, $dateTo);
        $remittanceCharge = $this->sumActive(
            RemittanceTransaction::query(),
            'service_charge',
            $dateFrom,
            $dateTo
        );
        $expense = $this->sumActive(Expense::query(), 'amount', $dateFrom, $dateTo);

        return [
            'income' => $income,
            'remittance_charge' => $remittanceCharge,
            'expense' => $expense,
            'profit' => $income + $remittanceCharge - $expense,
        ];
    }

    private function sumActive(
        Builder $query,
        string $column,
        ?string $dateFrom,
        ?string $dateTo
    ): int {
        $query->where('status', 'active');

        if ($dateFrom !== null && $dateFrom !== '') {
            $query->whereDate('date_ad', '>=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $query->whereDate('date_ad', '<=', $dateTo);
        }

        return (int) $query->sum($column);
    }
}
