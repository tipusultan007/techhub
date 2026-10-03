@extends('layouts.admin')
@section('header', 'Capital & Investments')

@section('content')
    <!-- Alpine.js Main Container -->
    <div class="w-full mx-auto" x-data="{ showModal: false, editData: {} }">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <!-- 1. Create Form -->
            <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200 h-fit">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Record Capital Transaction</h3>
                <form action="{{ route('capital-transactions.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Date</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded mt-1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Transaction Type</label>
                            <select name="type" class="w-full border p-2 rounded mt-1 bg-white" required>
                                <option value="investment">Investment (Money In)</option>
                                <option value="withdrawal">Withdrawal / Drawing (Money Out)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Amount (AED)</label>
                            <input type="number" step="0.01" name="amount" class="w-full border p-2 rounded mt-1" placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Bank / Cash Account</label>
                            <select name="bank_account_id" class="w-full border p-2 rounded mt-1 bg-white" required>
                                <option value="" disabled selected>Select an account...</option>
                                @foreach($bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Equity Account</label>
                            <select name="equity_account_id" class="w-full border p-2 rounded mt-1 bg-white" required>
                                <option value="" disabled selected>Select an account...</option>
                                @foreach($equityAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Reference / Note</label>
                            <input type="text" name="note" class="w-full border p-2 rounded mt-1" placeholder="e.g. Initial Capital, Dividend Payout">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700">Save Transaction</button>
                    </div>
                </form>
            </div>

            <!-- 2. List -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Accounts Impacted</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">{{ $txn->date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($txn->type == 'investment')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold">Investment</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-bold">Withdrawal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="font-bold">{{ $txn->bankAccount->name ?? '-' }}</div>
                                <div class="text-xs">{{ $txn->equityAccount->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-right {{ $txn->type == 'investment' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $txn->type == 'investment' ? '+' : '-' }}{{ number_format($txn->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-4">
                                    <button @click="showModal = true; editData = {{ $txn }}" class="text-blue-600 hover:text-blue-900" title="Edit"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('capital-transactions.destroy', $txn) }}" method="POST" class="inline m-0 p-0 flex">
                                        @csrf @method('DELETE')
                                        <button type="button" 
                                            class="text-red-600 hover:text-red-900 btn-delete-confirm" 
                                            title="Delete"
                                            data-type="Transaction"
                                            data-title="Delete Transaction?"
                                            data-summary='{
                                                "Date": "{{ $txn->date->format("d M Y") }}",
                                                "Type": "{{ ucfirst($txn->type) }}",
                                                "Amount": "AED {{ number_format($txn->amount, 2) }}"
                                            }'>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center p-6 text-gray-500">No transactions found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>

        <!-- === EDIT MODAL === -->
        <div x-show="showModal" style="display: none;"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg" @click.away="showModal = false">
                <h3 class="font-bold text-xl mb-4">Edit Transaction</h3>

                <form :action="'/backend/capital-transactions/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Date</label>
                            <input type="date" name="date" :value="editData.date ? editData.date.split('T')[0] : ''" class="w-full border p-2 rounded mt-1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Type</label>
                            <select name="type" x-model="editData.type" class="w-full border p-2 rounded mt-1 bg-white" required>
                                <option value="investment">Investment</option>
                                <option value="withdrawal">Withdrawal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Amount (AED)</label>
                            <input type="number" step="0.01" name="amount" x-model="editData.amount" class="w-full border p-2 rounded mt-1" required>
                        </div>
                        <div class="md:col-span-1"></div>
                        
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700">Bank / Cash Account</label>
                            <select name="bank_account_id" class="w-full border p-2 rounded mt-1 bg-white" required>
                                @foreach($bankAccounts as $acc)
                                    <option :value="{{ $acc->id }}" :selected="editData.bank_account_id == {{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700">Equity Account</label>
                            <select name="equity_account_id" class="w-full border p-2 rounded mt-1 bg-white" required>
                                @foreach($equityAccounts as $acc)
                                    <option :value="{{ $acc->id }}" :selected="editData.equity_account_id == {{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700">Reference / Note</label>
                            <input type="text" name="note" x-model="editData.note" class="w-full border p-2 rounded mt-1">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded text-gray-700 hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AlpineJS -->
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection
