@extends('layouts.admin')

@section('header', 'Stock Audit & Reconciliation')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-bold tracking-wider text-gray-400 mb-1">Products Checked</p>
                <h3 class="text-3xl font-black text-gray-800">{{ number_format($totalChecked) }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <i class="fas fa-boxes text-xl"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-bold tracking-wider text-gray-400 mb-1">Stock Mismatches</p>
                <h3 class="text-3xl font-black {{ $mismatchedCount > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($mismatchedCount) }}
                </h3>
            </div>
            <div class="w-12 h-12 {{ $mismatchedCount > 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }} rounded-xl flex items-center justify-center">
                <i class="fas {{ $mismatchedCount > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' }} text-xl"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs uppercase font-bold tracking-wider text-gray-400 mb-1">Total Unit Discrepancy</p>
                <h3 class="text-3xl font-black text-orange-600">{{ number_format($totalDiffUnits) }}</h3>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                <i class="fas fa-balance-scale-unbalanced text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Filter & Action Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('reports.stock-mismatch') }}" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Name or SKU..."
                    class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
            </div>

            <select name="filter_status" onchange="this.form.submit()" class="border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500">
                <option value="mismatched" {{ $filterStatus === 'mismatched' ? 'selected' : '' }}>Mismatched Only</option>
                <option value="matched" {{ $filterStatus === 'matched' ? 'selected' : '' }}>Matched Only</option>
                <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>All Products</option>
            </select>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-xl transition flex items-center justify-center gap-2">
                <i class="fas fa-filter text-xs"></i> Filter
            </button>
        </form>

        @if($mismatchedCount > 0)
            <form method="POST" action="{{ route('reports.stock-reconcile') }}" onsubmit="return confirm('Are you sure you want to reconcile ALL mismatched stocks to equal total purchases - total sales + returns?')">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt"></i> Reconcile All Stock
                </button>
            </form>
        @endif
    </div>

    {{-- Mismatch Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Product Stock Audit Details</h3>
            <span class="text-xs text-gray-400 font-mono">Formula: Calculated Stock = Total Purchased - Total Sold + Restocked Returns</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-xs uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                        <th class="px-6 py-4">Product Name</th>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4 text-center">Purchased</th>
                        <th class="px-6 py-4 text-center">Sold</th>
                        <th class="px-6 py-4 text-center">Returned</th>
                        <th class="px-6 py-4 text-center">Calculated Stock</th>
                        <th class="px-6 py-4 text-center">Current DB Stock</th>
                        <th class="px-6 py-4 text-center">Diff</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50">
                    @forelse($paginatedItems as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800">
                                {{ $item['name'] }}
                                @if($item['type'] === 'variant')
                                    <span class="block text-xs font-normal text-blue-600 mt-0.5">
                                        Variant: <strong>{{ $item['variant_name'] }}</strong>
                                    </span>
                                @else
                                    <span class="block text-xs font-normal text-gray-400 mt-0.5">Simple Product</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $item['sku'] }}</td>
                            <td class="px-6 py-4 text-center text-gray-700 font-medium">{{ $item['total_purchased'] }}</td>
                            <td class="px-6 py-4 text-center text-gray-700 font-medium">{{ $item['total_sold'] }}</td>
                            <td class="px-6 py-4 text-center text-gray-700 font-medium">{{ $item['total_returned'] }}</td>
                            <td class="px-6 py-4 text-center font-bold text-blue-700 bg-blue-50/50">
                                {{ $item['expected_stock'] }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold {{ $item['is_mismatched'] ? 'text-red-600' : 'text-green-600' }}">
                                {{ $item['current_stock'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item['is_mismatched'])
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        {{ $item['diff'] > 0 ? '+'.$item['diff'] : $item['diff'] }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        Matched
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($item['is_mismatched'])
                                    <form method="POST" action="{{ route('reports.stock-reconcile') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="type" value="{{ $item['type'] }}">
                                        <input type="hidden" name="id" value="{{ $item['type'] === 'variant' ? $item['variant_id'] : $item['product_id'] }}">
                                        <input type="hidden" name="expected_stock" value="{{ $item['expected_stock'] }}">
                                        <button type="submit" title="Sync DB stock to {{ $item['expected_stock'] }}"
                                                class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg border border-blue-200 transition">
                                            Sync to {{ $item['expected_stock'] }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-check-circle text-4xl text-green-400 mb-3"></i>
                                    <p class="font-bold text-gray-600 text-base">No Mismatched Products Found</p>
                                    <p class="text-xs text-gray-400 mt-1">All physical stock levels match purchases and sales calculations perfectly!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $paginatedItems->links() }}
        </div>
    </div>

</div>
@endsection
