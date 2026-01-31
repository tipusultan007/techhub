<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Traits\LogsActivity;

class QuotationController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $query = Quotation::with('customer', 'user', 'order');

        // Filter by Quotation No
        if ($request->filled('quotation_no')) {
            $query->where('quotation_no', 'LIKE', '%' . $request->quotation_no . '%');
        }

        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $quotations = $query->latest()->paginate(20)->withQueryString();
        
        $customers = Customer::select('id', 'name')->orderBy('name')->get();

        return view('admin.quotations.index', compact('quotations', 'customers'));
    }

    public function create()
    {
        $customers = Customer::select(['id', 'name', 'phone'])->latest('created_at')->get();
        $categories = Category::select(['id', 'name'])->orderBy('name')->get();
        $initialProducts = $this->getPosProducts('');

        return view('admin.quotations.create', compact('customers', 'categories', 'initialProducts'));
    }

    public function search(Request $request)
    {
        $products = $this->getPosProducts($request->term);
        return response()->json($products);
    }

    private function getPosProducts($term)
    {
        $queryLimit = 30;

        $simple = Product::whereIn('type', ['simple', 'service'])
            ->where(function ($q) {
                $q->where('stock_quantity', '>', 0)
                    ->orWhere('type', 'service');
            })
            ->when($term, function ($q) use ($term) {
                $q->where(function($sub) use ($term) {
                    $sub->where('name', 'LIKE', "%$term%")
                        ->orWhere('sku', 'LIKE', "%$term%")
                        ->orWhere('barcode', 'LIKE', "%$term%");
                });
            })
            ->latest()
            ->take($queryLimit)
            ->get();

        $variants = ProductVariant::with('product')
            ->when($term, function ($q) use ($term) {
                $q->where(function($sub) use ($term) {
                    $sub->where('variant_name', 'LIKE', "%$term%")
                        ->orWhere('sku', 'LIKE', "%$term%")
                        ->orWhere('barcode', 'LIKE', "%$term%")
                        ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%$term%"));
                });
            })
            ->latest()
            ->take($queryLimit)
            ->get();

        $results = [];

        foreach($simple as $p) {
            $results[] = [
                'id' => $p->id,
                'variant_id' => null,
                'name' => $p->name,
                'price' => $p->selling_price,
                'image' => $p->getFirstMediaUrl('product_image'),
                'stock' => $p->stock_quantity,
                'sku' => $p->sku,
                'type' => $p->type
            ];
        }

        foreach($variants as $v) {
            $results[] = [
                'id' => $v->product_id,
                'variant_id' => $v->id,
                'name' => $v->product->name . ' - ' . $v->variant_name,
                'price' => $v->selling_price,
                'image' => $v->product->getFirstMediaUrl('product_image'),
                'stock' => $v->stock_quantity,
                'sku' => $v->sku,
                'type' => 'variable'
            ];
        }

        return $results;
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'amount_paid' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalTax = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = ($item['price'] * $item['qty']);
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;
            }

            $discount = $request->discount ?? 0;
            $finalPayable = ($subtotal + $totalTax) - $discount;

            $quotation = Quotation::create([
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_id ? Customer::find($request->customer_id, ['name'])->name : 'Walk-in Customer',
                'date' => $request->date ?? now(),
                'subtotal' => $subtotal,
                'vat_amount' => $totalTax,
                'discount' => $discount,
                'total' => $finalPayable,
                'status' => 'submitted',
                'user_id' => Auth::id(),
                'expiry_date' => $request->expiry_date ?? now()->addDays(15) // Use manual date or default
            ]);

            foreach ($request->items as $item) {
                $itemSubtotal = ($item['price'] * $item['qty']);
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['id'] ?? null,
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $itemTax,
                    'subtotal' => $itemSubtotal,
                    'is_service' => filter_var($item['is_service'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);
            }

            DB::commit();

            $this->logActivity('Quotation', 'Create', "Created Quotation #{$quotation->quotation_no}", [
                'quotation_id' => $quotation->id,
                'quotation_no' => $quotation->quotation_no,
            ]);

            return response()->json([
                'status' => 'success',
                'quotation_id' => $quotation->id,
                'message' => 'Quotation created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Quotation $quotation)
    {
        if ($quotation->status != 'submitted') {
            return redirect()->route('quotations.show', $quotation->id)->with('error', 'Only submitted quotations can be edited.');
        }

        $quotation->load('items');
        $customers = Customer::select(['id', 'name', 'phone'])->latest('created_at')->get();
        $categories = Category::select(['id', 'name'])->orderBy('name')->get();
        $initialProducts = $this->getPosProducts('');

        return view('admin.quotations.edit', compact('quotation', 'customers', 'categories', 'initialProducts'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if ($quotation->status != 'submitted') {
            return response()->json(['status' => 'error', 'message' => 'Only submitted quotations can be edited.'], 403);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'discount' => 'nullable|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalTax = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = ($item['price'] * $item['qty']);
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                $subtotal += $itemSubtotal;
                $totalTax += $itemTax;
            }

            $discount = $request->discount ?? 0;
            $finalPayable = ($subtotal + $totalTax) - $discount;

            $quotation->update([
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_id ? Customer::find($request->customer_id, ['name'])->name : 'Walk-in Customer',
                'date' => $request->date ?? $quotation->date,
                'subtotal' => $subtotal,
                'vat_amount' => $totalTax,
                'discount' => $discount,
                'total' => $finalPayable,
                'expiry_date' => $request->expiry_date ?? $quotation->expiry_date,
            ]);

            // Delete old items and create new ones
            $quotation->items()->delete();

            foreach ($request->items as $item) {
                $itemSubtotal = ($item['price'] * $item['qty']);
                $itemTax = $itemSubtotal * (($item['tax_rate'] ?? 0) / 100);
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['id'] ?? null,
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $itemTax,
                    'subtotal' => $itemSubtotal,
                    'is_service' => filter_var($item['is_service'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]);
            }

            DB::commit();

            $this->logActivity('Quotation', 'Edit', "Updated Quotation #{$quotation->quotation_no}", [
                'quotation_id' => $quotation->id,
                'quotation_no' => $quotation->quotation_no,
            ]);

            return response()->json([
                'status' => 'success',
                'quotation_id' => $quotation->id,
                'message' => 'Quotation updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('items.product');
        return view('admin.quotations.show', compact('quotation'));
    }

    public function print(Quotation $quotation)
    {
        $quotation->load('items.product');
        return view('admin.quotations.print', compact('quotation'));
    }

    /**
     * Download Quotation as PDF.
     */
    public function downloadPdf(Quotation $quotation)
    {
        $quotation->load('items.product');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.quotations.pdf', compact('quotation'));
        
        $pdf->setOption('footer-right', 'Page [page] of [toPage]');
        $pdf->setOption('footer-left', 'Ref: ' . $quotation->quotation_no);
        $pdf->setOption('footer-font-size', 9);
        $pdf->setOption('footer-spacing', 5);
        
        return $pdf->download('Quotation-' . $quotation->quotation_no . '.pdf');
    }

    public function convertToSale(Quotation $quotation)
    {
        if ($quotation->status == 'converted') {
            return back()->with('error', 'Quotation already converted to sale.');
        }

        try {
            DB::beginTransaction();

            $lastOrder = Order::latest('id')->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
            $invoiceNo = 'INV-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $order = Order::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $quotation->customer_id,
                'customer_name' => $quotation->customer_name,
                'subtotal' => $quotation->subtotal,
                'vat_amount' => $quotation->vat_amount,
                'discount' => $quotation->discount,
                'total' => $quotation->total,
                'payment_method' => 'cash', // Default or ask user? Defaulting to cash for conversion
                'status' => 'completed',
                'user_id' => Auth::id(),
                'channel' => 'pos'
            ]);

            foreach ($quotation->items as $item) {
                // Stock Deduction Logic (Skip for services)
                if (!$item->is_service) {
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                        if (!$variant || $variant->stock_quantity < $item->quantity) {
                            throw new \Exception("Insufficient stock for variant: " . ($variant->sku ?? 'Unknown'));
                        }
                        $variant->decrement('stock_quantity', $item->quantity);
                    } else {
                        $product = Product::lockForUpdate()->find($item->product_id);
                        if ($product->type !== 'service') {
                            if (!$product || $product->stock_quantity < $item->quantity) {
                                throw new \Exception("Insufficient stock for product: " . ($product->name ?? 'Unknown'));
                            }
                            $product->decrement('stock_quantity', $item->quantity);
                        }
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'subtotal' => $item->subtotal,
                    'is_service' => filter_var($item->is_service, FILTER_VALIDATE_BOOLEAN),
                ]);
            }

            $quotation->forceFill([
                'status' => 'converted',
                'order_id' => $order->id
            ])->save();

            DB::commit();

            $this->logActivity('Quotation', 'Edit', "Converted Quotation #{$quotation->quotation_no} to Sale #{$order->invoice_no}", [
                'quotation_id' => $quotation->id,
                'order_id' => $order->id,
            ]);

            return redirect()->route('orders.show', $order->id)->with('success', 'Quotation converted to sale successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Conversion failed: ' . $e->getMessage());
        }
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Cannot delete a quotation that has already been converted to a sale.');
        }
        
        $this->logActivity('Quotation', 'Delete', "Deleted Quotation #{$quotation->quotation_no}", [
            'quotation_no' => $quotation->quotation_no,
        ]);
        
        $quotation->delete();
        return back()->with('success', 'Quotation deleted successfully');
    }
}
