<x-app-layout>
    <x-slot name="header">
        {{ __('Manager Final Approval for:') }} {{ $purchaseRequest->item_name }}
    </x-slot>

    <div class="mb-6">
        <!-- Request Details -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl mb-6">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Request Details</h3>
                <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->user->department ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Item Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchaseRequest->item_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Estimated Price</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                             @if($purchaseRequest->estimated_currency === 'USD')
                                ${{ number_format($purchaseRequest->estimated_price, 2) }}
                            @else
                                {{ number_format($purchaseRequest->estimated_price, 0) }} IQD
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        @php
            $procurementOffer = $purchaseRequest->offers->where('is_procurement_recommended', true)->first();
            $financeOffer = $purchaseRequest->offers->where('is_finance_recommended', true)->first();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Procurement Recommendation -->
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 p-4 shadow-sm rounded-r-lg">
                <h3 class="text-md font-bold text-indigo-800 dark:text-indigo-300">Procurement Recommendation</h3>
                @if($procurementOffer)
                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><strong>Vendor:</strong> {{ $procurementOffer->vendor_name }}</p>
                        <p><strong>Price:</strong> 
                            @if($procurementOffer->currency == 'USD') ${{ number_format($procurementOffer->price, 2) }} @else {{ number_format($procurementOffer->price, 0) }} IQD @endif
                        </p>
                        <p><strong>Reason:</strong> "{{ $procurementOffer->procurement_recommendation_reason ?? 'N/A' }}"</p>
                    </div>
                @else
                    <p class="mt-2 text-sm text-gray-500">No specific recommendation recorded.</p>
                @endif
            </div>

            <!-- Finance Recommendation -->
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 shadow-sm rounded-r-lg">
                <h3 class="text-md font-bold text-green-800 dark:text-green-300">Finance Recommendation</h3>
                @if($financeOffer)
                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><strong>Vendor:</strong> {{ $financeOffer->vendor_name }}</p>
                        <p><strong>Price:</strong> 
                            @if($financeOffer->currency == 'USD') ${{ number_format($financeOffer->price, 2) }} @else {{ number_format($financeOffer->price, 0) }} IQD @endif
                        </p>
                        <p><strong>Reason:</strong> "{{ $financeOffer->finance_recommendation_reason ?? 'N/A' }}"</p>
                    </div>
                @else
                    <p class="mt-2 text-sm text-gray-500">Finance did not select a specific offer (Budget Check Only?).</p>
                @endif
            </div>
        </div>

        <!-- Manager Decision Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Final Decision</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Please review all offers below and select the final vendor to approve for purchase.
                </p>

                <form action="{{ route('offers.managerApprove', $purchaseRequest) }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg mb-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Select</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Recommendations</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($purchaseRequest->offers as $offer)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="document.getElementById('manager_offer_{{ $offer->id }}').checked = true">
                                        <td class="px-6 py-4 text-center">
                                            <!-- Default selection: Finance's choice, then Procurement's choice -->
                                            <input type="radio" id="manager_offer_{{ $offer->id }}" name="manager_selected_offer_id" value="{{ $offer->id }}" 
                                                {{ $offer->is_finance_recommended ? 'checked' : ($offer->is_procurement_recommended && !$financeOffer ? 'checked' : '') }}
                                                class="focus:ring-brand-green h-4 w-4 text-brand-green border-gray-300" required>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $offer->vendor_name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            @if($offer->currency == 'USD') ${{ number_format($offer->price, 2) }} @else {{ number_format($offer->price, 0) }} IQD @endif
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm space-x-2">
                                            @if($offer->is_procurement_recommended)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    Procurement
                                                </span>
                                            @endif
                                            @if($offer->is_finance_recommended)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Finance
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="manager_reason" :value="__('Manager Approval Note (Optional)')" />
                        <textarea id="manager_reason" name="manager_reason" rows="3" class="mt-1 shadow-sm block w-full focus:ring-brand-green focus:border-brand-green sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="Any final notes or instructions..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button class="ml-4 bg-brand-green hover:bg-brand-green/90">
                            {{ __('Final Approve & Purchase') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
