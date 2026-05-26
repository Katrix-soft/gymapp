<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Portal de Socio
        </h2>
    </x-slot>

    <!-- Welcome / Header Banner -->
    <div class="bg-gradient-to-r from-orange-600 to-amber-600 rounded-3xl p-6 md:p-8 shadow-xl shadow-orange-500/10 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.1),transparent)] pointer-events-none"></div>
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-white leading-none">¡Hola, {{ auth()->user()->first_name }}!</h1>
            <p class="text-orange-100 text-sm mt-2 max-w-md">Bienvenido a tu panel de control. Aquí puedes reservar tus clases, revisar tu plan de entrenamiento asignado y realizar el pago de tu pase.</p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            @php
                $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
            @endphp
            <a href="{{ $prefix }}/member/bookings" class="btn bg-white hover:bg-zinc-100 text-orange-655 border-none font-bold rounded-xl shadow-lg">
                Reservar Clase
            </a>
            <a href="{{ $prefix }}/member/workout" class="btn bg-zinc-950/30 hover:bg-zinc-950/45 text-white border-none font-bold rounded-xl">
                Entrenar Hoy
            </a>
        </div>
    </div>

    <!-- Membership Status & Renewal Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Pass Card -->
        <div class="lg:col-span-2 bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Estado de tu Pase</h3>
                @if($activeMembership)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="badge badge-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400 font-extrabold mb-1">Pase Activo</span>
                            <h2 class="text-2xl font-black text-white leading-tight">{{ $activeMembership->plan->name }}</h2>
                            <p class="text-xs text-zinc-500 mt-1">Expira el {{ \Carbon\Carbon::parse($activeMembership->end_date)->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-left sm:text-right bg-zinc-950/40 border border-zinc-800 px-4 py-3 rounded-xl min-w-[120px]">
                            <div class="text-3xl font-black text-orange-500 leading-none">{{ $daysRemaining }}</div>
                            <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider block mt-1">Días Restantes</span>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="badge badge-error bg-rose-500/10 border-rose-500/20 text-rose-400 font-extrabold mb-1">Sin Pase Activo</span>
                            <h2 class="text-xl font-black text-white leading-tight">Tu membresía está vencida</h2>
                            <p class="text-xs text-zinc-500 mt-1">Renueva hoy para seguir ingresando al gimnasio y reservar tus turnos.</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-3xl text-rose-500 font-black">✕</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Fast Renewal Form -->
            <div class="border-t border-zinc-800/60 pt-4 mt-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="form-control w-full sm:max-w-xs">
                    <label class="label"><span class="label-text text-zinc-400 font-bold text-[10px] uppercase">Selecciona Plan para Renovar</span></label>
                    <select wire:model="renewalPlanId" class="select select-sm select-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg">
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (${{ number_format($p->price, 0) }} - {{ $p->duration_months }} Meses)</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="generateRenewalCheckout" class="btn btn-sm bg-orange-500 hover:bg-orange-600 border-none text-white rounded-lg px-6 font-bold shadow-lg shadow-orange-500/10">
                    Pagar con MercadoPago
                </button>
            </div>
        </div>

        <!-- Body Measurements Summary -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Últimas Mediciones Físicas</h3>
                @php
                    $last = $measurements->last();
                @endphp
                @if($last)
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-zinc-950/40 border border-zinc-800 p-2.5 rounded-xl">
                            <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block mb-1">Peso</span>
                            <div class="text-lg font-black text-white">{{ $last->weight }} kg</div>
                        </div>
                        <div class="bg-zinc-950/40 border border-zinc-800 p-2.5 rounded-xl">
                            <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block mb-1">Grasa</span>
                            <div class="text-lg font-black text-emerald-500">{{ $last->body_fat_percentage }}%</div>
                        </div>
                        <div class="bg-zinc-950/40 border border-zinc-800 p-2.5 rounded-xl">
                            <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block mb-1">Músculo</span>
                            <div class="text-lg font-black text-orange-500">{{ $last->muscle_mass_percentage }}%</div>
                        </div>
                    </div>
                    <p class="text-[10px] text-zinc-500 text-center mt-4">Registrado el {{ \Carbon\Carbon::parse($last->logged_at)->format('d/m/Y') }}</p>
                @else
                    <div class="text-center py-6 text-zinc-400">
                        <span class="text-xs">No hay mediciones físicas registradas aún</span>
                    </div>
                @endif
            </div>

            <div class="border-t border-zinc-800/60 pt-3 text-center">
                <span class="text-[10px] text-zinc-500 block">Tu instructor actualizará tus medidas físicas periódicamente</span>
            </div>
        </div>

    </div>

    <!-- Bookings & Routine Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Bookings Column -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Mis Próximas Clases</h3>
            <div class="space-y-3">
                @forelse($upcomingBookings as $booking)
                    <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-3.5 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-bold text-orange-500 uppercase tracking-widest block mb-0.5">{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</span>
                            <h4 class="font-extrabold text-sm text-white leading-tight">{{ $booking->gymClass->name }}</h4>
                            <p class="text-[10px] text-zinc-500 mt-1">Profesor: {{ $booking->gymClass->trainer->name ?? 'Instructor' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono font-bold bg-zinc-800 px-2 py-1 border border-zinc-800 rounded text-zinc-300">
                                {{ substr($booking->gymClass->start_time, 0, 5) }} - {{ substr($booking->gymClass->end_time, 0, 5) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-zinc-800 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span class="text-xs">No tienes clases reservadas para esta semana</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Routine Column -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Entrenamiento de Hoy</h3>
                
                <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-5 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-orange-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h4 class="font-extrabold text-white text-base leading-tight">¿Listo para entrenar?</h4>
                    <p class="text-xs text-zinc-400 max-w-xs mx-auto mt-2">Inicia el Workout Player interactivo para registrar tus series de hoy y cronometrar tus descansos.</p>
                </div>
            </div>

            <div class="mt-6 border-t border-zinc-800/60 pt-4 text-center">
                <a href="{{ $prefix }}/member/workout" class="btn btn-sm bg-orange-500 hover:bg-orange-600 border-none text-white rounded-lg px-6 font-bold">
                    Iniciar Workout Player
                </a>
            </div>
        </div>

    </div>

</div>
