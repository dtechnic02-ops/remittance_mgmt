<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Share Transaction
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $shareTransaction->transaction_number }}
                </p>
            </div>

            <a href="{{ route('share-transactions.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Main Transaction --}}
            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b flex flex-wrap items-center justify-between gap-4">

                    <div>
                        <div class="text-sm text-gray-500">
                            Transaction Number
                        </div>

                        <div class="mt-1 text-xl font-bold text-gray-900">
                            {{ $shareTransaction->transaction_number }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3">

                        @if ($shareTransaction->transaction_type === 'buy')
                            <span class="rounded-md bg-blue-100 px-4 py-2 text-sm font-bold text-blue-700">
                                BUY KITTA
                            </span>
                        @else
                            <span class="rounded-md bg-red-100 px-4 py-2 text-sm font-bold text-red-700">
                                WITHDRAW SHARE
                            </span>
                        @endif

                        @if ($shareTransaction->status === 'active')
                            <span class="rounded-md bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                                Active
                            </span>
                        @else
                            <span class="rounded-md bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                                Cancelled
                            </span>
                        @endif

                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <div>
                            <div class="text-sm text-gray-500">
                                English Date
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->date_ad->format('Y-m-d') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Nepali Date
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->date_bs }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Financial Year
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->financial_year }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Reference
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->reference ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Created By
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->creator?->name ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Created At
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->created_at?->format('Y-m-d H:i') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Shareholder --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Shareholder
                    </h3>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        <div>
                            <div class="text-sm text-gray-500">
                                Code
                            </div>

                            <div class="mt-1 font-semibold">
                                {{ $shareTransaction->shareholder->code }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Name
                            </div>

                            <div class="mt-1 font-semibold">
                                {{ $shareTransaction->shareholder->name }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Current Kitta
                            </div>

                            <div class="mt-1 text-xl font-bold">
                                {{ number_format($shareTransaction->shareholder->kitta) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Current Investment
                            </div>

                            <div class="mt-1 text-xl font-bold">
                                {{ number_format($shareTransaction->shareholder->total_investment) }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- Share Transaction Amount --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Share Details
                    </h3>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div class="rounded-lg bg-gray-50 p-5">
                            <div class="text-sm text-gray-500">
                                Kitta
                            </div>

                            <div class="mt-2 text-2xl font-bold">
                                {{ number_format($shareTransaction->kitta) }}
                            </div>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-5">
                            <div class="text-sm text-gray-500">
                                Per Kitta Value
                            </div>

                            <div class="mt-2 text-2xl font-bold">
                                {{ number_format($shareTransaction->per_kitta_value) }}
                            </div>
                        </div>

                        <div class="rounded-lg bg-gray-50 p-5">
                            <div class="text-sm text-gray-500">
                                Total Amount
                            </div>

                            @if ($shareTransaction->transaction_type === 'buy')
                                <div class="mt-2 text-2xl font-bold text-green-700">
                                    +{{ number_format($shareTransaction->total_amount) }}
                                </div>
                            @else
                                <div class="mt-2 text-2xl font-bold text-red-700">
                                    -{{ number_format($shareTransaction->total_amount) }}
                                </div>
                            @endif
                        </div>

                    </div>

                </div>
            </div>

            {{-- Account Effect --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Payment Account
                    </h3>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>
                            <div class="text-sm text-gray-500">
                                @if ($shareTransaction->transaction_type === 'buy')
                                    Received In
                                @else
                                    Paid From
                                @endif
                            </div>

                            <div class="mt-1 font-semibold">
                                {{ $shareTransaction->account->name }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Account Code
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->account->code }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Account Effect
                            </div>

                            @if ($shareTransaction->transaction_type === 'buy')
                                <div class="mt-1 font-bold text-green-700">
                                    +{{ number_format($shareTransaction->total_amount) }}
                                </div>
                            @else
                                <div class="mt-1 font-bold text-red-700">
                                    -{{ number_format($shareTransaction->total_amount) }}
                                </div>
                            @endif
                        </div>

                    </div>

                </div>
            </div>

            {{-- Financial Effect --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Financial Effect
                    </h3>
                </div>

                <div class="p-6">

                    @if ($shareTransaction->status === 'cancelled')

                        <div class="rounded-md bg-gray-100 p-4 text-sm text-gray-700">
                            This transaction has been cancelled.
                            Its financial effect has been reversed.
                        </div>

                    @elseif ($shareTransaction->transaction_type === 'buy')

                        <div class="space-y-2 text-sm text-gray-700">
                            <div>
                                Shareholder Kitta:
                                <strong>
                                    +{{ number_format($shareTransaction->kitta) }}
                                </strong>
                            </div>

                            <div>
                                Shareholder Investment:
                                <strong>
                                    +{{ number_format($shareTransaction->total_amount) }}
                                </strong>
                            </div>

                            <div>
                                {{ $shareTransaction->account->name }}:
                                <strong>
                                    +{{ number_format($shareTransaction->total_amount) }}
                                </strong>
                            </div>
                        </div>

                    @else

                        <div class="space-y-2 text-sm text-gray-700">
                            <div>
                                Shareholder Kitta:
                                <strong>
                                    -{{ number_format($shareTransaction->kitta) }}
                                </strong>
                            </div>

                            <div>
                                Shareholder Investment:
                                <strong>
                                    -{{ number_format($shareTransaction->total_amount) }}
                                </strong>
                            </div>

                            <div>
                                {{ $shareTransaction->account->name }}:
                                <strong>
                                    -{{ number_format($shareTransaction->total_amount) }}
                                </strong>
                            </div>
                        </div>

                    @endif

                </div>
            </div>

            {{-- Attachment / Note --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b">
                        <h3 class="font-semibold text-lg text-gray-800">
                            Attachment
                        </h3>
                    </div>

                    <div class="p-6">

                        @if ($shareTransaction->attachment)

                            <a href="{{ Storage::disk('public')->url($shareTransaction->attachment) }}"
                               target="_blank"
                               rel="noopener"
                               class="text-blue-600 hover:underline">
                                View Attachment
                            </a>

                        @else

                            <span class="text-gray-500">
                                No attachment
                            </span>

                        @endif

                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b">
                        <h3 class="font-semibold text-lg text-gray-800">
                            Note
                        </h3>
                    </div>

                    <div class="p-6 whitespace-pre-line">
                        {{ $shareTransaction->note ?: '-' }}
                    </div>
                </div>

            </div>

            {{-- Cancellation --}}
            @if ($shareTransaction->status === 'cancelled')

                <div class="mt-6 bg-red-50 border border-red-200 rounded-lg">

                    <div class="p-6 border-b border-red-200">
                        <h3 class="font-semibold text-lg text-red-800">
                            Cancellation Information
                        </h3>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>
                            <div class="text-sm text-red-600">
                                Cancelled By
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->canceller?->name ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-red-600">
                                Cancelled At
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->cancelled_at?->format('Y-m-d H:i') ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-red-600">
                                Reason
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareTransaction->cancellation_reason ?: '-' }}
                            </div>
                        </div>

                    </div>
                </div>

            @elseif (
                auth()->user()->isAdmin()
                || auth()->user()->hasPermission('share-transaction.cancel')
            )

                <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                    <div class="p-6 border-b">
                        <h3 class="font-semibold text-lg text-red-700">
                            Cancel Transaction
                        </h3>
                    </div>

                    <div class="p-6">

                        <form method="POST"
                              action="{{ route('share-transactions.cancel', $shareTransaction) }}"
                              onsubmit="return confirm('Are you sure you want to cancel this transaction?');">

                            @csrf

                            <label for="cancellation_reason"
                                   class="block text-sm font-medium text-gray-700">
                                Cancellation Reason *
                            </label>

                            <textarea id="cancellation_reason"
                                      name="cancellation_reason"
                                      rows="3"
                                      required
                                      maxlength="1000"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('cancellation_reason') }}</textarea>

                            @error('cancellation_reason')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <div class="mt-4 flex justify-end">

                                <button type="submit"
                                        class="px-5 py-2 bg-red-600 text-white rounded-md">
                                    Cancel Transaction
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            @endif

        </div>
    </div>
</x-app-layout>