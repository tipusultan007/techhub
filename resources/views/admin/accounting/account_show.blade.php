@extends('layouts.admin')
@section('header', 'Account Details: ' . $account->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Account Summary Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 font-bold text-lg shadow-inner">
                        {{ $account->code }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ $account->name }}</h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[0.65rem] font-bold uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100 mt-1">
                            {{ $account->type }} Account
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right bg-slate-50 p-4 rounded-xl border border-slate-100 min-w-[200px]">
                <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-1">Current Balance</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format($account->balance, 2) }} <span class="text-sm font-bold text-slate-400">AED</span></p>
            </div>
        </div>
    </div>

    <!-- Ledger Entries -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-list text-slate-400"></i> General Ledger Entries
            </h3>
            <a href="{{ route('accounting.accounts') }}" class="text-sm font-bold text-slate-500 hover:text-emerald-600 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to Chart
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-left text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Description / Ref</th>
                        <th class="px-6 py-4 text-right text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Debit</th>
                        <th class="px-6 py-4 text-right text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($ledgerEntries as $entry)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600">
                            {{ \Carbon\Carbon::parse($entry->journalEntry->date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            <p class="font-bold mb-0.5">{{ $entry->description ?: $entry->journalEntry->description }}</p>
                            
                            @if($entry->journalEntry->reference)
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.6rem] font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ class_basename($entry->journalEntry->reference_type) }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-mono">
                                        #{{ $entry->journalEntry->reference_id }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-slate-400 font-medium">Manual Entry #{{ $entry->journalEntry->id }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-slate-700">
                            {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-slate-700">
                            {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                <i class="fas fa-folder-open text-2xl text-slate-300"></i>
                            </div>
                            <p class="text-slate-500 font-medium">No ledger entries found for this account.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($ledgerEntries->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $ledgerEntries->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
