<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Notifications\OrderStatusUpdateNotification;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OrderController extends Controller implements HasMiddleware
{
    use LogsActivity;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage orders', only: ['edit', 'update', 'updateStatus', 'destroy']),
            new Middleware('permission:view orders', only: ['index', 'show', 'print', 'downloadPdf', 'details']),
        ];
    }

    /**
     * Display a listing of sales orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'user']);

        // Filter by Invoice No
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'LIKE', '%'.$request->invoice_no.'%');
        }

        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Payment Method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by Channel (Online/POS)
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $customers = \App\Models\Customer::select('id', 'name')->orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'customers'));
    }

    /**
     * Display the specified order (Invoice View).
     */
    public function show(Order $order)
    {
        // Eager load data needed for details
        $order->load(['items.product', 'customer', 'history.user']);

        // SEPARATION LOGIC:
        if ($order->channel === 'online') {
            // Return the specialized Online Order view (Sidebar, Shipping info, etc.)
            return view('admin.orders.show_online', compact('order'));
        }

        // Return your existing/standard view for POS or generic orders
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load(['items.product', 'items.variant', 'customer']);
        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('admin.orders.edit', compact('order', 'customers'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'po_number' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.product_name' => 'nullable|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer,advance,custom',
            'discount' => 'nullable|numeric|min:0',
            'attachment.*' => 'nullable|file|max:102400',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                // 1. Restore Stock from Old Items
                foreach ($order->items as $oldItem) {
                    if ($oldItem->product_id) {
                        if ($oldItem->product_variant_id) {
                            ProductVariant::where('id', $oldItem->product_variant_id)->increment('stock_quantity', $oldItem->quantity);
                        } else {
                            Product::where('id', $oldItem->product_id)->increment('stock_quantity', $oldItem->quantity);
                        }

                        // Log Inventory Transaction (Restore)
                        InventoryTransaction::create([
                            'product_id' => $oldItem->product_id,
                            'product_variant_id' => $oldItem->product_variant_id,
                            'type' => 'in',
                            'quantity' => $oldItem->quantity,
                            'description' => 'Order Update (Restore): '.$order->invoice_no,
                            'reference_id' => $order->id,
                            'reference_type' => get_class($order),
                            'user_id' => auth()->id(),
                        ]);
                    }
                }

                // 2. Delete Old Items
                $order->items()->delete();

                // 3. Re-calculate and Insert New Items
                $totalTax = 0;
                $totalSubtotal = 0; // Net amount (sum of row subtotals before tax)
                $totalGrand = 0;

                foreach ($request->items as $itemData) {
                    $qty = $itemData['qty'];
                    $price = $itemData['price']; // This is treated as inclusive price matching POS behavior
                    $taxRate = $itemData['tax_rate'];

                    // POS Model: Price is inclusive
                    // subtotal (inclusive) = price * qty
                    // tax_amount = inclusive_subtotal - (inclusive_subtotal / (1 + tax_rate/100))
                    // net_subtotal = inclusive_subtotal - tax_amount

                    $rowNetSubtotal = $price * $qty;
                    $rowTaxAmount = $rowNetSubtotal * ($taxRate / 100);
                    $rowInclusiveTotal = $rowNetSubtotal + $rowTaxAmount;

                    $productName = $itemData['product_name'] ?? 'Unknown Item';
                    if ($itemData['product_id']) {
                        $p = Product::find($itemData['product_id']);
                        $productName = $p->name;
                        if ($itemData['variant_id']) {
                            $v = ProductVariant::find($itemData['variant_id']);
                            if ($v) {
                                $productName .= ' - '.$v->variant_name;
                            }
                        }
                    }

                    $orderItem = $order->items()->create([
                        'product_id' => $itemData['product_id'] ?? null,
                        'product_variant_id' => $itemData['variant_id'] ?? null,
                        'product_name' => $productName,
                        'quantity' => $qty,
                        'unit_price' => $price, // Base price (exclusive)
                        'tax_rate' => $taxRate,
                        'tax_amount' => $rowTaxAmount,
                        'subtotal' => $rowNetSubtotal, // Row total exclusive
                    ]);

                    // 4. Deduct New Stock
                    if ($orderItem->product_id) {
                        if ($orderItem->product_variant_id) {
                            $variant = ProductVariant::find($orderItem->product_variant_id);
                            if ($variant && $variant->stock_quantity < $qty) {
                                throw new \Exception('Insufficient stock for '.$orderItem->product_name);
                            }
                            if ($variant) {
                                $variant->decrement('stock_quantity', $qty);
                            }
                        } else {
                            $product = Product::find($orderItem->product_id);
                            if ($product && $product->type !== 'service') {
                                if ($product->stock_quantity < $qty) {
                                    throw new \Exception('Insufficient stock for '.$orderItem->product_name);
                                }
                                $product->decrement('stock_quantity', $qty);
                            }
                        }

                        // Log Inventory Transaction (Deduct)
                        InventoryTransaction::create([
                            'product_id' => $orderItem->product_id,
                            'product_variant_id' => $orderItem->product_variant_id,
                            'type' => 'out',
                            'quantity' => $qty,
                            'description' => 'Order Update (Deduct): '.$order->invoice_no,
                            'reference_id' => $order->id,
                            'reference_type' => get_class($order),
                            'user_id' => auth()->id(),
                        ]);
                    }

                    $totalTax += $rowTaxAmount;
                    $totalSubtotal += $rowNetSubtotal;
                    $totalGrand += $rowInclusiveTotal;
                }

                // 5. Update Order Header
                $discount = $request->discount ?? 0;
                $finalGrand = $totalGrand - $discount;

                // Adjust tax proportionally if discount is applied to total
                if ($totalGrand > 0) {
                    $totalTax = $totalTax * ($finalGrand / $totalGrand);
                }

                $order->update([
                    'customer_id' => $request->customer_id,
                    'customer_name' => $request->customer_id ? \App\Models\Customer::find($request->customer_id)->name : 'Guest/Walk-in',
                    'po_number' => $request->po_number,
                    'payment_method' => $request->payment_method,
                    'subtotal' => $finalGrand - $totalTax, 
                    'vat_amount' => $totalTax,
                    'discount' => $discount,
                    'total' => $finalGrand,
                ]);

                // 6. Log History
                $order->history()->create([
                    'status' => $order->status,
                    'comment' => 'Order details updated by '.auth()->user()->name,
                    'user_id' => auth()->id(),
                ]);

                $this->logActivity('Order', 'Edit', "Updated Order #{$order->invoice_no}", [
                    'order_id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'grand_total' => $finalGrand,
                ]);

                // Handle Attachment (Replace existing)
                if ($request->hasFile('attachment')) {
                    $order->clearMediaCollection('attachments');
                    foreach ($request->file('attachment') as $file) {
                        $order->addMedia($file)->toMediaCollection('attachments');
                    }
                }
            });

            return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            \Log::error("Error updating order #{$order->id}: ".$e->getMessage());

            return back()->with('error', 'Update failed: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Print layout (Thermal Printer 80mm).
     * This method renders the specific print view designed for POS printers.
     */
    public function print(Order $order)
    {
        $order->load(['items']);

        return view('admin.orders.print', compact('order'));
    }

    /**
     * Download Invoice as PDF.
     */
    public function downloadPdf(Order $order)
    {
        $order->load(['items']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.pdf', compact('order'));

        return $pdf->download('Invoice-'.$order->invoice_no.'.pdf');
    }

    // App\Http\Controllers\OrderController.php

    public function details(Order $order)
    {
        // Eager load relationships for performance
        $order->load(['items.product', 'customer', 'history.user']);

        return view('admin.orders.details', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled,returned',
            'comment' => 'nullable|string',
        ]);

        // Update Order Status
        $order->update([
            'status' => $request->status,
        ]);

        $this->logActivity('Order', 'Edit', "Updated Order #{$order->invoice_no} status to {$request->status}", [
            'order_id' => $order->id,
            'invoice_no' => $order->invoice_no,
            'status' => $request->status,
        ]);

        // Log Activity
        $history = $order->history()->create([
            'status' => $request->status,
            'comment' => $request->comment ?? 'Status updated to '.ucfirst($request->status),
            'user_id' => auth()->id(),
        ]);

        // Notify Customer of Status Update
        $customerEmail = $order->guest_email ?? ($order->customer ? $order->customer->email : null);
        if ($customerEmail) {
            Notification::route('mail', $customerEmail)
                ->notify(new OrderStatusUpdateNotification($order, $history->comment));
        }

        return back()->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
        // 1. Authorization: Only Super Admin can delete
        if (! auth()->user()->hasRole('Super Admin')) {
            return back()->with('error', 'Only Super Admin can delete sales orders.');
        }

        // 2. Bypass restriction for Super Admin
        // Prevent accidental deletion of completed orders for others
        if ($order->status === 'completed' && ! auth()->user()->hasRole('Super Admin')) {
            return back()->with('error', 'Cannot delete a completed order. Please mark as returned instead.');
        }

        try {
            DB::transaction(function () use ($order) {
                // 0. Delete Related Returns & Revert their stock addition
                $returns = \App\Models\ReturnOrder::with('items')->where('order_id', $order->id)->get();
                foreach ($returns as $return) {
                    foreach ($return->items as $rItem) {
                        if ($rItem->restock_status === 'restockable') {
                            if ($rItem->product_variant_id) {
                                $variant = \App\Models\ProductVariant::find($rItem->product_variant_id);
                                if ($variant) {
                                    $variant->decrement('stock_quantity', $rItem->quantity);
                                }
                            } else {
                                $product = \App\Models\Product::find($rItem->product_id);
                                if ($product) {
                                    $product->decrement('stock_quantity', $rItem->quantity);
                                }
                            }
                        }
                    }
                    $return->items()->delete();
                    $return->delete();
                }

                // 1. Restock Inventory
                foreach ($order->items as $item) {
                    // Check if it's a Variant or Simple Product
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock_quantity', $item->quantity);
                        }
                    } else {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock_quantity', $item->quantity);
                        }
                    }

                    // Log Inventory Transaction (Restore on Delete)
                    InventoryTransaction::create([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'description' => 'Order Deletion: '.$order->invoice_no,
                        'reference_id' => $order->id,
                        'reference_type' => get_class($order),
                        'user_id' => auth()->id(),
                    ]);
                }

                // 2. Delete Related Data (History/Logs)
                // Note: Order Items are usually deleted automatically via database
                // cascading if you used ->cascadeOnDelete() in migrations.
                // But we delete explicitly here to be safe and trigger model events if any.
                $order->items()->delete();

                if (method_exists($order, 'history')) {
                    $order->history()->delete();
                }

                // 3. Delete the Order Header
                $order->delete();
            });

            $this->logActivity('Order', 'Delete', "Deleted Order #{$order->invoice_no}", [
                'invoice_no' => $order->invoice_no,
            ]);

            return back()->with('success', 'Order deleted and items restocked successfully.');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error("Error deleting order #{$order->id}: ".$e->getMessage());

            return back()->with('error', 'Something went wrong while deleting the order.');
        }
    }
}
