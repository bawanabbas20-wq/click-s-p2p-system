<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Purchase History: ') }} {{ $user->name }}
            </h2>
            <a href="{{ route('analytics.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                &larr; {{ __('Back to Analytics') }}
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div class="text-gray-600 dark:text-gray-300">
                    {{ __('Total Requests:') }} <span class="font-semibold text-gray-900 dark:text-white">{{ $requests->total() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Item Name') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Date Requested') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Est. Price') }}</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Final Price') }}</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">{{ __('View') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ Str::limit($request->item_name, 40) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($request->status)
                                        @case('Denied') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 @break
                                        @case('Completed') @case('Fulfilled from Stock') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @break
                                        @case('Approved for Purchase') @case('Purchase Logged') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 @break
                                        @case('Pending Final Payment') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 @break
                                        @case('Ready to Buy') bg-brand-green/20 text-brand-green @break
                                        @default bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                    @endswitch">
                                    {{ __($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($request->estimated_currency === 'USD')
                                    ${{ number_format($request->estimated_price, 2) }}
                                @else
                                    {{ number_format($request->estimated_price, 0) }} IQD
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($request->chosenOffer)
                                    <span class="text-green-600 dark:text-green-400 font-semibold">
                                    @if($request->chosenOffer->currency === 'USD')
                                        ${{ number_format($request->chosenOffer->price, 2) }}
                                    @else
                                        {{ number_format($request->chosenOffer->price, 0) }} IQD
                                    @endif
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                @can('is-approver')
                                    <a href="{{ route('approval.show', $request) }}" class="text-brand-green hover:text-green-700 dark:hover:text-green-300 transition-colors">{{ __('View') }}</a>
                                @else
                                    <a href="{{ route('requests.show', $request) }}" class="text-brand-green hover:text-green-700 dark:hover:text-green-300 transition-colors">{{ __('View') }}</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p>{{ __('No purchase history found for this user.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($requests->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
