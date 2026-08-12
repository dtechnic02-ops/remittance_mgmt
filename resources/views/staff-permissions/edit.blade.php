<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Staff Permissions
            </h2>

            <a href="{{ route('staff-permissions.index') }}"
               class="text-gray-600 hover:text-gray-900">
                Back
            </a>

        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="border-b pb-5">

                        <div class="text-sm text-gray-500">
                            Staff
                        </div>

                        <div class="mt-1 text-lg font-semibold">
                            {{ $user->name }}
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            {{ $user->email }}
                        </div>

                    </div>

                    <form method="POST"
                          action="{{ route('staff-permissions.update', $user) }}"
                          class="mt-6">

                        @csrf
                        @method('PUT')

                        @error('permissions')
                            <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('permissions.*')
                            <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
                                {{ $message }}
                            </div>
                        @enderror

                        @forelse ($permissions->groupBy('group') as $group => $groupPermissions)

                            <div class="mb-8">

                                <h3 class="border-b pb-2 text-base font-semibold text-gray-800">
                                    {{ $group ?: 'General' }}
                                </h3>

                                <div class="mt-4 space-y-3">

                                    @foreach ($groupPermissions as $permission)

                                        <label class="flex items-start gap-3 rounded-md border p-4">

                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   @checked(in_array(
                                                       $permission->id,
                                                       $assignedPermissionIds
                                                   ))
                                                   class="mt-1 rounded border-gray-300">

                                            <div>

                                                <div class="font-medium text-gray-800">
                                                    {{ $permission->name }}
                                                </div>

                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $permission->code }}
                                                </div>

                                                @if ($permission->description)
                                                    <div class="mt-1 text-sm text-gray-500">
                                                        {{ $permission->description }}
                                                    </div>
                                                @endif

                                            </div>

                                        </label>

                                    @endforeach

                                </div>

                            </div>

                        @empty

                            <div class="rounded-md bg-gray-50 p-5 text-gray-500">
                                No permissions are available.
                            </div>

                        @endforelse

                        <div class="mt-8 flex justify-end gap-3">

                            <a href="{{ route('staff-permissions.index') }}"
                               class="rounded-md border border-gray-300 px-5 py-2 text-gray-700">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="rounded-md bg-gray-800 px-5 py-2 text-white">
                                Update Permissions
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>