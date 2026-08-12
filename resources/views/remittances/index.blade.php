<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Remittances
            </h2>

            <a href="{{ route('remittances.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Remittance
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

                <div class="p-6 border-b">
                    <form method="GET"
                          action="{{ route('remittances.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search transaction, customer, mobile or provider reference..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('remittances.index') }}"
                               class="px-5 py-2 border border-gray-300 rounded-md text-center">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">
                      <thead>
    <tr class="border-b bg-gray-50 text-left">
        <th class="px-4 py-3">Transaction</th>
        <th class="px-4 py-3">Type</th>
        <th class="px-4 py-3">Date</th>
        <th class="px-4 py-3">Customer</th>
        <th class="px-4 py-3">Provider</th>
        <th class="px-4 py-3 text-right">Principal</th>
        <th class="px-4 py-3 text-right">Charge</th>
        <th class="px-4 py-3 text-right">Customer Cash</th>
        <th class="px-4 py-3">Status</th>
        <th class="px-4 py-3 text-right">Action</th>
    </tr>
</thead>

                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr class="border-b">

                                    <td class="px-4 py-3 font-semibold">
                                        {{ $transaction->transaction_number }}
                                    </td>
                                    <td class="px-4 py-3">
    @if ($transaction->direction === 'receive')
        <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700">
            RECEIVE
        </span>
    @else
        <span class="inline-flex rounded-md bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
            SEND
        </span>
    @endif
</td>

                                    <td class="px-4 py-3">
                                        {{ $transaction->date_ad->format('Y-m-d') }}
                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->date_bs }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium">
                                            {{ $transaction->customer->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->customer->customer_code }}

                                            @if ($transaction->customer->mobile)
                                                - {{ $transaction->customer->mobile }}
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $transaction->providerAccount->name }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        {{ number_format($transaction->principal_amount) }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        {{ number_format($transaction->service_charge) }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold">
                                        {{ number_format($transaction->total_cash_received) }}
                                    </td>

                                    <td class="px-4 py-3">
                                        @if ($transaction->status === 'active')
                                            <span class="text-green-700 font-medium">
                                                Active
                                            </span>
                                        @else
                                            <span class="text-red-700 font-medium">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('remittances.show', $transaction) }}"
                                           class="text-blue-600">
                                            View
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No remittance transactions found.
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