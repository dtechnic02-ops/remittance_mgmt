<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Staff Permission Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-5 rounded-md bg-green-100 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Staff Users
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Assign or remove permissions for individual staff members.
                        </p>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="min-w-full border-collapse">

                            <thead>
                                <tr class="border-b bg-gray-50 text-left">
                                    <th class="px-4 py-3">
                                        Staff
                                    </th>

                                    <th class="px-4 py-3">
                                        Email
                                    </th>

                                    <th class="px-4 py-3">
                                        Status
                                    </th>

                                    <th class="px-4 py-3">
                                        Permissions
                                    </th>

                                    <th class="px-4 py-3 text-right">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($staffUsers as $staff)

                                    <tr class="border-b">

                                        <td class="px-4 py-4 font-medium">
                                            {{ $staff->name }}
                                        </td>

                                        <td class="px-4 py-4">
                                            {{ $staff->email }}
                                        </td>

                                        <td class="px-4 py-4">
                                            @if ($staff->status === 'active')
                                                <span class="font-medium text-green-700">
                                                    Active
                                                </span>
                                            @else
                                                <span class="font-medium text-red-700">
                                                    {{ ucfirst($staff->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4">

                                            @forelse ($staff->permissions as $permission)

                                                <span class="mr-2 mb-1 inline-block rounded bg-gray-100 px-2 py-1 text-xs">
                                                    {{ $permission->name }}
                                                </span>

                                            @empty

                                                <span class="text-sm text-gray-400">
                                                    No permissions assigned
                                                </span>

                                            @endforelse

                                        </td>

                                        <td class="px-4 py-4 text-right">

                                            <a href="{{ route('staff-permissions.edit', $staff) }}"
                                               class="text-blue-600 hover:underline">
                                                Manage Permissions
                                            </a>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="5"
                                            class="px-4 py-10 text-center text-gray-500">
                                            No staff users found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>