@extends('layouts.admin')

@section('header', 'Edit Offer Popup')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Edit Popup</h2>
            <nav class="flex text-sm font-medium text-gray-500 mt-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('popups.admin.index') }}" class="hover:text-gray-900 transition-colors">Popups</a>
                    </li>
                    <li><span class="text-gray-300">/</span></li>
                    <li>
                        <span class="text-gray-900" aria-current="page">Edit</span>
                    </li>
                    <li><span class="text-gray-300">/</span></li>
                    <li>
                        <span class="text-gray-600 font-normal">{{ Str::limit($popup->title, 20) }}</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('popups.admin.preview', $popup->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-eye mr-2"></i> Preview
            </a>
        </div>
    </div>

    <form action="{{ route('popups.admin.update', $popup->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white shadow-xl sm:rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Popup Content</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                                value="{{ old('title', $popup->title) }}">
                            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Subtitle -->
                        <div>
                            <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                            <textarea name="subtitle" id="subtitle" rows="3"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border">{{ old('subtitle', $popup->subtitle) }}</textarea>
                            @error('subtitle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Image -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Popup Image</label>
                            
                            @if($popup->image_path)
                                <div class="mt-2 mb-4 p-2 border border-gray-200 rounded-lg inline-block bg-gray-50">
                                    <div class="relative group">
                                        <img src="{{ Storage::url($popup->image_path) }}" alt="Current Image" class="h-32 w-auto object-cover rounded-md">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition rounded-md"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 text-center">Current</p>
                                </div>
                            @endif

                            <div class="mt-1 flex items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                                <div class="space-y-1 text-center w-full">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="image-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a new file</span>
                                            <input id="image-upload" name="image" type="file" class="sr-only">
                                        </label>
                                        <p class="pl-1">to replace</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF (Max 2MB)</p>
                                </div>
                            </div>
                            @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Button & Link -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="button_text" class="block text-sm font-medium text-gray-700">Button Text</label>
                                <input type="text" name="button_text" id="button_text"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                                    value="{{ old('button_text', $popup->button_text) }}">
                            </div>
                            <div>
                                <label for="link" class="block text-sm font-medium text-gray-700">Target Link</label>
                                <input type="text" name="link" id="link"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                                    value="{{ old('link', $popup->link) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Actions -->
            <div class="space-y-6">
                <div class="bg-white shadow-xl sm:rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Display Settings</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Active Toggle -->
                        <div class="flex flex-col gap-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $popup->is_active) ? 'checked' : '' }}>
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900">Set as Active</span>
                            </label>
                            <p class="text-xs text-yellow-600 bg-yellow-50 p-2 rounded border border-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Only one popup can be active at a time.
                            </p>
                        </div>

                        <!-- Timing -->
                        <div>
                            <label for="display_delay" class="block text-sm font-medium text-gray-700">Display Delay (Seconds)</label>
                            <input type="number" name="display_delay" id="display_delay" min="0"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                                value="{{ old('display_delay', $popup->display_delay) }}">
                        </div>

                        <div>
                            <label for="cookie_duration" class="block text-sm font-medium text-gray-700">Cookie Duration (Days)</label>
                            <input type="number" name="cookie_duration" id="cookie_duration" min="1"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                                value="{{ old('cookie_duration', $popup->cookie_duration) }}">
                        </div>

                        <!-- Scheduling -->
                        <div class="border-t border-gray-100 pt-4 mt-4">
                            <span class="block text-xs font-semibold text-gray-500 uppercase mb-3">Scheduling (Optional)</span>
                            <div class="space-y-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                                        value="{{ $popup->start_date ? $popup->start_date->format('Y-m-d') : '' }}">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input type="date" name="end_date" id="end_date"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 border"
                                        value="{{ $popup->end_date ? $popup->end_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                            <a href="{{ route('popups.admin.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md transition-colors">
                                Update Popup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
