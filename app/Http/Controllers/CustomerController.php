<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\NewCustomerNotification;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Traits\LogsActivity;

class CustomerController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Simple Search Logic
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('trn_number', 'LIKE', "%{$search}%");
        }

        $customers = $query->latest()->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20', // Unique check optional depending on business logic
            'email' => 'nullable|email|max:255',
            'trn_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($request->all());

        // Notify Admin about new manual customer entry
        User::role('Admin')->get()->each->notify(new NewCustomerNotification($customer));

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    /**
     * Display the specified customer (Profile + Order History).
     */
    public function show(Customer $customer)
    {
        // Load recent orders for this customer
        $customer->load(['orders' => function($q) {
            $q->latest()->limit(10);
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'trn_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            return back()->with('error', 'Only Super Admin can delete customers.');
        }

        try {
            \DB::transaction(function () use ($customer) {
                // 1. Delete Orders and related data (with restocking)
                foreach ($customer->orders as $order) {
                    // Restock Inventory
                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                            if ($variant) {
                                $variant->increment('stock_quantity', $item->quantity);
                            }
                        } else {
                            $product = \App\Models\Product::find($item->product_id);
                            if ($product) {
                                $product->increment('stock_quantity', $item->quantity);
                            }
                        }
                    }

                    // Delete return items and returns (with destocking)
                    $returns = \App\Models\ReturnOrder::with('items')->where('order_id', $order->id)->get();
                    foreach ($returns as $return) {
                        foreach ($return->items as $rItem) {
                            if ($rItem->restock_status === 'restockable') {
                                if ($rItem->product_variant_id) {
                                    $variant = \App\Models\ProductVariant::find($rItem->product_variant_id);
                                    if ($variant) $variant->decrement('stock_quantity', $rItem->quantity);
                                } else {
                                    $product = \App\Models\Product::find($rItem->product_id);
                                    if ($product) $product->decrement('stock_quantity', $rItem->quantity);
                                }
                            }
                        }
                        $return->items()->delete();
                        $return->delete();
                    }
                    
                    // Delete order items and history
                    $order->items()->delete();
                    $order->history()->delete();
                    $order->delete();
                }

                // 2. Delete Quotations and related data
                foreach ($customer->quotations as $quotation) {
                    // Delete delivery challan items and challans
                    foreach ($quotation->deliveryChallans as $challan) {
                        $challan->items()->delete();
                        $challan->delete();
                    }
                    
                    // Delete quotation items
                    $quotation->items()->delete();
                    $quotation->delete();
                }

                // 3. Delete leftover Delivery Challans (manual ones not linked to quotations)
                foreach ($customer->deliveryChallans as $challan) {
                    $challan->items()->delete();
                    $challan->delete();
                }

                // 4. Delete Addresses and Wishlist
                $customer->addresses()->delete();
                \App\Models\Wishlist::where('customer_id', $customer->id)->delete();

                // 5. Delete Customer
                $customerName = $customer->name;
                $customer->delete();

                $this->logActivity('Customer', 'Delete', "Deleted Customer: {$customerName} and all related records.", [
                    'customer_name' => $customerName
                ]);
            });

            return redirect()->route('customers.index')->with('success', 'Customer and all related records deleted successfully.');

        } catch (\Exception $e) {
            \Log::error("Error deleting customer #{$customer->id}: " . $e->getMessage());
            return back()->with('error', 'Something went wrong while deleting the customer.');
        }
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();

        // Fetch stats (using customer's email or phone if user_id is not set on older orders)
        // Ideally, orders table should have 'customer_id' linked to 'customers' table.
        // Assuming Order model has 'customer_id' relationship setup as discussed previously.

        $orders = Order::where('customer_id', $customer->id)->latest()->take(5)->get();
        $totalOrders = Order::where('customer_id', $customer->id)->count();
        $pendingOrders = Order::where('customer_id', $customer->id)->whereIn('status', ['pending', 'processing'])->count();

        // Placeholder for wallet logic (if you implement wallet later)
        $walletBalance = 0;

        return view('frontend.customer.dashboard', compact('customer', 'orders', 'totalOrders', 'pendingOrders', 'walletBalance'));
    }

    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        $orders = Order::where('customer_id', $customer->id)->latest()->paginate(10);
        return view('frontend.customer.orders', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        // Ensure order belongs to logged in customer
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        return view('frontend.customer.order-details', compact('order'));
    }

    public function downloadInvoice(Order $order)
    {
        // Ensure order belongs to logged in customer
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('frontend.customer.invoice-pdf', compact('order'));
        return $pdf->download('invoice-' . $order->invoice_no . '.pdf');
    }
}
