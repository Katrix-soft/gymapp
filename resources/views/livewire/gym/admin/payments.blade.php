<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pagos y Membresías
            </h2>
            <div class="flex items-center gap-2">
                @if($activeTab === 'plans')
                    <button wire:click="openPlanCreateModal" class="btn bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700 rounded-xl gap-2">
                        + Nuevo Plan
                    </button>
                @endif
                <button wire:click="openAssignModal" class="btn bg-orange-500 hover:bg-orange-600 text-white border-none rounded-xl shadow-lg shadow-orange-500/20 gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Asignar Pase / Registrar Pago
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

    <!-- Navigation Tabs -->
    <div class="flex gap-2 border-b border-zinc-800/80 mb-6 pb-px">
        <button wire:click="switchTab('transactions')" class="px-5 py-3 text-sm font-bold uppercase tracking-wider transition-all duration-200 border-b-2 {{ $activeTab === 'transactions' ? 'border-orange-500 text-white' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
            Historial de Transacciones
        </button>
        <button wire:click="switchTab('plans')" class="px-5 py-3 text-sm font-bold uppercase tracking-wider transition-all duration-200 border-b-2 {{ $activeTab === 'plans' ? 'border-orange-500 text-white' : 'border-transparent text-zinc-500 hover:text-zinc-300' }}">
            Plantillas de Planes
        </button>
    </div>

    <!-- ================= TAB: TRANSACTIONS LIST ================= -->
    @if($activeTab === 'transactions')
        <!-- Search and Filters Panel -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search member -->
                <div class="relative w-full md:max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar pagos por socio..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 pl-10 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider hidden sm:inline">Filtrar Estado:</span>
                    <select wire:model.live="statusFilter" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl w-full sm:w-48">
                        <option value="">Todos los estados</option>
                        <option value="paid">Completados</option>
                        <option value="pending">Pendientes</option>
                        <option value="rejected">Rechazados</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-zinc-300">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 font-bold bg-zinc-950/20">
                            <th class="py-4 pl-6">Socio</th>
                            <th class="py-4">Monto</th>
                            <th class="py-4">Método de Pago</th>
                            <th class="py-4">Referencia de Pago</th>
                            <th class="py-4">Fecha de Registro</th>
                            <th class="py-4 text-right pr-6">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="border-b border-zinc-800/60 hover:bg-zinc-800/20 transition-all duration-150">
                                <td class="py-4 pl-6">
                                    <div class="font-extrabold text-white leading-tight">{{ $payment->user->name ?? 'Usuario Eliminado' }}</div>
                                    <div class="text-xs text-zinc-400 mt-0.5">{{ $payment->user->email ?? '' }}</div>
                                </td>
                                <td class="py-4 font-bold text-white text-base">
                                    ${{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4">
                                    <span class="badge badge-sm bg-zinc-800 border-zinc-700 text-zinc-300 font-semibold">
                                        {{ $payment->payment_method ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 font-mono text-xs text-zinc-450">
                                    {{ $payment->external_reference ?? 'Sin referencia' }}
                                </td>
                                <td class="py-4 text-xs text-zinc-450">
                                    {{ $payment->created_at->format('d/m/Y H:i') }}
                                    <span class="text-[10px] text-zinc-600 block">{{ $payment->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="py-4 text-right pr-6">
                                    <span class="badge badge-sm {{ $payment->status === 'paid' || $payment->status === 'approved' ? 'badge-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : ($payment->status === 'rejected' ? 'badge-error bg-rose-500/10 border-rose-500/20 text-rose-400' : 'badge-warning bg-amber-500/10 border-amber-500/20 text-amber-400') }} font-semibold">
                                        {{ $payment->status === 'paid' || $payment->status === 'approved' ? 'Aprobado' : ($payment->status === 'rejected' ? 'Rechazado' : 'Pendiente') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16 text-zinc-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>No se registran transacciones de pago en esta vista</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-zinc-800 bg-zinc-950/20">
                {{ $payments->links() }}
            </div>
        </div>
    @endif

    <!-- ================= TAB: PLAN TEMPLATES LIST ================= -->
    @if($activeTab === 'plans')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($plans as $plan)
                <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex flex-col justify-between hover:border-orange-500/30 hover:shadow-orange-500/5 transition-all duration-300 group">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-black text-white group-hover:text-orange-400 transition-colors duration-300">
                                {{ $plan->name }}
                            </h3>
                            <span class="text-xs font-bold font-mono bg-orange-500/10 border border-orange-500/25 text-orange-400 px-2 py-0.5 rounded-lg">
                                {{ $plan->duration_months }} {{ $plan->duration_months === 1 ? 'Mes' : 'Meses' }}
                            </span>
                        </div>
                        <p class="text-xs text-zinc-450 leading-relaxed mb-6">
                            {{ $plan->description ?? 'Sin descripción.' }}
                        </p>
                    </div>

                    <div>
                        <div class="border-t border-zinc-800/60 pt-4 flex items-center justify-between mb-4">
                            <span class="text-xs text-zinc-400 font-bold uppercase tracking-wider">Precio del Plan</span>
                            <span class="text-2xl font-black text-white">${{ number_format($plan->price, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-end gap-2 border-t border-zinc-800/60 pt-4">
                            <button wire:click="openPlanEditModal({{ $plan->id }})" class="btn btn-sm btn-ghost btn-circle text-orange-500 hover:text-orange-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button onclick="confirm('¿Eliminar esta plantilla de plan?') || event.stopImmediatePropagation()" wire:click="deletePlan({{ $plan->id }})" class="btn btn-sm btn-ghost btn-circle text-rose-500 hover:text-rose-455">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-zinc-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" /></svg>
                    <span>No se registran plantillas de planes de membresía</span>
                </div>
            @endforelse
        </div>
    @endif

    <!-- ================= MODAL: ASSIGN MEMBERSHIP & PAYMENT ================= -->
    @if($showAssignModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showAssignModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Modal Title -->
                <h3 class="text-xl font-black text-white mb-6 pr-8">
                    Asignar Pase y Registrar Pago
                </h3>

                <!-- Form Scrollable Area -->
                <div class="overflow-y-auto pr-1 flex-grow space-y-4">
                    
                    <!-- Search Member (Autocomplete) -->
                    <div class="form-control relative">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Socio / Miembro</span></label>
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
                                            <div class="text-[10px] text-zinc-500">{{ $sm->email }}</div>
                                        </div>
                                        <span class="text-[9px] font-bold text-zinc-500">Seleccionar</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('selectedMemberId') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Select Plan Template -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Plan a Asignar</span></label>
                        <select wire:model="selectedPlanId" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl">
                            <option value="">Seleccione un plan</option>
                            @foreach($plans as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (${{ number_format($p->price, 0) }} - {{ $p->duration_months }} Meses)</option>
                            @endforeach
                        </select>
                        @error('selectedPlanId') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Start Date -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Fecha de Inicio de Pase</span></label>
                        <input type="date" wire:model="membership_start_date" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                        @error('membership_start_date') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Payment Method -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Método de Pago</span></label>
                            <select wire:model="payment_method" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl">
                                <option value="Cash">Efectivo (Cash)</option>
                                <option value="Bank Transfer">Transferencia Bancaria</option>
                                <option value="Credit/Debit Card">Tarjeta Débito/Crédito</option>
                                <option value="MercadoPago">MercadoPago Manual</option>
                            </select>
                        </div>

                        <!-- Payment Status -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Estado Pago</span></label>
                            <select wire:model="payment_status" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl">
                                <option value="paid">Completado / Cobrado</option>
                                <option value="pending">Pendiente / Impago</option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end gap-2">
                    <button wire:click="$set('showAssignModal', false)" class="btn btn-ghost border-zinc-800 hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-white">Cancelar</button>
                    <button wire:click="assignMembership" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6">
                        Registrar Pase
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ================= MODAL: CREATE & EDIT PLAN TEMPLATE ================= -->
    @if($showPlanModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showPlanModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Modal Title -->
                <h3 class="text-xl font-black text-white mb-6 pr-8">
                    {{ $editingPlanId ? 'Editar Plantilla de Plan' : 'Crear Nueva Plantilla de Plan' }}
                </h3>

                <!-- Form Scrollable Area -->
                <div class="overflow-y-auto pr-1 flex-grow space-y-4">
                    
                    <!-- Plan Name -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Nombre del Plan</span></label>
                        <input type="text" wire:model="plan_name" placeholder="Ej: Pase Libre Mensual, Plan Anual Premium..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                        @error('plan_name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Descripción</span></label>
                        <textarea wire:model="plan_description" placeholder="Detalles de lo que incluye el plan..." class="textarea textarea-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl h-20"></textarea>
                        @error('plan_description') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Price & Duration -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Precio del Plan ($)</span></label>
                            <input type="number" wire:model="plan_price" min="0" placeholder="Ej: 15000" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('plan_price') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Duración (Meses)</span></label>
                            <input type="number" wire:model="plan_duration_months" min="1" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('plan_duration_months') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end gap-2">
                    <button wire:click="$set('showPlanModal', false)" class="btn btn-ghost border-zinc-800 hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-white">Cancelar</button>
                    <button wire:click="savePlan" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6">
                        Guardar Plan
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
