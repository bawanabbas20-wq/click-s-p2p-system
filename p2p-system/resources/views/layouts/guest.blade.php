<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $siteSettings['company_name'] ?? 'Click P2P' }} - Login</title>

        @php
            $logoUrl = !empty($siteSettings['company_logo']) 
                ? asset('storage/' . $siteSettings['company_logo']) 
                : asset('logo.png');
        @endphp

        <!-- Favicon (uses company logo if set) -->
        <link rel="icon" type="image/png" href="{{ $logoUrl }}">
        <link rel="shortcut icon" type="image/png" href="{{ $logoUrl }}">
        <link rel="apple-touch-icon" href="{{ $logoUrl }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --brand-primary: {{ $siteSettings['primary_color'] ?? '#65C34A' }};
                --brand-secondary: {{ $siteSettings['secondary_color'] ?? '#1F6BFF' }};
            }
            /* Reset and ensure clean styles */
            * { box-sizing: border-box; }
            body { margin: 0; padding: 0; }
            .login-container { 
                background: #f8fafc !important; 
                min-height: 100vh !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 2rem 1rem !important;
            }
            .login-card {
                background: white !important;
                border-radius: 1rem !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                padding: 2rem !important;
                width: 100% !important;
                max-width: 28rem !important;
            }
            /* Dynamic brand color styles */
            .btn-primary {
                background: var(--brand-primary) !important;
            }
            .btn-primary:hover {
                filter: brightness(0.9);
            }
            .link-primary {
                color: var(--brand-primary) !important;
            }
            .link-primary:hover {
                filter: brightness(0.8);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="login-container">
            
            <!-- Logo Section -->
            <div style="margin-bottom: 2rem;">
                <a href="/" style="display: flex; justify-content: center;">
                    <img src="{{ $logoUrl }}" alt="{{ $siteSettings['company_name'] ?? 'Company' }} Logo" style="width: 5rem; height: auto; object-fit: contain;" />
                </a>
            </div>

            <!-- Login Form Container -->
            <div class="login-card">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <div style="margin-top: 2rem; text-align: center;">
                <p style="font-size: 0.875rem; color: #6b7280;">© {{ date('Y') }} {{ $siteSettings['company_name'] ?? 'Click Iraq' }}</p>
            </div>
        </div>
    </body>
</html>
