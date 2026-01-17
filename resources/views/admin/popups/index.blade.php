@extends('layouts.admin')

@section('header', 'Offer Popups')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Offer Popups</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your promotional popups and exclusive offers.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('popups.admin.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                <i class="fas fa-plus mr-2"></i> Create New Popup
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Config</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($popups as $popup)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($popup->image_path)
                                    <img src="{{ Storage::url($popup->image_path) }}" alt="Popup" class="h-12 w-20 object-cover rounded-md border border-gray-200 shadow-sm">
                                @else
                                    <div class="h-12 w-20 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $popup->title }}</div>
                                <div class="text-xs text-gray-500 mt-1 max-w-xs truncate">{{ $popup->subtitle }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($popup->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500 flex flex-col gap-1">
                                    @if($popup->start_date)
                                        <span class="flex items-center gap-1"><i class="far fa-calendar-check text-green-500"></i> {{ $popup->start_date->format('M d, Y') }}</span>
                                    @endif
                                    @if($popup->end_date)
                                        <span class="flex items-center gap-1"><i class="far fa-calendar-times text-red-500"></i> {{ $popup->end_date->format('M d, Y') }}</span>
                                    @endif
                                    @if(!$popup->start_date && !$popup->end_date)
                                        <span class="text-gray-400 italic">Always Available</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500 flex flex-col gap-1">
                                    <span class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded border border-blue-100 w-fit">Delay: {{ $popup->display_delay }}s</span>
                                    <span class="bg-purple-50 text-purple-700 px-1.5 py-0.5 rounded border border-purple-100 w-fit">Cookie: {{ $popup->cookie_duration }}d</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('popups.admin.preview', $popup->id) }}" target="_blank" class="text-teal-600 hover:text-teal-900 transition-colors" title="Preview">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('popups.admin.edit', $popup->id) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <form action="{{ route('popups.admin.destroy', $popup->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this popup?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-bullhorn text-gray-400 text-xl"></i>
                                    </div>
                                    <p class="font-medium text-gray-900">No popups found</p>
                                    <p class="text-sm mt-1">Get started by creating a new special offer popup.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
