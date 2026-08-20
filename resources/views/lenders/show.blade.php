<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div><h2 class="text-xl font-semibold text-gray-800">Lender Details</h2><p class="mt-1 text-sm text-gray-500">{{ $lender->code }} - {{ $lender->name }}</p></div>
            <div class="flex items-center gap-3">
                @if ($lender->is_active && (auth()->user()->isAdmin() || auth()->user()->hasPermission('lender.update')))
                    <a href="{{ route('lenders.edit',$lender) }}" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white">Edit</a>
                @endif
                <a href="{{ route('lenders.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm">Back to Lenders</a>
            </div>
        </div>
    </x-slot>

    @php
        $totalBorrowed = (int) ($lender->total_borrowed ?? 0);
        $totalRepaid = (int) ($lender->total_repaid ?? 0);
        $outstanding = $totalBorrowed - $totalRepaid;
    @endphp

    <div class="py-8"><div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 rounded-md bg-green-50 p-4 text-green-700">{{ session('success') }}</div>@endif
        <div class="rounded-xl bg-white p-6 shadow-sm">
            @if(! $lender->is_active)<div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700"><strong>Inactive Lender</strong><div class="mt-1">This lender remains available as historical borrowing data.</div></div>@endif
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div><div class="text-sm text-gray-500">Code</div><div class="mt-1 font-semibold">{{ $lender->code }}</div></div>
                <div><div class="text-sm text-gray-500">Name</div><div class="mt-1 font-semibold">{{ $lender->name }}</div></div>
                <div><div class="text-sm text-gray-500">Mobile</div><div class="mt-1 font-semibold">{{ $lender->mobile ?: '-' }}</div></div>
                <div><div class="text-sm text-gray-500">Address</div><div class="mt-1 font-semibold">{{ $lender->address ?: '-' }}</div></div>
                <div><div class="text-sm text-gray-500">Status</div><span class="mt-1 inline-flex rounded-md px-2.5 py-1 text-xs font-semibold {{ $lender->is_active?'bg-green-100 text-green-700':'bg-red-100 text-red-700' }}">{{ $lender->is_active ? 'Active' : 'Inactive' }}</span></div>
            </div>
            <div class="mt-8 border-t pt-6"><div class="text-sm text-gray-500">Note</div><div class="mt-2 whitespace-pre-line">{{ $lender->note ?: '-' }}</div></div>
            <div class="mt-8 grid gap-4 border-t pt-6 sm:grid-cols-3">
                <div class="rounded-md bg-gray-50 p-4"><div class="text-sm text-gray-500">Total Borrowed</div><div class="mt-1 text-xl font-bold">{{ number_format($totalBorrowed) }}</div></div>
                <div class="rounded-md bg-gray-50 p-4"><div class="text-sm text-gray-500">Total Repaid</div><div class="mt-1 text-xl font-bold text-green-700">{{ number_format($totalRepaid) }}</div></div>
                <div class="rounded-md bg-gray-50 p-4"><div class="text-sm text-gray-500">Outstanding</div><div class="mt-1 text-xl font-bold {{ $outstanding>0?'text-red-700':'text-green-700' }}">{{ number_format($outstanding) }}</div></div>
            </div>
        </div>
    </div></div>
</x-app-layout>
