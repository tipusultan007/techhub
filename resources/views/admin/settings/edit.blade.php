@extends('layouts.admin')

@section('header', 'System Configuration')

@section('content')
    <div class="max-w-6xl mx-auto" x-data="{ activeTab: 'general' }">

        <!-- Tab Navigation -->
        <nav class="flex space-x-2 mb-6 border-b border-gray-200 overflow-x-auto">
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition">
                General & Branding
            </button>
            <button @click="activeTab = 'store'" :class="activeTab === 'store' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition">
                Store & Receipts
            </button>
            <button @click="activeTab = 'seo'" :class="activeTab === 'seo' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition">
                SEO & Meta
            </button>
            <button @click="activeTab = 'contact'" :class="activeTab === 'contact' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition">
                Contact Details
            </button>
            <button @click="activeTab = 'social'" :class="activeTab === 'social' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm transition">
                Social Media
            </button>
        </nav>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            @csrf

            <!-- 1. GENERAL TAB -->
            <div x-show="activeTab === 'general'" class="space-y-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">General Branding</h3>
                    <p class="text-sm text-gray-500">Settings for the public facing website.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Website Name</label>
                        <!-- Added 'border' class here -->
                        <input type="text" name="site_name" value="{{ settings('site_name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Currency Symbol</label>
                        <!-- Added 'border' class here -->
                        <input type="text" name="currency_symbol" value="{{ settings('currency_symbol', 'AED') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                </div>

                <!-- Styled File Uploads -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-100">

                    <!-- Site Logo Upload -->
                    <div x-data="{ fileName: '' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Website Logo</label>
                        <div class="flex items-start space-x-4">
                            @if(settings('site_logo'))
                                <div class="shrink-0 p-2 bg-gray-50 border border-gray-200 rounded-lg">
                                    <img src="{{ settings('site_logo') }}" class="h-16 w-auto object-contain">
                                    <p class="text-xs text-center text-gray-500 mt-1">Current</p>
                                </div>
                            @endif
                            <div class="flex-1">
                                <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-lg appearance-none cursor-pointer hover:border-blue-500 hover:bg-gray-50 focus:outline-none flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="font-medium text-gray-600 text-sm">
                                    <span x-text="fileName ? fileName : 'Drop files to Attach, or browse'"></span>
                                </span>
                                    <input type="file" name="site_logo" class="hidden" @change="fileName = $event.target.files[0].name">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Favicon Upload -->
                    <div x-data="{ fileName: '' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Favicon</label>
                        <div class="flex items-start space-x-4">
                            @if(settings('site_favicon'))
                                <div class="shrink-0 p-2 bg-gray-50 border border-gray-200 rounded-lg">
                                    <img src="{{ settings('site_favicon') }}" class="h-12 w-12 object-contain">
                                    <p class="text-xs text-center text-gray-500 mt-1">Current</p>
                                </div>
                            @endif
                            <div class="flex-1">
                                <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-lg appearance-none cursor-pointer hover:border-blue-500 hover:bg-gray-50 focus:outline-none flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span class="font-medium text-gray-600 text-sm">
                                    <span x-text="fileName ? fileName : 'Click to select favicon'"></span>
                                </span>
                                    <input type="file" name="site_favicon" class="hidden" @change="fileName = $event.target.files[0].name">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. STORE & RECEIPTS TAB -->
            <div x-show="activeTab === 'store'" class="space-y-6" style="display: none;">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Store & Receipt Details</h3>
                    <p class="text-sm text-gray-500">These details appear on printed receipts and invoices.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Official Shop Name</label>
                        <input type="text" name="shop_name" value="{{ settings('shop_name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">TRN Number (Tax ID)</label>
                        <input type="text" name="shop_trn" value="{{ settings('shop_trn') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Store Phone (Receipts)</label>
                        <input type="text" name="shop_phone" value="{{ settings('shop_phone') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Store Address (Receipts)</label>
                        <textarea name="shop_address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">{{ settings('shop_address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. SEO TAB -->
            <div x-show="activeTab === 'seo'" class="space-y-6" style="display: none;">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">SEO Configuration</h3>
                    <p class="text-sm text-gray-500">Default meta tags for search engine optimization.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Default Meta Title</label>
                    <input type="text" name="meta_title" value="{{ settings('meta_title') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">{{ settings('meta_description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ settings('meta_keywords') }}" placeholder="computer, laptop, gaming pc..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                </div>
            </div>

            <!-- 4. CONTACT TAB -->
            <div x-show="activeTab === 'contact'" class="space-y-6" style="display: none;">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Contact Page Details</h3>
                    <p class="text-sm text-gray-500">Displayed on the "Contact Us" page and Footer.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Support Phone</label>
                        <input type="text" name="contact_phone" value="{{ settings('contact_phone') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Support Email</label>
                        <input type="text" name="contact_email" value="{{ settings('contact_email') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Physical Address (Website)</label>
                        <textarea name="contact_address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">{{ settings('contact_address') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Google Maps Embed Link</label>
                        <input type="text" name="contact_map" value="{{ settings('contact_map') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition" placeholder="<iframe src='...'></iframe>">
                        <p class="text-xs text-gray-400 mt-1">Paste the full iframe code from Google Maps.</p>
                    </div>
                </div>
            </div>

            <!-- 5. SOCIAL TAB -->
            <div x-show="activeTab === 'social'" class="space-y-6" style="display: none;">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Social Media Links</h3>
                    <p class="text-sm text-gray-500">Links displayed in the site footer.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Facebook</label>
                        <div class="absolute inset-y-0 left-0 pt-6 pl-3 flex items-center pointer-events-none">
                            <i class="ri-facebook-fill text-gray-400"></i>
                        </div>
                        <input type="text" name="social_facebook" value="{{ settings('social_facebook') }}" class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Instagram</label>
                        <div class="absolute inset-y-0 left-0 pt-6 pl-3 flex items-center pointer-events-none">
                            <i class="ri-instagram-line text-gray-400"></i>
                        </div>
                        <input type="text" name="social_instagram" value="{{ settings('social_instagram') }}" class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Twitter (X)</label>
                        <div class="absolute inset-y-0 left-0 pt-6 pl-3 flex items-center pointer-events-none">
                            <i class="ri-twitter-x-line text-gray-400"></i>
                        </div>
                        <input type="text" name="social_twitter" value="{{ settings('social_twitter') }}" class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">LinkedIn</label>
                        <div class="absolute inset-y-0 left-0 pt-6 pl-3 flex items-center pointer-events-none">
                            <i class="ri-linkedin-fill text-gray-400"></i>
                        </div>
                        <input type="text" name="social_linkedin" value="{{ settings('social_linkedin') }}" class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition">
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Ensure AlpineJS is loaded -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <!-- Ensure Remix Icons are loaded for social icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
@endsection
