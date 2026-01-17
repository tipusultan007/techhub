@extends('layouts.admin')

@section('header', 'Banner Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @foreach(['main' => 'Main Banner', 'side_top' => 'Side Top Promo', 'side_bottom' => 'Side Bottom Promo'] as $pos => $label)
            @php $banner = $banners->get($pos); @endphp
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">{{ $label }}</h3>
                    <p class="text-sm text-gray-500">Manage the {{ strtolower($label) }} on the home page.</p>
                </div>

                <div class="p-6 flex-1">
                    <form action="{{ route('banners.update', $banner->id ?? 0) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Image Preview & Upload -->
                        <div x-data="{ photoName: null, photoPreview: null }" class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Banner Image</label>
                            
                            <div class="relative group">
                                <div class="w-full h-48 bg-gray-100 rounded-xl overflow-hidden border-2 border-dashed border-gray-200 group-hover:border-blue-400 transition-colors">
                                    <template x-if="!photoPreview">
                                        <div class="w-full h-full flex items-center justify-center">
                                            @if($banner && $banner->hasMedia('banner_image'))
                                                <img src="{{ $banner->getFirstMediaUrl('banner_image') }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="text-center">
                                                    <i class="ri-image-line text-4xl text-gray-300"></i>
                                                    <p class="text-xs text-gray-400 mt-1">No image uploaded</p>
                                                </div>
                                            @endif
                                        </div>
                                    </template>
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" class="w-full h-full object-cover">
                                    </template>
                                </div>
                                
                                <input type="file" name="banner_image" class="hidden" x-ref="photo"
                                       @change="
                                            photoName = $event.target.files[0].name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                photoPreview = e.target.result;
                                            };
                                            reader.readAsDataURL($event.target.files[0]);
                                       ">
                                
                                <button type="button" 
                                        class="absolute inset-0 w-full h-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-sm font-medium rounded-xl"
                                        @click.prevent="$refs.photo.click()">
                                    <i class="ri-upload-2-line mr-2 text-lg"></i> Change Image
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-400">Recommended: 1200x400 (Main), 400x200 (Side)</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                                <input type="text" name="title" value="{{ $banner->title ?? '' }}" 
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                                       placeholder="Enter banner title...">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Subtitle / Description</label>
                                <textarea name="subtitle" rows="2" 
                                          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                                          placeholder="Enter short description...">{{ $banner->subtitle ?? '' }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Badge Text</label>
                                    <input type="text" name="badge_text" value="{{ $banner->badge_text ?? '' }}" 
                                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                                           placeholder="e.g. NEW ARRIVAL">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Button Text</label>
                                    <input type="text" name="button_text" value="{{ $banner->button_text ?? '' }}" 
                                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                                           placeholder="e.g. Shop Now">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Target Link</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="ri-link text-gray-400"></i>
                                    </div>
                                    <input type="text" name="link" value="{{ $banner->link ?? '#' }}" 
                                           class="pl-10 w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                                           placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-auto">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transform transition active:scale-[0.98] flex items-center justify-center">
                                <i class="ri-save-line mr-2"></i> Update {{ $label }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Ensure AlpineJS is loaded -->
<script src="//unpkg.com/alpinejs" defer></script>
<!-- Ensure Remix Icons are loaded -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
@endsection
