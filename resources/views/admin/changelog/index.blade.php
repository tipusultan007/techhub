@extends('layouts.admin')

@section('header', 'System Change Log')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Version History</h1>
            <p class="text-gray-500 font-medium mt-1">Tracking our journey to excellence, one update at a time.</p>
        </div>
        <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl border border-emerald-100 font-black text-sm uppercase tracking-widest flex items-center gap-2">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            Current: v{{ app_version() }}
        </div>
    </div>

    <div class="space-y-12 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
        
        @foreach($changelogs as $log)
        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
            <!-- Icon -->
            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-white group-[.is-active]:bg-emerald-500 text-slate-500 group-[.is-active]:text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                @if($log['type'] === 'Major Feature')
                    <i class="fas fa-rocket text-sm"></i>
                @elseif($log['type'] === 'Release')
                    <i class="fas fa-flag-checkered text-sm"></i>
                @else
                    <i class="fas fa-wrench text-sm"></i>
                @endif
            </div>
            
            <!-- Content Card -->
            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <time class="font-bold text-xs uppercase tracking-widest text-[#2dae9a]">{{ \Carbon\Carbon::parse($log['date'])->format('M d, Y') }}</time>
                    <span class="px-3 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-widest 
                        @if($log['type'] === 'Major Feature') bg-blue-50 text-blue-600 
                        @elseif($log['type'] === 'Release') bg-purple-50 text-purple-600 
                        @else bg-gray-50 text-gray-600 @endif">
                        {{ $log['type'] }}
                    </span>
                </div>
                <div class="text-lg font-black text-slate-800 mb-3">{{ $log['title'] }}</div>
                
                <ul class="space-y-2">
                    @foreach($log['changes'] as $change)
                    <li class="flex items-start gap-3 group/item">
                        <i class="fas fa-check-circle text-emerald-400 mt-1 text-xs group-hover/item:scale-110 transition-transform"></i>
                        <span class="text-sm text-slate-600 font-medium leading-relaxed">{{ $change }}</span>
                    </li>
                    @endforeach
                </ul>
                
                <div class="mt-4 pt-4 border-t border-slate-50 flex items-center gap-2">
                    <span class="text-[0.6rem] font-black uppercase tracking-widest text-slate-300">Build Tag:</span>
                    <span class="text-xs font-bold text-slate-500">v{{ $log['version'] }}</span>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    <!-- Final Footer -->
    <div class="mt-16 text-center">
        <div class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-100 text-slate-500 text-xs font-black uppercase tracking-widest shadow-sm border border-white">
            <i class="fas fa-history"></i> End of Recorded History
        </div>
    </div>
</div>
@endsection
