@extends('layouts.admin')

@section('header', 'Purchase Order Details')

@section('content')
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-12 py-8">

        <!-- Top Navigation & Actions -->
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('purchases.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Back to History
            </a>

            <div class="flex gap-3">
                @if($purchase->status !== 'completed')
                    <form action="{{ route('purchases.mark_completed', $purchase->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark all items as received?')">
                        @csrf
                        <button type="submit" class="bg-emerald-600 text-white px-5 py-2.5 rounded-lg shadow-sm hover:bg-emerald-700 font-bold text-sm flex items-center transition-all">
                            <i class="fas fa-check-double mr-2"></i> Mark All as Received
                        </button>
                    </form>
                @endif

                @php
                    $needsSerials = $purchase->items->contains(fn($item) => $item->product->has_serial_number);
                @endphp

                @if ($needsSerials && $purchase->status !== 'pending')
                    <a href="{{ route('purchases.serials', $purchase->id) }}"
                        class="bg-amber-500 text-white px-5 py-2.5 rounded-lg shadow-sm hover:bg-amber-600 font-bold text-sm flex items-center transition-all">
                        <i class="fas fa-barcode mr-2"></i> Register Serials
                    </a>
                @endif

                <a href="{{ route('purchases.download_pdf', $purchase->id) }}"
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg shadow-sm hover:bg-indigo-700 font-bold text-sm flex items-center transition-all">
                    <i class="fas fa-file-pdf mr-2"></i> Download PDF
                </a>

                <a href="{{ route('purchases.edit', $purchase->id) }}"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow-sm hover:bg-blue-700 font-bold text-sm flex items-center transition-all">
                    <i class="fas fa-edit mr-2"></i> Edit Order
                </a>

                <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank"
                    class="bg-slate-800 text-white px-5 py-2.5 rounded-lg shadow-sm hover:bg-slate-900 font-bold text-sm flex items-center transition-all">
                    <i class="fas fa-print mr-2"></i> Print PO
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Left Side: Order Details & Bulk Reception (Takes 3 columns on XL) -->
            <div class="xl:col-span-3 space-y-8">
                <!-- Main Content Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
                    <!-- Header -->
                    <div class="bg-slate-50/50 px-8 py-6 border-b flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight">PURCHASE ORDER</h1>
                            <p class="text-sm text-slate-500 mt-1">Ref: <span class="font-mono font-bold text-slate-700">{{ $purchase->reference_no }}</span></p>
                            <p class="text-sm text-slate-500">Date: {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs text-slate-400 uppercase font-black mb-2 antialiased">Current Status</span>
                            @if ($purchase->status === 'completed')
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i class="fas fa-check-circle mr-2"></i> COMPLETED
                                </span>
                            @elseif($purchase->status === 'partial_received')
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    <i class="fas fa-truck-loading mr-2"></i> PARTIAL RECEIVED
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200">
                                    <i class="fas fa-clock mr-2"></i> PENDING
                                </span>
                            @endif

                            @if($purchase->hasMedia('attachments'))
                                <div class="mt-4 flex flex-col gap-2 items-end no-print">
                                    <span class="text-[10px] font-black text-slate-400 uppercase">Attachments</span>
                                    <button onclick="openAttachmentModal()" 
                                            class="bg-blue-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-blue-700 flex items-center shadow-lg shadow-blue-500/20 transition-all transform active:scale-95">
                                        <i class="fas fa-paperclip mr-2"></i> View Attachments ({{ $purchase->getMedia('attachments')->count() }})
                                    </button>
                                </div>

                                <!-- Attachment Modal -->
                                <div id="attachmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4 no-print">
                                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100">
                                        <div class="px-8 py-6 border-b bg-slate-50 flex justify-between items-center">
                                            <div>
                                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Order Attachments</h3>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Total Files: {{ $purchase->getMedia('attachments')->count() }}</p>
                                            </div>
                                            <button onclick="closeAttachmentModal()" class="w-10 h-10 rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors shadow-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="p-8 max-h-[60vh] overflow-y-auto space-y-3">
                                            @foreach($purchase->getMedia('attachments') as $media)
                                                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:border-blue-200 transition-colors">
                                                    <div class="flex items-center gap-4 truncate">
                                                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </div>
                                                        <div class="truncate">
                                                            <p class="text-xs font-black text-slate-900 truncate">{{ $media->file_name }}</p>
                                                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">{{ number_format($media->size / 1024, 0) }} KB</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <a href="{{ $media->getUrl() }}" target="_blank" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                            <i class="fas fa-eye text-[10px]"></i>
                                                        </a>
                                                        <a href="{{ $media->getUrl() }}" download class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-800 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                            <i class="fas fa-download text-[10px]"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="px-8 py-6 bg-slate-50 border-t flex justify-end">
                                            <button onclick="closeAttachmentModal()" class="px-6 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition-all shadow-lg active:scale-95 uppercase tracking-widest">
                                                Done
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items Table / Reception Form -->
                    <form action="{{ route('purchases.bulk_receive', $purchase->id) }}" method="POST">
                        @csrf
                        <div class="px-8 py-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Products Ordered</h3>
                                @if($purchase->status !== 'completed')
                                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 font-bold text-sm transition-all transform active:scale-95 flex items-center">
                                        <i class="fas fa-download mr-3"></i> Apply Reception
                                    </button>
                                @endif
                            </div>

                            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50/50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest min-w-[300px]">Product Information</th>
                                            <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">Cost</th>
                                            <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">Ordered</th>
                                            <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">Tax %</th>
                                            <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">Tax</th>
                                            <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-24">Received</th>
                                            <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest w-28">Remaining</th>
                                            @if($purchase->status !== 'completed')
                                                <th class="px-6 py-4 text-center text-[10px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50/30 w-48">Receiving Now</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach ($purchase->items as $item)
                                            <tr class="hover:bg-slate-50/30 transition-colors">
                                                <td class="px-6 py-5">
                                                    <div class="text-sm font-bold text-slate-900 leading-tight">{{ $item->product->name }}</div>
                                                    <div class="flex items-center gap-2 mt-1.5 font-mono text-[10px] font-bold">
                                                        <span class="text-slate-400 uppercase">SKU:</span>
                                                        <span class="text-slate-600">{{ $item->variant ? $item->variant->sku : $item->product->sku }}</span>
                                                        @if($item->variant) 
                                                            <span class="mx-1 text-slate-300">|</span>
                                                            <span class="bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100 uppercase tracking-tighter">{{ $item->variant->variant_name }}</span> 
                                                        @php
                                                            $allReceived = false; // dummy for style
                                                        @endphp
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 text-sm text-right font-medium text-slate-600">{{ number_format($item->unit_cost, 2) }}</td>
                                                <td class="px-6 py-5 text-sm text-right font-medium text-slate-600">{{ $item->quantity }}</td>
                                                <td class="px-6 py-5 text-sm text-right font-medium text-slate-400">{{ number_format($item->tax_rate, 0) }}%</td>
                                                <td class="px-6 py-5 text-sm text-right font-medium text-slate-600">{{ number_format($item->tax_amount, 2) }}</td>
                                                <td class="px-6 py-5 text-sm text-right font-black text-emerald-600">{{ $item->received_quantity }}</td>
                                                <td class="px-6 py-5 text-sm text-right font-black {{ $item->remaining_quantity() > 0 ? 'text-rose-500' : 'text-slate-300' }}">
                                                    {{ $item->remaining_quantity() }}
                                                </td>
                                                
                                                @if($purchase->status !== 'completed')
                                                    <td class="px-6 py-5 text-center bg-indigo-50/5">
                                                        @if($item->remaining_quantity() > 0)
                                                            <div class="inline-flex flex-col items-center">
                                                                <div class="flex items-center shadow-sm border border-indigo-100 rounded-lg bg-white overflow-hidden focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
                                                                    <input type="number" 
                                                                           name="items[{{ $item->id }}][received_qty]" 
                                                                           placeholder="0"
                                                                           max="{{ $item->remaining_quantity() }}" 
                                                                           min="0" 
                                                                           class="w-20 border-0 p-2 text-sm text-center font-black text-indigo-600 focus:ring-0">
                                                                    <div class="bg-indigo-50 px-2 py-2 text-[9px] font-black text-indigo-500 border-l border-indigo-100 flex flex-col justify-center leading-none">
                                                                        <span>MAX</span>
                                                                        <span class="mt-0.5">{{ $item->remaining_quantity() }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="inline-flex items-center text-emerald-600 text-xs font-black uppercase tracking-tight">
                                                                <i class="fas fa-check-circle mr-1.5 opacity-70"></i> Full
                                                            </span>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($purchase->status !== 'completed')
                                <div class="mt-8 bg-slate-50/50 p-6 rounded-2xl border border-dashed border-slate-200">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-slate-400">
                                            <i class="fas fa-sticky-note text-sm"></i>
                                        </div>
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Internal Reception Notes</label>
                                    </div>
                                    <textarea name="notes" rows="2" 
                                              class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-300 transition-all bg-white" 
                                              placeholder="Any notes about the condition of the shipment or reason for partial quantity..."></textarea>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Purchasing Policy & Delivery Instructions -->
                <div class="bg-amber-50 rounded-2xl p-8 border border-amber-100 mt-8">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 shrink-0 flex items-center justify-center text-amber-600">
                            <i class="fas fa-file-contract text-xl"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3">Purchasing Policy & Delivery Instructions</h5>
                            <div class="text-xs text-amber-800 leading-relaxed font-medium space-y-1">
                                {!! nl2br(e(settings('purchase_policy', ""))) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Sidebar Info (Takes 1 column on XL) -->
            <div class="space-y-8">
                <!-- Supplier Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Supplier Details</h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="bg-indigo-50 w-12 h-12 rounded-xl text-indigo-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-building text-xl"></i>
                            </div>
                            <div>
                                <p class="font-black text-slate-900 leading-none">{{ $purchase->supplier->name }}</p>
                                <p class="text-xs text-slate-500 mt-2 font-medium">{{ $purchase->supplier->company_name }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3 pt-6 border-t border-slate-50">
                            <div class="flex items-center gap-3 text-slate-500">
                                <i class="fas fa-map-marker-alt text-slate-300 w-4"></i>
                                <span class="text-xs leading-relaxed">{{ $purchase->supplier->address }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-500">
                                <i class="fas fa-phone-alt text-slate-300 w-4"></i>
                                <span class="text-xs font-bold">{{ $purchase->supplier->phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="bg-slate-900 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-cash-register text-9xl transform -rotate-12"></i>
                    </div>
                    
                    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-8 border-b border-slate-800 pb-4">Order Totals</h3>
                    
                    <div class="space-y-5 relative z-10">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-widest">Net Amount</span>
                            <span class="font-mono text-sm">{{ number_format($purchase->total_cost - $purchase->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-widest">VAT</span>
                            <span class="text-rose-400 font-mono text-sm">+ {{ number_format($purchase->tax_amount, 2) }}</span>
                        </div>
                        
                        <div class="pt-6 mt-6 border-t border-slate-800">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-1">Grand Total</span>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs text-slate-500 font-bold">AED</span>
                                    <span class="text-3xl font-black font-mono tracking-tighter">{{ number_format($purchase->total_cost, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reception History -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Shipment Logs</h3>
                        <span class="bg-white px-2 py-0.5 rounded text-[10px] font-black text-slate-400 border border-slate-100">{{ $purchase->receptions->count() }}</span>
                    </div>
                    <div class="divide-y divide-slate-50 max-h-[500px] overflow-y-auto">
                        @forelse($purchase->receptions->sortByDesc('created_at') as $reception)
                            <div class="p-6 hover:bg-slate-50/50 transition-colors">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-xs font-black text-slate-900 tracking-tight">{{ $reception->reception_no }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">{{ $reception->created_at->format('d M Y') }} • {{ $reception->created_at->format('h:i A') }}</p>
                                    </div>
                                    <a href="{{ route('purchases.reception.print', [$purchase->id, $reception->id]) }}" 
                                       target="_blank"
                                       class="w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors"
                                       title="Print Slip">
                                        <i class="fas fa-print text-[10px]"></i>
                                    </a>
                                </div>
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    @foreach($reception->items as $recItem)
                                        <span class="px-2 py-1 rounded-md bg-white border border-slate-200 text-slate-600 text-[9px] font-bold shadow-sm">
                                            {{ \Illuminate\Support\Str::limit($recItem->poItem->product->name, 20) }} <span class="text-indigo-600 ml-1">×{{ $recItem->quantity }}</span>
                                        </span>
                                    @endforeach
                                </div>
                                @if($reception->notes)
                                    <div class="text-[10px] text-slate-500 italic bg-amber-50/50 p-2 rounded-lg border border-amber-100/50 mb-3">
                                        "{{ $reception->notes }}"
                                    </div>
                                @endif
                                <div class="flex items-center justify-end gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                    <span>By</span>
                                    <span class="text-slate-900 bg-slate-100 px-1.5 py-0.5 rounded">{{ $reception->received_by }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                    <i class="fas fa-box-open text-slate-200 text-2xl"></i>
                                </div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">No entries found</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Admin Info -->
                <div class="px-6 py-4 bg-slate-50 rounded-2xl text-center border border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                        Logged by {{ Auth::user()->name }}
                    </p>
                    <p class="text-[9px] text-slate-300 mt-1 font-mono">
                        {{ $purchase->created_at->format('d/m/Y H:i:s') }}
                    </p>
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
        // Restore body scroll if no other modals are open
        if (!$('#processingOverlay').is(':visible')) {
            $('body').removeClass('overflow-hidden');
        }
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
