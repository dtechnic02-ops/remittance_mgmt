<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Shareholder KYC
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $shareholder->code }} - {{ $shareholder->name }}
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('shareholders.edit', $shareholder) }}"
                   class="px-4 py-2 bg-gray-800 text-white rounded-md">
                    Edit
                </a>

                <a href="{{ route('shareholders.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Profile --}}
            <div class="bg-white shadow-sm sm:rounded-lg">

                <div class="p-6">

                    <div class="flex flex-col md:flex-row gap-6">

                        {{-- Photo --}}
                        <div class="shrink-0">

                            @if ($shareholder->photo)

                                <img
                                    src="{{ Storage::disk('public')->url($shareholder->photo) }}"
                                    alt="{{ $shareholder->name }}"
                                    class="w-32 h-32 rounded-lg object-cover border"
                                >

                            @else

                                <div class="w-32 h-32 rounded-lg bg-gray-100 border flex items-center justify-center text-4xl font-semibold text-gray-500">
                                    {{ strtoupper(substr($shareholder->name, 0, 1)) }}
                                </div>

                            @endif

                        </div>

                        {{-- Main Identity --}}
                        <div class="flex-1">

                            <div class="flex flex-wrap items-center gap-3">

                                <h1 class="text-2xl font-bold text-gray-900">
                                    {{ $shareholder->name }}
                                </h1>

                                @if ($shareholder->is_active)
                                    <span class="rounded-md bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="rounded-md bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Inactive
                                    </span>
                                @endif

                            </div>

                            <div class="mt-2 text-gray-600">
                                Shareholder Code:
                                <span class="font-semibold text-gray-900">
                                    {{ $shareholder->code }}
                                </span>
                            </div>

                            <div class="mt-1 text-gray-600">
                                Type:
                                <span class="font-semibold text-gray-900">
                                    {{ $shareholder->type
                                        ? ucfirst($shareholder->type)
                                        : '-'
                                    }}
                                </span>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            {{-- Personal Information --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Personal Information
                    </h3>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <div class="text-sm text-gray-500">
                                Full Name
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $shareholder->name }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Mobile
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $shareholder->mobile ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Email
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $shareholder->email ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Address
                            </div>

                            <div class="mt-1 font-medium text-gray-900 whitespace-pre-line">
                                {{ $shareholder->address ?: '-' }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- Share Information --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Share Information
                    </h3>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div class="rounded-md bg-gray-50 p-5">

                            <div class="text-sm text-gray-500">
                                Kitta
                            </div>

                            <div class="mt-2 text-2xl font-bold text-gray-900">
                                {{ number_format($shareholder->kitta) }}
                            </div>

                        </div>

                        <div class="rounded-md bg-gray-50 p-5">

                            <div class="text-sm text-gray-500">
                                Per Kitta Value
                            </div>

                            <div class="mt-2 text-2xl font-bold text-gray-900">
                                {{ number_format($shareholder->per_kitta_value) }}
                            </div>

                        </div>

                        <div class="rounded-md bg-gray-50 p-5">

                            <div class="text-sm text-gray-500">
                                Total Investment
                            </div>

                            <div class="mt-2 text-2xl font-bold text-gray-900">
                                {{ number_format($shareholder->total_investment) }}
                            </div>

                        </div>

                    </div>

                    <div class="mt-4 text-xs text-gray-500">
                        Total Investment =
                        Kitta × Per Kitta Value
                    </div>

                </div>
            </div>

            {{-- KYC Documents --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        KYC Documents
                    </h3>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- Citizenship Front --}}
                        <div class="border rounded-lg p-4">

                            <div class="font-medium text-gray-800">
                                Citizenship Front
                            </div>

                            @if ($shareholder->citizenship_front)

                                <div class="mt-4">
                                    <a
                                        href="{{ Storage::disk('public')->url($shareholder->citizenship_front) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="text-blue-600 hover:underline">
                                        View Document
                                    </a>
                                </div>

                            @else

                                <div class="mt-4 text-sm text-gray-500">
                                    Not uploaded
                                </div>

                            @endif

                        </div>

                        {{-- Citizenship Back --}}
                        <div class="border rounded-lg p-4">

                            <div class="font-medium text-gray-800">
                                Citizenship Back
                            </div>

                            @if ($shareholder->citizenship_back)

                                <div class="mt-4">
                                    <a
                                        href="{{ Storage::disk('public')->url($shareholder->citizenship_back) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="text-blue-600 hover:underline">
                                        View Document
                                    </a>
                                </div>

                            @else

                                <div class="mt-4 text-sm text-gray-500">
                                    Not uploaded
                                </div>

                            @endif

                        </div>

                        {{-- Other Document --}}
                        <div class="border rounded-lg p-4">

                            <div class="font-medium text-gray-800">
                                Other Document
                            </div>

                            @if ($shareholder->other_document)

                                <div class="mt-4">
                                    <a
                                        href="{{ Storage::disk('public')->url($shareholder->other_document) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="text-blue-600 hover:underline">
                                        View Document
                                    </a>
                                </div>

                            @else

                                <div class="mt-4 text-sm text-gray-500">
                                    Not uploaded
                                </div>

                            @endif

                        </div>

                    </div>

                </div>
            </div>

            {{-- Note --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Note
                    </h3>
                </div>

                <div class="p-6 text-gray-800 whitespace-pre-line">
                    {{ $shareholder->note ?: '-' }}
                </div>

            </div>

            {{-- Audit Information --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">

                <div class="p-6 border-b">
                    <h3 class="font-semibold text-lg text-gray-800">
                        Record Information
                    </h3>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <div class="text-sm text-gray-500">
                                Created By
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareholder->creator?->name ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Last Updated By
                            </div>

                            <div class="mt-1 font-medium">
                                {{ $shareholder->updater?->name ?: '-' }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>