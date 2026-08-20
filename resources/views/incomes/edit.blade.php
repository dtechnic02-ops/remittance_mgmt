<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Edit Income</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $income->income_number }}</p>
            </div>

            <a href="{{ route('incomes.show', $income) }}"
               class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto w-full px-4 sm:px-6 lg:px-8" style="max-width: 900px;">
            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                <div class="border-b border-gray-200 p-5">
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <div class="text-xs font-medium uppercase text-gray-500">Income Category</div>
                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $income->category?->code }} - {{ $income->category?->name ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase text-gray-500">Account</div>
                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $income->account?->code }} - {{ $income->account?->name ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-medium uppercase text-gray-500">Amount</div>
                            <div class="mt-1 font-semibold text-gray-900">{{ number_format($income->amount) }}</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('incomes.update', $income) }}" class="p-5">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <label for="date_ad" class="block text-sm font-medium text-gray-700">Date AD *</label>
                            <input id="date_ad" type="date" name="date_ad"
                                   value="{{ old('date_ad', $income->date_ad?->format('Y-m-d')) }}"
                                   required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">
                                Nepali Date and Financial Year will be recalculated automatically.
                            </p>
                        </div>

                        <div class="grid gap-4 rounded-lg bg-gray-50 p-4 sm:grid-cols-2">
                            <div>
                                <label for="date_bs" class="text-xs font-medium uppercase text-gray-500">
                                    Date BS
                                </label>
                                <input id="date_bs" type="text" readonly
                                       value="{{ $income->date_bs }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 font-semibold text-gray-900 shadow-sm">
                            </div>
                            <div>
                                <label for="financial_year" class="text-xs font-medium uppercase text-gray-500">
                                    Financial Year
                                </label>
                                <input id="financial_year" type="text" readonly
                                       value="{{ $income->financial_year }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 font-semibold text-gray-900 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
                            <textarea id="note" name="note" rows="5" maxlength="2000"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('note', $income->note) }}</textarea>
                        </div>

                        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                            Only <strong>Date AD</strong> and <strong>Note</strong> can be edited. Income number,
                            category, account and amount cannot be changed.
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('incomes.show', $income) }}"
                               class="rounded-md border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="rounded-md bg-gray-800 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateAd = document.getElementById('date_ad');
            const dateBs = document.getElementById('date_bs');
            const financialYear = document.getElementById('financial_year');

            async function updateNepaliDate() {
                if (! dateAd.value) {
                    dateBs.value = '';
                    financialYear.value = '';
                    return;
                }

                try {
                    const response = await fetch(
                        `{{ route('incomes.date-convert') }}?date_ad=${encodeURIComponent(dateAd.value)}`,
                        {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (! response.ok) {
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

            dateAd.addEventListener('change', updateNepaliDate);
            updateNepaliDate();
        });
    </script>
</x-app-layout>
