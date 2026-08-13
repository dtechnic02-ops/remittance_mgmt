<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Services\ShareTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShareTransactionController extends Controller
{
    public function __construct(
        private readonly ShareTransactionService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search'));

        $transactions = ShareTransaction::query()
            ->with([
                'shareholder',
                'account',
                'creator',
                'canceller',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('shareholder', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%");
                        })
                        ->orWhereHas('account', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'share-transactions.index',
            compact('transactions', 'search')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        $shareholders = Shareholder::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $accounts = Account::query()
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->orderBy('name')
            ->get();

        return view(
            'share-transactions.create',
            compact('shareholders', 'accounts')
        );
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'transaction_type' => [
                'required',
                'in:buy,withdraw',
            ],

            'shareholder_id' => [
                'required',
                'exists:shareholders,id',
            ],

            'account_id' => [
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

            'kitta' => [
                'required',
                'integer',
                'min:1',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        $shareholder = Shareholder::query()
            ->whereKey($validated['shareholder_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $account = Account::query()
            ->whereKey($validated['account_id'])
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->firstOrFail();

        $validated['shareholder_id'] = $shareholder->id;
        $validated['account_id'] = $account->id;
        $validated['created_by'] = auth()->id();

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request
                ->file('attachment')
                ->store('share-transactions/attachments', 'local');

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
            ->route('share-transactions.show', $transaction)
            ->with(
                'success',
                'Share transaction created successfully.'
            );
    }

    public function show(ShareTransaction $shareTransaction)
    {
        $this->ensureAdminOrStaff();

        $shareTransaction->load([
            'shareholder',
            'account',
            'creator',
            'canceller',
        ]);

        return view(
            'share-transactions.show',
            compact('shareTransaction')
        );
    }

    public function cancel(
        Request $request,
        ShareTransaction $shareTransaction
    ) {
        $user = auth()->user();

        abort_unless(
            $user &&
            (
                $user->isAdmin()
                || $user->hasPermission('share-transaction.cancel')
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
                $shareTransaction,
                auth()->id(),
                $validated['cancellation_reason']
            );
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('share-transactions.show', $shareTransaction)
            ->with(
                'success',
                'Share transaction cancelled successfully.'
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
