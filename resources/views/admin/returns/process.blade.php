@extends('layouts.admin')

@section('header', 'Select Items to Return')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Order Information Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b bg-gray-50 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                    <i class="fas fa-file-invoice text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Order Information</h2>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Invoice #{{ $order->invoice_no }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Order Date</div>
                <div class="text-sm font-bold text-gray-700">{{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Customer</span>
                <span class="font-bold text-slate-800">{{ $order->customer_name }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Total Amount</span>
                <span class="font-bold text-slate-800">AED {{ number_format($order->total, 2) }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Payment Method</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-green-100 text-green-700 border border-green-200">
                    {{ $order->payment_method }}
                </span>
            </div>
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1">PO Number</span>
                <span class="font-bold text-slate-800">{{ $order->po_number ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Items to Return Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <form action="{{ route('returns.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <div class="p-6 border-b bg-gray-50 flex items-center gap-3">
                <div class="bg-green-100 text-green-600 p-2 rounded-lg">
                    <i class="fas fa-list-check"></i>
                </div>
                <h3 class="font-bold text-gray-800">Order Items</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left">Product / SKU</th>
                            <th class="px-6 py-4 text-center">Purchased</th>
                            <th class="px-6 py-4 text-center">Unit Price</th>
                            <th class="px-6 py-4 text-center w-32">Return Qty</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $item->product_name }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-mono">SKU: {{ $item->product->sku ?? 'N/A' }}</span>
                                    @if($item->serial_numbers)
                                        <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded font-mono">SN: {{ $item->serial_numbers }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-600">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-center text-slate-700">
                                AED {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="number" name="items[{{ $item->id }}][qty]" value="0" max="{{ $item->quantity }}" min="0"
                                    class="w-full border-2 border-slate-200 rounded-lg p-2 text-center focus:border-blue-500 outline-none transition font-bold qty-input" {{ $item->serial_numbers ? 'readonly' : '' }}>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <select name="items[{{ $item->id }}][status]" class="w-full border-2 border-slate-200 rounded-lg p-2 bg-white focus:border-blue-500 outline-none transition text-xs font-bold">
                                    <option value="restockable">Re-stockable</option>
                                    <option value="defective">Defective / Damaged</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">
                                AED {{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Reason for Return</label>
                    <textarea name="reason" rows="3" class="w-full border-2 border-slate-200 rounded-xl p-4 focus:border-blue-500 outline-none transition resize-none placeholder-slate-400" placeholder="Please specify why the items are being returned..."></textarea>
                </div>
                <div class="flex flex-col justify-end">
                    <button type="submit" class="w-full bg-green-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-green-700 shadow-lg active:scale-[0.98] transition flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle text-xl"></i>
                        Confirm Return & Process Refund
                    </button>
                    <a href="{{ url()->previous() }}" class="text-center mt-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition">
                        Cancel Processing
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection