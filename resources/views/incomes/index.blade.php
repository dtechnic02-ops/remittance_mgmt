<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Income
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Income Transactions
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                @if (
                    auth()->user()->isAdmin()
                    || auth()->user()->hasPermission('income-category.view')
                )

                    <a
                        href="{{ route('income-categories.index') }}"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Income Categories
                    </a>

                @endif


                @if (
                    auth()->user()->isAdmin()
                    || auth()->user()->hasPermission('income.create')
                )

                    <a href="{{ route('incomes.import.template') }}" class="text-sm text-gray-600 hover:underline">
                        Download Template
                    </a>

                    <a href="{{ route('incomes.import.create') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Import Income
                    </a>

                    <a
                        href="{{ route('incomes.create') }}"
                        class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                    >
                        + New Income
                    </a>

                @endif

            </div>

        </div>

    </x-slot>


    <div class="py-6">

        <div
            class="mx-auto w-full px-4 sm:px-6 lg:px-8"
            style="max-width: 1500px;"
        >

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


                {{-- Filters --}}
                <div class="border-b border-gray-200 p-4">

                    <form
                        method="GET"
                        action="{{ route('incomes.index') }}"
                        class="flex flex-wrap items-end gap-3"
                    >

                        {{-- Search --}}
                        <div style="min-width: 230px; flex: 1 1 260px;">

                            <label
                                for="search"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                Search
                            </label>

                            <input
                                id="search"
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Income, category, account..."
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                        </div>


                        {{-- Category --}}
                        <div style="width: 190px;">

                            <label
                                for="income_category_id"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                Category
                            </label>

                            <select
                                id="income_category_id"
                                name="income_category_id"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                                <option value="">
                                    All Categories
                                </option>

                                @foreach ($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(
                                            (string) $categoryId
                                            === (string) $category->id
                                        )
                                    >
                                        {{ $category->name }}

                                        @if ($category->code)
                                            ({{ $category->code }})
                                        @endif
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Account --}}
                        <div style="width: 190px;">

                            <label
                                for="account_id"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                Account
                            </label>

                            <select
                                id="account_id"
                                name="account_id"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                                <option value="">
                                    All Accounts
                                </option>

                                @foreach ($accounts as $account)

                                    <option
                                        value="{{ $account->id }}"
                                        @selected(
                                            (string) $accountId
                                            === (string) $account->id
                                        )
                                    >
                                        {{ $account->name }}
                                        ({{ $account->code }})
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Financial Year --}}
                        <div style="width: 170px;">

                            <label
                                for="financial_year"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                Financial Year
                            </label>

                            <select
                                id="financial_year"
                                name="financial_year"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                                <option
                                    value="all"
                                    @selected(
                                        strtolower($financialYear)
                                        === 'all'
                                    )
                                >
                                    All FY
                                </option>

                                @foreach ($financialYears as $fy)

                                    <option
                                        value="{{ $fy }}"
                                        @selected(
                                            $financialYear === $fy
                                        )
                                    >
                                        {{ $fy }}

                                        @if ($fy === $currentFinancialYear)
                                            (Current)
                                        @endif
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Status --}}
                        <div style="width: 145px;">

                            <label
                                for="status"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                                <option
                                    value="active"
                                    @selected($status === 'active')
                                >
                                    Active
                                </option>

                                <option
                                    value="cancelled"
                                    @selected($status === 'cancelled')
                                >
                                    Cancelled
                                </option>

                                <option
                                    value="all"
                                    @selected($status === 'all')
                                >
                                    All
                                </option>

                            </select>

                        </div>


                        {{-- From Date --}}
                        <div style="width: 160px;">

                            <label
                                for="date_from"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                From Date
                            </label>

                            <input
                                id="date_from"
                                type="date"
                                name="date_from"
                                value="{{ $dateFrom }}"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                        </div>


                        {{-- To Date --}}
                        <div style="width: 160px;">

                            <label
                                for="date_to"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                To Date
                            </label>

                            <input
                                id="date_to"
                                type="date"
                                name="date_to"
                                value="{{ $dateTo }}"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                        </div>


                        <button
                            type="submit"
                            class="rounded-md bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700"
                        >
                            Filter
                        </button>


                        <a
                            href="{{ route('incomes.index') }}"
                            class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>

                    </form>

                </div>


                {{-- Table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead class="bg-gray-50">

                            <tr class="border-b border-gray-200 text-left text-sm text-gray-600">

                                <th class="px-4 py-3">
                                    Income
                                </th>

                                <th class="px-4 py-3">
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    Category
                                </th>

                                <th class="px-4 py-3">
                                    Received In
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Amount
                                </th>

                                <th class="px-4 py-3">
                                    Status
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($incomes as $income)

                                <tr class="border-b border-gray-100 hover:bg-gray-50">


                                    {{-- Income --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <div class="font-semibold text-gray-900">
                                            {{ $income->income_number }}
                                        </div>

                                    </td>


                                    {{-- Date --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <div class="font-medium text-gray-900">
                                            {{ $income->date_ad?->format('Y-m-d') ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $income->date_bs ?: '-' }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            FY {{ $income->financial_year ?: '-' }}
                                        </div>

                                    </td>


                                    {{-- Category --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium text-gray-900">
                                            {{ $income->category?->name ?? '-' }}
                                        </div>

                                        @if ($income->category?->code)

                                            <div class="text-xs text-gray-500">
                                                {{ $income->category->code }}
                                            </div>

                                        @endif

                                    </td>


                                    {{-- Account --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium text-gray-900">
                                            {{ $income->account?->name ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $income->account?->code ?? '-' }}
                                        </div>

                                    </td>


                                    {{-- Amount --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-green-700">
                                        +{{ number_format($income->amount) }}
                                    </td>


                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($income->status === 'active')

                                            <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                Cancelled
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right">

                                        <a
                                            href="{{ route(
                                                'incomes.show',
                                                $income
                                            ) }}"
                                            class="text-sm font-medium text-blue-600 hover:underline"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="px-4 py-12 text-center text-gray-500"
                                    >
                                        No income transactions found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if ($incomes->hasPages())

                    <div class="border-t border-gray-100 p-4">
                        {{ $incomes->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>
