<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Sales Report with Date Filter
     */
    public function sales(Request $request)
    {
        // Default to current month
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('user') // Cashier
            ->latest()
            ->get();

        // Calculations
        $totalSales = $orders->sum('total');
        $totalDiscount = $orders->sum('discount');
        $totalVAT = $orders->sum('vat_amount');
        $netSales = $orders->sum('subtotal'); // Excl VAT

        return view('admin.reports.sales', compact('orders', 'totalSales', 'totalDiscount', 'totalVAT', 'netSales', 'startDate', 'endDate'));
    }

    /**
     * Inventory Valuation Report (Assets)
     */
    public function inventory()
    {
        // 1. Simple Products
        $simpleProducts = Product::where('type', 'simple')->get();

        $simpleTotal = $simpleProducts->sum(function ($p) {
            return $p->stock_quantity * $p->cost_price;
        });

        // 2. Variable Products (Variants)
        $variants = ProductVariant::with('product')->get();

        $variantTotal = $variants->sum(function ($v) {
            return $v->stock_quantity * $v->cost_price;
        });

        $grandTotalValue = $simpleTotal + $variantTotal;
        $totalItems = $simpleProducts->sum('stock_quantity') + $variants->sum('stock_quantity');

        return view('admin.reports.inventory', compact('simpleProducts', 'variants', 'grandTotalValue', 'totalItems'));
    }

    public function vat(Request $request)
    {
        // Default to Current Quarter
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->firstOfQuarter();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1. OUTPUT VAT (Sales)
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])->get();
        
        $grossSalesTotal = $sales->sum('total');
        $grossOutputVat = $sales->sum('vat_amount');

        // 2. CREDIT NOTES (Returns) --- THIS IS THE NEW PART ---
        $returns = ReturnOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        
        $totalRefunds = $returns->sum('total_refund');
        
        // Back-calculate the VAT from the refunded amount (assuming inclusive VAT)
        $taxRate = 0.05;
        $vatOnReturns = $returns->sum(function($return) use ($taxRate) {
            $net = $return->total_refund / (1 + $taxRate);
            return $return->total_refund - $net;
        });

        // 3. NET OUTPUT VAT (Sales minus Returns)
        $finalOutputVat = $grossOutputVat - $vatOnReturns;

        // 4. INPUT VAT (Purchases)
        $purchases = \App\Models\PurchaseOrder::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'received')
            ->get();

        $purchasesTotal = $purchases->sum('total_cost');
        $inputVat = $purchases->sum('tax_amount');
        $purchasesNet = $purchasesTotal - $inputVat;

        // 5. FINAL CALCULATION
        $netVatPayable = $finalOutputVat - $inputVat;

        return view('admin.reports.vat', compact(
            'startDate', 'endDate', 
            'grossSalesTotal', 'grossOutputVat',
            'returns', 'totalRefunds', 'vatOnReturns',
            'finalOutputVat',
            'purchases', 'purchasesTotal', 'purchasesNet', 'inputVat',
            'netVatPayable','sales','purchases'
        ));
    }
}
