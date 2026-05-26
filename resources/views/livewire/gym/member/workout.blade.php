<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Workout Player
            </h2>
            @if($started)
                <button wire:click="completeWorkout" class="btn btn-sm bg-emerald-500 hover:bg-emerald-600 border-none text-white rounded-xl font-bold shadow-lg shadow-emerald-500/10 gap-2">
                    Finalizar Entrenamiento
                </button>
            @endif
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

    @if(!$routine)
        <!-- No routine fallback -->
        <div class="max-w-md mx-auto text-center py-16 bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-8 shadow-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-zinc-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <h3 class="text-lg font-bold text-white mb-2">Sin Rutina Asignada</h3>
            <p class="text-xs text-zinc-500 leading-relaxed mb-6">Aún no tienes un plan de rutina asignado. Contacta a un instructor o administrador para que diseñe tu plan personalizado de musculación o cardio.</p>
            @php
                $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
            @endphp
            <a href="{{ $prefix }}/member/dashboard" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6 font-bold">Volver al Portal</a>
        </div>
    @else

        <!-- Select routine day tabs -->
        <div class="flex flex-wrap gap-2 border-b border-zinc-800 pb-4 mb-6">
            @foreach($routine->days as $day)
                <button wire:click="selectDay({{ $day->id }})" class="btn btn-sm rounded-xl font-bold {{ $selectedDayId === $day->id ? 'bg-orange-500 text-white border-none' : 'bg-zinc-800 text-zinc-350 border-zinc-700 hover:bg-zinc-700' }}" {{ $started ? 'disabled' : '' }}>
                    {{ $day->name }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- ================= LEFT COLUMN: EXERCISES LIST ================= -->
            <div class="lg:col-span-8 space-y-6">
                
                @if(!$started)
                    <div class="bg-zinc-900/40 border border-zinc-800 rounded-2xl p-6 shadow-xl text-center space-y-4">
                        <h3 class="text-lg font-black text-white">¿Listo para comenzar el día de entrenamiento?</h3>
                        <p class="text-xs text-zinc-500 max-w-sm mx-auto leading-relaxed">Una vez que comiences, podrás ir marcando cada serie como completada, ingresar el peso cargado y cronometrar los intervalos de descanso recomendados.</p>
                        <button wire:click="startWorkout" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-8 font-bold shadow-lg shadow-orange-500/10">
                            Comenzar Entrenamiento
                        </button>
                    </div>
                @endif

                @foreach($selectedDay->routineExercises as $re)
                    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-5 shadow-xl">
                        <!-- Exercise details -->
                        <div class="flex justify-between items-start mb-4 border-b border-zinc-800/80 pb-3">
                            <div>
                                <span class="badge badge-sm bg-orange-500/10 border border-orange-500/20 text-orange-400 font-extrabold mb-1">
                                    {{ $re->exercise->muscle_group }}
                                </span>
                                <h4 class="text-base font-black text-white">{{ $re->exercise->name }}</h4>
                            </div>
                            @if($re->exercise->instructions)
                                <div class="collapse collapse-arrow bg-zinc-950/20 border border-zinc-800/60 rounded-xl text-xs max-w-xs">
                                    <input type="checkbox" /> 
                                    <div class="collapse-title font-bold text-zinc-450 p-2 min-h-0 pl-3">Ver instrucciones</div>
                                    <div class="collapse-content text-zinc-500 p-3 pt-0 leading-relaxed border-t border-zinc-800/40">
                                        {{ $re->exercise->instructions }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sets Logger Table -->
                        <div class="overflow-x-auto">
                            <table class="table table-xs w-full text-zinc-300">
                                <thead>
                                    <tr class="border-b border-zinc-800 text-zinc-400 font-bold uppercase text-[9px]">
                                        <th class="py-2 pl-4">Serie</th>
                                        <th class="py-2 text-center">Objetivo</th>
                                        <th class="py-2 text-center w-24">Reps Log</th>
                                        <th class="py-2 text-center w-24">Peso (kg) Log</th>
                                        <th class="py-2 text-right pr-4">Completado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < $re->sets; $i++)
                                        @php
                                            $setCompleted = $workoutData[$re->exercise_id][$i]['completed'] ?? false;
                                        @endphp
                                        <tr class="border-b border-zinc-800/40 transition-all duration-150 {{ $setCompleted ? 'bg-emerald-500/5 text-emerald-500' : 'hover:bg-zinc-800/10' }}">
                                            <td class="py-2.5 pl-4 font-bold">#{{ $i + 1 }}</td>
                                            <td class="py-2.5 text-center text-zinc-500">
                                                {{ $re->reps }} reps @ {{ $re->weight }}kg
                                            </td>
                                            <!-- Reps input -->
                                            <td class="py-2.5 text-center">
                                                <input type="text" wire:model="workoutData.{{ $re->exercise_id }}.{{ $i }}.reps" class="input input-xs bg-zinc-950 border-zinc-800 text-center text-white rounded focus:border-orange-500 w-16" {{ !$started ? 'disabled' : '' }} />
                                            </td>
                                            <!-- Weight input -->
                                            <td class="py-2.5 text-center">
                                                <input type="number" step="0.5" wire:model="workoutData.{{ $re->exercise_id }}.{{ $i }}.weight" class="input input-xs bg-zinc-950 border-zinc-800 text-center text-white rounded focus:border-orange-500 w-16" {{ !$started ? 'disabled' : '' }} />
                                            </td>
                                            <!-- Completed Toggle -->
                                            <td class="py-2.5 text-right pr-4">
                                                <button type="button" wire:click="toggleSet({{ $re->exercise_id }}, {{ $i }})" class="btn btn-xs rounded {{ $setCompleted ? 'bg-emerald-500 text-white border-none' : 'bg-zinc-950 border-zinc-800 text-zinc-500 hover:bg-zinc-800' }}" {{ !$started ? 'disabled' : '' }}>
                                                    {{ $setCompleted ? '✓' : 'Log' }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                    </div>
                @endforeach

            </div>

            <!-- ================= RIGHT COLUMN: INTERACTIVE REST TIMER ================= -->
            <div class="lg:col-span-4 bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between"
                 x-data="{
                    timeRemaining: 0,
                    totalDuration: 60,
                    timerHandle: null,
                    isRunning: false,
                    startTimer(seconds) {
                        this.totalDuration = seconds;
                        this.timeRemaining = seconds;
                        this.isRunning = true;
                        
                        if (this.timerHandle) clearInterval(this.timerHandle);
                        
                        this.timerHandle = setInterval(() => {
                            if (this.timeRemaining > 0) {
                                this.timeRemaining--;
                            } else {
                                this.stopTimer();
                                // Play standard audio feedback or beep
                                if (window.AudioContext || window.webkitAudioContext) {
                                    let ctx = new (window.AudioContext || window.webkitAudioContext)();
                                    let osc = ctx.createOscillator();
                                    let gain = ctx.createGain();
                                    osc.connect(gain);
                                    gain.connect(ctx.destination);
                                    osc.frequency.setValueAtTime(440, ctx.currentTime);
                                    osc.start();
                                    setTimeout(() => osc.stop(), 500);
                                }
                            }
                        }, 1000);
                    },
                    stopTimer() {
                        this.isRunning = false;
                        if (this.timerHandle) clearInterval(this.timerHandle);
                    },
                    togglePause() {
                        if (this.isRunning) {
                            this.stopTimer();
                        } else {
                            this.startTimer(this.timeRemaining);
                        }
                    },
                    resetTimer() {
                        this.stopTimer();
                        this.timeRemaining = this.totalDuration;
                    }
                 }"
                 @start-rest-timer.window="startTimer($event.detail.seconds)">

                <div class="text-center">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Cronómetro de Descanso</h3>
                    
                    <!-- Circular visual display -->
                    <div class="relative w-36 h-36 mx-auto flex items-center justify-center bg-zinc-950 rounded-full border-4 border-zinc-800 shadow-inner">
                        <!-- Countdown text -->
                        <div class="text-center">
                            <span class="text-3xl font-mono font-black text-white" x-text="timeRemaining">0</span>
                            <span class="text-[10px] text-zinc-500 font-bold block uppercase tracking-wider">segundos</span>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="w-full bg-zinc-950 rounded-full h-1.5 mt-6 border border-zinc-800 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-full transition-all duration-300"
                             :style="'width: ' + ((timeRemaining / totalDuration) * 100) + '%'"></div>
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center justify-center gap-2 mt-6">
                        <button type="button" @click="togglePause()" class="btn btn-sm bg-zinc-800 hover:bg-zinc-700 text-zinc-350 font-bold rounded-lg px-4" x-text="isRunning ? 'Pausar' : 'Reanudar'"></button>
                        <button type="button" @click="resetTimer()" class="btn btn-sm btn-ghost text-zinc-500 hover:text-zinc-300" title="Reiniciar">Reiniciar</button>
                    </div>
                </div>

                <!-- Footer Summary info -->
                <div class="border-t border-zinc-800/60 pt-4 mt-8 space-y-4">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-zinc-400 font-bold uppercase tracking-wider">Objetivo de Descanso:</span>
                        <span class="font-extrabold text-white" x-text="totalDuration + 's'">60s</span>
                    </div>
                    @if($started)
                        <button wire:click="completeWorkout" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl w-full font-bold shadow-lg shadow-orange-500/10 mt-2">
                            Finalizar Entrenamiento
                        </button>
                    @endif
                </div>

            </div>

        </div>

    @endif
</div>
