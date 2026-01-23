<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    public function index()
    {
        // Pagination + Eager Loading brands, categories, and variants (to calculate stock/price on index)
        $products = Product::with(['brand', 'category', 'variants'])
            ->latest()
            ->paginate(10); // Use paginate instead of get()

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();
        // Load attributes with their values (Color -> Red, Blue)
        $attributes = \App\Models\Attribute::with('values')->get();

        return view('admin.products.create', compact('brands', 'categories', 'attributes'));
    }

    public function store(Request $request)
    {
        // 1. Validation Logic
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:simple,variable',
            'brand_id' => 'required',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
        ];

        if ($request->type === 'simple') {
            $rules['sku'] = 'required|unique:products,sku';
            $rules['price'] = 'required|numeric';
            $rules['cost'] = 'required|numeric';
        } else {
            $rules['variants'] = 'required|array';
            $rules['variants.*.sku'] = 'required|distinct|unique:product_variants,sku';
        }
        
        $rules['image'] = 'nullable|image|max:2048';
        $rules['gallery'] = 'nullable|array';
        $rules['gallery.*'] = 'image|max:2048';

        $request->validate($rules);

        DB::transaction(function () use ($request) {
            // 2. Prepare Data
            $data = [
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'type' => $request->type,
                'description' => $request->description,
                'specifications' => $request->specifications,
            ];

            // 3. If Simple, Add Stock/Price Data to Main Table
            if ($request->type === 'simple') {
                $data['sku'] = $request->sku;
                $data['barcode'] = $request->barcode;
                $data['selling_price'] = $request->price;
                $data['cost_price'] = $request->cost;
                $data['stock_quantity'] = $request->stock ?? 0;
            }

            $product = Product::create($data);
            
            // Handle Main Image
            if ($request->hasFile('image')) {
                $product->addMediaFromRequest('image')->toMediaCollection('product_image');
            }
            
            // Handle Gallery Images
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $product->addMedia($file)->toMediaCollection('product_gallery');
                }
            }

            if ($request->type === 'variable') {
                foreach ($request->variants as $variantData) {
                    // 1. Create the Physical Variant (SKU)
                    $variant = \App\Models\ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_name' => $variantData['name'], // e.g. "Red/128GB"
                        'sku' => $variantData['sku'],
                        'cost_price' => $variantData['cost'],
                        'selling_price' => $variantData['price'],
                        'stock_quantity' => $variantData['stock'],
                        'barcode' => $variantData['barcode'] ?? null,
                    ]);

                    // 2. Link Attributes (The Professional Way)
                    // Expecting data like: variants[0][specs][Color] = 5 (Value ID for Red)
                    if (isset($variantData['specs'])) {
                        foreach ($variantData['specs'] as $attributeId => $valueId) {
                            DB::table('product_variant_attribute_values')->insert([
                                'product_variant_id' => $variant->id,
                                'attribute_id' => $attributeId,
                                'attribute_value_id' => $valueId
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Eager load relationships for performance
        $product->load(['brand', 'category', 'variants']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $brands = Brand::all();
        $categories = Category::all();
        $product->load('variants'); // Load variants if they exist
        $attributes = Attribute::with('values')->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories','attributes'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // 1. Basic Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:2048',
        ]);

        // 2. Specific Validation based on Type
        if ($product->type === 'simple') {
            $request->validate([
                'sku' => ['required', Rule::unique('products')->ignore($product->id)],
                'price' => 'required|numeric|min:0',
                'cost' => 'required|numeric|min:0',
                'stock' => 'integer|min:0',
            ]);
        } else {
            $request->validate([
                'variants' => 'required|array',
                'variants.*.sku' => 'required|distinct', // Unique check handled manually/softly for variants to allow updates
            ]);
        }


        DB::transaction(function () use ($request, $product) {
            // 3. Update General Info
            $data = [
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'specifications' => $request->specifications,
            ];

            // 4. Update Simple Product Logic
            if ($product->type === 'simple') {
                $data['sku'] = $request->sku;
                $data['barcode'] = $request->barcode;
                $data['selling_price'] = $request->price;
                $data['cost_price'] = $request->cost;
                $data['stock_quantity'] = $request->stock;
            }

            $product->update($data);

            // 5. Handle Image Update
            if ($request->hasFile('image')) {
                $product->clearMediaCollection('product_image');
                $product->addMediaFromRequest('image')->toMediaCollection('product_image');
            }
            
            // 6. Handle Gallery Update (Append new images)
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $product->addMedia($file)->toMediaCollection('product_gallery');
                }
            }

            // 6. Update Variable Product Logic
//            if ($product->type === 'variable') {
//                // A. Get IDs of variants present in the form
//                $submittedIds = array_filter(array_column($request->variants ?? [], 'id'));
//
//                // B. Delete variants that were removed in the form
//                $product->variants()->whereNotIn('id', $submittedIds)->delete();
//
//                // C. Update or Create variants
//                foreach ($request->variants as $v) {
//                    ProductVariant::updateOrCreate(
//                        ['id' => $v['id'] ?? null, 'product_id' => $product->id],
//                        [
//                            'variant_name' => $v['name'],
//                            'sku' => $v['sku'],
//                            'barcode' => $v['barcode'],
//                            'cost_price' => $v['cost'],
//                            'selling_price' => $v['price'],
//                            'stock_quantity' => $v['stock']
//                        ]
//                    );
//                }
//            }
            if ($product->type === 'variable') {

                // Get all attributes to look up names
                $allAttributes = Attribute::with('values')->get();

                // A. Get IDs of variants present in the form
                $submittedIds = array_filter(array_column($request->variants ?? [], 'id'));

                // B. Delete variants that were removed in the form
                $product->variants()->whereNotIn('id', $submittedIds)->delete();

                // C. Update or Create variants
                foreach ($request->variants as $key => $v) {

                    // --- LOGIC TO GENERATE NAME ---
                    // If 'specs' array exists (attribute values), generate name like "Red / XL"
                    $generatedName = 'Variant ' . ($key + 1);

                    if (isset($v['specs']) && is_array($v['specs'])) {
                        $names = [];
                        foreach ($v['specs'] as $attrId => $valId) {
                            // Find the value name from the loaded attributes
                            $attr = $allAttributes->find($attrId);
                            $val = $attr ? $attr->values->find($valId) : null;
                            if ($val) {
                                $names[] = $val->value;
                            }
                        }
                        if (!empty($names)) {
                            $generatedName = implode(' / ', $names);
                        }
                    } elseif (isset($v['name'])) {
                        // Fallback if provided explicitly (e.g. from JS generator)
                        $generatedName = $v['name'];
                    }
                    // -----------------------------

                    $variant = ProductVariant::updateOrCreate(
                        ['id' => $v['id'] ?? null, 'product_id' => $product->id],
                        [
                            'variant_name'   => $generatedName, // Use the generated name
                            'sku'            => $v['sku'],
                            'barcode'        => $v['barcode'],
                            'cost_price'     => $v['cost'],
                            'selling_price'  => $v['price'],
                            'stock_quantity' => $v['stock']
                        ]
                    );

                    // D. Sync Attributes (Important!)
                    // We need to update the pivot table `product_variant_attribute_values`
                    if (isset($v['specs'])) {
                        $variant->attributeValues()->sync(array_values($v['specs']));
                    }
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Spatie Media Library auto-deletes images.
        // Database "ON DELETE CASCADE" should handle variants,
        // but we can manually delete for safety if FKs aren't set up perfectly.

        if ($product->type === 'variable') {
            $product->variants()->delete();
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function printBarcode(Request $request)
{
    $request->validate(['ids' => 'required|array']);

    $products = Product::with('variants')->whereIn('id', $request->ids)->get();
    $printQueue = collect();

    foreach ($products as $product) {
        if ($product->type === 'simple') {
            $printQueue->push((object)[
                'name' => $product->name,
                'price' => $product->selling_price,
                'barcode_value' => $product->barcode ?? $product->sku,
            ]);
        } elseif ($product->type === 'variable') {
            foreach ($product->variants as $variant) {
                $printQueue->push((object)[
                    'name' => $product->name . ' (' . $variant->variant_name . ')',
                    'price' => $variant->selling_price,
                    'barcode_value' => $variant->barcode ?? $variant->sku,
                ]);
            }
        }
    }

    // This object is what creates the HTML
    $generator = new BarcodeGeneratorHTML();

    return view('admin.products.barcode', compact('printQueue', 'generator'));
}
}
