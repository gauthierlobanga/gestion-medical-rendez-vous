<section class="bg-white dark:bg-linear-to-b dark:from-gray-950 dark:to-gray-900 py-8">
    {{-- <div class="mx-auto max-w-screen-xl"> --}}

    <!-- Filtres et en-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div class="max-w-2xl">
            <h1 class="text-gray-900 dark:text-white text-6xl md:text-6xl font-extrabold mb-8">
                Blog
                <span class="text-blue-600">
                    N-xotech
                </span>
            </h1>
            <p class="text-lg text-gray-500 md:text-xl dark:text-gray-400font-normal mb-4">
                Découvrez nos dernières publications et ressources techniques
            </p>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div
        class="mt-4 md:mt-8 flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0">
        <div class="flex flex-wrap items-start gap-2">

            <!-- Bouton "Toutes" -->
            <button wire:click="$set('filterCategory', '')"
                class="px-3 py-1 text-sm rounded-full border transition cursor-pointer
                                {{ $filterCategory === '' ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white border-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Toutes
            </button>

            <!-- Autres catégories -->
            @foreach ($categories as $category)
                <button wire:key="category-{{ $category->id }}"
                    wire:click="$set('filterCategory', '{{ $category->slug }}')"
                    class="px-3 py-1 text-sm rounded-full border transition cursor-pointer
                        {{ $filterCategory == $category->slug ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white border-gray-300 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Liste des articles -->
    <div x-data="{
        init() {
            // Initialiser GSAP
            gsap.registerPlugin(ScrollTrigger);
    
            // Observer les changements Livewire
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                // Avant chaque requête
                const cards = this.$el.querySelectorAll('.training-card');
                if (cards.length) {
                    // Animation de sortie
                    gsap.to(cards, {
                        opacity: 0,
                        y: 10,
                        duration: 0.3,
                        stagger: 0.05,
                        onComplete: () => {
                            // Après l'animation, Livewire mettra à jour le DOM
                        }
                    });
                }
    
                succeed(() => {
                    // Après le succès de la requête
                    setTimeout(() => {
                        const newCards = this.$el.querySelectorAll('.training-card');
                        if (newCards.length) {
                            // Animation d'entrée
                            gsap.from(newCards, {
                                opacity: 0,
                                y: 30,
                                duration: 0.5,
                                stagger: 0.07,
                                ease: 'power2.out',
                                delay: 0.1
                            });
    
                            // Animation au survol
                            newCards.forEach(card => {
                                card.addEventListener('mouseenter', () => {
                                    gsap.to(card, {
                                        y: -5,
                                        boxShadow: '0 5px 10px -3px rgba(0, 0, 0, 0.1), 0 5px 5px -3px rgba(0, 0, 0, 0.04)',
                                        duration: 0.3
                                    });
                                });
    
                                card.addEventListener('mouseleave', () => {
                                    gsap.to(card, {
                                        y: 0,
                                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                                        duration: 0.3
                                    });
                                });
                            });
                        }
                    }, 50);
                });
            });
        }
    }" class="mt- md:mt-8 grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($posts as $post)
            <article wire:key="post-{{ $post->id }}" x-data="{ hover: false }"
                class="bg-white shadow-sm training-card dark:bg-gray-800 rounded-lg overflow-hidden transition-all duration-300"
                x-transition>
                <a wire:navigate href="{{ route('medical.blog.show', ['post' => $post->slug]) }}" class="block">
                    @if ($post->hasMedia('posts'))
                        <img src="{{ $post->getFirstMediaUrl('posts', 'card') }}"
                            srcset="{{ $post->getFirstMedia('posts')->getSrcset('card') }}" alt="{{ $post->title }}"
                            class="w-full h-48 object-cover" loading="lazy">
                    @endif
                </a>
                <div class="p-4">
                    <div class="flex justify-between items-center mb-2 text-gray-500 text-base">
                        <flux:badge variant="pill" color="indigo">
                            {{ $post->category->name }}
                        </flux:badge>
                        <span>{{ $post->published_at->diffForHumans() }}</span>
                    </div>

                    <h3 class="mb-2 text-lg font-medium text-gray-700 dark:text-gray-300">
                        <a wire:navigate href="{{ route('medical.blog.show', ['post' => $post->slug]) }}"
                            x-on:mouseenter="hover = true" x-on:mouseleave="hover = false">
                            <span class="transition-colors duration-300" :class="{ 'text-blue-500': hover }">
                                {{ $post->title }}
                            </span>
                        </a>
                    </h3>

                    <p class="text-gray-500 flex-1 mb-4">
                        {!! str(Str::limit(strip_tags($post->content), 80))->markdown()->sanitizeHtml() !!}
                    </p>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-12">
                <flux:icon.document-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Aucun article trouvé</h3>
                <p class="mt-1 text-gray-500 dark:text-gray-400">
                    Aucun article ne correspond à vos filtres.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-4 flex justify-end items-center">
        {{ $posts->links(data: ['scrollTo' => false]) }}
    </div>
</section>
