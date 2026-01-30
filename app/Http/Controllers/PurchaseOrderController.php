<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReception;
use App\Models\PurchaseReceptionItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of Purchase Orders.
     */
    public function index()
    {
        $orders = PurchaseOrder::with(['supplier', 'items'])
            ->latest('date')
            ->paginate(10);

        return view('admin.purchases.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $nextPoNumber = PurchaseOrder::generateNextPONumber();
        // We will fetch products via AJAX to keep page load fast
        return view('admin.purchases.create', compact('suppliers', 'nextPoNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'reference_no' => 'required|string|max:50',
            'status' => 'required|in:completed,pending',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Calculate Totals First
            $netTotal = 0;
            $taxAmount = 0;
            foreach ($request->items as $item) {
                $lineNet = ($item['qty'] * $item['cost']);
                $netTotal += $lineNet;
                
                // Use tax rate from request
                $rate = $item['tax_rate'] ?? 0;
                $taxAmount += ($lineNet * ($rate / 100));
            }

            $grandTotal = $netTotal + $taxAmount;

            // 2. Create Header
            $po = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'reference_no' => $request->reference_no ?? PurchaseOrder::generateNextPONumber(),
                'date' => $request->date,
                'status' => $request->status,
                'notes' => $request->notes,
                'total_cost' => $grandTotal,
                'tax_amount' => $taxAmount
            ]);

            // 3. Process Items
            foreach ($request->items as $item) {
                $itemSubtotal = $item['qty'] * $item['cost'];
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['qty'],
                    'received_quantity' => $request->status === 'completed' ? $item['qty'] : 0,
                    'unit_cost' => $item['cost'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $itemTax,
                    'subtotal' => $itemSubtotal
                ]);

                // 4. Update Stock & Cost Price ONLY if Status is 'completed'
                if ($request->status === 'completed') {
                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::find($item['variant_id']);
                        if ($variant) {
                            $variant->increment('stock_quantity', $item['qty']);
                            $variant->update(['cost_price' => $item['cost']]);
                        }
                    } else {
                        $product = Product::find($item['product_id']);
                        if ($product) {
                            $product->increment('stock_quantity', $item['qty']);
                            $product->update(['cost_price' => $item['cost']]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase Order saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving purchase: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $purchase = PurchaseOrder::with(['items.product', 'items.variant'])->findOrFail($id);
        $suppliers = Supplier::all();
        return view('admin.purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'reference_no' => 'required|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $po = PurchaseOrder::findOrFail($id);

            // 1. Calculate Totals
            $netTotal = 0;
            $taxAmount = 0;
            foreach ($request->items as $item) {
                $lineNet = ($item['qty'] * $item['cost']);
                $netTotal += $lineNet;
                $rate = $item['tax_rate'] ?? 0;
                $taxAmount += ($lineNet * ($rate / 100));
            }

            $grandTotal = $netTotal + $taxAmount;

            // 2. Update Header
            $po->update([
                'supplier_id' => $request->supplier_id,
                'reference_no' => $request->reference_no,
                'date' => $request->date,
                'notes' => $request->notes,
                'total_cost' => $grandTotal,
                'tax_amount' => $taxAmount
            ]);

            // 3. Sync Items (Robust sync to preserve reception history)
            $existingItemIds = $po->items->pluck('id')->toArray();
            $updatedItemIds = [];

            foreach ($request->items as $itemData) {
                $itemSubtotal = $itemData['qty'] * $itemData['cost'];
                $itemTax = $itemSubtotal * (($itemData['tax_rate'] ?? 0) / 100);

                // Try to find existing item to update
                $poItem = null;
                if (isset($itemData['item_id'])) {
                    $poItem = PurchaseOrderItem::find($itemData['item_id']);
                }

                if ($poItem && $poItem->purchase_order_id == $po->id) {
                    // Update existing item
                    $poItem->update([
                        'quantity' => $itemData['qty'],
                        'unit_cost' => $itemData['cost'],
                        'tax_rate' => $itemData['tax_rate'] ?? 0,
                        'tax_amount' => $itemTax,
                        'subtotal' => $itemSubtotal,
                        // Update received_quantity if status is completed
                        'received_quantity' => $po->status === 'completed' ? $itemData['qty'] : $poItem->received_quantity
                    ]);
                    $updatedItemIds[] = $poItem->id;
                } else {
                    // Create new item
                    $newItem = PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'product_id' => $itemData['product_id'],
                        'product_variant_id' => $itemData['variant_id'] ?? null,
                        'quantity' => $itemData['qty'],
                        'received_quantity' => $po->status === 'completed' ? $itemData['qty'] : 0,
                        'unit_cost' => $itemData['cost'],
                        'tax_rate' => $itemData['tax_rate'] ?? 0,
                        'tax_amount' => $itemTax,
                        'subtotal' => $itemSubtotal
                    ]);
                    $updatedItemIds[] = $newItem->id;
                }
            }

            // Remove items that are no longer in the request
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            foreach ($itemsToDelete as $deleteId) {
                $itemToDelete = PurchaseOrderItem::find($deleteId);
                if ($itemToDelete && $itemToDelete->received_quantity == 0) {
                    $itemToDelete->delete();
                }
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk reception of items.
     */
    public function bulkReceive(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.received_qty' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $po = PurchaseOrder::findOrFail($id);
            
            // Filter items that have a received quantity > 0
            $receivedItems = array_filter($request->items, function($item) {
                return isset($item['received_qty']) && $item['received_qty'] > 0;
            });

            if (empty($receivedItems)) {
                throw new \Exception('Please enter quantity for at least one item.');
            }

            // Create Reception Header
            $reception = PurchaseReception::create([
                'purchase_order_id' => $po->id,
                'reception_no' => 'REC-' . strtoupper(uniqid()),
                'date' => now(),
                'received_by' => Auth::user()->name,
                'notes' => $request->notes,
            ]);

            foreach ($receivedItems as $itemId => $data) {
                $poItem = PurchaseOrderItem::findOrFail($itemId);
                $qty = $data['received_qty'];

                if ($qty > $poItem->remaining_quantity()) {
                    throw new \Exception("Received quantity for {$poItem->product->name} cannot exceed remaining quantity.");
                }

                // Create Reception Item
                PurchaseReceptionItem::create([
                    'purchase_reception_id' => $reception->id,
                    'purchase_order_item_id' => $poItem->id,
                    'quantity' => $qty,
                ]);

                // Update PO Item
                $poItem->increment('received_quantity', $qty);

                // Update Stock
                if ($poItem->product_variant_id) {
                    $variant = ProductVariant::find($poItem->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock_quantity', $qty);
                        $variant->update(['cost_price' => $poItem->unit_cost]);
                    }
                } else {
                    $product = Product::find($poItem->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $qty);
                        $product->update(['cost_price' => $poItem->unit_cost]);
                    }
                }
            }

            // Update PO Status
            $this->updatePOStatus($po);

            DB::commit();
            return back()->with('success', 'Reception recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark entire PO as completed.
     */
    public function markAsCompleted($id)
    {
        try {
            DB::beginTransaction();

            $po = PurchaseOrder::with('items')->findOrFail($id);

            $reception = null;
            $itemsToReceive = $po->items->filter(fn($i) => $i->remaining_quantity() > 0);

            if ($itemsToReceive->isNotEmpty()) {
                $reception = PurchaseReception::create([
                    'purchase_order_id' => $po->id,
                    'reception_no' => 'REC-' . strtoupper(uniqid()),
                    'date' => now(),
                    'received_by' => Auth::user()->name,
                    'notes' => 'Bulk completion',
                ]);

                foreach ($itemsToReceive as $item) {
                    $remaining = $item->remaining_quantity();
                    
                    PurchaseReceptionItem::create([
                        'purchase_reception_id' => $reception->id,
                        'purchase_order_item_id' => $item->id,
                        'quantity' => $remaining,
                    ]);

                    $item->increment('received_quantity', $remaining);

                    // Update Stock
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock_quantity', $remaining);
                            $variant->update(['cost_price' => $item->unit_cost]);
                        }
                    } else {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock_quantity', $remaining);
                            $product->update(['cost_price' => $item->unit_cost]);
                        }
                    }
                }
            }

            $po->update(['status' => 'completed']);

            DB::commit();
            return back()->with('success', 'Purchase Order marked as completed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function updatePOStatus($po)
    {
        $po->load('items');
        $allProcessed = true;
        $anyReceived = false;

        foreach ($po->items as $item) {
            if (!$item->is_fully_received()) {
                $allProcessed = false;
            }
            if ($item->received_quantity > 0) {
                $anyReceived = true;
            }
        }

        if ($allProcessed) {
            $po->update(['status' => 'completed']);
        } elseif ($anyReceived) {
            $po->update(['status' => 'partial_received']);
        } else {
            $po->update(['status' => 'pending']);
        }
    }

    /**
     * Print a specific reception invoice.
     */
    public function printReception($id, $receptionId)
    {
        $reception = PurchaseReception::with(['purchaseOrder.supplier', 'items.poItem.product', 'items.poItem.variant'])
            ->where('purchase_order_id', $id)
            ->findOrFail($receptionId);

        return view('admin.purchases.reception_print', compact('reception'));
    }

    /**
     * Download PO as PDF.
     */
    public function downloadPdf($id)
    {
        $purchase = PurchaseOrder::with(['supplier', 'items.product', 'items.variant'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.purchases.pdf', compact('purchase'));
        return $pdf->download("PO-{$purchase->reference_no}.pdf");
    }

    // AJAX Search for Product Selection
    public function searchProducts(Request $request)
    {
        $term = $request->term;

        // Search Simple Products
        $simple = Product::where('type', 'simple')
            ->where(function ($query) use ($term) {
                $query->where('name', 'LIKE', "%$term%")
                    ->orWhere('sku', 'LIKE', "%$term%")
                    ->orWhere('barcode', 'LIKE', "%$term%");
            })
            ->select('id', 'name', 'sku', 'cost_price', 'tax_rate', 'tax_method')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'variant_id' => null,
                    'label' => $p->name . " (Simple) - SKU: " . $p->sku,
                    'cost' => $p->cost_price,
                    'tax_rate' => $p->tax_rate,
                    'tax_method' => $p->tax_method
                ];
            });

        // Search Variable Products (Variants)
        $variable = ProductVariant::with('product')
            ->where('sku', 'LIKE', "%$term%")
            ->orWhereHas('product', function ($q) use ($term) {
                $q->where('name', 'LIKE', "%$term%");
            })
            ->get()
            ->map(function ($v) {
                return [
                    'id' => $v->product_id,
                    'variant_id' => $v->id,
                    'label' => $v->product->name . " - " . $v->variant_name . " (SKU: $v->sku)",
                    'cost' => $v->cost_price,
                    'tax_rate' => $v->product->tax_rate,
                    'tax_method' => $v->product->tax_method
                ];
            });

        //return response()->json($simple->merge($variable));
        return response()->json($simple->toBase()->merge($variable));
    }

    /**
     * Display the specified Purchase Order.
     */
    public function show(PurchaseOrder $purchase) // Using route model binding
    {
        // Change variable name in route to match model or use $purchase
        // Assuming route is /purchases/{purchase}

        $purchase->load(['supplier', 'items.product', 'items.variant', 'receptions.items.poItem.product']);

        return view('admin.purchases.show', compact('purchase'));
    }

    /**
     * specific method to show a print-friendly layout
     */
    public function printInvoice($id)
    {
        $purchase = PurchaseOrder::with(['supplier', 'items.product', 'items.variant'])->findOrFail($id);
        return view('admin.purchases.print', compact('purchase'));
    }
    public function print($id)
    {
        $purchase = PurchaseOrder::with(['supplier', 'items.product', 'items.variant'])->findOrFail($id);
        return view('admin.purchases.print', compact('purchase'));
    }
    public function destroy($id)
    {
        $purchase = PurchaseOrder::with('items')->findOrFail($id);

        // Restriction: Cannot delete if items were received or status is completed
        if ($purchase->status === 'completed' || $purchase->items->sum('received_quantity') > 0) {
            return back()->with('error', 'Cannot delete this purchase order because items have already been received or the order is marked as completed.');
        }

        try {
            DB::transaction(function () use ($purchase) {
                $purchase->items()->delete();
                $purchase->delete();
            });

            return back()->with('success', 'Purchase order deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
