@extends('layouts.admin')

@push('styles')
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding-top: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush

@section('header', 'Activity Logs')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded-lg shadow overflow-hidden border border-gray-200">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-700">System Activity Logs</h3>
        <span class="text-xs text-gray-500">Super Admin Only</span>
    </div>

    <!-- Filters Section -->
    <div class="px-6 py-4 border-b bg-white">
        <form action="{{ route('activity-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- User -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">User</label>
                <select name="user_id" class="w-full select2 border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Module -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Module</label>
                <select name="module" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Action</label>
                <select name="action" class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <option value="">All Actions</option>
                    <option value="Create" {{ request('action') == 'Create' ? 'selected' : '' }}>Create</option>
                    <option value="Edit" {{ request('action') == 'Edit' ? 'selected' : '' }}>Edit</option>
                    <option value="Delete" {{ request('action') == 'Delete' ? 'selected' : '' }}>Delete</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Action Buttons -->
            <div class="md:col-span-3 lg:col-span-5 flex justify-end gap-2">
                <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300 transition">
                    <i class="fas fa-undo mr-1"></i> Reset
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm font-bold hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Module</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Action</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">IP Address</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Timestamp</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $log->user ? $log->user->name : 'System' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 py-1 rounded text-[10px] font-black uppercase bg-gray-100 text-gray-800 border border-gray-200">
                        {{ $log->module }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @php
                        $color = match($log->action) {
                            'Create' => 'bg-green-100 text-green-700 border-green-200',
                            'Edit' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'Delete' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-gray-100 text-gray-700 border-gray-200'
                        };
                    @endphp
                    <span class="px-2 py-1 rounded text-[10px] font-black uppercase {{ $color }} border">
                        {{ $log->action }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $log->description }}
                    @if($log->data)
                        <button type="button" class="ml-2 text-blue-500 hover:text-blue-700 text-xs focus:outline-none" 
                                onclick="toggleData('data-{{ $log->id }}')">
                            <i class="fas fa-info-circle"></i> View Data
                        </button>
                        <div id="data-{{ $log->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded border text-xs font-mono overflow-auto max-w-md">
                            <pre>{{ json_encode($log->data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $log->ip_address }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $log->created_at->format('d M Y, h:i A') }}
                    <div class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                    No activity logs found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">
        {{ $logs->links() }}
    </div>
</div>
@endsection

@section('scripts')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: "All Users",
                allowClear: true
            });
        });

        function toggleData(id) {
            const el = document.getElementById(id);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
    </script>
@endsection
