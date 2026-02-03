<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create(['name' => $request->name]);

        if ($request->hasFile('image')) {
            $brand->addMediaFromRequest('image')->toMediaCollection('brand_image');
        }

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update(['name' => $request->name]);

        if ($request->hasFile('image')) {
            // 'singleFile()' in model ensures old image is auto-deleted
            $brand->addMediaFromRequest('image')->toMediaCollection('brand_image');
        }

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        // Unset brand_id for all related products
        \App\Models\Product::where('brand_id', $brand->id)->update(['brand_id' => null]);

        $brand->delete(); // Spatie automatically cleans up files
        return redirect()->route('brands.index')->with('success', 'Brand deleted. Related products are now unbranded.');
    }
}