@extends('layouts.admin')

@section('header', 'Edit Supplier')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="mb-6 border-b pb-4 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">Edit Details: {{ $supplier->name }}</h2>
        </div>
        <!-- Delete Button (Top Right) -->
        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete this supplier?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-500 hover:text-red-700 text-sm underline">Delete Supplier</button>
        </form>
    </div>

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Contact Person Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Company -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" class="w-full mt-1 border border-gray-300 rounded-md p-2">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full mt-1 border border-gray-300 rounded-md p-2">
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full mt-1 border border-gray-300 rounded-md p-2">
            </div>

            <!-- TRN -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">TRN Number</label>
                <input type="text" name="trn_number" value="{{ old('trn_number', $supplier->trn_number) }}" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-gray-50">
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Full Address</label>
                <textarea name="address" rows="3" class="w-full mt-1 border border-gray-300 rounded-md p-2">{{ old('address', $supplier->address) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <a href="{{ route('suppliers.index') }}" class="mr-3 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700 shadow">Update Details</button>
        </div>
    </form>
</div>
@endsection