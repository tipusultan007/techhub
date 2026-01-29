<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Prevent issues during migration when table doesn't exist
        if (!Schema::hasTable('settings')) {
            return;
        }

        // Check if dynamic mail settings exist and are enabled (implied by existence of mail_mailer)
        $mailDriver = settings('mail_mailer');

        if ($mailDriver) {
            $config = [
                'transport' => $mailDriver,
                'host'       => settings('mail_host'),
                'port'       => settings('mail_port'),
                'encryption' => settings('mail_encryption'),
                'username'   => settings('mail_username'),
                'password'   => settings('mail_password'),
                'timeout'    => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ];

            // Override SMTP configuration
            if ($mailDriver === 'smtp') {
                Config::set('mail.mailers.smtp', $config);
            }

            // Define microsoft_graph mailer if it's the selected driver
            if ($mailDriver === 'microsoft_graph') {
                Config::set('mail.mailers.microsoft_graph', [
                    'transport' => 'microsoft_graph',
                ]);
            }

            // Set default mailer
            Config::set('mail.default', $mailDriver);

            // Set Global From Address
            $fromAddress = settings('mail_from_address');
            $fromName    = settings('mail_from_name');

            if ($fromAddress) {
                Config::set('mail.from.address', $fromAddress);
                Config::set('mail.from.name', $fromName ?? config('app.name'));
            }
        }
    }
}
