<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // We will fetch products via AJAX to keep page load fast
        return view('admin.purchases.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'reference_no' => 'required|string|max:50',
            'status' => 'required|in:received,pending',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Calculate Totals First
            $netTotal = 0;
            foreach ($request->items as $item) {
                $netTotal += ($item['qty'] * $item['cost']);
            }

            // VAT Calculation (Standard UAE 5%)
            $taxAmount = $netTotal * 0.05;
            $grandTotal = $netTotal + $taxAmount;

            // 2. Create Header
            $po = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                // Use provided reference or generate fallback
                'reference_no' => $request->reference_no ?? 'PO-' . strtoupper(uniqid()),
                'date' => $request->date,
                'status' => $request->status,
                'notes' => $request->notes,
                'total_cost' => $grandTotal, // Gross Amount (Payable to supplier)
                'tax_amount' => $taxAmount   // Input VAT (Recoverable)
            ]);

            // 3. Process Items
            foreach ($request->items as $item) {
                $subtotal = $item['qty'] * $item['cost'];

                // Create PO Item Record
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['qty'],
                    'unit_cost' => $item['cost'], // Cost EXCLUDING Tax
                    'subtotal' => $subtotal
                ]);

                // 4. Update Stock & Cost Price ONLY if Status is 'Received'
                if ($request->status === 'received') {
                    if (!empty($item['variant_id'])) {
                        // Variable Product
                        $variant = ProductVariant::find($item['variant_id']);
                        if ($variant) {
                            $variant->increment('stock_quantity', $item['qty']);

                            // Optional: Update Weighted Average Cost Price here if needed
                            // For now, we update to latest cost price
                            $variant->update(['cost_price' => $item['cost']]);
                        }
                    } else {
                        // Simple Product
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

    // AJAX Search for Product Selection
    public function searchProducts(Request $request)
    {
        $term = $request->term;

        // Search Simple Products
        $simple = Product::where('type', 'simple')
            ->where('name', 'LIKE', "%$term%")
            ->select('id', 'name', 'sku', 'cost_price')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'variant_id' => null,
                    'label' => $p->name . " (Simple) - SKU: " . $p->sku,
                    'cost' => $p->cost_price
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
                    'cost' => $v->cost_price
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

        $purchase->load(['supplier', 'items.product', 'items.variant']);

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
}
