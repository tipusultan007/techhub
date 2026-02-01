@extends('layouts.admin')

@push('styles')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding-top: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush

@section('header', 'Sales Orders')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow overflow-hidden border border-gray-200">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-700">All Transactions</h3>
        <span class="text-xs text-gray-500">Showing latest sales first</span>
    </div>

    <!-- Filters Section -->
    <div class="px-6 py-4 border-b bg-white">
        <form action="{{ route('orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Invoice No -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Invoice No</label>
                <input type="text" name="invoice_no" value="{{ request('invoice_no') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="INV-XXXXX">
            </div>

            <!-- Customer -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Customer</label>
                <select name="customer_id" class="w-full select2 border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending','processing','shipped','completed','cancelled','returned'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment</label>
                <select name="payment_method" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Bank</option>
                    <option value="advance" {{ request('payment_method') == 'advance' ? 'selected' : '' }}>Advance</option>
                    <option value="custom" {{ request('payment_method') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>

            <!-- Channel Filter -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Channel</label>
                <select name="channel" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Channels</option>
                    <option value="pos" {{ request('channel') == 'pos' ? 'selected' : '' }}>POS</option>
                    <option value="online" {{ request('channel') == 'online' ? 'selected' : '' }}>Online</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-3 lg:col-span-6 flex justify-end gap-2">
                <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300 transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm font-bold hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Invoice</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Channel</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total (AED)</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-bold text-blue-600">{{ $order->invoice_no }}</div>
                    @if($order->po_number)
                        <div class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter">PO#: {{ $order->po_number }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($order->channel == 'pos')
                        <span class="px-2 py-1 rounded text-[10px] font-black uppercase bg-gray-100 text-gray-800 border border-gray-200">
                            <i class="fas fa-desktop mr-1"></i> POS
                        </span>
                    @else
                        <span class="px-2 py-1 rounded text-[10px] font-black uppercase bg-blue-50 text-blue-700 border border-blue-100">
                            <i class="fas fa-globe mr-1"></i> Online
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $order->customer_name ?? 'Guest/Walk-in' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $order->created_at->format('d M Y, h:i A') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 uppercase">
                        {{ $order->payment_method }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                    {{ number_format($order->total, 2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <!-- View Details -->
                    <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>

                    <!-- Edit Order -->
                    <a href="{{ route('orders.edit', $order) }}" class="text-emerald-600 hover:text-emerald-900 mr-3" title="Edit Order">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Print Receipt -->
                    <a href="{{ route('orders.print', $order) }}" target="_blank" class="text-gray-500 hover:text-gray-900 mr-3" title="Print Receipt">
                        <i class="fas fa-print"></i>
                    </a>

                    <!-- Download PDF -->
                    <a href="{{ route('orders.download-pdf', $order) }}" class="text-blue-500 hover:text-blue-700 mr-3" title="Download PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>

                    <!-- Delete Order -->
                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="button" 
                            class="text-red-600 hover:text-red-900 btn-delete-confirm" 
                            title="Delete Order"
                            data-type="Order"
                            data-title="Delete Sales Order?"
                            data-summary='{
                                "Invoice": "{{ $order->invoice_no }}",
                                "Customer": "{{ $order->customer_name ?? "Guest" }}",
                                "Total": "AED {{ number_format($order->total, 2) }}"
                            }'>
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                    No sales records found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">
        {{ $orders->links() }}
    </div>
</div>
@endsection

@section('scripts')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "All Customers",
                allowClear: true
            });
        });
    </script>
@endsection
