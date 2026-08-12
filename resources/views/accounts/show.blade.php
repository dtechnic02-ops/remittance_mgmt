<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Account Details
            </h2>

            <div class="flex gap-4">
                <a href="{{ route('accounts.edit', $account) }}"
                   class="text-indigo-600 hover:text-indigo-900">
                    Edit
                </a>

                <a href="{{ route('accounts.index') }}"
                   class="text-gray-600 hover:text-gray-900">
                    Back to Accounts
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <p class="text-sm text-gray-500">Account Name</p>
                            <p class="font-semibold">
                                {{ $account->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Account Code</p>
                            <p class="font-semibold">
                                {{ $account->code }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Account Type</p>
                            <p class="font-semibold">
                                {{ \App\Models\Account::types()[$account->type] ?? $account->type }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Current Balance</p>

                            <p class="text-xl font-bold
                                {{ $account->current_balance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($account->current_balance) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Account Number</p>
                            <p class="font-semibold">
                                {{ $account->account_number ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Branch Name</p>
                            <p class="font-semibold">
                                {{ $account->branch_name ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>

                            @if ($account->is_active)
                                <p class="font-semibold text-green-700">
                                    Active
                                </p>
                            @else
                                <p class="font-semibold text-red-700">
                                    Inactive
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Created By</p>
                            <p class="font-semibold">
                                {{ $account->creator?->name ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Last Updated By</p>
                            <p class="font-semibold">
                                {{ $account->updater?->name ?: '-' }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-8">
                        <p class="text-sm text-gray-500">Note</p>

                        <div class="mt-2 p-4 bg-gray-50 rounded-md whitespace-pre-line">
                            {{ $account->note ?: 'No note.' }}
                        </div>
                    </div>

                    <div class="mt-8">
                        <p class="text-sm text-gray-500">Attachment</p>

                        @if ($account->attachment)
                            <a href="{{ asset('storage/'.$account->attachment) }}"
                               target="_blank"
                               class="inline-block mt-2 text-blue-600 underline">
                                View Attachment
                            </a>
                        @else
                            <p class="mt-2 text-gray-600">
                                No attachment.
                            </p>
                        @endif
                    </div>

                    <div class="mt-8 border-t pt-6 text-sm text-gray-500">
                        Created:
                        {{ $account->created_at?->format('Y-m-d H:i') }}

                        <br>

                        Updated:
                        {{ $account->updated_at?->format('Y-m-d H:i') }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
