<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Notifications')" :subheading="__('View system notifications and manage your preferences')">
        
        <!-- Recent Notifications Section (at the top) -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Recent Notifications</flux:heading>
                @if($notifications->count() > 0)
                    <flux:button wire:click="markAllAsRead" variant="outline" size="sm">
                        Mark All as Read
                    </flux:button>
                @endif
            </div>

            @if($notifications->count() > 0)
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        <div class="flex items-start justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg {{ $notification->is_read ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }}">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if($notification->type === 'missing_hours')
                                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-200">
                                            ⏰
                                        </span>
                                    @elseif($notification->type === 'user_created')
                                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-200">
                                            👤
                                        </span>
                                    @endif
                                    <flux:heading size="sm">{{ $notification->title }}</flux:heading>
                                    @if(!$notification->is_read)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-200">
                                            New
                                        </span>
                                    @endif
                                </div>
                                <flux:text size="sm" class="text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $notification->message }}
                                </flux:text>
                                <flux:text size="xs" class="text-gray-500 dark:text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                @if(!$notification->is_read)
                                    <flux:button wire:click="markAsRead({{ $notification->id }})" variant="ghost" size="sm">
                                        Mark as Read
                                    </flux:button>
                                @endif
                                <flux:button wire:click="deleteNotification({{ $notification->id }})" variant="ghost" size="sm" class="text-red-600 hover:text-red-800">
                                    Delete
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <flux:text class="text-gray-500 dark:text-gray-400">
                        No notifications yet.
                    </flux:text>
                </div>
            @endif
        </div>

        <flux:separator variant="subtle" />

        <!-- Notification Preferences Section (moved to bottom) -->
        <div class="mt-8">
            <flux:heading size="lg" class="mb-4">Notification Preferences</flux:heading>
            <form wire:submit="updateNotificationPreferences" class="space-y-6">
                
                <!-- New Users Notifications Section -->
                <div class="space-y-4">
                    <div class="space-y-3">
                        <flux:checkbox wire:model="new_user_notifications" label="New User Notifications" />
                        <flux:text size="sm" class="text-gray-600 dark:text-gray-400 ml-6">
                            Get notified when new users are created in the system
                        </flux:text>
                    </div>

                    <div class="space-y-3">
                        <flux:checkbox wire:model="missing_hours_notifications" label="Missing Hours Notifications" />
                        <flux:text size="sm" class="text-gray-600 dark:text-gray-400 ml-6">
                            Get notified when users haven't logged their hours for the day
                        </flux:text>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex items-center gap-4 pt-4">
                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit" class="w-full">Save Preferences</flux:button>
                    </div>

                    <x-action-message class="me-3" on="notifications-updated">
                        Saved.
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-settings.layout>
</section>
