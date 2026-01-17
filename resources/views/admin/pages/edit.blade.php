@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('pages.admin.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Edit Page: {{ $page->title }}</h2>
    </div>

    <form action="{{ route('pages.admin.update', $page->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <div class="space-y-2">
                        <label for="title" class="text-sm font-semibold text-gray-700">Page Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Enter page title...">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="content" class="text-sm font-semibold text-gray-700">Page Content</label>
                        <textarea name="content" id="content" class="tinymce">{{ old('content', $page->content) }}</textarea>
                        @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-bold text-gray-800 border-b border-gray-50 pb-2">SEO Settings</h3>
                    <div class="space-y-2">
                        <label for="meta_title" class="text-sm font-semibold text-gray-700">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="SEO Browser Title...">
                    </div>
                    <div class="space-y-2">
                        <label for="meta_description" class="text-sm font-semibold text-gray-700">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Brief summary for search engines...">{{ old('meta_description', $page->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <div class="space-y-2">
                        <label for="slug" class="text-sm font-semibold text-gray-700">Custom URL Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="e.g. about-us">
                        <p class="text-xs text-gray-500 font-medium italic">Warning: Changing the slug will break old links.</p>
                        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="is_active" class="text-sm font-semibold text-gray-700">Publication Status</label>
                        <select name="is_active" id="is_active" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                            <option value="1" {{ old('is_active', $page->is_active) == '1' ? 'selected' : '' }}>Published (Visible)</option>
                            <option value="0" {{ old('is_active', $page->is_active) == '0' ? 'selected' : '' }}>Draft (Hidden)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Update Page
                    </button>
                </div>

                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 italic text-sm text-blue-700">
                    <p><strong>Pro Tip:</strong> Ensure your content is readable and use Heading tags (H1, H2, H3) for better SEO performance.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#content',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount emoticons codesample',
        toolbar1: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
        toolbar2: 'link image media table | emoticons charmap | removeformat | codesample | code fullscreen preview',
        height: 600,
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
