@extends('layouts.admin')

@section('header', 'Add New Supplier')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-lg font-semibold text-gray-700">Supplier Information</h2>
        <p class="text-sm text-gray-500">Enter the details of the vendor/supplier for stock purchasing.</p>
    </div>

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Contact Person Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full mt-1 border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" required>
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <!-- Company -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" class="w-full mt-1 border border-gray-300 rounded-md p-2">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full mt-1 border border-gray-300 rounded-md p-2">
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-bold text-gray-700">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full mt-1 border border-gray-300 rounded-md p-2">
            </div>

            <!-- TRN (UAE Specific) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">TRN Number (Tax Registration)</label>
                <input type="text" name="trn_number" value="{{ old('trn_number') }}" placeholder="e.g. 100xxxxxxxxx" class="w-full mt-1 border border-gray-300 rounded-md p-2 bg-gray-50">
                <p class="text-xs text-gray-500 mt-1">Required for generating valid UAE VAT Tax Invoices.</p>
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Full Address</label>
                <textarea name="address" rows="3" class="w-full mt-1 border border-gray-300 rounded-md p-2">{{ old('address') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <a href="{{ route('suppliers.index') }}" class="mr-3 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700 shadow">Save Supplier</button>
        </div>
    </form>
</div>
@endsection