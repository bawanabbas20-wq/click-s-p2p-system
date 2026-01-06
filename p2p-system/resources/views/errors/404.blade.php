<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Page Not Found') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6 text-center">
                    <!-- Error Icon -->
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                        <svg class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>

                    <!-- Error Message -->
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">404</h1>
                    <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Page Not Found</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        Sorry, the page you're looking for doesn't exist or may have been moved. 
                        This could happen if:
                    </p>

                    <!-- Possible Reasons -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-8 text-left max-w-md mx-auto">
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-brand-green rounded-full me-3"></span>
                                The request has been processed already
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-brand-green rounded-full me-3"></span>
                                You don't have permission to view this item
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-brand-green rounded-full me-3"></span>
                                The URL was typed incorrectly
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-brand-green rounded-full me-3"></span>
                                The item was deleted or moved
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-6 py-3 bg-brand-green text-white font-medium rounded-lg hover:bg-opacity-80 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Go to Dashboard
                        </a>
                        
                        <button onclick="history.back()" 
                                class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Go Back
                        </button>
                    </div>

                    <!-- Quick Links -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Quick Links:</p>
                        <div class="flex flex-wrap justify-center gap-4 text-sm">
                            <a href="{{ route('requests.index') }}" class="text-brand-green hover:underline">My Requests</a>
                            <a href="{{ route('approval.queue') }}" class="text-brand-green hover:underline">Approval Queue</a>
                            <a href="{{ route('analytics.index') }}" class="text-brand-green hover:underline">Analytics</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
