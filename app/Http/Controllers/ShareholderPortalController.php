<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RemittanceTransaction;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use Illuminate\Http\Request;

class ShareholderPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $shareholder = $request->user()
            ->shareholder()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Total Company Kitta
        |--------------------------------------------------------------------------
        |
        | Profit/Loss allocation must use total issued/current Kitta.
        | Do not exclude an inactive shareholder who may still own shares.
        |
        */

        $totalKitta = (int) Shareholder::query()
            ->sum('kitta');

        $shareholdingPercentage = 0;

        if ($shareholder && $totalKitta > 0) {
            $shareholdingPercentage = round(
                ((int) $shareholder->kitta / $totalKitta) * 100,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Current Nepali Financial Year
        |--------------------------------------------------------------------------
        */

        $dateBs = LaravelNepaliDate::from(
            now()->toDateString()
        )->toNepaliDate();

        [$year, $month] = array_map(
            'intval',
            explode('-', $dateBs)
        );

        $startYear = $month >= 4
            ? $year
            : $year - 1;

        $financialYear =
            $startYear.'/'.substr(
                (string) ($startYear + 1),
                -2
            );

        /*
        |--------------------------------------------------------------------------
        | Company Net Profit / Loss
        |--------------------------------------------------------------------------
        |
        | Same formula as Admin Dashboard:
        | Income + Commission - Expense
        |
        */

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

        $companyProfitLoss =
            $income
            + $commission
            - $expense;

        /*
        |--------------------------------------------------------------------------
        | My Profit / Loss
        |--------------------------------------------------------------------------
        |
        | Company P/L × My Kitta ÷ Total Kitta
        |
        */

        $myProfitLoss = 0;

        if ($shareholder && $totalKitta > 0) {
            $myProfitLoss = round(
                $companyProfitLoss
                * (int) $shareholder->kitta
                / $totalKitta,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Share Transaction History
        |--------------------------------------------------------------------------
        */

        $transactions = $shareholder
            ? ShareTransaction::query()
                ->with([
                    'shareholder',
                    'toShareholder',
                ])
                ->where(function ($query) use ($shareholder) {
                    $query
                        ->where(
                            'shareholder_id',
                            $shareholder->id
                        )
                        ->orWhere(
                            'to_shareholder_id',
                            $shareholder->id
                        );
                })
                ->orderByDesc('date_ad')
                ->orderByDesc('id')
                ->paginate(20)
            : null;

        return view(
            'shareholder.dashboard',
            compact(
                'shareholder',
                'transactions',
                'totalKitta',
                'shareholdingPercentage',
                'financialYear',
                'companyProfitLoss',
                'myProfitLoss'
            )
        );
    }
}