<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z" /></svg>
            Alumnos
        </h2>
    </x-slot>

    <!-- Search -->
    <div class="mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar alumno por nombre o email..." class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-xl w-full sm:max-w-md" />
    </div>

    <!-- Members Grid -->
    @if($members->isEmpty())
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-12 text-center">
            <p class="text-zinc-500 text-sm">No se encontraron alumnos.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            @foreach($members as $member)
                <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-5 hover:border-zinc-700 transition-all duration-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-orange-400 font-bold text-sm">
                            {{ strtoupper(substr($member->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($member->last_name ?? '', 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-white font-bold text-sm leading-tight truncate">{{ $member->name }}</h4>
                            <span class="text-zinc-500 text-[11px] truncate block">{{ $member->email }}</span>
                        </div>
                    </div>

                    <!-- Membership Status -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] text-zinc-500 uppercase tracking-wider">Membresía</span>
                        @if($member->activeMembership)
                            <span class="badge badge-xs bg-emerald-500/10 border-emerald-500/20 text-emerald-400">{{ $member->activeMembership->plan->name ?? 'Activa' }}</span>
                        @else
                            <span class="badge badge-xs bg-rose-500/10 border-rose-500/20 text-rose-400">Sin plan</span>
                        @endif
                    </div>

                    <!-- Routines count -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] text-zinc-500 uppercase tracking-wider">Rutinas</span>
                        <span class="text-zinc-300 text-xs font-mono">{{ $member->routines->count() }}</span>
                    </div>

                    <button wire:click="openProfile({{ $member->id }})" class="btn btn-sm w-full bg-zinc-800 hover:bg-zinc-700 border-zinc-700 text-zinc-300 rounded-xl">
                        Ver Perfil
                    </button>
                </div>
            @endforeach
        </div>

        {{ $members->links() }}
    @endif

    <!-- Profile Modal -->
    @if($showProfileModal && $profileMember)
        <div class="modal modal-open">
            <div class="modal-box bg-zinc-900 border border-zinc-800 max-w-lg max-h-[80vh]">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-orange-400 font-bold text-lg">
                            {{ strtoupper(substr($profileMember->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($profileMember->last_name ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $profileMember->name }}</h3>
                            <span class="text-zinc-500 text-xs">{{ $profileMember->email }}</span>
                        </div>
                    </div>
                    <button wire:click="closeProfile" class="btn btn-ghost btn-sm btn-circle text-zinc-400">✕</button>
                </div>

                <!-- Memberships -->
                <div class="mb-4">
                    <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Membresías</h4>
                    @foreach($profileMember->memberships as $ms)
                        <div class="flex items-center justify-between bg-zinc-950/40 border border-zinc-800/60 rounded-lg px-3 py-2 mb-1">
                            <span class="text-white text-xs font-semibold">{{ $ms->plan->name ?? 'Plan' }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-500 text-[10px]">{{ \Carbon\Carbon::parse($ms->end_date)->format('d/m/Y') }}</span>
                                <span class="badge badge-xs {{ $ms->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-zinc-700 text-zinc-400' }}">{{ ucfirst($ms->status) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Routines -->
                <div class="mb-4">
                    <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Rutinas Asignadas</h4>
                    @forelse($profileMember->routines as $r)
                        <div class="bg-zinc-950/40 border border-zinc-800/60 rounded-lg px-3 py-2 mb-1">
                            <span class="text-white text-xs font-semibold">{{ $r->name }}</span>
                            <span class="text-zinc-500 text-[10px] block">{{ Str::limit($r->description, 50) }}</span>
                        </div>
                    @empty
                        <p class="text-zinc-600 text-xs">Sin rutinas asignadas.</p>
                    @endforelse
                </div>

                <!-- Body Measurements -->
                <div class="mb-4">
                    <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Medidas Corporales</h4>
                    @forelse($profileMember->bodyMeasurements as $bm)
                        <div class="flex items-center gap-3 text-xs text-zinc-400 mb-1">
                            <span class="text-zinc-500 font-mono">{{ \Carbon\Carbon::parse($bm->logged_at)->format('d/m') }}</span>
                            <span>{{ $bm->weight }}kg</span>
                            <span>{{ $bm->fat_percentage }}% grasa</span>
                            <span>Cintura: {{ $bm->waist }}cm</span>
                        </div>
                    @empty
                        <p class="text-zinc-600 text-xs">Sin medidas registradas.</p>
                    @endforelse
                </div>

                <!-- Recent Workouts -->
                <div>
                    <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Entrenamientos Recientes</h4>
                    @forelse($profileMember->workoutLogs as $wl)
                        <div class="flex items-center justify-between text-xs text-zinc-400 mb-1">
                            <span class="text-white">{{ $wl->routine->name ?? 'Rutina' }}</span>
                            <span class="text-zinc-500 font-mono">{{ \Carbon\Carbon::parse($wl->completed_at)->format('d/m/Y H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-zinc-600 text-xs">Sin entrenamientos registrados.</p>
                    @endforelse
                </div>
            </div>
            <div class="modal-backdrop" wire:click="closeProfile"></div>
        </div>
    @endif
</div>
