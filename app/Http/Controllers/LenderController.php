<?php

namespace App\Http\Controllers;

use App\Models\Lender;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LenderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search', ''));
        $status = strtolower(trim((string) $request->get('status', 'active')));

        if (! in_array($status, ['active', 'inactive', 'all'], true)) {
            $status = 'active';
        }

        $lenders = $this->withFinancialTotals(Lender::query())
            ->when($status !== 'all', fn ($query) => $query->where('is_active', $status === 'active'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'lenders.index',
            compact('lenders', 'search', 'status')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        return view('lenders.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:30',
                'unique:lenders,code',
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:30',
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'note' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        Lender::create($validated);

        return redirect()
            ->route('lenders.index')
            ->with(
                'success',
                'Lender created successfully.'
            );
    }

    public function edit(Lender $lender)
    {
        $this->ensureAdminOrStaff();

        return view(
            'lenders.edit',
            compact('lender')
        );
    }

    public function show(Lender $lender)
    {
        $this->ensureAdminOrStaff();

        $lender = $this->withFinancialTotals(Lender::query())
            ->findOrFail($lender->id);

        return view('lenders.show', compact('lender'));
    }

    public function update(
        Request $request,
        Lender $lender
    ) {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique(
                    'lenders',
                    'code'
                )->ignore($lender->id),
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'mobile' => [
                'nullable',
                'string',
                'max:30',
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'note' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $outstanding = $this->outstanding($lender);

        if ($lender->is_active && ! $validated['is_active'] && $outstanding > 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_active' => 'Lender cannot be made inactive while borrowing outstanding is greater than zero.',
                ]);
        }

        $validated['updated_by'] = auth()->id();

        $lender->update($validated);

        return redirect()
            ->route('lenders.index')
            ->with(
                'success',
                'Lender updated successfully.'
            );
    }

    private function withFinancialTotals($query)
    {
        return $query
            ->withSum([
                'borrowings as total_borrowed' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where('transaction_type', 'borrow'),
            ], 'amount')
            ->withSum([
                'borrowings as total_repaid' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where('transaction_type', 'repay'),
            ], 'amount');
    }

    private function outstanding(Lender $lender): int
    {
        $lender = $this->withFinancialTotals(Lender::query())->findOrFail($lender->id);

        return (int) ($lender->total_borrowed ?? 0)
            - (int) ($lender->total_repaid ?? 0);
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
