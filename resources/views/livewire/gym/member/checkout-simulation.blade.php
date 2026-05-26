<div class="max-w-md mx-auto my-12 bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-500 to-amber-500"></div>

    @if($simulationStatus === null)
        <div class="text-center mb-6">
            <span class="text-[9px] font-black text-orange-500 uppercase tracking-widest bg-orange-500/10 border border-orange-500/25 px-2.5 py-1 rounded-lg">Sandbox de Pruebas</span>
            <h2 class="text-2xl font-black text-white mt-4">Simulación MercadoPago</h2>
            <p class="text-xs text-zinc-400 mt-1">Estás simulando una pasarela de pago para el cobro de tu membresía.</p>
        </div>

        <div class="bg-zinc-950/50 p-4 border border-zinc-800 rounded-2xl mb-6 text-xs space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-zinc-500">Plan Seleccionado:</span>
                <span class="font-bold text-white">{{ $plan->name }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-zinc-500">Duración:</span>
                <span class="font-bold text-zinc-300 font-mono">{{ $plan->duration_months }} {{ $plan->duration_months === 1 ? 'Mes' : 'Meses' }}</span>
            </div>
            <div class="flex justify-between items-center border-t border-zinc-800/80 pt-3">
                <span class="text-zinc-500 font-bold">Total a Pagar:</span>
                <span class="text-lg font-black text-white">${{ number_format($plan->price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="space-y-3">
            <button wire:click="processSimulation('success')" class="btn w-full bg-emerald-500 hover:bg-emerald-600 border-none text-white font-bold rounded-xl gap-2 shadow-lg shadow-emerald-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Aprobar Pago (Simulado)
            </button>
            <button wire:click="processSimulation('failure')" class="btn w-full bg-rose-500/10 hover:bg-rose-500 border border-rose-500/25 hover:border-none text-rose-400 hover:text-white font-bold rounded-xl gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                Declinar Pago
            </button>
            <button wire:click="processSimulation('pending')" class="btn w-full bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-zinc-300 font-semibold rounded-xl">
                Simular Pago Pendiente
            </button>
        </div>
    @endif

    @if($simulationStatus === 'success')
        <div class="text-center py-6">
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <h2 class="text-2xl font-black text-white">¡Pago Aprobado!</h2>
            <p class="text-xs text-zinc-400 max-w-xs mx-auto mt-2">La simulación se ha procesado exitosamente. Tu membresía ha sido extendida y activada.</p>
            
            <button wire:click="goBack" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl mt-8 px-8 shadow-lg shadow-orange-500/10">
                Volver a mi Portal
            </button>
        </div>
    @endif

    @if($simulationStatus === 'failure')
        <div class="text-center py-6">
            <div class="w-16 h-16 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-500 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <h2 class="text-2xl font-black text-white">Pago Rechazado</h2>
            <p class="text-xs text-zinc-400 max-w-xs mx-auto mt-2">Tu entidad bancaria o tarjeta rechazó la operación. Por favor intenta con otro método.</p>
            
            <button wire:click="goBack" class="btn bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700 rounded-xl mt-8 px-8">
                Volver a mi Portal
            </button>
        </div>
    @endif

    @if($simulationStatus === 'pending')
        <div class="text-center py-6">
            <div class="w-16 h-16 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-500 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <h2 class="text-2xl font-black text-white">Pago Pendiente</h2>
            <p class="text-xs text-zinc-400 max-w-xs mx-auto mt-2">Tu transacción se encuentra bajo revisión o esperando acreditación.</p>
            
            <button wire:click="goBack" class="btn bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700 rounded-xl mt-8 px-8">
                Volver a mi Portal
            </button>
        </div>
    @endif
</div>
