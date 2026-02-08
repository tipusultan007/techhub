<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Financial Stats
        $dailySales = Order::whereDate('created_at', $today)->sum('total');
        $monthlySales = Order::whereDate('created_at', '>=', $startOfMonth)->sum('total');
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();

        // 2. Revenue Trend (Last 30 Days)
        $revenueData = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartRevenueLabels = $revenueData->pluck('date');
        $chartRevenueTotals = $revenueData->pluck('total');

        // 3. Channel Distribution
        $channelData = Order::selectRaw('channel, COUNT(*) as count')
            ->groupBy('channel')
            ->get()
            ->pluck('count', 'channel');
        
        $chartChannelLabels = ['Online', 'POS'];
        $chartChannelData = [
            $channelData->get('online', 0),
            $channelData->get('pos', 0)
        ];

        // 4. Sales by Category
        $categorySales = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', \Illuminate\Support\Facades\DB::raw('SUM(order_items.subtotal) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();
        
        $chartCategoryLabels = $categorySales->pluck('name');
        $chartCategoryTotals = $categorySales->pluck('total');

        // 5. Low Stock Alerts
        $lowStockSimple = Product::where('type', 'simple')
            ->whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->get();

        $lowStockVariants = ProductVariant::with('product')
            ->whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->get();

        $lowStockCount = $lowStockSimple->count() + $lowStockVariants->count();

        // 6. Pending Quotations
        $pendingQuotationsCount = \App\Models\Quotation::where('status', 'submitted')->count();

        // 7. Top Selling SKU (Last 7 Days)
        $topSellingItem = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->select(
                \Illuminate\Support\Facades\DB::raw('COALESCE(product_variants.sku, products.sku) as sku'),
                \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_qty')
            )
            ->where('order_items.created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('sku')
            ->orderByDesc('total_qty')
            ->first();
        
        $topSellingSku = $topSellingItem ? $topSellingItem->sku : 'N/A';

        // 8. Recent Orders
        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'dailySales', 
            'monthlySales', 
            'totalOrders', 
            'totalCustomers',
            'lowStockCount',
            'pendingQuotationsCount',
            'topSellingSku',
            'lowStockSimple',
            'lowStockVariants',
            'recentOrders',
            'chartRevenueLabels',
            'chartRevenueTotals',
            'chartChannelLabels',
            'chartChannelData',
            'chartCategoryLabels',
            'chartCategoryTotals'
        ));
    }
}