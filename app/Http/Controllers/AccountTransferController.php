<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransfer;
use App\Services\AccountTransferService;
use App\Services\FinancialDateService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountTransferController extends Controller
{
    public function __construct(
        private readonly AccountTransferService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search', ''));
        $fromAccountId = trim((string) $request->get('from_account_id', ''));
        $toAccountId = trim((string) $request->get('to_account_id', ''));
        $status = strtolower(trim((string) $request->get('status', 'active')));
        if (! in_array($status, ['active', 'cancelled', 'all'], true)) $status = 'active';
        $dateFrom = trim((string) $request->get('date_from', ''));
        $dateTo = trim((string) $request->get('date_to', ''));
        $currentFinancialYear = app(FinancialDateService::class)
            ->fromEnglishDate(now()->toDateString())['financial_year'];
        $financialYear = trim((string) $request->get('financial_year', $currentFinancialYear));
        if ($financialYear === '') $financialYear = $currentFinancialYear;

        $accounts = Account::query()->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)->orderBy('name')->get();
        $financialYears = AccountTransfer::query()->whereNotNull('financial_year')
            ->where('financial_year', '!=', '')->distinct()->orderByDesc('financial_year')
            ->pluck('financial_year');
        if (! $financialYears->contains($currentFinancialYear)) $financialYears->prepend($currentFinancialYear);

        $query = AccountTransfer::query()
            ->with([
                'fromAccount',
                'toAccount',
                'creator',
                'canceller',
            ]);
        if ($fromAccountId !== '') $query->where('from_account_id', $fromAccountId);
        if ($toAccountId !== '') $query->where('to_account_id', $toAccountId);
        if (strtolower($financialYear) !== 'all') $query->where('financial_year', $financialYear);
        if ($status !== 'all') $query->where('status', $status);
        if ($dateFrom !== '') $query->whereDate('date_ad', '>=', $dateFrom);
        if ($dateTo !== '') $query->whereDate('date_ad', '<=', $dateTo);
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                    $query
                        ->where('transfer_number', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhereHas('fromAccount', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('toAccount', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
        }
        $transfers = $query->orderByDesc('date_ad')->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'account-transfers.index',
            compact('transfers', 'search', 'accounts', 'fromAccountId', 'toAccountId',
                'financialYear', 'financialYears', 'currentFinancialYear', 'status', 'dateFrom', 'dateTo')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        $accounts = Account::query()
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->orderBy('name')
            ->get();

        return view(
            'account-transfers.create',
            compact('accounts')
        );
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'from_account_id' => [
                'required',
                'exists:accounts,id',
            ],

            'to_account_id' => [
                'required',
                'different:from_account_id',
                'exists:accounts,id',
            ],

            'date_ad' => [
                'required',
                'date',
            ],

            'date_bs' => [
                'required',
                'string',
                'max:10',
            ],

            'financial_year' => [
                'required',
                'string',
                'max:10',
            ],

            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)->fromEnglishDate($validated['date_ad'])
        );

        $fromAccount = Account::query()
            ->whereKey($validated['from_account_id'])
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->firstOrFail();

        $toAccount = Account::query()
            ->whereKey($validated['to_account_id'])
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->firstOrFail();

        $validated['from_account_id'] = $fromAccount->id;
        $validated['to_account_id'] = $toAccount->id;
        $validated['created_by'] = auth()->id();

        try {
            $transfer = $this->service->create($validated);
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('account-transfers.show', $transfer)
            ->with(
                'success',
                'Account transfer created successfully.'
            );
    }

    public function show(AccountTransfer $accountTransfer)
    {
        $this->ensureAdminOrStaff();

        $accountTransfer->load([
            'fromAccount',
            'toAccount',
            'creator',
            'canceller',
        ]);

        return view(
            'account-transfers.show',
            compact('accountTransfer')
        );
    }

    public function edit(AccountTransfer $accountTransfer)
    {
        $this->ensureAdminOrStaff();
        abort_unless($accountTransfer->status === 'active', 403,
            'Only active account transfers can be edited.');
        $accountTransfer->load(['fromAccount', 'toAccount']);
        return view('account-transfers.edit', compact('accountTransfer'));
    }

    public function update(Request $request, AccountTransfer $accountTransfer)
    {
        $this->ensureAdminOrStaff();
        if ($accountTransfer->status !== 'active') {
            return back()->withErrors(['transaction' => 'Only active account transfers can be edited.']);
        }
        $validated = $request->validate([
            'date_ad' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
        $validated = array_replace($validated,
            app(FinancialDateService::class)->fromEnglishDate($validated['date_ad']));
        try {
            $accountTransfer = $this->service->updateMetadata($accountTransfer, $validated);
        } catch (\RuntimeException $exception) {
            return back()->withInput()->withErrors(['transaction' => $exception->getMessage()]);
        }
        return redirect()->route('account-transfers.show', $accountTransfer)
            ->with('success', 'Account transfer updated successfully.');
    }

    public function cancel(
        Request $request,
        AccountTransfer $accountTransfer
    ) {
        $user = auth()->user();

        abort_unless(
            $user && (
                $user->isAdmin()
                || $user->hasPermission('account-transfer.cancel')
            ),
            403
        );

        $validated = $request->validate([
            'cancellation_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        try {
            $this->service->cancel(
                $accountTransfer,
                auth()->id(),
                $validated['cancellation_reason']
            );
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('account-transfers.show', $accountTransfer)
            ->with(
                'success',
                'Account transfer cancelled successfully.'
            );
    }

    public function convertDate(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'date_ad' => [
                'required',
                'date',
            ],
        ]);

        $dateBs = \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(
            $validated['date_ad']
        )->toNepaliDate();

        [$year, $month] = array_map(
            'intval',
            explode('-', $dateBs)
        );

        if ($month >= 4) {
            $startYear = $year;
            $endYear = $year + 1;
        } else {
            $startYear = $year - 1;
            $endYear = $year;
        }

        return response()->json([
            'date_bs' => $dateBs,
            'financial_year' =>
                $startYear.'/'.substr((string) $endYear, -2),
        ]);
    }

    private function ensureAdminOrStaff(): void
    {
        $user = auth()->user();

        abort_unless(
            $user &&
            $user->canAccessBusinessData(),
            403
        );
    }
}
