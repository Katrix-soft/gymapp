<div class="p-6 max-w-5xl mx-auto" x-data="biometricKiosk()">
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11a13.917 13.917 0 00-2.3-7.551m3.854 8.046a12.09 12.09 0 011.07 1.05m-3.97-1.05a12.47 12.47 0 00-1.123-8.32m2.91 8.32a12.422 12.422 0 01-1.082 5.53m1.538-12.2a12.093 12.093 0 01-1.08 1.058m1.586-.072A12.09 12.09 0 0115 11c0 3.06 1.007 5.885 2.71 8.12M9 11V9m15-1a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Kiosco de Acceso Biométrico
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
        
        <!-- Left: Action Area (3 Cols) -->
        <div class="lg:col-span-3 bg-zinc-900/40 backdrop-blur-md border border-zinc-800 rounded-2xl p-8 shadow-xl flex flex-col items-center justify-center min-h-[420px]">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-6 text-center">Recepción Autónoma</h3>
            
            <!-- Biometric Kiosk Button -->
            <button @click="authenticateBiometrics()" 
                    :disabled="loading"
                    class="relative group flex flex-col items-center justify-center p-10 rounded-full bg-zinc-950/80 border border-zinc-800 hover:border-orange-500/50 shadow-2xl transition-all duration-300 w-56 h-56 focus:outline-none">
                
                <!-- Glowing Aura -->
                <div class="absolute inset-0 rounded-full bg-orange-500/5 group-hover:bg-orange-500/10 blur-xl transition-all duration-300"></div>
                
                <!-- Inner Animated Ring -->
                <div class="absolute inset-2 rounded-full border-2 border-dashed border-zinc-800 group-hover:border-orange-500/40 animate-[spin_20s_linear_infinite]" x-show="!loading"></div>
                <div class="absolute inset-2 rounded-full border-2 border-orange-500 border-t-transparent animate-spin" x-show="loading"></div>

                <!-- Fingerprint Icon -->
                <svg class="w-20 h-20 text-orange-500 transition-all duration-300 transform group-hover:scale-105" 
                     :class="loading ? 'animate-pulse text-orange-400' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11a13.917 13.917 0 00-2.3-7.551m3.854 8.046a12.09 12.09 0 011.07 1.05m-3.97-1.05a12.47 12.47 0 00-1.123-8.32m2.91 8.32a12.422 12.422 0 01-1.082 5.53m1.538-12.2a12.093 12.093 0 01-1.08 1.058m1.586-.072A12.09 12.09 0 0115 11c0 3.06 1.007 5.885 2.71 8.12M9 11V9m15-1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>

                <span class="text-xs font-black text-zinc-400 group-hover:text-white uppercase tracking-widest mt-4">
                    <span x-text="loading ? 'Verificando...' : 'Presione para Ingresar'"></span>
                </span>
            </button>

            <!-- Status Info -->
            <p class="text-xs text-zinc-500 mt-6 text-center max-w-xs" x-text="statusText"></p>
        </div>

        <!-- Right: Scan Result Display (2 Cols) -->
        <div class="lg:col-span-2 bg-zinc-900/40 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Resultado del Acceso</h3>
                
                @if($scannedUser)
                    <div class="text-center py-4">
                        <div class="inline-flex p-3 rounded-full mb-4 {{ $scanStatus === 'success' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                            @if($scanStatus === 'success')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @endif
                        </div>

                        <h4 class="font-extrabold text-lg text-white leading-tight mb-4">{{ $scanMessage }}</h4>
                        
                        <div class="bg-zinc-950/60 border border-zinc-800 rounded-xl p-4 text-left flex items-center gap-3">
                            <img src="{{ $scannedUser['avatar'] }}" alt="Avatar" class="w-12 h-12 rounded-xl shrink-0 border border-zinc-800" />
                            <div>
                                <h5 class="font-bold text-sm text-zinc-100">{{ $scannedUser['name'] }}</h5>
                                <p class="text-xs text-zinc-500">{{ $scannedUser['email'] }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $scanStatus === 'success' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        Plan: {{ $scannedUser['plan_name'] }}
                                    </span>
                                    @if($scannedUser['end_date'])
                                        <span class="text-[9px] text-zinc-500">Vence: {{ $scannedUser['end_date'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Manual clear timer fallback / button -->
                        <button wire:click="clearScan" class="btn btn-xs bg-zinc-850 hover:bg-zinc-850 text-zinc-400 border-none rounded mt-4">
                            Limpiar Pantalla
                        </button>
                    </div>
                @else
                    <div class="text-center py-12 text-zinc-500">
                        <svg class="w-10 h-10 mx-auto text-zinc-850 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-xs">Coloque su huella en el lector o presione para iniciar la verificación.</p>
                    </div>
                @endif
            </div>

            <div class="border-t border-zinc-800/60 pt-4 text-center">
                <span class="text-[10px] text-zinc-500 block uppercase font-bold tracking-widest">Kiosco de Entrada</span>
            </div>
        </div>

    </div>

    <!-- Bottom Log: Biometric entries only -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Últimos Accesos Biométricos</h3>
        
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-500 text-[10px] uppercase font-bold">
                        <th>Socio</th>
                        <th>Fecha y Hora</th>
                        <th>Método</th>
                        <th>Ingreso</th>
                    </tr>
                </thead>
                <tbody class="text-zinc-300">
                    @forelse($recentCheckins as $checkin)
                        <tr class="border-b border-zinc-850/50">
                            <td>
                                <div class="font-bold text-sm text-white">{{ $checkin->user->name ?? 'Socio' }}</div>
                                <div class="text-xs text-zinc-500">{{ $checkin->user->email ?? '' }}</div>
                            </td>
                            <td class="text-xs text-zinc-400">
                                {{ \Carbon\Carbon::parse($checkin->checkin_time)->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i:s') }}
                            </td>
                            <td>
                                <span class="badge badge-sm border-none font-bold text-[9px] uppercase py-1 rounded bg-orange-500/10 text-orange-400">
                                    {{ $checkin->method }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-sm border-none font-extrabold text-[9px] uppercase py-1 rounded {{ $checkin->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                    {{ $checkin->status === 'active' ? 'Autorizado' : 'Rechazado' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-zinc-500 text-xs">No hay ingresos biométricos registrados hoy</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function biometricKiosk() {
            return {
                biometrics: null,
                loading: false,
                statusText: 'Cargando lector de huella/rostro...',
                
                init() {
                    setTimeout(() => {
                        if (!window.KatrixBiometrics) {
                            this.statusText = 'Servicios de biometría no inicializados en este equipo.';
                            return;
                        }
                        
                        this.biometrics = new window.KatrixBiometrics({
                            appName: '{{ tenant() ? addslashes(tenant("name")) : "SaaS Gym Central" }}'
                        });
                        
                        this.statusText = 'Lector biométrico listo. Presione el sensor.';
                    }, 500);
                },

                async authenticateBiometrics() {
                    if (!this.biometrics) {
                        alert('El lector biométrico no está listo o requiere conexión HTTPS segura.');
                        return;
                    }

                    this.loading = true;
                    this.statusText = 'Verificando huella dactilar/rostro... Mire la cámara o coloque su dedo.';

                    try {
                        const result = await this.biometrics.authenticate();
                        this.loading = false;
                        
                        if (result.success) {
                            this.statusText = 'Acceso verificado. Procesando ingreso...';
                            const credId = this.biometrics.getStatus().credentialId;
                            @this.call('processBiometricCheckin', credId);
                            
                            // Auto clear details after 5 seconds to keep kiosk running unattended
                            setTimeout(() => {
                                @this.call('clearScan');
                                this.statusText = 'Lector biométrico listo. Presione el sensor.';
                            }, 5000);
                        } else {
                            this.statusText = 'Lector biométrico listo. Intente de nuevo.';
                            alert(`Error de lectura: ${result.error.message}`);
                        }
                    } catch (err) {
                        this.loading = false;
                        this.statusText = 'Lector biométrico listo. Intente de nuevo.';
                        console.error(err);
                    }
                }
            }
        }
    </script>
</div>
