<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Alert;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No services to register
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Unread alert count for the navbar bell and sidebar badge.
         * Scoped to the navbar (a '*' composer ran this query for every component on the page)
         * and memoised so it runs at most once per request.
         */
        View::composer(['partials.navbar', 'partials.sidebar'], function ($view) {
            // Memo lives on the request object, so it can never leak between users or requests.
            $attrs = request()->attributes;

            if (!$attrs->has('unread_alerts')) {
                $attrs->set('unread_alerts', Auth::check()
                    ? Alert::where('user_id', Auth::id())->where('is_read', false)->count()
                    : 0);
            }

            $view->with('unreadAlerts', $attrs->get('unread_alerts'));
        });
    }
}
