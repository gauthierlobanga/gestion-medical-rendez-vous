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
                {{-- <div class="flex flex-wrap items-center gap-5 pt-10">
                    <a wire:navigate href="{{ route('medical.blog') }}" x-ref="feedback_header"
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
                </div> --}}
            </div>
            

            <!-- Section Image -->
            <div class="flex-1 h-full">
                <img src="{{ asset('storage/images/Learning-cuate.svg') }}" class="object-cover w-full h-full"
                    alt="Etudiant" loading="lazy">
            </div>
        </div>
    </div>
</div>
