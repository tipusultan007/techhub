@extends('layouts.admin')

@section('header', 'Purchase Order Details')

@section('content')
    <div class="max-w-5xl mx-auto">

        <!-- Top Navigation & Actions -->
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('purchases.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Back to History
            </a>

            <div class="flex gap-2">
                <!-- Check if any items need serials AND status is received -->
                @php
                    $needsSerials = $purchase->items->contains(fn($item) => $item->product->has_serial_number);
                @endphp

                @if ($needsSerials && $purchase->status === 'received')
                    <a href="{{ route('purchases.serials', $purchase->id) }}"
                        class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600 font-bold text-sm flex items-center">
                        <i class="fas fa-barcode mr-2"></i> Register Serials
                    </a>
                @endif

                <!-- Print/Download Button (Optional feature placeholder) -->
                <button onclick="window.print()"
                    class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700 font-bold text-sm flex items-center">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">

            <!-- Header / Status Bar -->
            <div class="bg-gray-50 px-8 py-6 border-b flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">PURCHASE ORDER</h1>
                    <p class="text-sm text-gray-500 mt-1">Internal Ref: <span
                            class="font-mono font-bold">{{ $purchase->reference_no }}</span></p>
                    <p class="text-sm text-gray-500">Date: {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="block text-xs text-gray-500 uppercase font-bold mb-1">Status</span>
                    @if ($purchase->status === 'received')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 border border-green-200">
                            <i class="fas fa-check-circle mr-2"></i> RECEIVED
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <i class="fas fa-clock mr-2"></i> PENDING
                        </span>
                    @endif
                </div>
            </div>

            <!-- Supplier & Details Grid -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-b">
                <!-- Supplier Info -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Vendor / Supplier</h3>
                    <div class="text-gray-900 font-bold text-lg">{{ $purchase->supplier->name }}</div>
                    <div class="text-gray-600">{{ $purchase->supplier->company_name }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ $purchase->supplier->address }}</div>
                    <div class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-phone mr-1 text-gray-400"></i> {{ $purchase->supplier->phone }}
                    </div>
                    @if ($purchase->supplier->trn_number)
                        <div class="text-sm text-blue-600 mt-2 font-mono bg-blue-50 inline-block px-2 py-1 rounded">
                            TRN: {{ $purchase->supplier->trn_number }}
                        </div>
                    @endif
                </div>

                <!-- Notes -->
                <div class="bg-gray-50 p-4 rounded border border-gray-100">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</h3>
                    <p class="text-sm text-gray-600 italic">
                        {{ $purchase->notes ?? 'No additional notes provided.' }}
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="px-8 py-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Items Ordered</h3>
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">SKU</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Unit Cost</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total (Net)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($purchase->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $item->product->name }}</div>
                                        @if ($item->variant)
                                            <div class="text-xs text-gray-500">{{ $item->variant->variant_name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-500">
                                        {{ $item->variant ? $item->variant->sku : $item->product->sku }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-600">
                                        {{ number_format($item->unit_cost, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-800">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                        {{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="px-8 py-6 bg-gray-50 border-t flex justify-end">
                <div class="w-full md:w-1/3 space-y-3">

                    <!-- Subtotal -->
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal (Net Amount)</span>
                        <span
                            class="font-medium">{{ number_format($purchase->total_cost - $purchase->tax_amount, 2) }}</span>
                    </div>

                    <!-- Input VAT -->
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Input VAT (5%)</span>
                        <span class="font-medium">{{ number_format($purchase->tax_amount, 2) }}</span>
                    </div>

                    <div class="border-t border-gray-300"></div>

                    <!-- Grand Total -->
                    <div class="flex justify-between text-xl font-bold text-gray-900 pt-2">
                        <span>Grand Total</span>
                        <span>AED {{ number_format($purchase->total_cost, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="px-8 py-4 bg-gray-100 border-t text-center text-xs text-gray-500">
                Recorded by {{ Auth::user()->name }} on {{ $purchase->created_at->format('d M Y H:i') }}
            </div>

        </div>
    </div>
@endsection
