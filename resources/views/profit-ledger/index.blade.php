<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Profit Ledger
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Date-filtered profit summary
            </p>
        </div>
    </x-slot>

    @php
        $signed = fn (int $value) => ($value < 0 ? '-' : '').'Rs. '.number_format(abs($value));
        $period = match (true) {
            $dateFrom !== '' && $dateTo !== '' => $dateFrom.' to '.$dateTo,
            $dateFrom !== '' => $dateFrom.' to …',
            $dateTo !== '' => '… to '.$dateTo,
            default => 'All dates',
        };
    @endphp

    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8" style="max-width: 1500px;">

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="border-b border-gray-200 p-4">
                    <form method="GET" action="{{ route('profit-ledger.index') }}" class="flex flex-wrap items-end gap-3">
                        <div style="width: 170px;">
                            <label for="financial_year" class="mb-1 block text-xs font-medium text-gray-600">
                                Financial Year
                            </label>
                            <select id="financial_year"
                                    name="financial_year"
                                    class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                                <option value="all" @selected(strtolower($financialYear) === 'all')>All FY</option>
                                @foreach ($financialYears as $fy)
                                    <option value="{{ $fy }}" @selected($financialYear === $fy)>
                                        {{ $fy }}@if ($fy === $currentFinancialYear) (Current)@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="width: 160px;">
                            <label for="date_from" class="mb-1 block text-xs font-medium text-gray-600">
                                Start Date
                            </label>
                            <input id="date_from"
                                   type="date"
                                   name="date_from"
                                   value="{{ $dateFrom }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        </div>

                        <div style="width: 160px;">
                            <label for="date_to" class="mb-1 block text-xs font-medium text-gray-600">
                                End Date
                            </label>
                            <input id="date_to"
                                   type="date"
                                   name="date_to"
                                   value="{{ $dateTo }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        </div>

                        <button class="rounded-md bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white">
                            Filter
                        </button>
                        <a href="{{ route('profit-ledger.index') }}"
                           class="rounded-md border border-gray-300 px-5 py-2.5 text-sm">
                            Reset
                        </a>
                        <button type="submit"
                                name="output"
                                value="print"
                                formtarget="_blank"
                                class="rounded-md border border-blue-300 px-4 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-50">
                            Print A4
                        </button>
                    </form>
                </div>

                <div class="p-6">
                    <p class="mb-4 text-sm font-medium text-gray-700">
                        Period: {{ $period }}@if (strtolower($financialYear) !== 'all') · FY {{ $financialYear }}@endif
                    </p>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <x-dashboard-card title="Total Income" :value="$signed($income)" />
                        <x-dashboard-card title="Remittance Charge" :value="$signed($remittanceCharge)" />
                        <x-dashboard-card title="Total Expenses" :value="'-Rs. '.number_format($expense)" />
                        <x-dashboard-card
                            title="Total Profit"
                            :value="$signed($profit)"
                            :negative="$profit < 0"
                            featured
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
