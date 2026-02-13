@extends('layouts.admin')

@section('header', 'Add New Brand')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
    <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Name Input -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Brand Name</label>
            <div class="mt-1">
                <input type="text" name="name" id="name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2" placeholder="e.g. Samsung" required>
            </div>
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Image Input -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Brand Logo</label>
            <div class="mt-1 flex items-center">
                <input type="file" name="image" class="block w-full text-sm text-slate-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-indigo-50 file:text-indigo-700
                  hover:file:bg-indigo-100">
            </div>
            @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Featured Toggle -->
        <div class="flex items-center">
            <input type="checkbox" name="is_featured" id="is_featured" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="is_featured" class="ml-2 block text-sm text-gray-900">Show on Home Page</label>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('brands.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Save Brand</button>
        </div>
    </form>
</div>
@endsection