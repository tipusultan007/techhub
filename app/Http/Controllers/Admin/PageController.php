<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::where('id', '!=', 0)->orderBy('created_at', 'desc')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $products = \App\Models\Product::published()->orderBy('name')->get();
        return view('admin.pages.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'redirect_url' => 'nullable|url|max:255',
            'type' => 'required|string|in:static,category,product',
            'reference_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'show_on_footer' => 'boolean',
        ]);

        Page::create($request->all());

        return redirect()->route('pages.admin.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $products = \App\Models\Product::published()->orderBy('name')->get();
        return view('admin.pages.edit', compact('page', 'categories', 'products'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'redirect_url' => 'nullable|url|max:255',
            'type' => 'required|string|in:static,category,product',
            'reference_id' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'show_on_footer' => 'boolean',
        ]);

        $page->update($request->all());

        return redirect()->route('pages.admin.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('pages.admin.index')->with('success', 'Page deleted successfully.');
    }
}
