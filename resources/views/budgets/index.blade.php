<x-app-layout>
    <x-slot name="header">
        {{ __('Budget Management for') }} {{ $year }}
    </x-slot>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Exchange Rate Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-brand-green/10 to-transparent dark:from-brand-green/20 p-6">
            <form method="POST" action="{{ route('budgets.store') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                @foreach ($months as $month)
                    @php $budget = $budgets->get($month); @endphp
                    <input type="hidden" name="iqd[{{ $month }}]" value="{{ $budget->budget_amount_iqd ?? 0 }}">
                    <input type="hidden" name="usd[{{ $month }}]" value="{{ $budget->budget_amount_usd ?? 0 }}">
                @endforeach

                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-brand-green/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Exchange Rate') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('USD to IQD conversion rate') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <span class="absolute start-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm font-medium">1 USD =</span>
                        <input type="number" 
                               step="0.01" 
                               name="exchange_rate_usd_to_iqd" 
                               value="{{ old('exchange_rate_usd_to_iqd', $exchangeRate->value ?? 1450) }}"
                               class="w-48 ps-20 pe-12 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-brand-green focus:ring focus:ring-brand-green/20 text-lg font-semibold"
                               required>
                        <span class="absolute end-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">IQD</span>
                    </div>
                    <button type="submit" 
                            class="px-6 py-3 bg-brand-green text-white font-semibold rounded-xl hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Budgets Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="{{ route('budgets.store') }}" x-data="{ 
            quickFillIqd: '', 
            quickFillUsd: '',
            applyToAll() {
                if (this.quickFillIqd !== '') {
                    document.querySelectorAll('input[name^=\'iqd[\']').forEach(el => el.value = this.quickFillIqd);
                }
                if (this.quickFillUsd !== '') {
                    document.querySelectorAll('input[name^=\'usd[\']').forEach(el => el.value = this.quickFillUsd);
                }
            }
        }">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            
            <!-- Section Header -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Monthly Budgets') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Set budget limits for each month') }}</p>
                        </div>
                    </div>
                    
                    <!-- Quick Fill Feature -->
                    <div class="flex flex-wrap items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ __('Quick Fill:') }}</span>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <span class="absolute start-2 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">IQD</span>
                                <input type="number" 
                                       x-model="quickFillIqd"
                                       placeholder="0"
                                       class="w-28 ps-10 pe-2 py-2 text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-brand-green focus:ring focus:ring-brand-green/20">
                            </div>
                            <div class="relative">
                                <span class="absolute start-2 top-1/2 -translate-y-1/2 text-xs font-semibold text-green-600 dark:text-green-400">USD</span>
                                <input type="number" 
                                       x-model="quickFillUsd"
                                       placeholder="0"
                                       class="w-28 ps-10 pe-2 py-2 text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-brand-green focus:ring focus:ring-brand-green/20">
                            </div>
                            <button type="button" 
                                    @click="applyToAll()"
                                    class="px-4 py-2 text-sm font-medium text-brand-green bg-brand-green/10 hover:bg-brand-green/20 rounded-lg transition-colors duration-200">
                                {{ __('Apply to All') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Month Cards Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($months as $month)
                        @php
                            $budget = $budgets->get($month);
                            $monthName = \Carbon\Carbon::create()->month($month)->format('F');
                            $isCurrentMonth = $month == now()->month;
                        @endphp
                        <div class="group relative bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-700
                            {{ $isCurrentMonth ? 'ring-1 ring-brand-green/50' : '' }}">
                            
                            <!-- Month Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-white dark:bg-gray-600 shadow-sm flex items-center justify-center text-sm font-bold text-gray-700 dark:text-gray-200">
                                        {{ $month }}
                                    </span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ __($monthName) }}</span>
                                </div>
                                @if($isCurrentMonth)
                                    <span class="px-2 py-0.5 text-xs font-medium text-brand-green bg-brand-green/10 rounded-full">
                                        {{ __('Current') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Budget Inputs -->
                            <div class="space-y-3">
                                <!-- IQD Input -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                        {{ __('IQD Budget') }}
                                    </label>
                                    <div class="relative">
                                        <span class="absolute start-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-400">IQD</span>
                                        <input type="number" 
                                               step="1000" 
                                               name="iqd[{{ $month }}]" 
                                               value="{{ old('iqd.'.$month, $budget->budget_amount_iqd ?? 0) }}"
                                               class="w-full ps-12 pe-3 py-2.5 text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-brand-green focus:ring focus:ring-brand-green/20 transition-all">
                                    </div>
                                </div>

                                <!-- USD Input -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                        {{ __('USD Budget') }}
                                    </label>
                                    <div class="relative">
                                        <span class="absolute start-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-green-600 dark:text-green-400">USD</span>
                                        <input type="number" 
                                               step="100" 
                                               name="usd[{{ $month }}]" 
                                               value="{{ old('usd.'.$month, $budget->budget_amount_usd ?? 0) }}"
                                               class="w-full ps-12 pe-3 py-2.5 text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-brand-green focus:ring focus:ring-brand-green/20 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sticky Save Button -->
            <div class="sticky bottom-0 p-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 inline me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('Enter 0 for months with no budget limit') }}
                    </p>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-brand-green text-white font-bold rounded-xl hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        {{ __('Save All Budgets') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
