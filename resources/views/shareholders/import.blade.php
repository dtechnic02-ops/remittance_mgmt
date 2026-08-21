<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Import Shareholders</h2>
                <p class="mt-1 text-sm text-gray-500">Upload a validated Shareholder XLSX workbook.</p>
            </div>
            <a href="{{ route('shareholders.import.template') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Download Excel Template</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-900">
                    <ul class="list-disc space-y-1 pl-5">
                        <li>XLSX only; maximum 5 MB and 500 shareholder rows.</li>
                        <li>Code and Name are required.</li>
                        <li>Imported shareholders always start with zero Kitta and zero investment.</li>
                        <li>Any invalid row prevents the entire import.</li>
                    </ul>
                </div>

                @if ($errors->any())
                    <div class="mt-5 rounded-md bg-red-50 p-4">
                        <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('shareholders.import.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">Shareholder XLSX file</label>
                        <input id="file" name="file" type="file" accept=".xlsx" required class="mt-2 block w-full text-sm">
                    </div>
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <a href="{{ route('shareholders.index') }}" class="rounded-md border px-4 py-2 text-center text-sm">Cancel</a>
                        <button class="rounded-md bg-gray-900 px-5 py-2 text-sm font-medium text-white">Import Shareholders</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
