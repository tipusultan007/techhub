@extends('layouts.admin')

@section('header', 'Contract Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('amcs.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 text-gray-400 hover:text-emerald-500 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $amc->contract_number }}</h2>
                <p class="text-gray-500 text-sm">Customer: <span class="font-bold text-gray-700">{{ $amc->customer->name }}</span></p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('amcs.agreement', $amc) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 text-sm uppercase tracking-widest">
                <i class="fas fa-file-contract"></i> View Agreement
            </a>
            <form action="{{ route('amcs.generate-schedule', $amc) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold shadow-lg shadow-amber-500/30 transition-all transform hover:-translate-y-0.5 text-sm uppercase tracking-widest">
                    <i class="fas fa-calendar-alt"></i> Regenerate Schedule
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Overview -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Covered Items -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Covered Equipment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($amc->items as $item)
                    <div class="flex gap-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Description</p>
                            <p class="text-sm font-bold text-gray-800">{{ $item->description }}</p>
                            @if($item->product)
                                <p class="text-[10px] text-emerald-600 font-bold uppercase mt-1">Catalog Item: {{ $item->product->name }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Included Services -->
            @if($amc->includedServices->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Included Services</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($amc->includedServices as $service)
                    <div class="flex gap-4 p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Service</p>
                            <p class="text-sm font-bold text-gray-800">{{ $service->service_name }}</p>
                            @if($service->description)
                                <p class="text-xs text-gray-500 mt-1 leading-tight">{{ $service->description }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Service Schedule -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest italic border-b border-gray-50 pb-3">Maintenance Schedule</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                                <th class="py-3 px-2">Visit No.</th>
                                <th class="py-3 px-2">Scheduled Date</th>
                                <th class="py-3 px-2">Status</th>
                                <th class="py-3 px-2 text-right">Performed Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($amc->services as $index => $service)
                            <tr class="group text-sm">
                                <td class="py-4 px-2 font-black text-gray-300">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-4 px-2 font-bold text-gray-900">{{ $service->scheduled_date->format('d M Y') }}</td>
                                <td class="py-4 px-2 text-xs font-black uppercase">
                                    @php
                                        $srvColors = ['scheduled' => 'blue', 'completed' => 'emerald', 'cancelled' => 'red', 'rescheduled' => 'amber'];
                                        $c = $srvColors[$service->status] ?? 'gray';
                                    @endphp
                                    <span class="text-{{ $c }}-600 bg-{{ $c }}-50 px-2 py-0.5 rounded ring-1 ring-{{ $c }}-100">{{ $service->status }}</span>
                                </td>
                                <td class="py-4 px-2 text-right text-gray-400">
                                    {{ $service->actual_service_date ? $service->actual_service_date->format('d M Y') : '---' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Details -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Contract Timeline</h3>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Starts</p>
                            <p class="text-sm font-black text-gray-800">{{ $amc->start_date->format('d M Y') }}</p>
                        </div>
                        <div class="w-px h-8 bg-gray-100"></div>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Expires</p>
                            <p class="text-sm font-black text-red-600">{{ $amc->end_date->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Financials</h3>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                        <p class="text-[10px] font-bold text-emerald-600 uppercase">Contract Value</p>
                        <p class="text-2xl font-black text-emerald-700">{{ number_format($amc->amount, 2) }} <span class="text-xs font-medium">{{ settings('currency_symbol', '$') }}</span></p>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Service Policy</h3>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-redo text-emerald-500"></i>
                        <span class="text-sm font-bold text-gray-800 uppercase italic">{{ $amc->frequency }} Visits</span>
                    </div>
                </div>

                @if($amc->hasMedia('attachments'))
                <div class="no-print">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Attachments</h3>
                    <button onclick="openAttachmentModal()" 
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5 text-sm uppercase tracking-widest">
                        <i class="fas fa-paperclip"></i> View Attachments ({{ $amc->getMedia('attachments')->count() }})
                    </button>
                    
                    <!-- Attachment Modal -->
                    <div id="attachmentModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[100] p-4 no-print text-left">
                        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100">
                            <div class="px-8 py-6 border-b bg-gray-50 flex justify-between items-center">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 tracking-tight text-left">Contract Attachments</h3>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 text-left">Total Files: {{ $amc->getMedia('attachments')->count() }}</p>
                                </div>
                                <button onclick="closeAttachmentModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors shadow-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="p-8 max-h-[60vh] overflow-y-auto space-y-3">
                                @foreach($amc->getMedia('attachments') as $media)
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100 group hover:border-emerald-200 transition-colors">
                                        <div class="flex items-center gap-4 truncate">
                                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-xs font-bold text-gray-700 truncate text-left">{{ $media->file_name }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5 text-left">{{ number_format($media->size / 1024, 0) }} KB</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ $media->getUrl() }}" target="_blank" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                <i class="fas fa-eye text-[10px]"></i>
                                            </a>
                                            <a href="{{ $media->getUrl() }}" download class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-800 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                                <i class="fas fa-download text-[10px]"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="px-8 py-6 bg-gray-50 border-t flex justify-end">
                                <button onclick="closeAttachmentModal()" class="px-6 py-2.5 rounded-xl bg-gray-800 text-white font-bold text-xs hover:bg-gray-900 transition-all shadow-lg active:scale-95 uppercase tracking-widest">
                                    Done
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($amc->notes)
                <div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Internal Notes</h3>
                    <p class="text-xs text-gray-500 leading-relaxed italic">{{ $amc->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    function openAttachmentModal() {
        $('#attachmentModal').removeClass('hidden').addClass('flex');
        $('body').addClass('overflow-hidden');
    }

    function closeAttachmentModal() {
        $('#attachmentModal').addClass('hidden').removeClass('flex');
        $('body').removeClass('overflow-hidden');
    }

    // Close on escape
    $(document).keyup(function(e) {
        if (e.key === "Escape") closeAttachmentModal();
    });
    
    // Close on click outside
    $('#attachmentModal').click(function(e) {
        if (e.target === this) closeAttachmentModal();
    });
</script>
@endsection
@endsection
