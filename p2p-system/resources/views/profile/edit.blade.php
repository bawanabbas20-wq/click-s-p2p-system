<x-app-layout>
    <x-slot name="header">
        {{ __('Profile') }}
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        
        <!-- Profile Header Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Large Avatar -->
                <div class="relative group">
                    <img class="h-24 w-24 sm:h-28 sm:w-28 rounded-full object-cover ring-4 ring-brand-green/20" 
                         src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=128&background=10b981&color=ffffff&bold=true&format=svg' }}" 
                         alt="{{ auth()->user()->name }}">
                    <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer"
                         onclick="document.getElementById('avatar').click()">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="text-center sm:text-start flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ auth()->user()->email }}</p>
                    <div class="mt-3 flex flex-wrap justify-center sm:justify-start gap-2">
                        <!-- Role Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-brand-green/10 text-brand-green">
                            <svg class="w-3.5 h-3.5 me-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                        <!-- Account Age -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            <svg class="w-3.5 h-3.5 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ __('Member since') }} {{ auth()->user()->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Personal Information Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Security Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                @include('profile.partials.update-password-form')
            </div>
            
        </div>

        <!-- Danger Zone -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl border border-red-200 dark:border-red-900/50 overflow-hidden">
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-app-layout>
