@extends('layouts.admin')

@section('header', 'Reception Invoice: ' . $reception->reception_no)

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-10 shadow-lg border border-gray-100 rounded-xl printable-area">
        <!-- Header -->
        <div class="flex justify-between items-start mb-10 border-b pb-8">
            <div>
                <h1 class="text-4xl font-black text-blue-600 tracking-tighter">RECEPTION</h1>
                <p class="text-gray-400 font-mono text-sm mt-1">#{{ $reception->reception_no }}</p>
                <div class="mt-6 space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Received Date</p>
                    <p class="text-gray-900 font-bold border-l-2 border-blue-500 pl-3 leading-none">{{ $reception->created_at->format('d M, Y') }}</p>
                    <p class="text-[10px] text-gray-400 pl-3">{{ $reception->created_at->format('h:i A') }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="mb-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Related PO</p>
                    <p class="text-gray-900 font-mono font-bold">{{ $reception->purchaseOrder->reference_no }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Received By</p>
                    <p class="text-gray-900 font-bold italic">{{ $reception->received_by }}</p>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="grid grid-cols-2 gap-12 mb-12">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Supplier / Vendor</h3>
                <div class="text-gray-900 font-bold text-lg mb-1">{{ $reception->purchaseOrder->supplier->name }}</div>
                <div class="text-gray-600 text-sm italic mb-2">{{ $reception->purchaseOrder->supplier->company_name }}</div>
                <div class="text-gray-500 text-xs leading-relaxed max-w-xs">
                    <i class="fas fa-map-marker-alt mr-1 opacity-50"></i> {{ $reception->purchaseOrder->supplier->address }}
                </div>
                <div class="text-gray-500 text-xs mt-2">
                    <i class="fas fa-phone mr-1 opacity-50"></i> {{ $reception->purchaseOrder->supplier->phone }}
                </div>
            </div>
            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Ship To / Warehouse</h3>
                <p class="text-gray-900 font-bold">{{ settings('shop_name', 'TECH HUB') }}</p>
                <p class="text-gray-500 text-xs mt-1 leading-relaxed">{{ settings('shop_address', 'Dubai, United Arab Emirates') }}</p>
                <p class="text-gray-500 text-xs mt-1 italic"><i class="fas fa-envelope mr-1 opacity-50"></i> {{ settings('shop_email', 'info@techhubrak.ae') }}</p>
                <p class="text-gray-500 text-xs mt-1 italic"><i class="fas fa-phone mr-1 opacity-50"></i> {{ settings('contact_phone', 'info@techhubrak.ae') }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-12">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Received Items List</h3>
            <table class="min-w-full">
                <thead>
                    <tr class="border-b-2 border-gray-900">
                        <th class="py-4 text-left text-[10px] font-black text-gray-900 uppercase tracking-tighter">#</th>
                        <th class="py-4 text-left text-[10px] font-black text-gray-900 uppercase tracking-tighter">Product Description</th>
                        <th class="py-4 text-right text-[10px] font-black text-gray-900 uppercase tracking-tighter w-32">Qty Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reception->items as $index => $item)
                        <tr>
                            <td class="py-5 text-xs text-gray-400 font-mono">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}. </td>
                            <td class="py-5">
                                <div class="text-sm font-regular text-gray-900">{{ $item->poItem->product->name }}</div>
                                @if($item->poItem->variant)
                                    <div class="text-[10px] text-blue-600 font-bold uppercase mt-0.5">{{ $item->poItem->variant->variant_name }}</div>
                                @endif
                            </td>
                            
                            <td class="py-5 text-right font-medium text-gray-900 text-sm">
                                {{ $item->quantity }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="grid grid-cols-2 gap-12 mt-12 pt-12 border-t border-dashed border-gray-300">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Notes & Acknowledgement</h3>
                <p class="text-xs text-gray-600 italic leading-relaxed">
                    {{ $reception->notes ?? 'No special notes recorded for this reception.' }}
                </p>
            </div>
            <div class="flex flex-col items-end">
                <div class="w-48 border-b border-gray-900 pb-1 text-center">
                    <p class="text-[10px] text-gray-400 uppercase font-black mb-8 italic opacity-30">Authorized Signature</p>
                </div>
                <p class="text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-widest">Tech Hub Logistics Dept</p>
            </div>
        </div>

        
    </div>

    <style>
        .printable-area {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        @media print {
            body * { visibility: hidden; }
            .printable-area, .printable-area * { visibility: visible; }
            .printable-area { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>

    <div class="fixed bottom-8 right-8 no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white p-4 rounded-full shadow-2xl hover:bg-blue-700 transition-all transform hover:scale-110 active:scale-95">
            <i class="fas fa-print text-xl"></i>
        </button>
    </div>
@endsection
