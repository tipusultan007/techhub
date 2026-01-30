@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center no-print">
        <a href="{{ route('quotations.show', $challan->quotation_id) }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Quotation
        </a>
        <div class="flex gap-2">
            <a href="{{ route('delivery-challans.print', $challan->id) }}" target="_blank" class="bg-slate-800 text-white px-4 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ route('delivery-challans.pdf', $challan->id) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    <!-- DOCUMENT AREA -->
    <div class="bg-white p-10 border shadow-sm rounded-xl">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-10 border-b pb-6">
            <div>
                 @if(settings('site_logo'))
                    <img src="{{ settings('site_logo') }}" alt="{{ settings('site_name') }}" style="max-height: 60px; margin-bottom: 5px;">
                @else
                    <div class="text-2xl font-black text-slate-800">{{ settings('shop_name', 'Tech Hub Rak') }}</div>
                @endif
                <div class="mt-4 text-sm text-gray-600 space-y-0.5">
                    <p class="whitespace-pre-line">{{ settings('shop_address', 'Computer Street, Bur Dubai, UAE') }}</p>
                    <p>Phone: {{ settings('shop_phone', settings('contact_phone', '+971 4 000 0000')) }}</p>
                    <p>Email: {{ settings('contact_email', 'sales@techhubrak.ae') }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-black text-gray-800 mb-2">DELIVERY CHALLAN</h2>
                <div class="text-sm font-bold text-gray-700">
                    <p>Challan #: <span class="text-slate-900 font-mono">{{ $challan->challan_number }}</span></p>
                    <p>Date: <span class="text-slate-900">{{ \Carbon\Carbon::parse($challan->date)->format('d M, Y') }}</span></p>
                    <p>Ref Quotation: <span class="text-slate-900">{{ $challan->quotation->quotation_no }}</span></p>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="mb-10">
            <h4 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2">To:</h4>
            <div class="text-sm font-bold text-gray-800">
                <p class="text-lg">{{ $challan->customer->name ?? $challan->quotation->customer_name }}</p>
                @if($challan->customer)
                    <p class="mt-1"><i class="fas fa-phone mr-1 text-gray-400"></i> {{ $challan->customer->phone }}</p>
                    <p><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i> {{ $challan->customer->address }}</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left mb-10 border-y">
            <thead class="bg-gray-50 text-[10px] font-black uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3 w-16 text-center">#</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3 text-center w-24">Qty</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @foreach($challan->items as $index => $item)
                <tr class="border-b last:border-0 hover:bg-gray-50">
                    <td class="px-4 py-4 text-center text-gray-400">{{ $index + 1 }}</td>
                    <td class="px-4 py-4">
                        <div class="font-bold text-gray-900">{{ $item->product_name }}</div>
                    </td>
                    <td class="px-4 py-4 text-center font-bold text-gray-900">{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($challan->note)
        <div class="mb-10 bg-gray-50 p-4 rounded-lg border border-gray-100">
            <h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Note</h5>
            <p class="text-sm text-gray-600 italic">{{ $challan->note }}</p>
        </div>
        @endif

        <!-- Footer / Signature -->
        <div class="mt-20 pt-8 flex justify-between items-end">
            <div class="text-center w-48">
                <div class="border-b border-gray-300 pb-2 mb-2"></div>
                <p class="text-xs font-bold text-gray-500 uppercase">Receiver's Signature</p>
            </div>
            <div class="text-center w-48">
                <div class="border-b border-gray-300 pb-2 mb-2"></div>
                <p class="text-xs font-bold text-gray-500 uppercase">Authorized Signature</p>
            </div>
        </div>
    </div>
</div>
@endsection
