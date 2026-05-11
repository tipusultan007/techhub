<?php

namespace App\Http\Controllers;

use App\Models\IncompleteOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IncompleteOrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view orders', only: ['index', 'show']),
            new Middleware('permission:manage orders', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = IncompleteOrder::with(['user', 'customer']);

        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'LIKE', '%'.$request->invoice_no.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.incomplete_orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = IncompleteOrder::findOrFail($id);
        return view('admin.incomplete_orders.show', compact('order'));
    }

    public function destroy($id)
    {
        $order = IncompleteOrder::findOrFail($id);
        $order->delete();

        return back()->with('success', 'Incomplete order deleted successfully.');
    }
}
