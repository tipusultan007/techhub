<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // --- 1. General & Branding ---
            'site_name'       => 'Tech Hub',
            'currency_symbol' => 'AED',
            // Note: Logos are usually file paths. We'll leave them empty or use placeholders for testing.
            'site_logo'       => null,
            'site_favicon'    => null,

            // --- 2. Store & Receipts (Backend) ---
            'shop_name'    => 'Tech Hub Computer Trading LLC',
            'shop_trn'     => '100-200-300-400', // Tax Registration Number
            'shop_phone'   => '+971 4 359 0000',
            'shop_address' => "Shop #20, Ground Floor,\nAl Ain Center (Computer Plaza),\nBur Dubai, UAE",

            // --- 3. SEO & Meta ---
            'meta_title'       => 'Tech Hub | No.1 Gaming PC & Laptop Store in Dubai',
            'meta_description' => 'Buy the latest Gaming PCs, Laptops, Components, and Graphics Cards at the best prices in UAE. Official retailers for ASUS, MSI, and Gigabyte.',
            'meta_keywords'    => 'gaming pc, rtx 4090, laptop dubai, computer parts, tech hub, bur dubai',

            // --- 4. Contact Details ---
            'contact_phone'   => '+971 50 123 4567',
            'contact_email'   => 'sales@techhub.ae',
            'contact_address' => 'Al Ain Center, Bur Dubai, UAE',
            'contact_map'     => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3609.284793466986!2d55.29541837538269!3d25.22312677769917!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f432851167735%3A0xc3f17347960100d0!2sAl%20Ain%20Center%20-%20Computer%20Plaza!5e0!3m2!1sen!2sae!4v1700000000000!5m2!1sen!2sae" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',

            // --- 5. Social Media ---
            'social_facebook'  => 'https://facebook.com/techhub',
            'social_instagram' => 'https://instagram.com/techhub',
            'social_twitter'   => 'https://x.com/techhub',
            'social_linkedin'  => 'https://linkedin.com/company/techhub',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // IMPORTANT: Clear the cache so changes appear immediately
        Cache::forget('app_settings');

        $this->command->info('System settings seeded successfully!');
    }
}
