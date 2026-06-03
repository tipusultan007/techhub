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

        $coupon = Session::get('coupon');
        $totals = $this->getCartTotals($cart, $coupon);

        return view('frontend.checkout', [
            'cart' => $cart,
            'subtotal' => $totals['subtotal'],
            'vat' => $totals['vat'],
            'shipping' => $totals['shipping'],
            'total' => $totals['total'],
            'discount' => $totals['discount'],
            'coupon' => $coupon
        ]);
    }

    private function getCartTotals($cart, $coupon = null)
    {
        $netSubtotal = 0;
        $vatAmount = 0;
        $grossTotal = 0;
        $hasService = false;

        foreach ($cart as $item) {
            $price = $item['price'];
            $qty = $item['quantity'];
            $rate = $item['tax_rate'] ?? 5;
            $method = $item['tax_method'] ?? 'inclusive';

            $itemGrossBase = $price * $qty;

            if ($method === 'exclusive') {
                $itemVat = $itemGrossBase * ($rate / 100);
                $itemNet = $itemGrossBase;
                $itemGross = $itemGrossBase + $itemVat;
            } else {
                $itemVat = $itemGrossBase - ($itemGrossBase / (1 + ($rate / 100)));
                $itemNet = $itemGrossBase - $itemVat;
                $itemGross = $itemGrossBase;
            }

            $netSubtotal += $itemNet;
            $vatAmount += $itemVat;
            $grossTotal += $itemGross;

            // Check if product is a service (Installation/Project)
            $product = \App\Models\Product::find($item['product_id']);
            if ($product && $product->type === 'service') {
                $hasService = true;
            }
        }

        $discount = 0;
        if ($coupon) {
            $sumOfPrices = 0;
            foreach($cart as $item) $sumOfPrices += $item['price'] * $item['quantity'];

            if ($coupon['type'] === 'percentage') {
                $discount = $sumOfPrices * ($coupon['value'] / 100);
            } else {
                $discount = $coupon['value'];
            }
        }

        // Delivery Charge Logic: Free if total >= 250 or has a service
        $shippingCharge = ($grossTotal >= 250 || $hasService) ? 0 : 15;

        // VAT on Shipping (5% inclusive)
        $shippingVat = ($shippingCharge > 0) ? ($shippingCharge - ($shippingCharge / 1.05)) : 0;
        
        // Total VAT before discount
        $totalVatBeforeDiscount = $vatAmount + $shippingVat;
        $totalGrossBeforeDiscount = $grossTotal + $shippingCharge;

        // Final Total (Gross)
        $finalTotal = $totalGrossBeforeDiscount - $discount;

        // Proportional VAT Adjustment (if discount applied)
        $finalVat = ($totalGrossBeforeDiscount > 0) ? ($totalVatBeforeDiscount * ($finalTotal / $totalGrossBeforeDiscount)) : 0;
        
        // Final Net Subtotal (for the order header)
        $finalNetTotal = $finalTotal - $finalVat;

        return [
            // The formula used elsewhere: subtotal + vat + shipping - discount = total
            // To satisfy this: subtotal = total - vat - shipping + discount
            'subtotal' => round($finalTotal - $finalVat - $shippingCharge + $discount, 2),
            'vat' => round($finalVat, 2),
            'shipping' => $shippingCharge,
            'total' => round($finalTotal, 2),
            'discount' => round($discount, 2)
        ];
    }

    public function store(Request $request)
    {
        // --- ANTI-BOT MEASURES ---
        // 1. Honeypot Check: if this hidden field is filled, it's a bot
        if (!empty($request->input('_website_url'))) {
            \Illuminate\Support\Facades\Log::warning('Bot blocked by honeypot at checkout.', ['ip' => $request->ip()]);
            return redirect()->route('cart.index')->with('error', 'Invalid request.');
        }

        // 2. Block Known Bot Email Domains
        $email = $request->input('email');
        if ($email) {
            $emailDomain = strtolower(substr(strrchr($email, "@"), 1));
            $blockedDomains = [
                'storebotmail.joonix.net',
                // Can add more disposable/bot domains here
            ];
            
            if (in_array($emailDomain, $blockedDomains)) {
                \Illuminate\Support\Facades\Log::warning('Bot blocked by email domain at checkout.', ['email' => $email, 'ip' => $request->ip()]);
                return redirect()->route('cart.index')->with('error', 'Orders from this email domain are not permitted.');
            }
        }
        // -------------------------

        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'payment_method' => 'required|in:rakbank' // Only RAKBANK (Card) accepted
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) return redirect()->route('cart.index');

        $coupon = Session::get('coupon');
        $totals = $this->getCartTotals($cart, $coupon);

        // Capture IP Addresses
        $customerIp = $request->ip();
        $visitorIp = $request->header('X-Forwarded-For') ?? $request->ip();

        // Create Incomplete Order
        $incompleteOrder = \App\Models\IncompleteOrder::create([
            'invoice_no'     => \App\Models\Order::generateInvoiceNumber(),
            'user_id'        => auth()->id(),
            'customer_data'  => $request->only(['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'area']),
            'cart_data'      => $cart,
            'totals_data'    => $totals,
            'coupon_data'    => $coupon,
            'payment_method' => $request->payment_method,
            'customer_ip'    => $customerIp,
            'visitor_ip'     => $visitorIp,
            'status'         => 'pending',
        ]);

        if ($request->payment_method === 'rakbank') {
            return redirect()->route('rakbank.pay', $incompleteOrder->id);
        }

        // This part is currently not reachable as per user's feedback (No COD), 
        // but kept as fallback or for future use with OrderService.
        return redirect()->route('checkout.index')->with('error', 'Invalid payment method.');
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
