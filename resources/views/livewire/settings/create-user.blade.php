<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Create User')" :subheading="__('Enter user details to create a new account')">
        <form wire:submit.prevent="createUser" class="mt-6 space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="John Doe"
            />
            <flux:input
                wire:model="email"
                :label="__('Email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            <flux:select
                wire:model="team"
                :label="__('Team')"
            >
                <flux:select.option value="mobile">Mobile</flux:select.option>
                <flux:select.option value="front_end">Front End Development</flux:select.option>
                <flux:select.option value="back_end">Back End Development</flux:select.option>
                <flux:select.option value="design">Design</flux:select.option>
                <flux:select.option value="slt">Senior Leadership Team</flux:select.option>
                <flux:select.option value="e_commerce">E Commerce</flux:select.option>
                <flux:select.option value="bdm">Business Development Management</flux:select.option>
            </flux:select>

            <flux:select
                wire:model="admin_status"
                :label="__('Admin Status')"
            >
                <flux:select.option value="yes">Yes</flux:select.option>
                <flux:select.option value="no">No</flux:select.option>
            </flux:select>

            <flux:select
                wire:model="keys_status"
                :label="__('Keys Status')"
            >
                <flux:select.option value="yes">Yes</flux:select.option>
                <flux:select.option value="no">No</flux:select.option>
            </flux:select>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Create') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="user-created">
                    {{ __('User created.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
