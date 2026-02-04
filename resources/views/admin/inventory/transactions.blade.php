@extends('layouts.admin')

@section('title', 'Inventory Transactions')

@section('content')
<div class="content-header mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Inventory Transactions</h1>
            <p class="text-slate-500 text-sm">Monitor all stock movements and audit trails.</p>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-6 p-6">
    <form action="{{ route('inventory.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Product</label>
            <select name="product_id" id="product_id" class="w-full select2-basic">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Movement Type</label>
            <select name="type" class="w-full border-2 border-slate-100 rounded-xl p-2 focus:border-blue-500 outline-none transition">
                <option value="">All Types</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In (+)</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out (-)</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="w-full border-2 border-slate-100 rounded-xl p-2 focus:border-blue-500 outline-none transition uppercase text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="w-full border-2 border-slate-100 rounded-xl p-2 focus:border-blue-500 outline-none transition uppercase text-sm">
        </div>

        <div class="md:col-span-3">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Reference / Invoice #</label>
            <input type="text" name="reference" value="{{ request('reference') }}" placeholder="Search by reference..."
                class="w-full border-2 border-slate-100 rounded-xl p-2 focus:border-blue-500 outline-none transition">
        </div>

        <div>
            <button type="submit" class="w-full bg-slate-900 text-white font-bold py-2.5 rounded-xl hover:bg-black transition shadow-lg shadow-slate-200">
                Filter Results
            </button>
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase">
            <tr>
                <th class="px-6 py-4">Date & Time</th>
                <th class="px-6 py-4">Product / Variant</th>
                <th class="px-6 py-4 text-center">Type</th>
                <th class="px-6 py-4 text-center">Qty</th>
                <th class="px-6 py-4">Reference</th>
                <th class="px-6 py-4">User</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($transactions as $transaction)
            <tr class="hover:bg-slate-50 transition border-transparent">
                <td class="px-6 py-4">
                    <span class="block text-slate-800 font-bold leading-none">{{ $transaction->created_at->format('d M, Y') }}</span>
                    <span class="text-[10px] text-slate-400 font-medium uppercase mt-1">{{ $transaction->created_at->format('h:i A') }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        <span class="text-slate-900 font-bold">{{ $transaction->product->name ?? 'N/A' }}</span>
                        @if($transaction->variant)
                            <span class="text-[11px] text-slate-500 font-medium mt-0.5">Variant: {{ $transaction->variant->variant_name }}</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($transaction->type === 'in')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-700">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                            </svg>
                            IN
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-100 text-rose-700">
                             <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path>
                            </svg>
                            OUT
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-lg font-black {{ $transaction->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-600 font-medium text-sm">{{ $transaction->description }}</span>
                        @if($transaction->reference)
                            @php
                                $route = null;
                                $icon = 'fa-external-link-alt';
                                if($transaction->reference instanceof \App\Models\Order) {
                                    $route = route('orders.show', $transaction->reference_id);
                                    $icon = 'fa-shopping-bag';
                                } elseif($transaction->reference instanceof \App\Models\PurchaseOrder) {
                                    $route = route('purchases.show', $transaction->reference_id);
                                    $icon = 'fa-truck-loading';
                                } elseif($transaction->reference instanceof \App\Models\ReturnOrder) {
                                    $route = route('returns.show', $transaction->reference_id);
                                    $icon = 'fa-sync-alt';
                                }
                            @endphp
                            @if($route)
                                <a href="{{ $route }}" class="text-blue-500 hover:text-blue-700 transition" title="View Related Document">
                                    <i class="fas {{ $icon }} text-xs"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 text-slate-500 text-sm font-medium">
                    {{ $transaction->user->name ?? 'System' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <p class="font-bold">No transactions found matching your criteria.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $transactions->links() }}
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-basic').select2({
            theme: 'classic',
            placeholder: "Search for a product..."
        });
    });
</script>
<style>
    /* Premium Select2 Styling */
    .select2-container--classic .select2-selection--single {
        border: 2px solid #f1f5f9 !important;
        border-radius: 0.75rem !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        background-image: none !important;
        transition: all 0.2s !important;
    }
    .select2-container--classic.select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
    }
    .select2-container--classic .select2-selection--single .select2-selection__rendered {
        color: #1e293b !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
    }
</style>
@endpush
