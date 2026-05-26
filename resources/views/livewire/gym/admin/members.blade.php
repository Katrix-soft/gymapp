<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Gestión de Socios y Miembros
            </h2>
            <button wire:click="openCreateModal" class="btn bg-orange-500 hover:bg-orange-600 text-white border-none rounded-xl shadow-lg shadow-orange-500/20 gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nuevo Socio
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
            <button class="btn btn-ghost btn-xs btn-circle text-emerald-400" @click="open = false">✕</button>
        </div>
    @endif

    <!-- Search and Bulk Action Controls Card -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl p-6 shadow-xl mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search bar with glow -->
            <div class="relative w-full md:max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, email o código..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 pl-10 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
            </div>

            <!-- Bulk actions toolbar -->
            @if(count($selectedMembers) > 0)
                <div class="flex items-center gap-2 bg-orange-500/10 border border-orange-500/25 px-4 py-2 rounded-xl text-sm text-orange-400 w-full md:w-auto justify-between md:justify-start">
                    <div class="font-bold flex items-center gap-1">
                        <span>{{ count($selectedMembers) }}</span>
                        <span>seleccionados:</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="bulkActivate" class="btn btn-xs bg-emerald-500 hover:bg-emerald-600 text-white border-none rounded">Activar</button>
                        <button wire:click="bulkDeactivate" class="btn btn-xs bg-amber-500 hover:bg-amber-600 text-white border-none rounded">Inactivar</button>
                        <button onclick="confirm('¿Eliminar socios seleccionados?') || event.stopImmediatePropagation()" wire:click="bulkDelete" class="btn btn-xs bg-rose-500 hover:bg-rose-600 text-white border-none rounded">Eliminar</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Members Table Card -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-zinc-300">
                <thead>
                    <tr class="border-b border-zinc-800 text-zinc-400 font-bold bg-zinc-950/20">
                        <th class="py-4 pl-6 w-12">
                            <input type="checkbox" wire:model.live="selectAll" class="checkbox checkbox-xs border-zinc-700 checkbox-warning" />
                        </th>
                        <th class="py-4 cursor-pointer hover:text-white" wire:click="sortBy('last_name')">
                            Socio {!! $sortBy === 'last_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </th>
                        <th class="py-4 cursor-pointer hover:text-white" wire:click="sortBy('gym_code')">
                            Código {!! $sortBy === 'gym_code' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </th>
                        <th class="py-4">Membresía Activa</th>
                        <th class="py-4 cursor-pointer hover:text-white" wire:click="sortBy('status')">
                            Estado {!! $sortBy === 'status' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                        </th>
                        <th class="py-4 text-right pr-6">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr class="border-b border-zinc-800/60 hover:bg-zinc-800/20 transition-all duration-150">
                            <td class="pl-6 py-4">
                                <input type="checkbox" wire:model.live="selectedMembers" value="{{ $member->id }}" class="checkbox checkbox-xs border-zinc-700 checkbox-warning" />
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-orange-500 font-extrabold shadow-inner">
                                            {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-white leading-tight">{{ $member->name }}</div>
                                        <div class="text-xs text-zinc-500 mt-0.5">{{ $member->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 font-mono text-sm text-zinc-400">
                                {{ $member->gym_code ?? 'Sin código' }}
                            </td>
                            <td class="py-4">
                                @if($member->activeMembership)
                                    <div>
                                        <span class="badge badge-sm bg-orange-500/10 border-orange-500/25 text-orange-400 font-semibold">
                                            {{ $member->activeMembership->plan->name }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-zinc-500 mt-1">Vence: {{ \Carbon\Carbon::parse($member->activeMembership->end_date)->format('d/m/Y') }}</div>
                                @else
                                    <span class="badge badge-sm bg-zinc-800 border-zinc-700 text-zinc-500">Sin pase activo</span>
                                @endif
                            </td>
                            <td class="py-4">
                                <span class="badge badge-sm {{ $member->status === 'active' ? 'badge-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : ($member->status === 'suspended' ? 'badge-error bg-rose-500/10 border-rose-500/20 text-rose-400' : 'badge-warning bg-amber-500/10 border-amber-500/20 text-amber-400') }} font-semibold">
                                    {{ $member->status === 'active' ? 'Activo' : ($member->status === 'suspended' ? 'Suspendido' : 'Inactivo') }}
                                </span>
                            </td>
                            <td class="py-4 text-right pr-6">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Details Button -->
                                    <button wire:click="openProfileModal({{ $member->id }})" class="btn btn-ghost btn-xs btn-circle text-zinc-400 hover:text-white" title="Ficha del Socio">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <!-- Edit Button -->
                                    <button wire:click="openEditModal({{ $member->id }})" class="btn btn-ghost btn-xs btn-circle text-orange-500 hover:text-orange-400" title="Editar Socio">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <!-- Delete Button -->
                                    <button onclick="confirm('¿Estás seguro de que deseas eliminar este socio?') || event.stopImmediatePropagation()" wire:click="deleteMember({{ $member->id }})" class="btn btn-ghost btn-xs btn-circle text-rose-500 hover:text-rose-450" title="Eliminar Socio">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-zinc-500 bg-zinc-900/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-zinc-700 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <span>No se encontraron socios registrados</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 border-t border-zinc-800 bg-zinc-950/20">
            {{ $members->links() }}
        </div>
    </div>

    <!-- ================= MODAL: CREATE & EDIT MEMBER ================= -->
    @if($showCreateEditModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showCreateEditModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Modal Title -->
                <h3 class="text-xl font-black text-white mb-6 pr-8">
                    {{ $editingMemberId ? 'Editar Socio' : 'Registrar Nuevo Socio' }}
                </h3>

                <!-- Form Scrollable Area -->
                <div class="overflow-y-auto pr-1 flex-grow space-y-4">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <!-- First Name -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Nombre</span></label>
                            <input type="text" wire:model="first_name" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('first_name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Apellido</span></label>
                            <input type="text" wire:model="last_name" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('last_name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-control">
                        <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Correo Electrónico</span></label>
                        <input type="email" wire:model="email" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                        @error('email') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-zinc-400 font-semibold text-xs uppercase">Contraseña</span>
                        </label>
                        <input type="password" wire:model="password" placeholder="{{ $editingMemberId ? 'Dejar en blanco para mantener contraseña' : 'Contraseña del socio' }}" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                        @error('password') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Gym Code -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Código de Acceso / Credencial</span></label>
                            <input type="text" wire:model="gym_code" placeholder="Ej: 1004" class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl" />
                            @error('gym_code') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-zinc-400 font-semibold text-xs uppercase">Estado</span></label>
                            <select wire:model="status" class="select select-bordered bg-zinc-950 border-zinc-800 text-zinc-100 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                                <option value="suspended">Suspendido</option>
                            </select>
                            @error('status') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>

                <!-- Form Action Buttons -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end gap-2">
                    <button wire:click="$set('showCreateEditModal', false)" class="btn btn-ghost border-zinc-800 hover:bg-zinc-800 rounded-xl text-zinc-400 hover:text-white">Cancelar</button>
                    <button wire:click="saveMember" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-6">
                        Guardar
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ================= MODAL: DETAILED PROFILE VIEW ================= -->
    @if($showProfileModal && $profileMember)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-4xl w-full p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Close Button -->
                <button wire:click="$set('showProfileModal', false)" class="absolute top-4 right-4 text-zinc-400 hover:text-white btn btn-ghost btn-xs btn-circle">✕</button>

                <!-- Profile Header -->
                <div class="flex flex-col sm:flex-row items-center gap-4 border-b border-zinc-800 pb-6 mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/25 flex items-center justify-center text-orange-500 font-extrabold text-2xl shadow-inner">
                        {{ strtoupper(substr($profileMember->first_name, 0, 1)) }}{{ strtoupper(substr($profileMember->last_name, 0, 1)) }}
                    </div>
                    <div class="text-center sm:text-left flex-grow">
                        <h3 class="text-2xl font-black text-white leading-tight">{{ $profileMember->name }}</h3>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-1.5">
                            <span class="text-sm text-zinc-500">{{ $profileMember->email }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-700"></span>
                            <span class="font-mono text-xs text-zinc-400">Credencial: #{{ $profileMember->gym_code ?? 'N/A' }}</span>
                            <span class="badge badge-sm {{ $profileMember->status === 'active' ? 'badge-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : ($profileMember->status === 'suspended' ? 'badge-error bg-rose-500/10 border-rose-500/20 text-rose-400' : 'badge-warning bg-amber-500/10 border-amber-500/20 text-amber-400') }} font-semibold ml-2">
                                {{ $profileMember->status === 'active' ? 'Activo' : ($profileMember->status === 'suspended' ? 'Suspendido' : 'Inactivo') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Main Details Scrollable Panels -->
                <div class="overflow-y-auto pr-1 flex-grow grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Left Column details -->
                    <div class="space-y-6">
                        <!-- Membership card -->
                        <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-5">
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                                Historial de Pases / Membresías
                            </h4>
                            <div class="space-y-2">
                                @forelse($profileMember->memberships as $membership)
                                    <div class="flex justify-between items-center bg-zinc-900/60 p-3 rounded-lg border border-zinc-800/80">
                                        <div>
                                            <div class="font-extrabold text-sm text-white">{{ $membership->plan->name }}</div>
                                            <div class="text-[10px] text-zinc-500 mt-0.5">Vigencia: {{ \Carbon\Carbon::parse($membership->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($membership->end_date)->format('d/m/Y') }}</div>
                                        </div>
                                        <span class="badge badge-sm font-bold {{ $membership->status === 'active' && \Carbon\Carbon::parse($membership->end_date)->isFuture() ? 'badge-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'badge-neutral bg-zinc-800 border-zinc-700 text-zinc-400' }}">
                                            {{ $membership->status === 'active' && \Carbon\Carbon::parse($membership->end_date)->isFuture() ? 'Activo' : 'Vencido' }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="text-zinc-500 text-xs text-center py-4">No registra historial de pases</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Payments card -->
                        <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-5">
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Historial de Pagos
                            </h4>
                            <div class="space-y-2">
                                @forelse($profileMember->payments as $payment)
                                    <div class="flex justify-between items-center bg-zinc-900/60 p-3 rounded-lg border border-zinc-800/80">
                                        <div>
                                            <div class="font-extrabold text-sm text-emerald-400">${{ number_format($payment->amount, 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-zinc-400 mt-0.5">Método: {{ $payment->payment_method ?? 'MercadoPago' }} | Ref: {{ substr($payment->external_reference ?? 'N/A', 0, 12) }}</div>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-xs {{ $payment->status === 'paid' ? 'badge-success text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 'badge-warning text-amber-400 bg-amber-500/10 border-amber-500/20' }}">
                                                {{ $payment->status === 'paid' ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                            <div class="text-[9px] text-zinc-500 mt-1">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-zinc-500 text-xs text-center py-4">No registra pagos completados</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right Column details -->
                    <div class="space-y-6">
                        <!-- Physical Measurements -->
                        <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-5">
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" /></svg>
                                Seguimiento Físico / Medidas
                            </h4>
                            <div class="space-y-3">
                                @forelse($profileMember->bodyMeasurements as $measure)
                                    <div class="bg-zinc-900/60 p-4 rounded-lg border border-zinc-800/80">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="text-xs font-bold text-zinc-400">Control: {{ \Carbon\Carbon::parse($measure->date)->format('d/m/Y') }}</div>
                                            <span class="text-[10px] text-zinc-500">{{ $measure->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                            <div class="bg-zinc-950 p-2 rounded">
                                                <div class="text-zinc-500 font-bold text-[9px] uppercase">Peso</div>
                                                <div class="font-extrabold text-white mt-0.5">{{ $measure->weight }} kg</div>
                                            </div>
                                            <div class="bg-zinc-950 p-2 rounded">
                                                <div class="text-zinc-500 font-bold text-[9px] uppercase">Grasa %</div>
                                                <div class="font-extrabold text-white mt-0.5">{{ $measure->body_fat_percentage ?? 'N/D' }}%</div>
                                            </div>
                                            <div class="bg-zinc-950 p-2 rounded">
                                                <div class="text-zinc-500 font-bold text-[9px] uppercase">Músculo %</div>
                                                <div class="font-extrabold text-white mt-0.5">{{ $measure->muscle_mass_percentage ?? 'N/D' }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-zinc-500 text-xs text-center py-4">No registra fichas corporales</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Attendance history -->
                        <div class="bg-zinc-950/40 border border-zinc-800 rounded-xl p-5">
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Historial de Asistencias (Últimas 10)
                            </h4>
                            <div class="space-y-1.5 max-h-[220px] overflow-y-auto pr-1">
                                @forelse($profileMember->attendanceRecords as $record)
                                    <div class="flex justify-between items-center bg-zinc-900/60 px-3 py-2 rounded border border-zinc-800/80 text-xs">
                                        <span class="font-semibold text-zinc-300">{{ $record->gymClass->name ?? 'Acceso General' }}</span>
                                        <span class="text-zinc-500">{{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</span>
                                    </div>
                                @empty
                                    <div class="text-zinc-500 text-xs text-center py-4">Sin asistencias registradas</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Actions -->
                <div class="mt-6 border-t border-zinc-800 pt-4 flex justify-end">
                    <button wire:click="$set('showProfileModal', false)" class="btn bg-zinc-800 hover:bg-zinc-800 text-white border-zinc-800 rounded-xl px-6">
                        Cerrar Ficha
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
