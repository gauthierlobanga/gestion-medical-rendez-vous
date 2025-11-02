<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('Un lien de réinitialisation sera envoyé si le compte existe.'));
    }

    protected array $messages = [
        'email.required' => 'L\'adresse e-mail est obligatoire.',
        'email.string' => 'L\'adresse e-mail doit être une chaîne de caractères.',
        'email.email' => 'Veuillez saisir une adresse e-mail valide.',
    ];
}; ?>
<x-filament::card>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Mot de passe oublié')" :description="__('Entrez votre adresse e-mail pour recevoir un lien de réinitialisation du mot de passe.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
            <!-- Email Address -->
            <flux:input wire:model="email" :label="__('Adresse e-mail')" type="email" autofocus
                placeholder="email@example.com" />

            <flux:button variant="primary" type="submit" class="w-full cursor-pointer bg-blue-800 dark:bg-blue-800 text-white dark:text-white">
                {{ __('Envoyer le lien de réinitialisation du mot de passe') }}</flux:button>
        </form>

        <div class="space-x-1 text-center text-sm text-zinc-400">
            {{ __('Ou, retournez à') }}
            <flux:link :href="route('login')" wire:navigate>{{ __('Se connecter') }}</flux:link>
        </div>
    </div>
</x-filament::card>
