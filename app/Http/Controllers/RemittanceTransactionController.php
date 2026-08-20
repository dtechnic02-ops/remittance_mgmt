<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\RemittanceTransaction;
use App\Services\RemittanceTransactionService;
use App\Services\FinancialDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RemittanceTransactionController extends Controller
{
    public function __construct(
        private readonly RemittanceTransactionService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search', ''));
        $providerAccountId = trim((string) $request->get('provider_account_id', ''));
        $cashAccountId = trim((string) $request->get('cash_account_id', ''));
        $status = strtolower(trim((string) $request->get('status', 'active')));
        if (! in_array($status, ['active', 'cancelled', 'all'], true)) $status = 'active';
        $dateFrom = trim((string) $request->get('date_from', ''));
        $dateTo = trim((string) $request->get('date_to', ''));
        $currentFinancialYear = app(FinancialDateService::class)
            ->fromEnglishDate(now()->toDateString())['financial_year'];
        $financialYear = trim((string) $request->get('financial_year', $currentFinancialYear));
        if ($financialYear === '') $financialYear = $currentFinancialYear;

        $providers = Account::query()->where('type', Account::TYPE_REMITTANCE)
            ->where('is_active', true)->orderBy('name')->get();
        $cashAccounts = Account::query()->where('type', Account::TYPE_CASH)
            ->where('is_active', true)->orderBy('name')->get();
        $financialYears = RemittanceTransaction::query()->whereNotNull('financial_year')
            ->where('financial_year', '!=', '')->distinct()->orderByDesc('financial_year')
            ->pluck('financial_year');
        if (! $financialYears->contains($currentFinancialYear)) $financialYears->prepend($currentFinancialYear);

        $query = RemittanceTransaction::query()
            ->with([
                'customer',
                'providerAccount',
                'cashAccount',
                'creator',
                'canceller',
            ]);

        if ($providerAccountId !== '') $query->where('provider_account_id', $providerAccountId);
        if ($cashAccountId !== '') $query->where('cash_account_id', $cashAccountId);
        if (strtolower($financialYear) !== 'all') $query->where('financial_year', $financialYear);
        if ($status !== 'all') $query->where('status', $status);
        if ($dateFrom !== '') $query->whereDate('date_ad', '>=', $dateFrom);
        if ($dateTo !== '') $query->whereDate('date_ad', '<=', $dateTo);

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                    $query
                        ->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('provider_reference', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('customer_code', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%");
                        })
                        ->orWhereHas('providerAccount', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('cashAccount', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
        }

        $transactions = $query->orderByDesc('date_ad')->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'remittances.index',
            compact('transactions', 'search', 'providers', 'providerAccountId',
                'cashAccounts', 'cashAccountId', 'financialYear', 'financialYears',
                'currentFinancialYear', 'status', 'dateFrom', 'dateTo')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        $customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $providers = Account::query()
            ->where('type', Account::TYPE_REMITTANCE)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $cashAccounts = Account::query()
            ->where('type', Account::TYPE_CASH)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'remittances.create',
            compact(
                'customers',
                'providers',
                'cashAccounts'
            )
        );
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'direction' => [
                'required',
                Rule::in(['send', 'receive']),
            ],

            'customer_id' => [
                'required',
                'exists:customers,id',
            ],

            'provider_account_id' => [
                'required',
                'exists:accounts,id',
            ],

            'cash_account_id' => [
                'required',
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

            'principal_amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'service_charge' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'provider_reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'note' => [
                'nullable',
                'string',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ]);

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)->fromEnglishDate($validated['date_ad'])
        );

        $customer = Customer::query()
            ->whereKey($validated['customer_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $validated['customer_id'] = $customer->id;
        $validated['service_charge'] =
            (int) ($validated['service_charge'] ?? 0);

        $validated['created_by'] = auth()->id();

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request
                ->file('attachment')
                ->store('remittances/attachments', 'local');

            $validated['attachment'] = $attachment;
        }

        try {
            $transaction = $this->service->create($validated);
        } catch (\RuntimeException $exception) {
            if ($attachment) {
                Storage::disk('local')->delete($attachment);
            }

            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            if ($attachment) {
                Storage::disk('local')->delete($attachment);
            }

            throw $exception;
        }

        return redirect()
            ->route('remittances.show', $transaction)
            ->with(
                'success',
                'Remittance transaction created successfully.'
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



    public function show(RemittanceTransaction $remittance)
    {
        $this->ensureAdminOrStaff();

        $remittance->load([
            'customer',
            'providerAccount',
            'cashAccount',
            'creator',
            'canceller',
        ]);

        return view(
            'remittances.show',
            compact('remittance')
        );
    }

    public function edit(RemittanceTransaction $remittance)
    {
        $this->ensureAdminOrStaff();
        abort_unless($remittance->status === 'active', 403,
            'Only active remittance transactions can be edited.');
        $remittance->load(['customer', 'providerAccount', 'cashAccount']);
        return view('remittances.edit', compact('remittance'));
    }

    public function update(Request $request, RemittanceTransaction $remittance)
    {
        $this->ensureAdminOrStaff();
        if ($remittance->status !== 'active') {
            return back()->withErrors(['transaction' => 'Only active remittance transactions can be edited.']);
        }
        $validated = $request->validate([
            'date_ad' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
        $validated = array_replace($validated,
            app(FinancialDateService::class)->fromEnglishDate($validated['date_ad']));
        try {
            $remittance = $this->service->updateMetadata($remittance, $validated);
        } catch (\RuntimeException $exception) {
            return back()->withInput()->withErrors(['transaction' => $exception->getMessage()]);
        }
        return redirect()->route('remittances.show', $remittance)
            ->with('success', 'Remittance transaction updated successfully.');
    }

    public function cancel(
        Request $request,
        RemittanceTransaction $remittance
    ) {
        $user = auth()->user();

        abort_unless(
            $user && $user->hasPermission('remittance.cancel'),
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
                $remittance,
                auth()->id(),
                $validated['cancellation_reason']
            );
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('remittances.show', $remittance)
            ->with(
                'success',
                'Remittance transaction cancelled successfully.'
            );
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
