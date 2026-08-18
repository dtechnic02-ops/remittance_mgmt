<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Share Transfers
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Shareholder to Shareholder Kitta Transfer
                </p>
            </div>

            @if ($canCreate)
                <a href="{{ route('share-transfers.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white rounded-md">
                    + New Share Transfer
                </a>
            @endif
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

                                <th class="px-4 py-3">
                                    Transaction
                                </th>

                                <th class="px-4 py-3">
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    From Shareholder
                                </th>

                                <th class="px-4 py-3">
                                    To Shareholder
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Kitta
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Per Kitta
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Share Value
                                </th>

                                <th class="px-4 py-3">
                                    Reference
                                </th>

                                <th class="px-4 py-3">
                                    Status
                                </th>

                                <th class="px-4 py-3">
                                    Created By
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($transactions as $transaction)

                                <tr class="border-b hover:bg-gray-50">

                                    {{-- Transaction --}}
                                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                                        {{ $transaction->transaction_number }}

                                        <div class="mt-1">
                                            <span class="inline-flex rounded-md bg-purple-100 px-2.5 py-1 text-xs font-bold text-purple-700">
                                                TRANSFER
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-4 py-3 whitespace-nowrap">

                                        {{ $transaction->date_ad->format('Y-m-d') }}

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->date_bs }}
                                        </div>

                                        <div class="text-xs text-gray-400">
                                            FY {{ $transaction->financial_year }}
                                        </div>

                                    </td>

                                    {{-- From --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $transaction->shareholder?->name ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->shareholder?->code ?? '-' }}
                                        </div>

                                    </td>

                                    {{-- To --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $transaction->toShareholder?->name ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->toShareholder?->code ?? '-' }}
                                        </div>

                                    </td>

                                    {{-- Kitta --}}
                                    <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">
                                        {{ number_format($transaction->kitta) }}
                                    </td>

                                    {{-- Per Kitta --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        {{ number_format($transaction->per_kitta_value) }}
                                    </td>

                                    {{-- Share Value --}}
                                    <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">
                                        {{ number_format($transaction->total_amount) }}
                                    </td>

                                    {{-- Reference --}}
                                    <td class="px-4 py-3">
                                        {{ $transaction->reference ?: '-' }}
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

                                    {{-- Created By --}}
                                    <td class="px-4 py-3 whitespace-nowrap">

                                        {{ $transaction->creator?->name ?? '-' }}

                                        @if ($transaction->note)
                                            <div class="mt-1 text-xs text-gray-500"
                                                 title="{{ $transaction->note }}">
                                                Note available
                                            </div>
                                        @endif

                                        @if ($transaction->attachment)
                                            <div class="mt-2">
                                                <a href="{{ route('share-transfers.attachment', $transaction) }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="text-xs font-medium text-blue-600 hover:underline">
                                                    View attachment
                                                </a>
                                            </div>
                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="10"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No share transfers found.
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
