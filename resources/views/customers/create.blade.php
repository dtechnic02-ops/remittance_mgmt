<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Customer
            </h2>

            <a href="{{ route('customers.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Customers
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <div class="font-medium text-red-800 mb-2">
                        Please correct the following errors:
                    </div>

                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">

                <form method="POST"
                      action="{{ route('customers.store') }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf

                    {{-- Customer Information --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Customer Information
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Customer Code will be generated automatically.
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Name --}}
                        <div>
                            <label for="name"
                                   class="block text-sm font-medium text-gray-700">
                                Customer Name *
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   maxlength="150"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Mobile --}}
                        <div>
                            <label for="mobile"
                                   class="block text-sm font-medium text-gray-700">
                                Mobile
                            </label>

                            <input type="text"
                                   id="mobile"
                                   name="mobile"
                                   value="{{ old('mobile') }}"
                                   maxlength="30"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone"
                                   class="block text-sm font-medium text-gray-700">
                                Phone
                            </label>

                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   maxlength="30"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email"
                                   class="block text-sm font-medium text-gray-700">
                                Email
                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   maxlength="150"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('email')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Citizenship Number --}}
                        <div>
                            <label for="citizenship_number"
                                   class="block text-sm font-medium text-gray-700">
                                Citizenship Number
                            </label>

                            <input type="text"
                                   id="citizenship_number"
                                   name="citizenship_number"
                                   value="{{ old('citizenship_number') }}"
                                   maxlength="100"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Optional
                            </p>

                            @error('citizenship_number')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address"
                                   class="block text-sm font-medium text-gray-700">
                                Address
                            </label>

                            <input type="text"
                                   id="address"
                                   name="address"
                                   value="{{ old('address') }}"
                                   maxlength="255"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('address')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- Documents --}}
                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Photo & Citizenship Documents
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            JPG, JPEG and PNG images only.
                        </p>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- Customer Photo --}}
                            <div>
                                <label for="photo"
                                       class="block text-sm font-medium text-gray-700">
                                    Customer Photo
                                </label>

                                <input type="file"
                                       id="photo"
                                       name="photo"
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                       class="mt-2 block w-full text-sm">

                                <p class="mt-1 text-xs text-gray-500">
                                    One image only
                                </p>

                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Citizenship Front --}}
                            <div>
                                <label for="citizenship_front"
                                       class="block text-sm font-medium text-gray-700">
                                    Citizenship Front
                                </label>

                                <input type="file"
                                       id="citizenship_front"
                                       name="citizenship_front"
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                       class="mt-2 block w-full text-sm">

                                <p class="mt-1 text-xs text-gray-500">
                                    One image only
                                </p>

                                @error('citizenship_front')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Citizenship Back --}}
                            <div>
                                <label for="citizenship_back"
                                       class="block text-sm font-medium text-gray-700">
                                    Citizenship Back
                                </label>

                                <input type="file"
                                       id="citizenship_back"
                                       name="citizenship_back"
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                       class="mt-2 block w-full text-sm">

                                <p class="mt-1 text-xs text-gray-500">
                                    One image only
                                </p>

                                @error('citizenship_back')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Other Documents --}}
                    <div class="mt-8">

                        <label for="other_documents"
                               class="block text-sm font-medium text-gray-700">
                            Other Documents
                        </label>

                        <input type="file"
                               id="other_documents"
                               name="other_documents[]"
                               multiple
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               class="mt-2 block w-full text-sm">

                        <p class="mt-1 text-xs text-gray-500">
                            Multiple images can be selected. JPG, JPEG and PNG only.
                        </p>

                        @error('other_documents')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        @error('other_documents.*')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    {{-- Note --}}
                    <div class="mt-8">

                        <label for="note"
                               class="block text-sm font-medium text-gray-700">
                            Note
                        </label>

                        <textarea id="note"
                                  name="note"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note') }}</textarea>

                        @error('note')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    {{-- Buttons --}}
                    <div class="mt-8 border-t pt-6 flex justify-end gap-3">

                        <a href="{{ route('customers.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save Customer
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>