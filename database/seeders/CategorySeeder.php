<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing categories
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'name' => 'Computers & Laptops',
                'icon_class' => 'ri-macbook-line',
                'is_featured' => true
            ],
            [
                'name' => 'Gaming',
                'icon_class' => 'ri-gamepad-line',
                'is_featured' => true
            ],
            [
                'name' => 'Storage Devices',
                'icon_class' => 'ri-hard-drive-2-line',
                'is_featured' => true
            ],
            [
                'name' => 'PC Components',
                'icon_class' => 'ri-cpu-line',
                'is_featured' => true
            ],
            [
                'name' => 'Network',
                'icon_class' => 'ri-router-line',
                'is_featured' => true
            ],
            [
                'name' => 'Printers & Catridges',
                'icon_class' => 'ri-printer-line',
                'is_featured' => true
            ],
            [
                'name' => 'Smartphones',
                'icon_class' => 'ri-smartphone-line',
                'is_featured' => false
            ],
            [
                'name' => 'Accessories',
                'icon_class' => 'ri-plug-2-line',
                'is_featured' => false
            ],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon_class' => $cat['icon_class'],
                'is_featured' => $cat['is_featured'],
            ]);
        }
    }
}
