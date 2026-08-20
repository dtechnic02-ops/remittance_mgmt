<x-app-layout>

    @php
        $isBuy =
            $shareTransaction->transaction_type
            === \App\Models\ShareTransaction::TYPE_BUY;

        $isWithdraw =
            $shareTransaction->transaction_type
            === \App\Models\ShareTransaction::TYPE_WITHDRAW;

        $isTransfer =
            $shareTransaction->transaction_type
            === \App\Models\ShareTransaction::TYPE_TRANSFER;
    @endphp

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Edit Share Transaction
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $shareTransaction->transaction_number }}
                </p>
            </div>

            <a
                href="{{ route('share-transactions.show', $shareTransaction) }}"
                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Back
            </a>

        </div>

    </x-slot>


    <div class="py-6">

        <div
            class="mx-auto w-full px-4 sm:px-6 lg:px-8"
            style="max-width: 900px;"
        >

            @if ($errors->any())

                <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">

                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach

                </div>

            @endif


            <div class="overflow-hidden rounded-xl bg-white shadow-sm">


                {{-- Transaction Information --}}
                <div class="border-b border-gray-200 p-5">

                    <div class="mb-5 flex flex-wrap items-center gap-2">

                        @if ($isBuy)

                            <span class="rounded-md bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                                BUY
                            </span>

                        @elseif ($isWithdraw)

                            <span class="rounded-md bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                WITHDRAW
                            </span>

                        @else

                            <span class="rounded-md bg-purple-100 px-3 py-1 text-xs font-bold text-purple-700">
                                TRANSFER
                            </span>

                        @endif

                        <span class="rounded-md bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Active
                        </span>

                    </div>


                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">


                        {{-- Shareholder / From --}}
                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                @if ($isTransfer)
                                    From Shareholder
                                @else
                                    Shareholder
                                @endif
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $shareTransaction->shareholder?->code ?? '-' }}
                                -
                                {{ $shareTransaction->shareholder?->name ?? '-' }}
                            </div>

                        </div>


                        @if ($isTransfer)

                            <div>

                                <div class="text-xs font-medium uppercase text-gray-500">
                                    To Shareholder
                                </div>

                                <div class="mt-1 font-semibold text-gray-900">
                                    {{ $shareTransaction->toShareholder?->code ?? '-' }}
                                    -
                                    {{ $shareTransaction->toShareholder?->name ?? '-' }}
                                </div>

                            </div>

                        @else

                            <div>

                                <div class="text-xs font-medium uppercase text-gray-500">
                                    Account
                                </div>

                                <div class="mt-1 font-semibold text-gray-900">
                                    {{ $shareTransaction->account?->code ?? '-' }}
                                    -
                                    {{ $shareTransaction->account?->name ?? '-' }}
                                </div>

                            </div>

                        @endif


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Kitta
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ number_format($shareTransaction->kitta) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Per Kitta
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ number_format($shareTransaction->per_kitta_value) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                @if ($isTransfer)
                                    Share Value
                                @else
                                    Total Amount
                                @endif
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ number_format($shareTransaction->total_amount) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Reference
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $shareTransaction->reference ?: '-' }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Edit Form --}}
                <form
                    method="POST"
                    action="{{ route(
                        'share-transactions.update',
                        $shareTransaction
                    ) }}"
                    class="p-5"
                >

                    @csrf
                    @method('PUT')


                    <div class="space-y-5">


                        {{-- Date --}}
                        <div>

                            <label
                                for="date_ad"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Date AD *
                            </label>

                            <input
                                id="date_ad"
                                type="date"
                                name="date_ad"
                                value="{{ old(
                                    'date_ad',
                                    $shareTransaction->date_ad?->format('Y-m-d')
                                ) }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                Nepali Date and Financial Year will be recalculated automatically.
                            </p>

                        </div>


                        {{-- Current BS / FY --}}
                        <div class="grid gap-4 rounded-lg bg-gray-50 p-4 sm:grid-cols-2">

                            <div>

                                <div class="text-xs font-medium uppercase text-gray-500">
                                    Current Nepali Date
                                </div>

                                <div class="mt-1 font-semibold text-gray-900">
                                    {{ $shareTransaction->date_bs ?: '-' }}
                                </div>

                            </div>


                            <div>

                                <div class="text-xs font-medium uppercase text-gray-500">
                                    Current Financial Year
                                </div>

                                <div class="mt-1 font-semibold text-gray-900">
                                    {{ $shareTransaction->financial_year ?: '-' }}
                                </div>

                            </div>

                        </div>


                        {{-- Note --}}
                        <div>

                            <label
                                for="note"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Note
                            </label>

                            <textarea
                                id="note"
                                name="note"
                                rows="5"
                                maxlength="2000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >{{ old('note', $shareTransaction->note) }}</textarea>

                        </div>


                        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">

                            Only <strong>Date</strong> and <strong>Note</strong> can be edited.

                            <div class="mt-2">
                                Shareholder, Account/Receiver, Kitta, Per Kitta Value,
                                Amount and Transaction Type cannot be changed.
                            </div>

                        </div>


                        <div class="flex justify-end gap-3">

                            <a
                                href="{{ route(
                                    'share-transactions.show',
                                    $shareTransaction
                                ) }}"
                                class="rounded-md border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="rounded-md bg-gray-800 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                            >
                                Save Changes
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>