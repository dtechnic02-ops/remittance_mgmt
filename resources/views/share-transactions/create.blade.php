<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Share Transaction
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Buy Kitta / Withdraw Share
                </p>
            </div>

            <a href="{{ route('share-transactions.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Transactions
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
                      action="{{ route('share-transactions.store') }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf

                    {{-- Transaction Type --}}
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Transaction Type *
                        </label>

                        <div class="grid grid-cols-2 gap-3">

                            <label id="buy_button"
                                   class="cursor-pointer rounded-lg border-2 px-6 py-4 text-center font-bold">

                                <input type="radio"
                                       name="transaction_type"
                                       value="buy"
                                       class="hidden"
                                       @checked(old('transaction_type', 'buy') === 'buy')>

                                BUY KITTA
                            </label>

                            <label id="withdraw_button"
                                   class="cursor-pointer rounded-lg border-2 px-6 py-4 text-center font-bold">

                                <input type="radio"
                                       name="transaction_type"
                                       value="withdraw"
                                       class="hidden"
                                       @checked(old('transaction_type') === 'withdraw')>

                                WITHDRAW SHARE
                            </label>

                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        {{-- Shareholder --}}
                        <div>
                            <label for="shareholder_id"
                                   class="block text-sm font-medium text-gray-700">
                                Shareholder *
                            </label>

                            <select id="shareholder_id"
                                    name="shareholder_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">
                                    Select Shareholder
                                </option>

                                @foreach ($shareholders as $shareholder)
                                    <option value="{{ $shareholder->id }}"
                                            data-kitta="{{ $shareholder->kitta }}"
                                            data-investment="{{ $shareholder->total_investment }}"
                                            data-per-kitta="{{ $shareholder->per_kitta_value }}"
                                            @selected(old('shareholder_id') == $shareholder->id)>

                                        {{ $shareholder->code }}
                                        - {{ $shareholder->name }}

                                    </option>
                                @endforeach

                            </select>

                            @error('shareholder_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Current Kitta --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Current Kitta
                            </label>

                            <input type="text"
                                   id="current_kitta"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                        </div>

                        {{-- Current Investment --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Current Investment
                            </label>

                            <input type="text"
                                   id="current_investment"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                        </div>

                        {{-- Per Kitta Value --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Per Kitta Value
                            </label>

                            <input type="text"
                                   id="per_kitta_value"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                        </div>

                        {{-- Kitta --}}
                        <div>
                            <label for="kitta"
                                   class="block text-sm font-medium text-gray-700">
                                Kitta *
                            </label>

                            <input type="number"
                                   id="kitta"
                                   name="kitta"
                                   value="{{ old('kitta') }}"
                                   min="1"
                                   step="1"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        {{-- Total Amount --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Total Amount
                            </label>

                            <input type="text"
                                   id="total_amount"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 font-semibold shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Kitta × Per Kitta Value
                            </p>
                        </div>

                        {{-- Account --}}
                        <div>
                            <label for="account_id"
                                   id="account_label"
                                   class="block text-sm font-medium text-gray-700">
                                Received In *
                            </label>

                            <select id="account_id"
                                    name="account_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">
                                    Select Account
                                </option>

                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                            data-balance="{{ $account->current_balance }}"
                                            @selected(old('account_id') == $account->id)>

                                        {{ $account->name }}
                                        ({{ ucfirst(str_replace('_', ' ', $account->type)) }})

                                    </option>
                                @endforeach

                            </select>

                            <p class="mt-1 text-xs text-gray-500">
                                Current Balance:
                                <span id="account_balance" class="font-semibold">
                                    0
                                </span>
                            </p>
                        </div>

                        {{-- English Date --}}
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
                        </div>

                        {{-- Nepali Date --}}
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
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                        </div>

                        {{-- Financial Year --}}
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
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
                        </div>

                        {{-- Reference --}}
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
                        </div>

                        {{-- Attachment --}}
                        <div>
                            <label for="attachment"
                                   class="block text-sm font-medium text-gray-700">
                                Attachment
                            </label>

                            <input type="file"
                                   id="attachment"
                                   name="attachment"
                                   accept=".jpg,.jpeg,.png,.pdf"
                                   class="mt-1 block w-full rounded-md border border-gray-300 p-2">

                            <p class="mt-1 text-xs text-gray-500">
                                JPG, PNG or PDF — Maximum 5 MB
                            </p>
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
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note') }}</textarea>
                    </div>

                    {{-- Effect --}}
                    <div class="mt-6 rounded-md bg-gray-50 p-4">

                        <div class="font-semibold text-gray-800">
                            Transaction Effect
                        </div>

                        <div id="buy_effect"
                             class="mt-2 text-sm text-gray-700">

                            Shareholder Kitta increases.<br>
                            Shareholder Investment increases.<br>
                            Selected Account balance increases.

                        </div>

                        <div id="withdraw_effect"
                             class="mt-2 hidden text-sm text-gray-700">

                            Shareholder Kitta decreases.<br>
                            Shareholder Investment decreases.<br>
                            Selected Account balance decreases.

                        </div>

                    </div>

                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('share-transactions.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Save Transaction
                        </button>

                    </div>

                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const typeInputs =
                document.querySelectorAll(
                    'input[name="transaction_type"]'
                );

            const buyButton =
                document.getElementById('buy_button');

            const withdrawButton =
                document.getElementById('withdraw_button');

            const shareholder =
                document.getElementById('shareholder_id');

            const currentKitta =
                document.getElementById('current_kitta');

            const currentInvestment =
                document.getElementById('current_investment');

            const perKittaValue =
                document.getElementById('per_kitta_value');

            const kitta =
                document.getElementById('kitta');

            const totalAmount =
                document.getElementById('total_amount');

            const account =
                document.getElementById('account_id');

            const accountBalance =
                document.getElementById('account_balance');

            const accountLabel =
                document.getElementById('account_label');

            const buyEffect =
                document.getElementById('buy_effect');

            const withdrawEffect =
                document.getElementById('withdraw_effect');

            const dateAd =
                document.getElementById('date_ad');

            const dateBs =
                document.getElementById('date_bs');

            const financialYear =
                document.getElementById('financial_year');


            function getType() {
                const selected =
                    document.querySelector(
                        'input[name="transaction_type"]:checked'
                    );

                return selected
                    ? selected.value
                    : 'buy';
            }


            function updateTypeUI() {
                const type = getType();

                buyButton.style.backgroundColor = '#ffffff';
                buyButton.style.color = '#111827';
                buyButton.style.borderColor = '#d1d5db';

                withdrawButton.style.backgroundColor = '#ffffff';
                withdrawButton.style.color = '#111827';
                withdrawButton.style.borderColor = '#d1d5db';

                if (type === 'withdraw') {
                    withdrawButton.style.backgroundColor = '#dc2626';
                    withdrawButton.style.color = '#ffffff';
                    withdrawButton.style.borderColor = '#dc2626';

                    accountLabel.textContent =
                        'Paid From *';

                    buyEffect.classList.add('hidden');
                    withdrawEffect.classList.remove('hidden');
                } else {
                    buyButton.style.backgroundColor = '#2563eb';
                    buyButton.style.color = '#ffffff';
                    buyButton.style.borderColor = '#2563eb';

                    accountLabel.textContent =
                        'Received In *';

                    withdrawEffect.classList.add('hidden');
                    buyEffect.classList.remove('hidden');
                }
            }


            function updateShareholderInfo() {
                const option =
                    shareholder.options[
                        shareholder.selectedIndex
                    ];

                if (!option || !option.value) {
                    currentKitta.value = '0';
                    currentInvestment.value = '0';
                    perKittaValue.value = '0';
                    updateTotal();
                    return;
                }

                const kittaValue =
                    parseInt(
                        option.dataset.kitta || 0,
                        10
                    );

                const investment =
                    parseInt(
                        option.dataset.investment || 0,
                        10
                    );

                const perKitta =
                    parseInt(
                        option.dataset.perKitta || 0,
                        10
                    );

                currentKitta.value =
                    kittaValue.toLocaleString();

                currentInvestment.value =
                    investment.toLocaleString();

                perKittaValue.value =
                    perKitta.toLocaleString();

                updateTotal();
            }


            function updateTotal() {
                const option =
                    shareholder.options[
                        shareholder.selectedIndex
                    ];

                const perKitta =
                    option && option.value
                        ? parseInt(
                            option.dataset.perKitta || 0,
                            10
                        )
                        : 0;

                const quantity =
                    parseInt(kitta.value || 0, 10);

                totalAmount.value =
                    (quantity * perKitta)
                        .toLocaleString();
            }


            function updateAccountBalance() {
                const option =
                    account.options[
                        account.selectedIndex
                    ];

                if (!option || !option.value) {
                    accountBalance.textContent = '0';
                    return;
                }

                const balance =
                    parseInt(
                        option.dataset.balance || 0,
                        10
                    );

                accountBalance.textContent =
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
                        `{{ route('share-transactions.convert-date') }}?date_ad=${encodeURIComponent(dateAd.value)}`,
                        {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Date conversion failed.'
                        );
                    }

                    const data =
                        await response.json();

                    dateBs.value =
                        data.date_bs;

                    financialYear.value =
                        data.financial_year;

                } catch (error) {
                    dateBs.value = '';
                    financialYear.value = '';

                    console.error(error);
                }
            }


            typeInputs.forEach(function (input) {
                input.addEventListener(
                    'change',
                    updateTypeUI
                );
            });

            shareholder.addEventListener(
                'change',
                updateShareholderInfo
            );

            kitta.addEventListener(
                'input',
                updateTotal
            );

            account.addEventListener(
                'change',
                updateAccountBalance
            );

            dateAd.addEventListener(
                'change',
                updateNepaliDate
            );

            updateTypeUI();
            updateShareholderInfo();
            updateAccountBalance();
            updateNepaliDate();
        });
    </script>

</x-app-layout>