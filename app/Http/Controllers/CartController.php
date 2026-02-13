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

        // Calculate Totals
        $subtotal = 0;
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Validate applied coupon
        if ($coupon && $subtotal < $coupon['min_amount']) {
            Session::forget('coupon');
            $coupon = null;
        }

        $discount = 0;
        if ($coupon) {
            if ($coupon['type'] === 'percentage') {
                $discount = $subtotal * ($coupon['value'] / 100);
            } else {
                $discount = $coupon['value'];
            }
        }

        $taxableAmount = $subtotal - $discount;
        $vat = $taxableAmount * 0.05;
        $total = $taxableAmount + $vat;

        $crossSellProducts = \App\Models\Product::published()->physical()->inStock()->inRandomOrder(now()->timestamp)->take(4)->with('media')->get();

        return view('frontend.cart', compact('cart', 'subtotal', 'vat', 'total', 'crossSellProducts', 'discount', 'coupon'));
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

        // --- CART LOGIC (Using Session for simplicity, adjust if using DB/Package) ---
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
                'variant_id' => $request->variant_id
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
            // Check Stock Logic here if needed
            $cart[$key]['quantity'] = $request->quantity;
            Session::put('cart', $cart);

            // Recalculate for AJAX response
            $subtotal = 0;
            foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];

            // Re-validate Coupon
            $coupon = Session::get('coupon');
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

            return response()->json([
                'status' => 'success',
                'subtotal' => number_format($subtotal, 2),
                'discount' => number_format($discount, 2),
                'vat' => number_format($vat, 2),
                'total' => number_format($taxableAmount + $vat, 2),
                'itemTotal' => number_format($cart[$key]['price'] * $request->quantity, 2),
                'couponRemoved' => !$coupon && Session::has('coupon_temp_removed') // Internal flag if we want
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
        $subtotal = 0;
        foreach($cart as $item) $subtotal += $item['price'] * $item['quantity'];

        $vat = $subtotal * 0.05;
        $total = $subtotal + $vat;

        // Return a partial view
        return view('frontend.partials.mini-cart', compact('cart', 'subtotal', 'vat', 'total'));
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
