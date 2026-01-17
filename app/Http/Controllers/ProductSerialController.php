<?php

namespace App\Http\Controllers;

use App\Models\ProductSerial;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductSerialController extends Controller
{
    /**
     * Show the page to add serials for a specific Purchase Order
     */
    public function entry(PurchaseOrder $purchaseOrder)
    {
        // Load items that require serial numbers
        $purchaseOrder->load(['items.product', 'items.variant']);
        
        // Filter only items where the product has_serial_number = 1
        $items = $purchaseOrder->items->filter(function($item) {
            return $item->product->has_serial_number;
        });

        return view('admin.serials.entry', compact('purchaseOrder', 'items'));
    }

    /**
     * Store the serial numbers
     */
    public function store(Request $request)
    {
        $request->validate([
            'serials' => 'required|array',
            'serials.*.code' => 'required|string|distinct|unique:product_serials,serial_number',
            'serials.*.product_id' => 'required',
            'purchase_order_id' => 'required'
        ]);

        $count = 0;

        foreach ($request->serials as $data) {
            if(!empty($data['code'])) {
                ProductSerial::create([
                    'purchase_order_id' => $request->purchase_order_id,
                    'product_id' => $data['product_id'],
                    'product_variant_id' => $data['variant_id'] ?? null,
                    'serial_number' => $data['code'],
                    'status' => 'available'
                ]);
                $count++;
            }
        }

        return back()->with('success', "$count Serial Numbers Added Successfully!");
    }
    
    // Check if serial exists (For AJAX validation in View)
    public function check(Request $request) 
    {
        $exists = ProductSerial::where('serial_number', $request->serial)->exists();
        return response()->json(['exists' => $exists]);
    }
}