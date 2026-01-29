@extends('layouts.admin')

@section('header', 'System Configuration')

@section('content')
    <div class="h-full" x-data="{ activeTab: 'general' }">

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="flex bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden min-h-[750px]">
            @csrf

            <!-- Vertical Left Sidebar Tabs -->
            <div class="w-72 bg-slate-50 border-r border-slate-100 flex-shrink-0">
                <div class="p-6 border-b border-slate-100 bg-white">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Configuration</h3>
                    <p class="text-[0.65rem] text-slate-500 font-bold uppercase mt-1">Manage system settings</p>
                </div>
                
                <nav class="p-4 space-y-1">
                    <button type="button" @click="activeTab = 'general'" 
                            :class="activeTab === 'general' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100 hvr-icon-forward'" 
                            class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                        <i class="ri-palette-line mr-3 text-lg"></i>
                        <span>General & Branding</span>
                    </button>

                    <button type="button" @click="activeTab = 'store'" 
                            :class="activeTab === 'store' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                            class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                        <i class="ri-store-2-line mr-3 text-lg"></i>
                        <span>Store & Receipts</span>
                    </button>

                    <button type="button" @click="activeTab = 'seo'" 
                            :class="activeTab === 'seo' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                            class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                        <i class="ri-search-eye-line mr-3 text-lg"></i>
                        <span>SEO & Meta</span>
                    </button>

                    <button type="button" @click="activeTab = 'contact'" 
                            :class="activeTab === 'contact' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                            class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                        <i class="ri-customer-service-2-line mr-3 text-lg"></i>
                        <span>Contact Details</span>
                    </button>

                    <button type="button" @click="activeTab = 'social'" 
                            :class="activeTab === 'social' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                            class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                        <i class="ri-share-forward-line mr-3 text-lg"></i>
                        <span>Social Media</span>
                    </button>

                    <div class="pt-4 mt-4 border-t border-slate-200">
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest px-4 mb-2">Advanced</p>
                        
                        <button type="button" @click="activeTab = 'modes'" 
                                :class="activeTab === 'modes' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                                class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                            <i class="ri-toggle-line mr-3 text-lg"></i>
                            <span>System Modes</span>
                        </button>

                        <button type="button" @click="activeTab = 'analytics'" 
                                :class="activeTab === 'analytics' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                                class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                            <i class="ri-google-line mr-3 text-lg"></i>
                            <span>Analytics</span>
                        </button>

                        <button type="button" @click="activeTab = 'smtp'" 
                                :class="activeTab === 'smtp' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                                class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                            <i class="ri-mail-send-line mr-3 text-lg"></i>
                            <span>SMTP Engine</span>
                        </button>

                        <button type="button" @click="activeTab = 'tools'" 
                                :class="activeTab === 'tools' ? 'bg-[#2dae9a] text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-100'" 
                                class="w-full text-left px-4 py-3 rounded-xl font-bold text-sm transition-all flex items-center group">
                            <i class="ri-tools-line mr-3 text-lg"></i>
                            <span>Maintenance</span>
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Content Area (Right) -->
            <div class="flex-1 overflow-y-auto bg-white p-8 md:p-12">
                
                <!-- Inside Scrollable Area - Content Wrapper for readability -->
                <div class="max-w-4xl">
                    
                    @if(session('success'))
                        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3">
                            <i class="ri-checkbox-circle-fill text-xl"></i>
                            <span class="font-bold text-sm">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center gap-3">
                            <i class="ri-error-warning-fill text-xl"></i>
                            <div class="font-bold text-sm">
                                <ul> @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                            </div>
                        </div>
                    @endif

                    <!-- All Tab Sections here -->
                    <div x-show="activeTab === 'general'" class="space-y-8 animate-in fade-in duration-300">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">General Branding</h3>
                            <p class="text-slate-500 font-medium">Configure basic identity for your public storefront.</p>
                        </div>
                        <div class="h-px bg-slate-100 w-full mb-8"></div>
                        
                        <!-- Rest of General Tab Content (Will be copied back in simplified replace) -->

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
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Footer Description</label>
                        <textarea name="footer_description" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition" placeholder="Short description about the company displayed in the footer...">{{ settings('footer_description') }}</textarea>
                    </div>
                </div>

                <!-- Styled File Uploads -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-100">

                    <!-- Site Logo Upload -->
                    <div x-data="{ fileName: '' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Website Logo (Header - White)</label>
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

                    <!-- Scrolled Logo Upload -->
                    <div x-data="{ fileName: '' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Scrolled Header Logo (Header - Navy)</label>
                        <div class="flex items-start space-x-4">
                            @if(settings('site_logo_scrolled'))
                                <div class="shrink-0 p-2 bg-gray-900 border border-gray-700 rounded-lg">
                                    <img src="{{ settings('site_logo_scrolled') }}" class="h-16 w-auto object-contain">
                                    <p class="text-xs text-center text-gray-400 mt-1">Current</p>
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
                                    <input type="file" name="site_logo_scrolled" class="hidden" @change="fileName = $event.target.files[0].name">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Logo Upload -->
                    <div x-data="{ fileName: '' }">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Footer Logo</label>
                        <div class="flex items-start space-x-4">
                            @if(settings('site_logo_footer'))
                                <div class="shrink-0 p-2 bg-gray-900 border border-gray-700 rounded-lg">
                                    <img src="{{ settings('site_logo_footer') }}" class="h-16 w-auto object-contain">
                                    <p class="text-xs text-center text-gray-400 mt-1">Current</p>
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
                                    <input type="file" name="site_logo_footer" class="hidden" @change="fileName = $event.target.files[0].name">
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

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Invoice Footer Notes</label>
                        <textarea name="invoice_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition" placeholder="Terms and conditions for invoices...">{{ settings('invoice_notes') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Displayed at the bottom of printed invoices.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Quotation Footer Notes</label>
                        <textarea name="quotation_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition" placeholder="Terms and conditions for quotations...">{{ settings('quotation_notes') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Displayed at the bottom of printed quotations.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Purchase Order Policy</label>
                        <textarea name="purchase_policy" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition" placeholder="Terms and delivery instructions for purchase orders...">{{ settings('purchase_policy') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Displayed on Purchase Order PDF, Print, and Details views.</p>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">WhatsApp Number</label>
                        <input type="text" name="contact_whatsapp" value="{{ settings('contact_whatsapp') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition" placeholder="+971501234567">
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

                    <div class="md:col-span-2 border-t border-gray-100 pt-6 mt-2">
                         <h4 class="text-md font-bold text-gray-800 mb-3">Business Hours</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div class="flex gap-2">
                                 <input type="text" name="hours_label_1" value="{{ settings('hours_label_1', 'Monday - Saturday') }}" class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-sm" placeholder="Label 1">
                                 <input type="text" name="hours_time_1" value="{{ settings('hours_time_1', '9:00 AM - 9:00 PM') }}" class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Time 1">
                             </div>
                             <div class="flex gap-2">
                                 <input type="text" name="hours_label_2" value="{{ settings('hours_label_2', 'Sunday') }}" class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-sm" placeholder="Label 2">
                                 <input type="text" name="hours_time_2" value="{{ settings('hours_time_2', '11:00 AM - 7:00 PM') }}" class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Time 2">
                             </div>
                         </div>
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

            <!-- 6. SYSTEM MODES TAB -->
            <div x-show="activeTab === 'modes'" class="space-y-8" style="display: none;">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Maintenance & Coming Soon</h3>
                    <p class="text-sm text-gray-500">Control public access to your store.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Maintenance Mode -->
                    <div class="p-6 rounded-2xl border-2 {{ settings('maintenance_mode') ? 'border-red-100 bg-red-50/30' : 'border-gray-50 bg-gray-50/30' }} transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600">
                                    <i class="fas fa-tools text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest whitespace-nowrap">Maintenance Mode</h4>
                                    <p class="text-[0.7rem] text-gray-500 font-bold uppercase tracking-tight">Restrict site access</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="maintenance_mode" value="0">
                                <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer" {{ settings('maintenance_mode') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Custom Maintenance Message</label>
                        <textarea name="maintenance_message" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-red-500 focus:ring-0 shadow-sm transition" placeholder="We are currently performing scheduled maintenance...">{{ settings('maintenance_message') }}</textarea>
                    </div>

                    <!-- Coming Soon Mode -->
                    <div class="p-6 rounded-2xl border-2 {{ settings('coming_soon_mode') ? 'border-blue-100 bg-blue-50/30' : 'border-gray-50 bg-gray-50/30' }} transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="fas fa-clock text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest whitespace-nowrap">Coming Soon</h4>
                                    <p class="text-[0.7rem] text-gray-500 font-bold uppercase tracking-tight">Pre-launch display</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="coming_soon_mode" value="0">
                                <input type="checkbox" name="coming_soon_mode" value="1" class="sr-only peer" {{ settings('coming_soon_mode') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Custom Coming Soon Message</label>
                        <textarea name="coming_soon_message" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-blue-500 focus:ring-0 shadow-sm transition" placeholder="Something exciting is coming your way! Stay tuned.">{{ settings('coming_soon_message') }}</textarea>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 shrink-0 flex items-center justify-center text-amber-600">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-1">Administrator Access</h5>
                            <p class="text-xs text-amber-800 leading-relaxed font-medium">Administrators and staff with <span class="font-bold underline">manage settings</span> permissions will always be able to bypass these modes and view the site normally to perform updates and checks.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. MAINTENANCE TOOLS TAB -->
            <div x-show="activeTab === 'tools'" class="space-y-8" style="display: none;">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Hosting Maintenance Tools</h3>
                    <p class="text-sm text-gray-500">Utilities for managing your application on shared hosting environments.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Clear Cache Tool -->
                    <div class="p-6 rounded-2xl border-2 border-orange-50 bg-orange-50/20">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600 shrink-0">
                                <i class="fas fa-broom text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-1">Clear Application Cache</h4>
                                <p class="text-xs text-gray-500 font-medium leading-relaxed">Clears all compiled views, application cache, route cache, and configuration cache. Use this if you've made changes that aren't appearing.</p>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="if(confirm('This will clear all application caches. Proceed?')) document.getElementById('clear-cache-form').submit();"
                                class="w-full py-3 px-6 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-900/10 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-bolt"></i> Run Cache Clear
                        </button>
                    </div>

                    <!-- Storage Link Tool -->
                    <div class="p-6 rounded-2xl border-2 border-indigo-50 bg-indigo-50/20">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                                <i class="fas fa-link text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-1">Fix Storage Link</h4>
                                <p class="text-xs text-gray-500 font-medium leading-relaxed">Recreates the symbolic link from <span class="font-mono bg-gray-100 px-1">public/storage</span> to <span class="font-mono bg-gray-100 px-1">storage/app/public</span>. Essential after migration or on cPanel hosting.</p>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="if(confirm('This will recreate the storage symbolic link. Proceed?')) document.getElementById('storage-link-form').submit();"
                                class="w-full py-3 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-900/10 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-sync"></i> Recreate Storage Link
                        </button>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center gap-3">
                    <i class="fas fa-info-circle text-gray-400"></i>
                    <p class="text-[0.7rem] text-gray-500 font-bold uppercase tracking-tight">These tools execute Artisan commands directly on the server.</p>
                </div>
            </div>

                </div>

                <!-- 8. ANALYTICS TAB -->
                <div x-show="activeTab === 'analytics'" class="space-y-12 animate-in fade-in duration-300" style="display: none;">
                    <!-- Google Analytics -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Google Analytics</h3>
                            <p class="text-slate-500 font-medium">Track website traffic and user behavior.</p>
                        </div>
                        <div class="h-px bg-slate-100 w-full"></div>

                        <div class="max-w-2xl">
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Measurement ID</label>
                            <input type="text" name="google_analytics_id" value="{{ settings('google_analytics_id') }}" placeholder="G-XXXXXXXXXX" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                            <p class="text-xs text-slate-400 mt-2 font-medium">Found in Google Analytics > Admin > Data Streams.</p>
                        </div>
                    </div>

                    <!-- Facebook Pixel -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Facebook Pixel</h3>
                            <p class="text-slate-500 font-medium">Track conversions and optimize your ad campaigns.</p>
                        </div>
                        <div class="h-px bg-slate-100 w-full"></div>

                        <div class="max-w-2xl">
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Pixel ID</label>
                            <input type="text" name="facebook_pixel_id" value="{{ settings('facebook_pixel_id') }}" placeholder="Enter your Pixel ID" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                            <p class="text-xs text-slate-400 mt-2 font-medium">Found in Facebook Events Manager under Settings.</p>
                        </div>
                    </div>
                </div>

                <!-- 9. SMTP TAB -->
                <div x-show="activeTab === 'smtp'" x-data="{ mailDriver: '{{ settings('mail_mailer', 'smtp') }}' }" class="space-y-8 animate-in fade-in duration-300" style="display: none;">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">SMTP Engine</h3>
                            <p class="text-slate-500 font-medium">Configure how the system processes and sends emails.</p>
                        </div>
                        <button type="button" id="test-smtp-btn" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-6 rounded-xl text-xs flex items-center gap-2 transition-all shadow-lg shadow-slate-200">
                            <i class="ri-mail-check-line text-lg"></i>
                            <span>Test Connection</span>
                        </button>
                    </div>
                    <div class="h-px bg-slate-100 w-full mb-8"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Mail Driver</label>
                            <select name="mail_mailer" x-model="mailDriver" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                <option value="smtp" {{ settings('mail_mailer') == 'smtp' ? 'selected' : '' }}>SMTP (Outlook/Gmail/etc)</option>
                                <option value="microsoft_graph" {{ settings('mail_mailer') == 'microsoft_graph' ? 'selected' : '' }}>Microsoft Graph API (Recommended for GoDaddy)</option>
                                <option value="log" {{ settings('mail_mailer') == 'log' ? 'selected' : '' }}>Log (Testing Only)</option>
                            </select>
                        </div>

                        <template x-if="mailDriver === 'smtp'">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 contents">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Mail Host</label>
                                    <input type="text" name="mail_host" value="{{ settings('mail_host') }}" placeholder="smtp.gmail.com" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Mail Port</label>
                                    <input type="text" name="mail_port" value="{{ settings('mail_port', '587') }}" placeholder="587" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Encryption</label>
                                    <select name="mail_encryption" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                        <option value="tls" {{ settings('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ settings('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ settings('mail_encryption') == '' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Username</label>
                                    <input type="text" name="mail_username" value="{{ settings('mail_username') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Password</label>
                                    <input type="password" name="mail_password" value="{{ settings('mail_password') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                </div>
                            </div>
                        </template>

                        <template x-if="mailDriver === 'microsoft_graph'">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 contents">
                                <div class="md:col-span-2">
                                    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-800 font-medium">
                                        <i class="ri-information-line mr-1"></i> Bypasses SMTP blocks. Requires an App Registration in Azure Portal with <strong>Mail.Send</strong> application permissions.
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Tenant ID</label>
                                    <input type="text" name="microsoft_tenant_id" value="{{ settings('microsoft_tenant_id') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Client ID</label>
                                    <input type="text" name="microsoft_client_id" value="{{ settings('microsoft_client_id') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Client Secret</label>
                                    <input type="password" name="microsoft_client_secret" value="{{ settings('microsoft_client_secret') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                                </div>

                                <!-- Integrated Setup Guide -->
                                <div class="md:col-span-2 mt-6">
                                    <div class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden">
                                        <div class="p-5 bg-slate-100 border-b border-slate-200 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white shadow-sm">
                                                    <i class="ri-book-read-line"></i>
                                                </div>
                                                <h4 class="font-black text-slate-800 uppercase tracking-widest text-sm">Microsoft Graph API Setup Guide</h4>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-400 bg-white px-2 py-1 rounded-md border border-slate-200">STEP-BY-STEP</span>
                                        </div>
                                        <div class="p-6 space-y-6">
                                            <div class="flex gap-4">
                                                <div class="w-6 h-6 rounded-full bg-[#2dae9a] text-white flex-shrink-0 flex items-center justify-center text-xs font-bold mt-0.5">1</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800 mb-1">App Registration</p>
                                                    <p class="text-xs text-slate-500 leading-relaxed">
                                                        Go to <a href="https://entra.microsoft.com/" target="_blank" class="text-[#2dae9a] hover:underline font-bold text-[11px] uppercase tracking-tighter">Microsoft Entra ID</a> &rarr; <strong>App registrations</strong> &rarr; <strong>New registration</strong>. 
                                                        Name it "Electromart Mailer" and select "Single Tenant".
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-4">
                                                <div class="w-6 h-6 rounded-full bg-[#2dae9a] text-white flex-shrink-0 flex items-center justify-center text-xs font-bold mt-0.5">2</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800 mb-1">Get Credentials</p>
                                                    <p class="text-xs text-slate-500 leading-relaxed">
                                                        Copy the <strong>Application (client) ID</strong> and <strong>Directory (tenant) ID</strong> from the Overview page and paste them above.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-4">
                                                <div class="w-6 h-6 rounded-full bg-[#2dae9a] text-white flex-shrink-0 flex items-center justify-center text-xs font-bold mt-0.5">3</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800 mb-1">Generate Secret</p>
                                                    <p class="text-xs text-slate-500 leading-relaxed">
                                                        Go to <strong>Certificates & secrets</strong> &rarr; <strong>New client secret</strong>. 
                                                        Copy the <span class="text-red-500 font-bold underline">Value</span> immediately and paste it above.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-4">
                                                <div class="w-6 h-6 rounded-full bg-[#2dae9a] text-white flex-shrink-0 flex items-center justify-center text-xs font-bold mt-0.5">4</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800 mb-1">Configure Permissions</p>
                                                    <p class="text-xs text-slate-500 leading-relaxed">
                                                        Go to <strong>API permissions</strong> &rarr; <strong>Add a permission</strong> &rarr; <strong>Microsoft Graph</strong> &rarr; <strong>Application permissions</strong>. 
                                                        Search and select <span class="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded font-black border border-emerald-100">Mail.Send</span>.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-4">
                                                <div class="w-6 h-6 rounded-full bg-[#2dae9a] text-white flex-shrink-0 flex items-center justify-center text-xs font-bold mt-0.5">5</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800 mb-1">Grant Consent</p>
                                                    <p class="text-xs text-slate-500 leading-relaxed">
                                                        Finally, click <strong class="text-slate-900 border-b border-slate-900 pb-0.5">Grant admin consent</strong> on the permissions page to activate the service.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">From Email</label>
                            <input type="text" name="mail_from_address" value="{{ settings('mail_from_address') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">From Name</label>
                            <input type="text" name="mail_from_name" value="{{ settings('mail_from_name') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition">
                        </div>

                        <div class="md:col-span-2 pt-4 border-t border-slate-50 mt-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Test Recipient Email</label>
                            <div class="flex gap-4">
                                <input type="email" id="test_recipient" value="{{ settings('mail_from_address') }}" placeholder="Enter email to receive test message" class="flex-1 border border-slate-200 rounded-xl px-4 py-3 focus:border-[#2dae9a] focus:ring-0 shadow-sm transition bg-slate-50">
                                <p class="text-[10px] text-slate-400 max-w-[200px] leading-tight flex items-center">This email will only be used for the connection test and will not be saved.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Troubleshooting Info -->
                    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 mt-8">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 shrink-0 flex items-center justify-center text-blue-600">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-1">Troubleshooting "Connection Timed Out"</h5>
                                <p class="text-xs text-blue-800 leading-relaxed font-medium">
                                    If you see a <span class="font-bold">"Connection timed out"</span> error on your live site, your hosting provider is likely blocking outgoing ports <span class="font-bold">587</span> and <span class="font-bold">465</span>. 
                                    <br><br>
                                    <span class="underline">Recommended Actions:</span>
                                    <ul class="list-disc ml-5 mt-2 space-y-1">
                                        <li>Contact your hosting support and ask them to <span class="font-bold italic">"Whitelist outgoing SMTP connections for smtp.office365.com"</span>.</li>
                                        <li>Check if your host provides a local SMTP (e.g., <span class="font-mono">localhost</span> on port <span class="font-mono">25</span>).</li>
                                        <li>Ensure the <span class="font-bold">From Email</span> matches the authenticated <span class="font-bold">Username</span> (required by Outlook).</li>
                                    </ul>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="bg-[#2dae9a] hover:bg-[#248e7e] text-white font-black py-4 px-10 rounded-2xl shadow-xl shadow-emerald-200/50 transform transition hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-emerald-100 flex items-center gap-3">
                        <i class="ri-save-fill text-xl"></i>
                        <span>Save All Changes</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Ensure AlpineJS is loaded -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#test-smtp-btn').on('click', function() {
                const btn = $(this);
                const originalContent = btn.html();
                
                // Gather values from current form state
                const data = {
                    _token: '{{ csrf_token() }}',
                    mail_host: $('input[name="mail_host"]').val(),
                    mail_port: $('input[name="mail_port"]').val(),
                    mail_encryption: $('select[name="mail_encryption"]').val(),
                    mail_username: $('input[name="mail_username"]').val(),
                    mail_password: $('input[name="mail_password"]').val(),
                    mail_from_address: $('input[name="mail_from_address"]').val(),
                    mail_from_name: $('input[name="mail_from_name"]').val(),
                    test_recipient: $('#test_recipient').val(),
                    // Graph API fields
                    mail_mailer: $('select[name="mail_mailer"]').val(),
                    microsoft_tenant_id: $('input[name="microsoft_tenant_id"]').val(),
                    microsoft_client_id: $('input[name="microsoft_client_id"]').val(),
                    microsoft_client_secret: $('input[name="microsoft_client_secret"]').val(),
                };

                // Basic validation
                let isValid = true;
                if (data.mail_mailer === 'microsoft_graph') {
                    if (!data.microsoft_tenant_id || !data.microsoft_client_id || !data.microsoft_client_secret || !data.mail_from_address || !data.test_recipient) {
                        isValid = false;
                    }
                } else if (data.mail_mailer === 'smtp') {
                    if (!data.mail_host || !data.mail_username || !data.mail_password || !data.mail_from_address || !data.test_recipient) {
                        isValid = false;
                    }
                }

                if(!isValid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please fill in all required fields for the selected driver before testing.',
                        confirmButtonColor: '#0f172a',
                    });
                    return;
                }

                btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin text-lg"></i> <span>Testing...</span>');

                $.ajax({
                    url: '{{ route("settings.test_smtp") }}',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#2dae9a',
                        });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error occurred. Please check your credentials and firewall.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Failed',
                            text: msg,
                            confirmButtonColor: '#0f172a',
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalContent);
                    }
                });
            });
        });
    </script>
    @endpush
@endsection

<!-- Hidden Forms for Maintenance Tools -->
<form id="clear-cache-form" action="{{ route('settings.clear-cache') }}" method="POST" style="display: none;">
    @csrf
</form>
<form id="storage-link-form" action="{{ route('settings.storage-link') }}" method="POST" style="display: none;">
    @csrf
</form>
