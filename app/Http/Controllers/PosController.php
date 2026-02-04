<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\InventoryTransaction;
use App\Notifications\NewOrderNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Support\Facades\Notification;

use App\Traits\LogsActivity;

class PosController extends Controller
{
    use LogsActivity;
    // // 1. Load the POS Interface
    // public function index()
    // {
    //     $customers = Customer::all();
    //     // Load initial products (limit 20 for speed)
    //     $products = Product::with(['variants', 'media'])
    //         ->where('stock_quantity', '>', 0)
    //         ->orWhereHas('variants', fn($q) => $q->where('stock_quantity', '>', 0))
    //         ->latest()
    //         ->limit(20)
    //         ->get();

    //     return view('admin.pos.index', compact('customers', 'products'));
    // }

    // // 2. AJAX Product Search (Barcode or Name)
    // public function search(Request $request)
    // {
    //     $term = $request->term;

    //     // Search Simple Products
    //     $simple = Product::where('type', 'simple')
    //         ->where(function ($q) use ($term) {
    //             $q->where('name', 'LIKE', "%$term%")
    //                 ->orWhere('sku', 'LIKE', "%$term%")
    //                 ->orWhere('barcode', 'LIKE', "%$term%");
    //         })->where('stock_quantity', '>', 0)->get();

    //     // Search Variants
    //     // Updated Variable Product Search
    //     $variants = \App\Models\ProductVariant::with('product')
    //         ->where('stock_quantity', '>', 0)
    //         ->where(function ($q) use ($term) {
    //             $q->where('sku', 'LIKE', "%$term%")
    //                 ->orWhere('barcode', 'LIKE', "%$term%")
    //                 ->orWhere('variant_name', 'LIKE', "%$term%") // Searches "Red / 128GB"
    //                 // Search inside the linked Attribute Values (Professional search)
    //                 ->orWhereHas('attributeValues', function ($subQ) use ($term) {
    //                     $subQ->where('value', 'LIKE', "%$term%");
    //                 });
    //         })
    //         ->get();
    //     // Format data for JS
    //     $results = [];
    //     foreach ($simple as $p) {
    //         $results[] = [
    //             'id' => $p->id,
    //             'variant_id' => null,
    //             'name' => $p->name,
    //             'price' => $p->selling_price,
    //             'image' => $p->getFirstMediaUrl('product_image') ?: asset('no-image.png'),
    //             'stock' => $p->stock_quantity,
    //             'sku' => $p->sku
    //         ];
    //     }
    //     foreach ($variants as $v) {
    //         $results[] = [
    //             'id' => $v->product_id,
    //             'variant_id' => $v->id,
    //             'name' => $v->product->name . ' - ' . $v->variant_name, // "iPhone 15 - Red / 128GB"
    //             'price' => $v->selling_price,
    //             'stock' => $v->stock_quantity,
    //             'sku' => $v->sku
    //         ];
    //     }
    //     return response()->json($results);
    // }

      /**
     * Load the POS Interface with Initial Products
     */
    public function index()
    {
        $customers = Customer::select('id', 'name', 'phone')->latest()->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        // Load initial 30 products for instant display
        $initialProducts = $this->getPosProducts('');

        return view('admin.pos.index', compact('customers', 'categories', 'initialProducts'));
    }

    /**
     * AJAX Search (and Default Load)
     */
    public function search(Request $request)
    {
        $products = $this->getPosProducts($request->term);
        return response()->json($products);
    }

