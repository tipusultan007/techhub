@extends('layouts.admin')
@section('header', 'Select Items to Return')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
    <div class="mb-4 border-b pb-4">
        <h3 class="font-bold">Original Invoice: {{ $order->invoice_no }}</h3>
        <p class="text-sm text-gray-500">Customer: {{ $order->customer_name }}</p>
    </div>

    <form action="{{ route('returns.store') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 w-10">Return?</th>
                    <th class="p-2 text-left">Product</th>
                    <th class="p-2">Return Qty</th>
                    <th class="p-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($order->items as $item)
                <tr>
                    <td class="p-2 text-center">
                        <input type="checkbox" name="items[{{ $item->id }}][selected]" value="1" class="form-checkbox h-5 w-5">
                    </td>
                    <td class="p-2">
                        <div class="font-bold">{{ $item->product_name }}</div>
                        @if($item->serial_numbers)
                            <div class="text-xs text-gray-500 font-mono">SN: {{ $item->serial_numbers }}</div>
                        @endif
                    </td>
                    <td class="p-2">
                        <input type="number" name="items[{{ $item->id }}][qty]" value="{{ $item->quantity }}" max="{{ $item->quantity }}" 
                               class="w-20 border rounded p-1 text-center" {{ $item->serial_numbers ? 'readonly' : '' }}>
                    </td>
                    <td class="p-2">
                        <select name="items[{{ $item->id }}][status]" class="w-full border rounded p-1 bg-white">
                            <option value="restockable">Re-stockable</option>
                            <option value="defective">Defective / Damaged</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            <label class="block text-sm font-bold text-gray-700">Reason for Return (Optional)</label>
            <textarea name="reason" class="w-full border rounded p-2 mt-1"></textarea>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded font-bold hover:bg-green-700">
                Process Refund
            </button>
        </div>
    </form>
</div>
@endsection