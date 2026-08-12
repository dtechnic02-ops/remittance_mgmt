<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Lenders
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Short-Term Borrowing Contacts
                </p>
            </div>

            <a href="{{ route('lenders.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Lender
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

                <div class="p-6 border-b">
                    <form method="GET"
                          action="{{ route('lenders.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search code, name, mobile or address..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('lenders.index') }}"
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
                                <th class="px-4 py-3">Code</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Mobile</th>
                                <th class="px-4 py-3">Address</th>
                                <th class="px-4 py-3 text-right">
                                    Outstanding
                                </th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($lenders as $lender)

                                @php
                                    $outstanding =
                                        (int) ($lender->total_borrowed ?? 0)
                                        - (int) ($lender->total_repaid ?? 0);
                                @endphp

                                <tr class="border-b hover:bg-gray-50">

                                    <td class="px-4 py-3 font-semibold">
                                        {{ $lender->code }}
                                    </td>

                                    <td class="px-4 py-3 font-medium">
                                        {{ $lender->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $lender->mobile ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $lender->address ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-bold">

                                        @if ($outstanding > 0)
                                            <span class="text-red-700">
                                                {{ number_format($outstanding) }}
                                            </span>
                                        @else
                                            <span class="text-green-700">
                                                0
                                            </span>
                                        @endif

                                    </td>

                                    <td class="px-4 py-3">

                                        @if ($lender->is_active)
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
                                        <a href="{{ route('lenders.edit', $lender) }}"
                                           class="text-blue-600 hover:underline">
                                            Edit
                                        </a>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No lenders found.
                                    </td>
                                </tr>

                            @endforelse
                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $lenders->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>