    /**
     * Helper: Unified Query Logic for Simple + Variable Products
     */
    private function getPosProducts($term)
    {
        $queryLimit = 30;

        // 1. Search Simple & Service Products
        $simple = Product::whereIn('type', ['simple', 'service'])
            ->where(function ($q) {
                $q->where('stock_quantity', '>', 0)
                    ->orWhere('type', 'service'); // Services don't need stock
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

        // 2. Search Variants (Variable Products)
        $variants = ProductVariant::with('product')
            ->where('stock_quantity', '>', 0) // Only in stock
            ->when($term, function ($q) use ($term) {
                $q->where(function($sub) use ($term) {
                    $sub->where('variant_name', 'LIKE', "%$term%") // Search "Red / 128GB"
                        ->orWhere('sku', 'LIKE', "%$term%")
                        ->orWhere('barcode', 'LIKE', "%$term%")
                        ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%$term%"));
                });
            })
            ->latest()
            ->take($queryLimit)
            ->get();

        // 3. Merge & Format Data
        $results = [];

        // Format Simple
        foreach($simple as $p) {
            $results[] = [
                'id' => $p->id,
                'variant_id' => null,
                'name' => $p->name,
                'price' => $p->selling_price,
                'image' => $p->getFirstMediaUrl('product_image'),
                'stock' => $p->stock_quantity,
                'sku' => $p->sku,
                'has_serial_number' => $p->has_serial_number,
                'tax_method' => $p->tax_method,
                'tax_rate' => $p->tax_rate,
                'type' => $p->type
            ];
        }

        // Format Variants
        foreach($variants as $v) {
            $results[] = [
                'id' => $v->product_id,
                'variant_id' => $v->id,
                'name' => $v->product->name . ' - ' . $v->variant_name,
                'price' => $v->selling_price,
                'image' => $v->product->getFirstMediaUrl('product_image'), // Inherit parent image
                'stock' => $v->stock_quantity,
                'sku' => $v->sku,
                'has_serial_number' => $v->product->has_serial_number,
                'tax_method' => $v->product->tax_method,
                'tax_rate' => $v->product->tax_rate,
                'type' => 'variable'
            ];
        }

        return $results;
    }

    // 3. Store Sale (Checkout)
    public function store(Request $request)
{
    // 1. Validation
    $request->validate([
        'items' => 'required|array|min:1',
        'amount_paid' => 'required|numeric|min:0', // This is the Final Total (after discount)
        'discount' => 'nullable|numeric|min:0',
        'payment_method' => 'required|string',
        'customer_id' => 'nullable|exists:customers,id'
    ]);

    try {
        DB::beginTransaction();

        // 4. Granular Tax Calculation (Server-Side)
        $totalVat = 0;
        $calculatedPayable = 0;

        foreach ($request->items as $item) {
            $itemTaxRate = ($item['tax_rate'] ?? 0) / 100;
            $itemPrice = (float) $item['price'];
            $itemQty = (int) $item['qty'];
            $rowSubtotal = $itemPrice * $itemQty;
            $rowTax = 0;
            $rowPayable = 0;

            if (($item['tax_method'] ?? 'inclusive') === 'exclusive') {
                $rowTax = $rowSubtotal * $itemTaxRate;
                $rowPayable = $rowSubtotal + $rowTax;
            } else { // inclusive
                $rowTax = $rowSubtotal - ($rowSubtotal / (1 + $itemTaxRate));
                $rowPayable = $rowSubtotal;
            }

            $totalVat += $rowTax;
            $calculatedPayable += $rowPayable;
        }

        // Apply Discount
        $discount = $request->discount ?? 0;
        $finalPayable = $calculatedPayable - $discount;

        // Proportional VAT Adjustment (if discount applied)
        $vatAmount = $calculatedPayable > 0 ? ($totalVat * ($finalPayable / $calculatedPayable)) : 0;
        $netAmount = $finalPayable - $vatAmount;

        // 5. Generate Invoice Number
        $lastOrder = Order::latest()->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $invoiceNo = 'INV-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        // 6. Create Order Record
        $order = Order::create([
            'invoice_no' => $invoiceNo,
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_id ? Customer::find($request->customer_id)->name : 'Walk-in Customer',
            'po_number' => $request->po_number,
            
            // Financials
            'subtotal' => $netAmount,   // Amount before Tax
            'vat_amount' => $vatAmount, // Tax Amount
            'discount' => $discount,    // Discount applied
            'total' => $finalPayable,   // Final Amount Paid
            
            'payment_method' => $request->payment_method,
            'status' => 'completed',
            'channel' => 'pos',
            'user_id' => Auth::id()
        ]);

        // Notify Admin about new POS order
        User::role('Admin')->get()->each->notify(new NewOrderNotification($order));

        // Notify Customer about POS Order
        $customer = $request->customer_id ? Customer::find($request->customer_id) : null;
        if ($customer && $customer->email) {
            $order->load('items'); // Ensure items are loaded for the email template
            $customer->notify(new OrderConfirmationNotification($order));
        }

        // 7. Process Items
        foreach ($request->items as $item) {
            
            // --- A. Stock Deduction ---
            if (!empty($item['variant_id'])) {
                // Variable Product
                $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                if (!$variant || $variant->stock_quantity < $item['qty']) {
                    throw new \Exception("Insufficient stock for variant: " . ($variant->sku ?? 'Unknown'));
                }
                $variant->decrement('stock_quantity', $item['qty']);
                $productNameSnapshot = $variant->product->name . ' - ' . $variant->variant_name;

                // Check Low Stock
                if ($variant->stock_quantity <= $variant->alert_quantity) {
                    User::role('Admin')->get()->each->notify(new LowStockNotification($variant->product, $variant->stock_quantity));
                }

                // Log Inventory Transaction
                InventoryTransaction::create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'type' => 'out',
                    'quantity' => $item['qty'],
                    'description' => 'POS Sale: ' . $order->invoice_no,
                    'reference_id' => $order->id,
                    'reference_type' => get_class($order),
                    'user_id' => Auth::id(),
                ]);
            } elseif (!empty($item['id'])) {
                // Simple or Service Product
                $product = Product::lockForUpdate()->find($item['id']);
                
                if ($product->type !== 'service' && !($item['is_service'] ?? false)) {
                    if (!$product || $product->stock_quantity < $item['qty']) {
                        throw new \Exception("Insufficient stock for product: " . ($product->name ?? 'Unknown'));
                    }
                    $product->decrement('stock_quantity', $item['qty']);

                    // Check Low Stock
                    if ($product->stock_quantity <= $product->alert_quantity) {
                        User::role('Admin')->get()->each->notify(new LowStockNotification($product, $product->stock_quantity));
                    }

                    // Log Inventory Transaction
                    InventoryTransaction::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $item['qty'],
                        'description' => 'POS Sale: ' . $order->invoice_no,
                        'reference_id' => $order->id,
                        'reference_type' => get_class($order),
                        'user_id' => Auth::id(),
                    ]);
                }
                
                $productNameSnapshot = $item['name'] ?? $product->name;
            } else {
                // Instant Service (No ID)
                $productNameSnapshot = $item['name'] ?? 'Generic Service';
            }

            // --- B. Serial Number & Warranty Logic ---
            $serialNumbers = null;
            $warrantyEnd = null;

            if (!empty($item['serial'])) {
                $serialNumbers = $item['serial'];

                // Mark Serial as SOLD
                $serialRecord = \App\Models\ProductSerial::where('serial_number', $item['serial'])
                    ->where('status', 'available')
                    ->first();

                if ($serialRecord) {
                    $serialRecord->update([
                        'status' => 'sold',
                        'order_id' => $order->id
                    ]);
                } else {
                    // Fail safe if serial scanned but sold in another transaction milliseconds ago
                    throw new \Exception("Serial number {$item['serial']} is no longer available.");
                }

                // Calculate Warranty Date
                $product = Product::find($item['id']);
                if ($product->warranty_duration && $product->warranty_type) {
                    $date = now();
                    if ($product->warranty_type == 'months') $date->addMonths($product->warranty_duration);
                    elseif ($product->warranty_type == 'days') $date->addDays($product->warranty_duration);
                    elseif ($product->warranty_type == 'years') $date->addYears($product->warranty_duration);

                    $warrantyEnd = $date;
                }
            }

            // --- C. Create Order Item ---
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'] ?? null,
                'product_variant_id' => $item['variant_id'] ?? null,
                'product_name' => $productNameSnapshot, // Save snapshot of name
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
                'serial_numbers' => $serialNumbers,
                'warranty_end_date' => $warrantyEnd,
                'tax_rate' => $item['tax_rate'] ?? 0,
                'tax_amount' => ($item['price'] * $item['qty']) * (($item['tax_rate'] ?? 0) / 100),
                'is_service' => filter_var($item['is_service'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        DB::commit();

        $this->logActivity('POS', 'Create', "Created POS order #{$order->invoice_no}", [
            'order_id' => $order->id,
            'invoice_no' => $order->invoice_no,
            'total' => $order->total,
        ]);
        
        return response()->json([
            'status' => 'success', 
            'order_id' => $order->id,
            'message' => 'Sale processed successfully'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error', 
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'trn_number' => 'nullable|string|max:50',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'trn_number' => $request->trn_number,
            'password' => bcrypt('12345678'), // Default password, they can reset it
        ]);

        $this->logActivity('POS', 'Create', "Created customer {$customer->name} via POS", [
            'customer_id' => $customer->id,
            'name' => $customer->name,
        ]);

        return response()->json([
            'status' => 'success',
            'customer' => $customer,
            'message' => 'Customer created successfully'
        ]);
    }

    public function checkSerial(Request $request)
    {
        $exists = \App\Models\ProductSerial::where('serial_number', $request->serial)
            ->where('product_id', $request->product_id)
            ->where('status', 'available') // Only unsold items
            ->first();

        if ($exists) {
            return response()->json(['valid' => true]);
        } else {
            return response()->json(['valid' => false, 'message' => 'Serial Invalid or Sold']);
        }
    }
}
