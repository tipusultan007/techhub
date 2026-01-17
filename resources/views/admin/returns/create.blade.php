@extends('layouts.admin')
@section('header', 'Process a Return/Refund')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
    <form action="{{ route('returns.find') }}" method="POST">
        @csrf
        <label class="block text-sm font-bold text-gray-700 mb-2">Enter Invoice Number</label>
        <div class="flex gap-2">
            <input type="text" name="invoice_no" class="w-full border rounded p-2" placeholder="e.g. INV-2025-00123" required>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">Find Order</button>
        </div>
    </form>
</div>
@endsection