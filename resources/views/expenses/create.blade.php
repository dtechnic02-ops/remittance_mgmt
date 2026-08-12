<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New Expense
            </h2>

            <a href="{{ route('expenses.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Expenses
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

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
                      action="{{ route('expenses.store') }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Expense Category --}}
                        <div>
                            <label for="expense_category_id"
                                   class="block text-sm font-medium text-gray-700">
                                Expense Category *
                            </label>

                            <select id="expense_category_id"
                                    name="expense_category_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">
                                    Select Category
                                </option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                            @selected(
                                                old('expense_category_id') == $category->id
                                            )>
                                        {{ $category->name }}

                                        @if ($category->code)
                                            ({{ $category->code }})
                                        @endif
                                    </option>
                                @endforeach

                            </select>

                            @error('expense_category_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Paid From --}}
                        <div>
                            <label for="account_id"
                                   class="block text-sm font-medium text-gray-700">
                                Paid From Account *
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
                                            @selected(
                                                old('account_id') == $account->id
                                            )>
                                        {{ $account->name }}
                                        - {{ ucfirst(str_replace('_', ' ', $account->type)) }}
                                    </option>
                                @endforeach

                            </select>

                            <div class="mt-2 text-sm text-gray-500">
                                Current Balance:
                                <span id="account_balance"
                                      class="font-semibold text-gray-800">
                                    0
                                </span>
                            </div>

                            @error('account_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
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

                            @error('date_ad')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
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
                                   placeholder="Auto"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">

                            @error('date_bs')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
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
                                   placeholder="Auto"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">

                            @error('financial_year')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Amount --}}
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
                                   placeholder="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
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
                                   placeholder="Bill / Voucher / Reference No."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('reference')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
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
                                JPG, JPEG, PNG or PDF — maximum 5 MB
                            </p>

                            @error('attachment')
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
                                  placeholder="Optional expense note..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note') }}</textarea>

                        @error('note')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Expense Effect --}}
                    <div class="mt-6 rounded-md border border-gray-200 bg-gray-50 p-4">

                        <div class="text-sm font-semibold text-gray-800">
                            Expense Effect
                        </div>

                        <div class="mt-2 text-sm text-gray-600">
                            Selected Account
                            <span class="font-semibold text-red-700">
                                decreases
                            </span>
                            by
                            <span id="expense_effect_amount"
                                  class="font-semibold">
                                0
                            </span>
                        </div>

                    </div>

                    {{-- Buttons --}}
                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('expenses.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Save Expense
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const dateAd =
                document.getElementById('date_ad');

            const dateBs =
                document.getElementById('date_bs');

            const financialYear =
                document.getElementById('financial_year');

            const account =
                document.getElementById('account_id');

            const accountBalance =
                document.getElementById('account_balance');

            const amount =
                document.getElementById('amount');

            const expenseEffectAmount =
                document.getElementById('expense_effect_amount');

            function updateAccountBalance() {
                const option =
                    account.options[account.selectedIndex];

                if (!option || !option.value) {
                    accountBalance.textContent = '0';
                    return;
                }

                const balance =
                    parseInt(option.dataset.balance || 0, 10);

                accountBalance.textContent =
                    balance.toLocaleString();
            }

            function updateExpenseEffect() {
                const expenseAmount =
                    parseInt(amount.value || 0, 10);

                expenseEffectAmount.textContent =
                    expenseAmount.toLocaleString();
            }

            async function updateNepaliDate() {
                if (!dateAd.value) {
                    dateBs.value = '';
                    financialYear.value = '';
                    return;
                }

                try {
                    const response = await fetch(
                        `{{ route('expenses.date-convert') }}?date_ad=${encodeURIComponent(dateAd.value)}`,
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

                    const data = await response.json();

                    dateBs.value = data.date_bs;
                    financialYear.value =
                        data.financial_year;

                } catch (error) {
                    dateBs.value = '';
                    financialYear.value = '';
                    console.error(error);
                }
            }

            dateAd.addEventListener(
                'change',
                updateNepaliDate
            );

            account.addEventListener(
                'change',
                updateAccountBalance
            );

            amount.addEventListener(
                'input',
                updateExpenseEffect
            );

            updateAccountBalance();
            updateExpenseEffect();
            updateNepaliDate();
        });
    </script>

</x-app-layout>