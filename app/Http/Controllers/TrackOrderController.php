<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index(Request $request)
    {
        $order = null;

        // If form is submitted OR query params are present (for GET links)
        if ($request->isMethod('post') || ($request->has('invoice_no') && $request->has('email'))) {
            $request->validate([
                'invoice_no' => 'required|string',
                'email'      => 'required|email',
            ]);

            // Find order matching Invoice AND Email (Security check)
            $order = Order::with('items')
                ->where('invoice_no', $request->invoice_no)
                ->where(function($q) use ($request) {
                    $q->where('guest_email', $request->email) // Check Guest Email
                    ->orWhereHas('customer', function($c) use ($request) {
                        $c->where('email', $request->email); // Check Linked Customer Email
                    });
                })
                ->first();

            // Only redirect back with error if it was a POST request
            if (!$order && $request->isMethod('post')) {
                return back()->with('error', 'Order not found or email does not match.');
            }
        }

        return view('frontend.track', compact('order'));
    }
}
