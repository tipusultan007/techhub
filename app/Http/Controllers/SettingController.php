<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request)
    {
        // 1. Text Fields (excluding files)
        $inputs = $request->except(['_token', 'site_logo', 'site_favicon']);

        foreach ($inputs as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 2. Handle File Uploads via Spatie
        $this->handleFileUpload($request, 'site_logo');
        $this->handleFileUpload($request, 'site_favicon');

        // 3. Clear Cache
        Cache::forget('app_settings');

        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Handle the upload, media attachment, and value update.
     */
    private function handleFileUpload(Request $request, $key)
    {
        if ($request->hasFile($key)) {
            $setting = Setting::firstOrCreate(['key' => $key]);

            $media = $setting->addMediaFromRequest($key)->toMediaCollection($key);

            // Save the OPTIMIZED URL to the value column
            $setting->update(['value' => $media->getUrl('optimized')]);
        }
    }
}
