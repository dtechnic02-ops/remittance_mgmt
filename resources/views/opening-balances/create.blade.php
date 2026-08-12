<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Opening Balance
            </h2>

            <a href="{{ route('opening-balances.index') }}"
               class="text-gray-600">
                Back
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
                      action="{{ route('opening-balances.store') }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Account *
                            </label>

                            <select name="account_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">Select Account</option>

                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                        @selected(old('account_id') == $account->id)>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('account_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                English Date *
                            </label>

                            <input type="date"
                                   name="date_ad"
                                   value="{{ old('date_ad', now()->format('Y-m-d')) }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Nepali Date and Financial Year will be generated automatically.
                            </p>

                            @error('date_ad')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Opening Amount *
                            </label>

                            <input type="number"
                                   name="amount"
                                   value="{{ old('amount', 0) }}"
                                   required
                                   step="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Negative amount is allowed for approved accounts.
                            </p>

                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Attachment
                            </label>

                            <input type="file"
                                   name="attachment"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                PDF, JPG, JPEG or PNG only
                            </p>

                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">
                            Note
                        </label>

                        <textarea name="note"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note') }}</textarea>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('opening-balances.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Save Opening Balance
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>