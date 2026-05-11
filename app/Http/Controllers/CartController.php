<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $coupon = Session::get('coupon');

        $crossSellProducts = \App\Models\Product::published()->physical()->inStock()->inRandomOrder(now()->timestamp)->take(4)->with('media')->get();
        $totals = $this->getCartTotals($cart, $coupon);

        return view('frontend.cart', [
            'cart' => $cart,
            'subtotal' => $totals['subtotal'], 
            'vat' => $totals['vat'],
            'shipping' => $totals['shipping'],
            'total' => $totals['total'], 
            'crossSellProducts' => $crossSellProducts,
            'discount' => $totals['discount'],
            'coupon' => $coupon,
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
            // Discount calculated on the sum of original prices
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

        return [
            'subtotal' => $netSubtotal,
            'vat' => $vatAmount,
            'shipping' => $shippingCharge,
            'total' => $grossTotal - $discount + $shippingCharge,
            'discount' => $discount
        ];
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        $product = Product::physical()->find($request->product_id, ['*']);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not available.'
            ], 404);
        }

        // Validation: Variable product must have variant selected
        if ($product->type === 'variable' && !$request->variant_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select an option.'
            ], 422);
        }

        // Logic to resolve Price and Stock
        $price = $product->active_price;
        $name = $product->name;
        $options = [];

        if ($request->variant_id) {
            $variant = ProductVariant::find($request->variant_id, ['*']);

            // Check Stock
            if ($variant->stock_quantity < $request->quantity) {
                return response()->json(['status' => 'error', 'message' => 'Out of stock.'], 400);
            }

            $price = $variant->active_price;
            $name = $product->name . ' - ' . $variant->variant_name;
            $options['variant_id'] = $variant->id;
            $options['variant_name'] = $variant->variant_name;
        } else {
            // Check Simple Stock
            if ($product->stock_quantity < $request->quantity) {
                return response()->json(['status' => 'error', 'message' => 'Out of stock.'], 400);
            }
        }

        // --- CART LOGIC (Using Session for simplicity) ---
        $cart = Session::get('cart', []);

        // Create unique key for item (Product ID + Variant ID)
        $cartKey = $product->id . ($request->variant_id ? '_' . $request->variant_id : '');

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'name' => $name,
                'price' => $price,
                'quantity' => $request->quantity,
                'image' => $product->getFirstMediaUrl('product_images', 'thumb'),
                'product_id' => $product->id,
                'variant_id' => $request->variant_id,
                'tax_method' => $product->tax_method ?? 'inclusive',
                'tax_rate' => $product->tax_rate ?? 5,
            ];
        }

        Session::put('cart', $cart);
        $totalCount = count($cart);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Added to cart successfully!',
                'cartCount' => count(Session::get('cart')),
            ]);
        }

        return back()->with('success', 'Added to cart!');
    }

    public function update(Request $request)
    {
        $cart = Session::get('cart', []);
        $key = $request->key;

        if(isset($cart[$key])) {
            $item = $cart[$key];
            $product = \App\Models\Product::find($item['product_id']);
            $newQty = (int) $request->quantity;

            // Stock Check
            if (isset($item['variant_id']) && $item['variant_id']) {
                $variant = \App\Models\ProductVariant::find($item['variant_id']);
                if ($variant && $variant->stock_quantity < $newQty) {
                    return response()->json(['status' => 'error', 'message' => 'Not enough stock available.'], 400);
                }
            } else {
                if ($product && $product->stock_quantity < $newQty) {
                    return response()->json(['status' => 'error', 'message' => 'Not enough stock available.'], 400);
                }
            }

            $cart[$key]['quantity'] = $newQty;
            Session::put('cart', $cart);

            // Re-validate Coupon
            $sumOfPrices = 0;
            foreach($cart as $cItem) $sumOfPrices += $cItem['price'] * $cItem['quantity'];

            $coupon = Session::get('coupon');
            if ($coupon && $sumOfPrices < $coupon['min_amount']) {
                Session::forget('coupon');
                $coupon = null;
            }

            $totals = $this->getCartTotals($cart, $coupon);

            return response()->json([
                'status' => 'success',
                'subtotal' => number_format($totals['subtotal'], 2),
                'discount' => number_format($totals['discount'], 2),
                'vat' => number_format($totals['vat'], 2),
                'shipping' => number_format($totals['shipping'], 2),
                'total' => number_format($totals['total'], 2),
                'itemTotal' => number_format($cart[$key]['price'] * $request->quantity, 2),
                'couponRemoved' => !$coupon && Session::has('coupon_temp_removed')
            ]);
        }

        return response()->json(['status' => 'error'], 404);
    }

//    public function remove(Request $request)
//    {
//        $cart = Session::get('cart', []);
//        if(isset($cart[$request->key])) {
//            unset($cart[$request->key]);
//            Session::put('cart', $cart);
//        }
//        return back()->with('success', 'Item removed from cart');
//    }

    public function miniCart()
    {
        $cart = session()->get('cart', []);
        $totals = $this->getCartTotals($cart);

        // Return a partial view
        return view('frontend.partials.mini-cart', [
            'cart' => $cart,
            'subtotal' => $totals['subtotal'], 
            'vat' => $totals['vat'],
            'shipping' => $totals['shipping'],
            'total' => $totals['total'] 
        ]);
    }

    public function remove(Request $request)
    {
        $cart = Session::get('cart', []);

        if(isset($cart[$request->key])) {
            unset($cart[$request->key]);
            Session::put('cart', $cart);
        }

        // AJAX Response for Sidebar/JS
        if ($request->ajax()) {
            $subtotal = 0;
            foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];

            return response()->json([
                'status' => 'success',
                'message' => 'Item removed successfully.',
                'cartCount' => count($cart),
                'subtotal' => number_format($subtotal, 2)
            ]);
        }

        // Fallback for non-AJAX requests
        return back()->with('success', 'Item removed from cart');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = \App\Models\Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        // Check Uses
        if ($coupon->max_uses && $coupon->uses >= $coupon->max_uses) {
            return back()->with('error', 'This coupon has reached its usage limit.');
        }

        // Check Min Amount
        $cart = Session::get('cart', []);
        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];

        if ($subtotal < $coupon->min_amount) {
            return back()->with('error', 'Your subtotal must be at least $' . number_format($coupon->min_amount, 2) . ' to use this coupon.');
        }

        Session::put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'min_amount' => $coupon->min_amount
        ]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        Session::forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }
}
