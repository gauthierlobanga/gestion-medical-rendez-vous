<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (
            !Auth::guard('web')->validate([
                'email' => Auth::user()->email,
                'password' => $this->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    protected array $messages = [
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
    ];
}; ?>

<x-filament::card>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Mot de passe de confirmation')" :description="__(
            'Ceci est une zone sécurisée de l\'application. Veuillez confirmer votre mot de passe avant de continuer.',
        )" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="confirmPassword" class="flex flex-col gap-6">
            <!-- Password -->
            <flux:input wire:model="password" :label="__('Mot de passe')" type="password" autocomplete="new-password"
                :placeholder="__('Password')" viewable />

            <flux:button variant="primary" type="submit" class="w-full">{{ __('Confirmer') }}</flux:button>
        </form>
    </div>
</x-filament::card>
