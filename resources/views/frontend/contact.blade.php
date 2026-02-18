@extends('layouts.frontend')

@section('title', 'Contact Us | Tech Hub Computer Trading')
@section('meta_description', 'Get in touch with Tech Hub for Enterprise-ready IT services and the best deals on computer components in UAE.')

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@section('content')
<div class="container py-12">
    <div class="max-w-6xl mx-auto">
        
        <div class="text-center mb-12 mt-12">
            <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Get in Touch</h1>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Have a question about our services or need a custom quote? Our experts are here to help you upgrade your technology.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Contact Info Cards -->
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-brand-emerald/10 text-brand-emerald rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-map-pin-2-fill text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Our Office</h3>
                    <p class="text-slate-600">{{ settings('contact_address', 'Tech Hub Computer Trading, Dubai, UAE') }}</p>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-brand-navy/10 text-brand-navy rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-phone-fill text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Phone Number</h3>
                    <p class="text-slate-600 mb-1">Sales: {{ settings('contact_phone') }}</p>
                    <p class="text-slate-600">Support: {{ settings('support_phone') }}</p>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-brand-emerald/10 text-brand-emerald rounded-xl flex items-center justify-center mb-6">
                        <i class="ri-mail-fill text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Email Address</h3>
                    <p class="text-slate-600 mb-1">{{ settings('contact_email') }}</p>
                    <p class="text-slate-600">{{ settings('support_email') }}</p>
                </div>

            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white p-10 rounded-3xl border border-slate-100 shadow-xl">
                    
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 p-6 rounded-2xl mb-8 flex items-start">
                            <i class="ri-checkbox-circle-fill text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-lg">Thank You!</h4>
                                <p>{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                    class="w-full px-5 py-4 rounded-xl border border-slate-200 focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/10 outline-none transition-all" 
                                    placeholder="John Doe" required>
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                    class="w-full px-5 py-4 rounded-xl border border-slate-200 focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/10 outline-none transition-all" 
                                    placeholder="john@example.com" required>
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Phone Number (UAE)</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                                    class="w-full px-5 py-4 rounded-xl border border-slate-200 focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/10 outline-none transition-all" 
                                    placeholder="+971 50 XXXXXXX" required>
                                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-bold text-slate-700 mb-2">Subject</label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" 
                                    class="w-full px-5 py-4 rounded-xl border border-slate-200 focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/10 outline-none transition-all" 
                                    placeholder="How can we help?" required>
                                @error('subject') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-bold text-slate-700 mb-2">Your Message</label>
                            <textarea name="message" id="message" rows="6" 
                                class="w-full px-5 py-4 rounded-xl border border-slate-200 focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/10 outline-none transition-all resize-none" 
                                placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-center md:justify-start">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site') ?: settings('recaptcha_site_key') }}"></div>
                        </div>
                        @error('g-recaptcha-response') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-brand-emerald hover:bg-brand-emerald/90 text-white font-bold py-5 px-8 rounded-2xl shadow-lg shadow-brand-emerald/20 transform hover:-translate-y-1 transition-all flex items-center justify-center">
                                <i class="ri-send-plane-fill mr-3 text-xl"></i>
                                Send Message
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>

        <!-- Map Section -->
        <div class="mt-20 rounded-3xl overflow-hidden border border-slate-100 shadow-2xl h-96 relative">
             <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d115456.40237731996!2d55.2707!3d25.2048!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43496ad9c645%3A0xbde66e5084295162!2sDubai!5e0!3m2!1sen!2sae!4v1700000000000!5m2!1sen!2sae" 
                class="absolute inset-0 w-full h-full grayscale hover:grayscale-0 transition-all duration-700 border-0" 
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

    </div>
</div>
@endsection
