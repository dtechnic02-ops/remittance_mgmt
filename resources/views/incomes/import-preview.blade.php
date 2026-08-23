<x-app-layout>
    <x-slot name="header"><div><h2 class="text-xl font-semibold text-gray-800">Income Import Preview</h2><p class="mt-1 text-sm text-gray-500">All rows are valid. Confirm to post through the normal Income service.</p></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"><div class="overflow-hidden rounded-lg bg-white shadow-sm">
        @if ($errors->any())<div class="m-5 rounded-md bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <div class="overflow-x-auto"><table class="min-w-full divide-y"><thead class="bg-gray-50"><tr>@foreach (['Row','Date','Category','Account','Amount','Reference','Status'] as $heading)<th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">{{ $heading }}</th>@endforeach</tr></thead><tbody class="divide-y">
            @foreach ($rows as $row)<tr><td class="px-4 py-3">{{ $row['row_number'] }}</td><td class="px-4 py-3">{{ $row['date_ad'] }}</td><td class="px-4 py-3">{{ $row['category'] }} - {{ $row['category_name'] }}</td><td class="px-4 py-3">{{ $row['account'] }} - {{ $row['account_name'] }}</td><td class="px-4 py-3">{{ number_format($row['amount']) }}</td><td class="px-4 py-3">{{ $row['reference'] ?: '-' }}</td><td class="px-4 py-3 text-green-700">Valid</td></tr>@endforeach
        </tbody></table></div><div class="flex justify-end gap-3 border-t p-5"><a href="{{ route('incomes.import.create') }}" class="rounded-md border px-4 py-2 text-sm">Back</a><form method="POST" action="{{ route('incomes.import.confirm') }}">@csrf<button class="rounded-md bg-gray-900 px-5 py-2 text-sm font-medium text-white">Confirm Import</button></form></div>
    </div></div></div>
</x-app-layout>
