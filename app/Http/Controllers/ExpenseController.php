<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use App\Services\FinancialDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search', ''));
        $categoryId = trim((string) $request->get('expense_category_id', ''));
        $accountId = trim((string) $request->get('account_id', ''));
        $status = strtolower(trim((string) $request->get('status', 'active')));

        if (! in_array($status, ['active', 'cancelled', 'all'], true)) {
            $status = 'active';
        }

        $dateFrom = trim((string) $request->get('date_from', ''));
        $dateTo = trim((string) $request->get('date_to', ''));
        $currentFinancialYear = app(FinancialDateService::class)
            ->fromEnglishDate(now()->toDateString())['financial_year'];
        $financialYear = trim((string) $request->get('financial_year', $currentFinancialYear));

        if ($financialYear === '') {
            $financialYear = $currentFinancialYear;
        }

        $categories = ExpenseCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $accounts = Account::query()
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->orderBy('name')
            ->get();

        $financialYears = Expense::query()
            ->whereNotNull('financial_year')
            ->where('financial_year', '!=', '')
            ->distinct()
            ->orderByDesc('financial_year')
            ->pluck('financial_year');

        if (! $financialYears->contains($currentFinancialYear)) {
            $financialYears->prepend($currentFinancialYear);
        }

        $query = Expense::query()
            ->with([
                'category',
                'account',
                'creator',
                'canceller',
            ]);

        if ($categoryId !== '') {
            $query->where('expense_category_id', $categoryId);
        }

        if ($accountId !== '') {
            $query->where('account_id', $accountId);
        }

        if (strtolower($financialYear) !== 'all') {
            $query->where('financial_year', $financialYear);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($dateFrom !== '') {
            $query->whereDate('date_ad', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('date_ad', '<=', $dateTo);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                    $query
                        ->where('expense_number', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('account', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
        }

        $expenses = $query
            ->orderByDesc('date_ad')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'expenses.index',
            compact(
                'expenses',
                'search',
                'categories',
                'categoryId',
                'accounts',
                'accountId',
                'financialYear',
                'financialYears',
                'currentFinancialYear',
                'status',
                'dateFrom',
                'dateTo'
            )
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        $categories = ExpenseCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $accounts = Account::query()
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->orderBy('name')
            ->get();

        return view(
            'expenses.create',
            compact(
                'categories',
                'accounts'
            )
        );
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'expense_category_id' => [
                'required',
                'exists:expense_categories,id',
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

        $category = ExpenseCategory::query()
            ->whereKey($validated['expense_category_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $account = Account::query()
            ->whereKey($validated['account_id'])
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->firstOrFail();

        $validated['expense_category_id'] = $category->id;
        $validated['account_id'] = $account->id;
        $validated['created_by'] = auth()->id();

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request
                ->file('attachment')
                ->store('expenses/attachments', 'local');

            $validated['attachment'] = $attachment;
        }

        try {
            $expense = $this->service->create($validated);
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
            ->route('expenses.show', $expense)
            ->with(
                'success',
                'Expense created successfully.'
            );
    }

    public function show(Expense $expense)
    {
        $this->ensureAdminOrStaff();

        $expense->load([
            'category',
            'account',
            'creator',
            'canceller',
        ]);

        return view(
            'expenses.show',
            compact('expense')
        );
    }

    public function edit(Expense $expense)
    {
        $this->ensureAdminOrStaff();

        abort_unless(
            $expense->status === 'active',
            403,
            'Only active expense transactions can be edited.'
        );

        $expense->load([
            'category',
            'account',
        ]);

        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->ensureAdminOrStaff();

        if ($expense->status !== 'active') {
            return back()->withErrors([
                'transaction' => 'Only active expense transactions can be edited.',
            ]);
        }

        $validated = $request->validate([
            'date_ad' => [
                'required',
                'date',
            ],
            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)
                ->fromEnglishDate($validated['date_ad'])
        );

        try {
            $expense = $this->service->updateMetadata($expense, $validated);
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('expenses.show', $expense)
            ->with('success', 'Expense updated successfully.');
    }

    public function cancel(
        Request $request,
        Expense $expense
    ) {
        $user = auth()->user();

        abort_unless(
            $user && (
                $user->isAdmin()
                || $user->hasPermission('expense.cancel')
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
                $expense,
                auth()->id(),
                $validated['cancellation_reason']
            );
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('expenses.show', $expense)
            ->with(
                'success',
                'Expense cancelled successfully.'
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
