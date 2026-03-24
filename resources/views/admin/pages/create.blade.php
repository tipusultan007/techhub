@extends('layouts.admin')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding-top: 6px;
            padding-left: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-dropdown {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>
@endpush

@section('content')
<div class="p-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('pages.admin.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Add New Page</h2>
    </div>

    <form action="{{ route('pages.admin.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <div class="space-y-2">
                        <label for="title" class="text-sm font-semibold text-gray-700">Page Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Enter page title...">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="content" class="text-sm font-semibold text-gray-700">Page Content</label>
                        <textarea name="content" id="content" class="tinymce">{{ old('content') }}</textarea>
                        @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-bold text-gray-800 border-b border-gray-50 pb-2">SEO Settings</h3>
                    <div class="space-y-2">
                        <label for="meta_title" class="text-sm font-semibold text-gray-700">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="SEO Browser Title...">
                    </div>
                    <div class="space-y-2">
                        <label for="meta_keywords" class="text-sm font-semibold text-gray-700">Meta Keywords</label>
                        <textarea name="meta_keywords" id="meta_keywords" rows="2"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="e.g. keyword1, keyword2...">{{ old('meta_keywords') }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label for="meta_description" class="text-sm font-semibold text-gray-700">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="3"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="Brief summary for search engines...">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
                    <div class="space-y-2">
                        <label for="slug" class="text-sm font-semibold text-gray-700">Custom URL Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="e.g. about-us (optional)">
                        <p class="text-xs text-gray-500 italic">Leave empty to generate from title.</p>
                        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="redirect_url" class="text-sm font-semibold text-gray-700">Redirect URL (Optional)</label>
                        <input type="text" name="redirect_url" id="redirect_url" value="{{ old('redirect_url') }}"
                            class="w-full px-4 py-2 border border-blue-100 bg-blue-50 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="https://example.com/other-page">
                        <p class="text-xs text-blue-600 italic">If filled, this page will redirect to this URL.</p>
                        @error('redirect_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Page Type & Mirroring -->
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        <div class="space-y-2">
                            <label for="type" class="text-sm font-semibold text-gray-700">Page Type</label>
                            <select name="type" id="type" onchange="toggleReferenceField()"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none bg-white">
                                <option value="static" {{ old('type') == 'static' ? 'selected' : '' }}>Static Page (Standard)</option>
                                <option value="category" {{ old('type') == 'category' ? 'selected' : '' }}>Mirror Category Page</option>
                                <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Mirror Product Page</option>
                            </select>
                            <p class="text-xs text-gray-500">Choose "Mirror" to show content from another page.</p>
                        </div>

                        <!-- Category Selection -->
                        <div id="category_field" class="space-y-2 {{ old('type') == 'category' ? '' : 'hidden' }}">
                            <label for="category_id" class="text-sm font-semibold text-gray-700">Select Category</label>
                            <select name="reference_id_category" id="category_id" 
                                class="w-full px-4 py-2 border border-emerald-200 bg-emerald-50 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('reference_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Product Selection -->
                        <div id="product_field" class="space-y-2 {{ old('type') == 'product' ? '' : 'hidden' }}">
                            <label for="product_id" class="text-sm font-semibold text-gray-700">Select Product</label>
                            <select name="reference_id_product" id="product_id" 
                                class="w-full px-4 py-2 border border-emerald-200 bg-emerald-50 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                                <option value="">-- Choose Product --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" {{ old('reference_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                                @endforeach
                            </select>
                            {{-- Consider using a searchable select in a real app --}}
                        </div>

                        <!-- Hidden fields for the actual reference_id -->
                        <input type="hidden" name="reference_id" id="reference_id" value="{{ old('reference_id') }}">
                    </div>

                    <div class="space-y-2 pt-4 border-t border-gray-100">
                        <label for="canonical_url" class="text-sm font-semibold text-gray-700">Canonical URL (Optional)</label>
                        <input type="text" name="canonical_url" id="canonical_url" value="{{ old('canonical_url') }}"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none"
                            placeholder="https://example.com/canonical-source">
                        <p class="text-xs text-gray-500 italic">Overrides the default canonical link.</p>
                    </div>

                    <div class="space-y-2">
                        <label for="show_on_footer" class="text-sm font-semibold text-gray-700">Display in Footer</label>
                        <select name="show_on_footer" id="show_on_footer" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                            <option value="0" {{ old('show_on_footer') == '0' ? 'selected' : '' }}>No (Hide from Footer)</option>
                            <option value="1" {{ old('show_on_footer') == '1' ? 'selected' : '' }}>Yes (Show in Footer)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="is_active" class="text-sm font-semibold text-gray-700">Publication Status</label>
                        <select name="is_active" id="is_active" 
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Published (Visible)</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Draft (Hidden)</option>
                        </select>
                    </div>

                    <button type="submit" onclick="prepareSubmit()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Publish Page
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleReferenceField() {
        const type = document.getElementById('type').value;
        document.getElementById('category_field').classList.add('hidden');
        document.getElementById('product_field').classList.add('hidden');
        
        if (type === 'category') {
            document.getElementById('category_field').classList.remove('hidden');
        } else if (type === 'product') {
            document.getElementById('product_field').classList.remove('hidden');
        }
    }

    function prepareSubmit() {
        const type = document.getElementById('type').value;
        const refIdInput = document.getElementById('reference_id');
        
        if (type === 'category') {
            refIdInput.value = document.getElementById('category_id').value;
        } else if (type === 'product') {
            refIdInput.value = document.getElementById('product_id').value;
        } else {
            refIdInput.value = '';
        }
    }
</script>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#category_id, #product_id').select2({
            placeholder: "-- Choose --",
            allowClear: true
        });
    });

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
