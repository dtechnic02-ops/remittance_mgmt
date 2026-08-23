<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Staff Dashboard
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Financial overview and your available operations
                </p>
            </div>

            <p class="text-sm text-gray-600">
                Welcome, {{ auth()->user()->name }}
            </p>
        </div>
    </x-slot>

    @php
        $signed = fn (int $value) =>
            ($value > 0 ? '+' : ($value < 0 ? '-' : ''))
            .number_format(abs($value));

        $series = [
            [
                'key' => 'income',
                'label' => 'Income',
                'color' => 'bg-emerald-500',
            ],
            [
                'key' => 'commission',
                'label' => 'Commission',
                'color' => 'bg-blue-500',
            ],
            [
                'key' => 'expense',
                'label' => 'Expense',
                'color' => 'bg-amber-500',
            ],
            [
                'key' => 'profit_loss',
                'label' => 'Profit/Loss',
                'color' => 'bg-violet-500',
            ],
        ];
    @endphp

    <div class="py-5">

    <div class="mx-auto w-full space-y-5 px-4 sm:px-6 lg:px-8" style="max-width: 1400px;">

            {{-- Current Position --}}
            <section>

                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Current Position
                </h3>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

                    <x-dashboard-card
                        title="Total Share Capital"
                        :value="number_format($summary['share_capital'])"
                    />

                    <x-dashboard-card
                        title="Cash Balance"
                        :value="$signed($summary['cash'])"
                    />

                    <x-dashboard-card
                        title="Bank Balance"
                        :value="$signed($summary['bank'])"
                    />

                    <x-dashboard-card
                        title="Available Cash & Bank"
                        :value="number_format($summary['available'])"
                        featured
                    />

                </div>

            </section>


            {{-- Business Result --}}
            <section>

                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Business Result — FY {{ $financialYear }}
                </h3>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

                    <x-dashboard-card
                        title="Total Income"
                        :value="$signed($summary['income'])"
                        :detail="'Overall: Rs. '.number_format($summary['overall_income'])"
                    />

                    <x-dashboard-card
                        title="Total Commission"
                        :value="$signed($summary['commission'])"
                        :detail="'Overall: Rs. '.number_format($summary['overall_commission'])"
                    />

                    <x-dashboard-card
                        title="Total Expense"
                        :value="'-'.number_format($summary['expense'])"
                    />

                    <x-dashboard-card
                        title="Net Profit / Loss"
                        :value="$signed($summary['profit_loss'])"
                        :negative="$summary['profit_loss'] < 0"
                        :detail="'Overall '.($summary['overall_profit_loss'] < 0 ? 'Loss' : 'Profit').': Rs. '.number_format(abs($summary['overall_profit_loss']))"
                        featured
                    />

                </div>

            </section>


            {{-- Other Position --}}
            <section>

                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Other Position
                </h3>

                <div class="grid gap-3 sm:grid-cols-2">

                    <x-dashboard-card
                        title="Remittance Position"
                        :value="$signed($summary['remittance'])"
                    />

                    <x-dashboard-card
                        title="Borrowing Outstanding"
                        :value="number_format($summary['borrowing_outstanding'])"
                    />

                </div>

                <p class="mt-2 text-xs text-gray-500">
                    Share capital and borrowing are funding sources/liabilities
                    and are not added to profit or available cash and bank.
                </p>

            </section>


            {{-- Net Amount --}}
            <section
                class="
                    rounded-lg
                    border border-green-200
                    bg-green-50
                    px-4 py-3
                    shadow-sm
                    sm:px-5
                "
            >

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="text-sm font-semibold text-green-700">
                            Net Amount
                        </div>

                        <div class="mt-0.5 text-xs text-gray-500">
                            Available Cash & Bank + Remittance Position
                        </div>

                    </div>

                    <div class="text-2xl font-bold text-green-700">
                        {{ number_format($summary['net_amount']) }}
                    </div>

                </div>

            </section>


            {{-- Monthly Financial Chart --}}
            <section
                class="
                    rounded-lg
                    border border-gray-200
                    bg-white
                    p-4
                    shadow-sm
                "
            >

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h3 class="text-base font-semibold text-gray-900">
                            Monthly Financial Chart
                        </h3>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Business performance for Nepali FY {{ $financialYear }}
                        </p>

                    </div>

                    <div
                        class="flex flex-wrap gap-x-3 gap-y-1"
                        aria-label="Chart legend"
                    >

                        @foreach ($series as $item)

                            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">

                                <span class="h-2.5 w-2.5 rounded-sm {{ $item['color'] }}"></span>

                                {{ $item['label'] }}

                            </span>

                        @endforeach

                    </div>

                </div>


                {{-- Chart --}}
                <div class="mt-4 overflow-x-auto pb-1">

                    <div
                        class="min-w-[760px]"
                        role="img"
                        aria-label="Monthly income, commission, expense, and profit or loss chart"
                    >

                        <div class="relative h-48 border-y border-gray-200">

                            <div class="absolute inset-x-0 top-1/2 border-t border-dashed border-gray-400"></div>

                            <div class="grid h-full grid-cols-12 gap-2 px-2">

                                @foreach ($monthly as $month)

                                    <div class="relative flex h-full items-stretch justify-center">

                                        @foreach ($series as $item)

                                            @php
                                                $value = $month[$item['key']];

                                                $height =
                                                    abs($value)
                                                    / $chartMax
                                                    * 46;
                                            @endphp

                                            <div
                                                class="
                                                    absolute
                                                    w-2
                                                    {{ $item['color'] }}
                                                    {{
                                                        $value < 0
                                                            ? 'top-1/2 rounded-b-sm'
                                                            : 'bottom-1/2 rounded-t-sm'
                                                    }}
                                                "
                                                style="
                                                    height: {{ $height }}%;
                                                    transform:
                                                    translateX(
                                                        {{ ($loop->index - 1.5) * 9 }}px
                                                    );
                                                "
                                                title="
                                                    {{ $month['label'] }}
                                                    {{ $item['label'] }}:
                                                    {{ number_format($value) }}
                                                "
                                            ></div>

                                        @endforeach

                                    </div>

                                @endforeach

                            </div>

                        </div>


                        <div class="grid grid-cols-12 gap-2 px-2 pt-2">

                            @foreach ($monthly as $month)

                                <div class="text-center text-[10px] font-medium text-gray-600">
                                    {{ $month['label'] }}
                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>


                {{-- Exact Monthly Values --}}
                <div class="mt-3 overflow-x-auto">

                    <table class="min-w-full text-xs">

                        <caption class="sr-only">
                            Exact monthly financial values
                        </caption>

                        <thead>

                            <tr class="border-b text-left text-[11px] uppercase tracking-wide text-gray-500">

                                <th class="py-2 pr-4">
                                    Month
                                </th>

                                <th class="px-3 py-2 text-right">
                                    Income
                                </th>

                                <th class="px-3 py-2 text-right">
                                    Commission
                                </th>

                                <th class="px-3 py-2 text-right">
                                    Expense
                                </th>

                                <th class="py-2 pl-3 text-right">
                                    Profit/Loss
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($monthly as $month)

                                <tr class="border-b border-gray-100 last:border-0">

                                    <th class="py-1.5 pr-4 font-medium text-gray-700">
                                        {{ $month['label'] }}
                                    </th>

                                    <td class="px-3 py-1.5 text-right tabular-nums">
                                        {{ number_format($month['income']) }}
                                    </td>

                                    <td class="px-3 py-1.5 text-right tabular-nums">
                                        {{ number_format($month['commission']) }}
                                    </td>

                                    <td class="px-3 py-1.5 text-right tabular-nums">
                                        {{ number_format($month['expense']) }}
                                    </td>

                                    <td
                                        @class([
                                            'py-1.5 pl-3 text-right tabular-nums',
                                            'text-red-700' => $month['profit_loss'] < 0,
                                        ])
                                    >
                                        {{ $signed($month['profit_loss']) }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </section>

        </div>

    </div>

</x-app-layout>
