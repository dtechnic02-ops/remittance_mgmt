<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::query()
            ->orderBy('name')
            ->paginate(20);

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create', [
            'types' => Account::types(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:accounts,code'],
            'type' => ['required', Rule::in(array_keys(Account::types()))],
            'account_number' => ['nullable', 'string', 'max:100'],
            'branch_name' => ['nullable', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('accounts', 'local');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        try {
            Account::create($validated);
        } catch (\Throwable $exception) {
            if (! empty($validated['attachment'])) {
                Storage::disk('local')->delete($validated['attachment']);
            }
            throw $exception;
        }

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', [
            'account' => $account,
            'types' => Account::types(),
        ]);
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('accounts', 'code')->ignore($account->id),
            ],
            'type' => ['required', Rule::in(array_keys(Account::types()))],
            'account_number' => ['nullable', 'string', 'max:100'],
            'branch_name' => ['nullable', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $oldAttachment = null;
        $newAttachment = null;
        if ($request->hasFile('attachment')) {
            $oldAttachment = $account->attachment;
            $validated['attachment'] = $request
                ->file('attachment')
                ->store('accounts', 'local');
            $newAttachment = $validated['attachment'];
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['updated_by'] = auth()->id();

        try {
            $account->update($validated);
        } catch (\Throwable $exception) {
            if ($newAttachment) {
                Storage::disk('local')->delete($newAttachment);
            }
            throw $exception;
        }

        if ($oldAttachment) {
            Storage::disk('local')->delete($oldAttachment);
        }

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        if ($account->current_balance !== 0 || $account->opening_balance !== 0) {
            return back()->with(
                'error',
                'This account has a financial balance and cannot be deleted.'
            );
        }

        if ($account->attachment) {
            Storage::disk('local')->delete($account->attachment);
        }

        $account->delete();

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
