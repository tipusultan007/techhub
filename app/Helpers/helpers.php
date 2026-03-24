<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

if (!function_exists('settings')) {
    function settings($key = null, $default = null) {
        // Cache settings for 24 hours (or forever until updated)
        $settings = Cache::remember('app_settings', 60 * 24, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        if (is_null($key)) {
            return $settings;
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('app_version')) {
    function app_version() {
        return \App\Services\VersionService::currentVersion();
    }
}

if (!function_exists('app_changelog')) {
    function app_changelog() {
        return \App\Services\VersionService::getChangelog();
    }
}
