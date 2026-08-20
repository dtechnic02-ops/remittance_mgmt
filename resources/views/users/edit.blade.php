<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Edit User</h2></x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white shadow-sm">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-700">Role</span>
                        <p class="mt-1 rounded-md bg-gray-50 px-3 py-2 text-gray-700">
                            {{ $user->isHelpDesk() ? 'Help Desk' : ucfirst($user->role) }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">Role cannot be changed while editing.</p>
                    </div>
                    <div class="border-t pt-6">
                        <p class="text-sm text-gray-500">Leave password fields blank to keep the current password.</p>
                        <label for="password" class="mt-4 block text-sm font-medium text-gray-700">New Password</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <label for="password_confirmation" class="mt-4 block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ? '1' : '0') == '1') class="rounded border-gray-300">
                        <span class="text-sm font-medium text-gray-700">Active User</span>
                    </label>

                    <div class="flex justify-end gap-3 border-t pt-6">
                        <a href="{{ route('users.index') }}" class="rounded-md border border-gray-300 px-5 py-2 text-gray-700">Cancel</a>
                        <button class="rounded-md bg-gray-800 px-5 py-2 text-white">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
