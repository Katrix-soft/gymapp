<x-tenant-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-zinc-100 leading-tight">
            {{ __('Mi Perfil') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6 max-w-4xl mx-auto">
        {{-- Profile Information Card --}}
        <div class="p-6 bg-zinc-900/60 border border-zinc-800/80 shadow-2xl rounded-2xl backdrop-blur-md">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        {{-- Biometrics Settings Card --}}
        <div class="p-6 bg-zinc-900/60 border border-zinc-800/80 shadow-2xl rounded-2xl backdrop-blur-md">
            <div class="max-w-xl">
                <livewire:profile.biometrics-settings />
            </div>
        </div>

        {{-- Update Password Card --}}
        <div class="p-6 bg-zinc-900/60 border border-zinc-800/80 shadow-2xl rounded-2xl backdrop-blur-md">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        {{-- Delete Account Card --}}
        <div class="p-6 bg-zinc-900/60 border border-zinc-800/80 shadow-2xl rounded-2xl backdrop-blur-md">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-tenant-app-layout>
