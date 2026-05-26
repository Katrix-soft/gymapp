{{-- 
    Premium Toast Notification System
    Usage from Livewire: session()->flash('message', 'Texto...') or session()->flash('error', 'Texto...')
    Also supports Livewire events: $this->dispatch('toast', type: 'success', message: 'Texto...')
--}}
<div
    x-data="toastSystem()"
    x-on:toast.window="addToast($event.detail)"
    class="fixed top-4 right-4 z-[9999] flex flex-col items-end gap-3 pointer-events-none max-w-sm w-full"
>
    {{-- Render session flash messages on page load --}}
    @if(session('message'))
        <template x-init="addToast({ type: 'success', message: '{{ addslashes(session('message')) }}' })"></template>
    @endif
    @if(session('error'))
        <template x-init="addToast({ type: 'error', message: '{{ addslashes(session('error')) }}' })"></template>
    @endif
    @if(session('warning'))
        <template x-init="addToast({ type: 'warning', message: '{{ addslashes(session('warning')) }}' })"></template>
    @endif
    @if(session('info'))
        <template x-init="addToast({ type: 'info', message: '{{ addslashes(session('info')) }}' })"></template>
    @endif

    {{-- Toast items --}}
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-8 scale-95"
            class="pointer-events-auto w-full"
        >
            <div
                class="relative flex items-start gap-3 px-4 py-3.5 rounded-2xl shadow-2xl border backdrop-blur-xl overflow-hidden"
                :class="{
                    'bg-emerald-950/80 border-emerald-800/50 text-emerald-100': toast.type === 'success',
                    'bg-red-950/80 border-red-800/50 text-red-100': toast.type === 'error',
                    'bg-amber-950/80 border-amber-800/50 text-amber-100': toast.type === 'warning',
                    'bg-sky-950/80 border-sky-800/50 text-sky-100': toast.type === 'info'
                }"
            >
                {{-- Progress bar --}}
                <div class="absolute bottom-0 left-0 h-[2px] rounded-full transition-all duration-100"
                    :class="{
                        'bg-emerald-400': toast.type === 'success',
                        'bg-red-400': toast.type === 'error',
                        'bg-amber-400': toast.type === 'warning',
                        'bg-sky-400': toast.type === 'info'
                    }"
                    :style="'width: ' + toast.progress + '%'"
                ></div>

                {{-- Icon --}}
                <div class="flex-shrink-0 mt-0.5">
                    {{-- Success --}}
                    <template x-if="toast.type === 'success'">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </template>
                    {{-- Error --}}
                    <template x-if="toast.type === 'error'">
                        <div class="w-8 h-8 rounded-xl bg-red-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </template>
                    {{-- Warning --}}
                    <template x-if="toast.type === 'warning'">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                    </template>
                    {{-- Info --}}
                    <template x-if="toast.type === 'info'">
                        <div class="w-8 h-8 rounded-xl bg-sky-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </template>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold leading-tight"
                       :class="{
                           'text-emerald-200': toast.type === 'success',
                           'text-red-200': toast.type === 'error',
                           'text-amber-200': toast.type === 'warning',
                           'text-sky-200': toast.type === 'info'
                       }"
                       x-text="toast.title"></p>
                    <p class="text-sm mt-0.5 opacity-80 leading-snug" x-text="toast.message"></p>
                </div>

                {{-- Close button --}}
                <button
                    @click="removeToast(toast.id)"
                    class="flex-shrink-0 opacity-50 hover:opacity-100 transition-opacity p-1 rounded-lg hover:bg-white/10"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function toastSystem() {
    return {
        toasts: [],
        counter: 0,

        addToast(detail) {
            const id = ++this.counter;
            const type = detail.type || 'success';
            const titles = {
                success: '¡Éxito!',
                error: 'Error',
                warning: 'Atención',
                info: 'Información'
            };

            const toast = {
                id,
                type,
                title: detail.title || titles[type] || '¡Éxito!',
                message: detail.message || '',
                visible: true,
                progress: 100,
                duration: detail.duration || (type === 'error' ? 6000 : 4000)
            };

            this.toasts.push(toast);

            // Animate progress bar
            const interval = 50;
            const steps = toast.duration / interval;
            const decrement = 100 / steps;
            const timer = setInterval(() => {
                const t = this.toasts.find(t => t.id === id);
                if (!t) { clearInterval(timer); return; }
                t.progress = Math.max(0, t.progress - decrement);
                if (t.progress <= 0) {
                    clearInterval(timer);
                    this.removeToast(id);
                }
            }, interval);

            // Limit to max 5 visible toasts
            if (this.toasts.length > 5) {
                this.removeToast(this.toasts[0].id);
            }
        },

        removeToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    };
}
</script>
