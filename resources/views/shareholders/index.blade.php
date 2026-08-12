<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Shareholders
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Shareholder Master
                </p>
            </div>

            <a href="{{ route('shareholders.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Shareholder
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
                          action="{{ route('shareholders.index') }}"
                          class="flex flex-col md:flex-row gap-3">

                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search code, name, mobile, email or address..."
                               class="w-full md:flex-1 rounded-md border-gray-300 shadow-sm">

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Search
                        </button>

                        @if ($search !== '')
                            <a href="{{ route('shareholders.index') }}"
                               class="px-5 py-2 border border-gray-300 rounded-md text-center">
                                Clear
                            </a>
                        @endif

                    </form>

                </div>

                {{-- Shareholder Table --}}
                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>
                            <tr class="border-b bg-gray-50 text-left">

                                <th class="px-4 py-3">
                                    Photo
                                </th>

                                <th class="px-4 py-3">
                                    Code
                                </th>

                                <th class="px-4 py-3">
                                    Name
                                </th>

                                <th class="px-4 py-3">
                                    Mobile
                                </th>

                                <th class="px-4 py-3">
                                    Email
                                </th>

                                <th class="px-4 py-3">
                                    Address
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Kitta
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Per Kitta Value
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($shareholders as $shareholder)

                                <tr class="border-b hover:bg-gray-50">

                                    {{-- Photo --}}
                                    <td class="px-4 py-3">

                                        @if ($shareholder->photo)

                                            <img
                                                src="{{ Storage::disk('public')->url($shareholder->photo) }}"
                                                alt="{{ $shareholder->name }}"
                                                class="h-12 w-12 rounded-full object-cover border"
                                            >

                                        @else

                                            <div class="h-12 w-12 rounded-full bg-gray-100 border flex items-center justify-center text-gray-500 font-semibold">
                                                {{ strtoupper(substr($shareholder->name, 0, 1)) }}
                                            </div>

                                        @endif

                                    </td>

                                    {{-- Code --}}
                                    <td class="px-4 py-3 font-semibold whitespace-nowrap">
                                        {{ $shareholder->code }}
                                    </td>

                                    {{-- Name --}}
                                    <td class="px-4 py-3 font-medium whitespace-nowrap">
                                        {{ $shareholder->name }}
                                    </td>

                                    {{-- Mobile --}}
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $shareholder->mobile ?: '-' }}
                                    </td>

                                    {{-- Email --}}
                                    <td class="px-4 py-3">
                                        {{ $shareholder->email ?: '-' }}
                                    </td>

                                    {{-- Address --}}
                                    <td class="px-4 py-3">
                                        {{ $shareholder->address
                                            ? \Illuminate\Support\Str::limit(
                                                $shareholder->address,
                                                40
                                            )
                                            : '-'
                                        }}
                                    </td>

                                    {{-- Kitta --}}
                                    <td class="px-4 py-3 text-right font-semibold">
                                        {{ number_format($shareholder->kitta) }}
                                    </td>

                                    {{-- Per Kitta Value --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        {{ number_format($shareholder->per_kitta_value) }}
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap">

                                        <a href="{{ route('shareholders.show', $shareholder) }}"
                                           class="text-blue-600 hover:underline">
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="9"
                                        class="px-4 py-10 text-center text-gray-500">
                                        No shareholders found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                    <div class="mt-6">
                        {{ $shareholders->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>