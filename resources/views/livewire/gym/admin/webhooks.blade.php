<div class="p-6 max-w-6xl mx-auto">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Auditoría de Webhooks MercadoPago
            </h2>
            <button wire:click="clearAllWebhooks" 
                    onclick="confirm('¿Estás seguro de que deseas borrar todo el historial de webhooks?') || event.stopImmediatePropagation()" 
                    class="btn btn-sm bg-red-950/40 hover:bg-red-900 border border-red-800 text-red-200 rounded-lg px-4 font-bold">
                Vaciar Registro
            </button>
        </div>
    </x-slot>

    <!-- Notification Alert -->
    @if (session()->has('message'))
        <div class="alert alert-info bg-zinc-950 border border-zinc-800 text-zinc-300 mb-6 rounded-xl flex items-center justify-between shadow-lg">
            <span>{{ session('message') }}</span>
            <button class="btn btn-ghost btn-xs btn-circle text-zinc-400">✕</button>
        </div>
    @endif

    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Payloads Recibidos de MercadoPago</h3>
        
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-500 text-[10px] uppercase font-bold">
                        <th>ID de Transacción / Webhook</th>
                        <th>Fecha de Recepción</th>
                        <th>Tema (Topic)</th>
                        <th>Recurso</th>
                        <th>Estado de Procesamiento</th>
                        <th class="text-right">Detalles</th>
                    </tr>
                </thead>
                <tbody class="text-zinc-300">
                    @forelse($webhooks as $wh)
                        <tr class="border-b border-zinc-850/50 hover:bg-zinc-800/10">
                            <td class="font-mono text-xs text-white">
                                {{ $wh->webhook_id ?: 'N/A' }}
                            </td>
                            <td class="text-xs text-zinc-400">
                                {{ \Carbon\Carbon::parse($wh->created_at)->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i:s') }}
                            </td>
                            <td>
                                <span class="badge badge-sm border-none font-bold text-[9px] uppercase py-1 rounded {{ $wh->topic === 'payment' ? 'bg-orange-500/10 text-orange-400' : 'bg-zinc-800 text-zinc-400' }}">
                                    {{ $wh->topic ?: 'desconocido' }}
                                </span>
                            </td>
                            <td class="font-mono text-xs text-zinc-500">
                                {{ $wh->resource ?: 'N/A' }}
                            </td>
                            <td>
                                @if($wh->status === 'processed')
                                    <span class="badge badge-sm border-none font-extrabold text-[9px] uppercase py-1 rounded bg-emerald-500/10 text-emerald-400">
                                        Procesado Exitoso
                                    </span>
                                @elseif($wh->status === 'failed')
                                    <span class="badge badge-sm border-none font-extrabold text-[9px] uppercase py-1 rounded bg-rose-500/10 text-rose-400" title="{{ $wh->error_message }}">
                                        Error
                                    </span>
                                @else
                                    <span class="badge badge-sm border-none font-extrabold text-[9px] uppercase py-1 rounded bg-yellow-500/10 text-yellow-400">
                                        Recibido
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button wire:click="showPayload({{ $wh->id }})" class="btn btn-xs bg-zinc-800 hover:bg-zinc-700 text-zinc-300 border-zinc-700 font-bold rounded">
                                    Ver JSON
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-zinc-500 text-xs">No se han recibido notificaciones de MercadoPago en esta base de datos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $webhooks->links() }}
        </div>
    </div>

    <!-- Payload Details Modal -->
    @if($showPayloadModal && $selectedWebhook)
        <dialog class="modal modal-open">
            <div class="modal-box bg-zinc-950 border border-zinc-800 rounded-3xl p-6 max-w-2xl">
                <h3 class="font-black text-lg text-white mb-2">Detalles del Webhook</h3>
                <p class="text-xs text-zinc-400 mb-4">Payload recibido el {{ \Carbon\Carbon::parse($selectedWebhook->created_at)->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i:s') }}</p>
                
                @if($selectedWebhook->status === 'failed')
                    <div class="alert alert-error bg-rose-500/10 border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl mb-4 font-mono">
                        <strong>Error:</strong> {{ $selectedWebhook->error_message }}
                    </div>
                @endif

                <div class="bg-zinc-900 border border-zinc-850 rounded-xl p-4 overflow-y-auto max-h-[350px]">
                    <pre class="text-xs text-zinc-400 font-mono select-all leading-normal">{{ json_encode(json_decode($selectedWebhook->payload), JSON_PRETTY_PRINT) }}</pre>
                </div>
                
                <div class="modal-action">
                    <button wire:click="closePayloadModal" class="btn btn-sm bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border-zinc-700 rounded-lg px-6 font-bold">
                        Cerrar
                    </button>
                </div>
            </div>
        </dialog>
    @endif
</div>
