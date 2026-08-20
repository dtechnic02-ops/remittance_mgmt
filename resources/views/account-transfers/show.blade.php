<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Account Transfer Details
            </h2>

            <div class="flex flex-wrap items-center gap-3">
                @if ($accountTransfer->status === 'active')
                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('account-transfer.create'))
                        <a href="{{ route('account-transfers.edit',$accountTransfer) }}" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Edit</a>
                    @endif
                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('account-transfer.cancel'))
                        <button type="button" id="cancel_transfer_button" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white">Cancel Transaction</button>
                    @endif
                @endif
                <a href="{{ route('account-transfers.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Back to Transfers</a>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        <div>
                            <p class="text-sm text-gray-500">
                                Transfer Number
                            </p>

                            <p class="font-semibold">
                                {{ $accountTransfer->transfer_number }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Status
                            </p>

                            <p class="font-semibold">
                                {{ ucfirst($accountTransfer->status) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Amount
                            </p>

                            <p class="font-semibold">
                                {{ number_format($accountTransfer->amount) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                From Account
                            </p>

                            <p class="font-semibold">
                                {{ $accountTransfer->fromAccount->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                To Account
                            </p>

                            <p class="font-semibold">
                                {{ $accountTransfer->toAccount->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Reference
                            </p>

                            <p class="font-semibold">
                                {{ $accountTransfer->reference ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                English Date
                            </p>

                            <p class="font-semibold">
                                {{ $accountTransfer->date_ad->format('Y-m-d') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Nepali Date
                            </p>

                            <p class="font-semibold">
                                {{ $accountTransfer->date_bs }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Financial Year
                            </p>

                            <p class="font-semibold">
                                {{ $accountTransfer->financial_year }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-10 border-t pt-8">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Transfer Effect
                        </h3>

                        <div class="mt-4 space-y-2 text-sm">

                            <div>
                                {{ $accountTransfer->fromAccount->name }}:
                                <strong>
                                    -{{ number_format($accountTransfer->amount) }}
                                </strong>
                            </div>

                            <div>
                                {{ $accountTransfer->toAccount->name }}:
                                <strong>
                                    +{{ number_format($accountTransfer->amount) }}
                                </strong>
                            </div>

                        </div>
                    </div>

                    <div class="mt-10 border-t pt-8">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Note
                        </h3>

                        <div class="mt-3 bg-gray-50 p-4 rounded-md whitespace-pre-line">
                            {{ $accountTransfer->note ?: 'No note.' }}
                        </div>
                    </div>

                    @if ($accountTransfer->status === 'cancelled')
                        <div class="mt-10 border-t pt-8">

                            <div class="rounded-md bg-red-50 p-5">

                                <h3 class="font-semibold text-red-800">
                                    Transfer Cancelled
                                </h3>

                                <div class="mt-3 text-sm text-red-700">
                                    <div>
                                        Reason:
                                        {{ $accountTransfer->cancellation_reason }}
                                    </div>

                                    <div class="mt-1">
                                        Cancelled By:
                                        {{ $accountTransfer->canceller?->name ?: '-' }}
                                    </div>

                                    <div class="mt-1">
                                        Cancelled At:
                                        {{ $accountTransfer->cancelled_at?->format('Y-m-d H:i') }}
                                    </div>
                                </div>

                            </div>

                        </div>
                    @endif

                    <div class="mt-10 border-t pt-6 text-sm text-gray-500">
                        Created By:
                        {{ $accountTransfer->creator?->name ?: '-' }}

                        <br>

                        Created:
                        {{ $accountTransfer->created_at?->format('Y-m-d H:i') }}
                    </div>

                </div>
            </div>

        </div>
    </div>
    @if($accountTransfer->status==='active' && (auth()->user()->isAdmin() || auth()->user()->hasPermission('account-transfer.cancel')))
    <div id="cancel_transfer_modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 px-4"><div class="w-full rounded-xl bg-white p-6 shadow-xl" style="max-width:520px"><div class="flex justify-between"><div><h3 class="text-lg font-semibold">Cancel Transaction</h3><p class="text-sm text-gray-500">{{ $accountTransfer->transfer_number }}</p></div><button type="button" id="cancel_transfer_close" class="text-2xl">&times;</button></div><div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700">Cancellation reverses both existing account effects exactly once.</div><form method="POST" action="{{ route('account-transfers.cancel',$accountTransfer) }}" class="mt-5">@csrf<label for="cancellation_reason" class="block text-sm font-medium">Cancellation Reason *</label><textarea id="cancellation_reason" name="cancellation_reason" rows="4" maxlength="1000" required class="mt-1 block w-full rounded-md border-gray-300">{{ old('cancellation_reason') }}</textarea><div class="mt-5 flex justify-end gap-3"><button type="button" id="cancel_transfer_back" class="rounded-md border px-4 py-2 text-sm">Back</button><button type="submit" onclick="return confirm('Are you sure you want to cancel this account transfer?')" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white">Confirm Cancel</button></div></form></div></div>
    <script>document.addEventListener('DOMContentLoaded',function(){const o=document.getElementById('cancel_transfer_button'),m=document.getElementById('cancel_transfer_modal'),x=document.getElementById('cancel_transfer_close'),b=document.getElementById('cancel_transfer_back');function open(){m.classList.remove('hidden');m.classList.add('flex')}function close(){m.classList.add('hidden');m.classList.remove('flex')}o?.addEventListener('click',open);x?.addEventListener('click',close);b?.addEventListener('click',close);m.addEventListener('click',e=>{if(e.target===m)close()});document.addEventListener('keydown',e=>{if(e.key==='Escape')close()})});</script>
    @endif
</x-app-layout>
