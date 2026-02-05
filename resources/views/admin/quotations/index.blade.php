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

@section('header', 'Quotations')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quotations</h1>
        <a href="{{ route('quotations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Create Quotation
        </a>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form action="{{ route('quotations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Quo # -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Quotation #</label>
                <input type="text" name="quotation_no" value="{{ request('quotation_no') }}" 
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="QUO-XXXXX">
            </div>

            <!-- Customer -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Customer</label>
                <select name="customer_id" class="w-full select2 border border-gray-300 rounded-lg p-2 text-sm">
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
                <select name="status" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-5 flex justify-end gap-2 mt-2">
                <a href="{{ route('quotations.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-300 transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase font-bold">
                <tr>
                    <th class="px-6 py-4 border-b">QUO #</th>
                    <th class="px-6 py-4 border-b">Customer</th>
                    <th class="px-6 py-4 border-b">Total</th>
                    <th class="px-6 py-4 border-b">Status</th>
                    <th class="px-6 py-4 border-b">Created By</th>
                    <th class="px-6 py-4 border-b">Created At</th>
                    <th class="px-6 py-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($quotations as $quo)
                <tr class="hover:bg-gray-50 transition border-b last:border-0">
                    <td class="px-6 py-4">
                        <div class="font-mono font-bold">{{ $quo->quotation_no }}</div>
                        @if($quo->po_number)
                            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter">PO#: {{ $quo->po_number }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold">{{ $quo->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $quo->customer?->phone }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-blue-600">AED {{ number_format($quo->total, 2) }}</td>
                    <td class="px-6 py-4">
                        @if($quo->status == 'submitted')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold uppercase">Submitted</span>
                        @elseif($quo->status == 'converted')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold uppercase">Converted</span>
                            @if($quo->order)
                                <div class="text-[10px] text-gray-500 mt-1">Invoice: <a href="{{ route('orders.show', $quo->order_id) }}" class="text-blue-500 underline">{{ $quo->order?->invoice_no }}</a></div>
                            @endif
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-bold uppercase">Cancelled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $quo->user?->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $quo->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('quotations.show', $quo->id) }}" class="text-blue-500 hover:text-blue-700" title="View/Print">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('quotations.download-pdf', $quo->id) }}" class="text-indigo-500 hover:text-indigo-700" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @if($quo->status == 'submitted')
                            <a href="{{ route('quotations.edit', $quo->id) }}" class="text-orange-500 hover:text-orange-700" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                            @if($quo->status == 'submitted')
                            <button type="button" 
                                onclick="confirmConversion({{ $quo->id }}, '{{ $quo->quotation_no }}', '{{ $quo->customer_name }}', '{{ number_format($quo->total, 2) }}', {{ $quo->items->count() }}, '{{ $quo->po_number }}', '{{ ($quo->date ?? $quo->created_at)->format('d M, Y') }}')"
                                class="text-green-600 hover:text-green-800" title="Convert to Sale">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            <form id="convert-form-{{ $quo->id }}" action="{{ route('quotations.convert', $quo->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @endif
                            @if(auth()->user()->hasRole('Super Admin'))
                            <form action="{{ route('quotations.destroy', $quo->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                    class="text-red-500 hover:text-red-700 btn-delete-confirm" 
                                    title="Delete Quotation"
                                    data-type="Quotation"
                                    data-title="Delete Quotation?"
                                    data-summary='{
                                        "Quo #": "{{ $quo->quotation_no }}",
                                        "Customer": "{{ $quo->customer_name }}",
                                        "Total": "AED {{ number_format($quo->total, 2) }}"
                                    }'>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-file-invoice text-4xl mb-2"></i>
                        <p>No quotations found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $quotations->links() }}
    </div>
</div>
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

    function confirmConversion(id, quNo, customer, total, itemsCount, poNumber, date) {
        Swal.fire({
            title: 'Convert to Sale?',
            html: `
                <div class="text-left bg-gray-50 p-4 rounded-lg border border-gray-200 mt-4">
                    <p class="text-sm text-gray-600 mb-1 font-bold uppercase tracking-wider">Quotation Summary</p>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Quotation #:</span>
                        <span class="font-bold text-gray-800">${quNo}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Customer:</span>
                        <span class="font-bold text-gray-800">${customer}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-100">
                        <span class="text-gray-500">Total Items:</span>
                        <span class="font-bold text-gray-800">${itemsCount}</span>
                    </div>
                    <div class="flex justify-between pt-2 mb-4">
                        <span class="text-gray-700 font-bold uppercase">Grand Total:</span>
                        <span class="font-black text-blue-600 text-lg">AED ${total}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">PO Number (Optional)</label>
                            <input type="text" id="swal-po-number" class="swal2-input !m-0 !w-full !text-sm" placeholder="Enter PO#" value="${poNumber || ''}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Method</label>
                            <select id="swal-payment-method" class="swal2-input !m-0 !w-full !text-sm">
                                <option value="cash" selected>💵 Cash</option>
                                <option value="card">💳 Card</option>
                                <option value="transfer">🏦 Bank Transfer</option>
                                <option value="advance">💰 Advance</option>
                                <option value="custom">⚙️ Custom</option>
                            </select>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-red-500 mt-4 font-bold italic"><i class="fas fa-exclamation-triangle mr-1"></i> Stock will be deducted upon conversion.</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#374151',
            confirmButtonText: '<i class="fas fa-check-circle mr-2"></i> Yes, Convert Now',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                return {
                    po_number: document.getElementById('swal-po-number').value,
                    payment_method: document.getElementById('swal-payment-method').value
                };
            },
            customClass: {
                confirmButton: 'px-6 py-2.5 rounded-lg font-bold',
                cancelButton: 'px-6 py-2.5 rounded-lg font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('convert-form-' + id);
                
                // Add PO# input to form
                let poInput = document.createElement('input');
                poInput.type = 'hidden';
                poInput.name = 'po_number';
                poInput.value = result.value.po_number;
                form.appendChild(poInput);

                // Add Payment Method input to form
                let pmInput = document.createElement('input');
                pmInput.type = 'hidden';
                pmInput.name = 'payment_method';
                pmInput.value = result.value.payment_method;
                form.appendChild(pmInput);
                
                form.submit();
            }
        });
    }
</script>
@endsection
@endsection
