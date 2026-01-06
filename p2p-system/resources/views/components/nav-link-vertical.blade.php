@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-sm font-semibold text-brand-green bg-light-gray rounded-lg'
            : 'flex items-center px-4 py-3 text-sm font-medium text-gray-500 rounded-lg hover:bg-gray-50 hover:text-gray-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
