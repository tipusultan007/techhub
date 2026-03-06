<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\NewCustomerNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Support\Facades\Notification;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $coupon = Session::get('coupon');

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        // Calculate Totals
        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];

        // Validate Coupon
        if ($coupon && $subtotal < $coupon['min_amount']) {
            Session::forget('coupon');
            $coupon = null;
        }

        $discount = 0;
        if ($coupon) {
            $discount = ($coupon['type'] === 'percentage') ? ($subtotal * ($coupon['value'] / 100)) : $coupon['value'];
        }

        $taxableAmount = $subtotal - $discount;
        $vat = $taxableAmount * 0.05;
        $total = $taxableAmount + $vat;

        return view('frontend.checkout', compact('cart', 'subtotal', 'vat', 'total', 'discount', 'coupon'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'payment_method' => 'required|in:cod,rakbank'
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];

        $coupon = Session::get('coupon');
        $discount = 0;
        if ($coupon && $subtotal >= $coupon['min_amount']) {
            $discount = ($coupon['type'] === 'percentage') ? ($subtotal * ($coupon['value'] / 100)) : $coupon['value'];
        }

        $taxableAmount = $subtotal - $discount;
        $vat_amount = $taxableAmount * 0.05;
        $total = $taxableAmount + $vat_amount;

        $order = DB::transaction(function () use ($request, $cart, $subtotal, $vat_amount, $total, $discount, $coupon) {

            // 1. CRM LOGIC
            $customer = Customer::where('phone', $request->phone)
                ->orWhere('email', $request->email)
                ->first();

            $fullName = $request->first_name . ' ' . $request->last_name;

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $fullName,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address . ', ' . $request->city,
                ]);

                // Notify Admin about new customer registration
                User::role(['Admin', 'Super Admin'])->get()->each->notify(new NewCustomerNotification($customer));
            } else {
                $customer->update([
                    'address' => $request->address . ', ' . $request->city
                ]);
            }

            // 2. CREATE ORDER
            $order = Order::create([
                'invoice_no'       => Order::generateInvoiceNumber(),
                'channel'          => 'online',
                'user_id'          => auth()->id(),
                'customer_id'      => $customer->id,
                'customer_name'    => $fullName,
                'guest_email'      => $request->email,
                'guest_phone'      => $request->phone,
                'shipping_address' => $request->address,
                'shipping_city'    => $request->city,
                'shipping_area'    => $request->area,
                'subtotal'         => $subtotal,
                'vat_amount'       => $vat_amount,
                'discount'         => $discount,
                'total'            => $total,
                'payment_method'   => $request->payment_method,
                'status'           => 'pending',
                'notes'            => $coupon ? 'Coupon: ' . $coupon['code'] : null,
            ]);

            // Notify Admin about new order
            User::role(['Admin', 'Super Admin'])->get()->each->notify(new NewOrderNotification($order));

            // Notify Customer about new order
            if ($request->email) {
                Notification::route('mail', $request->email)
                    ->notify(new OrderConfirmationNotification($order));
            }

            // Increment Coupon Usage
            if ($coupon) {
                \App\Models\Coupon::where('id', $coupon['id'])->increment('uses');
            }

            // 3. CREATE ORDER ITEMS
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name'       => $item['name'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['price'],
                    'subtotal'           => $item['price'] * $item['quantity'],
                ]);

                // Deduct Stock
                if (isset($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    $variant->decrement('stock_quantity', $item['quantity']);
                    
                    if ($variant->stock_quantity <= $variant->alert_quantity) {
                        User::role(['Admin', 'Super Admin'])->get()->each->notify(new LowStockNotification($variant->product, $variant->stock_quantity));
                    }
                } else {
                    $product = Product::find($item['product_id']);
                    $product->decrement('stock_quantity', $item['quantity']);

                    if ($product->stock_quantity <= $product->alert_quantity) {
                        User::role(['Admin', 'Super Admin'])->get()->each->notify(new LowStockNotification($product, $product->stock_quantity));
                    }
                }
            }

            if ($request->payment_method === 'cod') {
                Session::forget('cart');
                Session::forget('coupon');
            }

            return $order;
        });

        session()->put('placed_order_id', $order->id);

        if ($request->payment_method === 'rakbank') {
            return redirect()->route('rakbank.pay', $order->id);
        }

        return redirect()->route('checkout.success', $order->id);

    }

    public function success($orderId)
    {
        // 1. Find the order
        $order = \App\Models\Order::with('items.product.media')->findOrFail($orderId);
        //$order->load('items.product.media');

        // 2. Security Check (Authorization)
        // We allow access ONLY if:
        // A. The user is logged in AND owns the order
        // B. OR, the user is a guest BUT has the 'placed_order_id' in their session

        $isOwner = auth()->check() && $order->user_id === auth()->id();
        $isGuestOwner = session('placed_order_id') == $order->id;

        if (! $isOwner && ! $isGuestOwner) {
            // If neither, they are trying to peek at someone else's order
            abort(403, 'Unauthorized action.');
        }

        return view('frontend.success', compact('order'));
    }

    public function track(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required',
            'email' => 'required|email',
        ]);

        $order = \App\Models\Order::where('invoice_no', $request->invoice_no)
            ->where('guest_email', $request->email)
            ->first();

        if (!$order) {
            return back()->with('error', 'Order not found.');
        }

        // If found, show the success view again
        return view('frontend.success', compact('order'));
    }
}
