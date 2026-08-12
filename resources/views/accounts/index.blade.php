<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Account Management
            </h2>

            <a href="{{ route('accounts.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Account
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50 text-left">
                                    <th class="px-4 py-3">Code</th>
                                    <th class="px-4 py-3">Account Name</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3 text-right">Balance</th>
                                    <th class="px-4 py-3">Negative</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($accounts as $account)
                                    <tr class="border-b">
                                        <td class="px-4 py-3">
                                            {{ $account->code }}
                                        </td>

                                        <td class="px-4 py-3 font-medium">
                                            {{ $account->name }}
                                        </td>

                                        <td class="px-4 py-3">
                                            {{ \App\Models\Account::types()[$account->type] ?? $account->type }}
                                        </td>

                                        <td class="px-4 py-3 text-right font-semibold
                                            {{ $account->current_balance < 0 ? 'text-red-600' : '' }}">
                                            {{ number_format($account->current_balance) }}
                                        </td>

                                        <td class="px-4 py-3">
                                            {{ $account->allow_negative ? 'Yes' : 'No' }}
                                        </td>

                                        <td class="px-4 py-3">
                                            @if ($account->is_active)
                                                <span class="text-green-700">Active</span>
                                            @else
                                                <span class="text-red-700">Inactive</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('accounts.show', $account) }}"
                                                   class="text-blue-600">
                                                    View
                                                </a>

                                                <a href="{{ route('accounts.edit', $account) }}"
                                                   class="text-indigo-600">
                                                    Edit
                                                </a>

                                                <form method="POST"
                                                      action="{{ route('accounts.destroy', $account) }}"
                                                      onsubmit="return confirm('Delete this account?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-red-600">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-4 py-8 text-center text-gray-500">
                                            No accounts found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $accounts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>