<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            Mis Clases
        </h2>
    </x-slot>

    @if (session()->has('message'))
        <div class="alert bg-emerald-500/10 border-emerald-500/30 text-emerald-400 mb-6 rounded-xl" x-data="{ show: true }" x-show="show">
            <span class="font-bold text-sm">{{ session('message') }}</span>
            <button @click="show = false" class="btn btn-ghost btn-xs btn-circle">✕</button>
        </div>
    @endif

    <!-- Today's Classes -->
    <div class="mb-8">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">📅 Clases de Hoy ({{ now()->translatedFormat('l, d M') }})</h3>
        @if($todayClasses->isEmpty())
            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-8 text-center">
                <p class="text-zinc-500 text-sm">No tienes clases programadas para hoy.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($todayClasses as $class)
                    <div wire:click="selectClass({{ $class->id }})" class="cursor-pointer bg-zinc-900/40 border {{ $selectedClassId == $class->id ? 'border-orange-500 shadow-lg shadow-orange-500/10' : 'border-zinc-800/80 hover:border-zinc-700' }} rounded-2xl p-5 transition-all duration-200">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-white font-bold text-lg">{{ $class->name }}</h4>
                            <span class="badge badge-sm bg-orange-500/10 border-orange-500/20 text-orange-400 font-mono">{{ substr($class->start_time, 0, 5) }} - {{ substr($class->end_time, 0, 5) }}</span>
                        </div>
                        <p class="text-zinc-500 text-xs">{{ $class->description }}</p>
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-zinc-800/60">
                            <span class="text-[10px] text-zinc-500 uppercase tracking-wider">Capacidad: {{ $class->capacity }}</span>
                            <button class="btn btn-xs bg-orange-500/10 border-orange-500/20 text-orange-400 hover:bg-orange-500 hover:text-white rounded-lg">Asistencia</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Attendance Panel -->
    @if($selectedClass)
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-white">Asistencia: {{ $selectedClass->name }}</h3>
                    <p class="text-xs text-zinc-500">Marca presente/ausente a cada alumno inscripto.</p>
                </div>
                <input type="date" wire:model.live="attendanceDate" class="input input-sm input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg w-auto" />
            </div>

            @if(empty($attendanceRecords))
                <div class="text-center py-8">
                    <p class="text-zinc-500 text-sm">No hay alumnos inscriptos para esta clase en la fecha seleccionada.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr class="border-zinc-800 text-zinc-500">
                                <th class="text-xs uppercase">Alumno</th>
                                <th class="text-xs uppercase">Email</th>
                                <th class="text-xs uppercase text-center">Estado</th>
                                <th class="text-xs uppercase text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceRecords as $record)
                                <tr class="border-zinc-800/60 hover:bg-zinc-800/20">
                                    <td class="text-white font-semibold text-sm">{{ $record['name'] }}</td>
                                    <td class="text-zinc-400 text-xs">{{ $record['email'] }}</td>
                                    <td class="text-center">
                                        @if($record['status'] === 'present')
                                            <span class="badge badge-sm bg-emerald-500/10 border-emerald-500/20 text-emerald-400">Presente</span>
                                        @else
                                            <span class="badge badge-sm bg-rose-500/10 border-rose-500/20 text-rose-400">Ausente</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="toggleAttendance({{ $record['user_id'] }}, 'present')" class="btn btn-xs {{ $record['status'] === 'present' ? 'bg-emerald-500 text-white' : 'bg-zinc-800 text-zinc-400 hover:bg-emerald-500 hover:text-white' }} border-none rounded-lg">✓</button>
                                            <button wire:click="toggleAttendance({{ $record['user_id'] }}, 'absent')" class="btn btn-xs {{ $record['status'] === 'absent' ? 'bg-rose-500 text-white' : 'bg-zinc-800 text-zinc-400 hover:bg-rose-500 hover:text-white' }} border-none rounded-lg">✕</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    <!-- All Classes Schedule -->
    <div>
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">📋 Todas mis clases semanales</h3>
        @if($allClasses->isEmpty())
            <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-8 text-center">
                <p class="text-zinc-500 text-sm">No tienes clases asignadas aún.</p>
            </div>
        @else
            <div class="overflow-x-auto bg-zinc-900/40 border border-zinc-800/80 rounded-2xl">
                <table class="table table-sm w-full">
                    <thead>
                        <tr class="border-zinc-800 text-zinc-500">
                            <th class="text-xs uppercase">Día</th>
                            <th class="text-xs uppercase">Clase</th>
                            <th class="text-xs uppercase">Horario</th>
                            <th class="text-xs uppercase text-center">Capacidad</th>
                            <th class="text-xs uppercase text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allClasses as $class)
                            <tr class="border-zinc-800/60 hover:bg-zinc-800/20">
                                <td><span class="badge badge-sm bg-zinc-800 border-zinc-700 text-zinc-300">{{ $weekdays[$class->day_of_week] ?? '?' }}</span></td>
                                <td class="text-white font-semibold text-sm">{{ $class->name }}</td>
                                <td class="text-zinc-400 text-xs font-mono">{{ substr($class->start_time, 0, 5) }} - {{ substr($class->end_time, 0, 5) }}</td>
                                <td class="text-center text-zinc-400 text-sm">{{ $class->capacity }}</td>
                                <td class="text-right">
                                    <button wire:click="selectClass({{ $class->id }})" class="btn btn-xs bg-orange-500/10 border-orange-500/20 text-orange-400 hover:bg-orange-500 hover:text-white rounded-lg">Asistencia</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
