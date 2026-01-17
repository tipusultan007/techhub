<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO #{{ $purchase->reference_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans text-sm p-8">

    <!-- Print Actions -->
    <div class="max-w-4xl mx-auto mb-6 text-right no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-bold">
            Print Document
        </button>
    </div>

    <div class="max-w-4xl mx-auto border border-gray-300 p-8 min-h-[29.7cm]">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-gray-800 pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold uppercase tracking-wide text-gray-800">Purchase Order</h1>
                <p class="text-gray-500 mt-1">PO Date: {{ \Carbon\Carbon::parse($purchase->date)->format('d F, Y') }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold">ElectroMart UAE</h2>
                <p>Dubai Silicon Oasis</p>
                <p>Dubai, UAE</p>
                <p>TRN: 100200300400500</p>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="flex justify-between mb-8">
            <div class="w-1/2">
                <h3 class="font-bold text-gray-500 uppercase text-xs mb-2">Vendor / Supplier</h3>
                <div class="text-lg font-bold">{{ $purchase->supplier->name }}</div>
                <div>{{ $purchase->supplier->company_name }}</div>
                <div>{{ $purchase->supplier->address }}</div>
                <div>Phone: {{ $purchase->supplier->phone }}</div>
                @if($purchase->supplier->trn_number)
                    <div class="mt-1 font-mono text-xs">TRN: {{ $purchase->supplier->trn_number }}</div>
                @endif
            </div>
            <div class="w-1/2 text-right">
                <h3 class="font-bold text-gray-500 uppercase text-xs mb-2">Order Reference</h3>
                <div class="text-xl font-mono font-bold">{{ $purchase->reference_no }}</div>
                <div class="mt-2">
                    <span class="bg-gray-100 px-2 py-1 rounded text-xs font-bold border">
                        STATUS: {{ strtoupper($purchase->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full mb-8 border-collapse">
            <thead>
                <tr class="bg-gray-100 border-y-2 border-gray-800">
                    <th class="py-2 px-2 text-left uppercase text-xs">#</th>
                    <th class="py-2 px-2 text-left uppercase text-xs">Description</th>
                    <th class="py-2 px-2 text-left uppercase text-xs">SKU</th>
                    <th class="py-2 px-2 text-right uppercase text-xs">Unit Cost</th>
                    <th class="py-2 px-2 text-center uppercase text-xs">Qty</th>
                    <th class="py-2 px-2 text-right uppercase text-xs">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                <tr class="border-b border-gray-300">
                    <td class="py-3 px-2">{{ $index + 1 }}</td>
                    <td class="py-3 px-2 font-bold">
                        {{ $item->product->name }}
                        @if($item->variant)
                            <div class="text-xs font-normal text-gray-500">{{ $item->variant->variant_name }}</div>
                        @endif
                    </td>
                    <td class="py-3 px-2 font-mono text-xs">
                        {{ $item->variant ? $item->variant->sku : $item->product->sku }}
                    </td>
                    <td class="py-3 px-2 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="py-3 px-2 text-center">{{ $item->quantity }}</td>
                    <td class="py-3 px-2 text-right font-bold">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-1/3">
                <div class="flex justify-between py-1 border-b">
                    <span>Subtotal:</span>
                    <span class="font-bold">{{ number_format($purchase->total_cost - $purchase->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 border-b">
                    <span>Input VAT (5%):</span>
                    <span class="font-bold">{{ number_format($purchase->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 text-lg font-bold">
                    <span>Grand Total:</span>
                    <span>AED {{ number_format($purchase->total_cost, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
            <p>If you have any questions about this purchase order, please contact [Your Phone Number]</p>
            <p class="mt-1">Authorized Signature</p>
        </div>

    </div>

    <script>
        // Optional: Auto print
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>