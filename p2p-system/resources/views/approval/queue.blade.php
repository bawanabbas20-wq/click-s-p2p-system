<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __($pageTitle) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:px-6 lg:px-8">
            <!-- Clean Filter Bar -->
            <form method="GET" action="{{ route('approval.queue') }}" class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif

                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <svg class="absolute start-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search...') }}" 
                            class="w-full ps-10 pe-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-brand-green focus:border-brand-green">
                    </div>

                    <!-- Priority -->
                    <select name="priority" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-brand-green focus:border-brand-green">
                        <option value="">{{ __('All Priorities') }}</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                    </select>

                    <!-- Status -->
                    <select name="status" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-brand-green focus:border-brand-green">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="Pending Finance" {{ request('status') == 'Pending Finance' ? 'selected' : '' }}>{{ __('Pending Finance') }}</option>
                        <option value="Pending Final Payment" {{ request('status') == 'Pending Final Payment' ? 'selected' : '' }}>{{ __('Pending Final Payment') }}</option>
                        <option value="Pending Manager" {{ request('status') == 'Pending Manager' ? 'selected' : '' }}>{{ __('Pending Manager') }}</option>
                        <option value="Pending Procurement" {{ request('status') == 'Pending Procurement' ? 'selected' : '' }}>{{ __('Pending Procurement') }}</option>
                        <option value="Pending Final Approval" {{ request('status') == 'Pending Final Approval' ? 'selected' : '' }}>{{ __('Pending Final Approval') }}</option>
                    </select>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-green rounded-lg hover:bg-opacity-90 transition">
                            {{ __('Filter') }}
                        </button>
                        @if(request('search') || request('priority') || request('status'))
                            <a href="{{ route('approval.queue') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('error'))
                        <x-alert type="error" :message="session('error')" />
                    @endif

                    @if (session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif



                    @if (session('info'))
                        <x-alert type="info" :message="session('info')" />
                    @endif

                    @if($requests->isEmpty())
                        <x-empty-state 
                            title="{{ __('All Caught Up!') }}" 
                            message="{{ __('Your approval queue is empty. Great job staying on top of things!') }}" 
                            actionLabel="" 
                            :actionUrl="''" 
                        />
                    @else
                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        @php
                                            // Helper to create sort links
                                            $sortLink = function($column, $label) {
                                                $currentSort = request('sort', 'priority');
                                                $currentDirection = request('direction', 'asc');
                                                $newDirection = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                                                
                                                $icon = '';
                                                if ($currentSort === $column) {
                                                    $icon = $currentDirection === 'asc' ? ' &uarr;' : ' &darr;';
                                                }
                                                
                                                return '<a href="'.route('approval.queue', array_merge(request()->all(), ['sort' => $column, 'direction' => $newDirection])).'" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white group inline-flex items-center">
                                                            '. $label . '<span class="ml-1 text-gray-400 group-hover:text-gray-600">'.$icon.'</span>
                                                        </a>';
                                            };
                                        @endphp

                                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider">
                                            {{ __('Requested By') }}
                                        </th>
                                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider">
                                            {!! $sortLink('item_name', __('Item Name')) !!}
                                        </th>
                                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider">
                                            {!! $sortLink('estimated_price', __('Est. Price')) !!}
                                        </th>
                                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider">
                                            {!! $sortLink('created_at', __('Date Requested')) !!}
                                        </th>
                                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider">
                                            {!! $sortLink('priority', __('Priority')) !!}
                                        </th>
                                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider">
                                            {!! $sortLink('status', __('Status')) !!}
                                        </th>
                                        <th scope="col" class="relative px-4 py-2.5 text-right">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($requests as $request)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $request->user->name }}</td>
                                            <td class="px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400">{{ $request->item_name }}</td>
                                            <td class="px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400">
                                                @if($request->estimated_currency === 'USD')
                                                    ${{ number_format($request->estimated_price, 2) }}
                                                @else
                                                    {{ number_format($request->estimated_price, 0) }} IQD
                                                @endif
                                            </td>
                                            <td class="px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400">{{ $request->created_at->format('Y-m-d') }}</td>
                                            <td class="px-4 py-2.5 text-sm font-medium">
                                                <x-priority-badge :priority="$request->priority" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <x-status-badge :status="$request->status" />
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium">
                                                <a href="{{ route('approval.show', $request) }}" class="text-brand-green hover:text-opacity-80 font-semibold">{{ __('View') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
