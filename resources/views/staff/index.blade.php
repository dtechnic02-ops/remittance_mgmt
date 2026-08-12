<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Staff Management
            </h2>

            <a href="{{ route('staff.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md">
                + New Staff
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
                <div class="p-6 overflow-x-auto">

                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="border-b bg-gray-50 text-left">
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Permissions</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($staffUsers as $staff)
                                <tr class="border-b">

                                    <td class="px-4 py-3 font-medium">
                                        {{ $staff->name }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $staff->email }}
                                    </td>

                                    <td class="px-4 py-3">
                                        @if ($staff->is_active)
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
                                        @forelse ($staff->permissions as $permission)
                                            <span class="inline-block mr-2 mb-1 px-2 py-1 bg-gray-100 rounded text-xs">
                                                {{ $permission->name }}
                                            </span>
                                        @empty
                                            <span class="text-sm text-gray-400">
                                                No permissions assigned
                                            </span>
                                        @endforelse
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-3">

                                            <a href="{{ route('staff.edit', $staff) }}"
                                               class="text-indigo-600">
                                                Edit
                                            </a>

                                            <a href="{{ route('staff-permissions.edit', $staff) }}"
                                               class="text-blue-600">
                                                Permissions
                                            </a>

                                            @if ($staff->is_active)
                                                <form method="POST"
                                                      action="{{ route('staff.destroy', $staff) }}"
                                                      onsubmit="return confirm('Deactivate this staff user?');">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-red-600">
                                                        Deactivate
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
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

                    <div class="mt-6">
                        {{ $staffUsers->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>