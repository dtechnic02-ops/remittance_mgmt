<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Borrowing Transaction
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Borrow / Repay
                </p>
            </div>

            <a href="{{ route('borrowings.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Borrowings
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
                      action="{{ route('borrowings.store') }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf

                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Transaction Type *
                        </label>

                        <div class="grid grid-cols-2 gap-3">

                            <label id="borrow_button"
                                   class="cursor-pointer rounded-lg border-2 px-6 py-4 text-center font-bold">

                                <input type="radio"
                                       name="transaction_type"
                                       value="borrow"
                                       class="hidden"
                                       @checked(old('transaction_type', 'borrow') === 'borrow')>

                                BORROW
                            </label>

                            <label id="repay_button"
                                   class="cursor-pointer rounded-lg border-2 px-6 py-4 text-center font-bold">

                                <input type="radio"
                                       name="transaction_type"
                                       value="repay"
                                       class="hidden"
                                       @checked(old('transaction_type') === 'repay')>

                                REPAY
                            </label>

                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        <div>
                            <label for="lender_id"
                                   class="block text-sm font-medium text-gray-700">
                                Lender *
                            </label>

                            <select id="lender_id"
                                    name="lender_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">
                                    Select Lender
                                </option>

                                @foreach ($lenders as $lender)
                                    <option value="{{ $lender->id }}"
                                            data-outstanding="{{ $lender->outstanding }}"
                                            @selected(old('lender_id') == $lender->id)>

                                        {{ $lender->code }}
                                        - {{ $lender->name }}

                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Current Outstanding
                            </label>

                            <input type="text"
                                   id="current_outstanding"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 font-semibold shadow-sm">
                        </div>

                        <div>
                            <label for="amount"
                                   class="block text-sm font-medium text-gray-700">
                                Amount *
                            </label>

                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   min="1"
                                   step="1"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

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

                    <div class="mt-6 rounded-md bg-gray-50 p-4">

                        <div class="font-semibold text-gray-800">
                            Transaction Effect
                        </div>

                        <div id="borrow_effect"
                             class="mt-2 text-sm text-gray-700">

                            Selected Account balance increases.<br>
                            Lender Outstanding increases.

                        </div>

                        <div id="repay_effect"
                             class="mt-2 hidden text-sm text-gray-700">

                            Selected Account balance decreases.<br>
                            Lender Outstanding decreases.

                        </div>

                    </div>

                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('borrowings.index') }}"
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

            const borrowButton =
                document.getElementById('borrow_button');

            const repayButton =
                document.getElementById('repay_button');

            const lender =
                document.getElementById('lender_id');

            const currentOutstanding =
                document.getElementById('current_outstanding');

            const account =
                document.getElementById('account_id');

            const accountBalance =
                document.getElementById('account_balance');

            const accountLabel =
                document.getElementById('account_label');

            const borrowEffect =
                document.getElementById('borrow_effect');

            const repayEffect =
                document.getElementById('repay_effect');

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
                    : 'borrow';
            }

            function updateTypeUI() {
                const type = getType();

                borrowButton.style.backgroundColor = '#ffffff';
                borrowButton.style.color = '#111827';
                borrowButton.style.borderColor = '#d1d5db';

                repayButton.style.backgroundColor = '#ffffff';
                repayButton.style.color = '#111827';
                repayButton.style.borderColor = '#d1d5db';

                if (type === 'repay') {
                    repayButton.style.backgroundColor = '#dc2626';
                    repayButton.style.color = '#ffffff';
                    repayButton.style.borderColor = '#dc2626';

                    accountLabel.textContent =
                        'Paid From *';

                    borrowEffect.classList.add('hidden');
                    repayEffect.classList.remove('hidden');
                } else {
                    borrowButton.style.backgroundColor = '#2563eb';
                    borrowButton.style.color = '#ffffff';
                    borrowButton.style.borderColor = '#2563eb';

                    accountLabel.textContent =
                        'Received In *';

                    repayEffect.classList.add('hidden');
                    borrowEffect.classList.remove('hidden');
                }
            }

            function updateLenderOutstanding() {
                const option =
                    lender.options[lender.selectedIndex];

                if (!option || !option.value) {
                    currentOutstanding.value = '0';
                    return;
                }

                const outstanding =
                    parseInt(
                        option.dataset.outstanding || 0,
                        10
                    );

                currentOutstanding.value =
                    outstanding.toLocaleString();
            }

            function updateAccountBalance() {
                const option =
                    account.options[account.selectedIndex];

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
                        `{{ route('borrowings.convert-date') }}?date_ad=${encodeURIComponent(dateAd.value)}`,
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

            lender.addEventListener(
                'change',
                updateLenderOutstanding
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
            updateLenderOutstanding();
            updateAccountBalance();
            updateNepaliDate();
        });
    </script>

</x-app-layout>