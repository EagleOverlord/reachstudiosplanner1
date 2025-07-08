<?php

namespace App\Livewire\Settings;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Notifications extends Component
{
    use WithPagination;

    public bool $new_user_notifications = true;
    public bool $missing_hours_notifications = true;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Check if the current user is an admin
        if (Auth::user()->admin_status !== 'yes') {
            abort(403, 'Access denied. Admin privileges required.');
        }

        $user = Auth::user();
        
        // You can load existing notification preferences from the database here
        // For now, we'll use default values
        $this->new_user_notifications = true;
        $this->missing_hours_notifications = true;
    }

    /**
     * Update notification preferences.
     */
    public function updateNotificationPreferences(): void
    {
        $user = Auth::user();

        // Here you would save the notification preferences to the database
        // For now, we'll just dispatch a success event
        
        $this->dispatch('notifications-updated');
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($notificationId): void
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        Notification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification($notificationId): void
    {
        Notification::findOrFail($notificationId)->delete();
    }

    public function render()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->paginate(10);
        
        return view('livewire.settings.notifications', [
            'notifications' => $notifications
        ]);
    }
}
