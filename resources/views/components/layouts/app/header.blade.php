<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-linear-to-b dark:from-zinc-900 dark:to-zinc-900">
    <!-- Navbar Menu -->
    <flux:header container class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="right" />

        <a href="{{ route('dashboard') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navbar class="-mb-px max-lg:hidden" x-data="{ hover: false }">
            <flux:navbar.item :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                <span class="text-base">
                    {{ __('Accueil') }}
                </span>
            </flux:navbar.item>
            {{-- <flux:navbar.item :href="route('medical.service')" :current="request()->routeIs('medical.service')"
                wire:navigate>
                <span class="text-base">
                    {{ __('Services') }}
                </span>
            </flux:navbar.item>
            <flux:navbar.item :href="route('medical.contact')" :current="request()->routeIs('medical.contact')"
                wire:navigate>
                <span class="text-base">
                    {{ __('Contact') }}
                </span>
            </flux:navbar.item>
            <flux:navbar.item :href="route('medical.about')" :current="request()->routeIs('medical.about')"
                wire:navigate>
                <span class="text-base">
                    {{ __('About') }}
                </span>
            </flux:navbar.item>
            <flux:navbar.item :href="route('medical.blog')" :current="request()->routeIs('medical.blog')"
                wire:navigate>
                <span class="text-base">
                    {{ __('Blog') }}
                </span>
            </flux:navbar.item> --}}
        </flux:navbar>

        <flux:spacer />

        <flux:navbar class="hidden lg:flex mr-1.5 space-x-0.5 py-0!">
            <flux:tooltip :content="__('Search')" position="bottom">
                <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#"
                    :label="__('Search')" />
            </flux:tooltip>
            {{-- Mode sombre avec tooltip --}}
            <div x-cloak x-data="{ tooltip: false }" class="relative">
                <flux:button x-on:click="$flux.dark = !$flux.dark" x-on:mouseenter="tooltip = true"
                    x-on:mouseleave="tooltip = false" variant="subtle" square class="group"
                    aria-label="Toggle dark mode">
                    <flux:icon.sun x-show="!$flux.dark" variant="mini"
                        class="text-zinc-500 dark:text-white transition-all duration-300" />
                    <flux:icon.moon x-show="$flux.dark" variant="mini"
                        class="text-zinc-500 dark:text-white transition-all duration-300" />
                </flux:button>

                <div x-show="tooltip" x-transition
                    class="absolute -top-10 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-zinc-900 text-white text-xs rounded whitespace-nowrap">
                    <span x-text="!$flux.dark ? 'Activer le mode sombre' : 'Activer le mode clair'"></span>
                </div>
            </div>

            @guest
                <flux:navbar.item :href="route('login')" wire:navigate
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                    {{ __('Se connecter') }}
                </flux:navbar.item>
                <flux:navbar.item :href="route('register')" wire:navigate
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                    {{ __('S\'inscrire') }}
                </flux:navbar.item>
            @endguest
        </flux:navbar>

        <!-- Desktop User Menu -->
        @auth
            <flux:dropdown position="top" align="end" class="bg-zinc-50 dark:bg-zinc-900">
                <flux:profile icon:trailing="chevron-up-down" circle
                    avatar="{{ auth()->user()->avatar_url ?? auth()->user()->initials() }}" class="cursor-pointer"
                    :initials="auth()->user()->initials()" />
                <flux:menu class="bg-zinc-50 dark:bg-zinc-900">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        <flux:avatar circle badge badge:color="green" badge:circle
                                            src="{{ auth()->user()->avatar_url ?? auth()->user()->initials() }}" />
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                            {{ __('Profil') }}
                        </flux:menu.item>
                        @if (auth()->user()->hasRole('Super Admin'))
                            <flux:menu.item :href="route('filament.admin.home')" icon="chart-bar" target="_blank">
                                {{ __('admin panel') }}
                            </flux:menu.item>
                        @endif
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Se déconnecter') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @endauth
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="ml-1 flex items-center space-x-2">
            <x-app-logo />
        </a>

        <flux:navlist variant="outline" x-data="{ hover: false }">
            <flux:navlist.group :heading="__('HP-Medecal')">
                <flux:navlist.item :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                    <span class="text-base">
                        {{ __('Accueil') }}
                    </span>
                </flux:navlist.item>
                <flux:navlist.item :href="route('medical.service')" :current="request()->routeIs('medical.service')"
                    wire:navigate>
                    <span class="text-base">
                        {{ __('Services') }}
                    </span>
                </flux:navlist.item>
                <flux:navlist.item :href="route('medical.contact')" :current="request()->routeIs('medical.contact')"
                    wire:navigate>
                    <span class="text-base">
                        {{ __('Contact') }}
                    </span>
                </flux:navlist.item>
                <flux:navlist.item :href="route('medical.about')" :current="request()->routeIs('medical.about')"
                    wire:navigate>
                    <span class="text-base">
                        {{ __('About') }}
                    </span>
                </flux:navlist.item>
                <flux:navlist.item :href="route('medical.blog')" :current="request()->routeIs('medical.blog')"
                    wire:navigate>
                    <span class="text-base">
                        {{ __('Blog') }}
                    </span>
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            @guest
                <flux:navlist.item :href="route('login')" wire:navigate
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                    {{ __('Se connecter') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('register')" wire:navigate
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                    {{ __('S\'inscrire') }}
                </flux:navlist.item>
            @endguest
        </flux:navlist>
    </flux:sidebar>
    {{ $slot }}
    <flux:separator />
    <!--Footer -->
    <!-- Flux Scripts -->
    @fluxScripts
    <!-- Filament Scripts -->
    @filamentScripts
    <!-- Livewire Scripts -->
    @livewireScripts
    <!-- Livewire notifications -->
    @livewire('notifications')
    <!-- Flowbite Scripts -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script> --}}
</body>

</html>
