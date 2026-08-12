<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'account_id' => [
                'nullable',
                'integer',
                'exists:accounts,id',
            ],
            'transaction_type' => [
                'nullable',
                'string',
                Rule::in([
                    'remittance',
                    'account_transfer',
                ]),
            ],
            'date_from' => [
                'nullable',
                'date',
            ],
            'date_to' => [
                'nullable',
                'date',
            ],
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $accountId = $validated['account_id'] ?? null;
        $transactionType = $validated['transaction_type'] ?? null;
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;
        $search = trim((string) ($validated['search'] ?? ''));

        $accounts = Account::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $entries = LedgerEntry::query()
            ->with([
                'account',
                'creator',
                'reversalOf',
            ])
            ->when(
                $accountId,
                fn ($query) =>
                    $query->where('account_id', $accountId)
            )
            ->when(
                $transactionType,
                fn ($query) =>
                    $query->where(
                        'transaction_type',
                        $transactionType
                    )
            )
            ->when(
                $dateFrom,
                fn ($query) =>
                    $query->whereDate(
                        'date_ad',
                        '>=',
                        $dateFrom
                    )
            )
            ->when(
                $dateTo,
                fn ($query) =>
                    $query->whereDate(
                        'date_ad',
                        '<=',
                        $dateTo
                    )
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
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
                                function ($query) use ($search) {
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
                    });
                }
            )
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
            $user &&
            ($user->isAdmin() || $user->isStaff()),
            403
        );
    }
}