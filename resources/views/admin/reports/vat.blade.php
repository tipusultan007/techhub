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
            <!-- 1. OUTPUT VAT SECTION -->
            <div>
                <h3 class="text-lg font-bold text-gray-700 mb-3 border-l-4 border-green-500 pl-3">VAT on Sales (Output Tax)</h3>
                <div class="space-y-4 bg-gray-50 p-6 rounded-lg border">
                    <div class="flex justify-between items-center text-lg">
                        <span class="text-gray-600">A. Total VAT from Sales</span>
                        <span class="font-mono font-bold text-gray-800">{{ number_format($grossOutputVat, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-lg">
                        <span class="text-gray-600">B. Less: VAT on Returns</span>
                        <span class="font-mono font-bold text-yellow-600">- {{ number_format($vatOnReturns, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xl font-bold border-t-2 border-gray-300 pt-4 mt-4">
                        <span class="text-green-700">Net Output VAT Due (A - B)</span>
                        <span class="font-mono text-green-700">{{ number_format($finalOutputVat, 2) }}</span>
                    </div>
                </div>

                <!-- UAE FTA Box 1: Supplies by Emirate -->
                <div class="mt-8">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i> Standard Rated Supplies by Emirate (Box 1)
                    </h4>
                    <div class="border rounded-lg overflow-hidden shadow-sm">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 border-b">
                                <tr>
                                    <th class="p-3 text-slate-700">Emirate</th>
                                    <th class="p-3 text-right text-slate-700">Amount (AED) - Net</th>
                                    <th class="p-3 text-right text-slate-700">VAT Amount (AED) - 5%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emirateSales as $emirate => $data)
                                <tr class="border-b hover:bg-slate-50">
                                    <td class="p-3 font-semibold text-slate-800">{{ $emirate }}</td>
                                    <td class="p-3 text-right font-mono">{{ number_format($data['net'], 2) }}</td>
                                    <td class="p-3 text-right font-mono font-bold text-green-700">{{ number_format($data['vat'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-gray-500 italic">No sales found for this period.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($emirateSales) > 0)
                            <tfoot class="bg-slate-100 font-bold">
                                <tr>
                                    <td class="p-3 text-slate-800">Total Standard Rated Supplies</td>
                                    <td class="p-3 text-right">{{ number_format(collect($emirateSales)->sum('net'), 2) }}</td>
                                    <td class="p-3 text-right text-green-700">{{ number_format(collect($emirateSales)->sum('vat'), 2) }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. INPUT VAT SECTION -->
            <div>
                <h3 class="text-lg font-bold text-gray-700 mb-3 border-l-4 border-red-500 pl-3">VAT on Expenses (Input Tax)</h3>
                <div class="bg-gray-50 p-6 rounded-lg border">
                    <div class="flex justify-between items-center text-xl font-bold">
                        <span class="text-red-700">Total Input VAT Recoverable</span>
                        <span class="font-mono text-red-700">{{ number_format($inputVat, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. FINAL SUMMARY -->
            <div class="bg-slate-800 text-white p-8 rounded-lg shadow-xl text-center">
                <p class="text-sm font-bold uppercase tracking-wider mb-2">Net VAT Payable to FTA</p>
                <div class="text-5xl font-mono font-bold">
                    AED {{ number_format($netVatPayable, 2) }}
                </div>
                @if($netVatPayable < 0)
                    <p class="mt-2 text-lg font-semibold text-green-300">(Refundable from FTA)</p>
                @endif
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