<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Panel de Instructor / Profesor
        </h2>
    </x-slot>

    <!-- KPIs Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Members Count -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex items-center justify-between">
            <div>
                <span class="text-xs text-zinc-500 uppercase tracking-widest font-bold block mb-1">Socios Totales</span>
                <span class="text-3xl font-black text-white leading-none">{{ $membersCount }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>

        <!-- Taught Classes count -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex items-center justify-between">
            <div>
                <span class="text-xs text-zinc-500 uppercase tracking-widest font-bold block mb-1">Mis Clases Hoy</span>
                <span class="text-3xl font-black text-white leading-none">{{ count($todayClasses) }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        </div>

        <!-- Bookings Received -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex items-center justify-between">
            <div>
                <span class="text-xs text-zinc-500 uppercase tracking-widest font-bold block mb-1">Reservas Recibidas Hoy</span>
                <span class="text-3xl font-black text-white leading-none">{{ $bookingsCount }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
        </div>
    </div>

    <!-- Taught Classes for Today -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl mb-6">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Mis Clases de Hoy</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($todayClasses as $tc)
                <div class="bg-zinc-950/40 border border-zinc-855 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <h4 class="font-extrabold text-sm text-white leading-tight">{{ $tc->name }}</h4>
                        <p class="text-[10px] text-zinc-500 mt-1">Capacidad: {{ $tc->capacity }} cupos</p>
                    </div>
                    <span class="text-xs font-mono font-bold bg-zinc-800 px-2 py-1 border border-zinc-800 rounded text-zinc-300">
                        {{ substr($tc->start_time, 0, 5) }} - {{ substr($tc->end_time, 0, 5) }}
                    </span>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-zinc-600 text-xs">
                    No tienes clases asignadas para el día de hoy
                </div>
            @endforelse
        </div>
    </div>

    <!-- Members Search Directory -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl shadow-xl overflow-hidden">
        <!-- Search bar -->
        <div class="p-6 border-b border-zinc-800">
            <div class="relative w-full md:max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar socio por nombre..." class="input input-sm input-bordered w-full bg-zinc-950 border-zinc-800 pl-9 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg text-xs" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-zinc-300">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 font-bold bg-zinc-950/20 text-xs">
                        <th class="py-4 pl-6">Socio</th>
                        <th class="py-4">Membresía / Pase</th>
                        <th class="py-4">Rutina Activa</th>
                        <th class="py-4 text-right pr-6">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        @php
                            $activeMem = $member->memberships->where('status', 'active')->first();
                            $activeRoutine = $member->routines->first();
                        @endphp
                        <tr class="border-b border-zinc-800/60 hover:bg-zinc-800/20 transition-all duration-150 text-xs">
                            <td class="py-4 pl-6">
                                <div class="font-extrabold text-white leading-tight">{{ $member->name }}</div>
                                <div class="text-[10px] text-zinc-400 mt-0.5">{{ $member->email }}</div>
                            </td>
                            <td class="py-4">
                                @if($activeMem)
                                    <span class="badge badge-sm bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold">
                                        {{ $activeMem->plan->name ?? 'Activo' }}
                                    </span>
                                @else
                                    <span class="badge badge-sm bg-rose-500/10 border border-rose-500/20 text-rose-455 font-bold">
                                        Sin Pase Activo
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 font-semibold text-zinc-300">
                                {{ $activeRoutine->name ?? 'Sin rutina asignada' }}
                            </td>
                            <td class="py-4 text-right pr-6">
                                @php
                                    $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
                                @endphp
                                <a href="{{ $prefix }}/admin/chat" class="btn btn-xs bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700 rounded-lg">
                                    Enviar Mensaje
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-zinc-400">
                                No se encontraron socios en la búsqueda
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-zinc-800 bg-zinc-950/20">
            {{ $members->links() }}
        </div>
    </div>
</div>
