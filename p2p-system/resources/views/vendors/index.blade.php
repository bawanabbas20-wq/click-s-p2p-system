
@section('title', 'Vendor Management')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vendor Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Search and Actions -->
                    <div class="flex justify-between items-center gap-4 mb-6">
                        <form method="GET" action="{{ route('vendors.index') }}" class="flex-1 max-w-lg">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('...Search vendors') }}" class="w-full ps-10 pe-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-green focus:border-transparent">
                                <div class="absolute inset-y-0 start-0 ps-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>
                        <a href="{{ route('vendors.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-green border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Add New Vendor') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @endif

                    @if($vendors->isEmpty())
                        <x-empty-state 
                            title="{{ __('No Vendors Found') }}" 
                            message="{{ __('No vendors match your search criteria or none have been added yet.') }}" 
                        />
                    @else
                        <!-- Desktop View -->
                        <div class="hidden md:block overflow-x-auto min-h-[500px]">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Vendor Name') }}</th>
                                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Contact Person') }}</th>
                                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Email / Phone') }}</th>
                                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Rating') }}</th>
                                        <th class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($vendors as $vendor)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $vendor->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $vendor->contact_person }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div class="flex flex-col space-y-1">
                                                    <a href="mailto:{{ $vendor->email }}" class="text-indigo-600 dark:text-indigo-400 hover:underline hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors duration-200 flex items-center">
                                                        <svg class="w-3 h-3 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                        {{ $vendor->email }}
                                                    </a>
                                                    <a href="tel:{{ $vendor->phone }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:underline transition-colors duration-200 flex items-center text-xs">
                                                        <svg class="w-3 h-3 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                        {{ $vendor->phone }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-500">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @php $percent = max(0, min(100, ($vendor->rating - ($i - 1)) * 100)); @endphp
                                                    <div class="relative inline-block text-gray-300 dark:text-gray-600">
                                                        <i class="far fa-star"></i>
                                                        <div class="absolute top-0 start-0 overflow-hidden text-yellow-500" style="width: {{ $percent }}%">
                                                            <i class="fas fa-star"></i>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                <a href="{{ route('vendors.edit', $vendor) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 me-3">{{ __('Edit') }}</a>
                                                <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-4">
                            @foreach($vendors as $vendor)
                                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-bold text-gray-900 dark:text-white">{{ $vendor->name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ $vendor->contact_person }}</p>
                                        </div>
                                        <div class="text-yellow-500 text-xs">
                                            @for($i = 1; $i <= 5; $i++)
                                                @php $percent = max(0, min(100, ($vendor->rating - ($i - 1)) * 100)); @endphp
                                                <div class="relative inline-block text-gray-300 dark:text-gray-600">
                                                    <i class="far fa-star"></i>
                                                    <div class="absolute top-0 start-0 overflow-hidden text-yellow-500" style="width: {{ $percent }}%">
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-3 space-y-2">
                                        <a href="mailto:{{ $vendor->email }}" class="flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ $vendor->email }}
                                        </a>
                                        <a href="tel:{{ $vendor->phone }}" class="flex items-center hover:text-gray-900 dark:hover:text-white">
                                            <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            {{ $vendor->phone }}
                                        </a>
                                    </div>
                                    <div class="flex justify-end space-x-3 pt-3 border-t border-gray-100 dark:border-gray-600">
                                        <a href="{{ route('vendors.edit', $vendor) }}" class="text-indigo-600 dark:text-indigo-400 text-sm font-medium">Edit</a>
                                        <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 text-sm font-medium">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $vendors->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
