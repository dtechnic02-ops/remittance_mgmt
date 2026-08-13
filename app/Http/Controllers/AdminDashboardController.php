<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\Borrowing;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RemittanceTransaction;
use App\Models\Shareholder;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $financialYear = $this->currentFinancialYear();

        $accountBalances = Account::query()
            ->where('is_active', true)
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
            'income' => $income,
            'commission' => $commission,
            'expense' => $expense,
            'profit_loss' => $income + $commission - $expense,
            'borrowing_outstanding' => (int) $borrowing,
        ];

        return view('admin.dashboard', compact('summary', 'financialYear'));
    }

    private function currentFinancialYear(): string
    {
        $dateBs = LaravelNepaliDate::from(now()->toDateString())->toNepaliDate();
        [$year, $month] = array_map('intval', explode('-', $dateBs));
        $startYear = $month >= 4 ? $year : $year - 1;

        return $startYear.'/'.substr((string) ($startYear + 1), -2);
    }
}
