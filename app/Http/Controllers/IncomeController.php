<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Services\IncomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncomeController extends Controller
{
    public function __construct(
        private readonly IncomeService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search'));

        $incomes = Income::query()
            ->with([
                'category',
                'account',
                'creator',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('income_number', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('account', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'incomes.index',
            compact('incomes', 'search')
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
            ($user->isAdmin() || $user->isStaff()),
            403
        );
    }
}
