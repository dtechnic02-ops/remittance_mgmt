<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Expense Details
            </h2>

            <div class="flex flex-wrap items-center gap-3">
                @if ($expense->status === 'active')
                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('expense.create'))
                        <a href="{{ route('expenses.edit', $expense) }}"
                           class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Edit</a>
                    @endif

                    @if (auth()->user()->isAdmin() || auth()->user()->hasPermission('expense.cancel'))
                        <button type="button" id="cancel_expense_button"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                            Cancel Transaction
                        </button>
                    @endif
                @endif

                <a href="{{ route('expenses.index') }}"
                   class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">
                    Back to Expenses
                </a>
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
                                Expense Number
                            </p>

                            <p class="font-semibold">
                                {{ $expense->expense_number }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Category
                            </p>

                            <p class="font-semibold">
                                {{ $expense->category->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Status
                            </p>

                            @if ($expense->status === 'active')
                                <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    {{ ucfirst($expense->status) }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Paid From Account
                            </p>

                            <p class="font-semibold">
                                {{ $expense->account->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Amount
                            </p>

                            <p class="text-lg font-bold text-red-700">
                                {{ number_format($expense->amount) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Reference
                            </p>

                            <p class="font-semibold">
                                {{ $expense->reference ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                English Date
                            </p>

                            <p class="font-semibold">
                                {{ $expense->date_ad->format('Y-m-d') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Nepali Date
                            </p>

                            <p class="font-semibold">
                                {{ $expense->date_bs }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Financial Year
                            </p>

                            <p class="font-semibold">
                                {{ $expense->financial_year }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Created By
                            </p>

                            <p class="font-semibold">
                                {{ $expense->creator?->name ?: '-' }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Financial Effect
                        </h3>

                        <div class="mt-4 rounded-md bg-gray-50 p-4 text-sm">
                            <div>
                                {{ $expense->account->name }}
                                <strong class="text-red-700">
                                    -{{ number_format($expense->amount) }}
                                </strong>
                            </div>
                        </div>

                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Note
                        </h3>

                        <div class="mt-3 bg-gray-50 p-4 rounded-md whitespace-pre-line">
                            {{ $expense->note ?: 'No note.' }}
                        </div>

                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Attachment
                        </h3>

                        @if ($expense->attachment)

                            <a href="{{ route('expenses.attachment', $expense) }}"
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

                    @if ($expense->status === 'cancelled')

                        <div class="mt-10 border-t pt-8">

                            <div class="rounded-md bg-red-50 p-5">

                                <h3 class="font-semibold text-red-800">
                                    Expense Cancelled
                                </h3>

                                <div class="mt-3 text-sm text-red-700">

                                    <div>
                                        Reason:
                                        {{ $expense->cancellation_reason }}
                                    </div>

                                    <div class="mt-1">
                                        Cancelled By:
                                        {{ $expense->canceller?->name ?: '-' }}
                                    </div>

                                    <div class="mt-1">
                                        Cancelled At:
                                        {{ $expense->cancelled_at?->format('Y-m-d H:i') }}
                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                    <div class="mt-10 border-t pt-6 text-sm text-gray-500">
                        Created:
                        {{ $expense->created_at?->format('Y-m-d H:i') }}
                    </div>

                </div>
            </div>

        </div>
    </div>

    @if (
        $expense->status === 'active'
        && (auth()->user()->isAdmin() || auth()->user()->hasPermission('expense.cancel'))
    )
        <div id="cancel_expense_modal"
             class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 px-4">
            <div class="w-full rounded-xl bg-white p-6 shadow-xl" style="max-width: 520px;">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Cancel Transaction</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $expense->expense_number }}</p>
                    </div>
                    <button type="button" id="cancel_expense_close"
                            class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                    Cancelling this expense will reverse its financial effect and restore the selected account balance.
                </div>

                <form method="POST" action="{{ route('expenses.cancel', $expense) }}" class="mt-5">
                    @csrf
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">
                        Cancellation Reason *
                    </label>
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="4"
                              maxlength="1000" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                              placeholder="Enter reason for cancellation...">{{ old('cancellation_reason') }}</textarea>

                    <div class="mt-5 flex justify-end gap-3">
                        <button type="button" id="cancel_expense_back"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Back</button>
                        <button type="submit"
                                onclick="return confirm('Are you sure you want to cancel this expense? This will reverse its effect.')"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Confirm Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const openButton = document.getElementById('cancel_expense_button');
                const modal = document.getElementById('cancel_expense_modal');
                const closeButton = document.getElementById('cancel_expense_close');
                const backButton = document.getElementById('cancel_expense_back');

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
