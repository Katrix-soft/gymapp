<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenancyServiceProvider.
|
*/

// Root URL redirects to login
Route::get('/', function () {
    return redirect()->route('login');
})->name('tenant.index');

// Shared Dashboard Router: inspects roles and redirects
Route::get('/dashboard', function () {
    $user = auth()->user();
    $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';

    if ($user->hasRole('gym_admin')) {
        return redirect($prefix . '/admin/dashboard');
    } elseif ($user->hasRole('trainer')) {
        return redirect($prefix . '/trainer/dashboard');
    } elseif ($user->hasRole('member')) {
        return redirect($prefix . '/member/dashboard');
    }
    abort(403, 'Rol no autorizado.');
})->middleware(['auth'])->name('dashboard');

// Auth routes (Breeze) scoped to the tenant
require __DIR__.'/auth.php';

Route::get('/logout', function (\App\Livewire\Actions\Logout $logout) {
    $logout();
    $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
    return redirect($prefix . '/login');
})->name('logout');

Route::get('/profile', function () {
    return view('tenant-profile');
})->middleware(['auth'])->name('profile');

Route::get('/manifest.json', function () {
    $gymName = \App\Models\TenantConfig::get('gym_name', 'SaaS Gym');
    $brandColor = \App\Models\TenantConfig::get('brand_color', '#f97316');
    $logoUrl = \App\Models\TenantConfig::get('logo_url') ?: '/icon-192.png';
    $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';

    return response()->json([
        'name' => $gymName . ' Portal',
        'short_name' => $gymName,
        'description' => 'Tu portal de socios para reservar clases, ver rutinas y realizar pagos.',
        'start_url' => $prefix . '/dashboard',
        'display' => 'standalone',
        'background_color' => '#09090b',
        'theme_color' => $brandColor,
        'orientation' => 'portrait',
        'icons' => [
            [
                'src' => $logoUrl,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ],
            [
                'src' => $logoUrl,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ]
        ]
    ]);
});

// Gym Admin Portal
Route::middleware(['auth', 'role:gym_admin'])->prefix('admin')->name('gym.admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Gym\Admin\Dashboard::class)->name('dashboard');
    Route::get('/members', \App\Livewire\Gym\Admin\Members::class)->name('members');
    Route::get('/classes', \App\Livewire\Gym\Admin\Classes::class)->name('classes');
    Route::get('/payments', \App\Livewire\Gym\Admin\Payments::class)->name('payments');
    Route::get('/routines', \App\Livewire\Gym\Admin\Routines::class)->name('routines');
    Route::get('/chat', \App\Livewire\Gym\Admin\Chat::class)->name('chat');
});

// Trainer Portal
Route::middleware(['auth', 'role:trainer'])->prefix('trainer')->name('gym.trainer.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Gym\Trainer\Dashboard::class)->name('dashboard');
});

// Member Portal
Route::middleware(['auth', 'role:member'])->prefix('member')->name('gym.member.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Gym\Member\Dashboard::class)->name('dashboard');
    Route::get('/bookings', \App\Livewire\Gym\Member\Bookings::class)->name('bookings');
    Route::get('/workout', \App\Livewire\Gym\Member\Workout::class)->name('workout');
    Route::get('/checkout-simulation', \App\Livewire\Gym\Member\CheckoutSimulation::class)->name('checkout.simulation');
    Route::get('/chat', \App\Livewire\Gym\Member\Chat::class)->name('chat');

    // Payments routes
    Route::get('/payment/success', function() {
        session()->flash('message', '¡Membresía activada exitosamente!');
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    })->name('payment.success');

    Route::get('/payment/pending', function() {
        session()->flash('message', 'Tu pago está pendiente de aprobación.');
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    })->name('payment.pending');

    Route::get('/payment/failure', function() {
        session()->flash('error', 'El pago fue rechazado. Intenta de nuevo.');
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    })->name('payment.failure');
});
