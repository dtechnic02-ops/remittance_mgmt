<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Income Categories
            </h2>

            <a href="{{ route('income-categories.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Category
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

            <div class="bg-white shadow-sm sm:rounded-lg">

                {{-- Search --}}
                <div class="p-6 border-b">

                    <form method="GET"
                          action="{{ route('income-categories.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search category name, code or description..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('income-categories.index') }}"
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

                                <th class="px-4 py-3">
                                    Category
                                </th>

                                <th class="px-4 py-3">
                                    Code
                                </th>

                                <th class="px-4 py-3">
                                    Description
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

                            @forelse ($categories as $category)

                                <tr class="border-b">

                                    <td class="px-4 py-3 font-semibold">
                                        {{ $category->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $category->code ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">

                                        {{ $category->description
                                            ? \Illuminate\Support\Str::limit(
                                                $category->description,
                                                60
                                            )
                                            : '-'
                                        }}

                                    </td>

                                    <td class="px-4 py-3">

                                        @if ($category->is_active)

                                            <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                Inactive
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3 text-right">

                                        <a href="{{ route('income-categories.edit', $category) }}"
                                           class="text-blue-600 hover:underline">
                                            Edit
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No income categories found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $categories->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>