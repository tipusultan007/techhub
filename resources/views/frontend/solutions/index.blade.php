@extends('layouts.frontend')

@section('title', 'Expert IT Solutions | Tech Hub')
@section('meta_description', 'Professional IT services in UAE: Network infrastructure, Security, Communication, and AMC.')

@section('content')
<div class="container py-10">
    <div class="section-center-header mb-12">
        <span class="font-bold uppercase tracking-widest text-sm" style="color: var(--brand-emerald);">Our Services</span>
        <h1 class="text-4xl font-extrabold mt-2" style="color: var(--text-main);">Expert IT Solutions</h1>
        <p class="mt-4 max-w-2xl mx-auto" style="color: var(--text-muted);">We deliver professional, modern, and enterprise-ready IT solutions to streamline your business operations and ensure maximum security.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($solutions as $solution)
            <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="w-16 h-16 rounded-xl flex items-center justify-center text-3xl mb-6 transition-colors duration-300" style="background: rgba(45, 174, 154, 0.1); color: var(--brand-emerald);">
                    <i class="{{ $solution->icon_class }}"></i>
                </div>
                <h3 class="text-xl font-bold mb-4" style="color: var(--text-main);">{{ $solution->title }}</h3>
                <p class="text-sm leading-relaxed mb-6" style="color: var(--text-muted);">{{ $solution->summary }}</p>
                <a href="{{ route('solutions.show', $solution->slug) }}" class="inline-flex items-center font-bold transition-colors" style="color: var(--brand-navy);">
                    Learn More <i class="ri-arrow-right-line ml-2"></i>
                </a>
            </div>
        @endforeach
    </div>
    
    <!-- AMC CTA -->
    <div class="mt-20 mb-20 rounded-3xl p-10 md:p-16 relative overflow-hidden text-white flex flex-col md:flex-row items-center justify-between" style="background: var(--brand-navy);">
        <div class="relative z-10 max-w-xl text-center md:text-left">
            <h2 class="text-3xl font-extrabold mb-4">Dedicated Support for Your Business</h2>
            <p style="color: rgba(255,255,255,0.7);">Looking for a reliable IT partner? Our Annual Maintenance Contracts (AMC) ensure your business stays online 24/7 with expert support.</p>
        </div>
        <div class="relative z-10 mt-8 md:mt-0">
            <a href="tel:+{{ $settings['contact_phone'] }}" class="btn btn-brand !px-10 !py-4 text-lg">Contact Us Now</a>
        </div>
        <!-- Abstract BG Elements -->
        <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full blur-3xl" style="background: rgba(45, 174, 154, 0.2);"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 rounded-full blur-3xl" style="background: rgba(45, 174, 154, 0.1);"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-brand { color: var(--brand-emerald); }
</style>
@endpush
