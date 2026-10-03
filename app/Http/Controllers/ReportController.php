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

        // 1. OUTPUT VAT (Sales)
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', '!=', 'cancelled')
                      ->get();
        
        $grossSalesTotal = $sales->sum('total');
        $grossOutputVat = $sales->sum('vat_amount');

        // Box 1: Standard Rated Supplies (Breakdown by Emirate)
        $emirateSales = [];
        $standardRatedNet = 0;
        $standardRatedVat = 0;

        // Box 2 & 3: Zero Rated and Exempt (If VAT is 0)
        $zeroRatedNet = 0;
        $exemptNet = 0;

        foreach ($sales as $sale) {
            if ($sale->vat_amount > 0) {
                $city = $sale->channel === 'online' ? ($sale->shipping_city ?: 'Dubai') : 'Dubai';
                $city = ucwords(strtolower(trim($city)));
                
                if (!isset($emirateSales[$city])) {
                    $emirateSales[$city] = ['net' => 0, 'vat' => 0];
                }
                $net = ($sale->total - $sale->vat_amount);
                $emirateSales[$city]['net'] += $net;
                $emirateSales[$city]['vat'] += $sale->vat_amount;
                
                $standardRatedNet += $net;
                $standardRatedVat += $sale->vat_amount;
            } else {
                // For now, group all 0% VAT under Zero Rated (common for e-commerce exports)
                $zeroRatedNet += $sale->total;
            }
        }

        // Box 5: Adjustments (Returns / Credit Notes)
        $returns = ReturnOrder::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalRefunds = $returns->sum('total_refund');
        
        $taxRate = 0.05;
        $vatOnReturns = $returns->sum(function($return) use ($taxRate) {
            $net = $return->total_refund / (1 + $taxRate);
            return $return->total_refund - $net;
        });

        // Net Output VAT
        $finalOutputVat = $grossOutputVat - $vatOnReturns;

        // 2. INPUT VAT (Purchases & Expenses)
        $purchases = \App\Models\PurchaseOrder::whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['received', 'completed', 'partial_received'])
            ->get();

        $expenses = \App\Models\Expense::whereBetween('date', [$startDate, $endDate])->get();

        $purchasesTotal = $purchases->sum('total_cost');
        $purchaseVat = $purchases->sum('tax_amount');
        $purchasesNet = $purchasesTotal - $purchaseVat;

        $expensesTotal = $expenses->sum('amount');
        $expenseVat = $expenses->sum('tax_amount');
        $expensesNet = $expensesTotal - $expenseVat;

        $inputVat = $purchaseVat + $expenseVat;

        // 3. FINAL CALCULATION
        $netVatPayable = $finalOutputVat - $inputVat;

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'grossSalesTotal' => $grossSalesTotal,
            'grossOutputVat' => $grossOutputVat,
            'emirateSales' => $emirateSales,
            'standardRatedNet' => $standardRatedNet,
            'standardRatedVat' => $standardRatedVat,
            'zeroRatedNet' => $zeroRatedNet,
            'exemptNet' => $exemptNet,
            'returns' => $returns,
            'totalRefunds' => $totalRefunds,
            'vatOnReturns' => $vatOnReturns,
            'finalOutputVat' => $finalOutputVat,
            'purchases' => $purchases,
            'purchasesTotal' => $purchasesTotal,
            'purchasesNet' => $purchasesNet,
            'purchaseVat' => $purchaseVat,
            'expensesTotal' => $expensesTotal,
            'expensesNet' => $expensesNet,
            'expenseVat' => $expenseVat,
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
    /**
     * Profit & Loss Report
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1. Revenue (Sales)
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->with(['items.product', 'items.variant'])
            ->get();

        $totalRevenue = $sales->sum('total');
        $totalDiscount = $sales->sum('discount');
        $totalVAT = $sales->sum('vat_amount');
        $totalShipping = 0;
        $netSales = $sales->sum('subtotal');

        // Revenue VAT Split Calculations
        $standardRatedNet = 0;
        $standardRatedVat = 0;
        $zeroRatedNet = 0;
        foreach ($sales as $sale) {
            $actualShipping = max(0, round($sale->total - $sale->subtotal - $sale->vat_amount, 2));
            $totalShipping += $actualShipping;
            if ($sale->vat_amount > 0) {
                // Exclude shipping and tax to get standard-rated net product sales
                $standardRatedNet += $sale->subtotal;
                $standardRatedVat += $sale->vat_amount;
            } else {
                // Exclude shipping to get zero-rated net product sales
                $zeroRatedNet += $sale->subtotal;
            }
        }

        // Channel Breakdown
        $channelBreakdown = $sales->groupBy('channel')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
                'subtotal' => $group->sum('subtotal'),
                'vat' => $group->sum('vat_amount'),
                'discount' => $group->sum('discount'),
            ];
        });

        // Payment Method Breakdown
        $paymentBreakdown = $sales->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        // 2. Returns (Credit Notes)
        $returns = ReturnOrder::whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product', 'items.variant'])
            ->get();
        $totalReturns = $returns->sum('total_refund');
        $returnsCount = $returns->count();

        // VAT on Returns
        $taxRate = 0.05;
        $vatOnReturns = $returns->sum(function($return) use ($taxRate) {
            $net = $return->total_refund / (1 + $taxRate);
            return $return->total_refund - $net;
        });
        $netReturns = $totalReturns - $vatOnReturns;

        // 3. COGS (Cost of Goods Sold)
        $cogs = 0;
        $cogsSimple = 0;
        $cogsVariant = 0;
        $itemsCount = 0;
        foreach($sales as $order) {
            foreach($order->items as $item) {
                $product = $item->product;
                if ($item->product_variant_id) {
                    $variant = $item->variant;
                    $cost = $variant ? $variant->cost_price : ($product ? $product->cost_price : 0);
                } else {
                    $cost = $product ? $product->cost_price : 0;
                }
                $itemCogs = ($cost * $item->quantity);
                $cogs += $itemCogs;
                $itemsCount += $item->quantity;
                
                if ($item->product_variant_id) {
                    $cogsVariant += $itemCogs;
                } else {
                    $cogsSimple += $itemCogs;
                }
            }
        }

        // Adjust COGS for Returned Items
        foreach($returns as $return) {
            foreach($return->items as $item) {
                if ($item->product_variant_id) {
                    $variant = $item->variant;
                    $cost = $variant ? $variant->cost_price : 0;
                    $itemCost = $cost * $item->quantity;
                    $cogsVariant -= $itemCost;
                } else {
                    $product = $item->product;
                    $cost = $product ? $product->cost_price : 0;
                    $itemCost = $cost * $item->quantity;
                    $cogsSimple -= $itemCost;
                }
                $cogs -= $itemCost;
                $itemsCount -= $item->quantity;
            }
        }

        // 4. Stock Purchases (For Input VAT auditing side-by-side with COGS)
        $purchases = \App\Models\PurchaseOrder::whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['received', 'completed', 'partial_received'])
            ->get();
        $purchasesTotal = $purchases->sum('total_cost');
        $purchaseVat = $purchases->sum('tax_amount');
        $purchasesNet = $purchasesTotal - $purchaseVat;

        // 5. Gross Profit (VAT-exclusive)
        // Net Revenue (Excl. VAT) = Total revenue (Excl. VAT) - Net Returns (Excl. VAT)
        $netRevenueExclVat = ($totalRevenue - $totalVAT) - $netReturns;
        $grossProfit = $netRevenueExclVat - $cogs;

        // 6. Operating Expenses
        $expenses = \App\Models\Expense::whereBetween('date', [$startDate, $endDate])
            ->with('category')
            ->get();
        $expensesNet = $expenses->sum(function($e) {
            return $e->net_amount ?? $e->amount;
        });
        $expensesTax = $expenses->sum('tax_amount');
        $totalExpenses = $expensesNet + $expensesTax; // True gross expense

        // Expenses Category Breakdown
        $expenseCategories = $expenses->groupBy('expense_category_id')->map(function ($group) {
            $net = $group->sum(function($e) {
                return $e->net_amount ?? $e->amount;
            });
            $tax = $group->sum('tax_amount');
            return [
                'name' => $group->first()->category->name ?? 'Uncategorized',
                'count' => $group->count(),
                'total' => $net + $tax,
                'net' => $net,
                'tax' => $tax,
            ];
        })->sortByDesc('total');

        // 7. Net Profit
        // Net Profit = Gross Profit - Net Expenses (since VAT input tax is recoverable, not an expense)
        $netProfit = $grossProfit - $expensesNet;

        return view('admin.reports.profit_loss', compact(
            'totalRevenue', 'totalDiscount', 'totalVAT', 'totalShipping', 'netSales',
            'standardRatedNet', 'standardRatedVat', 'zeroRatedNet',
            'channelBreakdown', 'paymentBreakdown', 'totalReturns', 'returnsCount',
            'netReturns', 'vatOnReturns',
            'cogs', 'cogsSimple', 'cogsVariant', 'itemsCount', 'grossProfit',
            'purchases', 'purchasesTotal', 'purchaseVat', 'purchasesNet',
            'totalExpenses', 'expensesNet', 'expensesTax', 'expenseCategories',
            'netProfit', 'startDate', 'endDate'
        ));
    }

    /**
     * Generate Profit & Loss Report PDF
     */
    public function profitLossPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1. Revenue (Sales)
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->with(['items.product', 'items.variant'])
            ->get();

        $totalRevenue = $sales->sum('total');
        $totalDiscount = $sales->sum('discount');
        $totalVAT = $sales->sum('vat_amount');
        $totalShipping = 0;
        $netSales = $sales->sum('subtotal');

        // Revenue VAT Split Calculations
        $standardRatedNet = 0;
        $standardRatedVat = 0;
        $zeroRatedNet = 0;
        foreach ($sales as $sale) {
            $actualShipping = max(0, round($sale->total - $sale->subtotal - $sale->vat_amount, 2));
            $totalShipping += $actualShipping;
            if ($sale->vat_amount > 0) {
                $standardRatedNet += $sale->subtotal;
                $standardRatedVat += $sale->vat_amount;
            } else {
                $zeroRatedNet += $sale->subtotal;
            }
        }

        // Channel Breakdown
        $channelBreakdown = $sales->groupBy('channel')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
                'subtotal' => $group->sum('subtotal'),
                'vat' => $group->sum('vat_amount'),
                'discount' => $group->sum('discount'),
            ];
        });

        // Payment Method Breakdown
        $paymentBreakdown = $sales->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        // 2. Returns (Credit Notes)
        $returns = ReturnOrder::whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product', 'items.variant'])
            ->get();
        $totalReturns = $returns->sum('total_refund');
        $returnsCount = $returns->count();

        // VAT on Returns
        $taxRate = 0.05;
        $vatOnReturns = $returns->sum(function($return) use ($taxRate) {
            $net = $return->total_refund / (1 + $taxRate);
            return $return->total_refund - $net;
        });
        $netReturns = $totalReturns - $vatOnReturns;

        // 3. COGS (Cost of Goods Sold)
        $cogs = 0;
        $cogsSimple = 0;
        $cogsVariant = 0;
        $itemsCount = 0;
        foreach($sales as $order) {
            foreach($order->items as $item) {
                $product = $item->product;
                if ($item->product_variant_id) {
                    $variant = $item->variant;
                    $cost = $variant ? $variant->cost_price : ($product ? $product->cost_price : 0);
                } else {
                    $cost = $product ? $product->cost_price : 0;
                }
                $itemCogs = ($cost * $item->quantity);
                $cogs += $itemCogs;
                $itemsCount += $item->quantity;
                
                if ($item->product_variant_id) {
                    $cogsVariant += $itemCogs;
                } else {
                    $cogsSimple += $itemCogs;
                }
            }
        }

        // Adjust COGS for Returned Items
        foreach($returns as $return) {
            foreach($return->items as $item) {
                if ($item->product_variant_id) {
                    $variant = $item->variant;
                    $cost = $variant ? $variant->cost_price : 0;
                    $itemCost = $cost * $item->quantity;
                    $cogsVariant -= $itemCost;
                } else {
                    $product = $item->product;
                    $cost = $product ? $product->cost_price : 0;
                    $itemCost = $cost * $item->quantity;
                    $cogsSimple -= $itemCost;
                }
                $cogs -= $itemCost;
                $itemsCount -= $item->quantity;
            }
        }

        // 4. Stock Purchases (For Input VAT auditing side-by-side with COGS)
        $purchases = \App\Models\PurchaseOrder::whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['received', 'completed', 'partial_received'])
            ->get();
        $purchasesTotal = $purchases->sum('total_cost');
        $purchaseVat = $purchases->sum('tax_amount');
        $purchasesNet = $purchasesTotal - $purchaseVat;

        // 5. Gross Profit (VAT-exclusive)
        $netRevenueExclVat = ($totalRevenue - $totalVAT) - $netReturns;
        $grossProfit = $netRevenueExclVat - $cogs;

        // 6. Operating Expenses
        $expenses = \App\Models\Expense::whereBetween('date', [$startDate, $endDate])
            ->with('category')
            ->get();
        $expensesNet = $expenses->sum(function($e) {
            return $e->net_amount ?? $e->amount;
        });
        $expensesTax = $expenses->sum('tax_amount');
        $totalExpenses = $expensesNet + $expensesTax;

        // Expenses Category Breakdown
        $expenseCategories = $expenses->groupBy('expense_category_id')->map(function ($group) {
            $net = $group->sum(function($e) {
                return $e->net_amount ?? $e->amount;
            });
            $tax = $group->sum('tax_amount');
            return [
                'name' => $group->first()->category->name ?? 'Uncategorized',
                'count' => $group->count(),
                'total' => $net + $tax,
                'net' => $net,
                'tax' => $tax,
            ];
        })->sortByDesc('total');

        // 7. Net Profit
        $netProfit = $grossProfit - $expensesNet;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.profit_loss_pdf', compact(
            'sales', 'totalRevenue', 'totalDiscount', 'totalVAT', 'totalShipping', 'netSales',
            'standardRatedNet', 'standardRatedVat', 'zeroRatedNet',
            'channelBreakdown', 'paymentBreakdown', 'returns', 'totalReturns', 'returnsCount',
            'netReturns', 'vatOnReturns',
            'cogs', 'cogsSimple', 'cogsVariant', 'itemsCount', 'grossProfit',
            'purchases', 'purchasesTotal', 'purchaseVat', 'purchasesNet',
            'expenses', 'totalExpenses', 'expensesNet', 'expensesTax', 'expenseCategories',
            'netProfit', 'startDate', 'endDate'
        ));

        return $pdf->download('Profit-Loss-Statement-' . $startDate->format('d-M-Y') . '-to-' . $endDate->format('d-M-Y') . '.pdf');
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



    /**
     * Stock Mismatch / Audit Report
     */
    public function stockMismatch(Request $request)
    {
        $search = $request->input('search');
        $filterStatus = $request->input('filter_status', 'mismatched');

        $query = Product::with('variants');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('variants', function ($vq) use ($search) {
                      $vq->where('sku', 'like', "%{$search}%")
                         ->orWhere('variant_name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->get();
        $auditList = collect();

        foreach ($products as $p) {
            if ($p->type === 'variable') {
                foreach ($p->variants as $v) {
                    $purchased = (int) \Illuminate\Support\Facades\DB::table('purchase_order_items')
                        ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
                        ->where('purchase_order_items.product_variant_id', $v->id)
                        ->whereIn('purchase_orders.status', ['completed', 'received', 'partial'])
                        ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(purchase_order_items.received_quantity, 0), purchase_order_items.quantity)'));

                    $sold = (int) \Illuminate\Support\Facades\DB::table('order_items')
                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                        ->where('order_items.product_variant_id', $v->id)
                        ->where('orders.status', '!=', 'cancelled')
                        ->sum('order_items.quantity');

                    $returned = (int) \Illuminate\Support\Facades\DB::table('return_items')
                        ->join('returns', 'returns.id', '=', 'return_items.return_id')
                        ->where('return_items.product_variant_id', $v->id)
                        ->where('return_items.restock_status', 'restockable')
                        ->sum('return_items.quantity');

                    $expected = $purchased - $sold + $returned;
                    $current = (int) $v->stock_quantity;
                    $diff = $current - $expected;
                    $isMismatched = ($current !== $expected);

                    if ($filterStatus === 'mismatched' && !$isMismatched) {
                        continue;
                    }
                    if ($filterStatus === 'matched' && $isMismatched) {
                        continue;
                    }

                    $auditList->push([
                        'type' => 'variant',
                        'product_id' => $p->id,
                        'variant_id' => $v->id,
                        'name' => $p->name,
                        'variant_name' => $v->variant_name,
                        'sku' => $v->sku,
                        'current_stock' => $current,
                        'total_purchased' => $purchased,
                        'total_sold' => $sold,
                        'total_returned' => $returned,
                        'expected_stock' => $expected,
                        'diff' => $diff,
                        'is_mismatched' => $isMismatched,
                    ]);
                }
            } else {
                $purchased = (int) \Illuminate\Support\Facades\DB::table('purchase_order_items')
                    ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
                    ->where('purchase_order_items.product_id', $p->id)
                    ->whereIn('purchase_orders.status', ['completed', 'received', 'partial'])
                    ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(purchase_order_items.received_quantity, 0), purchase_order_items.quantity)'));

                $sold = (int) \Illuminate\Support\Facades\DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('order_items.product_id', $p->id)
                    ->where('orders.status', '!=', 'cancelled')
                    ->sum('order_items.quantity');

                $returned = (int) \Illuminate\Support\Facades\DB::table('return_items')
                    ->join('returns', 'returns.id', '=', 'return_items.return_id')
                    ->where('return_items.product_id', $p->id)
                    ->where('return_items.restock_status', 'restockable')
                    ->sum('return_items.quantity');

                $expected = $purchased - $sold + $returned;
                $current = (int) $p->stock_quantity;
                $diff = $current - $expected;
                $isMismatched = ($current !== $expected);

                if ($filterStatus === 'mismatched' && !$isMismatched) {
                    continue;
                }
                if ($filterStatus === 'matched' && $isMismatched) {
                    continue;
                }

                $auditList->push([
                    'type' => 'simple',
                    'product_id' => $p->id,
                    'variant_id' => null,
                    'name' => $p->name,
                    'variant_name' => null,
                    'sku' => $p->sku,
                    'current_stock' => $current,
                    'total_purchased' => $purchased,
                    'total_sold' => $sold,
                    'total_returned' => $returned,
                    'expected_stock' => $expected,
                    'diff' => $diff,
                    'is_mismatched' => $isMismatched,
                ]);
            }
        }

        $totalChecked = $auditList->count();
        $mismatchedCount = $auditList->where('is_mismatched', true)->count();
        $totalDiffUnits = $auditList->where('is_mismatched', true)->sum(function ($item) {
            return abs($item['diff']);
        });

        // Manual Pagination for Collection
        $page = request()->get('page', 1);
        $perPage = 25;
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $auditList->forPage($page, $perPage),
            $auditList->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.reports.stock_mismatch', compact(
            'paginatedItems', 'totalChecked', 'mismatchedCount', 'totalDiffUnits',
            'search', 'filterStatus'
        ));
    }

    /**
     * Reconcile/Fix product stock to expected stock.
     */
    public function reconcileStock(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'all') {
            \Illuminate\Support\Facades\Artisan::call('stock:audit --fix');
            return back()->with('success', 'All mismatched product stocks have been reconciled successfully!');
        }

        $expected = (int) $request->input('expected_stock');

        if ($type === 'variant') {
            $variant = ProductVariant::findOrFail($id);
            $variant->update(['stock_quantity' => $expected]);
        } else {
            $product = Product::findOrFail($id);
            $product->update(['stock_quantity' => $expected]);
        }

        return back()->with('success', 'Stock quantity reconciled successfully.');
    }
}
