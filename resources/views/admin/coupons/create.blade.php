@extends('layouts.admin')

@section('header', 'Create New Coupon')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('coupons.admin.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Coupons
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-4">Create New Coupon</h1>
    </div>

    <form action="{{ route('coupons.admin.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-1">Coupon Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all uppercase"
                        placeholder="E.G. SAVE20">
                    @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-1">Discount Type</label>
                    <select name="type" id="type" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount ($)</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="value" class="block text-sm font-semibold text-gray-700 mb-1">Discount Value</label>
                    <input type="number" step="0.01" name="value" id="value" value="{{ old('value') }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="20.00">
                    @error('value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="min_amount" class="block text-sm font-semibold text-gray-700 mb-1">Minimum Spend ($)</label>
                    <input type="number" step="0.01" name="min_amount" id="min_amount" value="{{ old('min_amount', 0) }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="max_uses" class="block text-sm font-semibold text-gray-700 mb-1">Max Total Uses</label>
                    <input type="number" name="max_uses" id="max_uses" value="{{ old('max_uses') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="Leave empty for unlimited">
                </div>
                <div>
                    <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-50">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-2 block text-sm font-semibold text-gray-700">Active Status</label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('coupons.admin.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-save mr-2"></i> Save Coupon
            </button>
        </div>
    </form>
</div>
@endsection
