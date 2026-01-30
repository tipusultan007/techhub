@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('delivery-challans.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-edit text-[#d97706]"></i> Edit Delivery Challan <span class="text-gray-400 text-lg ml-2">#{{ $challan->challan_number }}</span>
        </h2>

        <form action="{{ route('delivery-challans.update', $challan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="{{ $challan->date }}" class="w-full border rounded-lg px-3 py-2 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Note (Optional)</label>
                    <textarea name="note" rows="1" class="w-full border rounded-lg px-3 py-2 bg-gray-50">{{ $challan->note }}</textarea>
                </div>
            </div>

            <table class="w-full text-left mb-6">
                <thead class="bg-gray-100 text-xs font-bold uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Item Description</th>
                        <th class="px-4 py-3 text-center">In This Challan</th>
                        <th class="px-4 py-3 text-center">Addtl. Available</th>
                        <th class="px-4 py-3 text-center w-32">Revised Qty</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y">
                    @foreach($challan->items as $item)
                        @php
                            $maxQty = $item->quantity + $item->quotationItem->remaining_qty;
                        @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $item->product_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-center text-green-600 font-bold">+{{ $item->quotationItem->remaining_qty }}</td>
                            <td class="px-4 py-3">
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <input type="number" 
                                       name="items[{{ $loop->index }}][qty]" 
                                       value="{{ $item->quantity }}" 
                                       min="0" 
                                       max="{{ $maxQty }}" 
                                       class="w-full border rounded px-2 py-1 text-center font-bold outline-none focus:border-[#d97706] focus:ring-1 focus:ring-[#d97706]">
                                <div class="text-[10px] text-gray-400 text-center mt-1">Max: {{ $maxQty }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="submit" class="bg-[#d97706] hover:bg-[#b45309] text-white px-6 py-2 rounded-lg font-bold transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Update Challan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
