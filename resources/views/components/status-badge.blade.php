@props(['status'])

@php
    $statusClasses = match($status) {
        'Completed', 'Fulfilled from Stock' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
        'Approved for Purchase', 'Purchase Logged', 'Ready to Buy' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
        'Denied', 'Rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
        'Pending Final Payment', 'Pending Finance', 'Pending Manager', 'Pending Procurement', 'Pending Final Approval' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$statusClasses}"]) }}>
    {{ __($status) }}
</span>
