@extends('layouts.admin')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-radius: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            color: #1e293b !important;
            font-weight: 500;
        }
        .select2-results__option {
            padding: 8px 12px !important;
            border-bottom: 1px solid #f1f5f9;
        }
        .select2-results__option--highlighted {
            background-color: #f8fafc !important; /* Extremely light slate */
            color: inherit !important;
            border-left: 4px solid #3b82f6 !important;
        }
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border-radius: 0.5rem !important;
            margin-top: 4px;
        }
    </style>
@endpush

@section('header', 'Process a Return/Refund')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg">
                    <i class="fas fa-undo-alt text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Initiate Return</h2>
                    <p class="text-xs text-gray-500">Search by Invoice Number or Customer Name</p>
                </div>
            </div>

            <form action="{{ route('returns.find') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Find Order</label>
                    <select name="invoice_no" id="order_search" class="w-full" required>
                        <option value="">Search Invoice # or Customer...</option>
                    </select>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        Find Order Items
                    </button>
                    <a href="{{ route('returns.index') }}" class="w-full text-center text-gray-500 hover:text-gray-700 font-semibold py-2 text-sm">
                        Go Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#order_search').select2({
                ajax: {
                    url: '{{ route("returns.search-orders") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                placeholder: 'Search Invoice # or Customer...',
                minimumInputLength: 2,
                templateResult: formatOrder,
                templateSelection: formatOrderSelection
            });

            function formatOrder(order) {
                if (order.loading) return order.text;
                
                return $(`
                    <div class="flex flex-col py-1">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-slate-800">${order.id}</span>
                            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-bold">${order.date}</span>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-sm text-slate-500">${order.customer}</span>
                            <span class="text-xs font-bold text-slate-900">AED ${order.total}</span>
                        </div>
                    </div>
                `);
            }

            function formatOrderSelection(order) {
                return order.id || order.text;
            }
        });
    </script>
@endsection