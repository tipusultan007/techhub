<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request)
    {
        // 1. Text Fields (excluding files)
        $inputs = $request->except(['_token', 'site_logo', 'site_logo_scrolled', 'site_logo_footer', 'site_favicon']);

        foreach ($inputs as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 2. Handle File Uploads via Spatie
        $this->handleFileUpload($request, 'site_logo');
        $this->handleFileUpload($request, 'site_logo_scrolled');
        $this->handleFileUpload($request, 'site_logo_footer');
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

    /**
     * Clear all application caches.
     */
    public function clearCache()
    {
        try {
            Artisan::call('optimize:clear');
            return back()->with('success', 'Application cache cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error clearing cache: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate the storage symbolic link.
     */
    public function linkStorage()
    {
        try {
            // In cPanel, if the link already exists but is broken, storage:link might fail.
            // We can try to remove the existing link first if it's a symlink.
            $publicStoragePath = public_path('storage');
            if (is_link($publicStoragePath)) {
                @unlink($publicStoragePath);
            }

            Artisan::call('storage:link');
            return back()->with('success', 'Storage link recreated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating storage link: ' . $e->getMessage());
        }
    }

    /**
     * Test SMTP Settings.
     */
    public function testSMTP(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required',
            'mail_from_address' => 'required|email',
            'test_recipient' => 'required|email',
        ]);

        try {
            if ($request->mail_mailer === 'microsoft_graph') {
                $request->validate([
                    'microsoft_tenant_id' => 'required',
                    'microsoft_client_id' => 'required',
                    'microsoft_client_secret' => 'required',
                ]);

                $mailer = \Illuminate\Support\Facades\Mail::build([
                    'transport' => 'microsoft_graph',
                    'tenant_id' => $request->microsoft_tenant_id,
                    'client_id' => $request->microsoft_client_id,
                    'client_secret' => $request->microsoft_client_secret,
                    'from_address' => $request->mail_from_address,
                ]);

                // Register the temporary transport instance if not already accessible
                // Since we registered it in AppServiceProvider, Mail::build might not automatically use our custom transport closure
                // We'll use our custom class directly here for the test
                $transport = new \App\Mail\Transport\MicrosoftGraphTransport(
                    $request->microsoft_tenant_id,
                    $request->microsoft_client_id,
                    $request->microsoft_client_secret,
                    $request->mail_from_address
                );
                
                $mailer = new \Illuminate\Mail\Mailer(
                    'microsoft_graph_test',
                    app(\Illuminate\Contracts\View\Factory::class),
                    $transport,
                    app('events')
                );

            } else {
                $request->validate([
                    'mail_host' => 'required',
                    'mail_port' => 'required',
                    'mail_username' => 'required',
                    'mail_password' => 'required',
                ]);

                // Build configuration array for on-the-fly mailer
                $config = [
                    'transport' => 'smtp',
                    'host' => $request->mail_host,
                    'port' => $request->mail_port,
                    'encryption' => $request->mail_encryption,
                    'username' => $request->mail_username,
                    'password' => $request->mail_password,
                    'timeout' => 30, // Increased timeout for live servers
                    'auth_mode' => null,
                ];

                // Set temporary mailer configuration
                config(['mail.mailers.smtp_test' => $config]);
                $mailer = \Illuminate\Support\Facades\Mail::mailer('smtp_test');
            }

            // Common From Configuration
            config(['mail.from.address' => $request->mail_from_address]);
            config(['mail.from.name' => $request->mail_from_name ?? config('app.name')]);

            $mailer->raw(
                "Hello,\n\nThis is a test email sent from Tech Hub Admin Panel to verify that your " . strtoupper($request->mail_mailer) . " Configuration is working correctly.\n\nSuccess!", 
                function ($message) use ($request) {
                    $message->from($request->mail_from_address, $request->mail_from_name ?? config('app.name'))
                        ->to($request->test_recipient)
                        ->subject('Mail Connection Test Results');
                }
            );

            return response()->json([
                'success' => true, 
                'message' => 'Connection verified! A test email has been sent to ' . $request->test_recipient
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Mail Connection Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manually regenerate the sitemap.
     */
    public function generateSitemap()
    {
        try {
            Artisan::call('sitemap:generate');
            return back()->with('success', 'Sitemap regenerated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating sitemap: ' . $e->getMessage());
        }
    }
}
