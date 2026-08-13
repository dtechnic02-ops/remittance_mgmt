<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">My Shareholding</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (! $shareholder)
                <div class="rounded-lg bg-yellow-50 p-6 text-yellow-800">
                    Your shareholder profile is not linked yet. Please contact the administrator.
                </div>
            @else
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="grid gap-5 md:grid-cols-3">
                        <div><div class="text-sm text-gray-500">Name</div><div class="font-semibold">{{ $shareholder->name }}</div></div>
                        <div><div class="text-sm text-gray-500">Code</div><div class="font-semibold">{{ $shareholder->code }}</div></div>
                        <div><div class="text-sm text-gray-500">Status</div><div class="font-semibold">{{ $shareholder->is_active ? 'Active' : 'Inactive' }}</div></div>
                        <div><div class="text-sm text-gray-500">Current Kitta</div><div class="text-xl font-bold">{{ number_format($shareholder->kitta) }}</div></div>
                        <div><div class="text-sm text-gray-500">Per Kitta Value</div><div class="text-xl font-bold">{{ number_format($shareholder->per_kitta_value) }}</div></div>
                        <div><div class="text-sm text-gray-500">Total Investment</div><div class="text-xl font-bold">{{ number_format($shareholder->total_investment) }}</div></div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold">My Share Transaction History</h3>
                    <table class="min-w-full border-collapse">
                        <thead><tr class="border-b bg-gray-50 text-left">
                            <th class="px-3 py-2">Transaction</th><th class="px-3 py-2">Date AD</th>
                            <th class="px-3 py-2">Date BS</th><th class="px-3 py-2">Type</th>
                            <th class="px-3 py-2 text-right">Kitta</th><th class="px-3 py-2 text-right">Per Kitta</th>
                            <th class="px-3 py-2 text-right">Total</th><th class="px-3 py-2">Status</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr class="border-b">
                                    <td class="px-3 py-2">{{ $transaction->transaction_number }}</td>
                                    <td class="px-3 py-2">{{ $transaction->date_ad->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2">{{ $transaction->date_bs }}</td>
                                    <td class="px-3 py-2">{{ strtoupper($transaction->transaction_type) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($transaction->kitta) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($transaction->per_kitta_value) }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format($transaction->total_amount) }}</td>
                                    <td class="px-3 py-2">{{ ucfirst($transaction->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-3 py-8 text-center text-gray-500">No share transactions found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-5">{{ $transactions->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
