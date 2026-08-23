<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Services\IncomeService;
use App\Services\IncomeExpenseImportService;
use App\Services\IncomeExpenseExportService;
use App\Services\FinancialDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncomeController extends Controller
{
    public function __construct(
        private readonly IncomeService $service,
        private readonly IncomeExpenseImportService $importService,
        private readonly IncomeExpenseExportService $exportService
    ) {
    }

    public function importCreate()
    {
        $this->ensureAdminOrStaff();

        return view('incomes.import');
    }

    public function importTemplate()
    {
        $this->ensureAdminOrStaff();

        return $this->importService->template('income');
    }

    public function importPreview(Request $request)
    {
        $this->ensureAdminOrStaff();
        $request->session()->forget('income_import_rows');
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
        ]);
        $rows = $this->importService->validateWorkbook($validated['file'], 'income');
        $request->session()->put('income_import_rows', $rows);

        return view('incomes.import-preview', compact('rows'));
    }

    public function importConfirm(Request $request)
    {
        $this->ensureAdminOrStaff();
        $rows = $request->session()->get('income_import_rows');

        if (! is_array($rows) || $rows === []) {
            return redirect()->route('incomes.import.create')->withErrors([
                'file' => 'Upload and preview an Income workbook before confirming.',
            ]);
        }

        try {
            $count = $this->importService->post('income', $rows, $request->user()->id);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['file' => $exception->getMessage()]);
        }

        $request->session()->forget('income_import_rows');

        return redirect()->route('incomes.index')
            ->with('success', $count.' income transactions imported successfully.');
    }
