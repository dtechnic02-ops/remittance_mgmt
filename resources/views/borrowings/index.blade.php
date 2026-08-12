<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Borrowings
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Short-Term Borrow / Repay Transactions
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('lenders.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700">
                    Lenders
                </a>

                <a href="{{ route('borrowings.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white rounded-md">
                    + New Borrow / Repay
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

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

                {{-- Search --}}
                <div class="p-6 border-b">
                    <form method="GET"
                          action="{{ route('borrowings.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search transaction, lender, account or reference..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('borrowings.index') }}"
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
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    Lender
                                </th>

                                <th class="px-4 py-3">
                                    Type
                                </th>

                                <th class="px-4 py-3">
                                    Account
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Amount
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Outstanding
                                </th>

                                <th class="px-4 py-3">
                                    Status
                                </th>

                                <th class="px-4 py-3">
                                    Attachment
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($transactions as $transaction)

                                @php
                                    $borrowed = \App\Models\Borrowing::query()
                                        ->where('lender_id', $transaction->lender_id)
                                        ->where('status', 'active')
                                        ->where('transaction_type', 'borrow')
                                        ->sum('amount');

                                    $repaid = \App\Models\Borrowing::query()
                                        ->where('lender_id', $transaction->lender_id)
                                        ->where('status', 'active')
                                        ->where('transaction_type', 'repay')
                                        ->sum('amount');

                                    $outstanding =
                                        (int) $borrowed - (int) $repaid;
                                @endphp

                                <tr class="border-b hover:bg-gray-50">

                                    {{-- Transaction --}}
                                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                                        {{ $transaction->transaction_number }}

                                        @if ($transaction->reference)
                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ $transaction->reference }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $transaction->date_ad->format('Y-m-d') }}

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->date_bs }}
                                        </div>
                                    </td>

                                    {{-- Lender --}}
                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $transaction->lender->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $transaction->lender->code }}

                                            @if ($transaction->lender->mobile)
                                                - {{ $transaction->lender->mobile }}
                                            @endif
                                        </div>

                                    </td>

                                    {{-- Type --}}
                                    <td class="px-4 py-3 whitespace-nowrap">

                                        @if ($transaction->transaction_type === 'borrow')

                                            <span class="inline-flex rounded-md bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
                                                BORROW
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">
                                                REPAY
                                            </span>

                                        @endif

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

                                    {{-- Amount --}}
                                    <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">

                                        @if ($transaction->transaction_type === 'borrow')

                                            <span class="text-green-700">
                                                +{{ number_format($transaction->amount) }}
                                            </span>

                                        @else

                                            <span class="text-red-700">
                                                -{{ number_format($transaction->amount) }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Outstanding --}}
                                    <td class="px-4 py-3 text-right font-bold whitespace-nowrap">

                                        @if ($outstanding > 0)
                                            <span class="text-red-700">
                                                {{ number_format($outstanding) }}
                                            </span>
                                        @else
                                            <span class="text-green-700">
                                                0
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
                                                Cancelled
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Attachment --}}
                                    <td class="px-4 py-3">

                                        @if ($transaction->attachment)

                                            <a href="{{ Storage::disk('public')->url($transaction->attachment) }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="text-blue-600 hover:underline">
                                                View
                                            </a>

                                        @else
                                            <span class="text-gray-400">
                                                -
                                            </span>
                                        @endif

                                    </td>

                                    {{-- Action --}}
                                    <td class="px-4 py-3 text-right min-w-[220px]">

                                        @if ($transaction->status === 'cancelled')

                                            <div class="text-xs text-red-700">
                                                Cancelled

                                                @if ($transaction->cancellation_reason)
                                                    <div class="mt-1 text-gray-500">
                                                        {{ \Illuminate\Support\Str::limit(
                                                            $transaction->cancellation_reason,
                                                            35
                                                        ) }}
                                                    </div>
                                                @endif
                                            </div>

                                        @elseif (
                                            auth()->user()->isAdmin()
                                            || auth()->user()->hasPermission('borrowing.cancel')
                                        )

                                            <form method="POST"
                                                  action="{{ route('borrowings.cancel', $transaction) }}"
                                                  onsubmit="return confirm('Are you sure you want to cancel this transaction?');">

                                                @csrf

                                                <div class="flex items-center justify-end gap-2">

                                                    <input type="text"
                                                           name="cancellation_reason"
                                                           required
                                                           maxlength="1000"
                                                           placeholder="Cancel reason"
                                                           class="w-32 rounded-md border-gray-300 text-xs shadow-sm">

                                                    <button type="submit"
                                                            class="px-3 py-2 bg-red-600 text-white text-xs rounded-md">
                                                        Cancel
                                                    </button>

                                                </div>

                                            </form>

                                        @else
                                            <span class="text-gray-400">
                                                -
                                            </span>
                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="10"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No borrowing transactions found.
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