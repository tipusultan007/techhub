@extends('layouts.admin')

@section('header', 'Import Products')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center text-sm text-gray-500">
        <a href="{{ route('products.index') }}" class="hover:text-blue-600">Products</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-medium">Import</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900">Import from Excel</h3>
            <p class="text-sm text-gray-500 mt-1">Upload an Excel or CSV file to create products in bulk.</p>
        </div>

        <div class="p-8">
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-bold text-blue-900 mb-2">Required File Structure</h4>
                    <p class="text-xs text-blue-800 mb-2">Your Excel file must contain the following columns (headers are case-insensitive):</p>
                    <ul class="list-disc list-inside text-xs text-blue-700 space-y-1 ml-1">
                        <li><strong>Title</strong>: Product Name</li>
                        <li><strong>Price</strong>: Selling Price</li>
                        <li><strong>Old Price</strong>: (Optional) Original Price (triggers sale logic)</li>
                        <li><strong>Image</strong>: (Optional) Public URL of the product image</li>
                    </ul>
                </div>

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                    <input type="file" name="file" accept=".csv, .xls, .xlsx" required
                        class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2.5 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-bold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        cursor-pointer border border-gray-300 rounded-lg p-1">
                    <p class="text-xs text-gray-400 mt-2">Supported formats: .xlsx, .xls, .csv (Max 10MB)</p>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition text-sm">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-md hover:shadow-lg transition text-sm">
                        <i class="fas fa-file-import mr-2"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
