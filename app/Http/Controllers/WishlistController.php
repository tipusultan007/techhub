<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// --------------------------------------------------------

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['status' => 'guest', 'message' => 'Please login first.']);
        }

        $customer = Auth::guard('customer')->user();
        $productId = $request->product_id;

        $exists = Wishlist::where('customer_id', $customer->id)->where('product_id', $productId)->first();

        if ($exists) {
            $exists->delete();
            $status = 'removed';
            $msg = 'Removed from Wishlist';
        } else {
            Wishlist::create(['customer_id' => $customer->id, 'product_id' => $productId]);
            $status = 'added';
            $msg = 'Added to Wishlist';
        }

        // Get updated count
        $newCount = Wishlist::where('customer_id', $customer->id)->count();

        return response()->json([
            'status' => $status,
            'message' => $msg,
            'count' => $newCount // <--- Return the new count
        ]);
    }

    public function index()
    {
        $wishlistItems = Wishlist::where('customer_id', Auth::guard('customer')->id())
            ->with('product.media') // Ensure Product model has media relation
            ->get();

        return view('frontend.customer.wishlist', compact('wishlistItems'));
    }

    public function destroy($id)
    {
        Wishlist::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->id())
            ->delete();

        return back()->with('success', 'Item removed from wishlist.');
    }
}
