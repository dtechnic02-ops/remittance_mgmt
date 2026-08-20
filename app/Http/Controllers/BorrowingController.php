<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Borrowing;
use App\Models\Lender;
use App\Services\BorrowingService;
use App\Services\FinancialDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BorrowingController extends Controller
{
    public function __construct(
        private readonly BorrowingService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search'));

        $transactions = Borrowing::query()
            ->with([
                'lender',
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
                        ->orWhereHas('lender', function ($query) use ($search) {
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
            'borrowings.index',
            compact('transactions', 'search')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        $lenders = Lender::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $lenders->each(function ($lender) {
            $lender->outstanding =
                $this->service->outstanding($lender->id);
        });

        $accounts = Account::query()
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->orderBy('name')
            ->get();

        return view(
            'borrowings.create',
            compact('lenders', 'accounts')
        );
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'transaction_type' => [
                'required',
                'in:borrow,repay',
            ],

            'lender_id' => [
                'required',
                'exists:lenders,id',
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

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)->fromEnglishDate($validated['date_ad'])
        );

        $lender = Lender::query()
            ->whereKey($validated['lender_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $account = Account::query()
            ->whereKey($validated['account_id'])
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->firstOrFail();

        $validated['lender_id'] = $lender->id;
        $validated['account_id'] = $account->id;
        $validated['created_by'] = auth()->id();

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request
                ->file('attachment')
                ->store('borrowings/attachments', 'local');

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
        ->withErrors([
            'transaction' => $exception->getMessage(),
        ]);
} catch (\Throwable $exception) {
    if ($attachment) {
        Storage::disk('local')->delete($attachment);
    }

    throw $exception;
}

        return redirect()
            ->route('borrowings.index')
            ->with(
                'success',
                'Borrowing transaction created successfully.'
            );
    }

    public function cancel(
        Request $request,
        Borrowing $borrowing
    ) {
        $user = auth()->user();

        abort_unless(
            $user &&
            (
                $user->isAdmin()
                || $user->hasPermission('borrowing.cancel')
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
                $borrowing,
                auth()->id(),
                $validated['cancellation_reason']
            );
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('borrowings.index')
            ->with(
                'success',
                'Borrowing transaction cancelled successfully.'
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
