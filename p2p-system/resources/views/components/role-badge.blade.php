@props(['role'])

@php
    $roleClasses = match(strtolower($role)) {
        'admin' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
        'finance' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300',
        'manager' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
        'procurement' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
        'employee' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$roleClasses}"]) }}>
    {{ __(ucfirst($role)) }}
</span>
