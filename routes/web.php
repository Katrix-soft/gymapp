<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', \App\Livewire\Central\TenantsManager::class)
    ->middleware(['auth', 'verified'])
    ->name('central.dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('central.profile');

require __DIR__.'/auth.php';

Route::get('/manifest.json', function () {
    return response()->json([
        'name' => 'Arkhon Gym SaaS Central',
        'short_name' => 'Arkhon SaaS',
        'description' => 'Administración y gestión de gimnasios SaaS.',
        'start_url' => '/dashboard',
        'display' => 'standalone',
        'background_color' => '#09090b',
        'theme_color' => '#f97316',
        'orientation' => 'portrait',
        'icons' => [
            [
                'src' => '/icon-192.png',
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ],
            [
                'src' => '/icon-512.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ]
        ]
    ], 200, [
        'Content-Type' => 'application/manifest+json'
    ]);
});
