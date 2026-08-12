<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeCategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search'));

        $categories = IncomeCategory::query()
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
            'income-categories.index',
            compact('categories', 'search')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        return view('income-categories.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:income_categories,name',
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                'unique:income_categories,code',
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

        IncomeCategory::create($validated);

        return redirect()
            ->route('income-categories.index')
            ->with(
                'success',
                'Income category created successfully.'
            );
    }

    public function edit(IncomeCategory $incomeCategory)
    {
        $this->ensureAdminOrStaff();

        return view(
            'income-categories.edit',
            compact('incomeCategory')
        );
    }

    public function update(
        Request $request,
        IncomeCategory $incomeCategory
    ) {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique(
                    'income_categories',
                    'name'
                )->ignore($incomeCategory->id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(
                    'income_categories',
                    'code'
                )->ignore($incomeCategory->id),
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

        $incomeCategory->update($validated);

        return redirect()
            ->route('income-categories.index')
            ->with(
                'success',
                'Income category updated successfully.'
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