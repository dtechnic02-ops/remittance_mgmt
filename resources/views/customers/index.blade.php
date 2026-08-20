<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customers
            </h2>

            @if (auth()->user()->hasPermission('customer.create'))
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('customers.import.template') }}" class="text-sm text-gray-600 hover:underline">Download Excel Template</a>
                    <a href="{{ route('customers.import.create') }}" class="text-sm text-gray-600 hover:underline">Import Customers</a>
                    <a href="{{ route('customers.create') }}" class="rounded-md bg-gray-800 px-4 py-2 text-white">+ New Customer</a>
                </div>
            @endif
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

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">

                    <form method="GET"
                          action="{{ route('customers.index') }}"
                          class="grid grid-cols-1 gap-3 md:grid-cols-4">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search code, name, mobile, phone or citizenship..."
                               class="w-full rounded-md border-gray-300 shadow-sm md:col-span-2">

                        <select name="status"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Cancelled / Inactive</option>
                            <option value="all" @selected($status === 'all')>All</option>
                        </select>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="px-5 py-2 bg-gray-800 text-white rounded-md">
                                Filter
                            </button>

                            <a href="{{ route('customers.index') }}"
                               class="px-5 py-2 border border-gray-300 rounded-md text-center">
                                Reset
                            </a>
                        </div>

                    </form>

                </div>

                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>
                            <tr class="border-b bg-gray-50 text-left">

                                <th class="px-4 py-3">
                                    Code
                                </th>

                                <th class="px-4 py-3">
                                    Customer
                                </th>

                                <th class="px-4 py-3">
                                    Mobile
                                </th>

                                <th class="px-4 py-3">
                                    Date
                                </th>

                                <th class="px-4 py-3">
                                    Account Details
                                </th>

                                <th class="px-4 py-3">
                                    Status
                                </th>

                                <th class="px-4 py-3">
                                    Created By
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($customers as $customer)

                                <tr class="border-b">

                                    <td class="px-4 py-3 font-medium">
                                        {{ $customer->customer_code }}
                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="flex items-center gap-3">

                                            @if ($customer->photo)

                                                <img src="{{ route('customers.documents.type', [$customer, 'photo']) }}"
                                                     alt=""
                                                     class="w-10 h-10 rounded-full object-cover">

                                            @else

                                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-semibold text-gray-600">
                                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                </div>

                                            @endif

                                            <div>
                                                <div class="font-semibold">
                                                    {{ $customer->name }}
                                                </div>

                                                @if ($customer->address)
                                                    <div class="text-xs text-gray-500">
                                                        {{ $customer->address }}
                                                    </div>
                                                @endif
                                            </div>

                                        </div>

                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $customer->mobile ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="whitespace-nowrap text-sm">AD: {{ $customer->english_date?->format('Y-m-d') ?: '-' }}</div>
                                        <div class="whitespace-nowrap text-xs text-gray-500">BS: {{ $customer->nepali_date ?: '-' }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="whitespace-nowrap text-sm">{{ $customer->account ?: '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $customer->branch ?: '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $customer->accountTypeLabel() ?: '-' }}</div>
                                    </td>

                                    <td class="px-4 py-3">

                                        @if ($customer->is_active)

                                            <span class="text-green-700 font-medium">
                                                Active
                                            </span>

                                        @else

                                            <span class="text-red-700 font-medium">
                                                Inactive
                                            </span>

                                        @endif

                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $customer->creator?->name ?: '-' }}
                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="text-right">
                                            <a href="{{ route('customers.show', $customer) }}"
                                               class="text-blue-600">View</a>
                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No customers found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $customers->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
