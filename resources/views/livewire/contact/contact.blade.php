<div class="relative h-full flex-1 overflow-hidden">
    <section class="bg-white dark:bg-linear-to-b dark:from-gray-950 dark:to-gray-900">
        <div class=" py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
            <h2 class="mb-4 text-5xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white">
                Contactez-nous
            </h2>
            <p class="mb-4 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl">
                Vous avez un problème technique ? Une remarque sur une fonctionnalité ? Besoin d'infos sur notre plan
                entreprise ? Dites-le-nous.
            </p>
            <!-- Message flash corrigé -->
            <div class="fixed top-20 flex justify-end inset-x-0 z-50 right-5">
                @if (session()->has('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5000)"
                        class="w-full max-w-lg transition-all duration-500"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 translate-y-0"
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
            <x-filament::card>
                <!-- indicator de step-->
                <div x-data="{ currentStep: $wire.entangle('step') }" class="my-2">
                    <ol
                        class="flex items-center justify-center w-full p-3 space-x-2 text-sm font-medium text-center text-gray-500 bg-white dark:text-gray-400 sm:text-base dark:bg-gray-900 sm:p-4 sm:space-x-4 rtl:space-x-reverse">
                        <template x-for="(step, index) in 3" :key="index">
                            <li class="flex items-center">
                                <div :class="{
                                    'text-blue-600 dark:text-blue-500': currentStep > index + 1,
                                    'text-blue-600 dark:text-blue-500': currentStep === index + 1
                                }"
                                    class="flex items-center transition-all duration-300">
                                    <span
                                        class="relative flex items-center justify-center w-6 h-6 me-2 text-xs border rounded-full shrink-0"
                                        :class="{
                                            'border-blue-600 bg-blue-100/20': currentStep > index + 1 || currentStep ===
                                                index + 1,
                                            'border-gray-500 dark:border-gray-400': currentStep < index + 1
                                        }">
                                        <span x-show="currentStep <= index + 1"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 scale-50"
                                            x-transition:enter-end="opacity-100 scale-100" class="absolute"
                                            x-text="index + 1"></span>

                                        <svg x-show="currentStep > index + 1"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 scale-50"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="w-8 h-5 text-green-500" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                                        </svg>
                                    </span>

                                    <span class="transition-colors duration-300"
                                        :class="{
                                            'font-semibold text-blue-600 dark:text-blue-500': currentStep === index + 1,
                                            'text-gray-700 dark:text-gray-300': currentStep !== index + 1
                                        }">
                                        <template x-if="index === 0">Personal Info</template>
                                        <template x-if="index === 1">Account Info</template>
                                        <template x-if="index === 2">Review</template>
                                    </span>
                                </div>

                                <svg x-show="index < 2"
                                    class="w-5 h-5 ms-2 sm:ms-4 rtl:rotate-180 text-gray-400 dark:text-gray-500"
                                    :class="{
                                        'text-blue-600 dark:text-blue-500': currentStep > index + 1
                                    }"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 12 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                                </svg>
                            </li>
                        </template>
                    </ol>
                </div>

                <!-- Formulaire de contact step-->
                <div class="flex justify-center items-center pb-8">
                    <form wire:submit.prevent="{{ $step == 3 ? 'submitForm' : 'nextStep' }}" method="POST"
                        class="flex flex-col gap-6 w-full max-w-lg">
                        @csrf

                        <!-- Étape 1 -->
                        @if ($step == 1)
                            <flux:input wire:model.live.debounce.250ms="personalForm.firstname" :label="__('Nom')"
                                type="text" :placeholder="__('Nom')" />
                            <flux:input wire:model.live.debounce.250ms="personalForm.lastname" :label="__('Prénom')"
                                type="text" :placeholder="__('Prénom')" />
                            <flux:input wire:model.live.debounce.250ms="personalForm.email"
                                :label="__('Adresse e-mail')" type="email" placeholder="email@example.com" />
                        @endif

                        <!-- Étape 2 -->
                        @if ($step == 2)
                            <flux:select wire:model.live.debounce.250ms="locationForm.country_id"
                                :label="__('Pays')" placeholder="Choisir un pays">
                                @foreach ($countries as $country)
                                    <flux:select.option wire:key="country-{{ $country['id'] }}"
                                        value="{{ $country['id'] }}" class="bg-gray-900">
                                        {{ $country['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:select wire:model.live.debounce.250ms="locationForm.city_id" :label="__('Ville')"
                                placeholder="Choisir une ville">
                                <flux:select.option value="">Choisir une ville</flux:select.option>
                                @if ($locationForm->country_id)
                                    @foreach ($cities as $city)
                                        <flux:select.option wire:key="city-{{ $city['id'] }}"
                                            value="{{ $city['id'] }}">
                                            {{ $city['name'] }}
                                        </flux:select.option>
                                    @endforeach
                                @endif
                            </flux:select>

                            <flux:field>
                                <flux:label>Téléphone</flux:label>
                                <flux:input.group>
                                    <flux:select wire:model.live.debounce.250ms="locationForm.phoneCode"
                                        class="max-w-fit w-32">
                                        @if ($locationForm->country_id)
                                            <flux:select.option value="{{ $locationForm->phoneCode }}">
                                                {{ $locationForm->iso2 }} +{{ $locationForm->phoneCode }}
                                            </flux:select.option>
                                        @endif
                                    </flux:select>
                                    <flux:input wire:model.live.debounce.250ms="locationForm.phone" type="tel"
                                        placeholder="(+{{ $locationForm->phoneCode ?? '555' }}) 55 555 55 55"
                                        mask="(999) 99 999 99 99" :disabled="!$locationForm->country_id" />
                                </flux:input.group>
                                <flux:error name="locationForm.phone" />
                            </flux:field>
                        @endif

                        <!-- Étape 3 -->
                        @if ($step == 3)
                            <flux:select wire:model.live.debounce.250ms="messageForm.subject_id" :label="__('Sujet')"
                                :placeholder="__('Sélectionnez un sujet')">
                                @foreach ($subjects as $id => $value)
                                    <flux:select.option wire:key="city-{{ $id }}"
                                        value="{{ $id }}">
                                        {{ $value }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:textarea wire:model.live.debounce.250ms="messageForm.message"
                                :label="__('Description')" :placeholder="__('Rédiger votre message...')"
                                :description:trailing="__('Le message doit contenir au moins 10 caractères.')" />
                        @endif

                        <!-- Navigation des étapes -->
                        <div class="flex justify-between">
                            @if ($step > 1)
                                <flux:button variant="primary" wire:click="previousStep"
                                    class="bg-blue-800 dark:bg-blue-800 text-white dark:text-white">
                                    {{ __('Précédent') }}
                                </flux:button>
                            @endif
                            <flux:button type="submit" variant="primary"
                                class="{{ $step == 3 ? 'bg-emerald-700 dark:bg-emerald-700' : 'bg-blue-800 dark:bg-blue-800' }}text-white dark:text-white">
                                {{ $step == 3 ? __('Envoyer') : __('Suivant') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </x-filament::card>
        </div>
    </section>

</div>
<!-- Modal -->
@script
    <script>
        const modalEl = document.getElementById('info-popup');
        const privacyModal = new Modal(modalEl, {
            placement: 'center'
        });

        privacyModal.show();

        const closeModalEl = document.getElementById('close-modal');
        closeModalEl.addEventListener('click', function() {
            privacyModal.hide();
        });

        const acceptPrivacyEl = document.getElementById('confirm-button');
        acceptPrivacyEl.addEventListener('click', function() {
            alert('privacy accepted');
            privacyModal.hide();
        });
    </script>
@endscript
