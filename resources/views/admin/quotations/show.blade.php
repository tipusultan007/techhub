@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    
    <div class="mb-6 flex justify-between items-center no-print">
        <a href="{{ route('quotations.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <div class="flex gap-2">
            <a href="{{ route('quotations.print', $quotation->id) }}" target="_blank" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-print mr-1"></i> Print
            </a>
            <a href="{{ route('quotations.download-pdf', $quotation->id) }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
            
            @if($quotation->items->sum('remaining_qty') > 0)
            <a href="{{ route('quotations.challan.create', $quotation->id) }}" class="px-4 py-2 bg-[#d97706] text-white rounded hover:bg-[#b45309]">
                <i class="fas fa-truck mr-1"></i> Prepare Challan
            </a>
            @endif

            @if($quotation->status == 'submitted')
            <a href="{{ route('quotations.edit', $quotation->id) }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-orange-600 transition flex items-center gap-2">
                <i class="fas fa-edit"></i> Edit Quotation
            </a>
            @endif
            @if($quotation->status == 'submitted')
            <button type="button"
                onclick="confirmConversion({{ $quotation->id }}, '{{ $quotation->quotation_no }}', '{{ $quotation->customer_name }}', '{{ number_format($quotation->total, 2) }}', {{ $quotation->items->count() }})"
                class="bg-green-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-shopping-cart"></i> Convert to Sale
            </button>
            <form id="convert-form-{{ $quotation->id }}" action="{{ route('quotations.convert', $quotation->id) }}" method="POST" class="hidden">
                @csrf
            </form>
            @endif
        </div>
    </div>

    <!-- PRINTABLE AREA -->
    <div id="quotation-print-area" class="bg-white p-10 border shadow-sm rounded-xl">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-10 border-b pb-6">
            <div>
                 @if(settings('site_logo'))
                        <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" style="max-height: 60px; margin-bottom: 5px;">
                    @else
                        <div class="shop-name">{{ settings('shop_name', 'Tech Hub Rak') }}</div>
                    @endif
                    <div class="mt-4 text-sm text-gray-600 space-y-0.5">
                    <p class="whitespace-pre-line">{{ settings('shop_address', 'Computer Street, Bur Dubai, UAE') }}</p>
                    <p>Phone: {{ settings('shop_phone', settings('contact_phone', '+971 4 000 0000')) }}</p>
                    <p>Email: {{ settings('contact_email', 'sales@techhubrak.ae') }}</p>
                    <p>TRN: {{ settings('shop_trn', '100XXXXXXXXXXXX') }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-black text-blue-600 mb-2">QUOTATION</h2>
                <div class="text-sm font-bold text-gray-700">
                    <p>Number: <span class="text-slate-900 font-mono">{{ $quotation->quotation_no }}</span></p>
                    <p>Date: <span class="text-slate-900">{{ ($quotation->date ?? $quotation->created_at)->format('d M, Y') }}</span></p>
                    <p>Valid Until: <span class="text-slate-900">{{ $quotation->expiry_date?->format('d M, Y') }}</span></p>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="grid grid-cols-2 gap-10 mb-10">
            <div>
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Customer Details</h4>
                <div class="text-sm font-bold text-gray-800">
                    <p class="text-lg">{{ $quotation->customer_name }}</p>
                    @if($quotation->customer)
                        <p class="mt-1"><i class="fas fa-phone mr-1 text-gray-400"></i> {{ $quotation->customer->phone }}</p>
                        <p><i class="fas fa-envelope mr-1 text-gray-400"></i> {{ $quotation->customer->email }}</p>
                        @if($quotation->customer->trn_number)
                            <p class="mt-1">TRN: {{ $quotation->customer->trn_number }}</p>
                        @endif
                    @endif
                </div>
            </div>
            <div class="text-right">
                <h4 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Quotation Status</h4>
                <div class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase {{ $quotation->status == 'submitted' ? 'bg-yellow-100 text-yellow-800' : ($quotation->status == 'converted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                    {{ $quotation->status }}
                </div>
                @if($quotation->status == 'converted' && $quotation->order)
                    <p class="text-xs text-gray-500 mt-2 font-bold uppercase">Converted to Invoice:</p>
                    <p class="text-sm font-black text-blue-600">{{ $quotation->order->invoice_no }}</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left mb-10 border-y">
            <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3 text-center w-12">#</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3 text-center">Qty</th>
                    <th class="px-4 py-3 text-right">Rate</th>
                    <th class="px-4 py-3 text-right">Tax %</th>
                    <th class="px-4 py-3 text-right">Tax</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @foreach($quotation->items as $item)
                <tr class="border-b last:border-0 hover:bg-gray-50">
                    <td class="px-4 py-4 text-center font-bold text-gray-400">{{ $loop->iteration }}</td>
                    <td class="px-4 py-4">
                        <div class="font-medium text-xs text-gray-900">{{ $item->product_name }}</div>
                    </td>
                    <td class="px-4 py-4 text-center font-bold">{{ number_format($item->quantity, 3) }}</td>
                    <td class="px-4 py-4 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-4 py-4 text-right font-mono">{{ number_format($item->tax_rate, 2) }}</td>
                    <td class="px-4 py-4 text-right font-mono">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="px-4 py-4 text-right font-mono font-bold">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Wrap -->
        <div class="flex justify-end">
            <div class="w-64 space-y-2">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-bold">AED {{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                @if($quotation->discount > 0)
                <div class="flex justify-between text-sm text-red-600 font-bold italic">
                    <span>Discount</span>
                    <span>- AED {{ number_format($quotation->discount, 2) }}</span>
                </div>
                @endif
                @php
                    $groupedTaxes = $quotation->items->groupBy('tax_rate');
                @endphp
                @foreach($groupedTaxes as $rate => $items)
                    @php
                        $taxAmount = $items->sum('tax_amount');
                        if($taxAmount <= 0) continue;
                        $label = $rate == 0 ? 'Zero Rate (0%)' : ($rate == 5 ? 'VAT (5%)' : "Tax ($rate%)");
                    @endphp
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>{{ $label }}</span>
                        <span class="font-bold">AED {{ number_format($taxAmount, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between text-xl font-black text-slate-900 border-t pt-2 mt-2">
                    <span>Total</span>
                    <span class="text-blue-600">AED {{ number_format($quotation->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="mt-20 border-t pt-8">
            <h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Notes</h5>
            <div class="text-[10px] text-gray-500 space-y-1 font-bold leading-relaxed">
                @if(settings('quotation_notes'))
                    {!! nl2br(e(settings('quotation_notes'))) !!}
                @else
                    <ul class="space-y-1">
                        <li>1. Prices are valid for 15 days from the date of quotation.</li>
                        <li>2. Items are subject to availability at the time of order confirmation.</li>
                        <li>3. Standard warranty applies to all electronic items unless otherwise specified.</li>
                        <li>4. This is a computer-generated document and does not require a physical signature.</li>
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    .max-w-4xl { max-width: 100% !important; width: 100% !important; margin: 0 !important; }
    #quotation-print-area { border: none !important; box-shadow: none !important; padding: 0 !important; }
    @page { margin: 1cm; }
}
</style>
@push('scripts')
<script>
    function confirmConversion(id, quNo, customer, total, itemsCount) {
        Swal.fire({
            title: 'Convert to Sale?',
            html: `
                <div class="text-left bg-gray-50 p-4 rounded-lg border border-gray-200 mt-4">
                    <p class="text-sm text-gray-600 mb-1 font-bold uppercase tracking-wider">Quotation Summary</p>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Quotation #:</span>
                        <span class="font-bold text-gray-800">${quNo}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Customer:</span>
                        <span class="font-bold text-gray-800">${customer}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Total Items:</span>
                        <span class="font-bold text-gray-800">${itemsCount}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-gray-700 font-bold uppercase">Grand Total:</span>
                        <span class="font-black text-blue-600 text-lg">AED ${total}</span>
                    </div>
                </div>
                <p class="text-xs text-red-500 mt-4 font-bold italic"><i class="fas fa-exclamation-triangle mr-1"></i> Stock will be deducted upon conversion.</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#374151',
            confirmButtonText: '<i class="fas fa-check-circle mr-2"></i> Yes, Convert Now',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'px-6 py-2.5 rounded-lg font-bold',
                cancelButton: 'px-6 py-2.5 rounded-lg font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('convert-form-' + id).submit();
            }
        });
    }
</script>
@endpush
@endsection
