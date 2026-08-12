<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Expense Category
            </h2>

            <a href="{{ route('expense-categories.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Back to Categories
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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
                      action="{{ route('expense-categories.update', $expenseCategory) }}"
                      class="p-6">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label for="name"
                                   class="block text-sm font-medium text-gray-700">
                                Category Name *
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $expenseCategory->name) }}"
                                   maxlength="150"
                                   required
                                   autofocus
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('name')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="code"
                                   class="block text-sm font-medium text-gray-700">
                                Code
                            </label>

                            <input type="text"
                                   id="code"
                                   name="code"
                                   value="{{ old('code', $expenseCategory->code) }}"
                                   maxlength="50"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                            @error('code')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-6">
                        <label for="description"
                               class="block text-sm font-medium text-gray-700">
                            Description
                        </label>

                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $expenseCategory->description) }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-6">

                        <label class="inline-flex items-center">

                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', $expenseCategory->is_active))
                                   class="rounded border-gray-300 text-gray-800 shadow-sm">

                            <span class="ms-2 text-sm text-gray-700">
                                Active
                            </span>

                        </label>

                        <p class="mt-1 text-xs text-gray-500">
                            Uncheck this to stop using this category for new expenses.
                        </p>

                    </div>

                    <div class="mt-8 flex justify-end gap-3">

                        <a href="{{ route('expense-categories.index') }}"
                           class="px-5 py-2 border border-gray-300 rounded-md text-gray-700">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-5 py-2 bg-gray-800 text-white rounded-md">
                            Update Category
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>