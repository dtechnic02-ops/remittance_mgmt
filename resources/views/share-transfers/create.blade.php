<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Share Transfer
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Transfer Kitta from one shareholder to another
                </p>
            </div>

            <a href="{{ route('share-transfers.index') }}"
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
                      action="{{ route('share-transfers.store') }}"
                      enctype="multipart/form-data"
                      class="p-6">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        {{-- From Shareholder --}}
                        <div>
                            <label for="shareholder_id"
                                   class="block text-sm font-medium text-gray-700">
                                From Shareholder *
                            </label>

                            @if ($fromShareholder)

                                <input type="text"
                                       value="{{ $fromShareholder->code }} - {{ $fromShareholder->name }}"
                                       readonly
                                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">

                                <input type="hidden"
                                       id="shareholder_id"
                                       value="{{ $fromShareholder->id }}"
                                       data-kitta="{{ $fromShareholder->kitta }}"
                                       data-investment="{{ $fromShareholder->total_investment }}"
                                       data-per-kitta="{{ $fromShareholder->per_kitta_value }}">

                            @else

                                <select id="shareholder_id"
                                        name="shareholder_id"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                    <option value="">
                                        Select From Shareholder
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

                            @endif

                            @error('shareholder_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- To Shareholder --}}
                        <div>
                            <label for="to_shareholder_id"
                                   class="block text-sm font-medium text-gray-700">
                                To Shareholder *
                            </label>

                            <select id="to_shareholder_id"
                                    name="to_shareholder_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                <option value="">
                                    Select To Shareholder
                                </option>

                                @foreach ($shareholders as $shareholder)
                                    <option value="{{ $shareholder->id }}"
                                            data-kitta="{{ $shareholder->kitta }}"
                                            @selected(old('to_shareholder_id') == $shareholder->id)>

                                        {{ $shareholder->code }}
                                        - {{ $shareholder->name }}

                                    </option>
                                @endforeach

                            </select>

                            @error('to_shareholder_id')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Available Kitta --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Available Kitta
                            </label>

                            <input type="text"
                                   id="current_kitta"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 font-semibold shadow-sm">
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

                        {{-- Transfer Kitta --}}
                        <div>
                            <label for="kitta"
                                   class="block text-sm font-medium text-gray-700">
                                Transfer Kitta *
                            </label>

                            <input type="number"
                                   id="kitta"
                                   name="kitta"
                                   value="{{ old('kitta') }}"
                                   min="1"
                                   step="1"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('kitta')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Total Share Value --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Total Share Value
                            </label>

                            <input type="text"
                                   id="total_amount"
                                   value="0"
                                   readonly
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 font-semibold shadow-sm">

                            <p class="mt-1 text-xs text-gray-500">
                                Transfer Kitta × Per Kitta Value
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
                            Transfer Effect
                        </div>

                        <div class="mt-2 text-sm text-gray-700">
                            From Shareholder Kitta decreases.<br>
                            To Shareholder Kitta increases.<br>
                            Per Kitta Value does not change.<br>
                            No Cash or Bank account is affected.
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('share-transfers.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Transfer Share
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const fromShareholder =
                document.getElementById('shareholder_id');

            const toShareholder =
                document.getElementById('to_shareholder_id');

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

            const dateAd =
                document.getElementById('date_ad');

            const dateBs =
                document.getElementById('date_bs');

            const financialYear =
                document.getElementById('financial_year');


            function getFromData() {

                if (fromShareholder.tagName === 'SELECT') {
                    const option =
                        fromShareholder.options[
                            fromShareholder.selectedIndex
                        ];

                    if (!option || !option.value) {
                        return null;
                    }

                    return {
                        id: option.value,
                        kitta: parseInt(
                            option.dataset.kitta || 0,
                            10
                        ),
                        investment: parseInt(
                            option.dataset.investment || 0,
                            10
                        ),
                        perKitta: parseInt(
                            option.dataset.perKitta || 0,
                            10
                        )
                    };
                }

                return {
                    id: fromShareholder.value,
                    kitta: parseInt(
                        fromShareholder.dataset.kitta || 0,
                        10
                    ),
                    investment: parseInt(
                        fromShareholder.dataset.investment || 0,
                        10
                    ),
                    perKitta: parseInt(
                        fromShareholder.dataset.perKitta || 0,
                        10
                    )
                };
            }


            function updateFromInfo() {
                const data = getFromData();

                if (!data) {
                    currentKitta.value = '0';
                    currentInvestment.value = '0';
                    perKittaValue.value = '0';
                    updateTotal();
                    updateReceiverOptions();
                    return;
                }

                currentKitta.value =
                    data.kitta.toLocaleString();

                currentInvestment.value =
                    data.investment.toLocaleString();

                perKittaValue.value =
                    data.perKitta.toLocaleString();

                kitta.max = data.kitta;

                updateTotal();
                updateReceiverOptions();
            }


            function updateReceiverOptions() {
                const data = getFromData();

                Array.from(toShareholder.options)
                    .forEach(function (option) {

                        if (!option.value) {
                            return;
                        }

                        option.disabled =
                            data &&
                            String(option.value) ===
                            String(data.id);
                    });

                if (
                    data &&
                    String(toShareholder.value) ===
                    String(data.id)
                ) {
                    toShareholder.value = '';
                }
            }


            function updateTotal() {
                const data = getFromData();

                const quantity =
                    parseInt(kitta.value || 0, 10);

                const perKitta =
                    data ? data.perKitta : 0;

                totalAmount.value =
                    (quantity * perKitta)
                        .toLocaleString();
            }


            async function updateNepaliDate() {
                if (!dateAd.value) {
                    dateBs.value = '';
                    financialYear.value = '';
                    return;
                }

                try {
                    const response = await fetch(
                        `{{ route('share-transfers.convert-date') }}?date_ad=${encodeURIComponent(dateAd.value)}`,
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


            if (fromShareholder.tagName === 'SELECT') {
                fromShareholder.addEventListener(
                    'change',
                    updateFromInfo
                );
            }

            kitta.addEventListener(
                'input',
                updateTotal
            );

            dateAd.addEventListener(
                'change',
                updateNepaliDate
            );

            updateFromInfo();
            updateNepaliDate();
        });
    </script>

</x-app-layout>