public function index(Request $request)
{
    $this->ensureAdminOrStaff();

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    $search = trim(
        (string) $request->get(
            'search',
            ''
        )
    );

    $categoryId = trim(
        (string) $request->get(
            'income_category_id',
            ''
        )
    );

    $accountId = trim(
        (string) $request->get(
            'account_id',
            ''
        )
    );

    $status = strtolower(
        trim(
            (string) $request->get(
                'status',
                'active'
            )
        )
    );

    if (! in_array(
        $status,
        [
            'active',
            'cancelled',
            'all',
        ],
        true
    )) {
        $status = 'active';
    }

    $dateFrom = trim(
        (string) $request->get(
            'date_from',
            ''
        )
    );

    $dateTo = trim(
        (string) $request->get(
            'date_to',
            ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Current Financial Year
    |--------------------------------------------------------------------------
    */

    $currentFinancialYear =
        app(FinancialDateService::class)
            ->fromEnglishDate(
                now()->toDateString()
            )['financial_year'];

    $financialYear = trim(
        (string) $request->get(
            'financial_year',
            $currentFinancialYear
        )
    );

    if ($financialYear === '') {
        $financialYear =
            $currentFinancialYear;
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Options
    |--------------------------------------------------------------------------
    */

    $categories =
        IncomeCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

    $accounts =
        Account::query()
            ->where('is_active', true)
            ->where(
                'type',
                '!=',
                Account::TYPE_FIXED_DEPOSIT
            )
            ->orderBy('name')
            ->get();

    $financialYears =
        Income::query()
            ->whereNotNull('financial_year')
            ->where(
                'financial_year',
                '!=',
                ''
            )
            ->distinct()
            ->orderByDesc('financial_year')
            ->pluck('financial_year');

    if (
        ! $financialYears->contains(
            $currentFinancialYear
        )
    ) {
        $financialYears->prepend(
            $currentFinancialYear
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Income Query
    |--------------------------------------------------------------------------
    */

    $query =
        Income::query()
            ->with([
                'category',
                'account',
                'creator',
                'canceller',
            ]);

    /*
    |--------------------------------------------------------------------------
    | Category
    |--------------------------------------------------------------------------
    */

    if ($categoryId !== '') {
        $query->where(
            'income_category_id',
            $categoryId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Account
    |--------------------------------------------------------------------------
    */

    if ($accountId !== '') {
        $query->where(
            'account_id',
            $accountId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Financial Year
    |--------------------------------------------------------------------------
    */

    if (
        strtolower($financialYear)
        !== 'all'
    ) {
        $query->where(
            'financial_year',
            $financialYear
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    if ($status !== 'all') {
        $query->where(
            'status',
            $status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Date Range
    |--------------------------------------------------------------------------
    */

    if ($dateFrom !== '') {
        $query->whereDate(
            'date_ad',
            '>=',
            $dateFrom
        );
    }

    if ($dateTo !== '') {
        $query->whereDate(
            'date_ad',
            '<=',
            $dateTo
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($search !== '') {
        $query->where(
            function ($query) use (
                $search
            ) {
                $query
                    ->where(
                        'income_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'reference',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'note',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'category',
                        function ($query) use (
                            $search
                        ) {
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
                    )
                    ->orWhereHas(
                        'account',
                        function ($query) use (
                            $search
                        ) {
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
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Results
    |--------------------------------------------------------------------------
    */

    if (in_array($request->get('output'), ['print', 'excel'], true)) {
        $filteredIncomes = (clone $query)
            ->orderByDesc('date_ad')
            ->orderByDesc('id')
            ->get();

        if ($request->get('output') === 'excel') {
            return $this->exportService->excel($filteredIncomes, 'income');
        }

        return view('transactions.print', [
            'title' => 'Filtered Income Report',
            'type' => 'income',
            'transactions' => $filteredIncomes,
            'totalAmount' => (int) $filteredIncomes->sum('amount'),
        ]);
    }

    $filteredSummary = (clone $query)
        ->selectRaw('COUNT(*) as total_records, COALESCE(SUM(amount), 0) as total_amount')
        ->first();

    $totalRecords = (int) $filteredSummary->total_records;
    $totalAmount = (int) $filteredSummary->total_amount;

    $incomes =
        $query
            ->orderByDesc('date_ad')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

    return view(
        'incomes.index',
        compact(
            'incomes',
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
            'dateTo',
            'totalRecords',
            'totalAmount'
        )
    );
}
    

    public function create()
    {
        $this->ensureAdminOrStaff();

        $categories = IncomeCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $accounts = Account::query()
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->orderBy('name')
            ->get();

        return view(
            'incomes.create',
            compact('categories', 'accounts')
        );
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'income_category_id' => [
                'required',
                'exists:income_categories,id',
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

        $category = IncomeCategory::query()
            ->whereKey($validated['income_category_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $account = Account::query()
            ->whereKey($validated['account_id'])
            ->where('is_active', true)
            ->where('type', '!=', Account::TYPE_FIXED_DEPOSIT)
            ->firstOrFail();

        $validated['income_category_id'] = $category->id;
        $validated['account_id'] = $account->id;
        $validated['created_by'] = auth()->id();

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request
                ->file('attachment')
                ->store('incomes/attachments', 'local');

            $validated['attachment'] = $attachment;
        }

        try {
            $income = $this->service->create($validated);
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
            ->route('incomes.show', $income)
            ->with(
                'success',
                'Income created successfully.'
            );
    }

    public function show(Income $income)
    {
        $this->ensureAdminOrStaff();

        $income->load([
            'category',
            'account',
            'creator',
            'canceller',
        ]);

        return view(
            'incomes.show',
            compact('income')
        );
    }

    public function edit(Income $income)
    {
        $this->ensureAdminOrStaff();

        abort_unless(
            $income->status === 'active',
            403,
            'Only active income transactions can be edited.'
        );

        $income->load([
            'category',
            'account',
        ]);

        return view('incomes.edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        $this->ensureAdminOrStaff();

        if ($income->status !== 'active') {
            return back()->withErrors([
                'transaction' => 'Only active income transactions can be edited.',
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
            $income = $this->service->updateMetadata($income, $validated);
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('incomes.show', $income)
            ->with('success', 'Income updated successfully.');
    }

    public function cancel(
        Request $request,
        Income $income
    ) {
        $user = auth()->user();

        abort_unless(
            $user &&
            (
                $user->isAdmin() ||
                $user->hasPermission('income.cancel')
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
                $income,
                auth()->id(),
                $validated['cancellation_reason']
            );
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['transaction' => $exception->getMessage()]);
        }

        return redirect()
            ->route('incomes.show', $income)
            ->with(
                'success',
                'Income cancelled successfully.'
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
