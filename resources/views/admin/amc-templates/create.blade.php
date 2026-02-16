@extends('layouts.admin')

@section('header', 'New AMC Template')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('amc-templates.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-100 text-gray-400 hover:text-emerald-500 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Create Agreement Template</h2>
    </div>

    <form action="{{ route('amc-templates.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Template Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border-gray-100 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all" placeholder="e.g., Annual Maintenance Agreement">
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Agreement Content</label>
                    <textarea name="content" id="template-editor" class="tinymce"></textarea>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest border-b border-gray-50 pb-3">Available Variables</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Copy and paste these variables into your template. They will be replaced with real data when generating the agreement.</p>
                
                <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2">
                    @foreach([
                        '{customer_name}' => 'Customer Full Name',
                        '{customer_phone}' => 'Customer Phone Number',
                        '{contract_number}' => 'AMC Contract Unique ID',
                        '{start_date}' => 'Contract Start Date',
                        '{end_date}' => 'Contract Expiry Date',
                        '{amount}' => 'Total Contract Amount',
                        '{frequency}' => 'Service Frequency (e.g., Monthly)',
                        '{items_table}' => 'Dynamic table of covered items',
                        '{services_table}' => 'Dynamic table of scheduled visits',
                        '{site_name}' => 'Company Name (from settings)'
                    ] as $var => $desc)
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 group hover:border-emerald-200 transition-all cursor-pointer" onclick="copyVar('{{ $var }}')">
                        <code class="text-emerald-600 font-bold block mb-1">{{ $var }}</code>
                        <span class="text-[0.65rem] text-gray-500 font-medium tracking-wide uppercase">{{ $desc }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Set as Default</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
                
                <button type="submit" class="w-full py-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-widest shadow-lg shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5">
                    Save Template
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#template-editor',
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount emoticons codesample',
        toolbar1: 'undo redo | blocks | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
        toolbar2: 'link image table | removeformat | code fullscreen preview',
        height: 600,
        menubar: 'edit insert view format table help',
        branding: false,
        promotion: false,
        elementpath: true,
        content_style: 'body { font-family: "Outfit", sans-serif; font-size: 14px; line-height: 1.6; color: #334155; }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
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
