@extends('layouts.admin')

@section('header', 'AMC Agreement')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between no-print">
        <div class="flex items-center gap-4">
            <a href="{{ route('amcs.show', $amc) }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 text-gray-400 hover:text-emerald-500 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Preview Agreement</h2>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-5 py-3 rounded-xl bg-gray-800 text-white font-bold text-sm uppercase tracking-widest hover:bg-black transition-all">
                <i class="fas fa-print mr-2"></i> Print Now
            </button>
            <a href="{{ route('amcs.pdf', ['amc' => $amc->id]) }}" class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold text-sm uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                <i class="fas fa-download mr-2"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Agreement Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-12 mx-auto max-w-4xl print:shadow-none print:border-0 print:p-0 min-h-[1100px]">
        
        <!-- Header for Print -->
        <div class="hidden print:flex justify-between items-start border-b-2 border-emerald-500 pb-8 mb-10">
            <div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter">{{ settings('site_name', 'TECH HUB') }}</h1>
                <p class="text-emerald-600 font-black uppercase tracking-widest text-xs mt-1">Information Technology Services</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">AMC Contract</p>
                <p class="text-lg font-black text-slate-800">#{{ $amc->contract_number }}</p>
            </div>
        </div>

        <!-- Dynamic Content -->
        <article class="prose prose-slate max-w-none prose-table:border prose-th:bg-gray-50 prose-th:px-4 prose-td:px-4 prose-th:py-2 prose-td:py-2">
            {!! $content !!}
        </article>

        <!-- Signature for Print -->
        <div class="mt-20 flex justify-between pt-10 border-t border-gray-100">
            <div class="w-48 text-center pt-2 border-t border-gray-300">
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Customer Signature</p>
            </div>
            <div class="w-48 text-center pt-2 border-t border-gray-300">
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Authorized Signatory</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        header, aside, .no-print, nav { display: none !important; }
        body { background: white !important; padding: 0 !important; }
        .bg-gray-100 { background: white !important; }
        main { padding: 0 !important; }
        .rounded-2xl { border-radius: 0 !important; }
        .shadow-xl { box-shadow: none !important; }
    }
</style>
@endsection
