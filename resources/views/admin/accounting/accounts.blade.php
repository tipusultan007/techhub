@extends('layouts.admin')
@section('header', 'Chart of Accounts')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-lg">Accounts</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Balance</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($accounts as $account)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-bold">{{ $account->code }}</td>
                    <td class="px-6 py-4 text-sm">{{ $account->name }}</td>
                    <td class="px-6 py-4 text-sm capitalize">{{ $account->type }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold">{{ number_format($account->balance, 2) }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('accounting.accounts.show', $account) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-lg transition-colors">
                            <i class="fas fa-list"></i> View Ledger
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
