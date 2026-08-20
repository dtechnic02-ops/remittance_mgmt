<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Share Transaction
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $shareTransaction->transaction_number }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                <a
                    href="{{ route('share-transactions.index') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Back
                </a>

                @if ($shareTransaction->status === 'active')

                    <a
                        href="{{ route(
                            'share-transactions.edit',
                            $shareTransaction
                        ) }}"
                        class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                    >
                        Edit
                    </a>

                    <button
                        type="button"
                        id="cancel_transaction_button"
                        class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500"
                    >
                        Cancel Transaction
                    </button>

                @endif

            </div>

        </div>

    </x-slot>


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


    <div class="py-6">

        <div
            class="mx-auto w-full px-4 sm:px-6 lg:px-8"
            style="max-width: 1100px;"
        >

            @if (session('success'))

                <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>

            @endif


            @if ($errors->any())

                <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">

                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach

                </div>

            @endif


            <div class="overflow-hidden rounded-xl bg-white shadow-sm">


                {{-- Transaction Header --}}
                <div class="border-b border-gray-200 p-5">

                    <div class="flex flex-wrap items-center justify-between gap-4">

                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Transaction Number
                            </div>

                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                {{ $shareTransaction->transaction_number }}
                            </div>

                        </div>


                        <div class="flex items-center gap-2">

                            @if ($isBuy)

                                <span class="inline-flex rounded-md bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                                    BUY
                                </span>

                            @elseif ($isWithdraw)

                                <span class="inline-flex rounded-md bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                    WITHDRAW
                                </span>

                            @else

                                <span class="inline-flex rounded-md bg-purple-100 px-3 py-1 text-xs font-bold text-purple-700">
                                    TRANSFER
                                </span>

                            @endif


                            @if ($shareTransaction->status === 'active')

                                <span class="inline-flex rounded-md bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Active
                                </span>

                            @else

                                <span class="inline-flex rounded-md bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Cancelled
                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- Transaction Details --}}
                <div class="p-5">

                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Date AD
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $shareTransaction->date_ad?->format('Y-m-d') ?? '-' }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Date BS
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $shareTransaction->date_bs ?: '-' }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Financial Year
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $shareTransaction->financial_year ?: '-' }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">

                                @if ($isTransfer)
                                    From Shareholder
                                @else
                                    Shareholder
                                @endif

                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $shareTransaction->shareholder?->name ?? '-' }}
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ $shareTransaction->shareholder?->code ?? '-' }}
                            </div>

                        </div>


                        @if ($isTransfer)

                            <div>

                                <div class="text-xs font-medium uppercase text-gray-500">
                                    To Shareholder
                                </div>

                                <div class="mt-1 font-semibold text-gray-900">
                                    {{ $shareTransaction->toShareholder?->name ?? '-' }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $shareTransaction->toShareholder?->code ?? '-' }}
                                </div>

                            </div>

                        @else

                            <div>

                                <div class="text-xs font-medium uppercase text-gray-500">
                                    Account
                                </div>

                                <div class="mt-1 font-semibold text-gray-900">
                                    {{ $shareTransaction->account?->name ?? '-' }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $shareTransaction->account?->code ?? '-' }}
                                </div>

                            </div>

                        @endif


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Kitta
                            </div>

                            <div class="mt-1 text-lg font-semibold text-gray-900">
                                {{ number_format($shareTransaction->kitta) }}
                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Per Kitta Value
                            </div>

                            <div class="mt-1 text-lg font-semibold text-gray-900">
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

                            <div class="mt-1 text-lg font-semibold">

                                @if ($isBuy)

                                    <span class="text-green-700">
                                        +{{ number_format($shareTransaction->total_amount) }}
                                    </span>

                                @elseif ($isWithdraw)

                                    <span class="text-red-700">
                                        -{{ number_format($shareTransaction->total_amount) }}
                                    </span>

                                @else

                                    <span class="text-purple-700">
                                        {{ number_format($shareTransaction->total_amount) }}
                                    </span>

                                @endif

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


                        <div>

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Created By
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $shareTransaction->creator?->name ?? '-' }}
                            </div>

                        </div>

                    </div>


                    {{-- Note --}}
                    <div class="mt-6 border-t border-gray-100 pt-5">

                        <div class="text-xs font-medium uppercase text-gray-500">
                            Note
                        </div>

                        <div class="mt-2 whitespace-pre-line text-sm text-gray-800">
                            {{ $shareTransaction->note ?: '-' }}
                        </div>

                    </div>


                    {{-- Attachment --}}
                    @if ($shareTransaction->attachment)

                        <div class="mt-6 border-t border-gray-100 pt-5">

                            <div class="text-xs font-medium uppercase text-gray-500">
                                Attachment
                            </div>

                            <div class="mt-2">

                                @if ($isTransfer)

                                    <a
                                        href="{{ route(
                                            'share-transfers.attachment',
                                            $shareTransaction
                                        ) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-medium text-blue-600 hover:underline"
                                    >
                                        View Attachment
                                    </a>

                                @else

                                    <a
                                        href="{{ route(
                                            'share-transactions.attachment',
                                            $shareTransaction
                                        ) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-medium text-blue-600 hover:underline"
                                    >
                                        View Attachment
                                    </a>

                                @endif

                            </div>

                        </div>

                    @endif


                    {{-- Cancellation Information --}}
                    @if ($shareTransaction->status === 'cancelled')

                        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-5">

                            <div class="text-base font-semibold text-red-800">
                                Cancelled Transaction
                            </div>

                            <div class="mt-4 grid gap-4 sm:grid-cols-2">

                                <div>

                                    <div class="text-xs font-medium uppercase text-red-600">
                                        Cancelled By
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-red-800">
                                        {{ $shareTransaction->canceller?->name ?? '-' }}
                                    </div>

                                </div>


                                <div>

                                    <div class="text-xs font-medium uppercase text-red-600">
                                        Cancelled At
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-red-800">
                                        {{ $shareTransaction->cancelled_at?->format('Y-m-d H:i:s') ?? '-' }}
                                    </div>

                                </div>

                            </div>


                            <div class="mt-4">

                                <div class="text-xs font-medium uppercase text-red-600">
                                    Cancellation Reason
                                </div>

                                <div class="mt-1 whitespace-pre-line text-sm text-red-800">
                                    {{ $shareTransaction->cancellation_reason ?: '-' }}
                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- Cancel Modal --}}
    @if ($shareTransaction->status === 'active')

        <div
            id="cancel_transaction_modal"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 px-4"
        >

            <div
                class="w-full rounded-xl bg-white p-6 shadow-xl"
                style="max-width: 520px;"
            >

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <h3 class="text-lg font-semibold text-gray-900">
                            Cancel Transaction
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $shareTransaction->transaction_number }}
                        </p>

                    </div>


                    <button
                        type="button"
                        id="cancel_modal_close"
                        class="text-2xl leading-none text-gray-400 hover:text-gray-700"
                    >
                        &times;
                    </button>

                </div>


                <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700">

                    @if ($isBuy)

                        Cancelling this Buy transaction will reverse the shareholder Kitta, investment and account effect.

                    @elseif ($isWithdraw)

                        Cancelling this Withdraw transaction will restore the shareholder Kitta, investment and account effect.

                    @else

                        Cancelling this Transfer will return the Kitta to the original shareholder and remove it from the receiving shareholder.

                    @endif

                </div>


                <form
                    method="POST"
                    action="{{ route(
                        'share-transactions.cancel',
                        $shareTransaction
                    ) }}"
                    class="mt-5"
                >

                    @csrf


                    <label
                        for="cancellation_reason"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Cancellation Reason *
                    </label>


                    <textarea
                        id="cancellation_reason"
                        name="cancellation_reason"
                        rows="4"
                        maxlength="1000"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        placeholder="Enter reason for cancellation..."
                    >{{ old('cancellation_reason') }}</textarea>


                    <div class="mt-5 flex justify-end gap-3">

                        <button
                            type="button"
                            id="cancel_modal_back"
                            class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Back
                        </button>


                        <button
                            type="submit"
                            onclick="return confirm('Are you sure you want to cancel this transaction? This will reverse its effect.')"
                            class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500"
                        >
                            Confirm Cancel
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <script>

            document.addEventListener(
                'DOMContentLoaded',
                function () {

                    const openButton =
                        document.getElementById(
                            'cancel_transaction_button'
                        );

                    const modal =
                        document.getElementById(
                            'cancel_transaction_modal'
                        );

                    const closeButton =
                        document.getElementById(
                            'cancel_modal_close'
                        );

                    const backButton =
                        document.getElementById(
                            'cancel_modal_back'
                        );


                    function openModal() {

                        modal.classList.remove(
                            'hidden'
                        );

                        modal.classList.add(
                            'flex'
                        );

                    }


                    function closeModal() {

                        modal.classList.add(
                            'hidden'
                        );

                        modal.classList.remove(
                            'flex'
                        );

                    }


                    if (openButton) {
                        openButton.addEventListener(
                            'click',
                            openModal
                        );
                    }


                    if (closeButton) {
                        closeButton.addEventListener(
                            'click',
                            closeModal
                        );
                    }


                    if (backButton) {
                        backButton.addEventListener(
                            'click',
                            closeModal
                        );
                    }


                    modal.addEventListener(
                        'click',
                        function (event) {

                            if (event.target === modal) {
                                closeModal();
                            }

                        }
                    );


                    document.addEventListener(
                        'keydown',
                        function (event) {

                            if (
                                event.key === 'Escape'
                            ) {
                                closeModal();
                            }

                        }
                    );

                }
            );

        </script>

    @endif

</x-app-layout>