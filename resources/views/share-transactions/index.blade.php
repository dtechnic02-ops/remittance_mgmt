<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Share Transactions
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Buy Kitta / Withdraw Share
                </p>
            </div>

            <a href="{{ route('share-transactions.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Share Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Search --}}
                <div class="p-6 border-b">
                    <form method="GET"
                          action="{{ route('share-transactions.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search transaction, shareholder, account or reference..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('share-transactions.index') }}"
                               class="px-5 py-2 border border-gray-300 rounded-md text-center">
                                Clear
                            </a>
                        @endif

                    </form>
                </div>

                {{-- Table --}}
                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>
                            <tr class="border-b bg-gray-50 text-left">

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
                                    Shareholder
                                </th>

                                <th class="px-4 py-3">
                                    Account
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Kitta
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Per Kitta
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Total Amount
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

                                <tr class="border-b hover:bg-gray-50">

                                    {{-- Transaction --}}
                                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                                        {{ $transaction->transaction_number }}
                                    </td>

                                    {{-- Type --}}
                                    <td class="px-4 py-3 whitespace-nowrap">

                                        @if ($transaction->transaction_type === 'buy')

                                            <span class="inline-flex rounded-md bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
                                                BUY
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">
                                                WITHDRAW
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Date --}}
                                    <td class="px-4 py-3 whitespace-nowrap">

                                        {{ $transaction->date_ad->format('Y-m-d') }}

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->date_bs }}
                                        </div>

                                    </td>

                                    {{-- Shareholder --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $transaction->shareholder->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->shareholder->code }}
                                        </div>

                                    </td>

                                    {{-- Account --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $transaction->account->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->account->code }}
                                        </div>

                                    </td>

                                    {{-- Kitta --}}
                                    <td class="px-4 py-3 text-right font-semibold">
                                        {{ number_format($transaction->kitta) }}
                                    </td>

                                    {{-- Per Kitta --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        {{ number_format($transaction->per_kitta_value) }}
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">

                                        @if ($transaction->transaction_type === 'buy')

                                            <span class="text-green-700">
                                                +{{ number_format($transaction->total_amount) }}
                                            </span>

                                        @else

                                            <span class="text-red-700">
                                                -{{ number_format($transaction->total_amount) }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3">

                                        @if ($transaction->status === 'active')

                                            <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                {{ ucfirst($transaction->status) }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Action --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap">

                                        <a href="{{ route('share-transactions.show', $transaction) }}"
                                           class="text-blue-600 hover:underline">
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="10"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No share transactions found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>