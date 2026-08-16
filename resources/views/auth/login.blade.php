<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto mb-3 flex h-20 w-20 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="currentColor"
                 class="h-12 w-12">
                <path fill-rule="evenodd"
                      d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.75 20.105a8.25 8.25 0 0 1 16.5 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5a18.683 18.683 0 0 1-7.813-1.7.75.75 0 0 1-.437-.695Z"
                      clip-rule="evenodd" />
            </svg>
        </div>

        <div class="text-5xl font-extrabold tracking-tight text-slate-900">
            DG
        </div>

        <div class="mt-1 text-lg font-semibold uppercase tracking-[0.35em] text-emerald-600">
            Enterprises
        </div>

        <div class="mt-2 text-sm font-medium text-slate-500">
            Bhume Rukum East Nepal
        </div>

        <h1 class="mt-5 text-2xl font-semibold text-slate-800">
            Welcome to
            <span class="text-emerald-600">DG Enterprises</span>
        </h1>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>