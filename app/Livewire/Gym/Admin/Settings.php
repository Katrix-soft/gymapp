<?php

namespace App\Livewire\Gym\Admin;

use App\Models\TenantConfig;
use Livewire\Component;

class Settings extends Component
{
    // Branding
    public $gymName = '';
    public $logoUrl = '';
    public $brandColor = '#f97316';

    // MercadoPago Credentials
    public $mpClientId = '';
    public $mpClientSecret = '';
    public $mpAccessToken = '';
    public $mpPublicKey = '';

    // Gym policy
    public $cancellationPolicyHours = '2';

    public function mount()
    {
        $this->gymName = TenantConfig::get('gym_name', '');
        $this->logoUrl = TenantConfig::get('logo_url', '');
        $this->brandColor = TenantConfig::get('brand_color', '#f97316');
        $this->mpClientId = TenantConfig::get('mp_client_id', '');
        $this->mpClientSecret = TenantConfig::get('mp_client_secret', '');
        $this->mpAccessToken = TenantConfig::get('mp_access_token', '');
        $this->mpPublicKey = TenantConfig::get('mp_public_key', '');
        $this->cancellationPolicyHours = TenantConfig::get('cancellation_policy_hours', '2');
    }

    public function saveBranding()
    {
        $this->validate([
            'gymName' => 'required|string|max:100',
            'logoUrl' => 'nullable|url|max:500',
            'brandColor' => 'required|string|max:20',
        ]);

        TenantConfig::set('gym_name', $this->gymName);
        TenantConfig::set('logo_url', $this->logoUrl);
        TenantConfig::set('brand_color', $this->brandColor);
        TenantConfig::set('cancellation_policy_hours', $this->cancellationPolicyHours);

        session()->flash('message', 'Configuración de marca guardada exitosamente.');
    }

    public function saveCredentials()
    {
        $this->validate([
            'mpClientId' => 'nullable|string|max:255',
            'mpClientSecret' => 'nullable|string|max:255',
            'mpAccessToken' => 'nullable|string|max:500',
            'mpPublicKey' => 'nullable|string|max:255',
        ]);

        TenantConfig::set('mp_client_id', $this->mpClientId);
        TenantConfig::set('mp_client_secret', $this->mpClientSecret);
        TenantConfig::set('mp_access_token', $this->mpAccessToken);
        TenantConfig::set('mp_public_key', $this->mpPublicKey);

        session()->flash('message', 'Credenciales de MercadoPago actualizadas exitosamente.');
    }

    public function render()
    {
        return view('livewire.gym.admin.settings')
            ->layout('layouts.tenant-app');
    }
}
