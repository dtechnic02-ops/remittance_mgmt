<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    User Management
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Manage Staff and Shareholder login access
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full space-y-5 px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="rounded-xl bg-white p-5 shadow-sm">

                <form method="GET"
                      action="{{ route('users.index') }}"
                      class="grid grid-cols-1 gap-4 md:grid-cols-4">

                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <label for="search"
                               class="mb-1 block text-sm font-medium text-gray-700">
                            Search
                        </label>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Name or email..."
                            class="block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="role"
                               class="mb-1 block text-sm font-medium text-gray-700">
                            Role
                        </label>

                        <select
                            id="role"
                            name="role"
                            class="block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="">All Roles</option>

                            <option value="staff"
                                @selected($role === 'staff')>
                                Staff
                            </option>

                            <option value="shareholder"
                                @selected($role === 'shareholder')>
                                Shareholder
                            </option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status"
                               class="mb-1 block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="">All Status</option>

                            <option value="active"
                                @selected($status === 'active')>
                                Active
                            </option>

                            <option value="blocked"
                                @selected($status === 'blocked')>
                                Blocked
                            </option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2 md:col-span-4">

                        <button
                            type="submit"
                            class="rounded-md bg-gray-800 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('users.index') }}"
                            class="rounded-md border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>

                    </div>

                </form>

            </div>


            {{-- User List --}}
            <div class="overflow-hidden rounded-xl bg-white shadow-sm">

                <div class="border-b border-gray-200 px-5 py-4 sm:px-6">
                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="font-semibold text-gray-900">
                                Users
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Staff and Shareholder login accounts
                            </p>
                        </div>

                        <div class="text-sm text-gray-500">
                            Total: {{ $users->total() }}
                        </div>

                    </div>
                </div>


                {{-- Desktop / Tablet Table --}}
                <div class="hidden overflow-x-auto md:block">

                    <table class="min-w-full border-collapse">

                        <thead>
                            <tr class="border-b bg-gray-50 text-left text-sm text-gray-600">

                                <th class="px-5 py-3">
                                    User
                                </th>

                                <th class="px-5 py-3">
                                    Email
                                </th>

                                <th class="px-5 py-3">
                                    Role
                                </th>

                                <th class="px-5 py-3">
                                    Status
                                </th>

                                <th class="px-5 py-3 text-right">
                                    Action
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse ($users as $user)

                                <tr class="hover:bg-gray-50">

                                    {{-- User --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 font-bold text-gray-600">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>

                                            <div>
                                                <div class="font-semibold text-gray-900">
                                                    {{ $user->name }}
                                                </div>

                                                <div class="text-xs text-gray-400">
                                                    ID: {{ $user->id }}
                                                </div>
                                            </div>

                                        </div>

                                    </td>

                                    {{-- Email --}}
                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        {{ $user->email }}
                                    </td>

                                    {{-- Role --}}
                                    <td class="px-5 py-4">

                                        @if ($user->role === 'staff')

                                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                                Staff
                                            </span>

                                        @elseif ($user->role === 'shareholder')

                                            <span class="inline-flex rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                                Shareholder
                                            </span>

                                        @else

                                            <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                                {{ ucfirst($user->role) }}
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Status --}}
                                    <td class="px-5 py-4">

                                        @if ($user->is_active)

                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                                Active
                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                                Blocked
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Action --}}
                                    <td class="px-5 py-4 text-right">

                                        @if ($user->is_active)

                                            <form
                                                method="POST"
                                                action="{{ route('users.block', $user) }}"
                                                class="inline"
                                                onsubmit="return confirm('Block this user? This user will not be able to log in.');"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="rounded-md border border-red-300 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50"
                                                >
                                                    Block
                                                </button>
                                            </form>

                                        @else

                                            <form
                                                method="POST"
                                                action="{{ route('users.unblock', $user) }}"
                                                class="inline"
                                                onsubmit="return confirm('Unblock this user?');"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="rounded-md border border-green-300 px-3 py-2 text-sm font-medium text-green-700 hover:bg-green-50"
                                                >
                                                    Unblock
                                                </button>
                                            </form>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-5 py-12 text-center text-gray-500"
                                    >
                                        No users found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>



                @if ($users->hasPages())
                    <div class="border-t border-gray-100 p-5">
                        {{ $users->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
