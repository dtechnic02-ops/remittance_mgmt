<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customer Details
            </h2>

            <div class="flex gap-4">
                <a href="{{ route('customers.edit', $customer) }}"
                   class="text-indigo-600">
                    Edit
                </a>

                <a href="{{ route('customers.index') }}"
                   class="text-gray-600">
                    Back to Customers
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                        {{-- Photo --}}
                        <div>
                            <p class="text-sm text-gray-500 mb-2">
                                Customer Photo
                            </p>

                            @if ($customer->photo)
                                <img src="{{ asset('storage/'.$customer->photo) }}"
                                     alt="{{ $customer->name }}"
                                     class="w-40 h-40 object-cover rounded-md border">
                            @else
                                <div class="w-40 h-40 bg-gray-100 rounded-md border flex items-center justify-center text-4xl font-semibold text-gray-500">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Main Information --}}
                        <div class="lg:col-span-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Customer Code
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->customer_code }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Name
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->name }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Mobile
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->mobile ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Phone
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->phone ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Email
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->email ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Citizenship Number
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->citizenship_number ?: '-' }}
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500">
                                        Address
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->address ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Status
                                    </p>

                                    @if ($customer->is_active)
                                        <p class="font-semibold text-green-700">
                                            Active
                                        </p>
                                    @else
                                        <p class="font-semibold text-red-700">
                                            Inactive
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">
                                        Created By
                                    </p>
                                    <p class="font-semibold">
                                        {{ $customer->creator?->name ?: '-' }}
                                    </p>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- Citizenship --}}
                    <div class="mt-10 border-t pt-8">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Citizenship Documents
                        </h3>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <p class="text-sm text-gray-500 mb-2">
                                    Front
                                </p>

                                @if ($customer->citizenship_front)
                                    <a href="{{ asset('storage/'.$customer->citizenship_front) }}"
                                       target="_blank">
                                        <img src="{{ asset('storage/'.$customer->citizenship_front) }}"
                                             alt="Citizenship Front"
                                             class="max-h-72 rounded-md border object-contain">
                                    </a>
                                @else
                                    <p class="text-gray-500">
                                        No front image.
                                    </p>
                                @endif
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 mb-2">
                                    Back
                                </p>

                                @if ($customer->citizenship_back)
                                    <a href="{{ asset('storage/'.$customer->citizenship_back) }}"
                                       target="_blank">
                                        <img src="{{ asset('storage/'.$customer->citizenship_back) }}"
                                             alt="Citizenship Back"
                                             class="max-h-72 rounded-md border object-contain">
                                    </a>
                                @else
                                    <p class="text-gray-500">
                                        No back image.
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- Other Documents --}}
                    <div class="mt-10 border-t pt-8">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Other Documents
                        </h3>

                        @if ($customer->otherDocuments->isNotEmpty())

                            <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-5">

                                @foreach ($customer->otherDocuments as $document)

                                    <div class="border rounded-md p-3">

                                        <a href="{{ asset('storage/'.$document->file_path) }}"
                                           target="_blank">

                                            <img src="{{ asset('storage/'.$document->file_path) }}"
                                                 alt="Customer Document"
                                                 class="w-full h-36 object-cover rounded-md">

                                        </a>

                                        <form method="POST"
                                              action="{{ route('customers.documents.destroy', [
                                                  'customer' => $customer,
                                                  'document' => $document->id,
                                              ]) }}"
                                              class="mt-3"
                                              onsubmit="return confirm('Remove this document?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="text-sm text-red-600">
                                                Remove
                                            </button>
                                        </form>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <p class="mt-4 text-gray-500">
                                No other documents.
                            </p>

                        @endif
                    </div>

                    {{-- Note --}}
                    <div class="mt-10 border-t pt-8">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Note
                        </h3>

                        <div class="mt-3 bg-gray-50 rounded-md p-4 whitespace-pre-line">
                            {{ $customer->note ?: 'No note.' }}
                        </div>
                    </div>

                    {{-- Audit --}}
                    <div class="mt-10 border-t pt-6 text-sm text-gray-500">
                        Created:
                        {{ $customer->created_at?->format('Y-m-d H:i') }}

                        <br>

                        Updated:
                        {{ $customer->updated_at?->format('Y-m-d H:i') }}

                        @if (! $customer->is_active && $customer->deactivated_at)
                            <br>
                            Deactivated:
                            {{ $customer->deactivated_at->format('Y-m-d H:i') }}
                            by
                            {{ $customer->deactivator?->name ?: '-' }}
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>