<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\NewCustomerNotification;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
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
        // Prevent deletion if customer has orders (Data Integrity)
        if ($customer->orders()->exists()) {
            return back()->with('error', 'Cannot delete customer. They have existing sales records.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
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
