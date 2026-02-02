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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

use App\Traits\LogsActivity;

class ProductController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'variants']);

        // Filter by Search (Name or SKU)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('sku', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->latest()->paginate($perPage)->withQueryString();
        
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
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
            'type' => 'required|in:simple,variable,service',
            'brand_id' => 'required_if:type,simple,variable',
            'category_id' => 'nullable',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'tax_method' => 'required|in:inclusive,exclusive',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:draft,published',
        ];

        if ($request->type === 'simple' || $request->type === 'service') {
            $rules['sku'] = 'required|unique:products,sku';
            $rules['price'] = 'required|numeric';
            $rules['sale_price'] = 'nullable|numeric|lt:price';
            $rules['cost'] = ($request->type === 'service') ? 'nullable|numeric' : 'required|numeric';
        } else {
            $rules['variants'] = 'required|array';
            $rules['variants.*.sku'] = 'required|distinct|unique:product_variants,sku';
            $rules['variants.*.sale_price'] = 'nullable|numeric|lt:variants.*.price';
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
                'status' => $request->status,
                'description' => $request->description,
                'specifications' => $request->specifications,
                'tax_method' => $request->tax_method,
                'tax_rate' => $request->tax_rate ?? 0,
            ];

            // 3. If Simple/Service, Add Stock/Price Data to Main Table
            if ($request->type === 'simple' || $request->type === 'service') {
                $data['sku'] = $request->sku;
                $data['barcode'] = $request->barcode;
                $data['selling_price'] = $request->price;
                $data['sale_price'] = $request->sale_price;
                $data['cost_price'] = $request->cost;
                $data['stock_quantity'] = $request->stock ?? 0;
                $data['alert_quantity'] = $request->alert_quantity;
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
                        'sale_price' => $variantData['sale_price'] ?? null,
                        'stock_quantity' => $variantData['stock'],
                        'alert_quantity' => $variantData['alert_quantity'] ?? 5,
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

        $product = Product::latest()->first(); // To get the created product for logging
        $this->logActivity('Product', 'Create', "Created Product: {$product->name}", [
            'product_id' => $product->id,
            'sku' => $product->sku,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Eager load basic details
        $product->load(['brand', 'category', 'variants.attributeValues.attribute']);

        // Fetch Sales History (Last 20)
        $salesHistory = \App\Models\OrderItem::where('product_id', $product->id)
            ->with(['order.user', 'variant'])
            ->latest()
            ->take(20)
            ->get();

        // Fetch Stock-In History (Last 20)
        // Linking through PurchaseOrderItem to get PurchaseReceptionItem
        $stockHistory = \App\Models\PurchaseReceptionItem::whereHas('poItem', function($q) use ($product) {
            $q->where('product_id', $product->id);
        })->with(['reception.purchaseOrder.supplier', 'poItem.variant'])->latest()->take(20)->get();

        return view('admin.products.show', compact('product', 'salesHistory', 'stockHistory'));
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
            'brand_id' => 'required_if:type,simple,variable',
            'category_id' => 'nullable',
            'description' => 'nullable|string',
            'specifications' => 'nullable|string',
            'tax_method' => 'required|in:inclusive,exclusive',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:2048',
        ]);

        // 2. Specific Validation based on Type
        if ($product->type === 'simple' || $product->type === 'service') {
            $request->validate([
                'sku' => ['required', Rule::unique('products')->ignore($product->id)],
                'price' => 'required|numeric|min:0',
                'sale_price' => 'nullable|numeric|lt:price',
                'cost' => ($product->type === 'service') ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
                'stock' => 'integer|min:0',
                'alert_quantity' => 'nullable|integer|min:0',
            ]);
        } else {
            $request->validate([
                'variants' => 'required|array',
                'variants.*.sku' => 'required|distinct', // Unique check handled manually/softly for variants to allow updates
                'variants.*.sale_price' => 'nullable|numeric|lt:variants.*.price',
            ]);
        }


        DB::transaction(function () use ($request, $product) {
            // 3. Update General Info
            $data = [
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'description' => $request->description,
                'specifications' => $request->specifications,
                'tax_method' => $request->tax_method,
                'tax_rate' => $request->tax_rate ?? 0,
            ];

            // 4. Update Simple/Service Product Logic
            if ($product->type === 'simple' || $product->type === 'service') {
                $data['sku'] = $request->sku;
                $data['barcode'] = $request->barcode;
                $data['selling_price'] = $request->price;
                $data['sale_price'] = $request->sale_price;
                $data['cost_price'] = $request->cost;
                $data['stock_quantity'] = $request->stock;
                $data['alert_quantity'] = $request->alert_quantity;
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
                            'sale_price'     => $v['sale_price'],
                            'stock_quantity' => $v['stock'],
                            'alert_quantity' => $v['alert_quantity'] ?? 5
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

        $this->logActivity('Product', 'Edit', "Updated Product: {$product->name}", [
            'product_id' => $product->id,
            'sku' => $product->sku,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->type === 'variable') {
            $product->variants()->delete();
        }

        // Explicitly clear media to ensure storage files are removed
        $product->clearMediaCollection('product_image');
        $product->clearMediaCollection('product_gallery');

        $this->logActivity('Product', 'Delete', "Deleted Product: {$product->name}", [
            'sku' => $product->sku,
        ]);

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

    /**
     * Show the import form.
     */
    public function importForm()
    {
        return view('admin.products.import');
    }

    /**
     * Handle the import process.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return redirect()->route('products.index')->with('success', 'Products imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        Product::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected products deleted successfully.']);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'status' => 'required|in:draft,published'
        ]);

        Product::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully for selected products.']);
    }
}
