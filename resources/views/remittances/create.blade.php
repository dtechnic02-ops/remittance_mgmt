<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New Remittance
            </h2>

            <a href="{{ route('remittances.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Remittances
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
                      action="{{ route('remittances.store') }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf
                   
    <div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Transaction Type *
    </label>

    <div class="grid grid-cols-2 gap-3">

        <label id="send_button"
               class="cursor-pointer rounded-lg border-2 px-6 py-4 text-center font-bold">
            <input
                type="radio"
                name="direction"
                value="send"
                class="hidden"
                @checked(old('direction', 'send') === 'send')
            >
            SEND
        </label>

        <label id="receive_button"
               class="cursor-pointer rounded-lg border-2 px-6 py-4 text-center font-bold">
            <input
                type="radio"
                name="direction"
                value="receive"
                class="hidden"
                @checked(old('direction') === 'receive')
            >
            RECEIVE
        </label>

    </div>

    @error('direction')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        <div>
                            <label for="customer_search" class="block text-sm font-medium text-gray-700">
                                Customer *
                            </label>

                            <div id="customer_selector" class="relative mt-1">
                                <input type="hidden"
                                       name="customer_id"
                                       id="customer_id"
                                       value="{{ old('customer_id') }}"
                                       required>

                                <input type="search"
                                       id="customer_search"
                                       autocomplete="off"
                                       placeholder="Search name, code, or mobile"
                                       class="block w-full rounded-md border-gray-300 shadow-sm">

                                <div id="customer_selected"
                                     class="hidden items-center justify-between rounded-md border border-gray-300 bg-gray-50 px-3 py-2">
                                    <span id="customer_selected_label" class="text-sm text-gray-800"></span>
                                    <button type="button"
                                            id="customer_clear"
                                            class="ml-3 text-xs font-medium text-gray-600 hover:text-gray-900">
                                        Clear
                                    </button>
                                </div>

                                <ul id="customer_results"
                                    class="absolute z-20 mt-1 hidden max-h-48 w-full overflow-y-auto rounded-md border border-gray-200 bg-white py-1 shadow-sm"></ul>
                            </div>

                            @php
                                $customerSearchData = $customers->map(function ($customer) {
                                    return [
                                        'id' => $customer->id,
                                        'name' => $customer->name,
                                        'customer_code' => $customer->customer_code,
                                        'mobile' => $customer->mobile,
                                    ];
                                })->values();
                            @endphp

                            <script type="application/json" id="customer_search_data">@json($customerSearchData)</script>

                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Provider Account *
                            </label>

                            <select name="provider_account_id"
                                    id="provider_account_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">Select Provider</option>

                                @foreach ($providers as $provider)
                                    <option value="{{ $provider->id }}"
                                            data-balance="{{ $provider->current_balance }}"
                                        @selected(old('provider_account_id') == $provider->id)>
                                        {{ $provider->name }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-1 text-xs text-gray-500">
                                Current Balance:
                                <span id="provider_balance">0</span>
                            </p>

                            @error('provider_account_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Cash Account *
                            </label>

                            <select name="cash_account_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">Select Cash Account</option>

                                @foreach ($cashAccounts as $cash)
                                    <option value="{{ $cash->id }}"
                                        @selected(old('cash_account_id') == $cash->id)>
                                        {{ $cash->name }}
                                        ({{ number_format($cash->current_balance) }})
                                    </option>
                                @endforeach
                            </select>

                            @error('cash_account_id')
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
                            <label for="principal_amount"
                                   class="block text-sm font-medium text-gray-700">
                                Principal Amount *
                            </label>

                            <input type="number"
                                   id="principal_amount"
                                   name="principal_amount"
                                   value="{{ old('principal_amount') }}"
                                   min="1"
                                   step="1"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('principal_amount')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="service_charge"
                                   class="block text-sm font-medium text-gray-700">
                                Service Charge
                            </label>

                            <input type="number"
                                   id="service_charge"
                                   name="service_charge"
                                   value="{{ old('service_charge', 0) }}"
                                   min="0"
                                   step="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('service_charge')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label id="customer_cash_label"
       class="block text-sm font-medium text-gray-700">
    Customer Cash
</label>

                            <input type="text"
                                   id="total_cash_received"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm font-semibold">
                        </div>

                        <div>
                            <label for="provider_reference"
                                   class="block text-sm font-medium text-gray-700">
                                Provider Reference
                            </label>

                            <input type="text"
                                   id="provider_reference"
                                   name="provider_reference"
                                   value="{{ old('provider_reference') }}"
                                   maxlength="100"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('provider_reference')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="attachment"
                                   class="block text-sm font-medium text-gray-700">
                                Attachment
                            </label>

                            <input type="file"
                                   id="attachment"
                                   name="attachment"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="mt-1 block w-full text-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                One PDF, JPG, JPEG or PNG file.
                            </p>

                            @error('attachment')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
<div class="mt-8 rounded-md bg-gray-50 p-4 text-sm text-gray-700">
    <div class="font-semibold mb-2">
        Transaction Effect
    </div>

    <div id="send_effect">
        <div>
            Cash Account increases by Principal + Service Charge.
        </div>

        <div>
            Provider Account decreases by Principal.
        </div>
    </div>

    <div id="receive_effect" class="hidden">
        <div>
            Provider Account increases by Principal.
        </div>

        <div>
            Cash Account decreases by Principal and retains Service Charge.
        </div>

        <div>
            Customer receives Principal - Service Charge.
        </div>
    </div>

    <div class="mt-2">
        Customer has no running balance.
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

                    
                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('remittances.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Save Remittance
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const principal =
            document.getElementById('principal_amount');

        const serviceCharge =
            document.getElementById('service_charge');

        const totalCash =
            document.getElementById('total_cash_received');

        const dateAd =
            document.getElementById('date_ad');

        const dateBs =
            document.getElementById('date_bs');

        const financialYear =
            document.getElementById('financial_year');

        const provider =
            document.getElementById('provider_account_id');

        const providerBalance =
            document.getElementById('provider_balance');

        const directionInputs =
            document.querySelectorAll('input[name="direction"]');

        const sendButton =
            document.getElementById('send_button');

        const receiveButton =
            document.getElementById('receive_button');

        const cashLabel =
            document.getElementById('customer_cash_label');

        const sendEffect =
            document.getElementById('send_effect');

        const receiveEffect =
            document.getElementById('receive_effect');

        const customerSearchDataEl =
            document.getElementById('customer_search_data');
        const customerIdInput =
            document.getElementById('customer_id');
        const customerSearchInput =
            document.getElementById('customer_search');
        const customerResults =
            document.getElementById('customer_results');
        const customerSelected =
            document.getElementById('customer_selected');
        const customerSelectedLabel =
            document.getElementById('customer_selected_label');
        const customerClear =
            document.getElementById('customer_clear');
        const customerSelector =
            document.getElementById('customer_selector');
        let customerSearchItems = [];

        function customerLabel(customer) {
            let label = customer.customer_code
                ? customer.customer_code + ' - ' + customer.name
                : customer.name;

            if (customer.mobile) {
                label += ' - ' + customer.mobile;
            }

            return label;
        }

        function hideCustomerResults() {
            if (!customerResults) {
                return;
            }

            customerResults.innerHTML = '';
            customerResults.classList.add('hidden');
        }

        function setSelectedCustomer(customer) {
            if (!customerIdInput || !customerSearchInput || !customerSelected) {
                return;
            }

            customerIdInput.value = customer ? String(customer.id) : '';
            customerSearchInput.setCustomValidity('');

            if (customer) {
                customerSearchInput.classList.add('hidden');
                customerSearchInput.removeAttribute('required');
                customerSearchInput.value = '';
                customerSelectedLabel.textContent = customerLabel(customer);
                customerSelected.classList.remove('hidden');
                customerSelected.classList.add('flex');
            } else {
                customerSearchInput.classList.remove('hidden');
                customerSearchInput.setAttribute('required', 'required');
                customerSelected.classList.add('hidden');
                customerSelected.classList.remove('flex');
                customerSelectedLabel.textContent = '';
            }

            hideCustomerResults();
        }

        function renderCustomerResults(matches) {
            customerResults.innerHTML = '';

            if (!matches.length) {
                const empty = document.createElement('li');
                empty.className = 'px-3 py-2 text-sm text-gray-500';
                empty.textContent = 'No matching customers';
                customerResults.appendChild(empty);
                customerResults.classList.remove('hidden');
                return;
            }

            matches.forEach(function (customer) {
                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'block w-full px-3 py-2 text-left text-sm text-gray-800 hover:bg-gray-100';
                button.textContent = customerLabel(customer);
                button.addEventListener('click', function () {
                    setSelectedCustomer(customer);
                });
                item.appendChild(button);
                customerResults.appendChild(item);
            });

            customerResults.classList.remove('hidden');
        }

        function searchCustomers(query) {
            const needle = String(query || '').trim().toLowerCase();

            if (!needle) {
                hideCustomerResults();
                return;
            }

            const matches = [];

            for (let i = 0; i < customerSearchItems.length; i++) {
                const customer = customerSearchItems[i];
                const haystack = [
                    customer.name,
                    customer.customer_code,
                    customer.mobile
                ].filter(Boolean).join(' ').toLowerCase();

                if (haystack.indexOf(needle) !== -1) {
                    matches.push(customer);
                }

                if (matches.length >= 20) {
                    break;
                }
            }

            renderCustomerResults(matches);
        }

        if (customerSearchDataEl && customerIdInput && customerSearchInput && customerResults) {
            try {
                customerSearchItems = JSON.parse(customerSearchDataEl.textContent || '[]');
            } catch (error) {
                customerSearchItems = [];
            }

            const initiallySelected = customerSearchItems.find(function (customer) {
                return String(customer.id) === String(customerIdInput.value || '');
            });

            if (initiallySelected) {
                setSelectedCustomer(initiallySelected);
            } else {
                setSelectedCustomer(null);
            }

            customerSearchInput.addEventListener('input', function () {
                customerIdInput.value = '';
                searchCustomers(customerSearchInput.value);
            });

            customerSearchInput.addEventListener('focus', function () {
                if (String(customerSearchInput.value || '').trim() !== '') {
                    searchCustomers(customerSearchInput.value);
                } else {
                    hideCustomerResults();
                }
            });

            if (customerClear) {
                customerClear.addEventListener('click', function () {
                    setSelectedCustomer(null);
                    customerSearchInput.focus();
                });
            }

            document.addEventListener('click', function (event) {
                if (customerSelector && !customerSelector.contains(event.target)) {
                    hideCustomerResults();
                }
            });

            customerSearchInput.form.addEventListener('submit', function (event) {
                if (!customerIdInput.value) {
                    event.preventDefault();
                    customerSearchInput.setCustomValidity('Please select a customer.');
                    customerSearchInput.reportValidity();
                }
            });
        }


        function getDirection() {
            const selected =
                document.querySelector(
                    'input[name="direction"]:checked'
                );

            return selected ? selected.value : 'send';
        }


        function updateDirectionButtons() {
            const direction = getDirection();

            // Reset SEND
            sendButton.style.backgroundColor = '#ffffff';
            sendButton.style.color = '#111827';
            sendButton.style.borderColor = '#d1d5db';

            // Reset RECEIVE
            receiveButton.style.backgroundColor = '#ffffff';
            receiveButton.style.color = '#111827';
            receiveButton.style.borderColor = '#d1d5db';

            if (direction === 'receive') {
                receiveButton.style.backgroundColor = '#16a34a';
                receiveButton.style.color = '#ffffff';
                receiveButton.style.borderColor = '#16a34a';
            } else {
                sendButton.style.backgroundColor = '#2563eb';
                sendButton.style.color = '#ffffff';
                sendButton.style.borderColor = '#2563eb';
            }
        }


        function updateTransaction() {
            const principalAmount =
                parseInt(principal.value || 0, 10);

            const charge =
                parseInt(serviceCharge.value || 0, 10);

            const direction = getDirection();

            if (direction === 'receive') {

                cashLabel.textContent =
                    'Cash Paid to Customer';

                sendEffect.classList.add('hidden');
                receiveEffect.classList.remove('hidden');

                if (charge > principalAmount) {
                    totalCash.value = '0';
                    totalCash.classList.add('border-red-500');
                    return;
                }

                const customerCash =
                    principalAmount - charge;

                totalCash.value =
                    customerCash.toLocaleString();

                totalCash.classList.remove(
                    'border-red-500'
                );

            } else {

                cashLabel.textContent =
                    'Cash Received from Customer';

                receiveEffect.classList.add('hidden');
                sendEffect.classList.remove('hidden');

                const customerCash =
                    principalAmount + charge;

                totalCash.value =
                    customerCash.toLocaleString();

                totalCash.classList.remove(
                    'border-red-500'
                );
            }
        }


        function updateProviderBalance() {
            const option =
                provider.options[provider.selectedIndex];

            if (!option || !option.value) {
                providerBalance.textContent = '0';
                return;
            }

            const balance =
                parseInt(
                    option.dataset.balance || 0,
                    10
                );

            providerBalance.textContent =
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
                    `{{ route('remittances.date-convert') }}?date_ad=${encodeURIComponent(dateAd.value)}`,
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


        principal.addEventListener(
            'input',
            function () {
                updateTransaction();
            }
        );


        serviceCharge.addEventListener(
            'input',
            function () {
                updateTransaction();
            }
        );


        directionInputs.forEach(function (input) {
            input.addEventListener(
                'change',
                function () {
                    updateDirectionButtons();
                    updateTransaction();
                }
            );
        });


        provider.addEventListener(
            'change',
            function () {
                updateProviderBalance();
            }
        );


        dateAd.addEventListener(
            'change',
            function () {
                updateNepaliDate();
            }
        );


        // Initial Load
        updateDirectionButtons();
        updateTransaction();
        updateProviderBalance();
        updateNepaliDate();
    });
</script>
  

</x-app-layout>