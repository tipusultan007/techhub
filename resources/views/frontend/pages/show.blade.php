@extends('layouts.frontend')

@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description)

@section('content')
<!-- Page Breadcrumb -->
<div class="bg-slate-50 border-b border-slate-100 py-8">
    <div class="container mx-auto px-4 lg:px-20">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">{{ $page->title }}</h1>
            <nav class="flex items-center text-sm font-medium text-slate-500 whitespace-nowrap overflow-x-auto">
                <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Home</a>
                <span class="mx-3 text-slate-300">/</span>
                <span class="text-slate-900 font-semibold">{{ $page->title }}</span>
            </nav>
        </div>
    </div>
</div>

<!-- Page Content -->
<article class="py-16 bg-white min-h-[60vh]">
    <div class="container mx-auto px-4 lg:px-20">
        <div class="max-w-4xl mx-auto prose prose-slate prose-lg lg:prose-xl prose-headings:font-extrabold prose-headings:text-slate-900 prose-a:text-blue-600 hover:prose-a:text-blue-700 prose-img:rounded-3xl prose-img:shadow-2xl">
            {!! $page->content !!}
        </div>
    </div>
</article>

<!-- Call to Action (Optional, matches solutions style) -->
<section class="py-20 bg-brand-navy overflow-hidden relative">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-teal rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-brand-teal rounded-full blur-[120px]"></div>
    </div>
    
    <div class="container mx-auto px-4 lg:px-20 relative z-10 text-center">
        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6">Need expert assistance?</h2>
        <p class="text-slate-400 text-lg md:text-xl max-w-2xl mx-auto mb-10">Our team of specialists is ready to help you navigate your IT challenges and deliver scalable solutions.</p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="tel:{{ settings('contact_phone') }}" class="px-8 py-4 bg-brand-emerald hover:bg-brand-teal text-white font-bold rounded-2xl transition-all hover:scale-105 shadow-xl shadow-brand-emerald/20">Contact Our Team</a>
            <a href="{{ route('solutions.index') }}" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl transition-all backdrop-blur-sm">Browse Solutions</a>
        </div>
    </div>
</section>
@endsection
