<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search'));

        $categories = ExpenseCategory::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'expense-categories.index',
            compact('categories', 'search')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        return view('expense-categories.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:expense_categories,name',
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
                'unique:expense_categories,code',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['code'] =
            filled($validated['code'] ?? null)
                ? trim($validated['code'])
                : null;

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        ExpenseCategory::create($validated);

        return redirect()
            ->route('expense-categories.index')
            ->with(
                'success',
                'Expense category created successfully.'
            );
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $this->ensureAdminOrStaff();

        return view(
            'expense-categories.edit',
            compact('expenseCategory')
        );
    }

    public function update(
        Request $request,
        ExpenseCategory $expenseCategory
    ) {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique(
                    'expense_categories',
                    'name'
                )->ignore($expenseCategory->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(
                    'expense_categories',
                    'code'
                )->ignore($expenseCategory->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['code'] =
            filled($validated['code'] ?? null)
                ? trim($validated['code'])
                : null;

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['updated_by'] = auth()->id();

        $expenseCategory->update($validated);

        return redirect()
            ->route('expense-categories.index')
            ->with(
                'success',
                'Expense category updated successfully.'
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