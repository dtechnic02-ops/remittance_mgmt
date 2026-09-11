<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\Borrowing;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RemittanceTransaction;
use App\Models\Shareholder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    private const MONTHS = [
        4 => 'Shrawan',
        5 => 'Bhadra',
        6 => 'Ashwin',
        7 => 'Kartik',
        8 => 'Mangsir',
        9 => 'Poush',
        10 => 'Magh',
        11 => 'Falgun',
        12 => 'Chaitra',
        1 => 'Baisakh',
        2 => 'Jestha',
        3 => 'Asar',
    ];

    public function __invoke(): View
    {
        $financialYear = $this->currentFinancialYear();

        $accountBalances = Account::query()
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->whereIn('type', [
                Account::TYPE_CASH,
                Account::TYPE_BANK,
                Account::TYPE_REMITTANCE,
            ])
            ->selectRaw('type, COALESCE(SUM(current_balance), 0) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $cash = (int) ($accountBalances[Account::TYPE_CASH] ?? 0);
        $bank = (int) ($accountBalances[Account::TYPE_BANK] ?? 0);
        $income = (int) Income::query()
            ->where('status', 'active')
            ->where('financial_year', $financialYear)
            ->sum('amount');
        $commission = (int) RemittanceTransaction::query()
            ->where('status', 'active')
            ->where('financial_year', $financialYear)
            ->sum('service_charge');
        $expense = (int) Expense::query()
            ->where('status', 'active')
            ->where('financial_year', $financialYear)
            ->sum('amount');
        $overallIncome = (int) Income::query()->where('status', 'active')->sum('amount');
        $overallCommission = (int) RemittanceTransaction::query()->where('status', 'active')->sum('service_charge');
        $overallExpense = (int) Expense::query()->where('status', 'active')->sum('amount');

        $borrowing = Borrowing::query()
            ->where('status', 'active')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN transaction_type = ? THEN amount WHEN transaction_type = ? THEN -amount ELSE 0 END), 0) as outstanding',
                [Borrowing::TYPE_BORROW, Borrowing::TYPE_REPAY]
            )
            ->value('outstanding');

        $summary = [
            'share_capital' => (int) Shareholder::query()->sum('total_investment'),
            'cash' => $cash,
            'bank' => $bank,
            'available' => $cash + $bank,
            'remittance' => (int) ($accountBalances[Account::TYPE_REMITTANCE] ?? 0),
            'net_amount' =>
    ($cash + $bank)
    + (int) ($accountBalances[Account::TYPE_REMITTANCE] ?? 0),
            'income' => $income,
            'commission' => $commission,
            'expense' => $expense,
            'profit_loss' => $income + $commission - $expense,
            'overall_income' => $overallIncome,
            'overall_commission' => $overallCommission,
            'overall_profit_loss' => $overallIncome + $overallCommission - $overallExpense,
            'borrowing_outstanding' => (int) $borrowing,
        ];

        $monthly = $this->monthlyPerformance($financialYear);
        $chartMax = max(1, ...collect($monthly)->flatMap(
            fn (array $month) => [
                abs($month['income']),
                abs($month['commission']),
                abs($month['expense']),
                abs($month['profit_loss']),
            ]
        )->all());

        return view('staff.dashboard', compact(
            'summary',
            'financialYear',
            'monthly',
            'chartMax',
            'overallExpense'
        ));
    }

    private function monthlyPerformance(string $financialYear): array
    {
        $income = $this->monthlyTotals(Income::query(), 'amount', $financialYear);
        $commission = $this->monthlyTotals(RemittanceTransaction::query(), 'service_charge', $financialYear);
        $expense = $this->monthlyTotals(Expense::query(), 'amount', $financialYear);

        return collect(self::MONTHS)->map(function (string $label, int $month) use ($income, $commission, $expense): array {
            $monthIncome = (int) ($income[$month] ?? 0);
            $monthCommission = (int) ($commission[$month] ?? 0);
            $monthExpense = (int) ($expense[$month] ?? 0);

            return [
                'month' => $month,
                'label' => $label,
                'income' => $monthIncome,
                'commission' => $monthCommission,
                'expense' => $monthExpense,
                'profit_loss' => $monthIncome + $monthCommission - $monthExpense,
            ];
        })->values()->all();
    }

    private function monthlyTotals(Builder $query, string $column, string $financialYear): array
    {
        $totals = [];

        foreach ($query
            ->where('status', 'active')
            ->where('financial_year', $financialYear)
            ->select(['date_bs', $column])
            ->cursor() as $record) {
            $month = (int) substr((string) $record->date_bs, 5, 2);

            if ($month < 1 || $month > 12) {
                continue;
            }

            $totals[$month] = ($totals[$month] ?? 0) + (int) $record->{$column};
        }

        return $totals;
    }

    private function currentFinancialYear(): string
    {
        $dateBs = LaravelNepaliDate::from(now()->toDateString())->toNepaliDate();
        [$year, $month] = array_map('intval', explode('-', $dateBs));
        $startYear = $month >= 4 ? $year : $year - 1;

        return $startYear.'/'.substr((string) ($startYear + 1), -2);
    }
}
