<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ];

    protected array $messages = [
        'email.required' => 'L\'adresse e-mail est obligatoire.',
        'email.email' => 'Veuillez saisir une adresse e-mail valide.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
    ];

    public function updated($propertyName)
    {
        $this->resetErrorBag($propertyName);
        $this->validateOnly($propertyName);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: false);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>
<x-filament::card>
    <div class="flex flex-col gap-6 max-w-7xl">

        <x-auth-header :title="__('Connectez-vous')" :description="__('')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="login" class="flex flex-col gap-6">

            <div class="relative">
                <!-- Email Address -->
                <flux:input wire:model="email" :label="__('Nom d\'utilisateur ou adresse e-mail')" type="email"
                    autofocus autocomplete="email" placeholder="email@example.com" />
            </div>

            <!-- Password -->
            <div class="relative">
                <flux:input wire:model="password" :label="__('Mot de passe')" type="password"
                    autocomplete="current-password" :placeholder="__('Password')" viewable />

                @if (Route::has('password.request'))
                    <flux:link class="absolute right-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                        {{ __('Mot de passe oublié?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('Souviens-toi de moi')" />

            <div class="flex items-center justify-end">
                <flux:button class="w-full cursor-pointer bg-blue-800 dark:bg-blue-800 text-white dark:text-white"
                    variant="primary" type="submit">{{ __('Se connecter') }}</flux:button>
            </div>
        </form>
        @if (Route::has('register'))
            <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Pas encore inscrit ?') }}
                <flux:link :href="route('register')" wire:navigate>{{ __('Inscrivez-vous') }}</flux:link>
            </div>
        @endif
    </div>
</x-filament::card>
