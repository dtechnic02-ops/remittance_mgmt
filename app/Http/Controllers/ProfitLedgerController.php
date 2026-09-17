<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
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
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'output' => ['nullable', 'in:print'],
        ]);

        $dateFrom = trim((string) ($validated['date_from'] ?? ''));
        $dateTo = trim((string) ($validated['date_to'] ?? ''));

        $summary = $this->service->summarize(
            $dateFrom !== '' ? $dateFrom : null,
            $dateTo !== '' ? $dateTo : null
        );

        $payload = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
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

    private function ensureAdminOrStaff(): void
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->canAccessBusinessData(),
            403
        );
    }
}
