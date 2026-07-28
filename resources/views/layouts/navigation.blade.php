

<div class="sidebar-fixed hidden lg:flex lg:flex-shrink-0">
    <div class="h-screen p-4">
        <div class="h-full bg-white dark:bg-gray-800 rounded-tl-2xl rounded-tr-2xl rounded-bl-2xl shadow-lg flex flex-col p-6 w-64 transition-colors duration-200">
            <div class="flex items-center justify-center h-16 mb-6">
                <a href="{{ route('dashboard') }}">
                    @if(!empty($siteSettings['company_logo']))
                        <img src="{{ asset('storage/' . $siteSettings['company_logo']) }}" alt="{{ $siteSettings['company_name'] ?? 'Logo' }}" class="w-24 h-auto" />
                    @else
                        <img src="/logo.png" alt="{{ $siteSettings['company_name'] ?? 'Click' }}" class="w-24 h-auto" />
                    @endif
                </a>
            </div>
            <div class="flex-1 space-y-2 overflow-y-auto">
                <x-nav-link-vertical :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('requests.*')">
                    {{ __('My Dashboard') }}
                </x-nav-link-vertical>
                
                @can('is-approver')
                <x-nav-link-vertical :href="route('approval.queue')" :active="request()->routeIs('approval.queue')">
                    <span class="flex-1">{{ __('Approval Queue') }}</span>
                    @if($queueCounts['approval'] > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-white bg-brand-green rounded-full">
                            {{ $queueCounts['approval'] }}
                        </span>
                    @endif
                </x-nav-link-vertical>
                @endcan
                
                @if(auth()->user()->can('is-procurement') || auth()->user()->can('is-admin'))
                <x-nav-link-vertical :href="route('offers.index')" :active="request()->routeIs('offers.index') || request()->routeIs('offers.create')">
                    <span class="flex-1">{{ __('Needs Quotations') }}</span>
                    @if(isset($queueCounts['needs_quotations']) && $queueCounts['needs_quotations'] > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-white bg-brand-green rounded-full">
                            {{ $queueCounts['needs_quotations'] }}
                        </span>
                    @endif
                </x-nav-link-vertical>

                <x-nav-link-vertical :href="route('offers.ready_to_buy')" :active="request()->routeIs('offers.ready_to_buy')">
                    <span class="flex-1">{{ __('Ready to Buy (Cash)') }}</span>
                    @if(isset($queueCounts['ready_to_buy']) && $queueCounts['ready_to_buy'] > 0)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 ms-2 text-xs font-medium text-white bg-brand-green rounded-full">
                            {{ $queueCounts['ready_to_buy'] }}
                        </span>
                    @endif
                </x-nav-link-vertical>
                @endif

                @can('can-manage-vendors')
                <x-nav-link-vertical :href="route('vendors.index')" :active="request()->routeIs('vendors.*')">
                    {{ __('Vendor Management') }}
                </x-nav-link-vertical>
                @endcan

                @can('can-manage-budgets')
                <x-nav-link-vertical :href="route('analytics.index')" :active="request()->routeIs('analytics.index')">
                    {{ __('Analytics') }}
                </x-nav-link-vertical>
                <x-nav-link-vertical :href="route('budgets.index')" :active="request()->routeIs('budgets.index')">
                    {{ __('Budget Management') }}
                </x-nav-link-vertical>
                @endcan

                @can('is-admin')
                <x-nav-link-vertical :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('User Management') }}
                </x-nav-link-vertical>
                <x-nav-link-vertical :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                    {{ __('Site Settings') }}
                </x-nav-link-vertical>
                @endcan
            </div>
        </div>
    </div>
</div>
