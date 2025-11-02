<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('medical.accueil') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse"
            wire:navigate>
            <x-app-logo />
        </a>
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Tableau de bord') }}
                </flux:navlist.item>
                <flux:navlist.item icon="calendar" :href="route('medecin.agenda')"
                    :current="request()->routeIs('medecin.agenda')" wire:navigate>
                    {{ __('Mon agenda') }}
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-check" :href="route('medecin.rendezvous')"
                    :current="request()->routeIs('medecin.rendezvous')" wire:navigate>
                </flux:navlist.item>
                <flux:navlist.item icon="users" :href="route('medecin.patients')"
                    :current="request()->routeIs('medecin.patients')" wire:navigate>{{ __('Patients') }}
                </flux:navlist.item>
                <flux:navlist.item icon="calendar-days" :href="route('medecin.disponibilites')"
                    :current="request()->routeIs('medecin.disponibilites')" wire:navigate>
                    {{ __('Disponibilités') }}
                </flux:navlist.item>
                <flux:navlist.item icon="rectangle-stack" :href="route('medecin.service')"
                    :current="request()->routeIs('medecin.service')" wire:navigate>
                    {{ __('Service') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        @guest
            <flux:navbar>
                <flux:navbar.item :href="route('login')" wire:navigate
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                    {{ __('Se connecter') }}
                </flux:navbar.item>
                <flux:navbar.item :href="route('register')" wire:navigate
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                    {{ __('S\'inscrire') }}
                </flux:navbar.item>
            </flux:navbar>
        @endguest

        <!-- Desktop User Menu -->
        @auth
            <flux:dropdown position="top" align="end" class="bg-zinc-50 dark:bg-gray-900">
                <flux:profile name="{{ auth()->user()->name }}" icon:trailing="chevron-up-down" circle
                    avatar="{{ auth()->user()->avatar_url ?? auth()->user()->initials() }}" class="cursor-pointer"
                    :initials="auth()->user()->initials()" />
                <flux:menu class="bg-zinc-50 dark:bg-gray-900">
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
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>

                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
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
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
