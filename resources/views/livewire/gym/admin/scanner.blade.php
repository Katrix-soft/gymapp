<div class="p-6 max-w-6xl mx-auto" x-data="qrScanner()">
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            Escáner de Acceso QR
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Left Column: Camera Scanner -->
        <div class="lg:col-span-2 bg-zinc-900/40 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 shadow-xl relative">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Cámara de Recepción</h3>
            
            <!-- Scanner Viewport Container -->
            <div class="bg-black/80 rounded-xl overflow-hidden aspect-video relative flex flex-col items-center justify-center border border-zinc-800 shadow-inner">
                <div id="reader" class="w-full h-full max-h-[380px]"></div>
                
                <!-- Overlay message when scanner is offline -->
                <div x-show="!isActive" class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-950/90 p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-zinc-700 mb-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h4 class="font-extrabold text-white text-sm">Cámara Inactiva</h4>
                    <p class="text-xs text-zinc-500 mt-1 max-w-xs">Inicia la cámara para comenzar a escanear los pases QR de los socios.</p>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex justify-center gap-2 mt-4">
                <button @click="startScanner()" x-show="!isActive" class="btn btn-sm bg-orange-500 hover:bg-orange-600 border-none text-white rounded-lg px-6 font-bold">
                    Iniciar Cámara
                </button>
                <button @click="stopScanner()" x-show="isActive" class="btn btn-sm bg-zinc-800 hover:bg-zinc-700 border-zinc-700 text-zinc-300 rounded-lg px-6 font-bold">
                    Detener Cámara
                </button>
            </div>
        </div>

        <!-- Right Column: Scan Result -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Resultado del Escaneo</h3>
                
                @if($scannedUser)
                    <div class="text-center py-4">
                        <!-- Success / Error Visual indicator -->
                        <div class="inline-flex p-3 rounded-full mb-4 {{ $scanStatus === 'success' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                            @if($scanStatus === 'success')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>

                        <!-- Card text alerts -->
                        <h4 class="font-extrabold text-lg text-white leading-tight">{{ $scanMessage }}</h4>
                        
                        <!-- Member Profile Box -->
                        <div class="bg-zinc-950/60 border border-zinc-800 rounded-xl p-4 mt-6 text-left flex items-center gap-3">
                            <img src="{{ $scannedUser['avatar'] }}" alt="Avatar" class="w-12 h-12 rounded-xl shrink-0 border border-zinc-800" />
                            <div>
                                <h5 class="font-bold text-sm text-zinc-100">{{ $scannedUser['name'] }}</h5>
                                <p class="text-xs text-zinc-500">{{ $scannedUser['email'] }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $scanStatus === 'success' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        Plan: {{ $scannedUser['plan_name'] }}
                                    </span>
                                    @if($scannedUser['end_date'])
                                        <span class="text-[9px] text-zinc-500">Expira: {{ $scannedUser['end_date'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Clear details button -->
                        <button wire:click="clearScan" class="btn btn-xs bg-zinc-800 hover:bg-zinc-700 text-zinc-400 border-none rounded mt-4">
                            Limpiar Resultado
                        </button>
                    </div>
                @else
                    <!-- Idle state -->
                    <div class="text-center py-12 text-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-zinc-800 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01" />
                        </svg>
                        <p class="text-xs">Esperando escaneo...</p>
                    </div>
                @endif
            </div>

            <div class="border-t border-zinc-800/60 pt-4 text-center">
                <span class="text-[10px] text-zinc-500 block uppercase font-bold tracking-widest">Recepción Activa</span>
            </div>
        </div>

    </div>

    <!-- Bottom: Recent Checkins Log -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Últimos Ingresos (Historial)</h3>
        
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-500 text-[10px] uppercase font-bold">
                        <th>Socio</th>
                        <th>Fecha y Hora</th>
                        <th>Método</th>
                        <th>Estado de Membresía</th>
                    </tr>
                </thead>
                <tbody class="text-zinc-300">
                    @forelse($recentCheckins as $checkin)
                        <tr class="border-b border-zinc-850/50">
                            <td>
                                <div class="font-bold text-sm text-white">{{ $checkin->user->name ?? 'Invitado' }}</div>
                                <div class="text-xs text-zinc-500">{{ $checkin->user->email ?? '' }}</div>
                            </td>
                            <td class="text-xs text-zinc-400">
                                {{ \Carbon\Carbon::parse($checkin->checkin_time)->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i:s') }}
                            </td>
                            <td>
                                <span class="badge badge-sm border-none font-bold text-[9px] uppercase py-1 rounded {{ $checkin->method === 'qr_code' ? 'bg-orange-500/10 text-orange-400' : 'bg-emerald-500/10 text-emerald-400' }}">
                                    {{ $checkin->method }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-sm border-none font-extrabold text-[9px] uppercase py-1 rounded {{ $checkin->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                    {{ $checkin->status === 'active' ? 'Autorizado' : 'Vencido' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-zinc-500 text-xs">No hay registros de ingreso registrados hoy</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- html5-qrcode scanning library scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" 
            integrity="sha512-r6rDA7W6ZeQhvl8S7yRV0VUkyIEwJlAeC5CnRlTOdec3q6vfLkbDHTm5ZkTCgq83vOkONjvuwQLvT5j0SiTlkQ==" 
            crossorigin="anonymous" 
            referrerpolicy="no-referrer"></script>

    <script>
        function qrScanner() {
            return {
                isActive: false,
                html5Qrcode: null,
                init() {
                    // Pre-allocate reader config
                },
                startScanner() {
                    this.isActive = true;
                    this.$nextTick(() => {
                        this.html5Qrcode = new Html5Qrcode("reader");
                        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                            // Stop camera after scanning successfully
                            this.stopScanner();
                            // Process code in Livewire
                            @this.call('processScan', decodedText);
                        };
                        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                        this.html5Qrcode.start(
                            { facingMode: "environment" }, 
                            config, 
                            qrCodeSuccessCallback
                        ).catch(err => {
                            console.error("Camera start failed: ", err);
                            this.isActive = false;
                        });
                    });
                },
                stopScanner() {
                    if (this.html5Qrcode && this.isActive) {
                        this.html5Qrcode.stop().then(() => {
                            this.isActive = false;
                            this.html5Qrcode = null;
                        }).catch(err => {
                            console.error("Camera stop failed: ", err);
                        });
                    } else {
                        this.isActive = false;
                    }
                }
            }
        }
    </script>
</div>
