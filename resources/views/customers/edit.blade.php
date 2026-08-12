<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Customer
            </h2>

            <a href="{{ route('customers.show', $customer) }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Customer
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
                      action="{{ route('customers.update', $customer) }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf
                    @method('PUT')

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Customer Information
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Customer Code: {{ $customer->customer_code }}
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label for="name"
                                   class="block text-sm font-medium text-gray-700">
                                Customer Name *
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $customer->name) }}"
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
                            <label for="mobile"
                                   class="block text-sm font-medium text-gray-700">
                                Mobile
                            </label>

                            <input type="text"
                                   id="mobile"
                                   name="mobile"
                                   value="{{ old('mobile', $customer->mobile) }}"
                                   maxlength="30"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone"
                                   class="block text-sm font-medium text-gray-700">
                                Phone
                            </label>

                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $customer->phone) }}"
                                   maxlength="30"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="email"
                                   class="block text-sm font-medium text-gray-700">
                                Email
                            </label>

                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $customer->email) }}"
                                   maxlength="150"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('email')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="citizenship_number"
                                   class="block text-sm font-medium text-gray-700">
                                Citizenship Number
                            </label>

                            <input type="text"
                                   id="citizenship_number"
                                   name="citizenship_number"
                                   value="{{ old('citizenship_number', $customer->citizenship_number) }}"
                                   maxlength="100"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('citizenship_number')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="address"
                                   class="block text-sm font-medium text-gray-700">
                                Address
                            </label>

                            <input type="text"
                                   id="address"
                                   name="address"
                                   value="{{ old('address', $customer->address) }}"
                                   maxlength="255"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('address')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-10 border-t pt-8">

                        <h3 class="text-lg font-semibold text-gray-800">
                            Photo & Citizenship Documents
                        </h3>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Customer Photo
                                </label>

                                @if ($customer->photo)
                                    <img src="{{ asset('storage/'.$customer->photo) }}"
                                         alt=""
                                         class="mt-2 w-32 h-32 object-cover rounded-md border">
                                @endif

                                <input type="file"
                                       name="photo"
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                       class="mt-3 block w-full text-sm">

                                <p class="mt-1 text-xs text-gray-500">
                                    Choose new image only if replacing current photo.
                                </p>

                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Citizenship Front
                                </label>

                                @if ($customer->citizenship_front)
                                    <a href="{{ asset('storage/'.$customer->citizenship_front) }}"
                                       target="_blank">
                                        <img src="{{ asset('storage/'.$customer->citizenship_front) }}"
                                             alt=""
                                             class="mt-2 w-full max-h-40 object-contain rounded-md border">
                                    </a>
                                @endif

                                <input type="file"
                                       name="citizenship_front"
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                       class="mt-3 block w-full text-sm">

                                @error('citizenship_front')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Citizenship Back
                                </label>

                                @if ($customer->citizenship_back)
                                    <a href="{{ asset('storage/'.$customer->citizenship_back) }}"
                                       target="_blank">
                                        <img src="{{ asset('storage/'.$customer->citizenship_back) }}"
                                             alt=""
                                             class="mt-2 w-full max-h-40 object-contain rounded-md border">
                                    </a>
                                @endif

                                <input type="file"
                                       name="citizenship_back"
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                       class="mt-3 block w-full text-sm">

                                @error('citizenship_back')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="mt-8">

                        <label class="block text-sm font-medium text-gray-700">
                            Add Other Documents
                        </label>

                        <input type="file"
                               name="other_documents[]"
                               multiple
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                               class="mt-2 block w-full text-sm">

                        <p class="mt-1 text-xs text-gray-500">
                            Existing documents remain. New selected images will be added.
                        </p>

                        @error('other_documents.*')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        @if ($customer->otherDocuments->isNotEmpty())
                            <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-4">

                                @foreach ($customer->otherDocuments as $document)
                                    <div class="border rounded-md p-2">

                                        <a href="{{ asset('storage/'.$document->file_path) }}"
                                           target="_blank">

                                            <img src="{{ asset('storage/'.$document->file_path) }}"
                                                 alt=""
                                                 class="w-full h-28 object-cover rounded">

                                        </a>

                                        <button type="submit"
        form="delete-document-{{ $document->id }}"
        class="mt-2 text-sm text-red-600"
        onclick="return confirm('Remove this document?');">
    Remove
</button>

                                    </div>
                                @endforeach

                            </div>
                        @endif

                    </div>

                    <div class="mt-8">

                        <label for="note"
                               class="block text-sm font-medium text-gray-700">
                            Note
                        </label>

                        <textarea id="note"
                                  name="note"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note', $customer->note) }}</textarea>

                        @error('note')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="mt-8 border-t pt-6 flex justify-end gap-3">

                        <a href="{{ route('customers.show', $customer) }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Update Customer
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
    @foreach ($customer->otherDocuments as $document)
    <form id="delete-document-{{ $document->id }}"
          method="POST"
          action="{{ route('customers.documents.destroy', [
              'customer' => $customer,
              'document' => $document->id,
          ]) }}"
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach
</x-app-layout>