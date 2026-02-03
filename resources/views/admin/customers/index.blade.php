@extends('layouts.admin')

@section('header', 'Customers')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <form action="{{ route('customers.index') }}" method="GET" class="w-1/3 relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, TRN..." 
                   class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </form>
        
        <a href="{{ route('customers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Add Customer
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">TRN (B2B)</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total Orders</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">{{ $customer->name }}</div>
                        <div class="text-xs text-gray-500">Since {{ $customer->created_at->format('M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><i class="fas fa-phone w-4 text-gray-400"></i> {{ $customer->phone ?? '-' }}</div>
                        <div class="text-sm text-gray-900"><i class="fas fa-envelope w-4 text-gray-400"></i> {{ $customer->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $customer->trn_number ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                        {{ $customer->orders()->count() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-900 mr-3"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('customers.edit', $customer) }}" class="text-gray-600 hover:text-gray-900 mr-3"><i class="fas fa-edit"></i></a>
                        
                        @if(auth()->user()->hasRole('Super Admin'))
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="button" class="text-red-600 hover:text-red-900 btn-delete-customer" 
                                data-name="{{ $customer->name }}"
                                data-orders="{{ $customer->orders()->count() }}"
                                data-quotations="{{ $customer->quotations()->count() }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-6 text-gray-500">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">{{ $customers->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.btn-delete-customer', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const name = $(this).data('name');
        const orders = $(this).data('orders');
        const quotations = $(this).data('quotations');

        Swal.fire({
            title: 'Delete Customer?',
            html: `
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-4">You are about to delete <strong>${name}</strong>.</p>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-left">
                        <p class="text-red-700 font-bold text-sm mb-2"><i class="fas fa-exclamation-triangle mr-2"></i> CRITICAL WARNING:</p>
                        <p class="text-red-600 text-xs leading-relaxed">
                            Deleting this customer will <strong>permanently delete</strong>:
                            <ul class="list-disc list-inside mt-2 font-bold uppercase text-[10px]">
                                <li>All Invoices (${orders})</li>
                                <li>All Quotations (${quotations})</li>
                                <li>Deliver Challans & Returns</li>
                                <li>Customer Addresses & Profiles</li>
                            </ul>
                        </p>
                    </div>
                    <p class="text-sm text-gray-500 mt-4 font-bold">This action cannot be undone!</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i> Yes, Delete Everything',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'px-6 py-2.5 rounded-lg font-bold shadow-lg shadow-red-500/30',
                cancelButton: 'px-6 py-2.5 rounded-lg font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush