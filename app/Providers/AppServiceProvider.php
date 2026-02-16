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
        // Custom Microsoft Graph Mail Transport
        \Illuminate\Support\Facades\Mail::extend('microsoft_graph', function (array $config = []) {
            return new \App\Mail\Transport\MicrosoftGraphTransport(
                settings('microsoft_tenant_id'),
                settings('microsoft_client_id'),
                settings('microsoft_client_secret'),
                settings('mail_from_address')
            );
        });

        if (Schema::hasTable('settings')) {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
            View::share('settings', $settings);
        }

        Paginator::useTailwind();
        View::composer('layouts.frontend', function ($view) {
            // Get only Top Level categories (Parent is null)
            $categories = Category::whereNull('parent_id')
                ->with('children')
                ->orderBy('priority', 'asc')
                ->orderBy('name', 'asc')
                ->get();

            // Footer Data
            $footerCategories = Category::whereNull('parent_id')
                ->orderBy('priority', 'asc')
                ->orderBy('name', 'asc')
                ->take(5)
                ->get();
            
            $footerPages = \App\Models\Page::where('is_active', true)
                ->get();

            $headerMenu = \App\Models\Menu::where('location', 'header')
                ->orWhere('slug', 'main-header')
                ->with(['menuItems' => function($q) {
                    $q->whereNull('parent_id')->orderBy('order');
                }, 'menuItems.children' => function($q) {
                    $q->orderBy('order');
                }])
                ->first();

            $view->with('headerCategories', $categories)
                 ->with('footerCategories', $footerCategories)
                 ->with('footerPages', $footerPages)
                 ->with('headerMenu', $headerMenu);
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

        // Fix Forgot Password Link Bug
        \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(function ($user, string $token) {
            if ($user instanceof \App\Models\Customer) {
                return route('customer.password.reset', ['token' => $token, 'email' => $user->email]);
            }
            return url(config('app.url') . route('password.reset', ['token' => $token, 'email' => $user->email], false));
        });
    }
}
