<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get suppliers ordered by newest first, 10 per page
        $suppliers = Supplier::latest()->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'trn_number' => 'nullable|string|max:50', // Important for UAE VAT
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')
                         ->with('success', 'Supplier added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        // Load the purchase orders to show history on the profile page
        $supplier->load(['purchaseOrders' => function($query) {
            $query->latest()->limit(5); // Show last 5 orders
        }]);
        
        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'trn_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')
                         ->with('success', 'Supplier details updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            // Prevent deletion if supplier has history
            if ($supplier->purchaseOrders()->exists()) {
                return back()->with('error', 'Cannot delete this supplier because they have existing Purchase Orders. Contact Super Admin for force deletion.');
            }
        }

        try {
            \DB::transaction(function () use ($supplier) {
                // 1. Delete Purchase Orders and related data (Cascading)
                // We need to destock items before deleting
                $pos = $supplier->purchaseOrders()->with('items')->get();
                foreach ($pos as $po) {
                    // Revert Stock
                    foreach ($po->items as $item) {
                        if ($item->received_quantity > 0) {
                            if ($item->product_variant_id) {
                                $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                                if ($variant) $variant->decrement('stock_quantity', $item->received_quantity);
                            } else {
                                $product = \App\Models\Product::find($item->product_id);
                                if ($product) $product->decrement('stock_quantity', $item->received_quantity);
                            }
                        }
                    }

                    // Delete Receptions
                    $receptions = \App\Models\PurchaseReception::where('purchase_order_id', $po->id)->get();
                    foreach ($receptions as $reception) {
                        $reception->items()->delete();
                        $reception->delete();
                    }

                    // Delete PO Items and PO
                    $po->items()->delete();
                    $po->delete();
                }

                $supplier->delete();
            });

            return redirect()->route('suppliers.index')
                             ->with('success', 'Supplier and all related purchase data deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting supplier: ' . $e->getMessage());
        }
    }
}