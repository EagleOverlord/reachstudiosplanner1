<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('settings.profile')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.password')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
            @if(auth()->user()->admin_status === 'yes')
                <flux:navlist.item :href="route('settings.notifications')" wire:navigate>
                    <div class="flex items-center justify-between w-full">
                        <span>{{ __('Notifications') }}</span>
                        @if($unreadNotificationCount > 0)
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <div class="flex items-center justify-center w-6 h-6 text-white bg-red-500 rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 2C10.5523 2 11 2.44772 11 3V3.09199C13.5281 3.63568 15.4343 5.54188 15.978 8.07001H16C16.5523 8.07001 17 8.51772 17 9.07001C17 9.6223 16.5523 10.07 16 10.07H15.978C15.4343 12.5981 13.5281 14.5043 11 15.048V15C11 15.5523 10.5523 16 10 16C9.44772 16 9 15.5523 9 15V15.048C6.47188 14.5043 4.56568 12.5981 4.02199 10.07H4C3.44772 10.07 3 9.6223 3 9.07001C3 8.51772 3.44772 8.07001 4 8.07001H4.02199C4.56568 5.54188 6.47188 3.63568 9 3.09199V3C9 2.44772 9.44772 2 10 2Z"/>
                                        </svg>
                                    </div>
                                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-600 rounded-full border border-white">
                                        {{ $unreadNotificationCount }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </flux:navlist.item>
                <flux:navlist.item :href="route('settings.users')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                <flux:navlist.item :href="route('settings.teams')" wire:navigate>{{ __('Teams') }}</flux:navlist.item>
                <flux:navlist.item :href="route('settings.create_user')" wire:navigate>{{ __('Create new user') }}</flux:navlist.item>
            @endif
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-7xl">
            {{ $slot }}
        </div>
    </div>
</div>