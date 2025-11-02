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
            <div class="flex-1 flex flex-col justify-center px-4" x-ref="header" id="tweets">
                <div>
                    <flux:heading>
                        <span class="text-6xl font-extrabold">
                            The Laravel Podcast
                            {{-- <flux:badge inset="top bottom" class="ml-1 max-sm:hidden">New</flux:badge> --}}
                        </span>
                    </flux:heading>

                    <flux:text class="mt-2">
                        A podcast about Laravel, development best practices, and the PHP ecosystem—hosted by Jeffrey
                        Way, Matt Stauffer, and Taylor Otwell, later joined by Adam Wathan.
                    </flux:text>

                    <flux:avatar.group class="mt-6">
                        <flux:avatar circle size="lg" src="https://unavatar.io/x/taylorotwell" />
                        <flux:avatar circle size="lg" src="https://unavatar.io/x/adamwathan" />
                        <flux:avatar circle size="lg" src="https://unavatar.io/x/jeffrey_way" />
                        <flux:avatar circle size="lg" src="https://unavatar.io/x/stauffermatt" />
                    </flux:avatar.group>
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
