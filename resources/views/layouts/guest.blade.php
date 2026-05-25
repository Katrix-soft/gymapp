<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ tenant() ? \App\Models\TenantConfig::get('gym_name', 'SaaS Gym') : config('app.name', 'SaaS Central') }}</title>

        <!-- Fonts: Outfit for premium look -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                box-shadow: 0 0 15px var(--brand-color-glow);
                transform: translateY(-1px);
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
                background: rgba(17, 24, 39, 0.7);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
        </style>
    </head>
    <body class="antialiased text-gray-100 bg-gray-950 min-h-screen relative overflow-x-hidden flex items-center justify-center py-10 px-4">
        <!-- Premium Gym Background Image with Dark Overlay -->
        <div class="absolute inset-0 z-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1540206351-d6465b3ac5c1?q=80&w=1920&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-gradient-to-tr from-gray-950 via-gray-950/90 to-gray-900/40"></div>
        </div>

        <!-- Glowing background blobs -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-brand-color opacity-[0.08] blur-[120px] rounded-full z-0 pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-600 opacity-[0.06] blur-[120px] rounded-full z-0 pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-md">
            <!-- Logo & Brand Header -->
            <div class="text-center mb-8">
                <a href="/" wire:navigate class="inline-flex flex-col items-center">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $gymName }}" class="h-16 w-16 object-cover rounded-full border-2 border-brand-color shadow-lg mb-3" />
                    @else
                        <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-orange-500 to-red-600 flex items-center justify-center shadow-lg mb-3">
                            <!-- Premium barbell icon -->
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 100-4 2 2 0 000 4zm-14 0a2 2 0 100-4 2 2 0 000 4zm14 0v4m-14-4v4m1.5-6h11m-11 8h11"></path>
                            </svg>
                        </div>
                    @endif
                    <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-gray-100 to-gray-400 bg-clip-text text-transparent">
                        {{ $gymName }}
                    </h1>
                    @if(tenant())
                        <p class="text-sm text-gray-400 mt-1">Portal de Socios y Staff</p>
                    @else
                        <p class="text-sm text-gray-400 mt-1">Plataforma SaaS para Gimnasios</p>
                    @endif
                </a>
            </div>

            <!-- Glass Card Content -->
            <div class="w-full glass-card rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                <!-- Border Top Brand Indicator -->
                <div class="absolute top-0 left-0 right-0 h-[3px]" style="background-color: var(--brand-color);"></div>

                {{ $slot }}
            </div>
        </div>
    </body>
</html>
