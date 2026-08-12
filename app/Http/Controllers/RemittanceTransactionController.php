<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\RemittanceTransaction;
use App\Services\RemittanceTransactionService;
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

        $search = trim((string) $request->get('search'));

        $transactions = RemittanceTransaction::query()
            ->with([
                'customer',
                'providerAccount',
                'cashAccount',
                'creator',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('provider_reference', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('customer_code', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'remittances.index',
            compact('transactions', 'search')
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
                ->store('remittances/attachments', 'public');

            $validated['attachment'] = $attachment;
        }

        try {
            $transaction = $this->service->create($validated);
        } catch (\Throwable $exception) {
            if ($attachment) {
                Storage::disk('public')->delete($attachment);
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

        $this->service->cancel(
            $remittance,
            auth()->id(),
            $validated['cancellation_reason']
        );

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
            ($user->isAdmin() || $user->isStaff()),
            403
        );
    }
}