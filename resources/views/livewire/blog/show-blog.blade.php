<div class="flex flex-col min-h-screen bg-white dark:bg-gray-900">
    <!-- Breadcrumbs conditionnels -->
    @unless (request()->routeIs('dashboard'))
        <section class="bg-white dark:bg-gray-900 py-4">
            <div class="px-4 mx-auto max-w-screen-xl" x-data="{ hover: false }">
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" />
                    <flux:breadcrumbs.item href="{{ route('medical.blog') }}" x-on:mouseenter="hover = true" wire:navigate
                        x-on:mouseleave="hover = false">
                        <span class="text-lg transition-colors duration-300" :class="{ 'text-blue-700': hover }">
                            {{ 'Blog' }}
                        </span>
                    </flux:breadcrumbs.item>
                    <flux:breadcrumbs.item>
                        <span class="text-lg">
                            {{ $post->category->name }}
                        </span>
                    </flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </div>
        </section>
    @endunless

    <main class="flex-1 py-4">
        <div class="flex flex-col lg:flex-row justify-between mx-auto max-w-screen-xl gap-10">
            <!-- Contenu Principal -->
            <article
                class="w-full lg:w-7/12 format format-sm sm:format-base lg:format-lg format-blue dark:format-invert">
                @if ($post->hasMedia('posts'))
                    <figure class="my-4 overflow-hidden">
                        <img class="w-full h-auto object-cover" src="{{ $post->getFirstMediaUrl('posts', 'featured') }}"
                            srcset="{{ $post->getFirstMedia('posts')->getSrcset('featured') }}"
                            alt="{{ $post->title }}" loading="lazy">
                    </figure>
                @endif

                <header class="mb-4 lg:mb-8">
                    <h1 class="mb-1 text-3xl font-bold leading-tight text-gray-900 lg:text-3xl dark:text-white">
                        {{ $post->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <flux:icon.clock class="w-4 h-4" />
                            <span>{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:icon.eye class="w-4 h-4" />
                            <livewire:post.view-counter :post="$post" />
                        </div>
                        <flux:badge variant="pill" color="indigo" class="!text-xs">
                            {{ $post->category->name }}
                        </flux:badge>
                        @foreach ($post->tags as $tag)
                            <flux:badge variant="pill" color="lime" icon="tag" class="!text-xs">
                                {{ $tag->name }}
                            </flux:badge>
                        @endforeach
                    </div>
                </header>

                <div class="prose max-w-none dark:prose-invert prose-lg">
                    {!! str($post->content)->markdown()->sanitizeHtml() !!}
                </div>

                <livewire:comments-section :post="$post" />
            </article>

            <!-- Sidebar - Réorganisée selon l'image -->
            <aside class="w-full lg:w-5/12 space-y-6 lg:sticky lg:top-24 lg:self-start">
                <!-- Auteur avec détails supplémentaires -->
                <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="flex items-center gap-4 mb-4">
                        <flux:avatar class="w-12 h-12" src="{{ $post->user->profile_photo_url }}"
                            alt="{{ $post->user->name }}" />
                        <div>
                            <p class="font-bold dark:text-white">{{ $post->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $post->user->bio ?? 'Auteur sur N-xotech' }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <flux:icon.calendar class="w-4 h-4 mr-2" />
                            <span>California, United States</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <flux:icon.calendar class="w-4 h-4 mr-2" />
                            <span>Rejoint le: September 20, 2018</span>
                        </div>
                    </div>
                </div>

                <!-- Section RECOMMENDED TOPICS -->
                <div class="p-6 bg-gray-50 dark:bg-gray-800">
                    <h3 class="mb-3 text-lg font-semibold dark:text-white">RECOMMENDED TOPICS</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <flux:badge variant="pill" color="blue" class="text-center">Technology</flux:badge>
                        <flux:badge variant="pill" color="blue" class="text-center">Monkey</flux:badge>
                        <flux:badge variant="pill" color="blue" class="text-center">C4B</flux:badge>
                        <flux:badge variant="pill" color="blue" class="text-center">Drosdroidery</flux:badge>
                        <flux:badge variant="pill" color="blue" class="text-center">Hey/sobery</flux:badge>
                        <flux:badge variant="pill" color="blue" class="text-center">Design</flux:badge>
                    </div>
                </div>

                <!-- Section WHO TO FOLLOW -->
                <div class="p-6 bg-gray-50 dark:bg-gray-800">
                    <h3 class="mb-3 text-lg font-semibold dark:text-white">WHO TO FOLLOW</h3>
                    <div class="space-y-4">
                        @foreach ([['name' => 'Bonnie Green', 'role' => 'Web developer at Facebook'], ['name' => 'Jesse Lees', 'role' => 'Engineer at Appalachia'], ['name' => 'Paul Livingston', 'role' => 'Terms designer at Adobe Inc.']] as $person)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <flux:avatar class="w-10 h-10" />
                                    <div>
                                        <p class="font-medium dark:text-white">{{ $person['name'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $person['role'] }}</p>
                                    </div>
                                </div>
                                <button type="button"
                                    class="text-xs px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    Follow
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Articles Similaires -->
                @if ($relatedPosts->count())
                    <div class="p-6 bg-gray-50 dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-semibold dark:text-white">Articles similaires</h3>
                        <div class="space-y-4">
                            @foreach ($relatedPosts as $related)
                                <article wire:key="related-{{ $related->id }}" x-data="{ hover: false }"
                                    class="bg-white shadow-sm dark:bg-gray-800 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-lg">
                                    <a wire:navigate
                                        href="{{ route('medical.blog.show', ['post' => $related->slug]) }}"
                                        class="block group transition hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            @if ($related->hasMedia('posts'))
                                                <img class="w-18 h-18 object-cover flex-shrink-0"
                                                    src="{{ $related->getFirstMediaUrl('posts', 'thumb') }}"
                                                    alt="{{ $related->title }}">
                                            @endif
                                            <div>
                                                <h4 x-on:mouseenter="hover = true" x-on:mouseleave="hover = false"
                                                    class="transition-colors duration-300 font-medium text-gray-900 group-hover:text-primary-600 dark:text-white line-clamp-2"
                                                    :class="{ 'text-blue-700': hover }">
                                                    {{ $related->title }}
                                                </h4>
                                                <time class="text-base text-gray-500 dark:text-gray-400">
                                                    {{ $related->published_at->diffForHumans() }}
                                                </time>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

            </aside>
        </div>
    </main>

    <div class="px-6 mx-auto max-w-screen-xl mt-8">
        <livewire:newsletter-subscribe />
    </div>
</div>
