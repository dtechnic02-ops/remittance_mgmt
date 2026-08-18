<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    My Shareholding
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Share balance, investment and transaction history
                </p>
            </div>

            @if (
                $shareholder && $shareholder->is_active
            )
                <a href="{{ route('share-transfers.create') }}"
                   class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                    Transfer Share
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full space-y-6 px-4 sm:px-6 lg:px-8">

            @if (! $shareholder)

                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6 text-yellow-800">
                    Your shareholder profile is not linked yet.
                    Please contact the administrator.
                </div>

            @else

                {{-- Profile --}}
                <div class="overflow-hidden rounded-xl bg-white shadow-sm">

                    <div class="p-5 sm:p-6">

                        <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                            <div class="flex items-center gap-4">

                                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-100">

                                    @if ($shareholder->photo)

                                        <img
                                            src="{{ route('shareholder.documents.show', [$shareholder, 'photo']) }}"
                                            alt="{{ $shareholder->name }}"
                                            class="h-full w-full object-cover"
                                        >

                                    @else

                                        <div class="text-2xl font-bold text-gray-500">
                                            {{ strtoupper(substr($shareholder->name, 0, 1)) }}
                                        </div>

                                    @endif

                                </div>

                                <div>

                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                        Shareholder
                                    </div>

                                    <div class="mt-1 text-xl font-bold text-gray-900">
                                        {{ $shareholder->name }}
                                    </div>

                                    <div class="mt-1 text-sm text-gray-500">
                                        Code: {{ $shareholder->code }}
                                    </div>

                                </div>

                            </div>

                            <div>

                                @if ($shareholder->is_active)

                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">
                                        Active
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700">
                                        Inactive
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


               {{-- Summary --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div class="text-sm font-medium text-gray-500">
            Current Kitta
        </div>

        <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ number_format($shareholder->kitta) }}
        </div>

        <div class="mt-1 text-xs text-gray-400">
            Current share quantity
        </div>
    </div>

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div class="text-sm font-medium text-gray-500">
            Per Kitta Value
        </div>

        <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ number_format($shareholder->per_kitta_value) }}
        </div>

        <div class="mt-1 text-xs text-gray-400">
            Fixed share value
        </div>
    </div>

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div class="text-sm font-medium text-gray-500">
            Total Investment
        </div>

        <div class="mt-2 text-2xl font-bold text-gray-900">
            {{ number_format($shareholder->total_investment) }}
        </div>

        <div class="mt-1 text-xs text-gray-400">
            Kitta × Per Kitta Value
        </div>
    </div>

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div class="text-sm font-medium text-gray-500">
            My Shareholding
        </div>

        <div class="mt-2 text-2xl font-bold text-blue-700">
            {{ number_format($shareholdingPercentage, 2) }}%
        </div>

        <div class="mt-1 text-xs text-gray-400">
            {{ number_format($shareholder->kitta) }}
            of
            {{ number_format($totalKitta) }}
            total Kitta
        </div>
    </div>

    <div class="rounded-xl bg-white p-4 shadow-sm">
        <div class="text-sm font-medium text-gray-500">
            My Profit / Loss
        </div>

        <div class="mt-2 text-2xl font-bold
            {{ $myProfitLoss < 0
                ? 'text-red-600'
                : ($myProfitLoss > 0
                    ? 'text-green-600'
                    : 'text-gray-900')
            }}">
            {{ $myProfitLoss > 0 ? '+' : '' }}
            {{ number_format($myProfitLoss, 2) }}
        </div>

        <div class="mt-1 text-xs text-gray-400">
            FY {{ $financialYear }}
        </div>
    </div>

