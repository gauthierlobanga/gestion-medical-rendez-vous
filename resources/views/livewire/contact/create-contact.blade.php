<section>
    <!-- Message flash corrigé -->
    <div class="fixed top-20 flex justify-end inset-x-0 z-50 right-5">
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5000)"
                class="w-full max-w-lg transition-all duration-500" x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-8">
                <flux:callout role="alert" icon="bell-alert" variant="success" class="w-full">
                    <flux:callout.heading>
                        {{ __('Envoi réussi') }}
                    </flux:callout.heading>
                    <flux:callout.text>
                        {{ session('success') }}
                    </flux:callout.text>
                </flux:callout>
            </div>
        @endif
    </div>
    <form wire:submit.prevent="save">
        {{-- {{ $this->contactForm }} --}}
        {{ $this->form }}

        {{-- <flux:button type="submit" variant="primary"
                class="sm:w-auto px-6 py-3 bg-green-800 dark:bg-green-800 dark:text-gray-50 hover:bg-green-700 text-white rounded-lg transition-colors">
                {{ __('Envoyer') }}
            </flux:button> --}}
    </form>
    <x-filament-actions::modals />
</section>
