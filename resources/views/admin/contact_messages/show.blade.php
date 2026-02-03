@extends('layouts.admin')

@section('header', 'View Contact Message')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.contact_messages.index') }}" class="text-gray-600 hover:text-indigo-600 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Messages
    </a>
</div>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header -->
        <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $message->subject }}</h1>
                <p class="text-sm text-gray-500 mt-1">Received on {{ $message->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div>
                @if($message->status === 'unread')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Unread</span>
                @elseif($message->status === 'read')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">Read</span>
                @else
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Replied</span>
                @endif
            </div>
        </div>

        <!-- From Info -->
        <div class="px-8 py-6 grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-gray-100">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Sender Information</label>
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold mr-4">
                        {{ substr($message->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">{{ $message->name }}</div>
                        <div class="text-indigo-600">{{ $message->email }}</div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Contact Details</label>
                <div class="space-y-1">
                    <p class="text-gray-700 font-medium">
                        <i class="fas fa-phone-alt w-5 text-gray-400 mr-2"></i>
                        {{ $message->phone ?? 'Not Provided' }}
                    </p>
                    <p class="text-gray-700 font-medium">
                        <i class="fas fa-envelope w-5 text-gray-400 mr-2"></i>
                        <a href="mailto:{{ $message->email }}" class="text-indigo-600 hover:underline">Reply via Email</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Message Body -->
        <div class="px-8 py-10">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Message Content</label>
            <div class="bg-gray-50 p-8 rounded-2xl text-gray-800 text-lg leading-relaxed whitespace-pre-line border border-gray-100">
                {{ $message->message }}
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
            <form action="{{ route('admin.contact_messages.destroy', $message) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 font-bold transition-all btn-delete-confirm"
                    data-title="Delete this message?"
                    data-type="Message"
                    data-summary='{"From": "{{ $message->name }}"}'>
                    <i class="fas fa-trash-alt mr-2"></i> Delete Message
                </button>
            </form>
            
            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" 
               class="px-8 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 font-bold shadow-lg shadow-indigo-100 transform hover:-translate-y-0.5 transition-all flex items-center">
                <i class="fas fa-reply mr-2"></i> Reply to {{ $message->name }}
            </a>
        </div>
    </div>
</div>
@endsection
