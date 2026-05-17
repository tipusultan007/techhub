@extends('layouts.admin')

@section('header', 'Expense Details')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('expenses.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Back to Expenses
        </a>
        <div class="flex gap-3">
             <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" 
                    class="bg-red-50 text-red-600 px-5 py-2 rounded shadow-sm border border-red-100 hover:bg-red-100 font-bold transition btn-delete-confirm" 
                    data-type="Expense"
                    data-title="Delete Expense Record?"
                    data-summary='{
                        "Date": "{{ $expense->date->format("d M Y") }}",
                        "Category": "{{ $expense->category->name }}",
                        "Amount": "AED {{ number_format($expense->amount, 2) }}"
                    }'>
                    <i class="fas fa-trash mr-2"></i> Delete Expense
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
        
        <!-- Header -->
        <div class="px-8 py-6 border-b bg-gray-50 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
                    EXPENSE RECORD
                </h1>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p>Reference: <span class="font-mono font-bold text-gray-900">{{ $expense->note ?? 'N/A' }}</span></p>
                    <p>Date: <span class="font-bold text-gray-900">{{ $expense->date->format('d M Y') }}</span></p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-bold text-gray-400 uppercase mb-1">Total Amount</div>
                <div class="text-3xl font-black text-red-600">AED {{ number_format($expense->amount, 2) }}</div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="px-8 py-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 py-1 border-b">Expense Information</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 font-medium">Category:</span>
                        <span class="text-sm text-gray-900 font-bold text-right">{{ $expense->category->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 font-medium">Recorded By:</span>
                        <span class="text-sm text-gray-900 font-bold text-right">{{ $expense->user->name ?? 'System' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 font-medium">Timestamp:</span>
                        <span class="text-sm text-gray-900 font-bold text-right">{{ $expense->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 py-1 border-b">VAT & Tax Details</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 font-medium">Tax Method:</span>
                        <span class="text-sm font-bold uppercase text-right {{ $expense->tax_method === 'no_tax' ? 'text-gray-500' : 'text-slate-800' }}">
                            @if($expense->tax_method === 'inclusive')
                                Tax Inclusive (5%)
                            @elseif($expense->tax_method === 'exclusive')
                                Tax Exclusive (5%)
                            @else
                                No Tax / Exempt
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 font-medium">Net (Pre-Tax):</span>
                        <span class="text-sm text-gray-900 font-mono font-bold text-right">AED {{ number_format($expense->net_amount ?? $expense->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 font-medium">VAT (5%):</span>
                        <span class="text-sm text-red-600 font-mono font-bold text-right">AED {{ number_format($expense->tax_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 py-1 border-b">Note / Description</h4>
                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100 italic">
                    {{ $expense->note ?: 'No additional notes provided for this expense.' }}
                </p>
            </div>
        </div>

        <!-- Attachment Section -->
        @if($expense->hasMedia('attachment'))
        <div class="px-8 py-8 border-t bg-gray-50/30">
            <h4 class="text-xs font-bold text-gray-400 uppercase mb-4">Attachment / Receipt</h4>
            <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fas fa-file-invoice text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">{{ $expense->getFirstMedia('attachment')->file_name }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($expense->getFirstMedia('attachment')->size / 1024, 2) }} KB</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ $expense->getFirstMediaUrl('attachment') }}" target="_blank" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="{{ $expense->getFirstMediaUrl('attachment') }}" download class="px-4 py-2 bg-blue-600 border border-blue-600 rounded-lg text-xs font-bold text-white hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-download mr-1"></i> Download
                    </a>
                </div>
            </div>
            
            @php
                $media = $expense->getFirstMedia('attachment');
                $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
            @endphp
            
            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                <div class="mt-6 border rounded-xl overflow-hidden shadow-inner bg-gray-200">
                    <img src="{{ $media->getUrl() }}" alt="Expense Attachment" class="w-full max-h-[600px] object-contain">
                </div>
            @endif
        </div>
        @endif

        <!-- System info footer -->
        <div class="px-8 py-4 bg-gray-100 border-t flex justify-between items-center text-[10px] text-gray-500 font-bold uppercase tracking-widest">
            <div>
                Last Updated: {{ $expense->updated_at->diffForHumans() }}
            </div>
            <div>
                 Electromart Expense Module
            </div>
        </div>
    </div>
</div>
@endsection
