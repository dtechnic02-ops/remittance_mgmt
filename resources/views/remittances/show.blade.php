<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Remittance Details
            </h2>

            <div class="flex flex-wrap items-center gap-3">
                @if ($remittance->status === 'active')
                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('remittance.create'))
                        <a href="{{ route('remittances.edit', $remittance) }}" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Edit</a>
                    @endif
                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('remittance.cancel'))
                        <button type="button" id="cancel_remittance_button" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white">Cancel Transaction</button>
                    @endif
                @endif
                <a href="{{ route('remittances.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Back to Remittances</a>
            </div>
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

    @if ($remittance->status === 'active' && (auth()->user()->isAdmin() || auth()->user()->hasPermission('remittance.cancel')))
        <div id="cancel_remittance_modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 px-4">
            <div class="w-full rounded-xl bg-white p-6 shadow-xl" style="max-width:520px"><div class="flex items-start justify-between gap-4"><div><h3 class="text-lg font-semibold">Cancel Transaction</h3><p class="mt-1 text-sm text-gray-500">{{ $remittance->transaction_number }}</p></div><button type="button" id="cancel_remittance_close" class="text-2xl text-gray-400">&times;</button></div>
                <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700">Cancellation keeps the original transaction and reverses its existing financial effects.</div>
                <form method="POST" action="{{ route('remittances.cancel',$remittance) }}" class="mt-5">@csrf
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">Cancellation Reason *</label><textarea id="cancellation_reason" name="cancellation_reason" rows="4" maxlength="1000" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('cancellation_reason') }}</textarea>
                    <div class="mt-5 flex justify-end gap-3"><button type="button" id="cancel_remittance_back" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Back</button><button type="submit" onclick="return confirm('Are you sure you want to cancel this remittance? This will reverse its effect.')" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white">Confirm Cancel</button></div>
                </form></div>
        </div>
        <script>document.addEventListener('DOMContentLoaded',function(){const o=document.getElementById('cancel_remittance_button'),m=document.getElementById('cancel_remittance_modal'),x=document.getElementById('cancel_remittance_close'),b=document.getElementById('cancel_remittance_back');function open(){m.classList.remove('hidden');m.classList.add('flex')}function close(){m.classList.add('hidden');m.classList.remove('flex')}o?.addEventListener('click',open);x?.addEventListener('click',close);b?.addEventListener('click',close);m.addEventListener('click',e=>{if(e.target===m)close()});document.addEventListener('keydown',e=>{if(e.key==='Escape')close()});});</script>
    @endif
</x-app-layout>
