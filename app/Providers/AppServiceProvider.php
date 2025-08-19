<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share unread notification count with settings layout
        View::composer('components.settings.layout', function ($view) {
            $unreadNotificationCount = Notification::where('is_read', false)->count();
            $view->with('unreadNotificationCount', $unreadNotificationCount);
        });
    }
}
