<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Expenses
            </h2>

            <div class="flex flex-wrap items-center gap-3">

                <a href="{{ route('expense-categories.index') }}"
                   class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-md">
                    Expense Categories
                </a>

                <a href="{{ route('expenses.create') }}"
                   class="px-4 py-2 bg-gray-800 text-white rounded-md">
                    + New Expense
                </a>

            </div>

        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Search --}}
                <div class="p-6 border-b">

                    <form method="GET"
                          action="{{ route('expenses.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search expense number, category, account, reference or note..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('expenses.index') }}"
                               class="px-5 py-2 border border-gray-300 rounded-md text-center">
                                Clear
                            </a>
                        @endif

                    </form>

                </div>

                {{-- Table --}}
                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>
                            <tr class="border-b bg-gray-50 text-left">

                                <th class="px-4 py-3 whitespace-nowrap">
                                    Expense No.
                                </th>

                                <th class="px-4 py-3 whitespace-nowrap">
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    Category
                                </th>

                                <th class="px-4 py-3">
                                    Paid From
                                </th>

                                <th class="px-4 py-3">
                                    Reference
                                </th>

                                <th class="px-4 py-3 text-right whitespace-nowrap">
                                    Amount
                                </th>

                                <th class="px-4 py-3">
                                    Attachment
                                </th>

                                <th class="px-4 py-3">
                                    Status
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($expenses as $expense)

                                <tr class="border-b">

                                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                                        {{ $expense->expense_number }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $expense->date_ad->format('Y-m-d') }}

                                        <div class="text-xs text-gray-500">
                                            {{ $expense->date_bs }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $expense->category?->name ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $expense->account?->name ?: '-' }}
                                        </div>

                                        @if ($expense->account)
                                            <div class="text-xs text-gray-500">
                                                {{ ucfirst(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $expense->account->type
                                                    )
                                                ) }}
                                            </div>
                                        @endif

                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $expense->reference ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">
                                        {{ number_format($expense->amount) }}
                                    </td>

                                    <td class="px-4 py-3">

                                        @if ($expense->attachment)

                                            <a href="{{ route('expenses.attachment', $expense) }}"
                                               target="_blank"
                                               class="text-blue-600 hover:underline">
                                                View
                                            </a>

                                        @else

                                            <span class="text-gray-400">
                                                -
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3">

                                        @if ($expense->status === 'active')

                                            <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                Cancelled
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        <a href="{{ route('expenses.show', $expense) }}"
                                           class="text-blue-600 hover:underline">
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="9"
                                        class="px-4 py-12 text-center text-gray-500">
                                        No expenses found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $expenses->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
