@extends('layouts.frontend')

@section('title', $solution->title . ' | Tech Hub Solutions')
@section('meta_description', $solution->summary)

@section('content')
<div class="container py-16">
    <div class="flex flex-col lg:flex-row gap-12 my-20">
        <!-- Sidebar Navigation -->
        <aside class="w-full lg:w-1/4">
            <div class="bg-slate-50 rounded-2xl p-6 sticky top-24">
                <h4 class="text-lg font-bold text-slate-900 mb-6 px-2">Our Solutions</h4>
                <nav class="space-y-1">
                    @php 
                        $allSolutions = \App\Models\Solution::where('is_active', '=', 1)->orderBy('order', 'asc')->get();
                    @endphp
                    @foreach($allSolutions as $item)
                        <a href="{{ route('solutions.show', $item->slug) }}" 
                           class="flex items-center px-4 py-3 rounded-xl transition-all {{ $item->id == $solution->id ? 'text-white shadow-lg' : 'text-slate-600 hover:bg-white shadow-sm border border-transparent hover:border-slate-100' }}"
                           style="{{ $item->id == $solution->id ? 'background: var(--brand-emerald);' : '' }}">
                            <i class="{{ $item->icon_class }} mr-3 text-lg {{ $item->id == $solution->id ? '' : 'text-slate-400' }}" style="{{ $item->id != $solution->id ? 'color: var(--brand-emerald);' : '' }}"></i>
                            <span class="font-medium text-sm">{{ $item->title }}</span>
                        </a>
                    @endforeach
                </nav>
                
                <div class="mt-10 rounded-2xl p-6 text-white text-center" style="background: var(--brand-navy);">
                    <h5 class="font-bold mb-2">Need a Quote?</h5>
                    <p class="text-xs mb-6" style="color: rgba(255,255,255,0.7);">Get a professional consultation for your business IT needs.</p>
                    <a href="tel:{{ $settings['contact_phone']??'' }}" class="inline-block bg-white font-bold px-6 py-2.5 rounded-lg text-sm hover:bg-slate-50 transition-colors" style="color: var(--brand-navy);">Call Experts</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="w-full lg:w-3/4">
            <div class="mb-10">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl mb-8" style="background: rgba(45, 174, 154, 0.1); color: var(--brand-emerald);">
                    <i class="{{ $solution->icon_class }}"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-slate-900 leading-tight">{{ $solution->title }}</h1>
                <div class="flex items-center mt-6 text-slate-500 text-sm">
                    <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Home</a>
                    <i class="ri-arrow-right-s-line mx-2"></i>
                    <a href="{{ route('solutions.index') }}" class="hover:text-emerald-600 transition-colors">Solutions</a>
                    <i class="ri-arrow-right-s-line mx-2"></i>
                    <span class="text-slate-900 font-medium">{{ $solution->title }}</span>
                </div>
            </div>

            <div class="prose prose-slate max-w-none">
                <div class="bg-white border border-slate-100 rounded-3xl p-8 md:p-12 shadow-sm">
                    {!! $solution->description !!}
                    
                    <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6 p-8 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <h4 class="text-slate-900 font-bold mb-2 flex items-center">
                                <i class="ri-checkbox-circle-fill text-green-500 mr-2 text-xl"></i> Professional Team
                            </h4>
                            <p class="text-slate-600 text-sm">Highly skilled engineers with extensive UAE experience.</p>
                        </div>
                        <div>
                            <h4 class="text-slate-900 font-bold mb-2 flex items-center">
                                <i class="ri-checkbox-circle-fill text-green-500 mr-2 text-xl"></i> 24/7 Support
                            </h4>
                            <p class="text-slate-600 text-sm">Round-the-clock technical assistance for AMC clienst.</p>
                        </div>
                        <div>
                            <h4 class="text-slate-900 font-bold mb-2 flex items-center">
                                <i class="ri-checkbox-circle-fill text-green-500 mr-2 text-xl"></i> Genuine Hardware
                            </h4>
                            <p class="text-slate-600 text-sm">We only use certified and authentic IT components.</p>
                        </div>
                        <div>
                            <h4 class="text-slate-900 font-bold mb-2 flex items-center">
                                <i class="ri-checkbox-circle-fill text-green-500 mr-2 text-xl"></i> Cost Effective
                            </h4>
                            <p class="text-slate-600 text-sm">Competitive pricing with premium service standards.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .prose h3 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 2rem; margin-bottom: 1rem; }
    .prose p { color: #475569; font-size: 1rem; line-height: 1.8; margin-bottom: 1.5rem; }
</style>
@endpush
