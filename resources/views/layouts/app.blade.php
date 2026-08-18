<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Remittance Management System') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

<div
    x-data="{ sidebarOpen: false }"
    class="min-h-screen"
>

    {{-- Mobile Overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 lg:hidden"
    ></div>

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="
            fixed inset-y-0 left-0 z-50
            w-72
            transform
            bg-gray-900
            text-white
            transition-transform duration-200 ease-in-out
            lg:translate-x-0
        "
    >

        {{-- Logo --}}
        <div class="flex h-16 items-center justify-between border-b border-gray-800 px-5">

            <a href="{{ route('dashboard') }}"
               class="text-lg font-bold tracking-wide">
                Remittance MGMT
            </a>

            <button
                type="button"
                @click="sidebarOpen = false"
                class="rounded-md p-2 text-gray-300 hover:bg-gray-800 hover:text-white lg:hidden"
            >
                ✕
            </button>

        </div>

        {{-- User --}}
        <div class="border-b border-gray-800 px-5 py-4">

            <div class="text-sm font-semibold">
                {{ auth()->user()->name }}
            </div>

            <div class="mt-1 text-xs text-gray-400">
                {{ ucfirst(auth()->user()->role) }}
            </div>

        </div>

        {{-- Navigation --}}
        <nav class="h-[calc(100vh-145px)] overflow-y-auto px-3 py-4">

            <div class="space-y-1">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="
                        block rounded-md px-4 py-2.5 text-sm
                        {{ request()->routeIs('dashboard', '*.dashboard')
                            ? 'bg-gray-800 text-white'
                            : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                        }}
                   ">
                    Dashboard
                </a>

                {{-- Customers --}}
                @if (auth()->user()->hasPermission('customer.view'))
                    <a href="{{ route('customers.index') }}"
                       class="
                            block rounded-md px-4 py-2.5 text-sm
                            {{ request()->routeIs('customers.*')
                                ? 'bg-gray-800 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                            }}
                       ">
                        Customers
                    </a>
                @endif

                {{-- Remittance --}}
                @if (auth()->user()->hasPermission('remittance.view'))
                    <a href="{{ route('remittances.index') }}"
                       class="
                            block rounded-md px-4 py-2.5 text-sm
                            {{ request()->routeIs('remittances.*')
                                ? 'bg-gray-800 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                            }}
                       ">
                        Remittances
                    </a>
                @endif
@if (auth()->user()->hasPermission('account-transfer.view'))
    <a href="{{ route('account-transfers.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('account-transfers.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Account Transfers
    </a>
@endif
                {{-- Accounts --}}
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('accounts.index') }}"
                       class="
                            block rounded-md px-4 py-2.5 text-sm
                            {{ request()->routeIs('accounts.*')
                                ? 'bg-gray-800 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                            }}
                       ">
                        Accounts
                    </a>

                    <a href="{{ route('opening-balances.index') }}"
                       class="
                            block rounded-md px-4 py-2.5 text-sm
                            {{ request()->routeIs('opening-balances.*')
                                ? 'bg-gray-800 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                            }}
                       ">
                        Opening Balances
                    </a>
                @endif
                @if (auth()->user()->hasPermission('ledger.view'))
    <a href="{{ route('ledger.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('ledger.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Ledger
    </a>
@endif
@if (auth()->user()->hasPermission('expense.view'))
    <a href="{{ route('expenses.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('expenses.*') ||
               request()->routeIs('expense-categories.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Expense
    </a>
@endif
@if (auth()->user()->hasPermission('income.view'))
    <a href="{{ route('incomes.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('incomes.*') ||
               request()->routeIs('income-categories.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Income
    </a>
@endif
@if (auth()->user()->hasPermission('shareholder.view'))
    <a href="{{ route('shareholders.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('shareholders.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Shareholders
    </a>
@endif
@if (auth()->user()->hasPermission('lender.view'))
    <a href="{{ route('lenders.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('lenders.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Lenders
    </a>
@endif
@if (auth()->user()->hasPermission('share-transaction.view'))
    <a href="{{ route('share-transactions.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('share-transactions.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Share Transactions
    </a>
@endif
@if (
    auth()->user()->isAdmin() ||
    (
        auth()->user()->isStaff() &&
        auth()->user()->hasPermission('share-transfer.view')
    ) || auth()->user()->isShareholder()
)
    <a href="{{ route('share-transfers.index') }}"
       class="
            block rounded-md px-4 py-2.5 text-sm
            {{ request()->routeIs('share-transfers.*')
                ? 'bg-gray-800 text-white'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            }}
       ">
        Share Transfers
    </a>
@endif
                {{-- Administration --}}
                @if (auth()->user()->isAdmin())

                    <div class="pt-4">

                        <div class="px-4 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Administration
                        </div>

                        <details
                            class="group"
                            @if (
                                request()->routeIs('users.*') ||
                                request()->routeIs('staff.*') ||
                                request()->routeIs('staff-permissions.*') ||
                                request()->routeIs('company-info.*') ||
                                request()->routeIs('company-links.*')
                            )
                                open
                            @endif
                        >
                            <summary
                                class="
                                    flex cursor-pointer list-none items-center justify-between
                                    rounded-md px-4 py-2.5 text-sm
                                    text-gray-300 hover:bg-gray-800 hover:text-white
                                "
                            >
                                <span>Admin</span>
                                <span class="text-xs transition-transform group-open:rotate-180">▼</span>
                            </summary>

                            <div class="mt-1 space-y-1 pl-3">

                                <a href="{{ route('users.index') }}"
                                   class="
                                        block rounded-md px-4 py-2 text-sm
                                        {{ request()->routeIs('users.*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                                        }}
                                   ">
                                    Users
                                </a>

                                <a href="{{ route('staff.index') }}"
                                   class="
                                        block rounded-md px-4 py-2 text-sm
                                        {{ request()->routeIs('staff.*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                                        }}
                                   ">
                                    Staff
                                </a>

                                <a href="{{ route('staff-permissions.index') }}"
                                   class="
                                        block rounded-md px-4 py-2 text-sm
                                        {{ request()->routeIs('staff-permissions.*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                                        }}
                                   ">
                                    Staff Permissions
                                </a>

                                <a href="{{ route('company-info.edit') }}"
                                   class="
                                        block rounded-md px-4 py-2 text-sm
                                        {{ request()->routeIs('company-info.*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                                        }}
                                   ">
                                    Company Info
                                </a>

                                <a href="{{ route('company-links.index') }}"
                                   class="
                                        block rounded-md px-4 py-2 text-sm
                                        {{ request()->routeIs('company-links.*')
                                            ? 'bg-gray-800 text-white'
                                            : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                                        }}
                                   ">
                                    Company Links
                                </a>

                            </div>
                        </details>

                    </div>

                @endif

                {{-- Shareholder --}}
                @if (auth()->user()->isShareholder())
                    <a href="{{ route('shareholder.dashboard') }}"
                       class="
                            block rounded-md px-4 py-2.5 text-sm
                            {{ request()->routeIs('shareholder.*')
                                ? 'bg-gray-800 text-white'
                                : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                            }}
                       ">
                        My Shares
                    </a>
                @endif

            </div>

        </nav>

    </aside>

    {{-- Main Area --}}
    <div class="min-h-screen lg:pl-72">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b bg-white px-4 shadow-sm sm:px-6">

            <div class="flex items-center gap-3">

                {{-- Mobile Menu Button --}}
                <button
                    type="button"
                    @click="sidebarOpen = true"
                    class="rounded-md border border-gray-300 p-2 text-gray-700 hover:bg-gray-100 lg:hidden"
                >
                    ☰
                </button>

                <div class="hidden sm:block text-sm font-medium text-gray-700">
                    {{ config('app.name', 'Remittance Management System') }}
                </div>

            </div>

            {{-- User / Logout --}}
            <div class="flex items-center gap-4">

                <div class="hidden md:block text-right">

                    <div class="text-sm font-medium text-gray-800">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="text-xs text-gray-500">
                        {{ auth()->user()->email }}
                    </div>

                </div>

                <form method="POST"
                      action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Logout
                    </button>
                </form>

            </div>

        </header>

        {{-- Page Heading --}}
        @isset($header)

            <div class="border-b bg-white">
                <div class="px-4 py-5 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </div>

        @endisset

        {{-- Page Content --}}
        <main class="min-w-0">
            {{ $slot }}
        </main>

    </div>

</div>

</body>
</html>
