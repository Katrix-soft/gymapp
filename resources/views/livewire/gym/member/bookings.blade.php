<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Reserva de Clases
            </h2>
            <div class="flex items-center gap-2">
                <label class="text-xs text-zinc-400 font-bold uppercase tracking-wider">Fecha:</label>
                <input type="date" wire:model.live="bookingDate" class="input input-sm input-bordered bg-zinc-900 border-zinc-800 text-zinc-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg w-40" />
            </div>
        </div>
    </x-slot>

    <!-- Notifications Toast -->
    @if (session()->has('message'))
        <div class="alert alert-success bg-emerald-500/10 border-emerald-500/30 text-emerald-400 mb-6 rounded-xl flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button class="btn btn-ghost btn-xs btn-circle text-emerald-400">✕</button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error bg-rose-500/10 border-rose-500/30 text-rose-400 mb-6 rounded-xl flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button class="btn btn-ghost btn-xs btn-circle text-rose-455">✕</button>
        </div>
    @endif

    <!-- Selected Date Summary Banner -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-4 shadow-xl mb-6 flex justify-between items-center">
        <div>
            <span class="text-xs text-zinc-500 uppercase tracking-widest font-bold">Mostrando Clases para:</span>
            <h3 class="text-lg font-black text-white mt-0.5">
                {{ $weekdays[$filterDay] }}, {{ \Carbon\Carbon::parse($bookingDate)->format('d \d\e F') }}
            </h3>
        </div>
        <span class="badge bg-orange-500/10 border border-orange-500/25 text-orange-400 font-extrabold px-3 py-2 rounded-lg">
            {{ count($classList) }} {{ count($classList) === 1 ? 'Clase disponible' : 'Clases disponibles' }}
        </span>
    </div>

    <!-- Gym Classes List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classList as $class)
            <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between hover:border-orange-500/30 hover:shadow-orange-500/5 transition-all duration-300 group">
                <div>
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-black text-white group-hover:text-orange-400 transition-colors duration-300">
                            {{ $class['name'] }}
                        </h3>
                        <span class="text-xs font-mono font-bold bg-zinc-950 px-2.5 py-1 border border-zinc-850 rounded-lg text-zinc-400 shrink-0">
                            {{ $class['start_time'] }} - {{ $class['end_time'] }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-zinc-450 leading-relaxed mb-6">
                        {{ $class['description'] ?? 'Sin descripción añadida.' }}
                    </p>
                </div>

                <!-- Footer & Booking Control -->
                <div>
                    <div class="border-t border-zinc-800/60 pt-4 flex items-center justify-between text-xs mb-4">
                        <div>
                            <span class="text-[9px] text-zinc-550 block uppercase font-bold tracking-wider">Instructor</span>
                            <span class="font-extrabold text-zinc-300">{{ $class['trainer_name'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-zinc-550 block uppercase font-bold tracking-wider">Disponibilidad</span>
                            <span class="font-extrabold {{ $class['booked_count'] >= $class['capacity'] ? 'text-rose-455' : 'text-zinc-300' }}">
                                {{ $class['booked_count'] }} / {{ $class['capacity'] }} Cupos
                            </span>
                        </div>
                    </div>

                    <!-- Button mapping -->
                    <div class="border-t border-zinc-800/60 pt-4 mt-2">
                        @if($class['is_booked'])
                            <button wire:click="cancelBooking({{ $class['id'] }})" class="btn btn-sm bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border-rose-500/25 hover:border-none w-full rounded-xl font-bold">
                                Cancelar Reserva
                            </button>
                        @elseif($class['booked_count'] >= $class['capacity'])
                            <button class="btn btn-sm bg-zinc-800 text-zinc-600 border-zinc-850 w-full rounded-xl font-bold cursor-not-allowed" disabled>
                                Clase Llena / Sin Cupos
                            </button>
                        @else
                            <button wire:click="bookClass({{ $class['id'] }})" class="btn btn-sm bg-orange-500 hover:bg-orange-600 border-none text-white w-full rounded-xl font-bold shadow-lg shadow-orange-500/10">
                                Reservar Lugar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-zinc-550">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-zinc-750 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>No hay clases agendadas para el día de la semana seleccionado.</span>
            </div>
        @endforelse
    </div>
</div>
