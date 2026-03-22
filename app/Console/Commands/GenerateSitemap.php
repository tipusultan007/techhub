<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;
use App\Models\Solution;
use App\Models\Page;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--url= : Override the base URL for the sitemap}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml for the website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sitemap generation...');

        $baseUrl = $this->option('url') ?: config('app.url');
        if($this->option('url')) {
            \Illuminate\Support\Facades\URL::forceRootUrl($baseUrl);
            $this->info("Forcing base URL to: " . $baseUrl);
        }

        $sitemap = Sitemap::create();

        // 1. Static Pages
        $sitemap->add(Url::create(route('home'))->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create(route('shop.index'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_HOURLY));
        $sitemap->add(Url::create(route('solutions.index'))->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        $sitemap->add(Url::create(route('store.locator'))->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        $sitemap->add(Url::create(route('track.order'))->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        // 2. Published Products
        $this->info('Adding Products...');
        Product::published()->chunk(100, function ($products) use ($sitemap) {
            foreach ($products as $product) {
                $sitemap->add(
                    Url::create(route('product.show', $product->slug))
                        ->setLastModificationDate($product->updated_at)
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                );
            }
        });

        // 3. Categories
        $this->info('Adding Categories...');
        Category::all()->each(function (Category $category) use ($sitemap) {
            $sitemap->add(
                Url::create(route('category.show', $category->slug))
                    ->setLastModificationDate($category->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        });

        // 4. Solutions
        $this->info('Adding Solutions...');
        Solution::where('is_active', true)->get()->each(function (Solution $solution) use ($sitemap) {
            $sitemap->add(
                Url::create(route('solutions.show', $solution->slug))
                    ->setLastModificationDate($solution->updated_at)
                    ->setPriority(0.6)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        });

        // 5. Dynamic CMS Pages
        $this->info('Adding Pages...');
        Page::where('is_active', true)->get()->each(function (Page $page) use ($sitemap) {
            $sitemap->add(
                Url::create(route('pages.show', $page->slug))
                    ->setLastModificationDate($page->updated_at)
                    ->setPriority(0.5)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at ' . public_path('sitemap.xml'));
    }
}
