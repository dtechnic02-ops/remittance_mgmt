<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Company Info
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if (session('success'))
                        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('company-info.update') }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Company Name
                                </label>
                                <input
                                    type="text"
                                    name="company_name"
                                    value="{{ old('company_name', $companyInfo?->company_name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Location
                                </label>
                                <input
                                    type="text"
                                    name="location"
                                    value="{{ old('location', $companyInfo?->location) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Mobile Number
                                </label>
                                <input
                                    type="text"
                                    name="mobile_number"
                                    value="{{ old('mobile_number', $companyInfo?->mobile_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Phone Number
                                </label>
                                <input
                                    type="text"
                                    name="phone_number"
                                    value="{{ old('phone_number', $companyInfo?->phone_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $companyInfo?->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Website
                                </label>
                                <input
                                    type="url"
                                    name="website"
                                    value="{{ old('website', $companyInfo?->website) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Staff Mobile Number
                                </label>
                                <input
                                    type="text"
                                    name="staff_mobile_number"
                                    value="{{ old('staff_mobile_number', $companyInfo?->staff_mobile_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Staff Email
                                </label>
                                <input
                                    type="email"
                                    name="staff_email"
                                    value="{{ old('staff_email', $companyInfo?->staff_email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Staff Title
                                </label>
                                <input
                                    type="text"
                                    name="staff_title"
                                    value="{{ old('staff_title', $companyInfo?->staff_title) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Company Logo
                                </label>
                                <input
                                    type="file"
                                    name="logo"
                                    accept="image/*"
                                    class="mt-1 block w-full"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Company Image
                                </label>
                                <input
                                    type="file"
                                    name="image"
                                    accept="image/*"
                                    class="mt-1 block w-full"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Thumbnail Image
                                </label>
                                <input
                                    type="file"
                                    name="thumbnail_image"
                                    accept="image/*"
                                    class="mt-1 block w-full"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Staff Photo
                                </label>
                                <input
                                    type="file"
                                    name="staff_photo"
                                    accept="image/*"
                                    class="mt-1 block w-full"
                                >
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description 1
                                </label>
                                <textarea
                                    name="description_1"
                                    rows="5"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >{{ old('description_1', $companyInfo?->description_1) }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description 2
                                </label>
                                <textarea
                                    name="description_2"
                                    rows="5"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >{{ old('description_2', $companyInfo?->description_2) }}</textarea>
                            </div>

                        </div>

                        <div class="mt-6">
                            <button
                                type="submit"
                                class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                            >
                                Save Company Info
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>