@extends('layouts.admin')
@section('header', 'Journal Entries')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b">
            <h3 class="font-bold text-lg">Journal Entries</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Account Lines</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Debit</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Credit</th>
                </tr>
            </thead>
            @foreach($journals as $journal)
            <tbody class="divide-y divide-gray-100 bg-white border-b-2 border-gray-100">
                @foreach($journal->ledgerEntries as $index => $entry)
                <tr class="hover:bg-gray-50 transition-colors">
                    @if($index === 0)
                    <td rowspan="{{ $journal->ledgerEntries->count() }}" class="px-6 py-4 text-sm font-bold align-top text-gray-700 bg-white/50 w-32 border-r border-gray-50">
                        {{ $journal->date->format('d M Y') }}
                    </td>
                    <td rowspan="{{ $journal->ledgerEntries->count() }}" class="px-6 py-4 text-sm align-top text-gray-800 bg-white/50 w-64 border-r border-gray-50">
                        {{ $journal->description }}
                        @if($journal->reference)
                            <div class="mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[0.65rem] font-bold uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                {{ class_basename($journal->reference_type) }} #{{ $journal->reference_id }}
                            </div>
                        @endif
                    </td>
                    @endif
                    <td class="px-6 py-2.5 text-sm {{ $entry->credit > 0 ? 'pl-10 text-gray-600' : 'font-bold text-gray-800' }}">
                        {{ $entry->account->name }}
                    </td>
                    <td class="px-6 py-2.5 text-right text-sm font-medium {{ $entry->debit > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                    </td>
                    <td class="px-6 py-2.5 text-right text-sm font-medium {{ $entry->credit > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            @endforeach
        </table>
        <div class="p-4 border-t">
            {{ $journals->links() }}
        </div>
    </div>
</div>
@endsection
