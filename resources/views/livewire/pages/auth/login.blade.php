<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    // Honeypot anti-bot field (must remain empty)
    public string $website = '';

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        // Honeypot check: if a bot filled the hidden field, silently reject
        if (!empty($this->website)) {
            // Fake a small delay to not tip off bots
            usleep(500000);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Log successful login
        if (auth()->check()) {
            logger()->info('Login exitoso', [
                'user_id' => auth()->id(),
                'email' => auth()->user()->email,
                'ip' => request()->ip(),
                'tenant' => tenant('id') ?? 'central',
            ]);
        }

        $redirectUrl = tenant() && request()->segment(1) === 'g'
            ? '/g/' . tenant('id') . '/dashboard'
            : route('dashboard', absolute: false);

        $this->redirectIntended(default: $redirectUrl, navigate: true);
    }

    /**
     * Authenticate user with their biometric credential ID
     */
    public function loginWithBiometrics(string $credentialId): void
    {
        // Search user in tenant context first
        $user = \App\Models\User::where('biometric_credential_id', $credentialId)->first();

        // If not found, try central context
        if (!$user) {
            $user = \App\Models\CentralUser::where('biometric_credential_id', $credentialId)->first();
        }

        if (!$user) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'form.email' => 'Acceso biométrico no reconocido en esta cuenta o dispositivo.',
            ]);
        }

        // Authenticate the user directly
        Auth::login($user, remember: true);

        Session::regenerate();

        logger()->info('Login biométrico exitoso', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'tenant' => tenant('id') ?? 'central',
        ]);

        $redirectUrl = tenant() && request()->segment(1) === 'g'
            ? '/g/' . tenant('id') . '/dashboard'
            : route('dashboard', absolute: false);

        $this->redirectIntended(default: $redirectUrl, navigate: true);
    }
}; ?>

<div x-data="biometricLogin()">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Honeypot (hidden from real users, traps bots) -->
        <div class="absolute opacity-0 -z-10 h-0 overflow-hidden" aria-hidden="true" tabindex="-1">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" wire:model="website" autocomplete="off" tabindex="-1">
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Correo electrónico
                </span>
            </label>
            <div class="relative">
                <input
                    wire:model="form.email"
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="tu@email.com"
                    class="w-full bg-gray-900/50 border border-gray-700/60 text-gray-100 rounded-xl pl-4 pr-10 py-3 shadow-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-950 brand-focus transition-all duration-200"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Contraseña
                </span>
            </label>
            <div class="relative">
                <input
                    wire:model="form.password"
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full bg-gray-900/50 border border-gray-700/60 text-gray-100 rounded-xl pl-4 pr-12 py-3 shadow-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-950 brand-focus transition-all duration-200"
                />
                <!-- Toggle Password Visibility -->
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-300 transition-colors duration-200"
                    tabindex="-1"
                    :title="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                >
                    <!-- Eye Open -->
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <!-- Eye Closed -->
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center cursor-pointer select-none group">
                <input
                    wire:model="form.remember"
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-gray-600 bg-gray-800/50 text-orange-500 shadow-sm focus:ring-orange-500 focus:ring-offset-gray-950 transition"
                >
                <span class="ms-2 text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm brand-text hover:brightness-125 transition-all duration-200" href="{{ route('password.request') }}" wire:navigate>
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <!-- Biometric Quick Login Button -->
        <template x-if="hasBiometrics">
            <button
                type="button"
                @click="authenticateBiometrics()"
                class="w-full py-3 px-6 bg-orange-500/10 hover:bg-orange-500/25 border border-orange-500/30 hover:border-orange-500/50 text-orange-400 font-semibold rounded-xl text-sm transition-all duration-200 flex items-center justify-center gap-2 group"
                :disabled="loading"
            >
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 009 11a13.917 13.917 0 00-2.3-7.551m3.854 8.046a12.09 12.09 0 011.07 1.05m-3.97-1.05a12.47 12.47 0 00-1.123-8.32m2.91 8.32a12.422 12.422 0 01-1.082 5.53m1.538-12.2a12.093 12.093 0 01-1.08 1.058m1.586-.072A12.09 12.09 0 0115 11c0 3.06 1.007 5.885 2.71 8.12M9 11V9m15-1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-text="loading ? 'Verificando huella/rostro...' : 'INGRESAR CON HUELLA / ROSTRO'"></span>
            </button>
        </template>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full brand-btn text-white font-semibold py-3.5 px-6 rounded-xl uppercase tracking-wider text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-950 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 relative overflow-hidden"
            wire:loading.attr="disabled"
        >
            <!-- Loading spinner -->
            <svg wire:loading wire:target="login" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="login">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    INICIAR SESIÓN
                </span>
            </span>
            <span wire:loading wire:target="login">
                Verificando...
            </span>
        </button>

        <!-- Security badge -->
        <div class="flex items-center justify-center gap-1.5 pt-1">
            <svg class="w-3.5 h-3.5 text-green-500/70" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
            </svg>
            <span class="text-xs text-gray-500">Conexión segura con cifrado SSL</span>
        </div>
    </form>
</div>

<script>
function biometricLogin() {
    return {
        hasBiometrics: false,
        loading: false,
        biometrics: null,

        init() {
            setTimeout(() => {
                if (!window.KatrixBiometrics) return;

                this.biometrics = new window.KatrixBiometrics({
                    appName: '{{ tenant() ? addslashes(tenant("name")) : "SaaS Gym Central" }}'
                });

                const status = this.biometrics.getStatus();
                if (status.available && status.linked && status.credentialId) {
                    this.hasBiometrics = true;
                }
            }, 600);
        },

        async authenticateBiometrics() {
            if (!this.biometrics) return;
            this.loading = true;

            const result = await this.biometrics.authenticate();
            this.loading = false;

            if (result.success) {
                const credId = this.biometrics.getStatus().credentialId;
                @this.call('loginWithBiometrics', credId);
            } else {
                alert(`Error en verificación biométrica: ${result.error.message}`);
            }
        }
    }
}
</script>
