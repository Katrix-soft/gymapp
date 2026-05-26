<div>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Soporte y Profesores
        </h2>
    </x-slot>

    <!-- Chat Container Card -->
    <div class="bg-zinc-900/40 backdrop-blur-md border border-zinc-800/80 rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 h-[calc(100vh-210px)] min-h-[500px]">
        
        <!-- ================= LEFT SIDEBAR: CONTACTS LIST ================= -->
        <div class="lg:col-span-4 border-r border-zinc-800/80 flex flex-col h-full bg-zinc-950/20">
            <!-- Search Contact Box -->
            <div class="p-4 border-b border-zinc-800/80">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="chatSearch" placeholder="Buscar staff o profesor..." class="input input-sm input-bordered w-full bg-zinc-900 border-zinc-800 pl-9 text-zinc-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-lg text-xs" />
                </div>
            </div>

            <!-- Contacts Loop -->
            <div class="overflow-y-auto flex-grow divide-y divide-zinc-800/45 p-2 space-y-1">
                @forelse($contacts as $contact)
                    <button wire:click="selectChat({{ $contact['id'] }})" class="w-full text-left p-3 rounded-xl flex items-start gap-3 transition-all duration-200 {{ $activeUserId === $contact['id'] ? 'bg-orange-500/10 border border-orange-500/25' : 'border border-transparent hover:bg-zinc-800/30' }}">
                        <!-- Avatar -->
                        <div class="avatar placeholder relative">
                            <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-xs font-black text-orange-500 uppercase">
                                {{ strtoupper(substr($contact['name'], 0, 1)) }}{{ strtoupper(substr(strrchr($contact['name'], ' ') ?: ' ', 1, 1)) }}
                            </div>
                            @if($contact['unread_count'] > 0)
                                <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white font-extrabold text-[9px] w-4.5 h-4.5 rounded-full flex items-center justify-center border border-zinc-900 shadow">
                                    {{ $contact['unread_count'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <span class="font-extrabold text-sm text-white truncate pr-2">{{ $contact['name'] }}</span>
                                <span class="text-[9px] text-zinc-400 shrink-0 font-mono">
                                    {{ $contact['last_message_time'] ? \Carbon\Carbon::parse($contact['last_message_time'])->diffForHumans(null, true) : '' }}
                                </span>
                            </div>
                            
                            <!-- Role Badge & Snippet -->
                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                <p class="text-xs text-zinc-450 truncate">
                                    {{ $contact['last_message'] ?? 'Escribe una consulta...' }}
                                </p>
                                <span class="badge badge-xs uppercase font-extrabold px-1 text-[8px] {{ $contact['role'] === 'gym_admin' ? 'bg-orange-500/15 border-orange-500/30 text-orange-400' : 'bg-purple-500/15 border-purple-500/30 text-purple-400' }}">
                                    {{ $contact['role'] === 'gym_admin' ? 'Admin' : 'Profesor' }}
                                </span>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="text-center py-8 text-xs text-zinc-500">No se encontraron contactos de staff</div>
                @endforelse
            </div>
        </div>

        <!-- ================= RIGHT PANEL: CHAT WINDOW ================= -->
        <div class="lg:col-span-8 flex flex-col h-full bg-zinc-950/5">
            
            @if($activeUser)
                <!-- Active Chat Header -->
                <div class="p-4 border-b border-zinc-800/80 bg-zinc-950/20 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="avatar placeholder">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/25 flex items-center justify-center font-extrabold text-orange-500">
                                {{ strtoupper(substr($activeUser->first_name, 0, 1)) }}{{ strtoupper(substr($activeUser->last_name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <div class="font-extrabold text-white leading-tight">{{ $activeUser->name }}</div>
                            <span class="text-[10px] text-zinc-400 capitalize">{{ $activeUser->roles->first()->name === 'gym_admin' ? 'Administrador del Gym' : 'Instructor Técnico' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Chat Timeline (Auto-polling for new messages) -->
                <div wire:poll.3000ms="loadMessages" class="flex-grow overflow-y-auto p-6 space-y-4 flex flex-col min-h-0 bg-zinc-950/15">
                    @forelse($messages as $msg)
                        @php
                            $isMe = $msg['sender_id'] === auth()->id();
                        @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} w-full">
                            <div class="max-w-[70%] flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                <div class="px-4 py-2.5 rounded-2xl text-xs {{ $isMe ? 'bg-orange-500 text-white rounded-br-none shadow-lg shadow-orange-500/5' : 'bg-zinc-800 border border-zinc-700 text-zinc-100 rounded-bl-none' }}">
                                    <p class="leading-relaxed whitespace-pre-line">{{ $msg['message'] }}</p>
                                </div>
                                <span class="text-[9px] text-zinc-400 mt-1 font-mono">
                                    {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                                    @if($isMe)
                                        <span class="ml-1 text-zinc-600 font-bold">{{ $msg['read_at'] ? 'Leído' : 'Enviado' }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 text-zinc-500 text-xs">Escribe una pregunta para iniciar el contacto con el staff técnico</div>
                    @endforelse
                </div>

                <!-- Input Text Box -->
                <form wire:submit.prevent="sendMessage" class="p-4 border-t border-zinc-800/80 bg-zinc-950/20 flex gap-2">
                    <input type="text" wire:model="newMessage" placeholder="Escribe tu consulta aquí..." class="input input-bordered w-full bg-zinc-950 border-zinc-800 text-zinc-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 rounded-xl text-xs flex-grow" />
                    <button type="submit" class="btn bg-orange-500 hover:bg-orange-600 border-none text-white rounded-xl px-4 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            @else
                <!-- No chat selected fallback -->
                <div class="flex-grow flex flex-col items-center justify-center text-center p-8">
                    <div class="w-16 h-16 rounded-2xl bg-orange-500/10 border border-orange-500/25 flex items-center justify-center text-orange-500 mb-4 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-white">Soporte y Consultas</h3>
                    <p class="text-xs text-zinc-400 max-w-xs mt-1.5 leading-relaxed">Selecciona un instructor o administrador del panel izquierdo para enviarles un mensaje directo.</p>
                </div>
            @endif

        </div>

    </div>
</div>