</div>

                </div>


                {{-- Transaction History --}}
                <div class="rounded-xl bg-white shadow-sm">

                    <div class="border-b border-gray-200 p-5 sm:p-6">

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    My Share Transaction History
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Buy, withdraw, sent and received share records
                                </p>
                            </div>

                            @if ($shareholder && $shareholder->is_active)

                                <a href="{{ route('share-transfers.create') }}"
                                   class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    New Transfer
                                </a>

                            @endif

                        </div>

                    </div>

                    <div class="overflow-x-auto">

                        <table class="min-w-full border-collapse">

                            <thead>
                                <tr class="border-b bg-gray-50 text-left text-sm text-gray-600">

                                    <th class="whitespace-nowrap px-5 py-3">
                                        Transaction
                                    </th>

                                    <th class="whitespace-nowrap px-5 py-3">
                                        Date
                                    </th>

                                    <th class="whitespace-nowrap px-5 py-3">
                                        Type
                                    </th>

                                    <th class="whitespace-nowrap px-5 py-3">
                                        Details
                                    </th>

                                    <th class="whitespace-nowrap px-5 py-3 text-right">
                                        Kitta
                                    </th>

                                    <th class="whitespace-nowrap px-5 py-3 text-right">
                                        Per Kitta
                                    </th>

                                    <th class="whitespace-nowrap px-5 py-3 text-right">
                                        Total
                                    </th>

                                    <th class="whitespace-nowrap px-5 py-3">
                                        Status
                                    </th>

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">

                                @forelse ($transactions as $transaction)

                                    @php
                                        $isTransfer =
                                            $transaction->transaction_type === 'transfer';

                                        $isReceived =
                                            $isTransfer &&
                                            (int) $transaction->to_shareholder_id
                                            === (int) $shareholder->id;

                                        $isSent =
                                            $isTransfer &&
                                            (int) $transaction->shareholder_id
                                            === (int) $shareholder->id;
                                    @endphp

                                    <tr class="hover:bg-gray-50">

                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-gray-900">
                                            {{ $transaction->transaction_number }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">

                                            {{ $transaction->date_ad->format('Y-m-d') }}

                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ $transaction->date_bs }}
                                            </div>

                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4">

                                            @if ($transaction->transaction_type === 'buy')

                                                <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
                                                    BUY
                                                </span>

                                            @elseif ($transaction->transaction_type === 'withdraw')

                                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">
                                                    WITHDRAW
                                                </span>

                                            @elseif ($isReceived)

                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-bold text-green-700">
                                                    RECEIVED
                                                </span>

                                            @elseif ($isSent)

                                                <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-bold text-orange-700">
                                                    SENT
                                                </span>

                                            @else

                                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700">
                                                    TRANSFER
                                                </span>

                                            @endif

                                        </td>

                                        <td class="min-w-[180px] px-5 py-4 text-sm text-gray-700">

                                            @if ($isReceived)

                                                From:
                                                <span class="font-semibold">
                                                    {{ $transaction->shareholder?->name ?? '-' }}
                                                </span>

                                            @elseif ($isSent)

                                                To:
                                                <span class="font-semibold">
                                                    {{ $transaction->toShareholder?->name ?? '-' }}
                                                </span>

                                            @elseif ($transaction->transaction_type === 'buy')

                                                Share purchased

                                            @elseif ($transaction->transaction_type === 'withdraw')

                                                Share withdrawn

                                            @else

                                                -

                                            @endif

                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold">

                                            @if ($isReceived || $transaction->transaction_type === 'buy')

                                                <span class="text-green-700">
                                                    +{{ number_format($transaction->kitta) }}
                                                </span>

                                            @elseif ($isSent || $transaction->transaction_type === 'withdraw')

                                                <span class="text-red-700">
                                                    -{{ number_format($transaction->kitta) }}
                                                </span>

                                            @else

                                                {{ number_format($transaction->kitta) }}

                                            @endif

                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            {{ number_format($transaction->per_kitta_value) }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold">
                                            {{ number_format($transaction->total_amount) }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4">

                                            @if ($transaction->status === 'active')

                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                    Active
                                                </span>

                                            @else

                                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="8"
                                            class="px-5 py-12 text-center text-gray-500">
                                            No share transactions found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="border-t border-gray-100 p-5">
                        {{ $transactions->links() }}
                    </div>

                </div>

            @endif

        </div>
    </div>
</x-app-layout>
