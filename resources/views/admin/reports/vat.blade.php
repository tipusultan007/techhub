@extends('layouts.admin')

@section('header', 'VAT Return Report (UAE)')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Filter & Actions Bar -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 no-print">
        <form action="{{ route('reports.vat') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Filter inputs... -->
            <div class="flex-1">
                <label class="block text-sm font-bold text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full border rounded p-2 text-sm">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-bold text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full border rounded p-2 text-sm">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 font-bold">
                    <i class="fas fa-sync-alt mr-2"></i> Generate
                </button>
                <a href="{{ route('reports.vat.pdf', request()->all()) }}" class="bg-red-600 text-white px-6 py-2 rounded shadow hover:bg-red-700 font-bold ml-2">
                    <i class="fas fa-file-pdf mr-2"></i> Download PDF
                </a>
                <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-700 font-bold ml-2">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </form>
    </div>

    <!-- Main Report Container -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200" id="print-area">
        
        <!-- Report Header -->
        <div class="px-8 py-6 border-b-2 border-slate-800">
            <!-- Header content... -->
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">VAT Return Summary</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Period: <span class="font-semibold">{{ $startDate->format('d M, Y') }}</span> to <span class="font-semibold">{{ $endDate->format('d M, Y') }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg">{{ $settings['shop_name'] ?? 'Tech Hub UAE' }}</h3>
                    <p class="text-sm text-gray-600">TRN: {{ $settings['shop_trn'] ?? 'Not Set' }}</p>
                </div>
            </div>
        </div>

        <div class="p-8 space-y-8">
            <!-- 1. OUTPUT VAT SECTION (Sales) -->
            <div>
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-l-4 border-slate-800 pl-3 uppercase tracking-tight">VAT on Sales (Output Tax)</h3>
                <div class="overflow-hidden border rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b">
                            <tr>
                                <th class="p-3 w-16 text-center font-bold text-gray-500">Box</th>
                                <th class="p-3 text-gray-700">Description</th>
                                <th class="p-3 text-right text-gray-700">Amount (AED)</th>
                                <th class="p-3 text-right text-gray-700">VAT (AED)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <!-- Box 1 -->
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center bg-gray-50 font-bold border-r">1a-g</td>
                                <td class="p-3">
                                    <span class="font-semibold block">Standard Rated Supplies</span>
                                    <span class="text-xs text-gray-500 italic">Local sales within UAE Emirates</span>
                                </td>
                                <td class="p-3 text-right font-mono">{{ number_format($standardRatedNet, 2) }}</td>
                                <td class="p-3 text-right font-mono font-bold text-green-700">{{ number_format($standardRatedVat, 2) }}</td>
                            </tr>
                            <!-- Box 2 -->
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center bg-gray-50 font-bold border-r">2</td>
                                <td class="p-3 font-semibold text-gray-700">Tax Repayment / Zero Rated Supplies</td>
                                <td class="p-3 text-right font-mono">{{ number_format($zeroRatedNet, 2) }}</td>
                                <td class="p-3 text-right font-mono">0.00</td>
                            </tr>
                            <!-- Box 3 -->
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center bg-gray-50 font-bold border-r">3</td>
                                <td class="p-3 font-semibold text-gray-700">Exempt Supplies</td>
                                <td class="p-3 text-right font-mono">{{ number_format($exemptNet, 2) }}</td>
                                <td class="p-3 text-right font-mono">0.00</td>
                            </tr>
                            <!-- Box 5 -->
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center bg-gray-50 font-bold border-r">5</td>
                                <td class="p-3 font-semibold text-gray-700">Adjustments (Returns / Credit Notes)</td>
                                <td class="p-3 text-right font-mono text-red-600">- {{ number_format($totalRefunds - $vatOnReturns, 2) }}</td>
                                <td class="p-3 text-right font-mono text-red-600">- {{ number_format($vatOnReturns, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold">
                            <tr>
                                <td colspan="2" class="p-3 text-gray-800 uppercase">Total Output VAT Due</td>
                                <td class="p-3 text-right font-mono">{{ number_format($grossSalesTotal - $totalRefunds, 2) }}</td>
                                <td class="p-3 text-right font-mono text-green-700">{{ number_format($finalOutputVat, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Box 1 Breakdown (Mini Table) -->
                <div class="mt-4 ml-8">
                    <h5 class="text-xs font-bold text-gray-500 uppercase mb-2">Box 1 Breakdown by Emirate</h5>
                    <div class="border rounded overflow-hidden max-w-2xl">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="p-2">Emirate</th>
                                    <th class="p-2 text-right">Net Amount</th>
                                    <th class="p-2 text-right">VAT (5%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emirateSales as $emirate => $data)
                                <tr class="border-b last:border-0">
                                    <td class="p-2 font-medium">{{ $emirate }}</td>
                                    <td class="p-2 text-right font-mono">{{ number_format($data['net'], 2) }}</td>
                                    <td class="p-2 text-right font-mono">{{ number_format($data['vat'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. INPUT VAT SECTION (Purchases) -->
            <div>
                <h3 class="text-lg font-bold text-gray-700 mb-4 border-l-4 border-slate-800 pl-3 uppercase tracking-tight">VAT on Expenses (Input Tax)</h3>
                <div class="overflow-hidden border rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b">
                            <tr>
                                <th class="p-3 w-16 text-center font-bold text-gray-500">Box</th>
                                <th class="p-3 text-gray-700">Description</th>
                                <th class="p-3 text-right text-gray-700">Amount (AED)</th>
                                <th class="p-3 text-right text-gray-700">Recoverable VAT (AED)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <!-- Box 9a -->
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center bg-gray-50 font-bold border-r">9a</td>
                                <td class="p-3">
                                    <span class="font-semibold block">Standard Rated Purchases</span>
                                    <span class="text-xs text-gray-500 italic">Inventory / Stock from Suppliers</span>
                                </td>
                                <td class="p-3 text-right font-mono">{{ number_format($purchasesNet, 2) }}</td>
                                <td class="p-3 text-right font-mono font-bold text-red-600">{{ number_format($purchaseVat, 2) }}</td>
                            </tr>
                            <!-- Box 9b -->
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center bg-gray-50 font-bold border-r">9b</td>
                                <td class="p-3">
                                    <span class="font-semibold block">Standard Rated Expenses</span>
                                    <span class="text-xs text-gray-500 italic">Office bills, rent, petrol, etc.</span>
                                </td>
                                <td class="p-3 text-right font-mono">{{ number_format($expensesNet, 2) }}</td>
                                <td class="p-3 text-right font-mono font-bold text-red-600">{{ number_format($expenseVat, 2) }}</td>
                            </tr>
                            <!-- Box 10 -->
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-center bg-gray-50 font-bold border-r">10</td>
                                <td class="p-3 font-semibold text-gray-700">Supplies subject to Reverse Charge (Imports)</td>
                                <td class="p-3 text-right font-mono">0.00</td>
                                <td class="p-3 text-right font-mono text-gray-400">0.00</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold">
                            <tr>
                                <td colspan="2" class="p-3 text-gray-800 uppercase">Total Recoverable Input VAT</td>
                                <td class="p-3 text-right font-mono">{{ number_format($purchasesNet + $expensesNet, 2) }}</td>
                                <td class="p-3 text-right font-mono text-red-700">{{ number_format($inputVat, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- 3. FINAL SUMMARY -->
            <div class="bg-slate-800 text-white p-8 rounded-lg shadow-xl flex flex-col md:flex-row justify-between items-center text-center md:text-left">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-1">Net VAT Position</p>
                    <h2 class="text-2xl font-bold uppercase">Payable to FTA / (Refundable)</h2>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-5xl font-mono font-bold">
                        AED {{ number_format($netVatPayable, 2) }}
                    </div>
                    @if($netVatPayable < 0)
                        <p class="mt-2 text-lg font-semibold text-green-300 italic">Refundable from Federal Tax Authority</p>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- === DETAILED BREAKDOWN TABLES (NEW) === -->
    <div class="mt-8 space-y-8 no-print" x-data="{ openSales: false, openReturns: false, openPurchases: false }">

        <!-- 1. Sales Breakdown -->
        <div class="bg-white rounded-lg shadow border">
            <button @click="openSales = !openSales" class="w-full text-left p-4 font-bold text-gray-700 hover:bg-gray-50 flex justify-between items-center">
                <span>Sales Transactions ({{ $sales->count() }})</span>
                <i class="fas" :class="{ 'fa-chevron-down': !openSales, 'fa-chevron-up': openSales }"></i>
            </button>
            <div x-show="openSales" x-transition class="p-4 border-t max-h-96 overflow-y-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-100"><tr><th class="p-2">Invoice</th><th class="p-2">Date</th><th class="p-2 text-right">Net</th><th class="p-2 text-right">VAT</th><th class="p-2 text-right">Total</th></tr></thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr class="border-b">
                            <td class="p-2 font-mono"><a href="{{ route('orders.show', $sale) }}" class="text-blue-600" target="_blank">{{ $sale->invoice_no }}</a></td>
                            <td class="p-2">{{ $sale->created_at->format('Y-m-d') }}</td>
                            <td class="p-2 text-right">{{ number_format($sale->subtotal, 2) }}</td>
                            <td class="p-2 text-right font-bold">{{ number_format($sale->vat_amount, 2) }}</td>
                            <td class="p-2 text-right">{{ number_format($sale->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Returns Breakdown -->
        <div class="bg-white rounded-lg shadow border">
            <button @click="openReturns = !openReturns" class="w-full text-left p-4 font-bold text-gray-700 hover:bg-gray-50 flex justify-between items-center">
                <span>Returns / Credit Notes ({{ $returns->count() }})</span>
                <i class="fas" :class="{ 'fa-chevron-down': !openReturns, 'fa-chevron-up': openReturns }"></i>
            </button>
            <div x-show="openReturns" x-transition class="p-4 border-t max-h-96 overflow-y-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-100"><tr><th class="p-2">Credit Note</th><th class="p-2">Date</th><th class="p-2 text-right">Reversed VAT</th><th class="p-2 text-right">Total Refund</th></tr></thead>
                    <tbody>
                        @foreach($returns as $return)
                        @php
                            $vatReversed = $return->total_refund - ($return->total_refund / 1.05);
                        @endphp
                        <tr class="border-b">
                            <td class="p-2 font-mono"><a href="{{ route('returns.show', $return) }}" class="text-blue-600" target="_blank">{{ $return->credit_note_no }}</a></td>
                            <td class="p-2">{{ $return->created_at->format('Y-m-d') }}</td>
                            <td class="p-2 text-right font-bold text-yellow-700">- {{ number_format($vatReversed, 2) }}</td>
                            <td class="p-2 text-right">- {{ number_format($return->total_refund, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Purchases Breakdown -->
        <div class="bg-white rounded-lg shadow border">
            <button @click="openPurchases = !openPurchases" class="w-full text-left p-4 font-bold text-gray-700 hover:bg-gray-50 flex justify-between items-center">
                <span>Purchases ({{ $purchases->count() }})</span>
                <i class="fas" :class="{ 'fa-chevron-down': !openPurchases, 'fa-chevron-up': openPurchases }"></i>
            </button>
            <div x-show="openPurchases" x-transition class="p-4 border-t max-h-96 overflow-y-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-100"><tr><th class="p-2">Reference</th><th class="p-2">Date</th><th class="p-2 text-right">Net</th><th class="p-2 text-right">VAT</th><th class="p-2 text-right">Total</th></tr></thead>
                    <tbody>
                        @foreach($purchases as $po)
                        <tr class="border-b">
                            <td class="p-2 font-mono"><a href="{{ route('purchases.show', $po) }}" class="text-blue-600" target="_blank">{{ $po->reference_no }}</a></td>
                            <td class="p-2">{{ \Carbon\Carbon::parse($po->date)->format('Y-m-d') }}</td>
                            <td class="p-2 text-right">{{ number_format($po->total_cost - $po->tax_amount, 2) }}</td>
                            <td class="p-2 text-right font-bold">{{ number_format($po->tax_amount, 2) }}</td>
                            <td class="p-2 text-right">{{ number_format($po->total_cost, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Alpine.js for the collapsible sections -->
<script src="//unpkg.com/alpinejs" defer></script>

<!-- Print-Specific Styles -->
<style>
    @media print {
        .no-print { display: none !important; }
        body { background-color: white !important; font-size: 11pt; color: black; }
        
        #print-area { 
            box-shadow: none !important; 
            border: none !important; 
            width: 100% !important; 
            margin: 0 !important; 
            padding: 0 !important;
            overflow: visible !important;
        }

        /* Match PDF Aesthetic */
        .bg-slate-800 { background-color: #1e293b !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bg-gray-50, .bg-slate-50, .bg-slate-100 { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .border-l-4 { border-left: 4px solid #ccc !important; }
        
        .text-green-700 { color: #15803d !important; }
        .text-red-700 { color: #b91c1c !important; }
        .text-green-300 { color: #86efac !important; }
        
        /* Ensure tables look professional */
        table { width: 100% !important; border-collapse: collapse !important; }
        th, td { border: 1px solid #e2e8f0 !important; padding: 8px !important; }
        thead { display: table-header-group; }
        
        /* Final Summary Box Fix for Print */
        .bg-slate-800.text-white {
            border: 1px solid #1e293b !important;
            padding: 2rem !important;
        }
    }
</style>
@endsection