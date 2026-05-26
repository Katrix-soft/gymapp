<?php

namespace App\Livewire\Gym\Admin;

use App\Models\User;
use App\Models\Membership;
use App\Models\GeneralCheckin;
use Livewire\Component;

class Scanner extends Component
{
    public $scannedUser = null;
    public $scanStatus = null; // success, error
    public $scanMessage = '';

    public function processScan($code)
    {
        // Expected format: GYM-MEMBER-{id}
        if (!str_starts_with($code, 'GYM-MEMBER-')) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Código QR no reconocido / Inválido.';
            $this->scannedUser = null;
            return;
        }

        $userId = (int) str_replace('GYM-MEMBER-', '', $code);
        $user = User::find($userId);

        if (!$user) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Socio no encontrado en el sistema.';
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
            'method' => 'qr_code',
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
            $this->scanMessage = 'Acceso Denegado: Membresía Vencida o Inexistente.';
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
        // Load recent check-ins
        $recentCheckins = GeneralCheckin::with('user')
            ->orderBy('checkin_time', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.gym.admin.scanner', [
            'recentCheckins' => $recentCheckins,
        ])->layout('layouts.tenant-app');
    }
}
