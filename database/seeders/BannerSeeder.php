<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Main Hero Banner - Typically wide (e.g., 1400x500)
        $main = Banner::create([
            'position'    => 'main',
            'badge_text'  => 'NEW ARRIVALS',
            'title'       => 'Ultimate <br>Performance',
            'subtitle'    => 'Custom loops, RTX 4090s, and high-airflow cases. Build your dream rig today.',
            'button_text' => 'Shop Custom Builds',
            'link'        => '/custom-builds',
            'is_active'   => true,
        ]);

        // Placeholder for Main Banner (1400x500, Purple/Blue, Text: MAIN BANNER)
        $main->addMediaFromUrl('https://placehold.co/1400x500/7F00FF/FFFFFF/png?text=MAIN+BANNER')
            ->toMediaCollection('banner_image');


        // 2. Side Top Banner - Typically narrow/square (e.g., 600x300)
        $sideTop = Banner::create([
            'position' => 'side_top',
            'title'    => 'Graphics Cards',
            'subtitle' => 'RTX 40-Series Stock',
            'link'     => '/components/gpu',
            'is_active'=> true,
        ]);

        // Placeholder for Side Top Banner (600x300, Red, Text: SIDE TOP)
        $sideTop->addMediaFromUrl('https://placehold.co/600x300/FF5733/FFFFFF/png?text=SIDE+TOP')
            ->toMediaCollection('banner_image');


        // 3. Side Bottom Banner - Typically narrow/square (e.g., 600x300)
        $sideBottom = Banner::create([
            'position' => 'side_bottom',
            'title'    => 'Gaming Gear',
            'subtitle' => 'Mechanical Keyboards',
            'link'     => '/peripherals/keyboards',
            'is_active'=> true,
        ]);

        // Placeholder for Side Bottom Banner (600x300, Green, Text: SIDE BOTTOM)
        $sideBottom->addMediaFromUrl('https://placehold.co/600x300/33FF57/000000/png?text=SIDE+BOTTOM')
            ->toMediaCollection('banner_image');
    }
}
