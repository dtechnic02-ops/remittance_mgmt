<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    New Shareholder
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Shareholder Master / KYC
                </p>
            </div>

            <a href="{{ route('shareholders.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Shareholders
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('shareholders.store') }}"
                  enctype="multipart/form-data">

                @csrf

                {{-- Personal Information --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b">
                        <h3 class="font-semibold text-lg text-gray-800">
                            Personal Information
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Code *
                                </label>

                                <input type="text"
                                       name="code"
                                       value="{{ old('code') }}"
                                       required
                                       maxlength="50"
                                       placeholder="e.g. SH-001"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            @if (auth()->user()->isAdmin())
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Shareholder Login
                                    </label>
                                    <select name="user_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">Not linked yet</option>
                                        @foreach ($shareholderUsers as $shareholderUser)
                                            <option value="{{ $shareholderUser->id }}"
                                                @selected((string) old('user_id') === (string) $shareholderUser->id)>
                                                {{ $shareholderUser->name }} — {{ $shareholderUser->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Name *
                                </label>

                                <input type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       maxlength="150"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Mobile
                                </label>

                                <input type="text"
                                       name="mobile"
                                       value="{{ old('mobile') }}"
                                       maxlength="30"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>

                                <input type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       maxlength="150"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Address
                                </label>

                                <textarea name="address"
                                          rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('address') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Type
                                </label>

                                <select name="type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        Select Type
                                    </option>

                                    <option value="saving"
                                        @selected(old('type') === 'saving')>
                                        Saving
                                    </option>

                                    <option value="permanent"
                                        @selected(old('type') === 'permanent')>
                                        Permanent
                                    </option>

                                </select>

                                <p class="mt-1 text-xs text-gray-500">
                                    अर्को shareholder type पछि थप्न सकिन्छ।
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>

                                <div class="mt-3">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox"
                                               name="is_active"
                                               value="1"
                                               @checked(old('is_active', true))
                                               class="rounded border-gray-300">

                                        <span class="ms-2 text-sm text-gray-700">
                                            Active
                                        </span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

               

                {{-- KYC --}}
                <div class="mt-6 bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b">
                        <h3 class="font-semibold text-lg text-gray-800">
                            KYC Documents
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Photo
                                </label>

                                <input type="file"
                                       name="photo"
                                       accept=".jpg,.jpeg,.png"
                                       class="mt-1 block w-full rounded-md border border-gray-300 p-2">

                                <p class="mt-1 text-xs text-gray-500">
                                    JPG, JPEG or PNG — Maximum 5 MB
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Citizenship Front
                                </label>

                                <input type="file"
                                       name="citizenship_front"
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       class="mt-1 block w-full rounded-md border border-gray-300 p-2">

                                <p class="mt-1 text-xs text-gray-500">
                                    Image or PDF — Maximum 5 MB
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Citizenship Back
                                </label>

                                <input type="file"
                                       name="citizenship_back"
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       class="mt-1 block w-full rounded-md border border-gray-300 p-2">

                                <p class="mt-1 text-xs text-gray-500">
                                    Image or PDF — Maximum 5 MB
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Other Document
                                </label>

                                <input type="file"
                                       name="other_document"
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       class="mt-1 block w-full rounded-md border border-gray-300 p-2">

                                <p class="mt-1 text-xs text-gray-500">
                                    Image or PDF — Maximum 5 MB
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="mt-6 bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-700">
                            Note
                        </label>

                        <textarea name="note"
                                  rows="4"
                                  placeholder="Additional shareholder information..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('shareholders.index') }}"
                       class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-5 py-2 bg-gray-800 text-white rounded-md">
                        Save Shareholder
                    </button>
                </div>

            </form>

        </div>
    </div>

 

</x-app-layout>
