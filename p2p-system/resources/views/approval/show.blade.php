<x-app-layout>
    <x-slot name="header">
        {{ __('Review Request:') }} {{ $purchaseRequest->item_name }}
    </x-slot>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg max-w-7xl mx-auto">
            {{ session('error') }}
        </div>
    @endif

    {{-- Conflict of Interest Warning --}}
    @if($purchaseRequest->user && $purchaseRequest->user->role === 'procurement')
        <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded-lg max-w-7xl mx-auto flex items-start shadow-sm">
            <svg class="h-5 w-5 mr-3 mt-0.5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h3 class="text-sm font-bold">{{ __('Conflict of Interest Notice') }}</h3>
                <p class="text-sm mt-1">{{ __('This request was initiated by a Procurement staff member. Please scrutinize the selected quotation to ensure fair vendor selection.') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Request Information') }}</h3>
                    <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Requested By') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->user->name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ __($purchaseRequest->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Priority') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($purchaseRequest->priority == 'high') bg-red-100 text-red-800
                                    @elseif($purchaseRequest->priority == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ __(ucfirst($purchaseRequest->priority)) }}
                                </span>
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
                    </dl>
                </div>
            </div>
        </div>

        <div class="md:col-span-1 space-y-6">

            @if ($budgetData)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300">{{ __('Budget Overview (This Month)') }}</h3>
                <div class="mt-4 space-y-2">
                    <div>
                        <div class="flex justify-between text-sm font-medium text-blue-800 dark:text-blue-200">
                            <span>{{ __('IQD Budget') }}</span>
                            <span>{{ number_format($budgetData['budget_iqd'], 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-blue-700 dark:text-blue-300">
                            <span>{{ __('Spent') }}</span>
                            <span>- {{ number_format($budgetData['spent_iqd'], 0) }}</span>
                        </div>
                        <hr class="my-1 border-blue-200 dark:border-blue-700">
                        <div class="flex justify-between text-sm font-semibold text-blue-800 dark:text-blue-200">
                            <span>{{ __('Remaining') }}</span>
                            <span>{{ number_format($budgetData['remaining_iqd'], 0) }}</span>
                        </div>
                        
                        @if($budgetData['this_request_currency'] === 'IQD')
                        <div class="flex justify-between text-sm text-blue-700 dark:text-blue-300 border-t border-dashed border-blue-300 dark:border-blue-600 pt-1 mt-1">
                            <span>{{ __('This Request') }}</span>
                            <span>- {{ number_format($budgetData['this_request_cost'], 0) }} IQD</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-blue-900 dark:text-blue-100">
                            <span>{{ __('Remaining if Approved') }}</span>
                            <span>{{ number_format($budgetData['remaining_if_approved'], 0) }} IQD</span>
                        </div>
                        @endif
                    </div>
                    <div class="pt-2 border-t border-blue-200 dark:border-blue-700">
                        <div class="flex justify-between text-sm font-medium text-blue-800 dark:text-blue-200">
                            <span>{{ __('USD Budget') }}</span>
                            <span>${{ number_format($budgetData['budget_usd'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-blue-700 dark:text-blue-300">
                            <span>{{ __('Spent') }}</span>
                            <span>- ${{ number_format($budgetData['spent_usd'], 2) }}</span>
                        </div>
                        <hr class="my-1 border-blue-200 dark:border-blue-700">
                        <div class="flex justify-between text-sm font-semibold text-blue-800 dark:text-blue-200">
                            <span>{{ __('Remaining') }}</span>
                            <span>${{ number_format($budgetData['remaining_usd'], 2) }}</span>
                        </div>
                        
                        @if($budgetData['this_request_currency'] === 'USD')
                        <div class="flex justify-between text-sm text-blue-700 dark:text-blue-300 border-t border-dashed border-blue-300 dark:border-blue-600 pt-1 mt-1">
                            <span>{{ __('This Request') }}</span>
                            <span>- ${{ number_format($budgetData['this_request_cost'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-blue-900 dark:text-blue-100">
                            <span>{{ __('Remaining if Approved') }}</span>
                            <span>${{ number_format($budgetData['remaining_if_approved'], 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                <!-- Header with icon -->
                <div class="flex items-center gap-2 mb-5">
                    <div class="p-2 bg-brand-green/10 rounded-lg">
                        <svg class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Take Action') }}</h3>
                </div>
                
                <form method="POST" action="{{ route('approval.process', $purchaseRequest) }}" class="space-y-5">
                    @csrf
                    
                    @if(auth()->user()->role !== 'procurement')
                    <div>
                        <x-input-label for="comment" :value="__('Comment (Required if denying/rejecting)')" class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" />
                        <textarea id="comment" name="comment" rows="3" 
                            placeholder="{{ __('Add your comments here...') }}"
                            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500"></textarea>
                    </div>
                    @endif

                    {{-- Logic for High Value & Recommendations --}}
                    @php
                        $procurementOffer = $purchaseRequest->offers->where('is_procurement_recommended', true)->first();
                        $financeOffer = $purchaseRequest->offers->where('is_finance_recommended', true)->first();
                        
                        // Determine Threshold Value based on Procurement's choice
                        $thresholdValue = 0;
                        if ($procurementOffer) {
                            $thresholdValue = $procurementOffer->price;
                            if ($procurementOffer->currency === 'USD') {
                                 $exchangeRate = \App\Models\Setting::where('key', 'exchange_rate_usd_to_iqd')->value('value') ?? 1450;
                                 $thresholdValue = $thresholdValue * $exchangeRate;
                            }
                        }
                        $isHighValue = $thresholdValue >= 100000;
                    @endphp

                    <div class="flex flex-col gap-2">
                        {{-- Procurement Actions --}}
                        @if($purchaseRequest->status === 'Pending Procurement' && auth()->user()->role === 'procurement')
                            <div class="flex gap-2">
                                <button type="submit" name="action" value="escalate" class="flex-1 justify-center px-4 py-2 bg-brand-blue text-white text-sm font-medium rounded-lg hover:bg-opacity-80">{{ __('Request Quotations') }}</button>
                                <button type="submit" name="action" value="fulfill_stock" class="flex-1 justify-center px-4 py-2 bg-brand-green text-white text-sm font-medium rounded-lg hover:bg-opacity-80">{{ __('Fulfill from Stock') }}</button>
                            </div>
                        @endif

                        {{-- Procurement Recommendation Display (For Finance/Manager) --}}
                        @if($procurementOffer && in_array(auth()->user()->role, ['finance', 'manager', 'admin']))
                            <div class="mb-2 bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 p-3">
                                <h4 class="font-bold text-indigo-800 dark:text-indigo-300 text-sm">{{ __('Procurement Recommended:') }}</h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $procurementOffer->vendor_name }} - {{ number_format($procurementOffer->price, 2) }} {{ $procurementOffer->currency }}</p>
                                @if($procurementOffer->procurement_recommendation_reason)
                                    <p class="text-xs italic text-gray-600 dark:text-gray-400 mt-1">"{{ $procurementOffer->procurement_recommendation_reason }}"</p>
                                @endif
                            </div>
                        @endif

                        {{-- Finance Recommendation Display (For Manager) --}}
                        @if($financeOffer && in_array(auth()->user()->role, ['manager', 'admin']))
                             <div class="mb-2 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-3">
                                <h4 class="font-bold text-green-800 dark:text-green-300 text-sm">{{ __('Finance Recommended:') }}</h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $financeOffer->vendor_name }} - {{ number_format($financeOffer->price, 2) }} {{ $financeOffer->currency }}</p>
                                @if($financeOffer->finance_recommendation_reason)
                                    <p class="text-xs italic text-gray-600 dark:text-gray-400 mt-1">"{{ $financeOffer->finance_recommendation_reason }}"</p>
                                @endif
                            </div>
                        @endif

                        {{-- Finance Actions --}}
                        @if($purchaseRequest->status === 'Pending Finance' && (auth()->user()->role === 'finance' || auth()->user()->role === 'admin'))
                             @if($isHighValue)
                                <div class="mb-4 border border-red-200 bg-red-50 dark:bg-red-900/20 p-3 rounded">
                                    <p class="text-sm text-red-700 dark:text-red-300 font-bold mb-2">{{ __('High Value (>= 100k). Review offers & Escalate:') }}</p>
                                    <div class="mt-2 space-y-2 max-h-64 overflow-y-auto">
                                        @foreach($purchaseRequest->offers as $offer)
                                            <label class="flex items-center space-x-3 p-2 bg-white dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <input type="radio" name="finance_selected_offer_id" value="{{ $offer->id }}" {{ $offer->is_procurement_recommended ? 'checked' : '' }} class="form-radio h-4 w-4 text-brand-green border-gray-300 focus:ring-brand-green">
                                                <div class="text-sm flex-1">
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $offer->vendor_name }}</div>
                                                    <div class="text-gray-500 dark:text-gray-400">{{ number_format($offer->price, 2) }} {{ $offer->currency }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <textarea name="finance_reason" placeholder="Reason for choice..." rows="2" class="w-full mt-3 text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-brand-green focus:border-brand-green"></textarea>
                                </div>
                                <button type="submit" name="action" value="finance_approve_high" class="w-full justify-center px-4 py-2 bg-brand-blue text-white text-sm font-medium rounded-lg hover:bg-opacity-80">{{ __('Escalate to Manager') }}</button>
                             @else
                                <!-- Low Value: Modern Cash Confirmation Toggle -->
                                <div class="mb-5 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-xl border border-green-200 dark:border-green-800">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-green-100 dark:bg-green-800/50 rounded-lg">
                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('Low Value Purchase') }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Confirm Cash Availability') }}</p>
                                            </div>
                                        </div>
                                        <!-- Modern Toggle Switch -->
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" required name="cash_confirmed" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-green/30 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-green"></div>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Primary Approve Button -->
                                <button type="submit" name="action" value="finance_approve_low" 
                                    class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-brand-green border border-transparent text-white text-sm font-semibold rounded-xl hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ __('Approve & Ready to Buy') }}
                                </button>
                             @endif

                             <!-- Ghost-style Reject Buttons -->
                             <div class="grid grid-cols-2 gap-3 mt-4">
                                <button type="submit" name="action" value="reject_quote" 
                                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border-2 border-amber-400 text-amber-600 dark:text-amber-400 text-sm font-semibold rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 focus:outline-none focus:ring-4 focus:ring-amber-400/30 transition-all duration-200" 
                                    onclick="return confirm('{{ __('Reject quote and send back to Procurement?') }}');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    {{ __('Reject Quote') }}
                                </button>
                                <button type="submit" name="action" value="deny" 
                                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border-2 border-red-400 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-4 focus:ring-red-400/30 transition-all duration-200" 
                                    onclick="return confirm('{{ __('Cancel entire request?') }}');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    {{ __('Reject Request') }}
                                </button>
                             </div>

                        {{-- Manager Actions --}}
                        @elseif(in_array($purchaseRequest->status, ['Pending Manager', 'Pending Manager Approval']) && (auth()->user()->role === 'manager' || auth()->user()->role === 'admin'))
                             <!-- Modern Offer Selection Panel -->
                             <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ __('Select Final Offer:') }}</p>
                                <div class="space-y-2 max-h-64 overflow-y-auto">
                                    @foreach($purchaseRequest->offers as $offer)
                                        <label class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 cursor-pointer hover:border-brand-green hover:bg-green-50/50 dark:hover:bg-green-900/20 transition-all duration-200">
                                            <input type="radio" name="manager_selected_offer_id" value="{{ $offer->id }}" {{ $offer->is_finance_recommended ? 'checked' : ($offer->is_procurement_recommended && !$financeOffer ? 'checked' : '') }} class="form-radio h-4 w-4 text-brand-green border-gray-300 focus:ring-brand-green">
                                            <div class="text-sm flex-1 grid grid-cols-1 sm:grid-cols-2 gap-1">
                                                <div>
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $offer->vendor_name }}</div>
                                                    <div class="text-gray-500 dark:text-gray-400">{{ number_format($offer->price, 2) }} {{ $offer->currency }}</div>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    @if($offer->is_procurement_recommended) <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full">Procurement</span> @endif
                                                    @if($offer->is_finance_recommended) <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Finance</span> @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <textarea name="manager_reason" placeholder="{{ __('Final approval notes...') }}" rows="2" 
                                    class="w-full mt-4 text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition-all duration-200 placeholder-gray-400"></textarea>
                             </div>
                             
                             <!-- Primary Final Approve Button -->
                             <button type="submit" name="action" value="manager_approve" 
                                class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-brand-green border border-transparent text-white text-sm font-semibold rounded-xl hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('Final Approve') }}
                             </button>
                             
                             <!-- Ghost-style Reject Buttons -->
                             <div class="grid grid-cols-2 gap-3 mt-4">
                                <button type="submit" name="action" value="reject_quote" 
                                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border-2 border-amber-400 text-amber-600 dark:text-amber-400 text-sm font-semibold rounded-xl hover:bg-amber-50 dark:hover:bg-amber-900/20 focus:outline-none focus:ring-4 focus:ring-amber-400/30 transition-all duration-200" 
                                    onclick="return confirm('{{ __('Reject quote?') }}');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    {{ __('Reject Quote') }}
                                </button>
                                <button type="submit" name="action" value="deny" 
                                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border-2 border-red-400 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-4 focus:ring-red-400/30 transition-all duration-200" 
                                    onclick="return confirm('{{ __('Cancel request?') }}');">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    {{ __('Reject Request') }}
                                </button>
                             </div>

                        {{-- Fallback / Stock / Ready to Buy / Admin Override --}}
                        @else
                            {{-- View Selected (Read Only) or Cash Ready --}}
                             @if($purchaseRequest->status === 'Pending Final Payment' || $purchaseRequest->status === 'Pending Final Approval' || $purchaseRequest->status === 'Ready to Buy')
                                 <div class="w-full mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Selected Quotation') }}</h4>
                                    @if($purchaseRequest->chosenOffer)
                                        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Vendor:</strong> {{ $purchaseRequest->chosenOffer->vendor_name }}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Price:</strong> {{ number_format($purchaseRequest->chosenOffer->price, 2) }} {{ $purchaseRequest->chosenOffer->currency }}</p>
                                    @else
                                        <p class="text-sm text-red-500">{{ __('No offer selected.') }}</p>
                                    @endif
                                </div>
                                
                                {{-- Cash Ready Action --}}
                                @if($purchaseRequest->status === 'Pending Final Payment' || $purchaseRequest->status === 'Pending Final Approval')
                                    <button type="submit" name="action" value="cash_ready" class="w-full justify-center px-4 py-2 bg-brand-green text-white text-sm font-medium rounded-lg hover:bg-opacity-80">
                                        {{ __('Cash is Ready - Notify Procurement') }}
                                    </button>
                                @endif
                             @endif

                            {{-- Admin Override --}}
                            @if(auth()->user()->role === 'admin' && !in_array($purchaseRequest->status, ['Pending Finance', 'Pending Manager', 'Pending Manager Approval']))
                                 <div class="mt-4 border-t pt-4">
                                    <p class="text-xs text-gray-500 mb-1">{{ __('Admin Generic Override:') }}</p>
                                    <button type="submit" name="action" value="approve" class="w-full justify-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">{{ __('Force Approve') }}</button>
                                 </div>
                            @endif
                        @endif

                        {{-- Offers Link (View Only) --}}
                         @if($purchaseRequest->offers->count() > 1 && !in_array($purchaseRequest->status, ['Pending Finance', 'Pending Manager', 'Pending Manager Approval']))
                                <div x-data="{ showOffers: false }" class="mt-2 mb-4">
                                    <button type="button" @click="showOffers = !showOffers" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1 font-medium focus:outline-none">
                                        <span>{{ __('View all :count received offers', ['count' => $purchaseRequest->offers->count()]) }}</span>
                                    </button>
                                    <div x-show="showOffers" class="mt-2 space-y-2 pl-2 border-l-2 border-gray-200 dark:border-gray-600">
                                        @foreach($purchaseRequest->offers as $offer)
                                            <div class="text-xs flex justify-between items-center p-2 rounded {{ $offer->is_chosen ? 'bg-green-50' : 'bg-white' }}">
                                                <span>{{ $offer->vendor_name }} ({{ number_format($offer->price, 2) }} {{ $offer->currency }})</span>
                                                @if($offer->is_chosen) <span class="text-green-600 font-bold">(Selected)</span> @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                        @endif
                    </div>
                </form>
            </div>
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
                                    <p class="text-xs text-gray-400 mt-1.5">{{ $log->created_at->format('F j, Y \\a\\t g:i a') }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500">{{ __('No history for this request yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            </div>
    </div>
</x-app-layout>
