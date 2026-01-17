@extends('layouts.admin')

@section('header', 'Invoice Details')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
        <a href="{{ route('orders.print', $order) }}" target="_blank" class="bg-gray-800 text-white px-5 py-2 rounded shadow hover:bg-gray-700 font-bold transition">
            <i class="fas fa-print mr-2"></i> Print Receipt
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        
        <!-- Invoice Header -->
        <div class="px-8 py-6 border-b bg-gray-50 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
                     {{ $order->vat_amount > 0 ? 'TAX INVOICE' : 'SALES RECEIPT' }}
                </h1>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p>Invoice #: <span class="font-mono font-bold text-gray-900">{{ $order->invoice_no }}</span></p>
                    <p>Date: {{ $order->created_at->format('d M Y, h:i A') }}</p>
                    <p>Status: <span class="px-2 py-0.5 rounded text-xs font-bold uppercase {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $order->status }}</span></p>
                </div>
            </div>
            <div class="text-right">
                <h3 class="font-bold text-lg text-gray-700">ElectroMart UAE</h3>
                <p class="text-sm text-gray-500">Dubai Silicon Oasis</p>
                <p class="text-sm text-gray-500">Dubai, United Arab Emirates</p>
                <p class="text-sm font-bold text-gray-600 mt-1">TRN: 100200300400500</p>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="px-8 py-5 border-b grid grid-cols-2">
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Bill To:</h4>
                <p class="font-bold text-gray-800 text-lg">{{ $order->customer_name }}</p>
                @if($order->customer)
                    <p class="text-sm text-gray-600">{{ $order->customer->phone }}</p>
                    <p class="text-sm text-gray-600">{{ $order->customer->email }}</p>
                    @if($order->customer->trn_number)
                        <p class="text-sm text-blue-600 font-mono mt-1">TRN: {{ $order->customer->trn_number }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500 italic">Guest / Walk-in Customer</p>
                @endif
            </div>
            <div class="text-right">
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Payment Details:</h4>
                <p class="text-sm text-gray-600">Method: <span class="font-bold uppercase">{{ $order->payment_method }}</span></p>
                <p class="text-sm text-gray-600">Cashier: {{ $order->user->name ?? 'System' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-8 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Item Description</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-8 py-4">
                        <div class="text-sm font-bold text-gray-800">{{ $item->product_name }}</div>
                        
                        <!-- Serial & Warranty Info -->
                        @if($item->serial_numbers)
                            <div class="mt-1 flex items-center text-xs">
                                <span class="bg-gray-100 text-gray-600 px-1.5 rounded border mr-2 font-mono">SN: {{ $item->serial_numbers }}</span>
                                @if($item->warranty_end_date)
                                    <span class="text-green-600">
                                        <i class="fas fa-shield-alt mr-1"></i> 
                                        Warranty: {{ \Carbon\Carbon::parse($item->warranty_end_date)->format('d M Y') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-8 py-4 text-sm text-right text-gray-600">
                        {{ number_format($item->unit_price, 2) }}
                    </td>
                    <td class="px-8 py-4 text-sm text-right text-gray-600">
                        {{ $item->quantity }}
                    </td>
                    <td class="px-8 py-4 text-sm text-right font-bold text-gray-800">
                        {{ number_format($item->subtotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="px-8 py-6 bg-gray-50 flex justify-end">
            <div class="w-full md:w-1/2 lg:w-1/3 space-y-3">
                
                <!-- Cart Total (Calculated) -->
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Cart Total</span>
                    <span>AED {{ number_format($order->total + $order->discount, 2) }}</span>
                </div>

                <!-- Discount (If applicable) -->
                @if($order->discount > 0)
                <div class="flex justify-between text-sm text-red-600 font-medium">
                    <span>Discount Applied</span>
                    <span>- AED {{ number_format($order->discount, 2) }}</span>
                </div>
                @endif

                <div class="border-t border-gray-300 my-2"></div>

                <!-- Tax Breakdown -->
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Net Amount (Excl. VAT)</span>
                    <span>AED {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>VAT (5%)</span>
                    <span>AED {{ number_format($order->vat_amount, 2) }}</span>
                </div>

                <!-- Grand Total -->
                <div class="flex justify-between text-xl font-bold text-gray-900 border-t border-gray-400 pt-3">
                    <span>Grand Total</span>
                    <span>AED {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-100 border-t flex justify-between items-center text-xs text-gray-500">
            <div>
                <p>Thank you for your business!</p>
                <p class="mt-1">For warranty claims, please present this invoice.</p>
            </div>
            <div>
                System Generated Invoice
            </div>
        </div>
    </div>
</div>
@endsection