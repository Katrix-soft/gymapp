<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.guest')] class extends Component
{
    // Common fields
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Central SaaS signup fields
    public string $gym_name = '';
    public string $gym_slug = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        if (tenant()) {
            // Tenant Registration (Member signup)
            $validated = $this->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
                'gym_code' => 'MEM-' . strtoupper(Str::random(5)),
            ]);

            // Assign Spatie member role
            $memberRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
            $user->assignRole($memberRole);

            event(new Registered($user));
            Auth::login($user);

            $this->redirect(route('dashboard', absolute: false), navigate: true);
        } else {
            // Central SaaS Registration (Gym Owner signup)
            $validated = $this->validate([
                'gym_name' => ['required', 'string', 'max:255'],
                'gym_slug' => ['required', 'string', 'alpha_num', 'min:3', 'max:30', 'unique:tenants,id'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            ]);

            // 1. Create Tenant
            $tenant = Tenant::create([
                'id' => strtolower($validated['gym_slug']),
                'name' => $validated['gym_name'],
                'owner_email' => $validated['email'],
            ]);

            // 2. Create Domain
            $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'gym.test';
            $tenant->domains()->create([
                'domain' => strtolower($validated['gym_slug']) . '.' . $host,
            ]);

            // 3. Provision Tenant Admin inside tenant context
            $tenant->run(function () use ($validated) {
                // Ensure Spatie roles exist
                $adminRole = Role::firstOrCreate(['name' => 'gym_admin', 'guard_name' => 'web']);
                Role::firstOrCreate(['name' => 'trainer', 'guard_name' => 'web']);
                Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

                $admin = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'status' => 'active',
                    'gym_code' => 'ADM-100',
                ]);
                $admin->assignRole($adminRole);
            });

            // 4. Create central user record for auth link
            \App\Models\CentralUser::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // 5. Redirect to tenant site
            $tenantDomain = strtolower($validated['gym_slug']) . '.' . $host;
            $this->redirect('http://' . $tenantDomain . '/login');
        }
    }
}; ?>

<div>
    <form wire:submit="register">
        @if (!tenant())
            <!-- Gym Configuration (Central SaaS only) -->
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Configura tu Gimnasio</h3>
                
                <div>
                    <x-input-label for="gym_name" :value="__('Nombre del Gimnasio')" />
                    <x-text-input wire:model="gym_name" id="gym_name" class="block mt-1 w-full" type="text" name="gym_name" required autofocus />
                    <x-input-error :messages="$errors->get('gym_name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="gym_slug" :value="__('Subdominio del Gimnasio')" />
                    <div class="flex items-center mt-1">
                        <x-text-input wire:model="gym_slug" id="gym_slug" class="block w-full rounded-r-none" type="text" name="gym_slug" placeholder="mi-gimnasio" required />
                        <span class="bg-gray-100 dark:bg-gray-800 text-gray-500 border border-l-0 border-gray-300 dark:border-gray-700 px-3 py-2 rounded-r-md text-sm">
                            .gym.test
                        </span>
                    </div>
                    <x-input-error :messages="$errors->get('gym_slug')" class="mt-2" />
                </div>
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-700" />
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Cuenta del Administrador</h3>
        @endif

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('Nombre')" />
            <x-text-input wire:model="first_name" id="first_name" class="block mt-1 w-full" type="text" name="first_name" required :autofocus="tenant()" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Apellido')" />
            <x-text-input wire:model="last_name" id="last_name" class="block mt-1 w-full" type="text" name="last_name" required />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('¿Ya estás registrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</div>
