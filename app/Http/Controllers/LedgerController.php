<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\LedgerEntry;
use App\Services\FinancialDateService;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $accountId = $request->get('account_id');

        $transactionType = trim(
            (string) $request->get(
                'transaction_type',
                ''
            )
        );

        $status = strtolower(
            trim(
                (string) $request->get(
                    'status',
                    'posted'
                )
            )
        );

        if (! in_array(
            $status,
            [
                'posted',
                'reversal',
                'all',
            ],
            true
        )) {
            $status = 'posted';
        }

        $search = trim(
            (string) $request->get(
                'search',
                ''
            )
        );

        $dateFrom = trim(
            (string) $request->get(
                'date_from',
                ''
            )
        );

        $dateTo = trim(
            (string) $request->get(
                'date_to',
                ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Current Financial Year
        |--------------------------------------------------------------------------
        */

        $currentFinancialYear =
            app(FinancialDateService::class)
                ->fromEnglishDate(
                    now()->toDateString()
                )['financial_year'];

        $financialYear = trim(
            (string) $request->get(
                'financial_year',
                $currentFinancialYear
            )
        );

        if ($financialYear === '') {
            $financialYear =
                $currentFinancialYear;
        }

        /*
        |--------------------------------------------------------------------------
        | Accounts
        |--------------------------------------------------------------------------
        */

        $accounts = Account::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Available Transaction Types
        |--------------------------------------------------------------------------
        |
        | Read directly from ledger so every real ledger type remains available.
        |
        */

        $transactionTypes =
            LedgerEntry::query()
                ->whereNotNull('transaction_type')
                ->where(
                    'transaction_type',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderBy('transaction_type')
                ->pluck('transaction_type');

        /*
        |--------------------------------------------------------------------------
        | Available Financial Years
        |--------------------------------------------------------------------------
        */

        $financialYears =
            LedgerEntry::query()
                ->whereNotNull('financial_year')
                ->where(
                    'financial_year',
                    '!=',
                    ''
                )
                ->distinct()
                ->orderByDesc('financial_year')
                ->pluck('financial_year');

        if (
            ! $financialYears->contains(
                $currentFinancialYear
            )
        ) {
            $financialYears->prepend(
                $currentFinancialYear
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ledger Query
        |--------------------------------------------------------------------------
        */

        $query =
            LedgerEntry::query()
                ->with([
                    'account',
                    'creator',
                    'reversalOf',
                ]);

        /*
        |--------------------------------------------------------------------------
        | Account
        |--------------------------------------------------------------------------
        */

        if (
            $accountId !== null
            && $accountId !== ''
        ) {
            $query->where(
                'account_id',
                $accountId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction Type
        |--------------------------------------------------------------------------
        */

        if ($transactionType !== '') {
            $query->where(
                'transaction_type',
                $transactionType
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Financial Year
        |--------------------------------------------------------------------------
        */

        if (
            strtolower($financialYear)
            !== 'all'
        ) {
            $query->where(
                'financial_year',
                $financialYear
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        |
        | Posted   = normal ledger entries
        | Reversal = reversal entries
        | All      = complete audit history
        |
        */

        if ($status === 'posted') {
            $query->where(
                'is_reversal',
                false
            );
        } elseif ($status === 'reversal') {
            $query->where(
                'is_reversal',
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if ($dateFrom !== '') {
            $query->whereDate(
                'date_ad',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo !== '') {
            $query->whereDate(
                'date_ad',
                '<=',
                $dateTo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $query->where(
                function ($query) use (
                    $search
                ) {
                    $query
                        ->where(
                            'transaction_number',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'component',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'note',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'account',
                            function ($query) use (
                                $search
                            ) {
                                $query
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'code',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        */

        $entries =
            $query
                ->orderByDesc('date_ad')
                ->orderByDesc('id')
                ->paginate(50)
                ->withQueryString();

        return view(
            'ledger.index',
            compact(
                'entries',
                'accounts',
                'accountId',
                'transactionType',
                'transactionTypes',
                'status',
                'financialYear',
                'financialYears',
                'currentFinancialYear',
                'dateFrom',
                'dateTo',
                'search'
            )
        );
    }

    private function ensureAdminOrStaff(): void
    {
        $user = auth()->user();

        abort_unless(
            $user
            && (
                $user->canAccessBusinessData()
            ),
            403
        );
    }
}
