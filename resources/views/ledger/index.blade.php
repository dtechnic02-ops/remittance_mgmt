<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Financial Ledger
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Account Transaction Ledger
                </p>
            </div>

        </div>

    </x-slot>


    <div class="py-6">

        <div
            class="mx-auto w-full px-4 sm:px-6 lg:px-8"
            style="max-width: 1500px;"
        >

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
                        action="{{ route('ledger.index') }}"
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
                                placeholder="Transaction, account, component..."
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

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


                        {{-- Transaction Type --}}
                        <div style="width: 185px;">

                            <label
                                for="transaction_type"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                Transaction Type
                            </label>

                            <select
                                id="transaction_type"
                                name="transaction_type"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                                <option value="">
                                    All Transactions
                                </option>

                                @foreach ($transactionTypes as $ledgerType)

                                    <option
                                        value="{{ $ledgerType }}"
                                        @selected(
                                            $transactionType
                                            === $ledgerType
                                        )
                                    >
                                        {{ ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $ledgerType
                                            )
                                        ) }}
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
                        <div style="width: 140px;">

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
                                    value="posted"
                                    @selected(
                                        $status === 'posted'
                                    )
                                >
                                    Posted
                                </option>

                                <option
                                    value="reversal"
                                    @selected(
                                        $status === 'reversal'
                                    )
                                >
                                    Reversal
                                </option>

                                <option
                                    value="all"
                                    @selected(
                                        $status === 'all'
                                    )
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
                            href="{{ route('ledger.index') }}"
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
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    Transaction
                                </th>

                                <th class="px-4 py-3">
                                    Account
                                </th>

                                <th class="px-4 py-3">
                                    Type
                                </th>

                                <th class="px-4 py-3">
                                    Component
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Increase
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Decrease
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Balance
                                </th>

                                <th class="px-4 py-3">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($entries as $entry)

                                <tr class="border-b border-gray-100 hover:bg-gray-50">


                                    {{-- Date --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <div class="font-medium text-gray-900">
                                            {{ $entry->date_ad?->format('Y-m-d') ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $entry->date_bs ?: '-' }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            FY {{ $entry->financial_year ?: '-' }}
                                        </div>

                                    </td>


                                    {{-- Transaction --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <div class="font-semibold text-gray-900">
                                            {{ $entry->transaction_number }}
                                        </div>

                                    </td>


                                    {{-- Account --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium text-gray-900">
                                            {{ $entry->account?->name ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $entry->account?->code ?? '-' }}
                                        </div>

                                    </td>


                                    {{-- Type --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <span class="inline-flex rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $entry->transaction_type
                                                )
                                            ) }}

                                        </span>

                                    </td>


                                    {{-- Component --}}
                                    <td class="px-4 py-3">

                                        {{ $entry->component
                                            ? ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $entry->component
                                                )
                                            )
                                            : '-'
                                        }}

                                    </td>


                                    {{-- Increase --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right">

                                        @if ($entry->direction === 'increase')

                                            <span class="font-semibold text-green-700">
                                                +{{ number_format($entry->amount) }}
                                            </span>

                                        @else
                                            -
                                        @endif

                                    </td>


                                    {{-- Decrease --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right">

                                        @if ($entry->direction === 'decrease')

                                            <span class="font-semibold text-red-700">
                                                -{{ number_format($entry->amount) }}
                                            </span>

                                        @else
                                            -
                                        @endif

                                    </td>


                                    {{-- Balance --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-900">
                                        {{ number_format($entry->balance_after) }}
                                    </td>


                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($entry->is_reversal)

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                Reversal
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Posted
                                            </span>

                                        @endif

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="9"
                                        class="px-4 py-12 text-center text-gray-500"
                                    >
                                        No ledger entries found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if ($entries->hasPages())

                    <div class="border-t border-gray-100 p-4">
                        {{ $entries->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>