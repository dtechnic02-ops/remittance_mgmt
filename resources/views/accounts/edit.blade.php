<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Account
            </h2>

            <a href="{{ route('accounts.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Accounts
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
                      action="{{ route('accounts.update', $account) }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label for="name"
                                   class="block text-sm font-medium text-gray-700">
                                Account Name *
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $account->name) }}"
                                   required
                                   autofocus
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="code"
                                   class="block text-sm font-medium text-gray-700">
                                Account Code *
                            </label>

                            <input type="text"
                                   id="code"
                                   name="code"
                                   value="{{ old('code', $account->code) }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type"
                                   class="block text-sm font-medium text-gray-700">
                                Account Type *
                            </label>

                            <select id="type"
                                    name="type"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">Select Account Type</option>

                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}"
                                        @selected(old('type', $account->type) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_number"
                                   class="block text-sm font-medium text-gray-700">
                                Account Number
                            </label>

                            <input type="text"
                                   id="account_number"
                                   name="account_number"
                                   value="{{ old('account_number', $account->account_number) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('account_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="branch_name"
                                   class="block text-sm font-medium text-gray-700">
                                Branch Name
                            </label>

                            <input type="text"
                                   id="branch_name"
                                   name="branch_name"
                                   value="{{ old('branch_name', $account->branch_name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('branch_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="attachment"
                                   class="block text-sm font-medium text-gray-700">
                                Replace Attachment
                            </label>

                            <input type="file"
                                   id="attachment"
                                   name="attachment"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                PDF, JPG, JPEG or PNG only
                            </p>

                            @if ($account->attachment)
                                <p class="mt-2 text-sm text-gray-600">
                                    Current:
                                    <a href="{{ asset('storage/'.$account->attachment) }}"
                                       target="_blank"
                                       class="text-blue-600 underline">
                                        View Attachment
                                    </a>
                                </p>
                            @endif

                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-6">
                        <label for="note"
                               class="block text-sm font-medium text-gray-700">
                            Note
                        </label>

                        <textarea id="note"
                                  name="note"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note', $account->note) }}</textarea>

                        @error('note')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 flex flex-wrap gap-6">

                        <label class="inline-flex items-center">
                            <input type="checkbox"
                                   name="allow_negative"
                                   value="1"
                                   @checked(old('allow_negative', $account->allow_negative))
                                   class="rounded border-gray-300">

                            <span class="ml-2 text-sm text-gray-700">
                                Allow Negative Balance
                            </span>
                        </label>

                        <label class="inline-flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', $account->is_active))
                                   class="rounded border-gray-300">

                            <span class="ml-2 text-sm text-gray-700">
                                Active
                            </span>
                        </label>

                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('accounts.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Update Account
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>