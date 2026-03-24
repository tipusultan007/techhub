<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // 1. Check for external redirect first (Legacy support)
        if ($page->redirect_url && $page->type === 'static') {
            return redirect()->away($page->redirect_url);
        }

        // 2. Handle Mirror Logic based on Type
        if ($page->type === 'category' && $page->reference_id) {
            return $this->renderMirrorCategory($page);
        }

        if ($page->type === 'product' && $page->reference_id) {
            return $this->renderMirrorProduct($page);
        }

        // 3. Fallback to Standard Static Page
        return view('frontend.pages.show', compact('page'));
    }

    protected function renderMirrorCategory($page)
    {
        $category = \App\Models\Category::findOrFail($page->reference_id);
        return app(\App\Http\Controllers\ShopController::class)->index(request(), $category->slug, $page);
    }

    protected function renderMirrorProduct($page)
    {
        $product = \App\Models\Product::published()
            ->where('id', $page->reference_id)
            ->firstOrFail();

        return app(\App\Http\Controllers\HomeController::class)->product($product->slug, $page);
    }
}
