@section('title', 'My Purchase Requests')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Requests') }}
            </h2>
            <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-green border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Request
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            @if($requests->isEmpty())
                <x-empty-state 
                    title="No Requests Found" 
                    message="You haven't created any purchase requests yet. Start by creating your first request." 
                    actionLabel="Create New Request" 
                    :actionUrl="route('requests.create')" 
                />
            @else
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-hidden border border-gray-200 rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Item Name') }}</th>
                                <th scope="col" class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Priority') }}</th>
                                <th scope="col" class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Price') }}</th>
                                <th scope="col" class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date Wanted') }}</th>
                                <th scope="col" class="px-4 py-2.5 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="relative px-4 py-2.5"><span class="sr-only">{{ __('View') }}</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($requests as $request)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $request->item_name }}
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm">
                                        <x-priority-badge :priority="$request->priority" />
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($request->estimated_currency === 'USD')
                                            ${{ number_format($request->estimated_price, 2) }}
                                        @else
                                            {{ number_format($request->estimated_price, 0) }} IQD
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($request->date_wanted)->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm">
                                        <x-status-badge :status="$request->status" />
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-end text-sm font-medium">
                                        <a href="{{ route('requests.show', $request) }}" class="text-brand-green hover:text-opacity-80">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    @foreach ($requests as $request)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 flex-1 pe-2">{{ $request->item_name }}</h3>
                                <x-status-badge :status="$request->status" class="flex-shrink-0" />
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span class="font-medium">Priority:</span>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full me-1 
                                            @if($request->priority === 'high') bg-red-500
                                            @elseif($request->priority === 'medium') bg-orange-500
                                            @else bg-green-500 @endif">
                                        </div>
                                        <span class="capitalize text-xs font-medium 
                                            @if($request->priority === 'high') text-red-700
                                            @elseif($request->priority === 'medium') text-orange-700
                                            @else text-green-700 @endif">
                                            {{ $request->priority }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Price:</span>
                                    <span>
                                        @if($request->estimated_currency === 'USD')
                                            ${{ number_format($request->estimated_price, 2) }}
                                        @else
                                            {{ number_format($request->estimated_price, 0) }} IQD
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Date:</span>
                                    <span>{{ $request->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <a href="{{ route('requests.show', $request) }}" 
                                   class="inline-flex items-center text-sm font-medium text-brand-green hover:text-opacity-80">
                                    View Details
                                    <svg class="ms-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
