<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\ReturnItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    /**
 * Display a listing of all processed returns.
 */
public function index()
{
    $returns = ReturnOrder::with(['originalOrder', 'user']) // Eager load relationships
        ->latest()
        ->paginate(15);

    return view('admin.returns.index', compact('returns'));
}

    // Show the initial page to search for an invoice
    public function create() {
        return view('admin.returns.create');
    }

    // Find the order and show items available for return
    public function findOrder(Request $request) {
        $order = Order::with('items.product')->where('invoice_no', $request->invoice_no)->first();

        if(!$order) {
            return back()->with('error', 'Invoice not found.');
        }

        // You might want to add logic here to check if items are already returned
        return view('admin.returns.process', compact('order'));
    }

    // Store the actual return and update stock
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            $totalRefund = 0;

            // 1. Create Return Header
            $return = ReturnOrder::create([
                'order_id' => $order->id,
                'credit_note_no' => 'CRN-' . time(), // Simple generator
                'total_refund' => 0, // Calculated below
                'reason' => $request->reason,
                'user_id' => Auth::id()
            ]);

            // 2. Process each returned item
            foreach ($request->items as $orderItemId => $data) {
                $originalItem = $order->items()->find($orderItemId);
                
                // Security: ensure not returning more than purchased
                if($data['qty'] > $originalItem->quantity) {
                    throw new \Exception('Cannot return more items than were purchased.');
                }
                
                $subtotal = $data['qty'] * $originalItem->unit_price;
                $totalRefund += $subtotal;

                // Create Return Item record
                ReturnItem::create([
                    'return_id' => $return->id,
                    'order_item_id' => $originalItem->id,
                    'product_id' => $originalItem->product_id,
                    'product_variant_id' => $originalItem->product_variant_id,
                    'quantity' => $data['qty'],
                    'unit_price' => $originalItem->unit_price,
                    'subtotal' => $subtotal,
                    'restock_status' => $data['status']
                ]);

                // 3. Update Inventory & Serials
                if ($data['status'] === 'restockable') {
                    // Add stock back
                    if ($originalItem->product_variant_id) {
                        ProductVariant::find($originalItem->product_variant_id)->increment('stock_quantity', $data['qty']);
                    } else {
                        Product::find($originalItem->product_id)->increment('stock_quantity', $data['qty']);
                    }
                }
                
                // If it was a serialized item, update its status
                if($originalItem->serial_numbers) {
                    ProductSerial::where('serial_number', $originalItem->serial_numbers)
                        ->update(['status' => $data['status'] === 'restockable' ? 'available' : 'defective']);
                }
            }

            // 4. Update Return total and Order status
            $return->update(['total_refund' => $totalRefund]);
            // You can add logic here to check if all items are returned to mark order as 'fully_returned'

            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Return processed successfully. Credit Note #' . $return->credit_note_no);

        } catch(\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
 * Display the specified return (Credit Note).
 */
public function show(ReturnOrder $return)
{
    $return->load(['originalOrder.customer', 'items.product', 'items.variant']);
    return view('admin.returns.show', compact('return'));
}
}