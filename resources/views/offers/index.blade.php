<x-app-layout>
    <x-slot name="header">
        {{ __('Requests Needing Quotations') }}
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
        <div class="p-6 text-gray-900 dark:text-gray-100">

            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            <!-- Clean Filter Bar -->
            <form method="GET" action="{{ route('offers.index') }}" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <svg class="absolute start-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search items...') }}" 
                            class="w-full ps-10 pe-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-brand-green focus:border-brand-green">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-green rounded-lg hover:bg-opacity-90 transition">
                            {{ __('Search') }}
                        </button>
                        @if(request('search'))
                            <a href="{{ route('offers.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500 transition">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if($requests->isEmpty())
                <x-empty-state 
                    title="{{ __('No Pending Purchases') }}" 
                    message="{{ __('There are no requests currently approved and ready for purchase.') }}" 
                    actionLabel="" 
                    :actionUrl="''" 
                />
            @else
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg min-h-[500px]">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Item Name') }}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Requested By') }}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Date Wanted By') }}</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Est. Price') }}</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">{{ __('Log Purchase') }}</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($requests as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $request->item_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $request->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $request->date_wanted }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($request->estimated_currency === 'USD')
                                            ${{ number_format($request->estimated_price, 2) }}
                                        @else
                                            {{ number_format($request->estimated_price, 0) }} IQD
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                        <a href="{{ route('offers.create', $request) }}" class="inline-flex items-center px-4 py-2 bg-brand-green border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Manage Offers') }}
                                        </a>
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
</x-app-layout>
