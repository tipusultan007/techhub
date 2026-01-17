@extends('layouts.admin')

@section('header', 'Add New Solution')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('solutions.admin.index') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Create New IT Solution</h1>
    </div>

    <form action="{{ route('solutions.admin.store') }}" method="POST" class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        @csrf
        <div class="p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="space-y-2">
                    <label for="title" class="text-sm font-semibold text-gray-700">Solution Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        placeholder="e.g. Network Infrastructure Solutions">
                    @error('title') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                <!-- Icon Class -->
                <div class="space-y-2">
                    <label for="icon_class" class="text-sm font-semibold text-gray-700">Icon Class (Remix Icon)</label>
                    <input type="text" name="icon_class" id="icon_class" value="{{ old('icon_class', 'ri-service-line') }}" 
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        placeholder="e.g. ri-router-line">
                    <p class="text-xs text-gray-400">Use <a href="https://remixicon.com/" target="_blank" class="text-blue-500 underline">Remix Icons</a> classes.</p>
                </div>
            </div>

            <!-- Summary -->
            <div class="space-y-2">
                <label for="summary" class="text-sm font-semibold text-gray-700">Short Summary</label>
                <textarea name="summary" id="summary" rows="3" required
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                    placeholder="Briefly describe what this solution covers (shown on cards)...">{{ old('summary') }}</textarea>
                @error('summary') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <!-- Description (Rich Text) -->
            <div class="space-y-2">
                <label for="description" class="text-sm font-semibold text-gray-700">Full Description</label>
                <textarea name="description" id="description" class="tinymce">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                <!-- Order -->
                <div class="space-y-2">
                    <label for="order" class="text-sm font-semibold text-gray-700">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" 
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                </div>

                <!-- Active Status -->
                <div class="flex items-center pt-8">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:width-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-semibold text-gray-700">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('solutions.admin.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold text-sm hover:bg-gray-100 transition-all">Cancel</a>
            <button type="submit" class="px-8 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">Create Solution</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#description',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount emoticons codesample',
        toolbar1: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
        toolbar2: 'link image media table | emoticons charmap | removeformat | codesample | code fullscreen preview',
        height: 500,
        menubar: 'edit insert view format table help',
        branding: false,
        promotion: false,
        elementpath: true,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #334155; }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>
@endpush
