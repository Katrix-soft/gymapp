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
