<?php

namespace App\Livewire\Gym\Admin;

use App\Models\User;
use App\Models\Membership;
use App\Models\GeneralCheckin;
use Livewire\Component;

class Kiosk extends Component
{
    public $scannedUser = null;
    public $scanStatus = null; // success, error
    public $scanMessage = '';

    public function processBiometricCheckin($credentialId)
    {
        if (empty($credentialId)) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'No se recibió un ID de credencial válido.';
            $this->scannedUser = null;
            return;
        }

        // Find user by biometric credential
        $user = User::where('biometric_credential_id', $credentialId)->first();

        if (!$user) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Identidad biométrica no registrada en este gimnasio.';
            $this->scannedUser = null;
            return;
        }

        // Check if member has active membership
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->first();

        $isActive = !empty($activeMembership);

        // Record check-in
        GeneralCheckin::create([
            'user_id' => $user->id,
            'checkin_time' => now(),
            'checkin_date' => now()->toDateString(),
            'method' => 'biometrics',
            'status' => $isActive ? 'active' : 'expired',
        ]);

        $this->scannedUser = [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f97316&color=fff',
            'plan_name' => $isActive ? $activeMembership->plan->name : 'Ninguno',
            'end_date' => $isActive ? \Carbon\Carbon::parse($activeMembership->end_date)->format('d/m/Y') : null,
        ];

        if ($isActive) {
            $this->scanStatus = 'success';
            $this->scanMessage = '¡Ingreso Autorizado! Bienvenido/a ' . $user->first_name;
        } else {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Acceso Denegado: Tu pase está vencido.';
        }
    }

    public function clearScan()
    {
        $this->scannedUser = null;
        $this->scanStatus = null;
        $this->scanMessage = '';
    }

    public function render()
    {
        // Load recent biometric check-ins
        $recentCheckins = GeneralCheckin::with('user')
            ->where('method', 'biometrics')
            ->orderBy('checkin_time', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.gym.admin.kiosk', [
            'recentCheckins' => $recentCheckins,
        ])->layout('layouts.tenant-app');
    }
}
