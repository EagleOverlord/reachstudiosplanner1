<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        @livewireScripts
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <!-- Navigation Menu -->
            <flux:navlist variant="outline">
                <flux:navlist.group :heading="'Platform'" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        Dashboard
                    </flux:navlist.item>

                    <!-- Create Schedule with Icon -->
                    <flux:navlist.item icon="calendar" :href="route('schedule.create')" :current="request()->routeIs('schedule.create', 'schedule.edit')" wire:navigate>
                        Create Schedule
                    </flux:navlist.item>

                </flux:navlist.group>

                <flux:navlist.group :heading="'Settings'" class="grid mt-4">
                    <flux:navlist.item icon="user" :href="route('settings.profile')" :current="request()->routeIs('settings.profile')" wire:navigate>
                        Profile
                    </flux:navlist.item>
                    <flux:navlist.item icon="key" :href="route('settings.password')" :current="request()->routeIs('settings.password')" wire:navigate>
                        Password
                    </flux:navlist.item>
                    <flux:navlist.item icon="moon" :href="route('settings.appearance')" :current="request()->routeIs('settings.appearance')" wire:navigate>
                        Appearance
                    </flux:navlist.item>

                    @if(auth()->user()->isAdmin())
                        <flux:navlist.item icon="bell" :href="route('settings.notifications')" :current="request()->routeIs('settings.notifications')" wire:navigate>
                            Notifications
                        </flux:navlist.item>
                        <flux:navlist.item icon="user-plus" :href="route('settings.create_user')" :current="request()->routeIs('settings.create_user')" wire:navigate>
                            Create User
                        </flux:navlist.item>
                        <flux:navlist.item icon="users" :href="route('settings.users')" :current="request()->routeIs('settings.users')" wire:navigate>
                            Users
                        </flux:navlist.item>
                        <flux:navlist.item icon="rectangle-group" :href="route('settings.teams')" :current="request()->routeIs('settings.teams')" wire:navigate>
                            Teams
                        </flux:navlist.item>
                    @endif
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <!-- Additional Navigation (if needed) -->
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile :name="auth()->user()->name" icon:trailing="chevrons-up-down" />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('account.index')" icon="user" wire:navigate>
                            Settings
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            Log Out
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('account.index')" icon="user" wire:navigate>
                            Settings
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            Log Out
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Main Page Content -->
        @isset($slot)
            {{ $slot }}
        @endisset

        @fluxScripts
    @stack('scripts')
    </body>
</html>
