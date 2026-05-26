<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header area -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Gestor de Gimnasios SaaS (Tenants)
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Supervisión y control de instancias de base de datos multitenant para clientes SaaS.</p>
            </div>
            <button wire:click="openCreateModal" class="btn bg-orange-500 hover:bg-orange-600 text-white border-none rounded-xl font-bold shadow-lg shadow-orange-500/20">
                + Crear Instancia Gimnasio
            </button>
        </div>

        <!-- Notification banner -->
        @if (session()->has('message'))
            <div class="alert alert-success bg-emerald-500/10 border-emerald-500/30 text-emerald-500 mb-6 rounded-xl flex items-center justify-between p-4 shadow-lg">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="text-sm font-semibold">{{ session('message') }}</span>
                </div>
                <button class="btn btn-ghost btn-xs btn-circle text-emerald-500">✕</button>
            </div>
        @endif

        <!-- Filter bar -->
        <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xl mb-6">
            <div class="relative w-full md:max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-gray-450" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por subdominio/slug..." class="input input-bordered w-full bg-gray-50 dark:bg-zinc-950 border-gray-300 dark:border-zinc-800 pl-10 text-gray-900 dark:text-gray-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
            </div>
        </div>

        <!-- Table view -->
        <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-gray-700 dark:text-zinc-300">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-zinc-800 text-gray-500 dark:text-zinc-400 font-bold bg-gray-50 dark:bg-zinc-950/20 text-xs">
                            <th class="py-4 pl-6">Slug ID (Database)</th>
                            <th class="py-4">Nombre del Gimnasio</th>
                            <th class="py-4">Dominio de Acceso</th>
                            <th class="py-4">Fecha de Creación</th>
                            <th class="py-4 text-right pr-6">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                            <tr class="border-b border-gray-100 dark:border-zinc-800/60 hover:bg-gray-50 dark:hover:bg-zinc-800/20 transition-all duration-150 text-sm">
                                <td class="py-4 pl-6 font-mono font-bold text-gray-900 dark:text-white">
                                    {{ $tenant->id }}
                                </td>
                                <td class="py-4 font-semibold text-gray-800 dark:text-zinc-200">
                                    {{ $tenant->name ?? 'N/A' }}
                                </td>
                                <td class="py-4 font-mono text-xs text-orange-500 dark:text-orange-500 hover:underline">
                                    @php
                                        $domain = $tenant->domains->first()->domain ?? '';
                                    @endphp
                                    <a href="http://{{ $domain }}:8000" target="_blank">{{ $domain }}</a>
                                </td>
                                <td class="py-4 text-xs text-gray-500">
                                    {{ $tenant->created_at ? $tenant->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="py-4 text-right pr-6">
                                    <button onclick="confirm('¿Eliminar de forma permanente este Gimnasio y su base de datos?') || event.stopImmediatePropagation()" wire:click="deleteTenant('{{ $tenant->id }}')" class="btn btn-xs bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white border-rose-500/20 hover:border-none rounded-lg font-bold">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-16 text-gray-400">
                                    No se registran instancias creadas en la base de datos central
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-950/20">
                {{ $tenants->links() }}
            </div>
        </div>

        <!-- ================= MODAL: CREATE TENANT ================= -->
        @if($showCreateModal)
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative overflow-hidden flex flex-col">
                    
                    <button wire:click="$set('showCreateModal', false)" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6 pr-8">
                        Registrar Instancia Gimnasio (SaaS Tenant)
                    </h3>

                    <div class="space-y-4">
                        <!-- Slug/Subdomain -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-gray-600 dark:text-zinc-400 font-semibold text-xs uppercase">Subdominio / Slug (Ej: gymdemo)</span></label>
                            <input type="text" wire:model="tenantId" placeholder="Ej: fitzone" class="input input-bordered w-full bg-gray-50 dark:bg-zinc-950 border-gray-300 dark:border-zinc-800 text-gray-900 dark:text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('tenantId') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Gym Name -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-gray-600 dark:text-zinc-400 font-semibold text-xs uppercase">Nombre del Gimnasio</span></label>
                            <input type="text" wire:model="gymName" placeholder="Ej: Fit Zone Gym Premium" class="input input-bordered w-full bg-gray-50 dark:bg-zinc-950 border-gray-300 dark:border-zinc-800 text-gray-900 dark:text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('gymName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 border-t border-gray-200 dark:border-zinc-800 pt-4 flex justify-end gap-2">
                        <button wire:click="$set('showCreateModal', false)" class="btn btn-ghost border-gray-300 dark:border-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl text-gray-500 dark:text-zinc-400">Cancelar</button>
                        <button wire:click="saveTenant" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6 font-bold shadow-lg shadow-orange-500/10">
                            Registrar y Desplegar
                        </button>
                    </div>

                </div>
            </div>
        @endif

    </div>
</div>
