@extends('layouts.admin')

@section('header', 'Incomplete Order Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('incomplete-orders.index') }}" class="text-sm text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
        <div class="flex gap-2">
            @if($order->status == 'completed' && $order->order_id)
                <a href="{{ route('orders.show', $order->order_id) }}" class="px-4 py-2 bg-green-600 text-white rounded text-sm font-bold shadow hover:bg-green-700">
                    View Real Order #{{ $order->order_id }}
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <div class="p-6 border-b bg-gray-50 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Incomplete Order #{{ $order->invoice_no }}</h2>
                <p class="text-sm text-gray-500">Created on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="text-right">
                @if($order->status == 'completed')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold uppercase border border-green-200">Payment Completed</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold uppercase border border-red-200">Abandoned / Pending</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <!-- Customer Information -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-700 border-b pb-2 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-500"></i> Customer Details
                </h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="text-gray-500 font-medium">Name:</div>
                    <div class="text-gray-900 font-bold">{{ $order->customer_data['first_name'] ?? '' }} {{ $order->customer_data['last_name'] ?? '' }}</div>
                    
                    <div class="text-gray-500 font-medium">Email:</div>
                    <div class="text-gray-900">{{ $order->customer_data['email'] ?? 'N/A' }}</div>
                    
                    <div class="text-gray-500 font-medium">Phone:</div>
                    <div class="text-gray-900 font-bold">{{ $order->customer_data['phone'] ?? 'N/A' }}</div>
                    
                    <div class="text-gray-500 font-medium">Address:</div>
                    <div class="text-gray-900">{{ $order->customer_data['address'] ?? '' }}, {{ $order->customer_data['city'] ?? '' }}</div>
                </div>
            </div>

            <!-- Technical Tracing -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-700 border-b pb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Trace Information
                </h3>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="text-gray-500 font-medium">Customer IP:</div>
                    <div class="text-gray-900 font-mono text-xs">{{ $order->customer_ip }}</div>
                    
                    <div class="text-gray-500 font-medium">Visitor IP:</div>
                    <div class="text-gray-900 font-mono text-xs">{{ $order->visitor_ip }}</div>
                    
                    <div class="text-gray-500 font-medium">Payment Method:</div>
                    <div class="text-gray-900 uppercase font-bold">{{ $order->payment_method }}</div>

                    <div class="text-gray-500 font-medium">Applied Coupon:</div>
                    <div class="text-gray-900 font-bold text-blue-600">{{ $order->coupon_data['code'] ?? 'None' }}</div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-6 pt-0">
            <h3 class="font-bold text-gray-700 border-b pb-2 mb-4 flex items-center">
                <i class="fas fa-shopping-cart mr-2 text-blue-500"></i> Cart Items
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-bold">
                        <tr>
                            <th class="px-4 py-2 border">Product</th>
                            <th class="px-4 py-2 border text-center">Qty</th>
                            <th class="px-4 py-2 border text-right">Price</th>
                            <th class="px-4 py-2 border text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->cart_data as $item)
                        <tr>
                            <td class="px-4 py-2 border">
                                <div class="font-bold">{{ $item['name'] }}</div>
                                <div class="text-[10px] text-gray-400">ID: #{{ $item['product_id'] }}</div>
                            </td>
                            <td class="px-4 py-2 border text-center font-bold">{{ $item['quantity'] }}</td>
                            <td class="px-4 py-2 border text-right">{{ number_format($item['price'], 2) }}</td>
                            <td class="px-4 py-2 border text-right font-bold">{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right border uppercase text-xs">Subtotal (Net)</td>
                            <td class="px-4 py-2 text-right border">{{ number_format($order->totals_data['subtotal'], 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right border uppercase text-xs">VAT Amount</td>
                            <td class="px-4 py-2 text-right border">{{ number_format($order->totals_data['vat'], 2) }}</td>
                        </tr>
                        @if($order->totals_data['discount'] > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right border uppercase text-xs text-red-600">Discount</td>
                            <td class="px-4 py-2 text-right border text-red-600">-{{ number_format($order->totals_data['discount'], 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right border uppercase text-xs">Shipping</td>
                            <td class="px-4 py-2 text-right border">{{ number_format($order->totals_data['shipping'], 2) }}</td>
                        </tr>
                        <tr class="bg-blue-600 text-white text-lg">
                            <td colspan="3" class="px-4 py-3 text-right border uppercase">Grand Total (AED)</td>
                            <td class="px-4 py-3 text-right border">{{ number_format($order->totals_data['total'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
