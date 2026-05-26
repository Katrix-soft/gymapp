<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            Planificador de Rutinas
        </h2>
    </x-slot>

    @if (session()->has('message'))
        <div class="alert bg-emerald-500/10 border-emerald-500/30 text-emerald-400 mb-6 rounded-xl" x-data="{ show: true }" x-show="show">
            <span class="font-bold text-sm">{{ session('message') }}</span>
            <button @click="show = false" class="btn btn-ghost btn-xs btn-circle">✕</button>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar rutina..." class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-xl w-full sm:max-w-xs" />
        <button wire:click="openCreateModal" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white font-bold rounded-xl shadow-lg shadow-orange-500/10">
            + Nueva Rutina
        </button>
    </div>

    <!-- Routines Grid -->
    @if($routines->isEmpty())
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-700 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            <p class="text-zinc-500 text-sm">No tienes rutinas creadas aún. Creá una para tus alumnos.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($routines as $routine)
                <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-5 hover:border-zinc-700 transition-all duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="text-white font-bold text-base leading-tight">{{ $routine->name }}</h4>
                            <p class="text-xs text-zinc-500 mt-1">{{ Str::limit($routine->description, 60) }}</p>
                        </div>
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle text-zinc-400">⋮</div>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow-xl bg-zinc-900 border border-zinc-800 rounded-box w-40 z-50">
                                <li><button wire:click="openEditModal({{ $routine->id }})" class="text-zinc-300 text-xs hover:bg-zinc-800">Editar</button></li>
                                <li><button wire:click="openAddDayModal({{ $routine->id }})" class="text-zinc-300 text-xs hover:bg-zinc-800">Agregar Día</button></li>
                                <li><button wire:click="deleteRoutine({{ $routine->id }})" wire:confirm="¿Eliminar esta rutina?" class="text-rose-400 text-xs hover:bg-rose-500/10">Eliminar</button></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Assigned Member -->
                    <div class="flex items-center gap-2 mb-3 bg-zinc-950/40 border border-zinc-800/60 rounded-lg px-3 py-2">
                        <div class="w-7 h-7 rounded-full bg-zinc-800 flex items-center justify-center text-orange-400 font-bold text-[10px]">
                            {{ strtoupper(substr($routine->member->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($routine->member->last_name ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-white text-xs font-semibold">{{ $routine->member->name ?? 'Sin asignar' }}</span>
                            <span class="text-zinc-500 text-[10px] block">Alumno</span>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-3 text-[10px] text-zinc-500 uppercase tracking-wider mb-3">
                        <span>{{ $routine->days->count() }} días</span>
                        <span>•</span>
                        <span>{{ $routine->days->sum(fn($d) => $d->routineExercises->count()) }} ejercicios</span>
                    </div>

                    <button wire:click="viewRoutineDetail({{ $routine->id }})" class="btn btn-sm w-full bg-zinc-800 hover:bg-zinc-700 border-zinc-700 text-zinc-300 rounded-xl">
                        Ver Detalle
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showCreateModal)
        <div class="modal modal-open">
            <div class="modal-box bg-zinc-900 border border-zinc-800 max-w-md">
                <h3 class="text-lg font-bold text-white mb-4">{{ $editingRoutineId ? 'Editar Rutina' : 'Nueva Rutina' }}</h3>
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Nombre</span></label>
                        <input wire:model="routineName" type="text" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg" placeholder="Ej: Rutina Fuerza 3 días" />
                        @error('routineName') <span class="text-rose-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Descripción</span></label>
                        <textarea wire:model="routineDescription" class="textarea textarea-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg" rows="2" placeholder="Descripción breve..."></textarea>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Alumno</span></label>
                        <select wire:model="memberId" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg">
                            <option value="">Seleccionar alumno...</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->email }})</option>
                            @endforeach
                        </select>
                        @error('memberId') <span class="text-rose-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-action">
                    <button wire:click="$set('showCreateModal', false)" class="btn bg-zinc-800 border-zinc-700 text-zinc-300 rounded-xl">Cancelar</button>
                    <button wire:click="saveRoutine" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl">Guardar</button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="$set('showCreateModal', false)"></div>
        </div>
    @endif

    <!-- Add Day Modal -->
    @if($showAddDayModal)
        <div class="modal modal-open">
            <div class="modal-box bg-zinc-900 border border-zinc-800 max-w-sm">
                <h3 class="text-lg font-bold text-white mb-4">Agregar Día de Rutina</h3>
                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Nombre del Día</span></label>
                    <input wire:model="newDayName" type="text" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg" placeholder="Ej: Día 1: Empuje" />
                    @error('newDayName') <span class="text-rose-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="modal-action">
                    <button wire:click="$set('showAddDayModal', false)" class="btn bg-zinc-800 border-zinc-700 text-zinc-300 rounded-xl">Cancelar</button>
                    <button wire:click="saveDay" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl">Agregar</button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="$set('showAddDayModal', false)"></div>
        </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $detailRoutine)
        <div class="modal modal-open">
            <div class="modal-box bg-zinc-900 border border-zinc-800 max-w-2xl max-h-[80vh]">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $detailRoutine->name }}</h3>
                        <p class="text-xs text-zinc-500">Alumno: {{ $detailRoutine->member->name ?? 'Sin asignar' }}</p>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="btn btn-ghost btn-sm btn-circle text-zinc-400">✕</button>
                </div>
                <p class="text-sm text-zinc-400 mb-4">{{ $detailRoutine->description }}</p>

                @foreach($detailRoutine->days as $day)
                    <div class="mb-4 bg-zinc-950/40 border border-zinc-800/60 rounded-xl p-4">
                        <h4 class="text-white font-bold text-sm mb-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-400 text-xs font-mono">{{ $loop->iteration }}</span>
                            {{ $day->name }}
                        </h4>
                        @if($day->routineExercises->isEmpty())
                            <p class="text-zinc-600 text-xs italic">Sin ejercicios asignados.</p>
                        @else
                            <div class="space-y-2">
                                @foreach($day->routineExercises as $re)
                                    <div class="flex items-center justify-between bg-zinc-900/60 border border-zinc-800/40 rounded-lg px-3 py-2">
                                        <div>
                                            <span class="text-white text-xs font-semibold">{{ $re->exercise->name ?? 'Ejercicio' }}</span>
                                            <span class="text-zinc-500 text-[10px] ml-2">{{ $re->exercise->muscle_group ?? '' }}</span>
                                        </div>
                                        <span class="text-orange-400 text-xs font-mono">{{ $re->sets }}×{{ $re->reps }} @ {{ $re->weight }}kg</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="modal-backdrop" wire:click="$set('showDetailModal', false)"></div>
        </div>
    @endif
</div>
