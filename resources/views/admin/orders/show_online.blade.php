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
            <a href="{{ route('orders.edit', $order) }}" class="bg-emerald-600 text-white px-4 py-2 rounded shadow-sm hover:bg-emerald-700">
                <i class="fas fa-edit mr-2"></i> Edit Order
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

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
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-slate-800 text-white">
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest w-16">#</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest">Product Description</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest w-24">Qty</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest w-32">Unit Price</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest w-32">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $index => $item)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-5 text-sm font-bold text-slate-400 font-mono">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 overflow-hidden shrink-0 shadow-sm">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-box text-lg opacity-20"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-slate-900 line-clamp-2 leading-snug">{{ $item->product_name }}</div>
                                        @if($item->product && $item->product->sku)
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">SKU</span>
                                                <span class="text-[10px] font-bold text-slate-600 font-mono bg-slate-100 px-1.5 py-0.5 rounded">{{ $item->product->sku }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-50 border border-slate-100 text-sm font-black text-slate-700 shadow-inner">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right text-sm font-bold text-slate-500">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="text-sm font-black text-slate-900">{{ number_format($item->subtotal, 2) }}</div>
                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">{{ settings('currency_symbol', 'AED') }}</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <!-- Financial Summary -->
                <div class="bg-slate-50 px-8 py-8 border-t border-slate-200">
                    <div class="flex justify-end">
                        <div class="w-full md:w-80 space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Subtotal</span>
                                <span class="font-bold text-slate-700">{{ number_format($order->subtotal, 2) }} <span class="text-[10px] text-slate-400 ml-1">AED</span></span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">VAT</span>
                                    <span class="bg-slate-200 text-slate-600 text-[9px] font-black px-1.5 py-0.5 rounded tracking-tighter">5%</span>
                                </div>
                                <span class="font-bold text-slate-700">{{ number_format($order->vat_amount, 2) }} <span class="text-[10px] text-slate-400 ml-1">AED</span></span>
                            </div>
                            <div class="pt-4 mt-2 border-t-2 border-slate-200 flex justify-between items-end">
                                <div>
                                    <span class="block font-black text-slate-900 uppercase tracking-[0.2em] text-[11px]">Total Amount</span>
                                    <span class="text-[9px] text-emerald-600 font-bold uppercase mt-1 tracking-widest">Payment {{ $order->status == 'completed' ? 'Received' : 'Pending' }}</span>
                                </div>
                                <div class="text-right leading-none">
                                    <span class="block text-2xl font-black text-slate-900">{{ number_format($order->total, 2) }}</span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">AED</span>
                                </div>
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
                            <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
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
                        <textarea name="comment" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="Add a note about this change..."></textarea>
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
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Status</span>
                    @if($order->status == 'completed' || $order->payment_method == 'rakbank')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @endif
                </div>

                @if($order->payment_method === 'rakbank')
                <div class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-500">Transaction ID</span>
                        @if($order->transaction_id)
                            <span class="text-xs font-mono font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded select-all">{{ $order->transaction_id }}</span>
                        @else
                            <span class="text-xs text-gray-400 italic">Not recorded</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-xs text-gray-500">Gateway Order ID</span>
                        @if($order->gateway_order_id)
                            <span class="text-xs font-mono font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded select-all">{{ $order->gateway_order_id }}</span>
                        @else
                            <span class="text-xs text-gray-400 italic">{{ $order->invoice_no }}</span>
                        @endif
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">
                        Use these IDs to verify in the <strong>RAKBANK Merchant Portal</strong>.
                    </p>
                </div>
                @endif
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
