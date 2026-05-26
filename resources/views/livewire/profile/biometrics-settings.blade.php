<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $isLinked = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->isLinked = !empty(Auth::user()->biometric_credential_id);
    }

    /**
     * Link biometric credential.
     */
    public function linkBiometrics(string $credentialId): void
    {
        $user = Auth::user();
        $user->biometric_credential_id = $credentialId;
        $user->save();

        $this->isLinked = true;
        
        $this->dispatch('toast', type: 'success', message: '¡Acceso biométrico vinculado correctamente a tu cuenta!');
    }

    /**
     * Unlink biometric credential.
     */
    public function unlinkBiometrics(): void
    {
        $user = Auth::user();
        $user->biometric_credential_id = null;
        $user->save();

        $this->isLinked = false;

        $this->dispatch('toast', type: 'info', message: 'Acceso biométrico desvinculado de tu cuenta.');
    }
}; ?>

<section class="space-y-6" x-data="biometricLinking()">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11a13.917 13.917 0 00-2.3-7.551m3.854 8.046a12.09 12.09 0 011.07 1.05m-3.97-1.05a12.47 12.47 0 00-1.123-8.32m2.91 8.32a12.422 12.422 0 01-1.082 5.53m1.538-12.2a12.093 12.093 0 01-1.08 1.058m1.586-.072A12.09 12.09 0 0115 11c0 3.06 1.007 5.885 2.71 8.12M9 11V9m15-1a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Acceso Biométrico (Touch ID / Face ID)
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Vincula el sensor de huella digital, Face ID o Windows Hello de este dispositivo para iniciar sesión rápidamente sin ingresar tu contraseña.
        </p>
    </header>

    <div class="bg-gray-900/40 border border-gray-800/80 rounded-2xl p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20 text-orange-500">
                <template x-if="linked">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </template>
                <template x-if="!linked">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </template>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-100">Estado del Dispositivo</h4>
                <p class="text-xs text-gray-400 mt-0.5" x-text="statusText"></p>
            </div>
        </div>

        <div class="flex gap-3">
            {{-- Button to register biometrics --}}
            <button
                x-show="!linked"
                type="button"
                @click="registerDevice()"
                class="brand-btn text-white font-semibold py-2 px-4 rounded-xl text-sm transition-all duration-200 flex items-center gap-2"
                :disabled="loading"
            >
                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Configurando...' : 'Vincular Dispositivo'"></span>
            </button>

            {{-- Button to unlink biometrics --}}
            <button
                x-show="linked"
                type="button"
                @click="unlinkDevice()"
                class="px-4 py-2 bg-red-950/40 hover:bg-red-950/80 border border-red-800/50 hover:border-red-700/60 text-red-200 hover:text-red-100 font-semibold rounded-xl text-sm transition-all duration-200"
            >
                Desvincular
            </button>
        </div>
    </div>
</section>

<script>
function biometricLinking() {
    return {
        linked: @entangle('isLinked'),
        loading: false,
        statusText: 'Cargando información biométrica...',
        biometrics: null,

        init() {
            // Wait for bundle to load if necessary
            setTimeout(() => {
                if (!window.KatrixBiometrics) {
                    this.statusText = 'Biblioteca de biometría no disponible.';
                    return;
                }

                // Initialize KatrixBiometrics
                this.biometrics = new window.KatrixBiometrics({
                    appName: '{{ tenant() ? addslashes(tenant("name")) : "SaaS Gym Central" }}',
                    userId: '{{ auth()->id() }}',
                    userName: '{{ auth()->user()->email }}',
                    userDisplayName: '{{ addslashes(auth()->user()->name) }}'
                });

                this.updateStatus();
            }, 500);
        },

        updateStatus() {
            if (!this.biometrics) return;
            const status = this.biometrics.getStatus();
            if (!status.available) {
                this.statusText = 'Biometría no soportada en este navegador o requiere conexión HTTPS segura.';
                return;
            }

            if (this.linked) {
                this.statusText = 'Este dispositivo está vinculado para inicio de sesión biométrico.';
            } else {
                this.statusText = 'Disponible. Haz clic en Vincular para configurar tu huella/rostro.';
            }
        },

        async registerDevice() {
            if (!this.biometrics) return;
            this.loading = true;
            this.statusText = 'Por favor, escanea tu huella o usa Face ID cuando tu sistema lo solicite...';

            const result = await this.biometrics.register();
            this.loading = false;

            if (result.success) {
                const credId = this.biometrics.getStatus().credentialId;
                // Save it to DB
                @this.call('linkBiometrics', credId);
                this.linked = true;
                this.updateStatus();
            } else {
                this.updateStatus();
                // Dispatch warning/error toast
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: {
                        type: 'error',
                        message: `No se pudo vincular el dispositivo: ${result.error.message}`
                    }
                }));
            }
        },

        unlinkDevice() {
            if (confirm('¿Estás seguro de que deseas desvincular el acceso biométrico en este dispositivo?')) {
                if (this.biometrics) {
                    this.biometrics.unlink();
                }
                @this.call('unlinkBiometrics');
                this.linked = false;
                this.updateStatus();
            }
        }
    }
}
</script>
