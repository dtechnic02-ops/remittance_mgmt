<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Shareholder Login Account
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Create or manage login access for this shareholder.
                </p>
            </div>

            <a href="{{ route('shareholders.show', $shareholder) }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Shareholder
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="border-b border-gray-200 p-6">
                    <div class="text-sm text-gray-500">
                        Shareholder
                    </div>

                    <div class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $shareholder->name }}
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Code: {{ $shareholder->code }}
                    </div>

                    <div class="mt-3">
                        @if ($user)
                            @if ($user->is_active)
                                <span class="inline-flex rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    Login Account Active
                                </span>
                            @else
                                <span class="inline-flex rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    Login Account Inactive
                                </span>
                            @endif
                        @else
                            <span class="inline-flex rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                No Login Account
                            </span>
                        @endif
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('shareholder-accounts.update', $shareholder) }}"
                      class="p-6">

                    @csrf
                    @method('PUT')

                    <div class="space-y-6">

                        <div>
                            <label for="email"
                                   class="block text-sm font-medium text-gray-700">
                                Login Email *
                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $user?->email ?? $shareholder->email) }}"
                                   required
                                   autocomplete="off"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Shareholder will use this email to log in.
                            </p>

                            @error('email')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password"
                                   class="block text-sm font-medium text-gray-700">
                                Password {{ $user ? '' : '*' }}
                            </label>

                            <input type="password"
                                   id="password"
                                   name="password"
                                   {{ $user ? '' : 'required' }}
                                   autocomplete="new-password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @if ($user)
                                <p class="mt-1 text-xs text-gray-500">
                                    Leave blank to keep the current password.
                                </p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">
                                    Minimum 6 characters.
                                </p>
                            @endif

                            @error('password')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation"
                                   class="block text-sm font-medium text-gray-700">
                                Confirm Password {{ $user ? '' : '*' }}
                            </label>

                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   {{ $user ? '' : 'required' }}
                                   autocomplete="new-password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="rounded-md border border-gray-200 p-4">
                            <label class="flex items-center gap-3">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       @checked(old('is_active', $user ? $user->is_active : true))
                                       class="rounded border-gray-300">

                                <span>
                                    <span class="block text-sm font-medium text-gray-800">
                                        Active Login Account
                                    </span>

                                    <span class="block text-xs text-gray-500">
                                        If disabled, this shareholder cannot log in.
                                    </span>
                                </span>
                            </label>
                        </div>

                    </div>

                    <div class="mt-8 flex items-center justify-between gap-3">

                        <div>
                            @if ($user && $user->is_active)
                                <button type="submit"
                                        form="deactivate-shareholder-account"
                                        class="rounded-md border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                                    Deactivate Login
                                </button>
                            @endif
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('shareholders.show', $shareholder) }}"
                               class="rounded-md border border-gray-300 px-5 py-2 text-sm text-gray-700">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white">
                                {{ $user ? 'Update Login Account' : 'Create Login Account' }}
                            </button>
                        </div>

                    </div>

                </form>

                @if ($user && $user->is_active)
                    <form id="deactivate-shareholder-account"
                          method="POST"
                          action="{{ route('shareholder-accounts.deactivate', $shareholder) }}"
                          class="hidden">
                        @csrf
                    </form>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>