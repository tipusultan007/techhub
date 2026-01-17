@extends('layouts.admin')

@section('header', 'Create Attribute')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <form action="{{ route('attributes.store') }}" method="POST">
        @csrf
        
        <!-- Attribute Name -->
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Attribute Name</label>
            <input type="text" name="name" class="w-full border border-gray-300 rounded p-2 focus:ring-blue-500" 
                   placeholder="e.g. Color, Size, Storage" required>
        </div>

        <!-- Dynamic Values -->
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Attribute Values</label>
            <div id="values-container" class="space-y-3">
                <!-- Initial Input -->
                <div class="flex gap-2">
                    <input type="text" name="values[]" class="w-full border border-gray-300 rounded p-2" placeholder="Value (e.g. Red)" required>
                    <button type="button" class="text-gray-400 cursor-not-allowed px-2"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            
            <button type="button" id="add-value-btn" class="mt-3 text-sm text-blue-600 font-bold hover:underline">
                + Add Another Value
            </button>
        </div>

        <div class="flex justify-end pt-4 border-t gap-3">
            <a href="{{ route('attributes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Save Attribute</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#add-value-btn').click(function() {
            let html = `
            <div class="flex gap-2">
                <input type="text" name="values[]" class="w-full border border-gray-300 rounded p-2" placeholder="Value" required>
                <button type="button" class="text-red-500 hover:text-red-700 px-2 remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`;
            $('#values-container').append(html);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).parent().remove();
        });
    });
</script>
@endsection