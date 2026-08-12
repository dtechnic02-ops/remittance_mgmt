<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New Account Transfer
            </h2>

            <a href="{{ route('account-transfers.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Transfers
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

            <div class="bg-white shadow-sm sm:rounded-lg">
                <form method="POST"
                      action="{{ route('account-transfers.store') }}"
                      class="p-6">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        <div>
                            <label for="from_account_id"
                                   class="block text-sm font-medium text-gray-700">
                                From Account *
                            </label>

                            <select id="from_account_id"
                                    name="from_account_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">Select From Account</option>

                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                            data-balance="{{ $account->current_balance }}"
                                            @selected(old('from_account_id') == $account->id)>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-1 text-xs text-gray-500">
                                Current Balance:
                                <span id="from_balance">0</span>
                            </p>

                            @error('from_account_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="to_account_id"
                                   class="block text-sm font-medium text-gray-700">
                                To Account *
                            </label>

                            <select id="to_account_id"
                                    name="to_account_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">Select To Account</option>

                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                            data-balance="{{ $account->current_balance }}"
                                            @selected(old('to_account_id') == $account->id)>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-1 text-xs text-gray-500">
                                Current Balance:
                                <span id="to_balance">0</span>
                            </p>

                            @error('to_account_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="amount"
                                   class="block text-sm font-medium text-gray-700">
                                Transfer Amount *
                            </label>

                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   min="1"
                                   step="1"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_ad"
                                   class="block text-sm font-medium text-gray-700">
                                English Date *
                            </label>

                            <input type="date"
                                   id="date_ad"
                                   name="date_ad"
                                   value="{{ old('date_ad', now()->format('Y-m-d')) }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('date_ad')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_bs"
                                   class="block text-sm font-medium text-gray-700">
                                Nepali Date *
                            </label>

                            <input type="text"
                                   id="date_bs"
                                   name="date_bs"
                                   value="{{ old('date_bs') }}"
                                   readonly
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">

                            @error('date_bs')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="financial_year"
                                   class="block text-sm font-medium text-gray-700">
                                Financial Year *
                            </label>

                            <input type="text"
                                   id="financial_year"
                                   name="financial_year"
                                   value="{{ old('financial_year') }}"
                                   readonly
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">

                            @error('financial_year')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="reference"
                                   class="block text-sm font-medium text-gray-700">
                                Reference
                            </label>

                            <input type="text"
                                   id="reference"
                                   name="reference"
                                   value="{{ old('reference') }}"
                                   maxlength="100"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('reference')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
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
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note') }}</textarea>

                        @error('note')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-8 rounded-md bg-gray-50 p-4 text-sm text-gray-700">
                        <div class="font-semibold mb-2">
                            Transfer Effect
                        </div>

                        <div>
                            From Account decreases by the transfer amount.
                        </div>

                        <div>
                            To Account increases by the transfer amount.
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('account-transfers.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Save Transfer
                        </button>

                    </div>

                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fromAccount =
                document.getElementById('from_account_id');

            const toAccount =
                document.getElementById('to_account_id');

            const fromBalance =
                document.getElementById('from_balance');

            const toBalance =
                document.getElementById('to_balance');

            const dateAd =
                document.getElementById('date_ad');

            const dateBs =
                document.getElementById('date_bs');

            const financialYear =
                document.getElementById('financial_year');

            function updateBalance(select, target) {
                const option =
                    select.options[select.selectedIndex];

                if (!option || !option.value) {
                    target.textContent = '0';
                    return;
                }

                const balance =
                    parseInt(option.dataset.balance || 0, 10);

                target.textContent =
                    balance.toLocaleString();
            }

            async function updateNepaliDate() {
                if (!dateAd.value) {
                    dateBs.value = '';
                    financialYear.value = '';
                    return;
                }

                try {
                    const response = await fetch(
                        `{{ route('account-transfers.date-convert') }}?date_ad=${encodeURIComponent(dateAd.value)}`,
                        {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Date conversion failed.');
                    }

                    const data = await response.json();

                    dateBs.value = data.date_bs;
                    financialYear.value = data.financial_year;
                } catch (error) {
                    dateBs.value = '';
                    financialYear.value = '';
                    console.error(error);
                }
            }

            fromAccount.addEventListener('change', function () {
                updateBalance(
                    fromAccount,
                    fromBalance
                );
            });

            toAccount.addEventListener('change', function () {
                updateBalance(
                    toAccount,
                    toBalance
                );
            });

            dateAd.addEventListener(
                'change',
                updateNepaliDate
            );

            updateBalance(
                fromAccount,
                fromBalance
            );

            updateBalance(
                toAccount,
                toBalance
            );

            updateNepaliDate();
        });
    </script>

</x-app-layout>