@extends('layouts.admin')

@section('header', 'Credit Note Details')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('returns.index') }}" class="text-gray-600 hover:text-gray-900"><i class="fas fa-arrow-left mr-2"></i> Back to List</a>
        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700">
            <i class="fas fa-print mr-2"></i> Print Credit Note
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        
        <!-- Header -->
        <div class="px-8 py-6 border-b bg-gray-50 flex justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">CREDIT NOTE</h1>
                <p class="text-sm text-gray-500">Ref #: {{ $return->credit_note_no }}</p>
                <p class="text-sm text-gray-500">Date: {{ $return->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="text-right">
                <h3 class="font-bold text-gray-700">{{ $settings['shop_name'] ?? 'Tech Hub' }}</h3>
                <p class="text-xs text-gray-500">{{ $settings['shop_trn'] ?? 'TRN not set' }}</p>
            </div>
        </div>

        <!-- Customer & Order Info -->
        <div class="px-8 py-5 border-b">
            <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Details</h4>
            <div class="grid grid-cols-2 text-sm">
                <div>
                    <span class="text-gray-500">Customer:</span>
                    <span class="font-bold text-gray-800">{{ $return->originalOrder->customer_name }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Original Invoice:</span>
                    <a href="{{ route('orders.show', $return->originalOrder) }}" class="font-bold text-blue-600 hover:underline">
                        {{ $return->originalOrder->invoice_no }}
                    </a>
                </div>
            </div>
            @if($return->reason)
            <div class="mt-3 text-sm">
                <span class="text-gray-500">Reason for Return:</span>
                <span class="italic text-gray-800">{{ $return->reason }}</span>
            </div>
            @endif
        </div>

        <!-- Items Table -->
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-8 py-3 text-left text-xs font-bold text-gray-500 uppercase">Returned Item</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase">Unit Price</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase">Qty</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($return->items as $item)
                <tr>
                    <td class="px-8 py-4">
                        <div class="text-sm font-bold text-gray-800">{{ $item->product->name }}</div>
                        @if($item->variant)
                            <div class="text-xs text-gray-500">{{ $item->variant->variant_name }}</div>
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

        <!-- Totals -->
        <div class="px-8 py-6 bg-gray-50 flex justify-end">
            <div class="w-1/2 md:w-1/3 space-y-2">
                <div class="flex justify-between text-xl font-bold text-red-600 border-t border-gray-300 pt-2 mt-2">
                    <span>Total Refund</span>
                    <span>- AED {{ number_format($return->total_refund, 2) }}</span>
                </div>
                <p class="text-xs text-gray-500 text-right">VAT amount reversed accordingly.</p>
            </div>
        </div>

    </div>
</div>
@endsection