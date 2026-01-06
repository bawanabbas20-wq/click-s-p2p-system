<x-app-layout>
    <x-slot name="header">
        {{ __('Request Details') }}: {{ $purchaseRequest->item_name }}
    </x-slot>
    
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg max-w-7xl mx-auto">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Request Information') }}</h3>
                    <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Item Name') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->item_name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <x-status-badge :status="$purchaseRequest->status" />
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Priority') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <x-priority-badge :priority="$purchaseRequest->priority" />
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estimated Price') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($purchaseRequest->estimated_currency === 'USD')
                                    ${{ number_format($purchaseRequest->estimated_price, 2) }}
                                @else
                                    {{ number_format($purchaseRequest->estimated_price, 0) }} IQD
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date Wanted') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($purchaseRequest->date_wanted)->format('F j, Y') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Justification') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->justification }}</dd>
                        </div>
                        
                        @if($purchaseRequest->parent_request_id)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Original Request') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <a href="{{ route('requests.show', $purchaseRequest->parent_request_id) }}" class="text-brand-green hover:text-opacity-80 font-medium">
                                    {{ __('Resubmitted from Request #') }}{{ $purchaseRequest->parent_request_id }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        
                        @if($purchaseRequest->childRequests->count() > 0)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">{{ __('Resubmissions') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @foreach($purchaseRequest->childRequests as $child)
                                    <a href="{{ route('requests.show', $child->id) }}" class="inline-block mr-3 text-brand-green hover:text-opacity-80 font-medium">
                                        {{ __('Request #') }}{{ $child->id }} ({{ __($child->status) }})
                                    </a>
                                @endforeach
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>

                @if ($purchaseRequest->offers->isNotEmpty() && in_array(auth()->user()->role, ['procurement', 'finance', 'manager', 'admin']))
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Submitted Quotations') }}</h3>
                    <div class="mt-4 overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Vendor') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Price') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('File') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($purchaseRequest->offers->sortByDesc('is_chosen') as $offer)
                                    <tr class="{{ $offer->is_chosen ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                        <td class="px-6 py-4 text-sm font-medium">
                                            @if($offer->is_chosen)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    {{ __('Chosen') }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ __('Not Chosen') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $offer->vendor_name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($offer->currency === 'USD')
                                                ${{ number_format($offer->price, 2) }}
                                            @else
                                                {{ number_format($offer->price, 0) }} IQD
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($offer->quotation_file_path)
                                                <a href="{{ Storage::url($offer->quotation_file_path) }}" target="_blank" class="text-brand-green hover:text-opacity-80">
                                                    {{ __('View File') }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">{{ __('N/A') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="md:col-span-1 space-y-6">
            @if(in_array($purchaseRequest->status, ['Purchase Logged', 'Fulfilled from Stock']) && auth()->id() === $purchaseRequest->user_id)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl p-6">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Item is Ready for Pickup') }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Please confirm once you have received your item.') }}</p>
                    <form method="POST" action="{{ route('requests.confirm', $purchaseRequest) }}" class="mt-4" onsubmit="return confirm('{{ __('Are you sure you have received this item?') }}');">
                        @csrf
                        <x-primary-button class="w-full justify-center">
                            {{ __('Confirm Receipt') }}
                        </x-primary-button>
                    </form>
                </div>
            @endif

            @if($purchaseRequest->status === 'Denied' && auth()->id() === $purchaseRequest->user_id)
                <div class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-lg">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 border-2 border-green-500">
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Request Denied') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('Don\'t worry! You can easily resubmit this request for next month with all the same details.') }}</p>
                        
                        <form method="POST" action="{{ route('requests.resubmit', $purchaseRequest) }}" onsubmit="return confirm('{{ __('This will create a new request scheduled for next month with the same details. Continue?') }}');">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3.5 bg-green-500 hover:bg-green-600 border-none rounded-xl font-semibold text-sm text-white uppercase tracking-wide shadow-lg shadow-green-500/20 hover:shadow-xl hover:shadow-green-500/30 hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('Resubmit for Next Month') }}
                            </button>
                        </form>
                        
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">{{ __('All details will be copied automatically') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Request History') }}</h3>
                    <ul class="mt-4 space-y-6">
                        @forelse ($purchaseRequest->requestLogs->sortBy('created_at') as $log)
                            <li class="flex gap-x-4">
                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-200" src="{{ $log->user->avatar ? Storage::url($log->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($log->user->name).'&size=80&background=10b981&color=ffffff&bold=true&format=svg' }}" alt="">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->user->name }} <span class="text-gray-500 dark:text-gray-400 font-normal">({{ ucfirst($log->user->role) }})</span></p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('Changed status to:') }} <span class="font-semibold">{{ $log->new_status }}</span></p>
                                    @if($log->comment)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border dark:border-gray-600">{{ $log->comment }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">{{ $log->created_at->format('F j, Y \a\t g:i a') }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500 dark:text-gray-400">{{ __('No history for this request yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
