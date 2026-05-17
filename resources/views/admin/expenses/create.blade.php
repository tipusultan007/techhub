@extends('layouts.admin')
@section('header', 'Add Expense')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow border border-gray-200">
        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded mt-1" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" class="w-full border p-2 rounded mt-1" placeholder="0.00" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Tax Method <span class="text-red-500">*</span></label>
                    <select name="tax_method" class="w-full border p-2 rounded mt-1 bg-white" required>
                        <option value="inclusive">Tax Inclusive (5% VAT inside)</option>
                        <option value="exclusive">Tax Exclusive (Add 5% VAT)</option>
                    </select>
                    <p class="text-[10px] text-gray-500 mt-1 italic">Note: System will automatically calculate UAE VAT amount.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Category <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="expense_category_id" class="w-full border p-2 rounded mt-1 bg-white" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('expense-categories.index') }}" class="mt-1 bg-gray-200 px-3 py-2 rounded flex items-center justify-center hover:bg-gray-300" title="Manage Categories">+</a>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Reference No</label>
                    <input type="text" name="reference_no" class="w-full border p-2 rounded mt-1" placeholder="e.g. Receipt #">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700">Note</label>
                    <textarea name="note" rows="3" class="w-full border p-2 rounded mt-1"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-gray-200 rounded text-gray-700 hover:bg-gray-300">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Save Expense</button>
            </div>
        </form>
    </div>
@endsection
