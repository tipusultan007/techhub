<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::all();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request)
    {
        // 1. Create Category with new fields
        $category = Category::create([
            'name'             => $request->name,
            'slug'             => $request->slug,
            'parent_id'        => $request->parent_id,
            'icon_class'       => $request->icon_class,
            // Convert checkbox "on"/null to boolean true/false
            'is_featured'      => $request->boolean('is_featured'),
            'priority'         => $request->priority ?? 0,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
        ]);

        // 2. Handle Image Upload (Spatie)
        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')->toMediaCollection('category_image');
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        // Exclude self from parents list to prevent setting self as parent
        $parents = Category::where('id', '!=', $category->id)->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        // 1. Update fields
        $category->update([
            'name'             => $request->name,
            'slug'             => $request->slug,
            'parent_id'        => $request->parent_id,
            'icon_class'       => $request->icon_class,
            'is_featured'      => $request->boolean('is_featured'),
            'priority'         => $request->priority ?? 0,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
        ]);

        // 2. Handle Image Upload
        if ($request->hasFile('image')) {
            // Spatie 'singleFile()' collection will auto-delete the old one
            $category->addMediaFromRequest('image')->toMediaCollection('category_image');
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category because it has sub-categories attached.');
        }

        // Unset category_id for all related products (Requirement)
        \App\Models\Product::where('category_id', $category->id)->update(['category_id' => null]);

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted. Related products are now uncategorized.');
    }
}
