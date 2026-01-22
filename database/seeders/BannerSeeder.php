<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing banners
        Banner::truncate();

        // 1. MAIN BANNER
        $main = Banner::create([
            'title'       => 'Ultimate Gaming <br> Experience',
            'subtitle'    => 'Build your dream setup with our premium gaming components.',
            'badge_text'  => 'New Arrival',
            'button_text' => 'Shop Now',
            'link'        => '/category/gaming-pcs',
            'position'    => 'main',
            'is_active'   => true,
        ]);
        $main->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/afe2ebaa-b4cd-4b5d-b462-f0b614a15799/main_banner_tech_1769014234094.png')
            ->preservingOriginal()
            ->toMediaCollection('banner_image');

        // 2. SIDE TOP
        $sideTop = Banner::create([
            'title'       => 'Latest Smartphones',
            'subtitle'    => 'Up to 20% Off',
            'badge_text'  => null,
            'button_text' => null,
            'link'        => '/category/smartphones',
            'position'    => 'side_top',
            'is_active'   => true,
        ]);
        $sideTop->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/afe2ebaa-b4cd-4b5d-b462-f0b614a15799/side_top_tech_1769014261485.png')
            ->preservingOriginal()
            ->toMediaCollection('banner_image');

        // 3. SIDE BOTTOM
        $sideBottom = Banner::create([
            'title'       => 'Modern Workspace',
            'subtitle'    => 'Productivity focused',
            'badge_text'  => null,
            'button_text' => null,
            'link'        => '/category/laptops',
            'position'    => 'side_bottom',
            'is_active'   => true,
        ]);
        $sideBottom->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/afe2ebaa-b4cd-4b5d-b462-f0b614a15799/side_bottom_tech_1769014290216.png')
            ->preservingOriginal()
            ->toMediaCollection('banner_image');
    }
}
