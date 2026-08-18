<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Income
            </h2>

           <div class="flex items-center gap-2">

    @if (
        auth()->user()->isAdmin() ||
        auth()->user()->hasPermission('income-category.view')
    )
        <a href="{{ route('income-categories.index') }}"
           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
            Income Categories
        </a>
    @endif

    @if (
        auth()->user()->isAdmin() ||
        auth()->user()->hasPermission('income.create')
    )
        <a href="{{ route('incomes.create') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-md">
            + New Income
        </a>
    @endif

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

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <form method="GET"
                          action="{{ route('incomes.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search income number, category, account or reference..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('incomes.index') }}"
                               class="px-5 py-2 border border-gray-300 rounded-md text-center">
                                Clear
                            </a>
                        @endif

                    </form>
                </div>

                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>
                            <tr class="border-b bg-gray-50 text-left">

                                <th class="px-4 py-3">
                                    Income
                                </th>

                                <th class="px-4 py-3">
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    Category
                                </th>

                                <th class="px-4 py-3">
                                    Received In
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Amount
                                </th>

                                <th class="px-4 py-3">
                                    Reference
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

                            @forelse ($incomes as $income)

                                <tr class="border-b">

                                    <td class="px-4 py-3 font-semibold">
                                        {{ $income->income_number }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">

                                        {{ $income->date_ad->format('Y-m-d') }}

                                        <div class="text-xs text-gray-500">
                                            {{ $income->date_bs }}
                                        </div>

                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $income->category->name }}
                                        </div>

                                        @if ($income->category->code)
                                            <div class="text-xs text-gray-500">
                                                {{ $income->category->code }}
                                            </div>
                                        @endif

                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="font-medium">
                                            {{ $income->account->name }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $income->account->code }}
                                        </div>

                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold text-green-700">
                                        +{{ number_format($income->amount) }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $income->reference ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3">

                                        @if ($income->status === 'active')

                                            <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                {{ ucfirst($income->status) }}
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        <a href="{{ route('incomes.show', $income) }}"
                                           class="text-blue-600 hover:underline">
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No income transactions found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $incomes->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>