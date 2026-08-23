<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Expenses</h2>
                <p class="mt-1 text-sm text-gray-500">Expense Transactions</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('expense-category.view'))
                    <a href="{{ route('expense-categories.index') }}"
                       class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Expense Categories
                    </a>
                @endif

                @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('expense.create'))
                    <a href="{{ route('expenses.import.template') }}" class="text-sm text-gray-600 hover:underline">
                        Download Template
                    </a>
                    <a href="{{ route('expenses.import.create') }}"
                       class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Import Expense
                    </a>
                    <a href="{{ route('expenses.create') }}"
                       class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                        + New Expense
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8" style="max-width: 1500px;">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="border-b border-gray-200 p-4">
                    <form method="GET" action="{{ route('expenses.index') }}"
                          class="flex flex-wrap items-end gap-3">
                        <div style="min-width: 230px; flex: 1 1 260px;">
                            <label for="search" class="mb-1 block text-xs font-medium text-gray-600">Search</label>
                            <input id="search" type="text" name="search" value="{{ $search }}"
                                   placeholder="Expense, category, account..."
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        </div>

                        <div style="width: 190px;">
                            <label for="expense_category_id" class="mb-1 block text-xs font-medium text-gray-600">Expense Category</label>
                            <select id="expense_category_id" name="expense_category_id"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                            @selected((string) $categoryId === (string) $category->id)>
                                        {{ $category->name }}
                                        @if ($category->code) ({{ $category->code }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="width: 190px;">
                            <label for="account_id" class="mb-1 block text-xs font-medium text-gray-600">Account</label>
                            <select id="account_id" name="account_id"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="">All Accounts</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                            @selected((string) $accountId === (string) $account->id)>
                                        {{ $account->name }} ({{ $account->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="width: 170px;">
                            <label for="financial_year" class="mb-1 block text-xs font-medium text-gray-600">Financial Year</label>
                            <select id="financial_year" name="financial_year"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="all" @selected(strtolower($financialYear) === 'all')>All FY</option>
                                @foreach ($financialYears as $fy)
                                    <option value="{{ $fy }}" @selected($financialYear === $fy)>
                                        {{ $fy }}@if ($fy === $currentFinancialYear) (Current)@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="width: 145px;">
                            <label for="status" class="mb-1 block text-xs font-medium text-gray-600">Status</label>
                            <select id="status" name="status"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="active" @selected($status === 'active')>Active</option>
                                <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                                <option value="all" @selected($status === 'all')>All</option>
                            </select>
                        </div>

                        <div style="width: 160px;">
                            <label for="date_from" class="mb-1 block text-xs font-medium text-gray-600">From Date</label>
                            <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        </div>

                        <div style="width: 160px;">
                            <label for="date_to" class="mb-1 block text-xs font-medium text-gray-600">To Date</label>
                            <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        </div>

                        <button type="submit"
                                class="rounded-md bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                            Filter
                        </button>
                        <a href="{{ route('expenses.index') }}"
                           class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                        <div class="ml-auto flex flex-wrap items-center gap-x-5 gap-y-1 pb-0.5 text-sm">
                            <span class="text-gray-600">
                                Total Records: <strong class="font-semibold text-gray-900">{{ number_format($totalRecords) }}</strong>
                            </span>
                            <span class="text-gray-600">
                                Total Amount: <strong class="font-semibold text-red-700">Rs. {{ number_format($totalAmount) }}</strong>
                            </span>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead class="bg-gray-50">
                            <tr class="border-b border-gray-200 text-left text-sm text-gray-600">
                                <th class="px-4 py-3">Expense Number</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Paid From Account</th>
                                <th class="px-4 py-3 text-right">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($expenses as $expense)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="font-semibold text-gray-900">{{ $expense->expense_number }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $expense->date_ad?->format('Y-m-d') ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $expense->date_bs ?: '-' }}</div>
                                        <div class="text-xs text-gray-400">FY {{ $expense->financial_year ?: '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $expense->category?->name ?? '-' }}</div>
                                        @if ($expense->category?->code)
                                            <div class="text-xs text-gray-500">{{ $expense->category->code }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $expense->account?->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $expense->account?->code ?? '-' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-red-700">
                                        -{{ number_format($expense->amount) }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        @if ($expense->status === 'active')
                                            <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Active</span>
                                        @else
                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <a href="{{ route('expenses.show', $expense) }}"
                                           class="text-sm font-medium text-blue-600 hover:underline">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                        No expense transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($expenses->hasPages())
                    <div class="border-t border-gray-100 p-4">{{ $expenses->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
