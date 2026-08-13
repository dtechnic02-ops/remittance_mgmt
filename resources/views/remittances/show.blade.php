<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Remittance Details
            </h2>

            <a href="{{ route('remittances.index') }}"
               class="text-gray-600">
                Back to Remittances
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <p class="text-sm text-gray-500">Transaction Number</p>
                            <p class="font-semibold">
                                {{ $remittance->transaction_number }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-semibold">
                                {{ ucfirst($remittance->status) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Customer</p>
                            <p class="font-semibold">
                                {{ $remittance->customer->customer_code }}
                                -
                                {{ $remittance->customer->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Provider</p>
                            <p class="font-semibold">
                                {{ $remittance->providerAccount->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Cash Account</p>
                            <p class="font-semibold">
                                {{ $remittance->cashAccount->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Provider Reference</p>
                            <p class="font-semibold">
                                {{ $remittance->provider_reference ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">English Date</p>
                            <p class="font-semibold">
                                {{ $remittance->date_ad->format('Y-m-d') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Nepali Date</p>
                            <p class="font-semibold">
                                {{ $remittance->date_bs }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Financial Year</p>
                            <p class="font-semibold">
                                {{ $remittance->financial_year }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Created By</p>
                            <p class="font-semibold">
                                {{ $remittance->creator?->name ?: '-' }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Amount Summary
                        </h3>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div class="bg-gray-50 rounded-md p-4">
                                <p class="text-sm text-gray-500">
                                    Principal
                                </p>

                                <p class="mt-1 text-xl font-bold">
                                    {{ number_format($remittance->principal_amount) }}
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-md p-4">
                                <p class="text-sm text-gray-500">
                                    Service Charge
                                </p>

                                <p class="mt-1 text-xl font-bold">
                                    {{ number_format($remittance->service_charge) }}
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-md p-4">
                                <p class="text-sm text-gray-500">
                                    Total Cash Received
                                </p>

                                <p class="mt-1 text-xl font-bold">
                                    {{ number_format($remittance->total_cash_received) }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Financial Effect
                        </h3>

                        <div class="mt-4 space-y-2 text-sm">

                            <div>
                                Cash Principal:
                                <strong>
                                    +{{ number_format($remittance->principal_amount) }}
                                </strong>
                            </div>

                            <div>
                                Cash Service Charge:
                                <strong>
                                    +{{ number_format($remittance->service_charge) }}
                                </strong>
                            </div>

                            <div>
                                Provider Principal:
                                <strong>
                                    -{{ number_format($remittance->principal_amount) }}
                                </strong>
                            </div>

                        </div>
                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Note
                        </h3>

                        <div class="mt-3 bg-gray-50 p-4 rounded-md whitespace-pre-line">
                            {{ $remittance->note ?: 'No note.' }}
                        </div>

                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Attachment
                        </h3>

                        @if ($remittance->attachment)

                            <a href="{{ route('remittances.attachment', $remittance) }}"
                               target="_blank"
                               class="inline-block mt-3 text-blue-600 underline">
                                View Attachment
                            </a>

                        @else

                            <p class="mt-3 text-gray-500">
                                No attachment.
                            </p>

                        @endif

                    </div>
                                    @if (
                    auth()->user()->hasPermission('remittance.cancel')
                    && $remittance->status === 'active'
                )

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-red-700">
                            Cancel Remittance
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Cancellation will keep the original transaction
                            and reverse its financial effects.
                        </p>

                        <form method="POST"
                              action="{{ route('remittances.cancel', $remittance) }}"
                              class="mt-5"
                              onsubmit="return confirm('Cancel this remittance transaction?');">

                            @csrf

                            <label for="cancellation_reason"
                                   class="block text-sm font-medium text-gray-700">
                                Cancellation Reason *
                            </label>

                            <textarea id="cancellation_reason"
                                      name="cancellation_reason"
                                      rows="3"
                                      required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>

                            @error('cancellation_reason')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <button type="submit"
                                    class="mt-4 px-5 py-2 bg-red-600 text-white rounded-md">
                                Cancel Transaction
                            </button>

                        </form>

                    </div>

                @endif


                @if ($remittance->status === 'cancelled')

                    <div class="mt-10 border-t pt-8">

                        <div class="rounded-md bg-red-50 p-5">

                            <h3 class="font-semibold text-red-800">
                                Transaction Cancelled
                            </h3>

                            <div class="mt-3 text-sm text-red-700">

                                <div>
                                    Reason:
                                    {{ $remittance->cancellation_reason }}
                                </div>

                                <div class="mt-1">
                                    Cancelled By:
                                    {{ $remittance->canceller?->name ?: '-' }}
                                </div>

                                <div class="mt-1">
                                    Cancelled At:
                                    {{ $remittance->cancelled_at?->format('Y-m-d H:i') }}
                                </div>

                            </div>

                        </div>

                    </div>

                @endif

                    <div class="mt-10 border-t pt-6 text-sm text-gray-500">
                        Created:
                        {{ $remittance->created_at?->format('Y-m-d H:i') }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
