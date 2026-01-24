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

    {{-- READ-ONLY MODE --}}
    <input type="hidden" name="mode" value="{{ $notice->mode }}">
    <input type="hidden" name="status" value="{{ $notice->Stau }}">

 <div class="mt-6 card">
    <h3 class="card-title">Document Type</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- ACTION TYPE --}}
        <div>
            <label class="label">Action Type</label>
            <select name="action_type"
                    id="actionType"
                    class="input"
                    required>

                <option value="">Select Type</option>

                {{-- Draft mode --}}
                <option value="notice_issued"
                    {{ $notice->Action_Type === 'notice_issued' ? 'selected' : '' }}>
                    Notice Issued
                </option>

                <option value="circular_issued"
                    {{ $notice->Action_Type === 'circular_issued' ? 'selected' : '' }}>
                    Circular Issued
                </option>

                {{-- Attachment mode --}}
                <option value="notice_received"
                    class="attach-only"
                    {{ $notice->Action_Type === 'notice_received' ? 'selected' : '' }}>
                    Notice Received
                </option>

                <option value="circular_received"
                    class="attach-only"
                    {{ $notice->Action_Type === 'circular_received' ? 'selected' : '' }}>
                    Circular Received
                </option>
            </select>
        </div>

        {{-- ORGANIZATION (RECEIVED ONLY) --}}
        <div id="organizationWrapper"
             class="{{ str_contains($notice->Action_Type, 'received') ? '' : 'hidden' }}">
            <label class="label">Organization Name</label>
            <input type="text"
                   name="organization_name"
                   class="input"
                   placeholder="Enter Organization Name"
                   value="{{ $notice->Orga_Name }}">
        </div>

    </div>
