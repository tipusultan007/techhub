@extends('layouts.admin')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Online Order #{{ $order->invoice_no }}
            </h2>
            <span class="text-xs text-gray-500">
            Placed on {{ $order->created_at->format('d M Y, h:i A') }}
        </span>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('orders.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm hover:bg-gray-50">
                Back to List
            </a>
            <a href="{{ route('orders.print', $order) }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded shadow-sm hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </a>
            <a href="{{ route('orders.download-pdf', $order) }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow-sm hover:bg-indigo-700">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- === LEFT COLUMN: ORDER DETAILS === -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Shipping & Customer Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Customer & Shipping</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Online</span>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Details -->
                    <div>
                        <h4 class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-3">Contact Info</h4>
                        <div class="flex items-start space-x-3">
                            <div class="bg-gray-100 p-2 rounded-full"><i class="fas fa-user text-gray-500"></i></div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
                                <p class="text-sm text-gray-600">{{ $order->guest_email }}</p>
                                <p class="text-sm text-gray-600">{{ $order->guest_phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div>
                        <h4 class="text-xs uppercase font-bold text-gray-400 tracking-wider mb-3">Delivery Address</h4>
                        <div class="flex items-start space-x-3">
                            <div class="bg-gray-100 p-2 rounded-full"><i class="fas fa-map-marker-alt text-gray-500"></i></div>
                            <div>
                                <p class="text-sm text-gray-800 leading-relaxed">
                                    {{ $order->shipping_address }}<br>
                                    @if($order->shipping_area) {{ $order->shipping_area }}, @endif
                                    <span class="font-semibold">{{ $order->shipping_city }}</span><br>
                                    United Arab Emirates
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Order Items Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Order Items</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded border flex items-center justify-center text-gray-400">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                        @if($item->product && $item->product->sku)
                                            <div class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-800">
                                {{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <!-- Financial Summary -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/2 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>{{ number_format($order->subtotal, 2) }} AED</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>VAT (5%)</span>
                                <span>{{ number_format($order->vat_amount, 2) }} AED</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-gray-900 border-t border-gray-300 pt-2 mt-2">
                                <span>Total Amount</span>
                                <span>{{ number_format($order->total, 2) }} AED</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === RIGHT SIDEBAR: ACTIONS & HISTORY === -->
        <div class="lg:col-span-1 space-y-6">

            <!-- 1. Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-100">Update Status</h3>

                <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Order Status</label>
                        <div class="relative">
                            <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="returned" {{ $order->status == 'returned' ? 'selected' : '' }}>Returned</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Comment / Note</label>
                        <textarea name="comment" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Add a note about this change..."></textarea>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Order
                    </button>
                </form>
            </div>

            <!-- 2. Payment Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-100">Payment Info</h3>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Method</span>
                    <span class="font-semibold text-gray-800 uppercase">{{ $order->payment_method }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status</span>
                    @if($order->status == 'completed' || $order->payment_method == 'card')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @endif
                </div>
            </div>

            <!-- 3. Activity Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-100">Order History</h3>

                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @forelse($order->history as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                        <i class="fas fa-history text-white text-xs"></i>
                                    </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">{{ ucfirst($log->status) }}</p>
                                                @if($log->comment)
                                                    <p class="text-xs text-gray-500 mt-0.5">"{{ $log->comment }}"</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <!-- Default Entry -->
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-900 font-medium">Order Placed</p>
                                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M, h:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
@endsection
