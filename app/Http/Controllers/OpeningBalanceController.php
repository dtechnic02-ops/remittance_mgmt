<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\OpeningBalance;
use App\Services\OpeningBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

class OpeningBalanceController extends Controller
{
    public function __construct(
        private readonly OpeningBalanceService $openingBalanceService
    ) {
    }

    public function index()
    {
        $openingBalances = OpeningBalance::query()
            ->with(['account', 'creator'])
            ->orderByDesc('date_ad')
            ->paginate(20);

        return view('opening-balances.index', compact('openingBalances'));
    }

    public function create()
    {
        $accounts = Account::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('opening-balances.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'date_ad' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
            ],
        ]);

        $dateBs = LaravelNepaliDate::from($validated['date_ad'])
            ->toNepaliDate();

        $validated['date_bs'] = $dateBs;
        $validated['financial_year'] = $this->resolveFinancialYear($dateBs);

        $duplicateExists = OpeningBalance::query()
            ->where('account_id', $validated['account_id'])
            ->where('financial_year', $validated['financial_year'])
            ->exists();

        if ($duplicateExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'account_id' => 'Opening Balance already exists for this account in the selected financial year.',
                ]);
        }

        $newAttachment = null;

        if ($request->hasFile('attachment')) {
            $newAttachment = $request
                ->file('attachment')
                ->store('opening-balances', 'public');

            $validated['attachment'] = $newAttachment;
        }

        $validated['created_by'] = auth()->id();

        try {
            $this->openingBalanceService->create($validated);
        } catch (RuntimeException $exception) {
            if ($newAttachment) {
                Storage::disk('public')->delete($newAttachment);
            }

            return back()
                ->withInput()
                ->withErrors(['opening_balance' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            if ($newAttachment) {
                Storage::disk('public')->delete($newAttachment);
            }

            throw $exception;
        }

        return redirect()
            ->route('opening-balances.index')
            ->with('success', 'Opening Balance created successfully.');
    }

    public function edit(OpeningBalance $openingBalance)
    {
        $accounts = Account::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('opening-balances.edit', compact(
            'openingBalance',
            'accounts'
        ));
    }

    public function update(
        Request $request,
        OpeningBalance $openingBalance
    ) {
        $validated = $request->validate([
            'date_ad' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
            ],
        ]);

        $dateBs = LaravelNepaliDate::from($validated['date_ad'])
            ->toNepaliDate();

        $validated['date_bs'] = $dateBs;
        $validated['financial_year'] = $this->resolveFinancialYear($dateBs);

        $duplicateExists = OpeningBalance::query()
            ->where('account_id', $openingBalance->account_id)
            ->where('financial_year', $validated['financial_year'])
            ->whereKeyNot($openingBalance->id)
            ->exists();

        if ($duplicateExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'date_ad' => 'Opening Balance already exists for this account in that financial year.',
                ]);
        }

        $oldAttachment = $openingBalance->attachment;
        $newAttachment = null;

        if ($request->hasFile('attachment')) {
            $newAttachment = $request
                ->file('attachment')
                ->store('opening-balances', 'public');

            $validated['attachment'] = $newAttachment;
        }

        $validated['updated_by'] = auth()->id();

        try {
            $this->openingBalanceService->update(
                $openingBalance,
                $validated
            );
        } catch (RuntimeException $exception) {
            if ($newAttachment) {
                Storage::disk('public')->delete($newAttachment);
            }

            return back()
                ->withInput()
                ->withErrors(['opening_balance' => $exception->getMessage()]);
        } catch (\Throwable $exception) {
            if ($newAttachment) {
                Storage::disk('public')->delete($newAttachment);
            }

            throw $exception;
        }

        if ($newAttachment && $oldAttachment) {
            Storage::disk('public')->delete($oldAttachment);
        }

        return redirect()
            ->route('opening-balances.index')
            ->with('success', 'Opening Balance updated successfully.');
    }

    private function resolveFinancialYear(string $dateBs): string
    {
        [$year, $month] = array_map(
            'intval',
            explode('-', $dateBs)
        );

        // Nepali financial year starts from Shrawan (month 04).
        if ($month >= 4) {
            $startYear = $year;
            $endYear = $year + 1;
        } else {
            $startYear = $year - 1;
            $endYear = $year;
        }

        return $startYear.'/'.substr((string) $endYear, -2);
    }
}
