<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory transactions with filters.
     */
    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'variant', 'user', 'reference'])
            ->latest();

        // Filter by Product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by Type (in/out)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Description (Search)
        if ($request->filled('reference')) {
            $query->where('description', 'LIKE', '%' . $request->reference . '%');
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('admin.inventory.transactions', compact('transactions', 'products'));
    }
}
