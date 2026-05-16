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

        // Chart Data: Group by Date
        $chartData = $orders->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('d M');
        })->map(function($day) {
            return $day->sum('total');
        });

        return view('admin.reports.sales', compact('orders', 'totalSales', 'totalDiscount', 'totalVAT', 'netSales', 'startDate', 'endDate', 'chartData'));
    }

    /**
     * Generate Sales Report PDF
     */
    public function salesPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->latest()
            ->get();

        $totalSales = $orders->sum('total');
        $totalDiscount = $orders->sum('discount');
        $totalVAT = $orders->sum('vat_amount');
        $netSales = $orders->sum('subtotal');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.sales_pdf', compact(
            'orders', 'totalSales', 'totalDiscount', 'totalVAT', 'netSales', 'startDate', 'endDate'
        ));

        return $pdf->download('Sales-Report-' . $startDate->format('d-M-Y') . '-to-' . $endDate->format('d-M-Y') . '.pdf');
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
        $data = $this->getVatReportData($request);
        return view('admin.reports.vat', $data);
    }

    public function vatPdf(Request $request)
    {
        $data = $this->getVatReportData($request);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.vat_pdf', $data);
        
        $filename = 'VAT-Return-' . $data['startDate']->format('d-M-Y') . '-to-' . $data['endDate']->format('d-M-Y') . '.pdf';
        return $pdf->download($filename);
    }

    private function getVatReportData(Request $request)
    {
        // Default to Current Quarter
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->firstOfQuarter();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1. OUTPUT VAT (Sales) - Grouped by Emirate for Box 1
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled')
                      ->get();
        
        $grossSalesTotal = $sales->sum('total');
        $grossOutputVat = $sales->sum('vat_amount');

        // Group by Emirate (City) for Box 1 UAE VAT Return
        $emirateSales = [];
        foreach ($sales as $sale) {
            $city = $sale->channel === 'online' ? ($sale->shipping_city ?: 'Dubai') : 'Dubai';
            $city = ucwords(strtolower(trim($city)));
            
            if (!isset($emirateSales[$city])) {
                $emirateSales[$city] = ['net' => 0, 'vat' => 0];
            }
            $emirateSales[$city]['net'] += ($sale->total - $sale->vat_amount);
            $emirateSales[$city]['vat'] += $sale->vat_amount;
        }

        // 2. CREDIT NOTES (Returns)
        $returns = ReturnOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalRefunds = $returns->sum('total_refund');
        
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

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'grossSalesTotal' => $grossSalesTotal,
            'grossOutputVat' => $grossOutputVat,
            'emirateSales' => $emirateSales,
            'returns' => $returns,
            'totalRefunds' => $totalRefunds,
            'vatOnReturns' => $vatOnReturns,
            'finalOutputVat' => $finalOutputVat,
            'purchases' => $purchases,
            'purchasesTotal' => $purchasesTotal,
            'purchasesNet' => $purchasesNet,
            'inputVat' => $inputVat,
            'netVatPayable' => $netVatPayable,
            'sales' => $sales,
            'settings' => $settings
        ];
    }

    /**
     * Purchase Report
     */
    public function purchases(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $purchases = \App\Models\PurchaseOrder::whereBetween('date', [$startDate, $endDate])
            ->with(['supplier'])
            ->latest()
            ->get();

        $totalPurchases = $purchases->sum('total_cost');
        $totalTax = $purchases->sum('tax_amount');
        $receivedCount = $purchases->where('status', 'received')->count();
        $pendingCount = $purchases->where('status', 'pending')->count();

        return view('admin.reports.purchases', compact('purchases', 'totalPurchases', 'totalTax', 'receivedCount', 'pendingCount', 'startDate', 'endDate'));
    }

    /**
     * Profit & Loss Report
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1. Revenue (Sales)
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('items')
            ->get();
        $totalRevenue = $sales->sum('total');

        // 2. Returns (Credit Notes)
        $returns = ReturnOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalReturns = $returns->sum('total_refund');

        // 3. COGS (Cost of Goods Sold)
        // Calculated based on sales items' original costs at time of sale if possible, 
        // or current product cost as fallback
        $cogs = 0;
        foreach($sales as $order) {
            foreach($order->items as $item) {
                // In a robust system, we'd store purchase_price at time of sale.
                // Assuming it's available or fetching from product
                $product = $item->product;
                $cost = $item->purchase_price ?? ($product ? $product->cost_price : 0);
                $cogs += ($cost * $item->quantity);
            }
        }

        // 4. Gross Profit
        $grossProfit = ($totalRevenue - $totalReturns) - $cogs;

        // 5. Operating Expenses
        $expenses = \App\Models\Expense::whereBetween('date', [$startDate, $endDate])->get();
        $totalExpenses = $expenses->sum('amount');

        // 6. Net Profit
        $netProfit = $grossProfit - $totalExpenses;

        return view('admin.reports.profit_loss', compact(
            'totalRevenue', 'totalReturns', 'cogs', 'grossProfit', 
            'totalExpenses', 'netProfit', 'startDate', 'endDate'
        ));
    }

    /**
     * Low Stock Alert Report
     */
    public function lowStock()
    {
        // 1. Simple Products: Stock <= Alert Quantity (and ignore Service types unless you want to track them?)
        // Usually Services have no stock, but let's stick to 'simple' for now.
        $lowStockSimple = Product::where('type', 'simple')
            ->whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->get();

        // 2. Variable Products: Variants Stock <= Variant's Alert Quantity (or 5 default)
        $lowStockVariants = ProductVariant::whereColumn('stock_quantity', '<=', 'alert_quantity')
            ->with(['product'])
            ->get();

        $totalAlerts = $lowStockSimple->count() + $lowStockVariants->count();

        return view('admin.reports.low_stock', compact('lowStockSimple', 'lowStockVariants', 'totalAlerts'));
    }

    /**
     * Expense Report
     */
    public function expenses(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $query = \App\Models\Expense::whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'user'])
            ->latest('date');

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        $expenses = $query->get();

        // Calculations
        $totalExpenses = $expenses->sum('amount');
        $expenseCount = $expenses->count();
        $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;

        // Group by category for summary
        $categorySummary = $expenses->groupBy('expense_category_id')->map(function ($group) {
            return [
                'name' => $group->first()->category->name ?? 'Unknown',
                'total' => $group->sum('amount'),
                'count' => $group->count()
            ];
        })->sortByDesc('total');

        // Chart Data: Group by Date
        $chartData = $expenses->groupBy(function ($date) {
            return Carbon::parse($date->date)->format('d M');
        })->map(function ($day) {
            return $day->sum('amount');
        });

        $categories = \App\Models\ExpenseCategory::all();

        return view('admin.reports.expenses', compact(
            'expenses', 'totalExpenses', 'expenseCount', 'averageExpense',
            'categorySummary', 'startDate', 'endDate', 'chartData', 'categories'
        ));
    }

    /**
     * Generate Expense Report PDF
     */
    public function expensesPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $query = \App\Models\Expense::whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'user']);

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        $expenses = $query->orderBy('date', 'desc')->get();
        $totalExpenses = $expenses->sum('amount');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.expenses_pdf', compact(
            'expenses', 'totalExpenses', 'startDate', 'endDate'
        ));

        return $pdf->download('Expense-Report-' . $startDate->format('d-M-Y') . '-to-' . $endDate->format('d-M-Y') . '.pdf');
    }

    /**
     * Sales by Sales Person Report (POS Only)
     */
    public function salesByPerson(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $query = Order::where('channel', 'pos')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $orders = $query->latest()->get();

        // Summary Calculations
        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        
        // Group by Salesperson
        $salesByPerson = $orders->groupBy('user_id')->map(function ($group) {
            return [
                'name' => $group->first()->user->name ?? 'Unknown',
                'total' => $group->sum('total'),
                'count' => $group->count(),
                'avg' => $group->count() > 0 ? $group->sum('total') / $group->count() : 0
            ];
        })->sortByDesc('total');

        // Users who have made POS sales for the dropdown
        $salesPeople = \App\Models\User::whereHas('orders', function($q) {
            $q->where('channel', 'pos');
        })->get();

        return view('admin.reports.sales_by_person', compact(
            'orders', 'totalSales', 'totalOrders', 'salesByPerson', 
            'startDate', 'endDate', 'salesPeople'
        ));
    }

    /**
     * Sales by Sales Person PDF
     */
    public function salesByPersonPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $query = Order::where('channel', 'pos')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $orders = $query->get();
        $totalSales = $orders->sum('total');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.sales_by_person_pdf', compact(
            'orders', 'totalSales', 'startDate', 'endDate'
        ));

        return $pdf->download('Sales-by-Person-' . $startDate->format('d-M-Y') . '.pdf');
    }
}
