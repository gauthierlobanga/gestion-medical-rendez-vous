<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    protected array $messages = [
        'name.required' => 'Le nom d\'utilisateur est obligatoire.',
        'name.string' => 'Le nom d\'utilisateur doit être une chaîne de caractères.',
        'name.max' => 'Le nom d\'utilisateur ne doit pas dépasser 255 caractères.',
        'email.required' => 'L\'adresse e-mail est obligatoire.',
        'email.string' => 'L\'adresse e-mail doit être une chaîne de caractères.',
        'email.lowercase' => 'L\'adresse e-mail doit être en minuscules.',
        'email.email' => 'Veuillez saisir une adresse e-mail valide.',
        'email.max' => 'L\'adresse e-mail ne doit pas dépasser 255 caractères.',
        'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
        'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
    ];
}; ?>

<x-filament::card>
    <div class="flex flex-col gap-6 w-full max-w-lg">
        <x-auth-header :title="__('Inscrivez-vous')" :description="__('')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="register" class="flex flex-col gap-6">
            <!-- Name -->
            <flux:input wire:model="name" :label="__('Nom d\'utilisateur')" type="text" autofocus autocomplete="name"
                :placeholder="__('Full name')" />

            <!-- Email Address -->
            <flux:input wire:model="email" :label="__('Adresse e-mail')" type="email" autocomplete="email"
                placeholder="email@example.com" />

            <!-- Password -->
            <flux:input wire:model="password" :label="__('Mot de passe')" type="password" autocomplete="new-password"
                :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password"
                autocomplete="new-password" :placeholder="__('Confirmez le mot de passe')" />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary"
                    class="w-full bg-blue-800 dark:bg-blue-800 text-white dark:text-white">
                    {{ __('S\'inscrire') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Vous avez déjà un compte ?') }}
            <flux:link :href="route('login')" wire:navigate>{{ __('Se connecter') }}</flux:link>
        </div>
    </div>
</x-filament::card>
