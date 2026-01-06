<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $siteSettings['company_name'] ?? 'Click P2P' }} - Too Many Requests</title>

        @php
            $logoUrl = !empty($siteSettings['company_logo']) 
                ? asset('storage/' . $siteSettings['company_logo']) 
                : asset('logo.png');
        @endphp

        <!-- Favicon (uses company logo if set) -->
        <link rel="icon" type="image/png" href="{{ $logoUrl }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --brand-primary: {{ $siteSettings['primary_color'] ?? '#65C34A' }};
                --brand-secondary: {{ $siteSettings['secondary_color'] ?? '#1F6BFF' }};
            }
            * { box-sizing: border-box; }
            body { margin: 0; padding: 0; }
            .error-container { 
                background: #f8fafc !important; 
                min-height: 100vh !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 2rem 1rem !important;
            }
            .error-card {
                background: white !important;
                border-radius: 1rem !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                padding: 2.5rem !important;
                width: 100% !important;
                max-width: 28rem !important;
                text-align: center !important;
            }
            .btn-primary {
                background: var(--brand-primary) !important;
                color: white !important;
                padding: 0.75rem 1.5rem !important;
                border-radius: 0.5rem !important;
                font-weight: 500 !important;
                text-decoration: none !important;
                display: inline-block !important;
                transition: filter 0.2s !important;
            }
            .btn-primary:hover {
                filter: brightness(0.9);
            }
            .error-icon {
                width: 4rem;
                height: 4rem;
                margin: 0 auto 1.5rem;
                color: var(--brand-primary);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="error-container">
            
            <!-- Logo Section -->
            <div style="margin-bottom: 2rem;">
                <a href="/" style="display: flex; justify-content: center;">
                    <img src="{{ $logoUrl }}" alt="{{ $siteSettings['company_name'] ?? 'Company' }} Logo" style="width: 5rem; height: auto; object-fit: contain;" />
                </a>
            </div>

            <!-- Error Card -->
            <div class="error-card">
                <!-- Icon -->
                <svg class="error-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <h1 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem;">{{ __('Too Many Requests') }}</h1>
                
                <p style="color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6;">
                    {{ __('Please wait a moment before trying again. This helps us keep the system secure.') }}
                </p>

                <a href="{{ url()->previous() }}" class="btn-primary">
                    {{ __('Try Again') }}
                </a>
                
                <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 1rem;">
                    {{ __('The limit resets every minute.') }}
                </p>
            </div>
            
            <!-- Footer -->
            <div style="margin-top: 2rem; text-align: center;">
                <p style="font-size: 0.875rem; color: #6b7280;">© {{ date('Y') }} {{ $siteSettings['company_name'] ?? 'Click Iraq' }}</p>
            </div>
        </div>
    </body>
</html>
