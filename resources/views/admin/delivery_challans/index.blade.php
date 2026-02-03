@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-truck text-[#d97706] mr-2"></i> Delivery Challans
        </h1>
        
        <!-- Search & Filter Form -->
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <form action="{{ route('delivery-challans.index') }}" method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Number, PO#, Customer..." class="border rounded px-3 py-2 text-sm w-full md:w-56">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded px-3 py-2 text-sm">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded px-3 py-2 text-sm">
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-blue-700">Filter</button>
                @if(request()->hasAny(['search', 'start_date', 'end_date']))
                <a href="{{ route('delivery-challans.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded text-sm font-bold hover:bg-gray-600">Clear</a>
                @endif
            </form>

            <a href="{{ route('delivery-challans.create') }}" class="bg-[#d97706] text-white px-4 py-2 rounded text-sm font-bold hover:bg-[#b45309] flex items-center gap-2">
                <i class="fas fa-plus"></i> Create Challan
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs font-bold uppercase text-gray-700 border-b">
                <tr>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Challan #</th>
                    <th class="px-6 py-4 text-center">PO #</th>
                    <th class="px-6 py-4">Reference Quotation</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($challans as $challan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-bold text-gray-600">{{ \Carbon\Carbon::parse($challan->date)->format('d M, Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-mono font-bold text-[#d97706]">{{ $challan->challan_number }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-gray-600">{{ $challan->po_number ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-500">
                        @if($challan->quotation)
                            <a href="{{ route('quotations.show', $challan->quotation_id) }}" class="text-blue-600 hover:underline font-bold">
                                {{ $challan->quotation->quotation_no }}
                            </a>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $challan->customer->name ?? $challan->quotation->customer_name }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('delivery-challans.show', $challan->id) }}" class="text-blue-600 hover:text-blue-800" title="View"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('delivery-challans.edit', $challan->id) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit"><i class="fas fa-edit"></i></a>
                        @if(auth()->user()->hasRole('Super Admin'))
                        <button type="button" onclick="deleteChallan({{ $challan->id }})" class="text-red-600 hover:text-red-800" title="Delete"><i class="fas fa-trash"></i></button>
                        @endif
                        <a href="{{ route('delivery-challans.print', $challan->id) }}" target="_blank" class="text-gray-600 hover:text-gray-800" title="Print"><i class="fas fa-print"></i></a>
                        <a href="{{ route('delivery-challans.pdf', $challan->id) }}" class="text-red-600 hover:text-red-800" title="PDF"><i class="fas fa-file-pdf"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">No delivery challans found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t">
            {{ $challans->withQueryString()->links() }}
        </div>
    </div>
</div>

<form id="delete-form" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteChallan(id) {
        Swal.fire({
            title: 'Delete Delivery Challan?',
            text: "This will revert the delivered quantities back to the quotation.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('delete-form');
                form.action = '/backend/delivery-challans/' + id;
                form.submit();
            }
        })
    }
</script>
@endpush
@endsection
