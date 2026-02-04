<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\ReturnItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductSerial;
use App\Models\InventoryTransaction;
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

    public function searchOrders(Request $request)
    {
        $term = $request->term;
        $orders = Order::select('id', 'invoice_no', 'customer_name', 'created_at', 'total')
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($term) {
                $q->where('invoice_no', 'LIKE', "%$term%")
                  ->orWhere('customer_name', 'LIKE', "%$term%");
            })
            ->latest()
            ->take(20)
            ->get();

        $results = $orders->map(function($order) {
            return [
                'id' => $order->invoice_no, // We use invoice_no as ID because the existing findOrder expects it
                'text' => $order->invoice_no . ' (' . ($order->customer_name ?? 'Guest') . ')',
                'customer' => $order->customer_name ?? 'Guest',
                'date' => $order->created_at->format('d M Y'),
                'total' => number_format($order->total, 2)
            ];
        });

        return response()->json($results);
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
            'items' => 'required|array',
            'items.*.qty' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            // $totalRefund = 0; // This line is removed as it's re-declared below

            // 3. Create Return records
            $return = ReturnOrder::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'total_refund' => 0, // Will update below
                'credit_note_no' => 'CN-' . time(), // Temporary logic, consider a better generator
            ]);

            $totalRefund = 0;
            $returnedAny = false;
            foreach ($request->items as $orderItemId => $data) {
                if (($data['qty'] ?? 0) <= 0) continue;
                $returnedAny = true;

                $originalItem = $order->items()->findOrFail($orderItemId);
                
                // Security: ensure not returning more than purchased
                if ($data['qty'] > $originalItem->quantity) {
                    throw new \Exception("Cannot return more than purchased for product: " . $originalItem->product_name);
                }

                $itemSubtotal = $originalItem->unit_price * $data['qty'];
                $totalRefund += $itemSubtotal;

                ReturnItem::create([
                    'return_id' => $return->id,
                    'order_item_id' => $originalItem->id,
                    'product_id' => $originalItem->product_id,
                    'product_variant_id' => $originalItem->product_variant_id,
                    'quantity' => $data['qty'],
                    'unit_price' => $originalItem->unit_price,
                    'subtotal' => $itemSubtotal,
                    'restock_status' => $data['status'],
                ]);

                // 3. Update Inventory (if restockable)
                if ($data['status'] === 'restockable') {
                    // Add stock back
                    if ($originalItem->product_variant_id) {
                        ProductVariant::find($originalItem->product_variant_id)->increment('stock_quantity', $data['qty']);
                    } else {
                        Product::find($originalItem->product_id)->increment('stock_quantity', $data['qty']);
                    }
                    
                    // Log transaction
                    InventoryTransaction::create([
                        'product_id' => $originalItem->product_id,
                        'type' => 'in',
                        'quantity' => $data['qty'],
                        'description' => 'Return: ' . $return->credit_note_no,
                        'reference_id' => $return->id,
                        'reference_type' => get_class($return),
                    ]);
                }
                
                // If it was a serialized item, update its status
                if($originalItem->serial_numbers) {
                    ProductSerial::where('serial_number', $originalItem->serial_numbers)
                        ->update(['status' => $data['status'] === 'restockable' ? 'available' : 'defective']);
                }
            }

            if (!$returnedAny) {
                throw new \Exception('Please select at least one item to return by entering a quantity.');
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