@extends('layouts.admin')

@section('header', 'Banner Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
<div class="max-w-7xl mx-auto space-y-10">
    
    <!-- MAIN BANNER SLIDER SECTION -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Main Banner Slider</h3>
                <p class="text-sm text-gray-500">Add and manage slides for the homepage main slider.</p>
            </div>
            <button onclick="openModal('addMainBannerModal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-all flex items-center">
                <i class="ri-add-line mr-2"></i> Add New Slide
            </button>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3">Preview</th>
                            <th class="px-4 py-3">Content</th>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($mainBanners as $banner)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <img src="{{ $banner->getFirstMediaUrl('banner_image') }}" class="w-24 h-12 object-cover rounded-lg shadow-sm">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $banner->title }}</div>
                                    <div class="text-[11px] text-gray-500 truncate max-w-xs">{{ $banner->subtitle }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $banner->order }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right space-x-2">
                                    <button onclick="editBanner({{ $banner }})" class="text-blue-600 hover:text-blue-900 text-sm font-semibold">Edit</button>
                                    <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold" onclick="return confirm('Delete this slide?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($mainBanners->isEmpty())
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-400 italic">No slides added yet.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SIDE BANNERS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach(['side_top' => ['label' => 'Side Top Promo', 'banner' => $sideTop], 'side_bottom' => ['label' => 'Side Bottom Promo', 'banner' => $sideBottom]] as $pos => $data)
            @php $banner = $data['banner']; $label = $data['label']; @endphp
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">{{ $label }}</h3>
                    <p class="text-sm text-gray-500">Static promotional banner.</p>
                </div>

                <div class="p-6 flex-1">
                    <form action="{{ $banner->exists ? route('banners.update', $banner->id) : route('banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @if(!$banner->exists) <input type="hidden" name="position" value="{{ $pos }}"> @endif
                        
                        <!-- Image Preview & Upload -->
                        <div x-data="{ photoName: null, photoPreview: null }" class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Banner Image</label>
                            
                            <div class="relative group">
                                <div class="w-full h-40 bg-gray-100 rounded-xl overflow-hidden border-2 border-dashed border-gray-200 group-hover:border-blue-400 transition-colors">
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
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                                <input type="text" name="title" value="{{ $banner->title ?? '' }}" 
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:border-blue-500 text-sm"
                                       placeholder="Title">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Subtitle</label>
                                <textarea name="subtitle" rows="2" 
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:border-blue-500 text-sm"
                                       placeholder="Short description...">{{ $banner->subtitle ?? '' }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Target Link</label>
                                <input type="text" name="link" value="{{ $banner->link ?? '#' }}" 
                                       class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:border-blue-500 text-sm"
                                       placeholder="#">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all flex items-center justify-center">
                            <i class="ri-save-line mr-2"></i> Update {{ $label }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- ADD/EDIT MODAL FOR MAIN BANNER -->
<div id="bannerModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeModal()"></div>
        
        <div class="bg-white rounded-2xl shadow-2xl relative w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Add New Slide</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            
            <form id="bannerForm" action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="position" value="main">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Banner Image <span class="text-red-500">*</span></label>
                        <input type="file" name="banner_image" id="modal_banner_image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[10px] text-gray-400 mt-1">Recommended size: 1200x420px</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" id="modal_title" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 text-sm" placeholder="Banner Title">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Subtitle</label>
                        <textarea name="subtitle" id="modal_subtitle" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 text-sm" placeholder="Short description..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Badge Text</label>
                            <input type="text" name="badge_text" id="modal_badge_text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 text-sm" placeholder="NEW">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Button Text</label>
                            <input type="text" name="button_text" id="modal_button_text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 text-sm" placeholder="Shop Now">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Target Link</label>
                            <input type="text" name="link" id="modal_link" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 text-sm" placeholder="#">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Order</label>
                            <input type="number" name="order" id="modal_order" value="0" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 focus:border-blue-500 text-sm">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-md transition-all">Save Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(type = 'add') {
        document.getElementById('bannerModal').classList.remove('hidden');
        if (type === 'add') {
            document.getElementById('modalTitle').innerText = 'Add New Slide';
            document.getElementById('bannerForm').action = "{{ route('banners.store') }}";
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('bannerForm').reset();
            document.getElementById('modal_banner_image').required = true;
        }
    }

    function closeModal() {
        document.getElementById('bannerModal').classList.add('hidden');
    }

    function editBanner(banner) {
        openModal('edit');
        document.getElementById('modalTitle').innerText = 'Edit Slide';
        document.getElementById('bannerForm').action = "/backend/banners/" + banner.id;
        document.getElementById('formMethod').value = 'POST'; // We use POST for update because of multipart/form-data with hidden _method
        
        // Add hidden method spoofing
        let methodField = document.getElementById('formMethodSpoof');
        if(!methodField) {
            methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.id = 'formMethodSpoof';
            methodField.value = 'POST'; // We handle the actual update logic in controller, Laravel might need PUT but we use POST for images often
            document.getElementById('bannerForm').appendChild(methodField);
        }
        
        document.getElementById('modal_title').value = banner.title || '';
        document.getElementById('modal_subtitle').value = banner.subtitle || '';
        document.getElementById('modal_badge_text').value = banner.badge_text || '';
        document.getElementById('modal_button_text').value = banner.button_text || '';
        document.getElementById('modal_link').value = banner.link || '#';
        document.getElementById('modal_order').value = banner.order || 0;
        document.getElementById('modal_banner_image').required = false;
    }
</script>

</div>

<!-- Ensure AlpineJS is loaded -->
<script src="//unpkg.com/alpinejs" defer></script>
<!-- Ensure Remix Icons are loaded -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
@endsection
