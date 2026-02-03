@extends('layouts.admin')

@section('header', 'Contact Messages')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Contact Messages</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($messages as $msg)
            <tr class="hover:bg-gray-50 {{ $msg->status === 'unread' ? 'bg-blue-50/30' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($msg->status === 'unread')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Unread
                        </span>
                    @elseif($msg->status === 'read')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Read
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Replied
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $msg->name }}</div>
                    <div class="text-sm text-gray-500">{{ $msg->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                    {{ Str::limit($msg->subject, 40) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $msg->created_at->format('M d, Y H:i') }}
                    <div class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.contact_messages.show', $msg) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Message">
                        <i class="fas fa-eye text-lg"></i>
                    </a>
                    
                    <form action="{{ route('admin.contact_messages.destroy', $msg) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 btn-delete-confirm"
                            title="Delete"
                            data-title="Delete Message from {{ $msg->name }}?"
                            data-type="Message"
                            data-summary='{"Subject": "{{ $msg->subject }}"}'
                        >
                            <i class="fas fa-trash text-lg"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-500">No contact messages yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $messages->links() }}
    </div>
</div>
@endsection
