<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ tenant('name') ?? config('app.name', 'Gym App') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- CSS Variables for Dynamic Branding -->
        <style>
            :root {
                --primary: {{ tenant('primary_color') ?? '#f97316' }};
                --secondary: {{ tenant('secondary_color') ?? '#1e1b4b' }};
            }
            body {
                font-family: 'Outfit', sans-serif;
                background: radial-gradient(circle at 80% 20%, var(--secondary), #09090b 60%);
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased min-h-screen text-zinc-100 selection:bg-orange-500 selection:text-white">
        
        <div class="drawer lg:drawer-open">
            <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />
            
            <!-- Main Content Area -->
            <div class="drawer-content flex flex-col min-h-screen">
                
                <!-- Navbar (Mobile header, and desktop metadata) -->
                <header class="navbar bg-zinc-900/60 backdrop-blur-md border-b border-zinc-800/80 px-4 sm:px-6 py-3 z-30 sticky top-0">
                    <div class="flex-none lg:hidden">
                        <label for="sidebar-drawer" class="btn btn-square btn-ghost text-zinc-300 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </label>
                    </div>
                    
                    <!-- Tenant Logo / Name on Mobile -->
                    <div class="flex-1 lg:hidden pl-2">
                        <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-orange-400 to-amber-500 bg-clip-text text-transparent">
                            {{ tenant('name') ?? 'ARKHON' }}
                        </span>
                    </div>

                    <!-- Header Page Title / Breadcrumbs (Desktop) -->
                    <div class="flex-1 hidden lg:flex items-center space-x-2">
                        @if (isset($header))
                            <div class="text-lg font-semibold tracking-tight text-zinc-200">
                                {{ $header }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Right Navbar Section (User Info & Actions) -->
                    <div class="flex-none flex items-center space-x-4">
                        <!-- Notifications (Placeholder Indicator) -->
                        <button class="btn btn-ghost btn-circle text-zinc-400 hover:text-white relative">
                            <div class="indicator">
                               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                <span class="badge badge-xs badge-warning indicator-item"></span>
                            </div>
                        </button>

                        <!-- User Profile Dropdown -->
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar border border-zinc-700 hover:border-orange-500 transition-colors duration-300">
                                <div class="w-9 rounded-full bg-zinc-800 flex items-center justify-center text-orange-500 font-bold">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-50 p-2 shadow-xl bg-zinc-900 border border-zinc-800 rounded-box w-52">
                                <li class="menu-title px-4 py-2 text-xs font-semibold text-zinc-500 tracking-wider">
                                    {{ auth()->user()->name }}
                                </li>
                                <div class="h-px bg-zinc-800 my-1"></div>
                                <li>
                                    <a href="{{ request()->segment(1) === 'g' ? '/g/' . tenant('id') . '/logout' : '/logout' }}" class="text-rose-400 hover:text-rose-300 hover:bg-rose-500/10">
                                        Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>
                
                <!-- Page Content Body -->
                <main class="flex-grow p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
                
                <!-- Footer -->
                <footer class="footer footer-center p-4 bg-zinc-950 text-zinc-500 text-xs border-t border-zinc-900/60">
                    <aside>
                        <p>© {{ date('Y') }} {{ tenant('name') ?? 'Arkhon Gym' }}. Todos los derechos reservados. Powered by Arkhon SaaS.</p>
                    </aside>
                </footer>
            </div>
            
            <!-- Sidebar Drawer Container -->
            <div class="drawer-side z-40">
                <label for="sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                
                <!-- Sidebar Wrapper -->
                <div class="w-72 min-h-screen bg-zinc-900/90 backdrop-blur-md border-r border-zinc-800/80 flex flex-col justify-between p-6">
                    
                    <!-- Upper Section -->
                    <div>
                        <!-- Logo & Brand Header -->
                        <div class="flex items-center space-x-3 mb-8 px-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center shadow-lg shadow-orange-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-extrabold tracking-tight text-white leading-none">
                                    {{ tenant('name') ?? 'ARKHON' }}
                                </h1>
                                <span class="text-[10px] text-zinc-400 font-bold tracking-widest uppercase">
                                    @if(auth()->user()->hasRole('gym_admin'))
                                        Administrador
                                    @elseif(auth()->user()->hasRole('trainer'))
                                        Entrenador
                                    @else
                                        Miembro
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <!-- Navigation Menu -->
                        <nav class="space-y-1">
                            @php
                                $segment1 = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
                                $role = auth()->user()->hasRole('gym_admin') ? 'admin' : (auth()->user()->hasRole('trainer') ? 'trainer' : 'member');
                            @endphp

                            @if($role === 'admin')
                                <!-- Gym Admin Links -->
                                <a href="{{ $segment1 }}/admin/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/admin/dashboard') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                                    <span>Dashboard</span>
                                </a>

                                <a href="{{ $segment1 }}/admin/members" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/admin/members*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    <span>Socios / Miembros</span>
                                </a>

                                <a href="{{ $segment1 }}/admin/classes" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/admin/classes*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>Clases y Asistencia</span>
                                </a>

                                <a href="{{ $segment1 }}/admin/payments" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/admin/payments*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Pagos y Membresías</span>
                                </a>

                                <a href="{{ $segment1 }}/admin/routines" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/admin/routines*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                    <span>Planes de Rutina</span>
                                </a>

                                <a href="{{ $segment1 }}/admin/chat" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/admin/chat*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    <span>Mensajería / Chat</span>
                                </a>
                            @elseif($role === 'trainer')
                                <!-- Trainer Links -->
                                <a href="{{ $segment1 }}/trainer/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/trainer/dashboard') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                                    <span>Dashboard</span>
                                </a>

                                <a href="{{ $segment1 }}/trainer/classes" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/trainer/classes*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>Mis Clases</span>
                                </a>

                                <a href="{{ $segment1 }}/trainer/routines" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/trainer/routines*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                    <span>Planificador Rutinas</span>
                                </a>

                                <a href="{{ $segment1 }}/trainer/members" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/trainer/members*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z" /></svg>
                                    <span>Alumnos</span>
                                </a>

                                <a href="{{ $segment1 }}/trainer/chat" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/trainer/chat*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    <span>Chat</span>
                                </a>
                            @else
                                <!-- Member Links -->
                                <a href="{{ $segment1 }}/member/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/member/dashboard') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" /></svg>
                                    <span>Mi Portal</span>
                                </a>

                                <a href="{{ $segment1 }}/member/workout" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/member/workout*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Entrenar Ahora</span>
                                </a>

                                <a href="{{ $segment1 }}/member/classes" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/member/classes*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span>Reservar Clases</span>
                                </a>

                                <a href="{{ $segment1 }}/member/payments" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/member/payments*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                    <span>Mis Facturas / Pagos</span>
                                </a>

                                <a href="{{ $segment1 }}/member/chat" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('*/member/chat*') ? 'bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20' : 'text-zinc-400 hover:text-white hover:bg-zinc-800/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                    <span>Mensajes</span>
                                </a>
                            @endif
                        </nav>
                    </div>
                    
                    <!-- Bottom Section (User Profile Mini-Card) -->
                    <div class="border-t border-zinc-800 pt-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="w-10 h-10 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center text-orange-400 font-extrabold text-sm flex-none">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="truncate">
                                <h4 class="text-sm font-semibold text-white leading-tight truncate">{{ auth()->user()->name }}</h4>
                                <span class="text-[11px] text-zinc-400 truncate block">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                        <a href="{{ request()->segment(1) === 'g' ? '/g/' . tenant('id') . '/logout' : '/logout' }}" class="btn btn-ghost btn-circle btn-sm text-zinc-400 hover:text-rose-400" title="Cerrar Sesión">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
        
    </body>
</html>
