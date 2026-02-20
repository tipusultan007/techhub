@extends('layouts.admin')

@section('header', 'New AMC Contract')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-radius: 0.75rem !important;
        height: 3rem !important;
        border-color: #d1d5db !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 0.5rem !important;
        box-sizing: border-box !important;
        vertical-align: middle !important;
    }
    .select2-container {
        vertical-align: middle !important;
        display: inline-block !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 0.5rem !important;
        color: #1f2937 !important;
        display: block !important;
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 3rem !important;
        top: 0 !important;
        right: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-right: 2rem !important;
    }
    .form-input-styled {
        height: 3rem !important;
        line-height: 3rem !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        box-sizing: border-box !important;
        vertical-align: middle !important;
        display: block !important;
        width: 100% !important;
    }
    /* Scope baseline alignment to inner form grids only */
    .bg-white .grid-cols-1, 
    .bg-white .grid-cols-2, 
    .bg-white .grid-cols-12 {
        align-items: end !important;
    }
    
    /* Ensure main layout grid items stay at the top */
    #main-form-grid {
        align-items: start !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('amcs.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-400 hover:text-emerald-500 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Create New AMC</h2>
    </div>

    <form action="{{ route('amcs.store') }}" method="POST" id="amc-form" class="space-y-6" enctype="multipart/form-data">
        @csrf
        
        <!-- Toggle Agreement Content Area -->
        <div id="full-width-editor" class="hidden space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest italic">Custom Agreement Content</h3>
                    <button type="button" onclick="setAgreementType('template')" class="text-xs font-bold text-emerald-500 hover:underline">
                        <i class="fas fa-exchange-alt mr-1"></i> Switch to Template
                    </button>
                </div>
                <textarea name="custom_agreement_content" id="custom-agreement-editor" class="tinymce"></textarea>
                
                <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Available Variables (Click to copy)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            '{customer_name}' => 'Customer Name',
                            '{customer_phone}' => 'Phone',
                            '{contract_number}' => 'Contract No.',
                            '{start_date}' => 'Start Date',
                            '{end_date}' => 'End Date',
                            '{amount}' => 'Total Amount',
                            '{frequency}' => 'Frequency',
                            '{items_table}' => 'Items Table',
                            '{services_table}' => 'Visits Table',
                            '{included_services}' => 'Included Services',
                            '{site_name}' => 'Site Name'
                        ] as $var => $label)
                            <code class="text-[10px] bg-white px-2 py-1 rounded border border-emerald-200 cursor-pointer hover:bg-emerald-100" onclick="copyVar('{{ $var }}')" title="{{ $label }}">{{ $var }}</code>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" id="main-form-grid">
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest border-b border-gray-50 pb-3 italic">Contract Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest block">Select Customer</label>
                            <select name="customer_id" required class="w-full select2-basic">
                                <option value="">-- Choose Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Amount</label>
                            <input type="number" step="0.01" name="amount" required class="w-full form-input-styled form-input-styled px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all" placeholder="0.00">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Start Date</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full form-input-styled px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">End Date</label>
                            <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required class="w-full form-input-styled px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Service Frequency</label>
                            <select name="frequency" required class="w-full select2-basic">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly" selected>Quarterly</option>
                                <option value="semi-annually">Semi-Annually</option>
                                <option value="annually">Annually</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Covered Items -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest italic">Covered Equipment/Products</h3>
                        <button type="button" id="add-item-btn" class="text-emerald-500 hover:text-emerald-600 font-bold text-xs uppercase tracking-widest">
                            <i class="fas fa-plus-circle mr-1"></i> Add Product
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl relative group">
                            <div class="md:col-span-5 space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Product (Optional)</label>
                                <select name="items[0][product_id]" class="w-full select2-basic">
                                    <option value="">-- Choose Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-6 space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description/Serial No.</label>
                                <input type="text" name="items[0][description]" required class="w-full form-input-styled px-3 py-2 rounded-lg border-gray-200 text-sm" placeholder="e.g., HP LaserJet M1132 - S/N: 12345">
                            </div>
                            <div class="md:col-span-1 flex items-end justify-center pb-1">
                                <button type="button" class="remove-item-btn text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Included Services -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest italic">Included Services</h3>
                        <button type="button" id="add-service-btn" class="text-blue-500 hover:text-blue-600 font-bold text-xs uppercase tracking-widest">
                            <i class="fas fa-plus-circle mr-1"></i> Add Service
                        </button>
                    </div>

                    <div id="services-container" class="space-y-3">
                        <div class="service-row grid grid-cols-1 md:grid-cols-12 gap-4 p-4 bg-gray-50 rounded-xl relative group">
                            <div class="md:col-span-4 space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Service Name</label>
                                <input type="text" name="included_services[0][service_name]" class="w-full form-input-styled px-3 py-2 rounded-lg border-gray-200 text-sm" placeholder="e.g., Cleaning">
                            </div>
                            <div class="md:col-span-7 space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description</label>
                                <input type="text" name="included_services[0][description]" class="w-full form-input-styled px-3 py-2 rounded-lg border-gray-200 text-sm" placeholder="e.g., Full internal & external cabinet cleaning">
                            </div>
                            <div class="md:col-span-1 flex items-end justify-center pb-1">
                                <button type="button" class="remove-service-btn text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest border-b border-gray-50 pb-3 italic">Agreement Style</h3>
                    
                    <div class="space-y-4">
                        <div class="flex p-1 bg-gray-50 rounded-xl">
                            <button type="button" onclick="setAgreementType('template')" id="btn-type-template" class="flex-1 py-2 text-xs font-black uppercase tracking-widest rounded-lg transition-all bg-white shadow-sm text-emerald-600">
                                Use Template
                            </button>
                            <button type="button" onclick="setAgreementType('custom')" id="btn-type-custom" class="flex-1 py-2 text-xs font-black uppercase tracking-widest rounded-lg transition-all text-gray-400">
                                Custom Write
                            </button>
                            <input type="hidden" name="agreement_type" id="agreement_type" value="template">
                        </div>

                        <div id="wrapper-template" class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest block">Agreement Template</label>
                            <select name="template_id" class="w-full select2-basic">
                                <option value="">-- Use Default --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="wrapper-custom-summary" class="hidden p-4 bg-emerald-50 rounded-xl border border-emerald-100 text-center">
                            <p class="text-xs font-bold text-emerald-700">Custom Agreement Mode Active</p>
                            <button type="button" onclick="scrollToEditor()" class="text-[10px] text-emerald-500 hover:underline uppercase font-black mt-1">
                                <i class="fas fa-edit mr-1"></i> Edit Content
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest block">Contract Attachment</label>
                        <input type="file" name="attachment[]" multiple class="w-full border rounded-xl p-2 mt-1 bg-white focus:ring-2 focus:ring-emerald-500 outline-none text-sm">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Internal Notes</label>
                        <textarea name="notes" rows="4" class="w-full form-input-styled px-4 py-3 rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all" placeholder="Any private details..."></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-widest shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5">
                        Create Contract
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    $(document).ready(function() {
        initSelect2();
    });

    function initSelect2() {
        $('.select2-basic').select2({
            width: '100%'
        });
    }

    function setAgreementType(type) {
        const btnTemplate = document.getElementById('btn-type-template');
        const btnCustom = document.getElementById('btn-type-custom');
        const wrapperTemplate = document.getElementById('wrapper-template');
        const wrapperCustomEditor = document.getElementById('full-width-editor');
        const wrapperCustomSummary = document.getElementById('wrapper-custom-summary');
        const hiddenInput = document.getElementById('agreement_type');

        hiddenInput.value = type;

        if (type === 'template') {
            btnTemplate.classList.add('bg-white', 'shadow-sm', 'text-emerald-600');
            btnTemplate.classList.remove('text-gray-400');
            btnCustom.classList.remove('bg-white', 'shadow-sm', 'text-emerald-600');
            btnCustom.classList.add('text-gray-400');
            wrapperTemplate.classList.remove('hidden');
            wrapperCustomEditor.classList.add('hidden');
            wrapperCustomSummary.classList.add('hidden');
        } else {
            btnCustom.classList.add('bg-white', 'shadow-sm', 'text-emerald-600');
            btnCustom.classList.remove('text-gray-400');
            btnTemplate.classList.remove('bg-white', 'shadow-sm', 'text-emerald-600');
            btnTemplate.classList.add('text-gray-400');
            wrapperTemplate.classList.add('hidden');
            wrapperCustomEditor.classList.remove('hidden');
            wrapperCustomSummary.classList.remove('hidden');
            
            // Scroll to editor
            scrollToEditor();
        }
    }

    function scrollToEditor() {
        document.getElementById('full-width-editor').scrollIntoView({ behavior: 'smooth' });
    }

    tinymce.init({
        selector: '#custom-agreement-editor',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount emoticons codesample',
        toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table code | fullscreen',
        height: 500,
        menubar: 'edit insert view format table help',
        branding: false,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });

    let itemIndex = 1;
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const firstRow = container.querySelector('.item-row');
        
        // Destroy select2 before cloning
        $(firstRow).find('.select2-basic').select2('destroy');
        
        const newRow = firstRow.cloneNode(true);
        
        // Reset inputs and update names
        newRow.querySelectorAll('select, input').forEach(el => {
            el.value = '';
            el.name = el.name.replace('[0]', `[${itemIndex}]`);
            if (el.classList.contains('select2-hidden-accessible')) {
                $(el).removeClass('select2-hidden-accessible');
                $(el).next('.select2-container').remove();
            }
        });
        
        container.appendChild(newRow);
        
        // Re-init select2
        initSelect2();
        itemIndex++;
    });

    let serviceIndex = 1;
    document.getElementById('add-service-btn').addEventListener('click', function() {
        const container = document.getElementById('services-container');
        const firstRow = container.querySelector('.service-row');
        const newRow = firstRow.cloneNode(true);
        
        newRow.querySelectorAll('input').forEach(el => {
            el.value = '';
            el.name = el.name.replace('[0]', `[${serviceIndex}]`);
        });
        
        container.appendChild(newRow);
        serviceIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
            } else {
                toastr.warning('At least one item is required.');
            }
        }
        if (e.target.closest('.remove-service-btn')) {
            const rows = document.querySelectorAll('.service-row');
            if (rows.length > 1) {
                e.target.closest('.service-row').remove();
            } else {
                toastr.warning('At least one service row is required.');
            }
        }
    });

    function copyVar(text) {
        if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
            tinymce.activeEditor.execCommand('mceInsertContent', false, text);
            toastr.success('Inserted into editor');
        } else {
            navigator.clipboard.writeText(text);
            toastr.success('Copied: ' + text);
        }
    }
</script>
@endpush
