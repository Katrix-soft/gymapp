<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ tenant() ? \App\Models\TenantConfig::get('gym_name', 'SaaS Gym') : config('app.name', 'SaaS Central') }}</title>

        <!-- SEO -->
        <meta name="description" content="Sistema de gestión para gimnasios. Administra miembros, clases, pagos y más.">
        <meta name="robots" content="noindex, nofollow">

        <!-- Fonts: Outfit for premium look -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- PWA Settings -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Arkhon Gym">
        <link rel="apple-touch-icon" href="{{ tenant() ? (\App\Models\TenantConfig::get('logo_url') ?: '/icon-192.png') : '/icon-192.png' }}">
        <link rel="manifest" href="{{ tenant() && request()->segment(1) === 'g' ? '/g/' . tenant('id') . '/manifest.json' : '/manifest.json' }}">

        @php
            $brandColor = tenant() ? \App\Models\TenantConfig::get('brand_color', '#FF6B35') : '#FF6B35';
            $logoUrl = tenant() ? \App\Models\TenantConfig::get('logo_url') : null;
            $gymName = tenant() ? \App\Models\TenantConfig::get('gym_name', 'GymDemo') : config('app.name', 'SaaS Central');
        @endphp

        <style>
            :root {
                --brand-color: {{ $brandColor }};
                --brand-color-hover: {{ $brandColor }}cc;
                --brand-color-glow: {{ $brandColor }}33;
                --brand-color-soft: {{ $brandColor }}15;
            }
            body {
                font-family: 'Outfit', sans-serif;
            }
            .brand-btn {
                background-color: var(--brand-color);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .brand-btn:hover {
                background-color: var(--brand-color-hover);
                box-shadow: 0 0 20px var(--brand-color-glow), 0 4px 12px rgba(0,0,0,0.3);
                transform: translateY(-1px);
            }
            .brand-btn:active {
                transform: translateY(0);
            }
            .brand-text {
                color: var(--brand-color);
            }
            .brand-border {
                border-color: var(--brand-color);
            }
            .brand-focus:focus {
                border-color: var(--brand-color) !important;
                box-shadow: 0 0 0 3px var(--brand-color-glow) !important;
            }
            .glass-card {
                background: rgba(17, 24, 39, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            /* Subtle animation for the background */
            @keyframes float {
                0%, 100% { transform: translateY(0) scale(1); }
                50% { transform: translateY(-20px) scale(1.05); }
            }
            .float-blob {
                animation: float 8s ease-in-out infinite;
            }
            .float-blob-delayed {
                animation: float 10s ease-in-out infinite;
                animation-delay: -3s;
            }
            /* Logo pulse on hover */
            .logo-container:hover .logo-glow {
                opacity: 0.4;
                transform: scale(1.2);
            }
            .logo-glow {
                transition: all 0.5s ease;
                opacity: 0;
            }
        </style>
    </head>
    <body class="antialiased text-gray-100 bg-gray-950 min-h-screen relative overflow-x-hidden flex items-center justify-center py-10 px-4">
        <!-- Premium Gym Background Image with Dark Overlay -->
        <div class="absolute inset-0 z-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1920&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-gray-950/90 to-gray-900/50"></div>
        </div>

        <!-- Glowing background blobs -->
        <div class="float-blob absolute top-1/4 left-1/4 w-80 h-80 rounded-full z-0 pointer-events-none" style="background: var(--brand-color); opacity: 0.06; filter: blur(100px);"></div>
        <div class="float-blob-delayed absolute bottom-1/4 right-1/4 w-80 h-80 bg-indigo-600 opacity-[0.04] blur-[100px] rounded-full z-0 pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-md">
            <!-- Logo & Brand Header -->
            <div class="text-center mb-8 logo-container">
                <a href="/" wire:navigate class="inline-flex flex-col items-center relative">
                    @if ($logoUrl)
                        <div class="relative">
                            <div class="logo-glow absolute inset-0 rounded-full" style="background: var(--brand-color); filter: blur(20px);"></div>
                            <img src="{{ $logoUrl }}" alt="{{ $gymName }}" class="relative h-18 w-18 object-cover rounded-full border-2 shadow-lg mb-3" style="border-color: var(--brand-color);" />
                        </div>
                    @else
                        <div class="relative">
                            <div class="logo-glow absolute inset-0 rounded-2xl" style="background: var(--brand-color); filter: blur(20px);"></div>
                            <div class="relative h-16 w-16 rounded-2xl bg-gradient-to-tr from-orange-500 to-red-600 flex items-center justify-center shadow-lg mb-3">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 100-4 2 2 0 000 4zm-14 0a2 2 0 100-4 2 2 0 000 4zm14 0v4m-14-4v4m1.5-6h11m-11 8h11"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                    <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-gray-100 to-gray-400 bg-clip-text text-transparent">
                        {{ $gymName }}
                    </h1>
                    @if(tenant())
                        <p class="text-sm text-gray-400 mt-1 font-light">Portal de Socios y Staff</p>
                    @else
                        <p class="text-sm text-gray-400 mt-1 font-light">Plataforma SaaS para Gimnasios</p>
                    @endif
                </a>
            </div>

            <!-- Glass Card Content -->
            <div class="w-full glass-card rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                <!-- Border Top Brand Indicator -->
                <div class="absolute top-0 left-0 right-0 h-[3px]" style="background: linear-gradient(90deg, transparent, var(--brand-color), transparent);"></div>

                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-600">
                    &copy; {{ date('Y') }} {{ $gymName }}. Powered by
                    <span class="font-semibold text-gray-500">Arkhon</span>
                </p>
            </div>
        </div>
        <!-- Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('Service Worker registered!', reg))
                        .catch(err => console.log('Service Worker registration failed: ', err));
                });
            }
        </script>
    </body>
</html>
