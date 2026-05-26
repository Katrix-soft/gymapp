<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Planes de Rutina
            </h2>
            <div class="flex items-center gap-2">
                <button wire:click="openAddExerciseModal" class="btn bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700 rounded-xl gap-2">
                    + Nuevo Ejercicio BD
                </button>
                <button wire:click="openCreateModal" class="btn bg-orange-500 hover:bg-orange-600 text-white border-none rounded-xl shadow-lg shadow-orange-500/20 gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Diseñar Rutina
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Notification Toast -->
    @if (session()->has('message'))
        <div class="alert alert-success bg-emerald-500/10 border-emerald-500/30 text-emerald-400 mb-6 rounded-xl flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button class="btn btn-ghost btn-xs btn-circle text-emerald-400">✕</button>
        </div>
    @endif

    <!-- Search filter -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl mb-6">
        <div class="relative w-full md:max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar rutinas por nombre o socio..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 pl-10 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
        </div>
    </div>

    <!-- Routines list -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($routines as $routine)
            <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between hover:border-orange-500/30 hover:shadow-orange-500/5 transition-all duration-300 group">
                <div>
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-[10px] font-bold text-orange-500 uppercase tracking-widest block mb-0.5">Asignada a: {{ $routine->member->name ?? 'Socio Eliminado' }}</span>
                            <h3 class="text-lg font-black text-white group-hover:text-orange-400 transition-colors duration-300">
                                {{ $routine->name }}
                            </h3>
                        </div>
                        <span class="text-xs bg-zinc-950 px-2.5 py-1 border border-zinc-800 rounded-lg text-zinc-400">
                            {{ count($routine->days) }} {{ count($routine->days) === 1 ? 'Día' : 'Días' }}
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-zinc-450 leading-relaxed mb-6">
                        {{ $routine->description ?? 'Sin descripción añadida.' }}
                    </p>

                    <!-- Days Preview -->
                    <div class="space-y-2 mb-6">
                        @foreach($routine->days as $day)
                            <div class="bg-zinc-950/40 p-2.5 rounded-lg border border-zinc-800/80 text-xs">
                                <div class="font-bold text-white mb-1.5 flex justify-between items-center">
                                    <span>{{ $day->name }}</span>
                                    <span class="text-[10px] text-zinc-400">{{ count($day->routineExercises) }} ej.</span>
                                </div>
                                <div class="text-[10px] text-zinc-500 flex flex-wrap gap-x-2 gap-y-1">
                                    @foreach($day->routineExercises as $re)
                                        <span class="bg-zinc-900 border border-zinc-800 px-1.5 py-0.5 rounded text-zinc-400">
                                            {{ $re->exercise->name ?? 'Ejercicio' }} ({{ $re->sets }}x{{ $re->reps }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer / Actions -->
                <div class="border-t border-zinc-800/60 pt-4 flex items-center justify-between">
                    <span class="text-[10px] text-zinc-400">Creado por: {{ $routine->trainer->name ?? 'Profesor' }}</span>
                    <div class="flex items-center gap-1.5">
                        <button wire:click="openEditModal({{ $routine->id }})" class="btn btn-xs btn-ghost btn-circle text-orange-500 hover:text-orange-500" title="Editar Rutina">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button onclick="confirm('¿Eliminar esta rutina?') || event.stopImmediatePropagation()" wire:click="deleteRoutine({{ $routine->id }})" class="btn btn-xs btn-ghost btn-circle text-rose-500 hover:text-rose-455" title="Eliminar Rutina">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-zinc-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" /></svg>
                <span>No se encontraron planes de rutina creados</span>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $routines->links() }}
    </div>

    <!-- ================= WORKSPACE MODAL: CREATE & EDIT ROUTINE ================= -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-4xl w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showCreateModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Modal Title -->
                <h3 class="text-xl font-black text-white mb-6 pr-8">
                    {{ $editingRoutineId ? 'Modificar Plan de Rutina' : 'Diseñar Nueva Rutina de Entrenamiento' }}
                </h3>

                <!-- Scrollable Form Builder -->
                <div class="overflow-y-auto pr-1 flex-grow space-y-6">
                    
                    <!-- Top section: Details and member -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Select Member (Autocomplete) -->
                        <div class="form-control relative">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Socio / Miembro Asignado</span></label>
                            <input type="text" wire:model.live="memberSearch" placeholder="Buscar socio por nombre o email..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" {{ $selectedMemberId ? 'disabled' : '' }} />
                            
                            @if($selectedMemberId)
                                <div class="absolute right-3 top-10 flex items-center">
                                    <button wire:click="$set('selectedMemberId', '')" class="btn btn-xs bg-rose-500/10 border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-lg">Cambiar</button>
                                </div>
                            @endif

                            <!-- Dropdown results -->
                            @if(!empty($searchMembers))
                                <div class="absolute left-0 right-0 top-full mt-1 bg-zinc-950 border border-zinc-800 rounded-xl shadow-xl z-50 max-h-48 overflow-y-auto p-1.5 space-y-1">
                                    @foreach($searchMembers as $sm)
                                        <button wire:click="selectMember({{ $sm->id }}, '{{ $sm->name }}')" class="w-full text-left p-2 hover:bg-zinc-800 rounded-lg flex items-center justify-between text-xs text-zinc-300">
                                            <div>
                                                <div class="font-bold text-white">{{ $sm->name }}</div>
                                                <div class="text-[10px] text-zinc-400">{{ $sm->email }}</div>
                                            </div>
                                            <span class="text-[9px] font-bold text-zinc-500">Seleccionar</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            @error('selectedMemberId') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Routine Name -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Nombre de la Rutina</span></label>
                            <input type="text" wire:model="routineName" placeholder="Ej: Rutina de Hipertrofia 3 Días, Definición..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('routineName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Observaciones / Descripción General</span></label>
                        <textarea wire:model="routineDescription" placeholder="Ej: Realizar calentamiento previo de 10 min, enfocar en excéntrico..." class="textarea textarea-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl h-16"></textarea>
                    </div>

                    <!-- Day blocks builder -->
                    <div class="space-y-6 pt-4 border-t border-zinc-800">
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider flex items-center justify-between">
                            <span>Distribución de Días de Entrenamiento</span>
                            <button type="button" wire:click="addDay" class="btn btn-xs bg-zinc-800 hover:bg-zinc-700 text-zinc-300 border-zinc-700 rounded-lg">
                                + Añadir Día
                            </button>
                        </h4>

                        @foreach($days as $dayIndex => $dayData)
                            <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-5 relative space-y-4">
                                <!-- Day header -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-800/80 pb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                                        <input type="text" wire:model="days.{{ $dayIndex }}.name" class="bg-transparent text-sm font-extrabold text-white focus:outline-none border-b border-dashed border-zinc-700 focus:border-orange-500 max-w-xs" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" wire:click="addExerciseToDay({{ $dayIndex }})" class="btn btn-xs bg-orange-500/10 border-orange-500/20 text-orange-500 hover:bg-orange-500 hover:text-white rounded-lg">
                                            + Ejercicio
                                        </button>
                                        @if(count($days) > 1)
                                            <button type="button" wire:click="removeDay({{ $dayIndex }})" class="btn btn-xs btn-ghost text-rose-500 hover:bg-rose-500/15 rounded-lg">
                                                Eliminar Día
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Exercises in day -->
                                <div class="space-y-3">
                                    @foreach($dayData['exercises'] as $exIndex => $ex)
                                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-center bg-zinc-900/60 p-3 rounded-lg border border-zinc-800/65 relative pr-10 md:pr-3">
                                            <!-- Exercise name -->
                                            <div class="col-span-1 md:col-span-2 form-control">
                                                <select wire:model="days.{{ $dayIndex }}.exercises.{{ $exIndex }}.exercise_id" class="select select-sm select-bordered bg-zinc-950 border-zinc-800 text-xs text-zinc-200 rounded-lg">
                                                    <option value="">Seleccione Ejercicio</option>
                                                    @foreach($exercises as $exercise)
                                                        <option value="{{ $exercise->id }}">{{ $exercise->name }} ({{ $exercise->muscle_group }})</option>
                                                    @endforeach
                                                </select>
                                                @error("days.{$dayIndex}.exercises.{$exIndex}.exercise_id") <span class="text-[9px] text-rose-500 mt-0.5">{{ $message }}</span> @enderror
                                            </div>

                                            <!-- Sets -->
                                            <div class="form-control">
                                                <input type="number" wire:model="days.{{ $dayIndex }}.exercises.{{ $exIndex }}.sets" min="1" placeholder="Sets" class="input input-sm input-bordered bg-zinc-950 border-zinc-800 text-xs text-center rounded-lg" />
                                                @error("days.{$dayIndex}.exercises.{$exIndex}.sets") <span class="text-[9px] text-rose-500 mt-0.5">{{ $message }}</span> @enderror
                                            </div>

                                            <!-- Reps -->
                                            <div class="form-control">
                                                <input type="text" wire:model="days.{{ $dayIndex }}.exercises.{{ $exIndex }}.reps" placeholder="Reps (ej: 12, 10-12)" class="input input-sm input-bordered bg-zinc-950 border-zinc-800 text-xs text-center rounded-lg" />
                                                @error("days.{$dayIndex}.exercises.{$exIndex}.reps") <span class="text-[9px] text-rose-500 mt-0.5">{{ $message }}</span> @enderror
                                            </div>

                                            <!-- Weight -->
                                            <div class="form-control">
                                                <input type="number" step="0.5" wire:model="days.{{ $dayIndex }}.exercises.{{ $exIndex }}.weight" min="0" placeholder="Peso (kg)" class="input input-sm input-bordered bg-zinc-950 border-zinc-800 text-xs text-center rounded-lg" />
                                                @error("days.{$dayIndex}.exercises.{$exIndex}.weight") <span class="text-[9px] text-rose-500 mt-0.5">{{ $message }}</span> @enderror
                                            </div>

                                            <!-- Rest seconds -->
                                            <div class="form-control">
                                                <input type="number" wire:model="days.{{ $dayIndex }}.exercises.{{ $exIndex }}.rest_seconds" min="0" placeholder="Descanso (s)" class="input input-sm input-bordered bg-zinc-950 border-zinc-800 text-xs text-center rounded-lg" />
                                                @error("days.{$dayIndex}.exercises.{$exIndex}.rest_seconds") <span class="text-[9px] text-rose-500 mt-0.5">{{ $message }}</span> @enderror
                                            </div>

                                            <!-- Notes -->
                                            <div class="col-span-1 md:col-span-5 form-control">
                                                <input type="text" wire:model="days.{{ $dayIndex }}.exercises.{{ $exIndex }}.notes" placeholder="Notas (ej: dropset, enfocar en contracción...)" class="input input-sm input-bordered bg-zinc-950 border-zinc-800 text-xs rounded-lg" />
                                            </div>

                                            <!-- Remove Exercise button -->
                                            <div class="absolute right-2 top-2 md:relative md:right-auto md:top-auto flex justify-end md:col-span-1">
                                                @if(count($dayData['exercises']) > 1)
                                                    <button type="button" wire:click="removeExerciseFromDay({{ $dayIndex }}, {{ $exIndex }})" class="btn btn-xs btn-ghost btn-circle text-rose-500" title="Eliminar ejercicio">✕</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                <!-- Form Action Buttons -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end gap-2">
                    <button wire:click="$set('showCreateModal', false)" class="btn btn-ghost border-zinc-800 hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-white">Cancelar</button>
                    <button wire:click="saveRoutine" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6">
                        Guardar Rutina
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ================= QUICK MODAL: ADD EXERCISE TO DATABASE ================= -->
    @if($showAddExerciseModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showAddExerciseModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Modal Title -->
                <h3 class="text-xl font-black text-white mb-6 pr-8">
                    Nuevo Ejercicio
                </h3>

                <!-- Form -->
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Nombre del Ejercicio</span></label>
                        <input type="text" wire:model="newExName" placeholder="Ej: Press de Banca Plano, Sentadillas Búlgaras..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                        @error('newExName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Grupo Muscular Principal</span></label>
                        <select wire:model="newExMuscle" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl">
                            <option value="Pecho">Pecho (Chest)</option>
                            <option value="Espalda">Espalda (Back)</option>
                            <option value="Piernas">Piernas (Legs)</option>
                            <option value="Hombros">Hombros (Shoulders)</option>
                            <option value="Brazos">Brazos (Arms)</option>
                            <option value="Core">Core (Abs)</option>
                            <option value="Cardio">Cardio / Resistencia</option>
                        </select>
                        @error('newExMuscle') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Instrucciones Breves</span></label>
                        <textarea wire:model="newExInstructions" placeholder="Detalles de ejecución..." class="textarea textarea-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl h-20"></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end gap-2">
                    <button wire:click="$set('showAddExerciseModal', false)" class="btn btn-ghost border-zinc-800 hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-white">Cancelar</button>
                    <button wire:click="createExercise" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6">
                        Registrar Ejercicio
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
