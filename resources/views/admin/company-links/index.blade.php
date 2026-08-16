<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Company Links
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Add Link</h3>

                    <form method="POST" action="{{ route('company-links.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Title
                                </label>

                                <input
                                    type="text"
                                    name="title"
                                    value="{{ old('title') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    URL
                                </label>

                                <input
                                    type="url"
                                    name="url"
                                    value="{{ old('url') }}"
                                    placeholder="https://example.com"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                >
                            </div>
                        </div>

                        <div class="mt-4">
                            <button
                                type="submit"
                                class="rounded bg-blue-600 px-4 py-2 text-white"
                            >
                                Add Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Links</h3>

                    @forelse ($links as $link)
                        <div class="border rounded-lg p-4 mb-4">

                            <form
                                method="POST"
                                action="{{ route('company-links.update', $link) }}"
                            >
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            Title
                                        </label>

                                        <input
                                            type="text"
                                            name="title"
                                            value="{{ $link->title }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                            required
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            URL
                                        </label>

                                        <input
                                            type="url"
                                            name="url"
                                            value="{{ $link->url }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button
                                        type="submit"
                                        class="rounded bg-blue-600 px-4 py-2 text-white"
                                    >
                                        Update
                                    </button>
                                </div>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('company-links.destroy', $link) }}"
                                class="mt-2"
                                onsubmit="return confirm('Delete this link?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="rounded bg-red-600 px-4 py-2 text-white"
                                >
                                    Delete
                                </button>
                            </form>

                        </div>
                    @empty
                        <p class="text-gray-500">
                            No links added yet.
                        </p>
                    @endforelse

                </div>
            </div>

        </div>
    </div>
</x-app-layout>