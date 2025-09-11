<x-layouts.app :title="__('Accueil')">
    {{-- <div
        class="flex items-center justify-center w-full mb-22 transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="flex max-w-4xl w-full flex-col-reverse lg:max-w-7xl lg:flex-row">
            <div
                class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-linear-to-b dark:from-gray-950 dark:to-gray-900 dark:text-[#EDEDEC]">
                <h1 class="mb-1 font-bold text-6xl">
                    Bienvenue chez<br>N-XOTECH
                </h1>
                <p class="mb-2 text-lg text-[#706f6c] dark:text-[#A1A09A]">
                    L'innovation et l'expertise au service de votre transformation numérique <br>
                    Chez N-XoTech, nous combinons savoir-faire technologique <br>
                    et pédagogie pour vous offrir des services de qualité
                </p>
                <div class="flex flex-wrap items-center gap-5 pt-10">
                    <a wire:navigate href="" x-ref="feedback_header"
                        class="flex items-center justify-center gap-3 py-5 dark:bg-bleu-900 text-white transition duration-200 group/button rounded-xl bg-[#0F033A] px-7 motion-reduce:transition-none">
                        <div>Visitez notre blog</div>
                        <div
                            class="transition duration-300 group-hover/button:translate-x-1 motion-reduce:transition-none motion-reduce:group-hover/button:transform-none">
                            <svg width="24" height="25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 12.992h2.5m13.5 0-6-6m6 6-6 6m6-6H9.5" stroke="#fff" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                </div>
            </div>
            <div
                class="bg-white dark:bg-linear-to-b dark:from-gray-950 dark:to-gray-900 relative lg:-ml-px -mb-px lg:mb-0 aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
                <div
                    class="w-full text-[#F53003] dark:text-[#F61500] transition-all translate-y-0 opacity-100 max-w-none duration-750 starting:opacity-0 starting:translate-y-6">
                    <img src="{{ asset('storage/image/presentation-image.svg') }}" class="object-cover w-full h-full"
                        alt="Etudiant" loading="lazy">
                </div>
        </main>
    </div> --}}
    <div class="rounded-t-2xl overflow-hidden" x-cloak x-data="{ show_more: false } mb - 10">
    <div class="w-full max-w-screen-lg p-3 mx-auto lg:p-3">
        <div x-data="{}" x-init="() => {
            if (reducedMotion) return;
            gsap.timeline({
                    delay: 0.2,
                    scrollTrigger: {
                        trigger: $refs.header,
                        start: 'top bottom',
                    },
                })
                .fromTo(
                    $refs.header, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.7, ease: 'circ.out' }
                )
                .fromTo(
                    $refs.mockup, { autoAlpha: 0 }, { autoAlpha: 1, duration: 0.7, ease: 'circ.out' },
                    '>-0.5'
                );
        }"
            class="flex flex-col md:flex-row md:gap-0 lg:justify-between h-[530px]">

            <!-- Section Texte -->
            <div class="flex-1 flex flex-col justify-center px-10" x-ref="header" id="tweets">
                <div class="text-4xl" x-ref="twitter_icon">
                    <span class="font-bold">{{ __('StudenLink') }}</span>
                    <span
                        x-ref="feedback_header">{{ __('Plateforme d’Enregistrement et de Suivi des Étudiants') }}</span>
                </div>
                <div class="min-w-[18rem] max-w-[22rem] pt-7 font-medium text-base" x-ref="community">
                    Nous vous offrons un espace centralisé, sécurisé et facile d’accès pour enregistrer des
                    étudiants.<br />
                </div>
                <div class="flex flex-wrap items-center gap-5 pt-10">
                    <a wire:navigate href="" x-ref="feedback_header"
                        class="flex items-center justify-center gap-3 text-lg py-5 text-white transition duration-200 group/button rounded-lg bg-[#0F033A] px-7 motion-reduce:transition-none">
                        <div>Visitez notre blog</div>
                        <div
                            class="transition duration-300 group-hover/button:translate-x-1 motion-reduce:transition-none motion-reduce:group-hover/button:transform-none">
                            <svg width="24" height="25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 12.992h2.5m13.5 0-6-6m6 6-6 6m6-6H9.5" stroke="#fff" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section Image -->
            <div class="flex-1 h-full">
                <img src="{{ asset('storage/images/Learning-cuate.svg') }}" class="object-cover w-full h-full"
                    alt="Etudiant" loading="lazy">
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
