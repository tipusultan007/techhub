<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class GoogleFeedController extends Controller
{
    /**
     * Generate an XML feed for Google Merchant Center (RSS 2.0).
     */
    public function index()
    {
        $products = Product::published()
            ->physical()
            ->inStock()
            ->with(['brand', 'media'])
            ->get();

        $siteName = settings('site_name', 'Techhub');
        $siteUrl = url('/');
        $siteDescription = settings('meta_description', 'Your one-stop shop for electronics.');
        $currency = settings('currency_symbol', 'AED');

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', $siteName);
        $channel->addChild('link', $siteUrl);
        $channel->addChild('description', $siteDescription);

        foreach ($products as $product) {
            $item = $channel->addChild('item');
            
            // Standard RSS Tags
            $item->addChild('title', htmlspecialchars($product->name));
            $item->addChild('link', route('product.show', $product->slug));
            
            $description = strip_tags($product->description);
            $description = mb_substr($description, 0, 5000);
            $item->addChild('description', htmlspecialchars($description ?: $product->name));

            // Google Custom Tags (g: namespace)
            $item->addChild('g:id', $product->id, 'http://base.google.com/ns/1.0');
            $item->addChild('g:title', htmlspecialchars($product->name), 'http://base.google.com/ns/1.0');
            $item->addChild('g:description', htmlspecialchars($description ?: $product->name), 'http://base.google.com/ns/1.0');
            $item->addChild('g:link', route('product.show', $product->slug), 'http://base.google.com/ns/1.0');
            
            $imageUrl = $product->getFirstMediaUrl('product_image') ?: asset('images/placeholder.jpg');
            $item->addChild('g:image_link', $imageUrl, 'http://base.google.com/ns/1.0');
            
            $item->addChild('g:condition', 'new', 'http://base.google.com/ns/1.0');
            $item->addChild('g:availability', 'in stock', 'http://base.google.com/ns/1.0');
            
            $price = number_format($product->active_price, 2, '.', '') . ' ' . $currency;
            $item->addChild('g:price', $price, 'http://base.google.com/ns/1.0');
            
            $brand = $product->brand->name ?? $siteName;
            $item->addChild('g:brand', htmlspecialchars($brand), 'http://base.google.com/ns/1.0');
        }

        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
