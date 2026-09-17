<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RemittanceTransaction;
use App\Services\FinancialDateService;
use App\Services\ProfitLedgerService;
use Illuminate\Http\Request;

class ProfitLedgerController extends Controller
{
    public function __construct(
        private readonly ProfitLedgerService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'financial_year' => ['nullable', 'string', 'max:10'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'output' => ['nullable', 'in:print'],
        ]);

        $dateFrom = trim((string) ($validated['date_from'] ?? ''));
        $dateTo = trim((string) ($validated['date_to'] ?? ''));
        $financialYear = trim((string) ($validated['financial_year'] ?? 'all'));
        if ($financialYear === '') {
            $financialYear = 'all';
        }

        $currentFinancialYear = app(FinancialDateService::class)
            ->fromEnglishDate(now()->toDateString())['financial_year'];
        $financialYears = collect()
            ->merge($this->storedFinancialYears(Income::query()))
            ->merge($this->storedFinancialYears(Expense::query()))
            ->merge($this->storedFinancialYears(RemittanceTransaction::query()))
            ->unique()
            ->sortDesc()
            ->values();
        if (! $financialYears->contains($currentFinancialYear)) {
            $financialYears->prepend($currentFinancialYear);
        }

        $summary = $this->service->summarize(
            $dateFrom !== '' ? $dateFrom : null,
            $dateTo !== '' ? $dateTo : null,
            $financialYear
        );

        $payload = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'financialYear' => $financialYear,
            'financialYears' => $financialYears,
            'currentFinancialYear' => $currentFinancialYear,
            'income' => $summary['income'],
            'remittanceCharge' => $summary['remittance_charge'],
            'expense' => $summary['expense'],
            'profit' => $summary['profit'],
        ];

        if (($validated['output'] ?? '') === 'print') {
            return view('profit-ledger.print', $payload + [
                'companyName' => CompanyInfo::query()->value('company_name')
                    ?: (string) config('app.name'),
                'printedAt' => now()->format('Y-m-d H:i'),
            ]);
        }

        return view('profit-ledger.index', $payload);
    }

    private function storedFinancialYears($query)
    {
        return $query
            ->whereNotNull('financial_year')
            ->where('financial_year', '!=', '')
            ->distinct()
            ->orderByDesc('financial_year')
            ->pluck('financial_year');
    }

    private function ensureAdminOrStaff(): void
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->canAccessBusinessData(),
            403
        );
    }
}
