<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Staff
            </h2>

            <a href="{{ route('staff.index') }}"
               class="text-gray-600 hover:text-gray-900">
                Back to Staff
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-5 rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST"
                      action="{{ route('staff.store') }}"
                      class="p-6">

                    @csrf

                    <div>
                        <label for="name"
                               class="block text-sm font-medium text-gray-700">
                            Staff Name *
                        </label>

                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               autofocus
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="email"
                               class="block text-sm font-medium text-gray-700">
                            Email *
                        </label>

                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                        @error('email')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="password"
                               class="block text-sm font-medium text-gray-700">
                            Password *
                        </label>

                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                        @error('password')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="password_confirmation"
                               class="block text-sm font-medium text-gray-700">
                            Confirm Password *
                        </label>

                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div class="mt-6">
                        <label class="flex items-center gap-3">

                            <input type="hidden"
                                   name="is_active"
                                   value="0">

                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', '1') == '1')
                                   class="rounded border-gray-300">

                            <span class="text-sm font-medium text-gray-700">
                                Active Staff
                            </span>

                        </label>

                        <p class="mt-1 ml-7 text-xs text-gray-500">
                            Inactive staff cannot be used as an active system user.
                        </p>
                    </div>

                    <div class="mt-8 border-t pt-6">

                        <div class="rounded-md bg-gray-50 p-4 text-sm text-gray-600">
                            Staff permissions are assigned separately after
                            creating the staff account.
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('staff.index') }}"
                           class="rounded-md border border-gray-300 px-5 py-2 text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="rounded-md bg-gray-800 px-5 py-2 text-white">
                            Create Staff
                        </button>

                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>