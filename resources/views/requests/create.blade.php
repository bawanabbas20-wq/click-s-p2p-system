<x-app-layout>
    <x-slot name="header">
        {{ __('Create New Purchase Request') }}
    </x-slot>

    <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-2xl max-w-2xl mx-auto" x-data="{ priority: '{{ old('priority', 'medium') }}' }">
        <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">
            <x-input-error :messages="$errors->all()" class="mb-4" />

            <form method="POST" action="{{ route('requests.store') }}">
                @csrf
                <div>
                    <x-input-label for="item_name" :value="__('Item Name')" />
                    <x-text-input id="item_name" class="block mt-1 w-full" type="text" name="item_name" :value="old('item_name')" required autofocus />
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <x-input-label for="estimated_price" :value="__('Estimated Price')" />
                        <x-text-input id="estimated_price" class="block mt-1 w-full" type="number" name="estimated_price" :value="old('estimated_price')" required />
                    </div>
                    <div>
                        <x-input-label for="estimated_currency" :value="__('Currency')" />
                        <select name="estimated_currency" id="estimated_currency" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="IQD" {{ old('estimated_currency', $siteSettings['default_currency'] ?? 'USD') == 'IQD' ? 'selected' : '' }}>IQD</option>
                            <option value="USD" {{ old('estimated_currency', $siteSettings['default_currency'] ?? 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4" x-data="{ priority: '{{ old('priority', 'medium') }}' }">
                    <x-input-label :value="__('Priority Level')" />
                    
                    <input type="hidden" name="priority" x-model="priority">
                    
                    <div class="mt-2 flex w-full rounded-full border border-gray-300 overflow-hidden">
                        <button type="button" @click="priority = 'low'" 
                                class="flex-1 p-3 text-sm font-semibold text-white bg-green-500 focus:outline-none transition-opacity"
                                :class="priority !== 'low' && 'opacity-40 hover:opacity-100'">
                            {{ __('Low') }}
                        </button>

                        <button type="button" @click="priority = 'medium'" 
                                class="flex-1 p-3 text-sm font-semibold text-white bg-yellow-500 focus:outline-none transition-opacity border-l border-r rtl:border-l-0 rtl:border-r-0 border-gray-300"
                                :class="priority !== 'medium' && 'opacity-40 hover:opacity-100'">
                            {{ __('Medium') }}
                        </button>

                        <button type="button" @click="priority = 'high'" 
                                class="flex-1 p-3 text-sm font-semibold text-white bg-red-600 focus:outline-none transition-opacity"
                                :class="priority !== 'high' && 'opacity-40 hover:opacity-100'">
                            {{ __('High') }}
                        </button>
                    </div>

                    <div class="mt-2 text-center text-sm text-gray-500">
                        <span x-show="priority === 'low'">
                            {{ __('Standard processing time.') }}
                        </span>
                        <span x-show="priority === 'medium'">
                            {{ __('Moderate urgency.') }}
                        </span>
                        <span x-show="priority === 'high'">
                            {{ __('Urgent request, requires immediate attention.') }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <x-input-label for="date_wanted" :value="__('Date Wanted By')" />
                    <x-text-input id="date_wanted" class="block mt-1 w-full" type="date" name="date_wanted" :value="old('date_wanted')" required />
                </div>
                <div class="mt-4">
                    <x-input-label for="justification" :value="__('Justification (Why do you need this?)')" />
                    <textarea id="justification" name="justification" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="{{ __('Please describe why you need this item...') }}">{{ old('justification') }}</textarea>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('requests.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4 rtl:mr-0 rtl:ml-4">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button>
                        {{ __('Submit Request') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
