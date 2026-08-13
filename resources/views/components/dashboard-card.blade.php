@props(['title', 'value', 'featured' => false, 'negative' => false])

<div @class([
    'rounded-lg border p-5 shadow-sm',
    'border-blue-200 bg-blue-50' => $featured,
    'border-gray-200 bg-white' => ! $featured,
])>
    <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
    <p @class([
        'mt-2 text-2xl font-semibold tabular-nums',
        'text-red-700' => $negative,
        'text-gray-900' => ! $negative,
    ])>{{ $value }}</p>
</div>
