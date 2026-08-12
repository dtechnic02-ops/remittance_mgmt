<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Opening Balances
            </h2>

            <a href="{{ route('opening-balances.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Opening Balance
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
                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50 text-left">
                                <th class="px-4 py-3">Account</th>
                                <th class="px-4 py-3">English Date</th>
                                <th class="px-4 py-3">Nepali Date</th>
                                <th class="px-4 py-3">Financial Year</th>
                                <th class="px-4 py-3 text-right">Amount</th>
                                <th class="px-4 py-3">Created By</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($openingBalances as $openingBalance)
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-medium">
                                        {{ $openingBalance->account->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $openingBalance->date_ad->format('Y-m-d') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $openingBalance->date_bs }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $openingBalance->financial_year }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold
                                        {{ $openingBalance->amount < 0 ? 'text-red-600' : '' }}">
                                        {{ number_format($openingBalance->amount) }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $openingBalance->creator?->name ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('opening-balances.edit', $openingBalance) }}"
                                           class="text-indigo-600">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"
                                        class="px-4 py-8 text-center text-gray-500">
                                        No Opening Balances found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $openingBalances->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>