</div>

    {{-- DOCUMENT TYPE --}}
    <div class="hidden">
        <h3 class="card-title">Document Type</h3>

        <div class="">
            <label class="doc-card">
                <input type="radio" name="document_type" value="notice"
                       {{ $notice->Docu_Type === 'notice' ? 'checked' : '' }}>
                <span>Notice</span>
            </label>

            <label class="doc-card">
                <input type="radio" name="document_type" value="circular"
                       {{ $notice->Docu_Type === 'circular' ? 'checked' : '' }}>
                <span>Circular</span>
            </label>
        </div>
    </div>

    {{-- DETAILS --}}
    <div class="card">
        <h3 class="card-title">Details</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Subject</label>
                <input type="text" name="subject" class="input"
                       value="{{ $notice->Subj }}" required>
            </div>

            <div id="referenceWrapper"
                 class="">
                <label class="label">Reference No</label>
                <input type="text"
                       name="ref_no"
                       class="input"
                       value="{{ $notice->Ref_No }}" readonly>
            </div>

            <div>
                <label class="label">Issued / Received Date</label>
                <input type="date" name="notice_date" class="input"
                       value="{{ $notice->Ntic_Crcl_Dt }}" style="color: #fff; color-scheme: dark;"  required>
            </div>

            <div>
                <label class="label">Effective Date</label>
                <input type="date" name="effective_date" class="input"
                       value="{{ $notice->Eft_Dt }}" style="color: #fff; color-scheme: dark;" required>
            </div>
        @if ($notice->Action_Type === 'notice_issued' || $notice->Action_Type === 'circular_issued')

            <div>
                <label class="label">Department</label>
                <select name="department" class="input">
                    @foreach (['Administration','Accounts','HR','IT','Operations'] as $dept)
                        <option value="{{ $dept }}" {{ $notice->Dept === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        </div>
       
    </div>

    {{-- CONTENT --}}
    <div class="card">
           <h3 class="card-title">Content</h3>

        {{-- DRAFT MODE --}}
        @if ($notice->mode === 'draft')
            <textarea id="editor" name="content">{{ $notice->Cntn }}</textarea>

        {{-- ATTACHMENT MODE --}}
        @else

            {{-- EXISTING ATTACHMENT --}}
            @if ($notice->Atch_Path)
                <div id="existingAttachment"
                    class="flex items-center justify-between
                            bg-[#0f2d25] border border-green-700
                            text-green-400 rounded-lg px-4 py-3 mb-3">

                    <div class="flex items-center gap-2 truncate">
                        <i class="bi bi-paperclip"></i>
                        <a href="{{ asset($notice->Atch_Path) }}"
                        target="_blank"
                        class="truncate hover:underline">
                            {{ basename($notice->Atch_Path) }}
                        </a>
                    </div>

                    <button type="button"
                            id="removeAttachmentBtn"
                            class="text-green-400 hover:text-red-400 text-lg font-bold cursor-pointer">
                        ×
                    </button>
                </div>

                <input type="hidden" name="remove_attachment" id="removeAttachmentInput" value="0">
            @endif

            {{-- FILE INPUT (hidden initially if attachment exists) --}}
            <div id="attachmentInputWrapper"
                class="{{ $notice->Atch_Path ? 'hidden' : '' }}">
                <label class="label">Upload PDF</label>
                <input type="file"
                    name="attachment"
                    accept="application/pdf"
                    class="input"
                    id="attachmentInput">
            </div>

        @endif
    </div>



    {{-- SIGNATORY (DRAFT ONLY) --}}
    @if ($notice->mode === 'draft')
    <div class="card">
        <h3 class="card-title">Authorized Signatory</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <input type="file"
                   name="signature_image"
                   accept="image/png,image/jpeg,image/jpg"
                   class="input">

            <input type="text"
                   name="authorized_person_name"
                   class="input"
                   value="{{ $notice->Athr_Pers_Name }}"
                   placeholder="Authorized Person Name">

            <input type="text"
                   name="designation"
                   class="input"
                   value="{{ $notice->Dsig }}"
                   placeholder="Designation">
        </div>
    </div>
    @endif

    {{-- ACTIONS --}}
    <div class="flex justify-end">
        <button type="submit" class="btn-secondary">
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
    const form              = document.getElementById('edit-notice-form');
    const actionTypeSelect  = document.getElementById('actionType');
    const orgWrapper        = document.getElementById('organizationWrapper');
    const refWrapper        = document.getElementById('referenceWrapper');
    const refInput          = refWrapper?.querySelector('input');
     const draftBtn = document.getElementById('draftBtn');
    const attachBtn = document.getElementById('attachBtn');
    const subjectInput      = document.querySelector('input[name="subject"]');
    const noticeDateInput   = document.querySelector('input[name="notice_date"]');
    const attachmentInput  = document.querySelector('input[name="attachment"]');
    const signatureInput   = document.querySelector('input[name="signature_image"]');
    const modeInput        = document.querySelector('input[name="mode"]');
    const submitBtn        = document.querySelector('button[type="submit"]');

    if (!form) return;

    /* ================= ACTION TYPE VISIBILITY ================= */
    function handleActionVisibility() {
        const action = actionTypeSelect.value;

        // Organization → RECEIVED
        if (action.includes('received')) {
            orgWrapper?.classList.remove('hidden');
        } else {
            orgWrapper?.classList.add('hidden');
        }

        // Reference No → ISSUED (Notice + Circular)
        if (action.includes('issued')) {
            refWrapper?.classList.remove('hidden');
            if (refInput) refInput.required = true;
        } else {
            refWrapper?.classList.add('hidden');
            if (refInput) {
                refInput.required = false;
                refInput.value = '';
            }
        }
    }

    actionTypeSelect?.addEventListener('change', handleActionVisibility);
    handleActionVisibility(); // initial load

    /* ================= FORM SUBMIT VALIDATION ================= */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        toastr.clear();

        if (!subjectInput?.value.trim()) {
            toastr.error('Subject is required');
            return;
        }

        if (!noticeDateInput?.value) {
            toastr.error('Notice date is required');
            return;
        }

        const mode = modeInput?.value;

        /* ATTACHMENT MODE CHECK */
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

        /* SIGNATURE IMAGE CHECK */
        if (signatureInput?.files.length > 0) {
            const img = signatureInput.files[0];

            if (!img.type.startsWith('image/')) {
                toastr.error('Signature must be an image');
                return;
            }

            if (img.size > 2 * 1024 * 1024) {
                toastr.error('Signature image must be less than 2MB');
                return;
            }
        }

        submitBtn.disabled = true;
        form.submit();
    });
    // Sync initial mode UI (edit page)
    // ================= SAFE MODE SYNC (EDIT PAGE) =================
    if (modeInput?.value === 'attachment') {
        attachBtn?.classList.add('mode-active');
        draftBtn?.classList.remove('mode-active');

    } else {
        draftBtn?.classList.add('mode-active');
        attachBtn?.classList.remove('mode-active');
    }


    const removeBtn = document.getElementById('removeAttachmentBtn');
    const existingBox = document.getElementById('existingAttachment');
    const removeInput = document.getElementById('removeAttachmentInput');
    const fileWrapper = document.getElementById('attachmentInputWrapper');

    if (removeBtn) {
        removeBtn.addEventListener('click', () => {

            if (!confirm('Remove existing attachment?')) return;

            // mark removal
            removeInput.value = 1;

            // hide existing file
            existingBox.remove();

            // show upload input
            fileWrapper.classList.remove('hidden');
        });
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
