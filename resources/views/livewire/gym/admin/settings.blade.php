<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" /></svg>
            Configuración del Gimnasio
        </h2>
    </x-slot>

    @if (session()->has('message'))
        <div class="alert bg-emerald-500/10 border-emerald-500/30 text-emerald-400 mb-6 rounded-xl" x-data="{ show: true }" x-show="show">
            <span class="font-bold text-sm">{{ session('message') }}</span>
            <button @click="show = false" class="btn btn-ghost btn-xs btn-circle">✕</button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Branding Section -->
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/25 flex items-center justify-center text-orange-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Marca y Apariencia</h3>
                    <p class="text-xs text-zinc-500">Personaliza la identidad visual de tu gimnasio.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Nombre del Gimnasio</span></label>
                    <input wire:model="gymName" type="text" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg" placeholder="Mi Gimnasio" />
                    @error('gymName') <span class="text-rose-400 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">URL del Logo</span></label>
                    <input wire:model="logoUrl" type="url" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg" placeholder="https://ejemplo.com/logo.png" />
                    @error('logoUrl') <span class="text-rose-400 text-xs mt-1">{{ $message }}</span> @enderror
                    @if($logoUrl)
                        <div class="mt-2 flex items-center gap-3">
                            <img src="{{ $logoUrl }}" alt="Logo Preview" class="w-12 h-12 rounded-lg object-cover border border-zinc-800" />
                            <span class="text-xs text-zinc-500">Vista previa</span>
                        </div>
                    @endif
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Color de Marca</span></label>
                    <div class="flex items-center gap-3">
                        <input wire:model="brandColor" type="color" class="w-12 h-10 rounded-lg cursor-pointer border border-zinc-800 bg-zinc-950" />
                        <input wire:model="brandColor" type="text" class="input input-sm input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg w-32 font-mono" />
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Política de Cancelación (horas antes)</span></label>
                    <input wire:model="cancellationPolicyHours" type="number" min="0" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg w-32" />
                </div>

                <button wire:click="saveBranding" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white font-bold rounded-xl w-full shadow-lg shadow-orange-500/10 mt-2">
                    Guardar Marca
                </button>
            </div>
        </div>

        <!-- MercadoPago Credentials -->
        <div class="bg-zinc-900/40 border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/25 flex items-center justify-center text-sky-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">MercadoPago</h3>
                    <p class="text-xs text-zinc-500">Credenciales para cobros online a tus socios.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Client ID</span></label>
                    <input wire:model="mpClientId" type="text" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg font-mono text-xs" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Client Secret</span></label>
                    <input wire:model="mpClientSecret" type="password" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg font-mono text-xs" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Access Token</span></label>
                    <input wire:model="mpAccessToken" type="password" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg font-mono text-xs" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-zinc-400 text-xs font-bold uppercase">Public Key</span></label>
                    <input wire:model="mpPublicKey" type="text" class="input input-bordered bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 rounded-lg font-mono text-xs" />
                </div>

                <div class="bg-zinc-950/50 border border-zinc-800/60 rounded-xl p-3 mt-2">
                    <p class="text-[10px] text-zinc-500 leading-relaxed">
                        <span class="text-amber-400 font-bold">⚠ Importante:</span> Estas credenciales se almacenan de forma segura en la base de datos del tenant. Nunca compartas tu Access Token. Podés obtener tus credenciales en 
                        <a href="https://www.mercadopago.com.ar/developers/panel/app" target="_blank" class="text-sky-400 underline hover:text-sky-300">developers.mercadopago.com</a>
                    </p>
                </div>

                <button wire:click="saveCredentials" class="btn bg-sky-500 hover:bg-sky-600 border-none text-white font-bold rounded-xl w-full shadow-lg shadow-sky-500/10 mt-2">
                    Guardar Credenciales
                </button>
            </div>
        </div>

    </div>
</div>
