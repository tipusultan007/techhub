@extends('layouts.admin')

@section('header', 'Edit Customer')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex text-gray-500 text-sm mb-6" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="{{ route('customers.index') }}" class="hover:text-blue-600">Customers</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
            </li>
            <li class="flex items-center">
                <a href="{{ route('customers.show', $customer) }}" class="hover:text-blue-600">{{ $customer->name }}</a>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
            </li>
            <li class="flex items-center text-gray-900 font-bold">
                Edit Details
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/30">
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Modify Customer information</h3>
            <p class="text-xs text-gray-500 mt-1 uppercase font-bold tracking-widest">Update basic and B2B contact details</p>
        </div>
        
        <form action="{{ route('customers.update', $customer) }}" method="POST" class="p-8">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="md:col-span-2">
                    <label class="block text-[0.7rem] font-black text-gray-400 uppercase tracking-widest mb-2">Customer Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-gray-900 transition-all outline-none"
                           placeholder="e.g. John Doe">
                </div>
                
                <div>
                    <label class="block text-[0.7rem] font-black text-gray-400 uppercase tracking-widest mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-gray-900 transition-all outline-none"
                           placeholder="+971 50 XXXXXXX">
                </div>

                <div>
                    <label class="block text-[0.7rem] font-black text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-gray-900 transition-all outline-none"
                           placeholder="john@example.com">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[0.7rem] font-black text-gray-400 uppercase tracking-widest mb-2 flex justify-between">
                        <span>TRN Number (UAE B2B)</span>
                        <span class="text-[0.6rem] bg-blue-100 text-blue-600 px-2 py-0.5 rounded">Optional</span>
                    </label>
                    <input type="text" name="trn_number" value="{{ old('trn_number', $customer->trn_number) }}"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-gray-900 transition-all outline-none"
                           placeholder="15-digit UAE TRN Number">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[0.7rem] font-black text-gray-400 uppercase tracking-widest mb-2">Primary Billing Address</label>
                    <textarea name="address" rows="3" 
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-gray-900 transition-all outline-none"
                              placeholder="Full delivery/billing address details...">{{ old('address', $customer->address) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('customers.show', $customer) }}" 
                   class="px-8 py-3 bg-white text-gray-500 font-bold rounded-xl border border-gray-200 hover:bg-gray-50 transition-all">
                    Discard Changes
                </a>
                <button type="submit" 
                        class="px-10 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                    Save Updates
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
