<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Portal de Socio
        </h2>
    </x-slot>

    <!-- Session Alert with Confetti Celebration -->
    @if (session()->has('message'))
        <div class="alert alert-success bg-emerald-500/10 border-emerald-500/30 text-emerald-400 mb-6 rounded-xl flex items-center justify-between shadow-lg" x-data="{ show: true }" x-show="show">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6 text-emerald-400" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="btn btn-ghost btn-xs btn-circle text-emerald-400">✕</button>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
        <script>
            setTimeout(() => {
                confetti({
                    particleCount: 120,
                    spread: 80,
                    origin: { y: 0.6 },
                    colors: ['#f97316', '#fb923c', '#ffedd5', '#10b981', '#34d399']
                });
            }, 300);
        </script>
    @endif

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
            <button onclick="qr_pass_modal.showModal()" class="btn bg-white hover:bg-zinc-100 text-orange-600 border-none font-bold rounded-xl shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Mi Pase QR
            </button>
            <a href="{{ $prefix }}/member/bookings" class="btn bg-zinc-950/20 hover:bg-zinc-950/30 text-white border-zinc-700/50 font-bold rounded-xl">
                Reservar Clase
            </a>
            <a href="{{ $prefix }}/member/workout" class="btn bg-zinc-950/40 hover:bg-zinc-950/50 text-white border-none font-bold rounded-xl">
                Entrenar Hoy
            </a>
        </div>
    </div>

    <!-- 🔴 Membership Expiry Alert Banners -->
    @if(!$activeMembership)
        <div class="mb-6 bg-gradient-to-r from-rose-500/15 to-rose-600/10 border border-rose-500/30 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-pulse-slow" x-data>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-rose-500/15 border border-rose-500/25 flex items-center justify-center text-rose-500 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                </div>
                <div>
                    <h3 class="text-rose-400 font-extrabold text-sm">¡Tu membresía ha expirado!</h3>
                    <p class="text-rose-300/70 text-xs mt-0.5">No podrás acceder al gimnasio ni reservar clases hasta que renueves tu plan.</p>
                </div>
            </div>
            <a href="#renewal-section" class="btn bg-rose-500 hover:bg-rose-600 border-none text-white font-bold rounded-xl shadow-lg shadow-rose-500/10 shrink-0">
                🔥 Reactivar Membresía
            </a>
        </div>
    @elseif($daysRemaining <= 7 && $daysRemaining > 0)
        <div class="mb-6 bg-gradient-to-r from-amber-500/15 to-orange-500/10 border border-amber-500/30 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-500/25 flex items-center justify-center text-amber-500 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <h3 class="text-amber-400 font-extrabold text-sm">⏰ Tu membresía vence en {{ $daysRemaining }} {{ $daysRemaining === 1 ? 'día' : 'días' }}</h3>
                    <p class="text-amber-300/70 text-xs mt-0.5">Renová ahora para no perder acceso al gimnasio y tus reservas de clases.</p>
                </div>
            </div>
            <a href="#renewal-section" class="btn bg-amber-500 hover:bg-amber-600 border-none text-white font-bold rounded-xl shadow-lg shadow-amber-500/10 shrink-0">
                Renovar Ahora
            </a>
        </div>
    @endif

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
            <div id="renewal-section" class="border-t border-zinc-800/60 pt-4 mt-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
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
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Mi Entrenamiento</h3>
                
                <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-5 text-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-orange-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h4 class="font-extrabold text-white text-sm leading-tight">¿Listo para entrenar?</h4>
                    <p class="text-[11px] text-zinc-400 max-w-xs mx-auto mt-1">Inicia el Workout Player interactivo para registrar tus series.</p>
                </div>

                <!-- Weekly Progress Tracker -->
                <div class="border-t border-zinc-855/50 pt-4 flex items-center justify-between gap-4">
                    <div>
                        <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider block">Progreso Semanal</span>
                        <div class="text-base font-black text-white mt-0.5">{{ $workoutsThisWeek }} de {{ $weeklyGoal }}</div>
                        <p class="text-[10px] text-zinc-400">Completados esta semana</p>
                    </div>
                    <div class="relative flex items-center justify-center shrink-0">
                        <div class="radial-progress text-orange-500 bg-zinc-950 border-4 border-zinc-950" 
                             style="--value:{{ $weeklyProgressPercentage }}; --size:3.5rem; --thickness: 4px;" 
                             role="progressbar">
                            <span class="text-xs font-black text-white">{{ $weeklyProgressPercentage }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-zinc-800/60 pt-4 text-center">
                <a href="{{ $prefix }}/member/workout" class="btn btn-sm bg-orange-500 hover:bg-orange-600 border-none text-white rounded-lg px-6 font-bold shadow-lg shadow-orange-500/10">
                    Iniciar Workout Player
                </a>
            </div>
        </div>

    </div>

    <!-- Payments Log History Section -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl mb-6">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">💳 Historial de Pagos</h3>
        @if($pastPayments->isEmpty())
            <div class="text-center py-6 text-zinc-500">
                <span class="text-xs">Aún no has realizado ningún pago</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-sm w-full">
                    <thead>
                        <tr class="border-zinc-800 text-zinc-500">
                            <th class="text-xs uppercase">Fecha</th>
                            <th class="text-xs uppercase">Referencia</th>
                            <th class="text-xs uppercase">Monto</th>
                            <th class="text-xs uppercase">Método</th>
                            <th class="text-xs uppercase text-center">Estado</th>
                            <th class="text-xs uppercase text-right">Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastPayments as $payment)
                            <tr class="border-zinc-800/60 hover:bg-zinc-800/20">
                                <td class="text-zinc-300 text-xs font-mono">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-zinc-400 text-xs font-mono">{{ $payment->external_reference ?? 'N/A' }}</td>
                                <td class="text-white font-extrabold text-xs">${{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="text-zinc-400 text-xs">{{ $payment->payment_method ?? 'MercadoPago' }}</td>
                                <td class="text-center">
                                    @if($payment->status === 'paid')
                                        <span class="badge badge-xs bg-emerald-500/10 border-emerald-500/20 text-emerald-400 font-extrabold">Aprobado</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge badge-xs bg-amber-500/10 border-amber-500/20 text-amber-400 font-extrabold">Pendiente</span>
                                    @else
                                        <span class="badge badge-xs bg-rose-500/10 border-rose-500/20 text-rose-400 font-extrabold">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($payment->status === 'paid')
                                        <a href="{{ $prefix }}/member/payment/receipt/{{ $payment->id }}" class="btn btn-xs bg-orange-500/15 border-orange-500/30 text-orange-400 hover:bg-orange-500 hover:text-white rounded-lg flex items-center gap-1 inline-flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            PDF
                                        </a>
                                    @else
                                        <span class="text-[10px] text-zinc-650 italic">No disponible</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- QR Pass Modal -->
    <dialog id="qr_pass_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-zinc-950 border border-zinc-800 rounded-3xl p-6 text-center max-w-sm">
            <h3 class="font-black text-xl text-white mb-2">Mi Pase Digital QR</h3>
            <p class="text-xs text-zinc-400 mb-6">Muestra este código en la recepción del gimnasio para registrar tu ingreso.</p>
            
            <div class="bg-white p-4 rounded-2xl inline-block shadow-lg shadow-orange-500/5 mb-6 border border-zinc-200">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=GYM-MEMBER-{{ auth()->id() }}" 
                     alt="Código QR de Acceso" 
                     class="w-[200px] h-[200px] mx-auto select-none" />
            </div>
            
            <div class="text-zinc-500 text-[10px] font-mono mb-4 uppercase tracking-widest">
                ID: GYM-MEMBER-{{ auth()->id() }}
            </div>
            
            <div class="modal-action justify-center">
                <form method="dialog">
                    <button class="btn btn-sm bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border-zinc-700 rounded-lg px-6 font-bold">
                        Cerrar Pase
                    </button>
                </form>
            </div>
        </div>
    </dialog>

</div>
