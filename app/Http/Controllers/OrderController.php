<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderStatusUpdateNotification;
use Illuminate\Support\Facades\Notification;

use App\Traits\LogsActivity;

class OrderController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of sales orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'user']);

        // Filter by Invoice No
        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'LIKE', '%' . $request->invoice_no . '%');
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
        return $pdf->download('Invoice-' . $order->invoice_no . '.pdf');
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
            'comment' => 'nullable|string'
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
            'comment' => $request->comment ?? 'Status updated to ' . ucfirst($request->status),
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
        // Prevent accidental deletion of completed orders (Optional safety check)
         if ($order->status === 'completed') {
             return back()->with('error', 'Cannot delete a completed order. Please mark as returned instead.');
         }

        try {
            DB::transaction(function () use ($order) {

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
            \Log::error("Error deleting order #{$order->id}: " . $e->getMessage());

            return back()->with('error', 'Something went wrong while deleting the order.');
        }
    }
}
