<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransfer;
use App\Services\AccountTransferService;
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

        $search = trim((string) $request->get('search'));

        $transfers = AccountTransfer::query()
            ->with([
                'fromAccount',
                'toAccount',
                'creator',
                'canceller',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('transfer_number', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhereHas('fromAccount', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('toAccount', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'account-transfers.index',
            compact('transfers', 'search')
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

        $transfer = $this->service->create($validated);

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

        $this->service->cancel(
            $accountTransfer,
            auth()->id(),
            $validated['cancellation_reason']
        );

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
            ($user->isAdmin() || $user->isStaff()),
            403
        );
    }
}