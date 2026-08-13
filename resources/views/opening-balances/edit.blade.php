<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Opening Balance
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
                      action="{{ route('opening-balances.update', $openingBalance) }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Account
                            </label>

                            <input type="text"
                                   value="{{ $openingBalance->account->name }}"
                                   disabled
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Account cannot be changed after Opening Balance creation.
                            </p>
                        </div>

                        <div>
                            <label for="date_ad"
                                   class="block text-sm font-medium text-gray-700">
                                English Date *
                            </label>

                            <input type="date"
                                   id="date_ad"
                                   name="date_ad"
                                   value="{{ old('date_ad', $openingBalance->date_ad->format('Y-m-d')) }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Nepali Date and Financial Year will be recalculated automatically.
                            </p>

                            @error('date_ad')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="amount"
                                   class="block text-sm font-medium text-gray-700">
                                Opening Amount *
                            </label>

                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   value="{{ old('amount', $openingBalance->amount) }}"
                                   required
                                   min="0"
                                   step="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Opening amount cannot be negative.
                            </p>

                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
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

                            @if ($openingBalance->attachment)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/'.$openingBalance->attachment) }}"
                                       target="_blank"
                                       class="text-sm text-blue-600 underline">
                                        View Current Attachment
                                    </a>
                                </div>
                            @endif

                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <p class="text-sm text-gray-500">
                                Current Nepali Date
                            </p>

                            <p class="mt-1 font-semibold">
                                {{ $openingBalance->date_bs }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Current Financial Year
                            </p>

                            <p class="mt-1 font-semibold">
                                {{ $openingBalance->financial_year }}
                            </p>
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
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note', $openingBalance->note) }}</textarea>

                        @error('note')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-8 border-t pt-6">
                        <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                            Changing the Opening Amount will adjust the current account balance only by the difference.
                            Existing transaction effects will not be removed.
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('opening-balances.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Update Opening Balance
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
