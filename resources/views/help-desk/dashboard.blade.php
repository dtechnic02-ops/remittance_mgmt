<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Help Desk Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">Read-only operational overview</p>
        </div>
    </x-slot>

    @php
        $signed = fn (int $value) => ($value < 0 ? '-' : '').number_format(abs($value));
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section>
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Operational Position</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-dashboard-card title="Customers" :value="number_format($summary['customers'])" />
                    <x-dashboard-card title="Cash Balance" :value="$signed($summary['cash'])" />
                    <x-dashboard-card title="Bank Balance" :value="$signed($summary['bank'])" />
                    <x-dashboard-card title="Remittance Position" :value="$signed($summary['remittance'])" />
                    <x-dashboard-card title="Fixed Deposit Balance" :value="$signed($summary['fixed_deposit'])" />
                </div>
            </section>

            <section>
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Business Result — FY {{ $financialYear }}</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-dashboard-card title="Total Income" :value="$signed($summary['income'])" :detail="'Overall: Rs. '.number_format($summary['overall_income'])" />
                    <x-dashboard-card title="Total Commission" :value="$signed($summary['commission'])" :detail="'Overall: Rs. '.number_format($summary['overall_commission'])" />
                    <x-dashboard-card title="Total Expense" :value="'-'.number_format($summary['expense'])" />
                    <x-dashboard-card title="Net Profit / Loss" :value="$signed($summary['profit_loss'])" :negative="$summary['profit_loss'] < 0" :detail="'Overall '.($summary['overall_profit_loss'] < 0 ? 'Loss' : 'Profit').': Rs. '.number_format(abs($summary['overall_profit_loss']))" featured />
                </div>
            </section>

            <section>
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Other Position</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-dashboard-card title="Borrowing Outstanding" :value="number_format($summary['borrowing_outstanding'])" />
                    <x-dashboard-card title="Share Capital" :value="number_format($summary['share_capital'])" />
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
