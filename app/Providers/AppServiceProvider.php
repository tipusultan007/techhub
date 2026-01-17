<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
//        if (Schema::hasTable('settings')) {
//            $settings = Setting::all()->pluck('value', 'key')->toArray();
//            View::share('settings', $settings);
//        }

        Paginator::useBootstrapFive();
        View::composer('layouts.frontend', function ($view) {
            // Get only Top Level categories (Parent is null)
            $categories = Category::whereNull('parent_id')
                ->orderBy('id', 'asc')
                ->get();

            $view->with('headerCategories', $categories);
        });
        View::composer('layouts.frontend', function ($view) {
            $wishlistCount = 0;
            if (Auth::guard('customer')->check()) {
                $wishlistCount = Wishlist::where('customer_id', '=', Auth::guard('customer')->id())->count();
            }
            $view->with('wishlistCount', $wishlistCount);
        });

        // Share Active Popup with Frontend Layout
        View::composer('layouts.frontend', function ($view) {
            $view->with('activePopup', \App\Models\OfferPopup::active()->first());
        });
    }
}
