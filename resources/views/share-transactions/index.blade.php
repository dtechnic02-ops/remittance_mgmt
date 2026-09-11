<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Share Transactions
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Buy / Withdraw / Share Transfer
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('shareholders.index') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Shareholders
                </a>

                @if ($canCreateShareTransaction || $canCreateTransfer)
                    <a
                        href="{{ route('share-transactions.create') }}"
                        class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                    >
                        + New Share Transaction
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
                        action="{{ route('share-transactions.index') }}"
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
                                placeholder="Transaction, shareholder, account..."
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                        </div>


                        {{-- Type --}}
                        <div style="width: 155px;">

                            <label
                                for="type"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                Type
                            </label>

                            <select
                                id="type"
                                name="type"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                                <option
                                    value="all"
                                    @selected($type === 'all')
                                >
                                    All
                                </option>

                                @if ($canViewShareTransaction)

                                    <option
                                        value="buy"
                                        @selected($type === 'buy')
                                    >
                                        Buy
                                    </option>

                                    <option
                                        value="withdraw"
                                        @selected($type === 'withdraw')
                                    >
                                        Withdraw
                                    </option>

                                @endif

                                @if ($canViewTransfer)

                                    <option
                                        value="transfer"
                                        @selected($type === 'transfer')
                                    >
                                        Transfer
                                    </option>

                                @endif

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
                                        strtolower($financialYear) === 'all'
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
                                for="from_date"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                From Date
                            </label>

                            <input
                                id="from_date"
                                type="date"
                                name="from_date"
                                value="{{ $fromDate }}"
                                class="w-full rounded-md border-gray-300 text-sm shadow-sm"
                            >

                        </div>


                        {{-- To Date --}}
                        <div style="width: 160px;">

                            <label
                                for="to_date"
                                class="mb-1 block text-xs font-medium text-gray-600"
                            >
                                To Date
                            </label>

                            <input
                                id="to_date"
                                type="date"
                                name="to_date"
                                value="{{ $toDate }}"
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
                            href="{{ route('share-transactions.index') }}"
                            class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>

                        <div class="ml-auto flex flex-wrap items-center gap-x-5 gap-y-1 pb-0.5 text-sm">
                            <span class="text-gray-600">
                                Total Records: <strong class="font-semibold text-gray-900">{{ number_format($totalRecords) }}</strong>
                            </span>
                            <span class="text-gray-600">
                                Total Amount / Value: <strong class="font-semibold text-purple-700">Rs. {{ $totalAmount > 0 ? '+' : '' }}{{ number_format($totalAmount) }}</strong>
                            </span>
                        </div>

                        <button type="submit" name="output" value="print" formtarget="_blank"
                                class="rounded-md border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-50">
                            Print A4
                        </button>
                        <button type="submit" name="output" value="excel"
                                class="rounded-md border border-green-300 px-4 py-2.5 text-sm font-medium text-green-700 hover:bg-green-50">
                            Export Excel
                        </button>

                    </form>

                </div>


                {{-- Table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead class="bg-gray-50">

                            <tr class="border-b border-gray-200 text-left text-sm text-gray-600">

                                <th class="px-4 py-3">
                                    Transaction
                                </th>

                                <th class="px-4 py-3">
                                    Type
                                </th>

                                <th class="px-4 py-3">
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    Shareholder / From
                                </th>

                                <th class="px-4 py-3">
                                    To / Account
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Kitta
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Per Kitta
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Amount / Value
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

                            @forelse ($transactions as $transaction)

                                @php

                                    $isBuy =
                                        $transaction->transaction_type
                                        === \App\Models\ShareTransaction::TYPE_BUY;

                                    $isWithdraw =
                                        $transaction->transaction_type
                                        === \App\Models\ShareTransaction::TYPE_WITHDRAW;

                                    $isTransfer =
                                        $transaction->transaction_type
                                        === \App\Models\ShareTransaction::TYPE_TRANSFER;

                                @endphp


                                <tr class="border-b border-gray-100 hover:bg-gray-50">

                                    {{-- Transaction --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <div class="font-semibold text-gray-900">
                                            {{ $transaction->transaction_number }}
                                        </div>

                                    </td>


                                    {{-- Type --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($isBuy)

                                            <span class="inline-flex rounded-md bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
                                                BUY
                                            </span>

                                        @elseif ($isWithdraw)

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">
                                                WITHDRAW
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-purple-100 px-2.5 py-1 text-xs font-bold text-purple-700">
                                                TRANSFER
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Date --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        <div class="font-medium text-gray-900">
                                            {{ $transaction->date_ad?->format('Y-m-d') ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->date_bs ?: '-' }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            FY {{ $transaction->financial_year ?: '-' }}
                                        </div>

                                    </td>


                                    {{-- Shareholder / From --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium text-gray-900">
                                            {{ $transaction->shareholder?->name ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->shareholder?->code ?? '-' }}
                                        </div>

                                    </td>


                                    {{-- To / Account --}}
                                    <td class="px-4 py-3">

                                        @if ($isTransfer)

                                            <div class="font-medium text-gray-900">
                                                {{ $transaction->toShareholder?->name ?? '-' }}
                                            </div>

                                            <div class="text-xs text-purple-600">
                                                To:
                                                {{ $transaction->toShareholder?->code ?? '-' }}
                                            </div>

                                        @else

                                            <div class="font-medium text-gray-900">
                                                {{ $transaction->account?->name ?? '-' }}
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                {{ $transaction->account?->code ?? '-' }}
                                            </div>

                                        @endif

                                    </td>


                                    {{-- Kitta --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">
                                        {{ number_format($transaction->kitta) }}
                                    </td>


                                    {{-- Per Kitta --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        {{ number_format($transaction->per_kitta_value) }}
                                    </td>


                                    {{-- Amount --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">

                                        @if ($isBuy)

                                            <span class="text-green-700">
                                                +{{ number_format($transaction->total_amount) }}
                                            </span>

                                        @elseif ($isWithdraw)

                                            <span class="text-red-700">
                                                -{{ number_format($transaction->total_amount) }}
                                            </span>

                                        @else

                                            <span class="text-purple-700">
                                                {{ number_format($transaction->total_amount) }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($transaction->status === 'active')

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
                                                'share-transactions.show',
                                                $transaction
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
                                        colspan="10"
                                        class="px-4 py-12 text-center text-gray-500"
                                    >
                                        No share transactions found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if ($transactions->hasPages())

                    <div class="border-t border-gray-100 p-4">
                        {{ $transactions->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>
