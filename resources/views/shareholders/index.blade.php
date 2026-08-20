<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Shareholders
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Shareholder Master
                </p>
            </div>

            <a
                href="{{ route('shareholders.create') }}"
                class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
            >
                + New Shareholder
            </a>

        </div>
    </x-slot>


    <style>
        /*
        |--------------------------------------------------------------------------
        | Shareholder Summary
        |--------------------------------------------------------------------------
        */

        .shareholder-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .shareholder-summary-card {
            min-width: 0;
            padding: 12px 16px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .shareholder-summary-label {
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
        }

        .shareholder-summary-value {
            margin-top: 4px;
            font-size: 20px;
            line-height: 1.25;
            font-weight: 700;
            color: #111827;
        }

        .shareholder-summary-value.kitta {
            color: #1d4ed8;
        }

        .shareholder-summary-value.capital {
            color: #15803d;
        }


        /*
        |--------------------------------------------------------------------------
        | Search / Filter
        |--------------------------------------------------------------------------
        */

        .shareholder-search-row {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .shareholder-search-input {
            width: 100%;
            max-width: 650px;
            min-width: 0;
            height: 42px;
            padding: 0 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #ffffff;
        }

        .shareholder-search-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 1px #2563eb;
        }

        .shareholder-filter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            height: 42px;
            padding: 0 16px;
            border-radius: 6px;
            white-space: nowrap;
            font-size: 14px;
            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | Mobile
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .shareholder-summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
            }

            .shareholder-summary-card {
                padding: 9px 8px;
            }

            .shareholder-summary-label {
                font-size: 10px;
            }

            .shareholder-summary-value {
                font-size: 16px;
            }

            .shareholder-search-row {
                flex-wrap: wrap;
                gap: 7px;
            }

            .shareholder-search-input {
                flex: 1 1 100%;
                max-width: none;
            }

            .shareholder-filter-btn {
                height: 38px;
                padding: 0 12px;
                font-size: 12px;
            }
        }
    </style>


    <div class="py-6">

        <div
            class="mx-auto w-full space-y-5 px-4 sm:px-6 lg:px-8"
            style="max-width: 1400px;"
        >

            {{-- Success Message --}}
            @if (session('success'))

                <div class="rounded-lg bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>

            @endif


            {{-- ========================================================= --}}
            {{-- SUMMARY --}}
            {{-- ========================================================= --}}

            <div class="shareholder-summary-grid">

                <div class="shareholder-summary-card">

                    <div class="shareholder-summary-label">
                        Shareholders
                    </div>

                    <div class="shareholder-summary-value">
                        {{ number_format($summary['shareholder_count']) }}
                    </div>

                </div>


                <div class="shareholder-summary-card">

                    <div class="shareholder-summary-label">
                        Total Kitta
                    </div>

                    <div class="shareholder-summary-value kitta">
                        {{ number_format($summary['total_kitta']) }}
                    </div>

                </div>


                <div class="shareholder-summary-card">

                    <div class="shareholder-summary-label">
                        Share Capital
                    </div>

                    <div class="shareholder-summary-value capital">
                        {{ number_format($summary['total_share_capital']) }}
                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- SHAREHOLDER LIST --}}
            {{-- ========================================================= --}}

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">


                {{-- ===================================================== --}}
                {{-- SEARCH + STATUS --}}
                {{-- ===================================================== --}}

                <div class="border-b border-gray-200 p-4 sm:p-5">

                    <form
                        method="GET"
                        action="{{ route('shareholders.index') }}"
                        class="shareholder-search-row"
                    >

                        {{-- Search Input --}}
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search shareholder..."
                            class="shareholder-search-input"
                        >


                        {{-- Keep Current Status When Searching --}}
                        <input
                            type="hidden"
                            name="status"
                            value="{{ $status }}"
                        >


                        {{-- Search Button --}}
                        <button
                            type="submit"
                            class="shareholder-filter-btn bg-gray-800 text-white hover:bg-gray-700"
                        >
                            Search
                        </button>


                        {{-- Active --}}
                        <a
                            href="{{ route('shareholders.index', [
                                'status' => 'active',
                                'search' => $search,
                            ]) }}"
                            class="shareholder-filter-btn
                                {{
                                    $status === 'active'
                                        ? 'bg-gray-800 text-white'
                                        : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                                }}"
                        >
                            Active
                        </a>


                        {{-- Cancelled --}}
                        <a
                            href="{{ route('shareholders.index', [
                                'status' => 'cancelled',
                                'search' => $search,
                            ]) }}"
                            class="shareholder-filter-btn
                                {{
                                    $status === 'cancelled'
                                        ? 'bg-red-600 text-white'
                                        : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                                }}"
                        >
                            Cancelled
                        </a>


                        {{-- All --}}
                        <a
                            href="{{ route('shareholders.index', [
                                'status' => 'all',
                                'search' => $search,
                            ]) }}"
                            class="shareholder-filter-btn
                                {{
                                    $status === 'all'
                                        ? 'bg-gray-600 text-white'
                                        : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                                }}"
                        >
                            All
                        </a>


                        {{-- Clear Search --}}
                        @if ($search !== '')

                            <a
                                href="{{ route('shareholders.index', [
                                    'status' => $status,
                                ]) }}"
                                class="shareholder-filter-btn border border-gray-300 text-gray-600 hover:bg-gray-50"
                            >
                                Clear
                            </a>

                        @endif

                    </form>

                </div>


                {{-- ===================================================== --}}
                {{-- TABLE --}}
                {{-- ===================================================== --}}

                <div class="overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>

                            <tr class="border-b bg-gray-50 text-left text-sm text-gray-600">

                                <th class="whitespace-nowrap px-4 py-3">
                                    Photo
                                </th>

                                <th class="whitespace-nowrap px-4 py-3">
                                    Code
                                </th>

                                <th class="whitespace-nowrap px-4 py-3">
                                    Name
                                </th>

                                <th class="whitespace-nowrap px-4 py-3">
                                    Mobile
                                </th>

                                <th class="whitespace-nowrap px-4 py-3">
                                    Email
                                </th>

                                <th class="px-4 py-3">
                                    Address
                                </th>

                                <th class="whitespace-nowrap px-4 py-3 text-right">
                                    Kitta
                                </th>

                                <th class="whitespace-nowrap px-4 py-3 text-right">
                                    Per Kitta
                                </th>

                                <th class="whitespace-nowrap px-4 py-3">
                                    Status
                                </th>

                                <th class="whitespace-nowrap px-4 py-3 text-right">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @forelse ($shareholders as $shareholder)

                                @php
                                    $hasHistory =
                                        (bool) $shareholder->has_share_transactions
                                        || (bool) $shareholder->has_received_transfers;

                                    $canHardDelete =
                                        ! $hasHistory
                                        && (int) $shareholder->kitta === 0
                                        && (int) $shareholder->total_investment === 0;
                                @endphp


                                <tr class="hover:bg-gray-50">


                                    {{-- Photo --}}
                                    <td class="px-4 py-3">

                                        @if ($shareholder->photo)

                                            <img
                                                src="{{ route(
                                                    'shareholders.documents.show',
                                                    [$shareholder, 'photo']
                                                ) }}"
                                                alt="{{ $shareholder->name }}"
                                                class="h-11 w-11 rounded-full border object-cover"
                                            >

                                        @else

                                            <div class="flex h-11 w-11 items-center justify-center rounded-full border bg-gray-100 font-semibold text-gray-500">

                                                {{
                                                    strtoupper(
                                                        substr(
                                                            $shareholder->name,
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}

                                            </div>

                                        @endif

                                    </td>


                                    {{-- Code --}}
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold">
                                        {{ $shareholder->code }}
                                    </td>


                                    {{-- Name --}}
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">
                                        {{ $shareholder->name }}
                                    </td>


                                    {{-- Mobile --}}
                                    <td class="whitespace-nowrap px-4 py-3">
                                        {{ $shareholder->mobile ?: '-' }}
                                    </td>


                                    {{-- Email --}}
                                    <td class="px-4 py-3">
                                        {{ $shareholder->email ?: '-' }}
                                    </td>


                                    {{-- Address --}}
                                    <td class="min-w-[160px] px-4 py-3">

                                        {{
                                            $shareholder->address
                                                ? \Illuminate\Support\Str::limit(
                                                    $shareholder->address,
                                                    35
                                                )
                                                : '-'
                                        }}

                                    </td>


                                    {{-- Kitta --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">
                                        {{ number_format($shareholder->kitta) }}
                                    </td>


                                    {{-- Per Kitta --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right">

                                        {{
                                            number_format(
                                                $shareholder->per_kitta_value
                                            )
                                        }}

                                    </td>


                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-4 py-3">

                                        @if ($shareholder->is_active)

                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                Cancelled
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td class="whitespace-nowrap px-4 py-3 text-right">

                                        <div class="flex items-center justify-end gap-3">


                                            {{-- View --}}
                                            <a
                                                href="{{ route(
                                                    'shareholders.show',
                                                    $shareholder
                                                ) }}"
                                                class="text-sm font-medium text-blue-600 hover:underline"
                                            >
                                                View
                                            </a>


                                            {{-- Hard Delete:
                                                 Only when completely unused --}}
                                            @if ($canHardDelete)

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'shareholders.destroy',
                                                        $shareholder
                                                    ) }}"
                                                    onsubmit="return confirm('Permanently delete this unused shareholder? This action cannot be undone.');"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-sm font-medium text-red-600 hover:underline"
                                                    >
                                                        Delete
                                                    </button>

                                                </form>


                                            {{-- Cancel:
                                                 Used shareholder keeps history --}}
                                            @elseif ($shareholder->is_active)

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'shareholders.cancel',
                                                        $shareholder
                                                    ) }}"
                                                    onsubmit="return confirm('Cancel this shareholder? Historical records will remain unchanged.');"
                                                >

                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="text-sm font-medium text-orange-600 hover:underline"
                                                    >
                                                        Cancel
                                                    </button>

                                                </form>

                                            @endif

                                        </div>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="10"
                                        class="px-4 py-12 text-center text-gray-500"
                                    >
                                        No shareholders found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                <div class="border-t border-gray-100 p-4">
                    {{ $shareholders->links() }}
                </div>


            </div>

        </div>

    </div>

</x-app-layout>