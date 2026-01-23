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

        // 1. MAIN BANNERS (Slider)
        $main1 = Banner::create([
            'title'       => 'Ultimate Gaming <br> Experience',
            'subtitle'    => 'Build your dream setup with our premium gaming components.',
            'badge_text'  => 'New Arrival',
            'button_text' => 'Shop Now',
            'link'        => '/category/gaming-pcs',
            'position'    => 'main',
            'order'       => 1,
            'is_active'   => true,
        ]);
        $main1->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/2092037f-9f4f-454e-a7cc-58511329e0d5/slider_gaming_pc_1769196045334.png')
            ->preservingOriginal()
            ->toMediaCollection('banner_image');

        $main2 = Banner::create([
            'title'       => 'Modern Office <br> Solutions',
            'subtitle'    => 'Premium laptops and accessories for maximum productivity.',
            'badge_text'  => 'Best Seller',
            'button_text' => 'View Laptops',
            'link'        => '/category/laptops',
            'position'    => 'main',
            'order'       => 2,
            'is_active'   => true,
        ]);
        $main2->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/2092037f-9f4f-454e-a7cc-58511329e0d5/slider_laptop_lifestyle_1769196060936.png')
            ->preservingOriginal()
            ->toMediaCollection('banner_image');

        $main3 = Banner::create([
            'title'       => 'Enterprise IT <br> Infrastructure',
            'subtitle'    => 'Scalable server and networking solutions for your business.',
            'badge_text'  => 'Corporate',
            'button_text' => 'Get a Quote',
            'link'        => '/solutions',
            'position'    => 'main',
            'order'       => 3,
            'is_active'   => true,
        ]);
        $main3->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/2092037f-9f4f-454e-a7cc-58511329e0d5/slider_it_server_1769196076226.png')
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
        // Re-using an existing image or just pointing to it if possible. 
        // For now I'll just skip adding media to side banners if I don't have them, or use one of the generated ones if I must.
        // Actually I'll use the gaming one for side top just to have an image there.
        $sideTop->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/2092037f-9f4f-454e-a7cc-58511329e0d5/slider_gaming_pc_1769196045334.png')
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
        $sideBottom->addMedia('C:/Users/Tipusultan/.gemini/antigravity/brain/2092037f-9f4f-454e-a7cc-58511329e0d5/slider_laptop_lifestyle_1769196060936.png')
            ->preservingOriginal()
            ->toMediaCollection('banner_image');
    }
}
