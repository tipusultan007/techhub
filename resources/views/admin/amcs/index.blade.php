@extends('layouts.admin')

@section('header', 'AMC Management')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-radius: 0.75rem;
        height: 42px;
        border-color: #e5e7eb;
        display: flex;
        align-items: center;
        padding-left: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        font-size: 0.875rem;
        color: #374151;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        top: 1px;
    }
    .select2-dropdown {
        border-radius: 0.75rem;
        border-color: #e5e7eb;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">AMC Contracts</h2>
            <p class="text-gray-500 text-sm mt-1">Track and manage annual maintenance contracts with your customers.</p>
        </div>
        <a href="{{ route('amcs.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>New Contract</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('amcs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Search Contract</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="AMC-XXXXX" 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all text-sm">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Customer</label>
                <select name="customer_id" class="select2 w-full rounded-xl border-gray-200 text-sm">
                    <option value="">-- All Customers --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</label>
                <select name="status" class="w-full h-[42px] px-4 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all text-sm">
                    <option value="">-- All Status --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date Range</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full px-3 py-2 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all text-[10px]">
                    <span class="text-gray-400">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full px-3 py-2 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all text-[10px]">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20 text-xs">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('amcs.index') }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-gray-600 rounded-xl transition-all border border-gray-100" title="Clear Filters">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">ID / Number</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Customer</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Period</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($amcs as $amc)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded ring-1 ring-emerald-100">{{ $amc->contract_number }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900">
                            {{ $amc->customer->name }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-col">
                                <span class="text-gray-700 font-medium">{{ $amc->start_date->format('d M Y') }}</span>
                                <span class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">to {{ $amc->end_date->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'gray',
                                    'active' => 'emerald',
                                    'expired' => 'red',
                                    'cancelled' => 'amber'
                                ];
                                $color = $statusColors[$amc->status] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-{{ $color }}-50 text-{{ $color }}-600 text-[10px] font-black uppercase tracking-wider border border-{{ $color }}-100">
                                {{ $amc->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('amcs.show', $amc) }}" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 transition-all" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('amcs.edit', $amc) }}" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-amber-500 hover:bg-amber-50 transition-all" title="Edit Contract">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('amcs.agreement', $amc) }}" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition-all" title="Print Agreement">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <form action="{{ route('amcs.destroy', $amc) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete-confirm p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all" 
                                            data-type="AMC" data-summary='{"No": "{{ $amc->contract_number }}", "Customer": "{{ $amc->customer->name }}"}'>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No AMC Contracts Found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-50">
            {{ $amcs->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        placeholder: '-- All Customers --',
        allowClear: true
    });
});
</script>
@endpush
