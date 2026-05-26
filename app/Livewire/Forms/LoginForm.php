<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $email = '';

    #[Validate('required|string|min:6|max:128')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginValue = trim($this->email);
        $resolvedEmail = $loginValue;

        if (!str_contains($loginValue, '@')) {
            // Check if it's a tenant or central user
            if (tenant()) {
                // Try matching gym_code first
                $resolvedUser = \App\Models\User::where('gym_code', $loginValue)->first();
                // If not found, try matching prefix of the email (email LIKE 'username@%')
                if (!$resolvedUser) {
                    $resolvedUser = \App\Models\User::where('email', 'like', $loginValue . '@%')->first();
                }
                if ($resolvedUser) {
                    $resolvedEmail = $resolvedUser->email;
                }
            } else {
                // Central context login
                $resolvedUser = \App\Models\CentralUser::where('email', 'like', $loginValue . '@%')->first();
                if ($resolvedUser) {
                    $resolvedEmail = $resolvedUser->email;
                }
            }
        }

        if (! Auth::attempt(['email' => $resolvedEmail, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey(), 900); // 15 minutes lockout

            // Log failed attempt for security monitoring
            Log::warning('Intento de login fallido', [
                'email' => $this->email,
                'resolved_email' => $resolvedEmail,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'tenant' => tenant('id') ?? 'central',
            ]);

            throw ValidationException::withMessages([
                'form.email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = ceil($seconds / 60);

        Log::warning('Login bloqueado por rate limiting', [
            'email' => $this->email,
            'ip' => request()->ip(),
            'seconds_remaining' => $seconds,
            'tenant' => tenant('id') ?? 'central',
        ]);

        throw ValidationException::withMessages([
            'form.email' => "Demasiados intentos. Espera {$minutes} minuto(s) antes de reintentar.",
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        $tenantId = tenant('id') ?? 'central';
        return Str::transliterate($tenantId.'|'.Str::lower($this->email).'|'.request()->ip());
    }
}
