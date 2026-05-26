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
        // Force HTTPS in production (behind Easypanel/nginx reverse proxy)
        if (app()->environment('production') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
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
