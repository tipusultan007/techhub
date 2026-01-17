@extends('layouts.admin')

@section('header', 'Notification Center')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h3 class="text-xl font-bold text-gray-900 tracking-tight">Alert History</h3>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Review all system alerts and updates</p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                    Mark All Read
                </button>
            </form>
            <form action="{{ route('notifications.clear') }}" method="POST" onsubmit="return confirm('Wipe notification history?');">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-sm">
                    Clear History
                </button>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50">
            @forelse($notifications as $notification)
                <div class="p-6 transition-all hover:bg-gray-50/50 flex gap-6 items-start relative {{ $notification->read_at ? 'opacity-70' : '' }}">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $notification->data['color'] ?? 'blue' }}-50 flex items-center justify-center text-{{ $notification->data['color'] ?? 'blue' }}-600 shrink-0 shadow-inner">
                        <i class="{{ $notification->data['icon'] ?? 'fas fa-info-circle' }} text-lg"></i>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-1 mb-1">
                            <h4 class="text-sm font-black text-gray-900 tracking-tight">
                                {{ $notification->data['title'] }}
                                @if(!$notification->read_at)
                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                @endif
                            </h4>
                            <span class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-tighter">{{ $notification->created_at->format('d M, Y • H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $notification->data['message'] }}</p>
                        
                        <div class="mt-4 flex gap-4">
                            @if($notification->data['action_url'] ?? false)
                                <a href="{{ $notification->data['action_url'] }}" 
                                   class="text-[0.65rem] font-black text-blue-600 uppercase tracking-widest hover:underline flex items-center gap-1.5">
                                    View Details <i class="fas fa-arrow-right text-[0.5rem]"></i>
                                </a>
                            @endif
                            
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-ghost text-3xl text-gray-200"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Quiet in here...</h3>
                    <p class="text-gray-500 text-sm mt-1 mb-6">You don't have any notifications at the moment.</p>
                    <a href="{{ route('dashboard') }}" class="inline-block px-8 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-900/20 hover:bg-blue-700 transition-all">
                        Return to Dashboard
                    </a>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/30">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
