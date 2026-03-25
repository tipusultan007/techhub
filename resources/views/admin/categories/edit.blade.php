@extends('layouts.admin')

@section('header', 'Edit Category')

@section('content')

    <!-- 1. LOAD DEPENDENCIES -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">

        <!-- Alpine Scope for Name/Slug Binding -->
        <div x-data="{
        name: '{{ old('name', $category->name) }}',
        slug: '{{ old('slug', $category->slug) }}',
        generateSlug() {
            // Only auto-generate if the user is typing, logic can be adjusted
            this.slug = this.name.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        }
    }">

            <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="col-span-1">
                        <label for="name" class="block text-sm font-bold text-gray-700">Category Name</label>
                        <div class="mt-1">
                            <!-- x-model syncs the input with Alpine data -->
                            <input type="text" name="name" id="name" x-model="name" @input="generateSlug()"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5" required>
                        </div>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug -->
                    <div class="col-span-1">
                        <label for="slug" class="block text-sm font-bold text-gray-700">Slug (URL)</label>
                        <div class="mt-1">
                            <input type="text" name="slug" id="slug" x-model="slug"
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5 bg-gray-50 text-gray-600" required>
                        </div>
                        @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Parent Dropdown -->
                <div>
                    <label for="parent_id" class="block text-sm font-bold text-gray-700">Parent Category</label>
                    <div class="mt-1">
                        <select id="parent_id" name="parent_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5 bg-white">
                            <option value="">None (Top Level Category)</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- --- ICON PICKER (INLINE LOGIC WITH PRE-FILL) --- -->
                <div x-data="{
                isOpen: false,
                // PRE-FILL DATA FROM SERVER
                search: '{{ $category->icon_class }}',
                selectedIcon: '{{ $category->icon_class }}',

                icons: [
                    'ri-smartphone-line', 'ri-computer-line', 'ri-macbook-line', 'ri-hard-drive-2-line',
                    'ri-tv-line', 'ri-camera-lens-line', 'ri-headphone-line', 'ri-speaker-line',
                    'ri-gamepad-line', 'ri-watch-line', 'ri-printer-line', 'ri-router-line',
                    'ri-cpu-line', 'ri-battery-charge-line', 'ri-plug-line', 'ri-usb-line',
                    'ri-sim-card-line', 'ri-sd-card-line', 'ri-mouse-line', 'ri-keyboard-box-line',
                    'ri-tablet-line', 'ri-flight-takeoff-line', 'ri-fire-line', 'ri-vidicon-line',
                    'ri-scissors-cut-line', 'ri-webcam-line', 'ri-wifi-line', 'ri-bluetooth-line',
                    'ri-shopping-bag-line', 'ri-shopping-cart-2-line', 'ri-gift-line', 'ri-price-tag-3-line',
                    'ri-store-2-line', 'ri-wallet-3-line', 'ri-truck-line', 'ri-briefcase-line',
                    'ri-calculator-line', 'ri-projector-2-line', 'ri-archive-line', 'ri-settings-3-line',
                    'ri-lightbulb-line', 'ri-shield-check-line', 'ri-trophy-line', 'ri-rocket-line', 'ri-star-line'
                ],
                get filteredIcons() {
                    // Show all if search matches selected, otherwise filter
                    if (this.search === '' || this.search === this.selectedIcon) return this.icons;
                    return this.icons.filter(i => i.toLowerCase().includes(this.search.toLowerCase()));
                }
            }" class="border-t border-gray-100 pt-6">

                    <label class="block text-sm font-bold text-gray-700 mb-2">Category Icon</label>

                    <input type="hidden" name="icon_class" :value="selectedIcon">

                    <div class="relative">
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
                            <div class="bg-gray-50 p-3 border-r border-gray-300 flex items-center justify-center w-12 h-12">
                                <i :class="selectedIcon ? selectedIcon : 'ri-search-line'" class="text-xl text-gray-700"></i>
                            </div>

                            <input type="text" x-model="search" @focus="isOpen = true" @click.away="isOpen = false"
                                   placeholder="Click to search icons..."
                                   class="w-full p-3 outline-none text-sm text-gray-700 h-12">

                            <button type="button" @click="selectedIcon = ''; search = ''" class="p-3 text-gray-400 hover:text-red-500 h-12 flex items-center">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>

                        <div x-show="isOpen" style="display: none;"
                             class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto p-2 grid grid-cols-8 gap-2">
                            <template x-for="icon in filteredIcons" :key="icon">
                                <button type="button" @click="selectedIcon = icon; search = icon; isOpen = false"
                                        :class="{'bg-indigo-50 border-indigo-500 text-indigo-600': selectedIcon === icon}"
                                        class="flex items-center justify-center p-2 rounded hover:bg-gray-100 border border-transparent h-10 transition">
                                    <i :class="icon" class="text-lg"></i>
                                </button>
                            </template>
                            <div x-show="filteredIcons.length === 0" class="col-span-8 text-center text-xs text-gray-400 py-2">No icons found.</div>
                        </div>
                    </div>
                </div>

                <!-- Priority & Featured -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_featured" name="is_featured" value="1" type="checkbox"
                                   {{ $category->is_featured ? 'checked' : '' }}
                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_featured" class="font-medium text-gray-700">Feature on Homepage</label>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <label for="priority" class="text-sm font-bold text-gray-700 whitespace-nowrap">Display Priority</label>
                        <input type="number" name="priority" id="priority" value="{{ old('priority', $category->priority) }}" min="0"
                               class="block w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                        <p class="text-xs text-gray-400">(Lower = First)</p>
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="mt-4 border-t border-gray-100 pt-6">
                    <label class="block text-sm font-bold text-gray-700 mb-4">Category Banner Image</label>

                    <div class="flex items-center space-x-6">
                        <!-- Current Image Preview -->
                        @if($category->hasMedia('category_image'))
                            <div class="shrink-0 text-center">
                                <p class="text-xs text-gray-500 mb-2">Current</p>
                                <img src="{{ $category->getFirstMediaUrl('category_image') }}" alt="Category" class="h-20 w-20 object-cover border border-gray-200 rounded-lg p-1 bg-gray-50">
                            </div>
                        @endif

                        <div class="grow">
                            <input type="file" name="image" class="block w-full text-sm text-slate-500
                        file:mr-4 file:py-2.5 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100 transition">
                            <p class="text-xs text-gray-500 mt-1">Upload to replace the current image.</p>
                        </div>
                    </div>
                </div>

                <!-- SEO Meta Tags -->
                <div class="mt-8 border-t border-gray-100 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">SEO Meta Tags</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-bold text-gray-700">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $category->meta_title) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5"
                                   placeholder="SEO Title (Optional)">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-bold text-gray-700">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3"
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5"
                                      placeholder="Brief description for search results...">{{ old('meta_description', $category->meta_description) }}</textarea>
                        </div>
                        <div>
                            <label for="meta_keywords" class="block text-sm font-bold text-gray-700">Meta Keywords</label>
                            <textarea name="meta_keywords" id="meta_keywords" rows="2"
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5"
                                      placeholder="keyword1, keyword2, keyword3...">{{ old('meta_keywords', $category->meta_keywords) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6">
                    <a href="{{ route('categories.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 font-medium">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium shadow-sm">Update Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection
