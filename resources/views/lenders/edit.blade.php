<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Lender
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $lender->code }} - {{ $lender->name }}
                </p>
            </div>

            <a href="{{ route('lenders.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Lenders
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

                <form method="POST"
                      action="{{ route('lenders.update', $lender) }}"
                      class="p-6">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Code --}}
                        <div>
                            <label for="code"
                                   class="block text-sm font-medium text-gray-700">
                                Code *
                            </label>

                            <input type="text"
                                   id="code"
                                   name="code"
                                   value="{{ old('code', $lender->code) }}"
                                   maxlength="30"
                                   required
                                   autofocus
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('code')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Name --}}
                        <div>
                            <label for="name"
                                   class="block text-sm font-medium text-gray-700">
                                Name *
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $lender->name) }}"
                                   maxlength="150"
                                   required
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
                                   value="{{ old('mobile', $lender->mobile) }}"
                                   maxlength="30"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('mobile')
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
                                   value="{{ old('address', $lender->address) }}"
                                   maxlength="255"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('address')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- Note --}}
                    <div class="mt-6">
                        <label for="note"
                               class="block text-sm font-medium text-gray-700">
                            Note
                        </label>

                        <textarea id="note"
                                  name="note"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note', $lender->note) }}</textarea>

                        @error('note')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="mt-6">
                        <label class="inline-flex items-center">

                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', $lender->is_active))
                                   class="rounded border-gray-300">

                            <span class="ms-2 text-sm text-gray-700">
                                Active
                            </span>

                        </label>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('lenders.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Update Lender
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>