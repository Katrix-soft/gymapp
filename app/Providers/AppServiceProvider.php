<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Automatically inject tenant parameter into URL generation when tenancy is active
        \Illuminate\Support\Facades\Event::listen(
            \Stancl\Tenancy\Events\TenancyInitialized::class,
            function (\Stancl\Tenancy\Events\TenancyInitialized $event) {
                \Illuminate\Support\Facades\URL::defaults([
                    'tenant' => $event->tenancy->tenant->id
                ]);
            }
        );

        if (!app()->runningInConsole() && request()->segment(1) === 'g') {
            \Livewire\Livewire::setUpdateRoute(function ($handle) {
                return \Illuminate\Support\Facades\Route::post('/g/{tenant}/livewire/update', $handle)
                    ->middleware([
                        'web',
                        \Stancl\Tenancy\Middleware\InitializeTenancyByPath::class,
                    ]);
            });
        }
    }
}
