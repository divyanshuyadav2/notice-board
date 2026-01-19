@extends('layouts.layout')

@section('title', 'Edit Notice')

@section('content')
<div class="max-w-5xl mx-auto text-gray-200">
{{-- VALIDATION ERRORS --}}
@if ($errors->any())
    <div class="bg-red-600 text-white p-4 rounded mb-6 alert-dismissible">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-600 text-white p-4 rounded mb-6 alert-dismissible">
        {{ session('error') }}
    </div>
@endif
{{-- HEADER --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('notices.index') }}" class="text-cyan-400 text-xl">←</a>
    <h2 class="text-2xl font-semibold text-white">Edit</h2>
</div>

{{-- mode INDICATOR (READ ONLY) --}}
<div class="flex gap-2 mb-6">
    
    @if ($notice->mode === 'draft')
        <span class="mode-btn mode-active cursor-default">
            Draft mode
        </span>
    @else
        <span class="mode-btn mode-active cursor-default">
            Attachment mode
        </span>
    @endif
</div>



<form method="POST"
      action="{{ route('notices.update', $notice->Ntic_Crcl_UIN) }}"
      enctype="multipart/form-data"
      class="space-y-6"
      id="edit-notice-form">

    @csrf
    @method('PUT')

    {{-- DOCUMENT TYPE --}}
    <div class="mt-6 card">
        <h3 class="card-title">Document Type</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <label class="flex items-center gap-3 bg-[#0b2436] border border-[#1f425a] rounded-lg px-4 py-3 cursor-pointer hover:border-blue-400">
                <input type="radio"
                       name="document_type"
                       value="notice"
                       {{ $notice->Docu_Type === 'notice' ? 'checked' : '' }}
                       class="accent-blue-500">
                <span class="font-medium">Notice</span>
            </label>

            <label class="flex items-center gap-3 bg-[#0b2436] border border-[#1f425a] rounded-lg px-4 py-3 cursor-pointer hover:border-green-400">
                <input type="radio"
                       name="document_type"
                       value="circular"
                       {{ $notice->Docu_Type === 'circular' ? 'checked' : '' }}
                       class="accent-green-500">
                <span class="font-medium">Circular</span>
            </label>

        </div>
    </div>

    {{-- HIDDEN --}}
    <input type="hidden" name="mode" id="modeInput" value="{{ $notice->mode }}">
    <input type="hidden" name="status" id="statusInput" value="{{ $notice->Stau }}">

    {{-- DETAILS --}}
    <div class="card">
        <h3 class="card-title">Details</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <input type="hidden" name="organization_name" value="{{ $notice->Orga_Name }}">

            <div>
                <label class="label">Department</label>
                <select name="department" class="input">
                    @foreach (['Administration','Accounts','HR','IT','Operations'] as $dept)
                        <option value="{{ $dept }}"
                            {{ $notice->Dept === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Subject</label>
                <input type="text" name="subject" class="input"
                       value="{{ $notice->Subj }}" required>
            </div>

            <div>
                <label class="label">Notice Date</label>
                <input type="date" name="notice_date" class="input"
                       value="{{ $notice->Ntic_Crcl_Dt }}" required>
            </div>

            <div>
                <label class="label">Effective Date</label>
                <input type="date" name="effective_date" class="input"
                       value="{{ $notice->Eft_Dt }}" required>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
<div class="card">
    <h3 class="card-title"> Content</h3>

    {{-- DRAFT --}}
    @if ($notice->mode === 'draft')
        <div id="draftmode">
            <textarea id="editor" name="content">{{ $notice->Cntn }}</textarea>
        </div>
    @endif

    {{-- ATTACHMENT --}}
    @if ($notice->mode === 'attachment')
        <div id="attachmode">
            <label class="label">Replace PDF (optional)</label>
            <input type="file"
                   name="attachment"
                   accept="application/pdf"
                   class="input"
                   id="attachmentInput">
        </div>
    @endif
</div>


    {{-- SIGNATORY --}}
   @if ($notice->mode === 'draft')
<div class="card" id="signatorySection">
    <h3 class="card-title">Authorized Signatory</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <input type="file" name="signature_image" id="signatureInput"
               accept="image/png,image/jpeg,image/jpg" class="input">

        <input type="text" name="authorized_person_name"
               class="input"
               value="{{ $notice->Athr_Pers_Name }}"
               placeholder="Authorized Person Name">

        <input type="text" name="designation"
               class="input"
               value="{{ $notice->Desg }}"
               placeholder="Designation">
    </div>
</div>
@endif


    {{-- ACTIONS --}}
    <div class="flex justify-end gap-3">
        <button type="submit" class="btn-secondary" id="saveDraftBtn">
            Update
        </button>
    </div>
</form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ================= CKEDITOR INIT ================= */
    if (window.CKEDITOR && CKEDITOR.ClassicEditor) {
        CKEDITOR.ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: {
                items: [
                    'heading','|',
                    'bold','italic','underline','strikethrough','removeFormat','|',
                    'fontSize','fontFamily','fontColor','fontBackgroundColor','highlight','|',
                    'alignment','|',
                    'bulletedList','numberedList','todoList','|',
                    'outdent','indent','|',
                    'link','insertTable','blockQuote','horizontalLine','|',
                    'undo','redo','|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },

            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3' }
                ]
            },

            fontFamily: {
                options: [
                    'default',
                    'Arial Black, Gadget, sans-serif',
                    'Arial, Helvetica, sans-serif',
                    'Times New Roman, Times, serif',
                    'Georgia, serif',
                    'Courier New, Courier, monospace',
                    'Noto Sans Devanagari, sans-serif',
                    'Kruti Dev 010, serif'
                ],
                supportAllValues: true
            },

            fontSize: {
                options: [10, 12, 14, 16, 18, 20, 22],
                supportAllValues: true
            },

            htmlSupport: {
                allow: [{
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }]
            },

            removePlugins: [
                'CKBox',
                'CKFinder',
                'EasyImage',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                'MathType'
            ]
        }).then(editor => {
            window.noticeEditor = editor;
            console.log('CKEditor ready');
        }).catch(console.error);
    } else {
        console.error('CKEditor not loaded');
    }

    /* ================= ELEMENT REFERENCES ================= */
    const form = document.getElementById('edit-notice-form');
    const draftBtn = document.getElementById('draftBtn');
    const attachBtn = document.getElementById('attachBtn');
    const draftmode = document.getElementById('draftmode');
    const attachmode = document.getElementById('attachmode');
    const modeInput = document.getElementById('modeInput');
    const statusInput = document.getElementById('statusInput');
    const signatorySection = document.getElementById('signatorySection');

    const subjectInput = document.querySelector('input[name="subject"]');
    const noticeDateInput = document.querySelector('input[name="notice_date"]');
    const attachmentInput = document.getElementById('attachmentInput');
    const signatureInput = document.getElementById('signatureInput');
    const submitBtn = document.getElementById('saveDraftBtn');

    if (!form) return;

    /* ================= FORM SUBMIT + VALIDATION ================= */
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // IMPORTANT

        toastr.clear();

        // Required fields
        if (!subjectInput?.value.trim()) {
            toastr.error('Subject is required');
            return;
        }

        if (!noticeDateInput?.value) {
            toastr.error('Notice date is required');
            return;
        }

        const mode = modeInput.value;

        /* ===== ATTACHMENT VALIDATION ===== */
        if (mode === 'attachment' && attachmentInput?.files.length > 0) {
            const file = attachmentInput.files[0];

            if (file.type !== 'application/pdf') {
                toastr.error('Only PDF files are allowed');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                toastr.error('PDF must be less than 5MB');
                return;
            }
        }

        /* ===== SIGNATURE IMAGE VALIDATION ===== */
        if (signatureInput?.files.length > 0) {
            const img = signatureInput.files[0];

            if (!img.type.startsWith('image/')) {
                toastr.error('Signature must be an image file');
                return;
            }

            if (img.size > 2 * 1024 * 1024) {
                toastr.error('Signature image must be less than 2MB');
                return;
            }
        }

        // Prevent double submit
        submitBtn.disabled = true;

        // ✅ SAFE SUBMIT
        form.submit();
    });
    // Sync initial mode UI (edit page)
if (modeInput?.value === 'attachment') {
    attachBtn.classList.add('mode-active');
    draftBtn.classList.remove('mode-active');
    attachmode.classList.remove('hidden');
    draftmode.classList.add('hidden');
    signatorySection?.classList.add('hidden');
} else {
    draftBtn.classList.add('mode-active');
    attachBtn.classList.remove('mode-active');
    draftmode.classList.remove('hidden');
    attachmode.classList.add('hidden');
    signatorySection?.classList.remove('hidden');
}


});
</script>
<style>
    /* BASE mode BUTTON */
.mode-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    border: 1px solid #1f425a;
    background-color: #0b2436;
    color: #9ca3af;
    transition: all 0.2s ease;
}

/* ACTIVE (PRIMARY) */
.mode-btn.mode-active {
    background-color: #2563eb; /* blue-600 */
    border-color: #2563eb;
    color: #ffffff;
}

/* HOVER */
.mode-btn:hover:not(.mode-active) {
    background-color: #14324d;
    color: #e5e7eb;
}

</style>
@endsection
