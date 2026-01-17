@extends('layouts.admin')
@section('header', 'Manage Expenses')

@section('content')
    <!-- Alpine.js Main Container -->
    <div class="max-w-7xl mx-auto" x-data="{ showModal: false, editData: {} }">

        <!-- === CREATE & FILTER SECTION === -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <!-- 1. Create Form -->
            <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200 h-fit">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Record New Expense</h3>
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Date</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded mt-1">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Amount (AED)</label>
                            <input type="number" step="0.01" name="amount" class="w-full border p-2 rounded mt-1" placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Category</label>
                            <div class="flex gap-2">
                                <select name="expense_category_id" class="w-full border p-2 rounded mt-1 bg-white" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('expense-categories.index') }}" class="mt-1 bg-gray-200 px-3 py-2 rounded flex items-center justify-center hover:bg-gray-300" title="Manage Categories">+</a>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Reference / Note</label>
                            <input type="text" name="note" class="w-full border p-2 rounded mt-1" placeholder="e.g. DEWA Bill, Rent, etc.">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700">Save Expense</button>
                    </div>
                </form>
            </div>

            <!-- 2. List & Filters -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Filter Form -->
                <form method="GET" class="p-4 border-b bg-gray-50 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="text-xs font-bold text-gray-500">From Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border p-2 rounded mt-1">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500">To Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border p-2 rounded mt-1">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500">Category</label>
                        <select name="category_id" class="w-full border p-2 rounded mt-1 bg-white">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3 flex justify-end gap-2">
                        <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-gray-300 rounded text-gray-700 hover:bg-gray-400">Clear</a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Filter</button>
                    </div>
                </form>

                <!-- Table -->
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Added By</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">{{ $expense->date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm">{{ $expense->category->name }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-red-600">{{ number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $expense->user->name }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <button @click="showModal = true; editData = {{ $expense }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Edit"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Delete expense?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center p-6 text-gray-500">No expenses found for this period.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>

        <!-- === EDIT MODAL === -->
        <div x-show="showModal" style="display: none;"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">

            <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg" @click.away="showModal = false">
                <h3 class="font-bold text-xl mb-4">Edit Expense</h3>

                <form :action="'/backend/expenses/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Date</label>
                            <input type="date" name="date" :value="editData.date ? editData.date.split('T')[0] : ''" class="w-full border p-2 rounded mt-1">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Amount (AED)</label>
                            <input type="number" step="0.01" name="amount" x-model="editData.amount" class="w-full border p-2 rounded mt-1">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700">Category</label>
                            <select name="expense_category_id" class="w-full border p-2 rounded mt-1 bg-white">
                                @foreach($categories as $cat)
                                    <option :value="{{ $cat->id }}" :selected="editData.expense_category_id == {{ $cat->id }}">{{ $cat->name }}</option>
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
