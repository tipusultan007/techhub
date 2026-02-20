@extends('layouts.admin')

@section('header', 'Invoice Details')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
        <div class="flex gap-3">
            <a href="{{ route('orders.edit', $order) }}" class="bg-emerald-600 text-white px-5 py-2 rounded shadow hover:bg-emerald-700 font-bold transition">
                <i class="fas fa-edit mr-2"></i> Edit Order
            </a>
            <a href="{{ route('orders.print', $order) }}" target="_blank" class="bg-gray-800 text-white px-5 py-2 rounded shadow hover:bg-gray-700 font-bold transition">
                <i class="fas fa-print mr-2"></i> Print Receipt
            </a>
            <a href="{{ route('orders.download-pdf', $order) }}" class="bg-blue-600 text-white px-5 py-2 rounded shadow hover:bg-blue-700 font-bold transition">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        
        <!-- Invoice Header -->
        <div class="px-8 py-6 border-b bg-gray-50 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
                     {{ $order->vat_amount > 0 ? 'INVOICE' : 'SALES RECEIPT' }}
                </h1>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p>Invoice #: <span class="font-mono font-bold text-gray-900">{{ $order->invoice_no }}</span></p>
                    @if($order->po_number)
                        <p>PO #: <span class="font-mono font-bold text-gray-900 uppercase">{{ $order->po_number }}</span></p>
                    @endif
                    <p>Date: {{ $order->created_at->format('d M Y, h:i A') }}</p>
                    <p>Status: <span class="px-2 py-0.5 rounded text-xs font-bold uppercase {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $order->status }}</span></p>
                    @if($order->hasMedia('attachments'))
                        <div class="mt-4 no-print">
                            <button onclick="openAttachmentModal()" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-xl border border-blue-100 shadow-sm font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all transform active:scale-95">
                                <i class="fas fa-paperclip mr-2"></i> View Attachments ({{ $order->getMedia('attachments')->count() }})
                            </button>
                        </div>

                        <!-- Attachment Modal -->
                        <div id="attachmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4 no-print text-left">
                            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100">
                                <div class="px-8 py-6 border-b bg-gray-50 flex justify-between items-center">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800 tracking-tight">Invoice Attachments</h3>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total Files: {{ $order->getMedia('attachments')->count() }}</p>
                                    </div>
                                    <button onclick="closeAttachmentModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors shadow-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="p-8 max-h-[60vh] overflow-y-auto space-y-3">
                                    @foreach($order->getMedia('attachments') as $media)
                                        <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100 group hover:border-blue-200 transition-colors">
                                            <div class="flex items-center gap-4 truncate text-left">
                                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                                    <i class="fas fa-file-invoice"></i>
                                                </div>
                                                <div class="truncate">
                                                    <p class="text-xs font-bold text-gray-700 truncate text-left">{{ $media->file_name }}</p>
                                                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5 text-left">{{ number_format($media->size / 1024, 0) }} KB</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ $media->getUrl() }}" target="_blank" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                    <i class="fas fa-eye text-[10px]"></i>
                                                </a>
                                                <a href="{{ $media->getUrl() }}" download class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-800 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                    <i class="fas fa-download text-[10px]"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="px-8 py-6 bg-gray-50 border-t flex justify-end">
                                    <button onclick="closeAttachmentModal()" class="px-6 py-2.5 rounded-xl bg-gray-800 text-white font-bold text-xs hover:bg-gray-900 transition-all shadow-lg active:scale-95 uppercase tracking-widest">
                                        Done
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <h3 class="font-bold text-lg text-gray-700">{{ settings('shop_name', 'Tech Hub UAE') }}</h3>
                <p class="text-sm text-gray-500">{{ settings('shop_address', 'Dubai, UAE') }}</p>
                <p class="text-sm text-gray-500">Phone: {{ settings('shop_phone') }}</p>
                <p class="text-sm text-gray-500">Email: {{ settings('contact_email') }}</p>
                <p class="text-sm font-bold text-gray-600 mt-1">TRN: {{ settings('shop_trn', '100200300400500') }}</p>
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
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Tax (%)</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Tax Amount</th>
                    <th class="px-8 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-8 py-4">
                        <div class="text-sm font-regular text-gray-800">{{ $item->product_name }}</div>
                        
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
                        {{ $item->quantity + 0 }}
                    </td>
                    <td class="px-8 py-4 text-sm text-right text-gray-600">
                        {{ number_format($item->tax_rate, 2) }}%
                    </td>
                    <td class="px-8 py-4 text-sm text-right text-gray-600">
                        {{ number_format($item->tax_amount, 2) }}
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
                    <span>Net Amount</span>
                    <span>AED {{ number_format($order->subtotal, 2) }}</span>
                </div>
                @php
                    $groupedTaxes = $order->items->groupBy('tax_rate');
                @endphp
                @foreach($groupedTaxes as $rate => $items)
                    @php
                        $taxAmount = $items->sum('tax_amount');
                        if($taxAmount <= 0) continue;
                        $label = $rate == 0 ? 'Zero Rate (0%)' : ($rate == 5 ? 'VAT (5%)' : "Tax ($rate%)");
                    @endphp
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>{{ $label }}</span>
                        <span>AED {{ number_format($taxAmount, 2) }}</span>
                    </div>
                @endforeach

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

@section('scripts')
<script>
    function openAttachmentModal() {
        $('#attachmentModal').removeClass('hidden').addClass('flex');
        $('body').addClass('overflow-hidden');
    }

    function closeAttachmentModal() {
        $('#attachmentModal').addClass('hidden').removeClass('flex');
        $('body').removeClass('overflow-hidden');
    }

    // Close on escape
    $(document).keyup(function(e) {
        if (e.key === "Escape") closeAttachmentModal();
    });
    
    // Close on click outside
    $('#attachmentModal').click(function(e) {
        if (e.target === this) closeAttachmentModal();
    });
</script>
@endsection