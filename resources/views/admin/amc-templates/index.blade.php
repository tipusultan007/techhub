@extends('layouts.admin')

@section('header', 'Agreement Templates')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">AMC Agreement Templates</h2>
            <p class="text-gray-500 text-sm mt-1">Manage standard contract layouts for Annual Maintenance Contracts.</p>
        </div>
        <a href="{{ route('amc-templates.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Create Template</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Template Name</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Last Updated</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($templates as $template)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $template->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($template->is_default)
                                <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-wider border border-emerald-100">
                                    <i class="fas fa-check-circle text-[8px]"></i> Default
                                </span>
                            @else
                                <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">Optional</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $template->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('amc-templates.edit', $template) }}" class="p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 transition-all" title="Edit Template">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('amc-templates.destroy', $template) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete-confirm p-2 rounded-lg bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all" 
                                            data-type="Template" data-summary='{"Name": "{{ $template->name }}"}'>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-scroll text-gray-200 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No Agreement Templates Found</p>
                            <a href="{{ route('amc-templates.create') }}" class="text-emerald-500 text-xs font-black uppercase tracking-widest mt-2 block hover:underline">Create your first template</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
        <div class="p-4 border-t border-gray-50">
            {{ $templates->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
