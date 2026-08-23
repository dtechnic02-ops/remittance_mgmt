<x-app-layout>
    <x-slot name="header"><div class="flex flex-wrap items-center justify-between gap-3"><div><h2 class="text-xl font-semibold text-gray-800">Import Expense</h2><p class="mt-1 text-sm text-gray-500">Upload and preview an Expense XLSX workbook.</p></div><a href="{{ route('expenses.import.template') }}" class="rounded-md border px-4 py-2 text-sm">Download Template</a></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8"><div class="rounded-lg bg-white p-6 shadow-sm">
        <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-900">Exact headers: date_ad, category, account, amount, reference, note. Use active category and account codes. Maximum 500 rows and 5 MB.</div>
        @if ($errors->any())<div class="mt-5 rounded-md bg-red-50 p-4"><ul class="list-disc pl-5 text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('expenses.import.preview') }}" enctype="multipart/form-data" class="mt-6 space-y-5">@csrf
            <div><label for="file" class="block text-sm font-medium text-gray-700">Expense XLSX file</label><input id="file" name="file" type="file" accept=".xlsx" required class="mt-2 block w-full text-sm"></div>
            <div class="flex justify-end gap-3"><a href="{{ route('expenses.index') }}" class="rounded-md border px-4 py-2 text-sm">Cancel</a><button class="rounded-md bg-gray-900 px-5 py-2 text-sm font-medium text-white">Preview Import</button></div>
        </form>
    </div></div></div>
</x-app-layout>
