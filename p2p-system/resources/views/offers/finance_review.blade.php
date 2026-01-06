<x-app-layout>
    <x-slot name="header">
        {{ __('Finance Review for:') }} {{ $purchaseRequest->item_name }}
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
                </dl>
            </div>
        </div>

        <!-- Procurement Recommendation -->
        @php
            $chosenOffer = $purchaseRequest->offers->where('is_chosen', true)->first();
            $isHighValue = $chosenOffer && $chosenOffer->isHighValue();
        @endphp

        <div class="bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 p-4 shadow-sm rounded-r-lg mb-6">
            <h3 class="text-md font-bold text-indigo-800 dark:text-indigo-300">Procurement Recommendation</h3>
            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                <p><strong>Selected Vendor:</strong> {{ $chosenOffer->vendor_name ?? 'N/A' }}</p>
                <p><strong>Price:</strong> 
                    @if($chosenOffer)
                        @if($chosenOffer->currency == 'USD') ${{ number_format($chosenOffer->price, 2) }} @else {{ number_format($chosenOffer->price, 0) }} IQD @endif
                    @endif
                </p>
                <p><strong>Reason:</strong> "{{ $chosenOffer->procurement_recommendation_reason ?? 'No reason provided.' }}"</p>
            </div>
        </div>

        @if($isHighValue)
            <!-- High Value Logic: Show All Offers for Review -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">High Value Purchase (Review Required)</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        This purchase exceeds the threshold (100,000 IQD). Please review all offers, confirm the budget, and select your recommendation for the Manager.
                    </p>

                    <form action="{{ route('offers.financeSubmit', $purchaseRequest) }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Select</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Vendor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Procurement's Choice</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($purchaseRequest->offers as $offer)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="document.getElementById('finance_offer_{{ $offer->id }}').checked = true">
                                            <td class="px-6 py-4 text-center">
                                                <input type="radio" id="finance_offer_{{ $offer->id }}" name="finance_selected_offer_id" value="{{ $offer->id }}" 
                                                    {{ $offer->is_chosen ? 'checked' : '' }}
                                                    class="focus:ring-brand-green h-4 w-4 text-brand-green border-gray-300">
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $offer->vendor_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                @if($offer->currency == 'USD') ${{ number_format($offer->price, 2) }} @else {{ number_format($offer->price, 0) }} IQD @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if($offer->is_procurement_recommended)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Recommended
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="finance_reason" :value="__('Finance Opinion / Reason')" />
                            <textarea id="finance_reason" name="finance_reason" rows="3" class="mt-1 shadow-sm block w-full focus:ring-brand-green focus:border-brand-green sm:text-sm border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" required placeholder="Explain your choice or confirm budget availability..."></textarea>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button class="ml-4">
                                {{ __('Submit & Escalate to Manager') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- Low Value Logic: Simple Confirmation -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Low Value Purchase</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        This purchase is under the threshold. Please confirm availability of funds to proceed significantly.
                    </p>

                    <form action="{{ route('offers.financeSubmit', $purchaseRequest) }}" method="POST">
                        @csrf
                        <input type="hidden" name="finance_selected_offer_id" value="{{ $chosenOffer->id }}">
                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" required class="rounded border-gray-300 text-brand-green shadow-sm focus:border-brand-green focus:ring focus:ring-brand-green focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">I confirm that cash is ready for this purchase.</span>
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button class="ml-4">
                                {{ __('Confirm & Notify Procurement') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
