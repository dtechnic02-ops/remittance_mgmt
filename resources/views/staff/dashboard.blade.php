<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Staff Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ auth()->user()->name }} — Your available operations
            </p>
        </div>
    </x-slot>

    @php
        $user = auth()->user();
        $modules = [
            ['title' => 'Customers', 'description' => 'View and manage customer records.', 'view' => 'customer.view', 'viewRoute' => 'customers.index', 'create' => 'customer.create', 'createRoute' => 'customers.create', 'createLabel' => 'New Customer'],
            ['title' => 'Remittances', 'description' => 'Review remittance transactions and create new entries.', 'view' => 'remittance.view', 'viewRoute' => 'remittances.index', 'create' => 'remittance.create', 'createRoute' => 'remittances.create', 'createLabel' => 'New Remittance'],
            ['title' => 'Account Transfers', 'description' => 'Review transfers between financial accounts.', 'view' => 'account-transfer.view', 'viewRoute' => 'account-transfers.index', 'create' => 'account-transfer.create', 'createRoute' => 'account-transfers.create', 'createLabel' => 'New Transfer'],
            ['title' => 'Ledger', 'description' => 'Review authorized financial ledger entries.', 'view' => 'ledger.view', 'viewRoute' => 'ledger.index'],
            ['title' => 'Expenses', 'description' => 'Review business expenses and record new expenses.', 'view' => 'expense.view', 'viewRoute' => 'expenses.index', 'create' => 'expense.create', 'createRoute' => 'expenses.create', 'createLabel' => 'New Expense'],
            ['title' => 'Income', 'description' => 'Review income entries and record new income.', 'view' => 'income.view', 'viewRoute' => 'incomes.index', 'create' => 'income.create', 'createRoute' => 'incomes.create', 'createLabel' => 'New Income'],
            ['title' => 'Shareholders', 'description' => 'View and maintain authorized shareholder records.', 'view' => 'shareholder.view', 'viewRoute' => 'shareholders.index', 'create' => 'shareholder.create', 'createRoute' => 'shareholders.create', 'createLabel' => 'New Shareholder'],
            ['title' => 'Share Transactions', 'description' => 'Review share activity and record authorized transactions.', 'view' => 'share-transaction.view', 'viewRoute' => 'share-transactions.index', 'create' => 'share-transaction.create', 'createRoute' => 'share-transactions.create', 'createLabel' => 'New Share Transaction'],
            ['title' => 'Lenders', 'description' => 'View and maintain authorized lender records.', 'view' => 'lender.view', 'viewRoute' => 'lenders.index', 'create' => 'lender.create', 'createRoute' => 'lenders.create', 'createLabel' => 'New Lender'],
            ['title' => 'Borrowings', 'description' => 'Review borrowing activity and record new transactions.', 'view' => 'borrowing.view', 'viewRoute' => 'borrowings.index', 'create' => 'borrowing.create', 'createRoute' => 'borrowings.create', 'createLabel' => 'New Borrowing'],
            ['title' => 'Expense Categories', 'description' => 'View or manage expense categories.', 'view' => 'expense-category.view', 'viewRoute' => 'expense-categories.index', 'create' => 'expense-category.manage', 'createRoute' => 'expense-categories.create', 'createLabel' => 'Manage Categories'],
            ['title' => 'Income Categories', 'description' => 'View or manage income categories.', 'view' => 'income-category.view', 'viewRoute' => 'income-categories.index', 'create' => 'income-category.manage', 'createRoute' => 'income-categories.create', 'createLabel' => 'Manage Categories'],
        ];

        $visibleModules = collect($modules)->filter(fn ($module) =>
            $user->hasPermission($module['view'])
            || (isset($module['create']) && $user->hasPermission($module['create']))
        );
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($visibleModules->isEmpty())
                <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm sm:p-10">
                    <h3 class="text-lg font-semibold text-gray-800">No operations available</h3>
                    <p class="mx-auto mt-2 max-w-xl text-sm text-gray-600">
                        No operational permissions have been assigned to your account yet. Please contact the administrator.
                    </p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($visibleModules as $module)
                        <section class="flex flex-col rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $module['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-gray-600">{{ $module['description'] }}</p>
                            </div>

                            <div class="mt-5 flex flex-col gap-2 sm:flex-row">
                                @if ($user->hasPermission($module['view']))
                                    <a href="{{ route($module['viewRoute']) }}"
                                       class="inline-flex min-h-11 items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        View {{ $module['title'] }}
                                    </a>
                                @endif

                                @if (isset($module['create']) && $user->hasPermission($module['create']))
                                    <a href="{{ route($module['createRoute']) }}"
                                       class="inline-flex min-h-11 items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                        {{ $module['createLabel'] }}
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
