<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us...</p>',
                'meta_title' => 'Privacy Policy | Electromart',
                'meta_description' => 'Read our privacy policy to understand how we handle your data.',
                'is_active' => true,
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'content' => '<h1>Terms and Conditions</h1><p>By using our website, you agree to the following terms...</p>',
                'meta_title' => 'Terms and Conditions | Electromart',
                'meta_description' => 'Our terms and conditions for using Electromart services.',
                'is_active' => true,
            ],
            [
                'title' => 'Return Policy',
                'slug' => 'return-policy',
                'content' => '<h1>Return Policy</h1><p>We accept returns under the following conditions...</p>',
                'meta_title' => 'Return Policy | Electromart',
                'meta_description' => 'Understand our return process and eligibility.',
                'is_active' => true,
            ],
            [
                'title' => 'Refund Policy',
                'slug' => 'refund-policy',
                'content' => '<h1>Refund Policy</h1><p>Our refund process is explained below...</p>',
                'meta_title' => 'Refund Policy | Electromart',
                'meta_description' => 'Information about how we process refunds.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(['slug' => $pageData['slug']], $pageData);
        }
    }
}
