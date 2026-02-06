@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('quotations.show', $quotation->id) }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Quotation
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-truck text-[#d97706]"></i> Prepare Delivery Challan
        </h2>

        <!-- Quotation Info Summary -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100 text-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="block text-xs uppercase text-gray-500 font-bold">Quotation Reference</span>
                    <span class="font-mono font-bold text-gray-800 text-lg">{{ $quotation->quotation_no }}</span>
                </div>
                <div>
                    <span class="block text-xs uppercase text-gray-500 font-bold">Customer</span>
                    <div class="font-bold text-gray-800">{{ $quotation->customer_name }}</div>
                    @if($quotation->customer)
                        <div class="text-gray-500 text-xs mt-0.5">
                            <i class="fas fa-phone mr-1"></i> {{ $quotation->customer->phone }}
                        </div>
                    @endif
                </div>
                <div>
                    <span class="block text-xs uppercase text-gray-500 font-bold">Quotation Date</span>
                    <span class="font-bold text-gray-800">{{ ($quotation->date ?? $quotation->created_at)->format('d M, Y') }}</span>
                </div>
                @if($quotation->po_number)
                <div>
                    <span class="block text-xs uppercase text-gray-500 font-bold">PO Number</span>
                    <span class="font-bold text-gray-800 font-mono">{{ $quotation->po_number }}</span>
                </div>
                @endif
            </div>
        </div>

        <form action="{{ route('quotations.challan.store', $quotation->id) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">PO# (Optional)</label>
                    <input type="text" name="po_number" value="{{ $quotation->po_number }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50" placeholder="Enter PO#">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Note (Optional)</label>
                    <textarea name="note" rows="1" class="w-full border rounded-lg px-3 py-2 bg-gray-50" placeholder="Driver name, vehicle no, etc."></textarea>
                </div>
            </div>

            <table class="w-full text-left mb-6">
                <thead class="bg-gray-100 text-xs font-bold uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Item Description</th>
                        <th class="px-4 py-3 text-center">Ordered</th>
                        <th class="px-4 py-3 text-center">Delivered</th>
                        <th class="px-4 py-3 text-center">Remaining</th>
                        <th class="px-4 py-3 text-center w-32">Deliver Now</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y">
                    @foreach($quotation->items as $item)
                        @if($item->remaining_qty > 0)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $item->product_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $item->delivered_qty }}</td>
                            <td class="px-4 py-3 text-center font-bold text-[#d97706]">{{ $item->remaining_qty }}</td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <input type="number" 
                                       name="items[{{ $loop->index }}][qty]" 
                                       value="{{ $item->remaining_qty }}" 
                                       min="0" 
                                       max="{{ $item->remaining_qty }}" 
                                       class="w-full border-none p-0 text-center font-bold bg-transparent outline-none">
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="submit" class="bg-[#d97706] hover:bg-[#b45309] text-white px-6 py-2 rounded-lg font-bold transition flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Create Challan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
