@extends('layouts.admin')
@section('header', 'Expense Categories')

@section('content')
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Create Form -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 h-fit">
            <h3 class="font-bold text-gray-700 mb-4">Add Category</h3>
            <form action="{{ route('expense-categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700">Name</label>
                    <input type="text" name="name" class="w-full border p-2 rounded mt-1" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700">Description</label>
                    <textarea name="description" class="w-full border p-2 rounded mt-1"></textarea>
                </div>
                <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Save</button>
            </form>
        </div>

        <!-- List -->
        <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Records</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($categories as $cat)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $cat->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $cat->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $cat->expenses_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('expense-categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
