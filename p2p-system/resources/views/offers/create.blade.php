<x-app-layout>
    <x-slot name="header">
        {{ __('Manage Offers for:') }} {{ $purchaseRequest->item_name }}
    </x-slot>

    <!-- Rejection Feedback Banner -->
    @if(isset($rejectionLog) && $rejectionLog)
        <div class="max-w-7xl mx-auto mt-6 mb-2">
            <div class="bg-yellow-50 dark:bg-yellow-900/50 border-l-4 border-yellow-400 p-4 shadow-sm rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            {{ __('Action Required: Quotation Rejected by') }} {{ $rejectionLog->user->name ?? ucfirst($rejectionLog->user->role) }}
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p><strong>{{ __('Reason:') }}</strong> "{{ $rejectionLog->comment }}"</p>
                            <p class="mt-1 text-xs">{{ __('Please review the reason above and upload a new/better offer.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Purchase Request Details - Full Width -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Request Details') }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Information about this purchase request.') }}</p>
                
                <dl class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Requested By') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Item Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->item_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estimated Price') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @if($purchaseRequest->estimated_currency === 'USD')
                                ${{ number_format($purchaseRequest->estimated_price, 2) }}
                            @else
                                {{ number_format($purchaseRequest->estimated_price, 0) }} IQD
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Priority') }}</dt>
                        <dd class="mt-1 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($purchaseRequest->priority === 'high') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                @elseif($purchaseRequest->priority === 'medium') bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300
                                @else bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @endif">
                                {{ ucfirst($purchaseRequest->priority) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date Wanted') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->date_wanted }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Justification') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($purchaseRequest->justification, 100) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Add New Offer and Submitted Offers - Side by Side -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Add New Offer') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Add a new quotation you have received.') }}</p>
                    
                    <form method="POST" action="{{ route('offers.store', $purchaseRequest) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="vendor_id" :value="__('Select Vendor')" />
                            <div class="flex gap-2">
                                <select name="vendor_id" id="vendor_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">{{ __('-- Select a Vendor --') }}</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }} ({{ str_repeat('★', $vendor->rating) }}{{ str_repeat('☆', 5 - $vendor->rating) }})
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('vendors.create', ['return_to' => route('offers.create', $purchaseRequest)]) }}" class="mt-1 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" title="Add New Vendor">
                                    +
                                </a>
                            </div>
                            <x-input-error :messages="$errors->get('vendor_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="price" :value="__('Price')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price')" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="currency" :value="__('Currency')" />
                            <select name="currency" id="currency" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="IQD" {{ old('currency', $siteSettings['default_currency'] ?? 'USD') == 'IQD' ? 'selected' : '' }}>IQD</option>
                                <option value="USD" {{ old('currency', $siteSettings['default_currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label for="quotation_file" :value="__('Quotation File (PDF, JPG, PNG)')" />
                            <input id="quotation_file" name="quotation_file" type="file" class="block mt-1 w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                file:text-sm file:font-semibold file:bg-brand-green/10 file:text-brand-green
                                hover:file:bg-brand-green/20 dark:file:bg-brand-green/20 dark:file:text-brand-green-light
                            "/>
                            <x-input-error :messages="$errors->get('quotation_file')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-2">
                            <x-primary-button class="w-full justify-center">
                                {{ __('Add Offer') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Submitted Offers') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Review all submitted offers. Select one to finalize the purchase.') }}</p>

                    @if (session('success'))
                        <div class="my-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('offers.submitRecommendation', $purchaseRequest) }}" method="POST">
                        @csrf
                        <div class="mt-6 overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Select') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Vendor') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Price') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('File') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($purchaseRequest->offers as $offer)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="document.getElementById('offer_{{ $offer->id }}').checked = true">
                                            <td class="px-6 py-4 text-center">
                                                <input type="radio" id="offer_{{ $offer->id }}" name="selected_offer_id" value="{{ $offer->id }}" class="focus:ring-brand-green h-4 w-4 text-brand-green border-gray-300" required>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $offer->vendor_name }}
                                                @if($offer->vendor)
                                                    <span class="text-yellow-500 text-xs ml-1 inline-flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @php $percent = max(0, min(100, ($offer->vendor->rating - ($i - 1)) * 100)); @endphp
                                                            <div class="relative inline-block text-gray-300 dark:text-gray-600">
                                                                <i class="far fa-star"></i>
                                                                <div class="absolute top-0 start-0 overflow-hidden text-yellow-500" style="width: {{ $percent }}%">
                                                                    <i class="fas fa-star"></i>
                                                                </div>
                                                            </div>
                                                        @endfor
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                @if($offer->currency === 'USD')
                                                    ${{ number_format($offer->price, 2) }}
                                                @else
                                                    {{ number_format($offer->price, 0) }} IQD
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if($offer->quotation_file_path)
                                                    <a href="{{ Storage::url($offer->quotation_file_path) }}" target="_blank" class="text-brand-green hover:text-opacity-80" onclick="event.stopPropagation();">
                                                        {{ __('View File') }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No offers submitted yet.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($purchaseRequest->offers->count() > 0)
                            <div class="mt-6">
                                <x-input-label for="recommendation_reason" :value="__('Reason for Selection / Opinion')" />
                                <div class="mt-1">
                                    <textarea id="recommendation_reason" name="recommendation_reason" rows="3" class="shadow-sm block w-full focus:ring-brand-green focus:border-brand-green sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="{{ __('Why is this the best offer? (Price, Quality, Delivery Time, etc.)') }}" required></textarea>
                                </div>
                                <x-input-error :messages="$errors->get('recommendation_reason')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    {{ __('Submit Recommendation') }}
                                </x-primary-button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
