<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Clases y Asistencia
            </h2>
            <button wire:click="openCreateModal" class="btn bg-orange-500 hover:bg-orange-600 text-white border-none rounded-xl shadow-lg shadow-orange-500/20 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nueva Clase
            </button>
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

    <!-- Search and Day Filters -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search field -->
            <div class="relative w-full md:max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar clase por nombre..." class="input input-bordered w-full bg-zinc-950 border-zinc-850 pl-10 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
            </div>

            <!-- Day Filter -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider hidden sm:inline">Filtrar Día:</span>
                <select wire:model.live="filterDay" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl w-full sm:w-48">
                    <option value="">Todos los días</option>
                    <option value="1">Lunes</option>
                    <option value="2">Martes</option>
                    <option value="3">Miércoles</option>
                    <option value="4">Jueves</option>
                    <option value="5">Viernes</option>
                    <option value="6">Sábado</option>
                    <option value="0">Domingo</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Gym Classes List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($classes as $class)
            <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between hover:border-orange-500/30 hover:shadow-orange-500/5 transition-all duration-300 group">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <span class="badge badge-sm bg-orange-500/10 border-orange-500/25 text-orange-400 font-semibold mb-1">
                                {{ $weekdays[$class->day_of_week] }}
                            </span>
                            <h3 class="text-lg font-black text-white group-hover:text-orange-400 transition-colors duration-300">
                                {{ $class->name }}
                            </h3>
                        </div>
                        <div class="text-right text-xs text-zinc-400 font-mono bg-zinc-950 px-2.5 py-1 rounded-lg border border-zinc-800/85">
                            {{ substr($class->start_time, 0, 5) }} - {{ substr($class->end_time, 0, 5) }}
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-zinc-450 mb-6 leading-relaxed">
                        {{ $class->description ?? 'Sin descripción añadida.' }}
                    </p>
                </div>

                <!-- Info Grid / Action -->
                <div>
                    <div class="border-t border-zinc-800/60 pt-4 flex items-center justify-between text-xs mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-zinc-800 flex items-center justify-center text-[10px] text-zinc-300 font-extrabold uppercase">
                                {{ strtoupper(substr($class->trainer->first_name ?? 'P', 0, 1)) }}{{ strtoupper(substr($class->trainer->last_name ?? 'F', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-zinc-300 leading-none">{{ $class->trainer->name ?? 'Prof. No asignado' }}</div>
                                <span class="text-[9px] text-zinc-550">Instructor</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-zinc-300">{{ $class->capacity }}</div>
                            <span class="text-[9px] text-zinc-550">Cupos Máx.</span>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-between gap-2 border-t border-zinc-800/60 pt-4">
                        <button wire:click="openAttendanceModal({{ $class->id }})" class="btn btn-sm bg-orange-500/10 hover:bg-orange-500 text-orange-400 hover:text-white border-orange-500/25 hover:border-none flex-grow rounded-xl gap-1 text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            Asistencia
                        </button>
                        <button wire:click="openEditModal({{ $class->id }})" class="btn btn-sm btn-ghost btn-circle text-zinc-400 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button onclick="confirm('¿Eliminar esta clase?') || event.stopImmediatePropagation()" wire:click="deleteClass({{ $class->id }})" class="btn btn-sm btn-ghost btn-circle text-rose-500 hover:text-rose-455">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-zinc-550">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-zinc-750 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span class="text-zinc-500">No se encontraron clases agendadas en este filtro</span>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $classes->links() }}
    </div>

    <!-- ================= MODAL: CREATE & EDIT CLASS ================= -->
    @if($showCreateEditModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showCreateEditModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Modal Title -->
                <h3 class="text-xl font-black text-white mb-6 pr-8">
                    {{ $editingClassId ? 'Editar Clase' : 'Crear Nueva Clase' }}
                </h3>

                <!-- Form Scrollable Area -->
                <div class="overflow-y-auto pr-1 flex-grow space-y-4">
                    
                    <!-- Class Name -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Nombre de la Clase</span></label>
                        <input type="text" wire:model="name" placeholder="Ej: Spinning, CrossFit, Zumba..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                        @error('name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Descripción / Detalles</span></label>
                        <textarea wire:model="description" placeholder="Resumen o requisitos de la clase..." class="textarea textarea-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl h-20"></textarea>
                        @error('description') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Trainer -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Instructor / Profesor</span></label>
                        <select wire:model="trainer_id" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl">
                            <option value="">Seleccione un instructor</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                            @endforeach
                        </select>
                        @error('trainer_id') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Day and Capacity -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Día de la Semana</span></label>
                            <select wire:model="day_of_week" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl">
                                <option value="1">Lunes</option>
                                <option value="2">Martes</option>
                                <option value="3">Miércoles</option>
                                <option value="4">Jueves</option>
                                <option value="5">Viernes</option>
                                <option value="6">Sábado</option>
                                <option value="0">Domingo</option>
                            </select>
                            @error('day_of_week') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Capacidad Máxima</span></label>
                            <input type="number" wire:model="capacity" min="1" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('capacity') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Start & End Time -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Hora Inicio</span></label>
                            <input type="time" wire:model="start_time" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('start_time') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Hora Fin</span></label>
                            <input type="time" wire:model="end_time" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('end_time') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end gap-2">
                    <button wire:click="$set('showCreateEditModal', false)" class="btn btn-ghost border-zinc-850 hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-white">Cancelar</button>
                    <button wire:click="saveClass" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6">
                        Guardar
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ================= MODAL: ATTENDANCE CHECKLIST ================= -->
    @if($showAttendanceModal && $selectedClass)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-3xl w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showAttendanceModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Modal Title -->
                <div class="mb-6">
                    <span class="text-xs font-bold text-orange-500 uppercase tracking-wider">Control de Asistencia</span>
                    <h3 class="text-2xl font-black text-white leading-tight mt-0.5">{{ $selectedClass->name }}</h3>
                    <p class="text-xs text-zinc-500 mt-1">Schedules: {{ substr($selectedClass->start_time,0,5) }} - {{ substr($selectedClass->end_time,0,5) }} | Instructor: {{ $selectedClass->trainer->name ?? 'Sin asignar' }}</p>
                </div>

                <!-- Date & Member Search Panel -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 bg-zinc-950/45 p-4 border border-zinc-800/80 rounded-xl">
                    <!-- Date Picker -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-450 font-bold text-[10px] uppercase">Fecha de Asistencia</span></label>
                        <input type="date" wire:model.live="attendanceDate" class="input input-sm input-bordered bg-zinc-900 border-zinc-800 text-zinc-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg w-full" />
                    </div>

                    <!-- Search to Check-in on the fly -->
                    <div class="form-control relative">
                        <label class="label"><span class="label-text text-zinc-450 font-bold text-[10px] uppercase">Registrar Socio (Búsqueda Rápida)</span></label>
                        <input type="text" wire:model.live="attendanceSearch" placeholder="Buscar por nombre o email..." class="input input-sm input-bordered bg-zinc-900 border-zinc-800 text-zinc-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg w-full" />
                        
                        <!-- Search dropdown overlay -->
                        @if(!empty($searchMembers))
                            <div class="absolute left-0 right-0 top-full mt-1.5 bg-zinc-900 border border-zinc-800 rounded-xl shadow-xl z-50 max-h-48 overflow-y-auto p-1.5 space-y-1">
                                @foreach($searchMembers as $sm)
                                    <button wire:click="addMemberAttendance({{ $sm->id }})" class="w-full text-left p-2 hover:bg-zinc-800 rounded-lg flex items-center justify-between text-xs text-zinc-300">
                                        <div>
                                            <div class="font-bold text-white">{{ $sm->name }}</div>
                                            <div class="text-[10px] text-zinc-500">{{ $sm->email }}</div>
                                        </div>
                                        <span class="text-[9px] font-bold text-orange-500 bg-orange-500/10 border border-orange-500/25 px-1.5 py-0.5 rounded">Presente</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attendance Table Checklist -->
                <div class="overflow-y-auto pr-1 flex-grow">
                    <table class="table table-xs w-full text-zinc-300">
                        <thead>
                            <tr class="border-b border-zinc-850 text-zinc-450 font-bold uppercase text-[10px]">
                                <th class="py-3">Socio</th>
                                <th class="py-3 text-center">Tipo Registro</th>
                                <th class="py-3 text-right">Estado Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceRecords as $record)
                                <tr class="border-b border-zinc-800/40 hover:bg-zinc-800/10">
                                    <td class="py-3">
                                        <div class="font-extrabold text-white text-sm">{{ $record['name'] }}</div>
                                        <div class="text-[10px] text-zinc-500">{{ $record['email'] }}</div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge badge-xs {{ $record['is_booked'] ? 'badge-info bg-blue-500/10 border-blue-500/20 text-blue-400' : 'badge-warning bg-amber-500/10 border-amber-500/20 text-amber-400' }} font-bold">
                                            {{ $record['is_booked'] ? 'Reservado' : 'Asistencia Directa' }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button wire:click="toggleAttendance({{ $record['user_id'] }}, 'present')" class="btn btn-xs rounded {{ $record['status'] === 'present' || $record['status'] === 'checked_in' ? 'bg-emerald-500 hover:bg-emerald-600 text-white border-none font-bold' : 'btn-ghost border border-zinc-800 text-zinc-500 hover:bg-zinc-850' }}">
                                                Presente
                                            </button>
                                            <button wire:click="toggleAttendance({{ $record['user_id'] }}, 'absent')" class="btn btn-xs rounded {{ $record['status'] === 'absent' ? 'bg-rose-500 hover:bg-rose-600 text-white border-none font-bold' : 'btn-ghost border border-zinc-800 text-zinc-500 hover:bg-zinc-850' }}">
                                                Ausente
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-10 text-zinc-550">Sin reservas ni check-ins para este día</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Modal Actions -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end">
                    <button wire:click="$set('showAttendanceModal', false)" class="btn bg-zinc-850 hover:bg-zinc-800 text-white border-zinc-800 rounded-xl px-6">
                        Cerrar Registro
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
