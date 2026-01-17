@extends('layouts.admin')

@section('header', 'Edit Attribute')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <form action="{{ route('attributes.update', $attribute) }}" method="POST">
        @csrf @method('PUT')
        
        <!-- Attribute Name -->
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Attribute Name</label>
            <input type="text" name="name" value="{{ old('name', $attribute->name) }}" 
                   class="w-full border border-gray-300 rounded p-2 focus:ring-blue-500" required>
        </div>

        <!-- Existing Values -->
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Existing Values</label>
            <div class="space-y-3 bg-gray-50 p-4 rounded border">
                @foreach($attribute->values as $val)
                <div class="flex gap-2 items-center">
                    <!-- Update existing value -->
                    <input type="text" name="existing_values[{{ $val->id }}]" value="{{ $val->value }}" 
                           class="w-full border border-gray-300 rounded p-2 bg-white">
                    
                    <!-- Delete Button (Form inside Form workaround not needed, use Link or separate API) -->
                    <!-- We use a button that triggers a hidden form submission or direct link -->
                    <a href="#" onclick="event.preventDefault(); document.getElementById('delete-val-{{ $val->id }}').submit();" 
                       class="text-red-500 hover:text-red-700 px-2" title="Delete Value">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- New Values -->
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Add New Values</label>
            <div id="new-values-container" class="space-y-3">
                <!-- JS appends here -->
            </div>
            <button type="button" id="add-value-btn" class="mt-2 text-sm text-blue-600 font-bold hover:underline">
                + Add New Value Row
            </button>
        </div>

        <div class="flex justify-end pt-4 border-t gap-3">
            <a href="{{ route('attributes.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Update Attribute</button>
        </div>
    </form>

    <!-- Hidden Delete Forms for Values -->
    @foreach($attribute->values as $val)
        <form id="delete-val-{{ $val->id }}" action="{{ route('attributes.value.destroy', $val->id) }}" method="POST" style="display: none;">
            @csrf @method('DELETE')
        </form>
    @endforeach
</div>

<script>
    $(document).ready(function() {
        $('#add-value-btn').click(function() {
            let html = `
            <div class="flex gap-2">
                <input type="text" name="new_values[]" class="w-full border border-gray-300 rounded p-2" placeholder="New Value" required>
                <button type="button" class="text-red-500 hover:text-red-700 px-2 remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`;
            $('#new-values-container').append(html);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).parent().remove();
        });
    });
</script>
@endsection