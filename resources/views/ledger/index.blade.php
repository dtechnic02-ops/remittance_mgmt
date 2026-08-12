<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Financial Ledger
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Filters --}}
                <div class="p-6 border-b">

                    <form method="GET"
                          action="{{ route('ledger.index') }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

                            <div>
                                <label for="account_id"
                                       class="block text-sm font-medium text-gray-700">
                                    Account
                                </label>

                                <select id="account_id"
                                        name="account_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">All Accounts</option>

                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                                @selected((string) $accountId === (string) $account->id)>
                                            {{ $account->name }}
                                            ({{ $account->code }})
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div>
                                <label for="transaction_type"
                                       class="block text-sm font-medium text-gray-700">
                                    Transaction Type
                                </label>

                                <select id="transaction_type"
                                        name="transaction_type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        All Transactions
                                    </option>

                                    <option value="remittance"
                                            @selected($transactionType === 'remittance')>
                                        Remittance
                                    </option>

                                    <option value="account_transfer"
                                            @selected($transactionType === 'account_transfer')>
                                        Account Transfer
                                    </option>

                                </select>
                            </div>

                            <div>
                                <label for="search"
                                       class="block text-sm font-medium text-gray-700">
                                    Search
                                </label>

                                <input type="text"
                                       id="search"
                                       name="search"
                                       value="{{ $search }}"
                                       placeholder="Transaction, account, component..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div>
                                <label for="date_from"
                                       class="block text-sm font-medium text-gray-700">
                                    From Date
                                </label>

                                <input type="date"
                                       id="date_from"
                                       name="date_from"
                                       value="{{ $dateFrom }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div>
                                <label for="date_to"
                                       class="block text-sm font-medium text-gray-700">
                                    To Date
                                </label>

                                <input type="date"
                                       id="date_to"
                                       name="date_to"
                                       value="{{ $dateTo }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div class="flex items-end gap-3">

                                <button type="submit"
                                        class="px-5 py-2 bg-gray-800 text-white rounded-md">
                                    Filter
                                </button>

                                <a href="{{ route('ledger.index') }}"
                                   class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                                    Clear
                                </a>

                            </div>

                        </div>
                    </form>

                </div>

                {{-- Ledger Table --}}
                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>
                            <tr class="border-b bg-gray-50 text-left">

                                <th class="px-3 py-3">
                                    Date
                                </th>

                                <th class="px-3 py-3">
                                    Transaction
                                </th>

                                <th class="px-3 py-3">
                                    Account
                                </th>

                                <th class="px-3 py-3">
                                    Type
                                </th>

                                <th class="px-3 py-3">
                                    Component
                                </th>

                                <th class="px-3 py-3 text-right">
                                    Increase
                                </th>

                                <th class="px-3 py-3 text-right">
                                    Decrease
                                </th>

                                <th class="px-3 py-3 text-right">
                                    Balance
                                </th>

                                <th class="px-3 py-3">
                                    Status
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($entries as $entry)

                                <tr class="border-b">

                                    {{-- Date --}}
                                    <td class="px-3 py-3 whitespace-nowrap">

                                        {{ $entry->date_ad->format('Y-m-d') }}

                                        <div class="text-xs text-gray-500">
                                            {{ $entry->date_bs }}
                                        </div>

                                    </td>

                                    {{-- Transaction --}}
                                    <td class="px-3 py-3">

                                        <div class="font-semibold">
                                            {{ $entry->transaction_number }}
                                        </div>

                                        @if ($entry->note)
                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ \Illuminate\Support\Str::limit($entry->note, 40) }}
                                            </div>
                                        @endif

                                    </td>

                                    {{-- Account --}}
                                    <td class="px-3 py-3">

                                        <div class="font-medium">
                                            {{ $entry->account->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $entry->account->code }}
                                        </div>

                                    </td>

                                    {{-- Transaction Type --}}
                                    <td class="px-3 py-3 whitespace-nowrap">

                                        @if ($entry->transaction_type === 'remittance')

                                            <span class="inline-flex rounded-md bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">
                                                Remittance
                                            </span>

                                        @elseif ($entry->transaction_type === 'account_transfer')

                                            <span class="inline-flex rounded-md bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-700">
                                                Transfer
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">
                                                {{ ucfirst(str_replace('_', ' ', $entry->transaction_type)) }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Component --}}
                                    <td class="px-3 py-3">
                                        {{ $entry->component
                                            ? ucfirst(str_replace('_', ' ', $entry->component))
                                            : '-'
                                        }}
                                    </td>

                                    {{-- Increase --}}
                                    <td class="px-3 py-3 text-right whitespace-nowrap">

                                        @if ($entry->direction === 'increase')
                                            <span class="font-semibold text-green-700">
                                                +{{ number_format($entry->amount) }}
                                            </span>
                                        @else
                                            -
                                        @endif

                                    </td>

                                    {{-- Decrease --}}
                                    <td class="px-3 py-3 text-right whitespace-nowrap">

                                        @if ($entry->direction === 'decrease')
                                            <span class="font-semibold text-red-700">
                                                -{{ number_format($entry->amount) }}
                                            </span>
                                        @else
                                            -
                                        @endif

                                    </td>

                                    {{-- Balance --}}
                                    <td class="px-3 py-3 text-right font-semibold whitespace-nowrap">
                                        {{ number_format($entry->balance_after) }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-3 py-3">

                                        @if ($entry->is_reversal)

                                            <span class="inline-flex rounded-md bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">
                                                Reversal
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                                Posted
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="9"
                                        class="px-4 py-12 text-center text-gray-500">
                                        No ledger entries found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $entries->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>