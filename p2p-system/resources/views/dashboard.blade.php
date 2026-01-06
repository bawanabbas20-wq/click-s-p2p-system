@section('title', 'Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Welcome back, :name!', ['name' => Auth::user()->name]) }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Here\'s what\'s happening with your purchase requests.') }}</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ms-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Requests') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalRequests }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-500 dark:text-yellow-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ms-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pending Status') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $pendingRequests }}</p>
                        </div>
                    </div>
                </div>

                <!-- Completed Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-500 dark:text-green-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ms-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed') }}</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $completedRequests }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('Recent Requests') }}</h3>
                    <div class="flex gap-x-3 items-center">
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                             <button @click="open = !open" type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none me-2" title="Filter Requests">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                            </button>
                            <!-- Mobile: Full-width bottom sheet style, Desktop: Dropdown -->
                             <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="fixed sm:absolute inset-x-4 sm:inset-x-auto top-1/2 sm:top-auto sm:end-0 sm:mt-2 -translate-y-1/2 sm:translate-y-0 w-auto sm:w-72 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-[100] p-4 text-start border border-gray-200 dark:border-gray-700"
                                 style="display: none;">
                                <form method="GET" action="{{ route('dashboard') }}" class="space-y-4">
                                    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700 sm:hidden">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">{{ __('Filter Requests') }}</h4>
                                        <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Search Item') }}</label>
                                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-green focus:ring focus:ring-brand-green focus:ring-opacity-50 text-sm dark:bg-gray-700 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-green focus:ring focus:ring-brand-green focus:ring-opacity-50 text-sm dark:bg-gray-700 dark:text-gray-100">
                                            <option value="">{{ __('Any') }}</option>
                                            <option value="Approved for Purchase" {{ request('status') == 'Approved for Purchase' ? 'selected' : '' }}>{{ __('Approved for Purchase') }}</option>
                                            <option value="Pending Finance" {{ request('status') == 'Pending Finance' ? 'selected' : '' }}>{{ __('Pending Finance') }}</option>
                                            <option value="Pending Manager" {{ request('status') == 'Pending Manager' ? 'selected' : '' }}>{{ __('Pending Manager') }}</option>
                                            <option value="Ready to Buy" {{ request('status') == 'Ready to Buy' ? 'selected' : '' }}>{{ __('Ready to Buy') }}</option>
                                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-between items-center pt-2">
                                        <a href="{{ route('dashboard') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">{{ __('Clear') }}</a>
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-green hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                            {{ __('Apply') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-green border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Create Request') }}
                        </a>
                    </div>
                </div>
                
                @if($recentRequests->isEmpty())
                    <div class="p-6">
                        <x-empty-state 
                            title="{{ __('No Recent Activity') }}" 
                            message="{{ __('You haven\'t made any requests recently.') }}" 
                        />
                    </div>
                @else
                    <div class="overflow-x-auto min-h-[300px]">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Item') }}</th>
                                    <th class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date') }}</th>
                                    <th class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-4 py-2.5 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentRequests as $request)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $request->item_name }}</td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $request->created_at->format('M d, Y') }}</td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-sm">
                                            <x-status-badge :status="$request->status" />
                                        </td>
                                        <td class="px-4 py-2.5 whitespace-nowrap text-end text-sm font-medium">
                                            <a href="{{ route('requests.show', $request) }}" class="text-brand-green hover:text-opacity-80 font-semibold">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                     <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $recentRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
