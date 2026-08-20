<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Income {{ $income->income_number }}
                </h2>

                <div class="mt-1 text-sm text-gray-500">
                    Income Transaction Details
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if ($income->status === 'active')
                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('income.create'))
                        <a href="{{ route('incomes.edit', $income) }}"
                           class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            Edit
                        </a>
                    @endif

                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('income.cancel'))
                        <button type="button" id="cancel_income_button"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                            Cancel Transaction
                        </button>
                    @endif
                @endif

                <a href="{{ route('incomes.index') }}"
                   class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">
                    Back to Income
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b flex items-center justify-between">

                    <div>
                        <div class="text-sm text-gray-500">
                            Income Number
                        </div>

                        <div class="text-xl font-semibold text-gray-900">
                            {{ $income->income_number }}
                        </div>
                    </div>

                    <div>
                        @if ($income->status === 'active')
                            <span class="inline-flex rounded-md bg-green-100 px-3 py-1.5 text-sm font-semibold text-green-700">
                                Active
                            </span>
                        @else
                            <span class="inline-flex rounded-md bg-red-100 px-3 py-1.5 text-sm font-semibold text-red-700">
                                {{ ucfirst($income->status) }}
                            </span>
                        @endif
                    </div>

                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                        <div>
                            <div class="text-sm text-gray-500">
                                Income Category
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $income->category->name }}
                            </div>

                            @if ($income->category->code)
                                <div class="text-xs text-gray-500">
                                    {{ $income->category->code }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Received In
                            </div>

                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $income->account->name }}
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ ucfirst($income->account->type) }}

                                @if ($income->account->code)
                                    - {{ $income->account->code }}
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                English Date
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $income->date_ad->format('Y-m-d') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Nepali Date
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $income->date_bs }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Financial Year
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $income->financial_year }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Amount
                            </div>

                            <div class="mt-1 text-xl font-bold text-green-700">
                                +{{ number_format($income->amount) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Reference
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $income->reference ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Created By
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $income->creator?->name ?: '-' }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 border-t pt-6">

                        <div class="text-sm text-gray-500">
                            Note
                        </div>

                        <div class="mt-2 text-gray-900 whitespace-pre-line">
                            {{ $income->note ?: '-' }}
                        </div>

                    </div>

                    @if ($income->attachment)
                        <div class="mt-6 border-t pt-6">

                            <div class="text-sm text-gray-500">
                                Attachment
                            </div>

                            <div class="mt-2">
                                <a href="{{ route('incomes.attachment', $income) }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="text-blue-600 hover:underline">
                                    View Attachment
                                </a>
                            </div>

                        </div>
                    @endif

                    @if ($income->status === 'cancelled')

                        <div class="mt-8 rounded-md border border-red-200 bg-red-50 p-5">

                            <div class="font-semibold text-red-800">
                                Cancelled
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <div class="text-xs text-red-600">
                                        Cancelled By
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-red-900">
                                        {{ $income->canceller?->name ?: '-' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-red-600">
                                        Cancelled At
                                    </div>

                                    <div class="mt-1 text-sm font-medium text-red-900">
                                        {{ $income->cancelled_at?->format('Y-m-d H:i:s') ?: '-' }}
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4">
                                <div class="text-xs text-red-600">
                                    Cancellation Reason
                                </div>

                                <div class="mt-1 text-sm text-red-900 whitespace-pre-line">
                                    {{ $income->cancellation_reason }}
                                </div>
                            </div>

                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>

    @if (
        $income->status === 'active'
        && (auth()->user()->isAdmin() || auth()->user()->hasPermission('income.cancel'))
    )
        <div id="cancel_income_modal"
             class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 px-4">
            <div class="w-full rounded-xl bg-white p-6 shadow-xl" style="max-width: 520px;">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Cancel Transaction</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $income->income_number }}</p>
                    </div>
                    <button type="button" id="cancel_income_close"
                            class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                    Cancelling this income will reverse its financial effect from the selected account.
                </div>

                <form method="POST" action="{{ route('incomes.cancel', $income) }}" class="mt-5">
                    @csrf
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">
                        Cancellation Reason *
                    </label>
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="4"
                              maxlength="1000" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                              placeholder="Enter reason for cancellation...">{{ old('cancellation_reason') }}</textarea>

                    <div class="mt-5 flex justify-end gap-3">
                        <button type="button" id="cancel_income_back"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Back
                        </button>
                        <button type="submit"
                                onclick="return confirm('Are you sure you want to cancel this income? This will reverse its effect.')"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                            Confirm Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const openButton = document.getElementById('cancel_income_button');
                const modal = document.getElementById('cancel_income_modal');
                const closeButton = document.getElementById('cancel_income_close');
                const backButton = document.getElementById('cancel_income_back');

                function openModal() {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                openButton?.addEventListener('click', openModal);
                closeButton?.addEventListener('click', closeModal);
                backButton?.addEventListener('click', closeModal);
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) closeModal();
                });
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') closeModal();
                });
            });
        </script>
    @endif
</x-app-layout>
