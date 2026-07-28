<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'ku']) ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - @endif{{ $siteSettings['company_name'] ?? config('app.name', 'Click P2P System') }}</title>

        <!-- Favicon (uses company logo if set) -->
        @php
            $faviconUrl = !empty($siteSettings['company_logo']) 
                ? asset('storage/' . $siteSettings['company_logo']) 
                : asset('logo.png');
        @endphp
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
        <link rel="shortcut icon" type="image/png" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <!-- Arabic/Kurdish Font - Tajawal (Modern, Bold) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <script>
            // Anti-flicker script: Apply dark mode immediately before page renders
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --brand-primary: {{ $siteSettings['primary_color'] ?? '#65C34A' }};
                --brand-secondary: {{ $siteSettings['secondary_color'] ?? '#1F6BFF' }};
            }
            /* RTL Font Family - Tajawal for better readability */
            html[dir="rtl"] {
                font-family: 'Tajawal', 'Inter', sans-serif;
            }
            html[dir="ltr"] {
                font-family: 'Inter', sans-serif;
            }
            [x-cloak] { display: none !important; }
            /* Dynamic brand color overrides */
            .bg-brand-green { background-color: var(--brand-primary) !important; }
            .text-brand-green { color: var(--brand-primary) !important; }
            .border-brand-green { border-color: var(--brand-primary) !important; }
            .ring-brand-green { --tw-ring-color: var(--brand-primary) !important; }
            .focus\:ring-brand-green:focus { --tw-ring-color: var(--brand-primary) !important; }
            .focus\:border-brand-green:focus { border-color: var(--brand-primary) !important; }
            .hover\:bg-brand-green:hover { background-color: var(--brand-primary) !important; }
            /* Force sidebar to be sticky */
            /* Force sidebar to be sticky */
            .sidebar-fixed {
                position: sticky !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                z-index: 9999 !important;
                width: 288px !important; /* w-72 equivalent */
                float: left !important;
            }
            
            [dir="rtl"] .sidebar-fixed {
                left: auto !important;
                right: 0 !important;
                float: right !important;
            }
            
            @media (max-width: 1023px) {
                .sidebar-fixed {
                    display: none !important;
                }
            }

            /* Autofill fix for dark mode - Injected directly to bypass build */
            .dark input:-webkit-autofill,
            .dark input:-webkit-autofill:hover, 
            .dark input:-webkit-autofill:focus, 
            .dark input:-webkit-autofill:active {
                -webkit-box-shadow: 0 0 0 30px #111827 inset !important;
                -webkit-text-fill-color: white !important;
                caret-color: white !important;
                transition: background-color 5000s ease-in-out 0s;
            }
        </style>
    </head>
    <body class="font-sans antialiased" 
        x-data="{ 
            sidebarOpen: false,
            darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('darkMode', this.darkMode);
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        }"
        x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')); if(darkMode) document.documentElement.classList.add('dark'); $watch('sidebarOpen', val => { if(val) { document.body.style.overflow='hidden'; document.body.style.height='100%'; document.documentElement.style.overflow='hidden'; document.documentElement.style.height='100%'; } else { document.body.style.overflow=''; document.body.style.height=''; document.documentElement.style.overflow=''; document.documentElement.style.height=''; } });"
    >
        <!-- Fixed Sidebar -->
        @include('layouts.navigation')

        
        <div class="min-h-screen bg-light-gray dark:bg-gray-900 lg:{{ in_array(app()->getLocale(), ['ar', 'ku']) ? 'pr-72' : 'pl-72' }} transition-colors duration-200">
            <div class="flex flex-col flex-1">
                
                <header class="sticky top-0 z-10 bg-white dark:bg-gray-800 rounded-b-2xl shadow-sm transition-colors duration-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            
                            <div class="flex items-center">
                                <button type="button" class="text-gray-500 dark:text-gray-400 focus:outline-none lg:hidden" @click.stop="sidebarOpen = true">
                                    <span class="sr-only">Open sidebar</span>
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                </button>
                                
                                <div class="hidden lg:flex items-center">
                                    @if (isset($header))
                                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                            {{ $header }}
                                        </h2>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center ms-6">
                                
                                <!-- Language Switcher -->
                                <div class="relative me-2" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" class="p-2 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-green transition-colors duration-200">
                                        <span class="uppercase font-semibold text-sm">{{ app()->getLocale() }}</span>
                                        <i class="fas fa-chevron-down text-xs ms-1"></i>
                                    </button>
                                    <div x-show="open" x-cloak x-transition class="absolute right-0 rtl:left-0 rtl:right-auto mt-2 w-32 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                                        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-left">English</a>
                                        <a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-right">العربية</a>
                                        <a href="{{ route('lang.switch', 'ku') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-right">کوردی</a>
                                    </div>
                                </div>

                                <!-- Dark Mode Toggle -->
                                <button @click="toggleTheme()" class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-green me-2 transition-colors duration-200">
                                    <!-- Sun Icon -->
                                    <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <!-- Moon Icon -->
                                    <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </button>

                                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" class="p-2 relative rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-green">
                                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                        @if($unreadNotificationCount > 0)
                                        <span class="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-brand-green ring-2 ring-white dark:ring-gray-800"></span>
                                        @endif
                                    </button>
                                    
                                    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-72 sm:w-80 md:w-96 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-20 border border-gray-200 dark:border-gray-700 max-w-[calc(100vw-2rem)]">
                                        <div class="py-3 px-4 flex justify-between items-center border-b dark:border-gray-700">
                                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Notifications') }}</h3>
                                            @if($unreadNotificationCount > 0)
                                                <span class="text-xs font-medium text-white bg-brand-green rounded-full px-2 py-0.5">{{ $unreadNotificationCount }} {{ __('New') }}</span>
                                            @endif
                                        </div>
                                        <div class="divide-y dark:divide-gray-700 max-h-80 sm:max-h-96 overflow-y-auto">
                                            @forelse($unreadNotifications as $notification)
                                                <a href="{{ route('notifications.read', $notification->id) }}" class="block p-3 sm:p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <p class="text-sm text-gray-800 dark:text-gray-300 leading-relaxed">{{ __($notification->data['message']) }}</p>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">{{ $notification->created_at->locale(app()->getLocale() == 'ku' ? 'ckb' : app()->getLocale())->diffForHumans() }}</span>
                                                </a>
                                            @empty
                                                <div class="p-6 text-center">
                                                    <div class="text-gray-400 dark:text-gray-500 mb-2">
                                                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12"></path>
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No unread notifications.') }}</p>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('You\'re all caught up!') }}</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="ms-3 relative">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="inline-flex items-center gap-3 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                <img class="h-8 w-8 rounded-full object-cover flex-shrink-0 ring-1 ring-gray-300 dark:ring-gray-600" src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=64&background=10b981&color=ffffff&bold=true&format=svg' }}" alt="{{ auth()->user()->name }}">
                                                <span class="hidden sm:block truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                                                <div class="hidden sm:block">
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                                </div>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link :href="route('profile.edit')">
                                                {{ __('Profile') }}
                                            </x-dropdown-link>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')"
                                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </x-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 shadow-sm lg:hidden rounded-bl-2xl">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            @if (isset($header))
                                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                    {{ $header }}
                                </h2>
                            @endif
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto bg-light-gray dark:bg-gray-900 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
            </div>
        @include('layouts.mobile-navigation')
    </body>
</html>
