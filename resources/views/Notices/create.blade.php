@extends('layouts.layout')

@section('title', 'Add')

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
    <div class="bg-red-600 text-white p-4 rounded mb-6 alert-dismissible" i>
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="bg-green-600 text-white p-4 rounded mb-6 alert-dismissible">
        {{ session('success') }}
    </div>
@endif
    {{-- PAGE HEADER --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('notices.index') }}" class="text-cyan-400 text-xl">←</a>
        <h2 class="text-2xl font-semibold text-white">Add</h2>
    </div>

    {{-- MODE SWITCH --}}
    <div class="flex gap-2 mb-6">
        <button type="button" id="draftBtn"
        class="mode-btn {{ old('mode','draft')==='draft'?'mode-active':'' }}">
            Draft Mode
        </button>

        <button type="button" id="attachBtn"
            class="mode-btn {{ old('mode')==='attachment'?'mode-active':'' }}">
            Attachment Mode
        </button>
    </div>

 {{-- FORM Live AJAX ref_no check --}}
    <form method="POST" action="{{ route('notices.store') }}"  enctype="multipart/form-data" class="space-y-6" id="create-notice-form">
        @csrf
    <div class="mt-6 card">
                  <h3 class="card-title">Document Type</h3>

       <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <input type="text" name="Orga_UIN" class="hidden" value="{{ session('organization_uin') }}">
            <div>
                <label class="label">Action Type</label>
                <select name="action_type" id="actionType" class="input" required>
                    <!-- Draft mode options -->
                    <option value="" selected>Seelct Type </option>
                    <option value="notice_issued">Notice Issued</option>
                    <option value="circular_issued">Circular Issued</option>

                    <!-- Attachment mode options -->
                    <option value="notice_received" class="attach-only hidden">Notice Received</option>
                    <option value="circular_received" class="attach-only hidden">Circular Received</option>
                </select>
            </div>

                <div id="organizationWrapper" class="hidden">
                    <label class="label">Organization Name</label>
                    <input type="text" name="organization_name" class="input"
                        placeholder="Enter Organization Name" value="{{ session('org_name') }}">
                </div>

            </div>
    </div>



        {{-- IMPORTANT HIDDEN FIELDS --}}
        <input type="hidden" name="mode" id="modeInput" value="{{ old('mode', 'draft') }}">
        <input type="hidden" name="status" id="statusInput" value="draft">

        {{-- NOTICE DETAILS --}}
        <div class="card">
            <h3 class="card-title"> Details</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div>
                    <label class="label">Issued/Recevied Date</label>
                    <input type="date" name="notice_date" class="input" value="{{ old('notice_date') }}" style="color: #fff; color-scheme: dark;" required>
                    
                </div>
                <div>
                    <label class="label">Effective Date</label>
                    <input type="date" name="effective_date" value="{{ old('effective_date') }}"  class="input" style="color: #fff; color-scheme: dark;" required>
                </div>
                <div>
                    <label class="label">Subject</label>
                    <input type="text" name="subject" class="input" value="{{ old('subject') }}" required>
                </div>
                <div id="referenceWrapper">
                    <label class="label">Reference No.</label>
                    <input type="text" name="ref_no" value="{{ old('ref_no') }}" class="input" id="refNoInput"  required>
                </div>

                 <div class="" id="depart-ment">
                    <label class="label">Department</label>
                     <select name="department" class="input">
                        <option value="">Select Department</option>
                        <option value="Administration" {{ old('department')=='Administration'?'selected':'' }}>Administration</option>
                        <option value="Accounts" {{ old('department')=='Accounts'?'selected':'' }}>Accounts</option>
                        <option value="HR" {{ old('department')=='HR'?'selected':'' }}>HR</option>
                        <option value="IT" {{ old('department')=='IT'?'selected':'' }}>IT</option>
                        <option value="Operations" {{ old('department')=='Operations'?'selected':'' }}>Operations</option>
                    </select>
                </div>
                
              
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="card">

            {{-- TEXT MODE --}}
            <div id="draftMode" class="{{ old('mode','draft')==='attachment'?'hidden':'' }}">
                    <textarea id="editor" name="content">{{ old('content') }}</textarea>
            </div>

             {{-- Attachment --}}
            <div id="attachMode"
                class="{{ old('mode','draft')==='draft'?'hidden':'' }}">
                <label class="label">Upload PDF</label>
                <input type="file" name="attachment"
                    id="attachmentInput"
                    class="input"
                    accept="application/pdf">
            </div>
        </div>

        {{-- SIGNATURE --}}
        <div class="card" id="signatorySection" class="{{ old('mode','draft')==='attachment'?'hidden':'' }}">
            <h3 class="card-title">Authorized Signatory</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Signature Image</label>
                    <input type="file" name="signature_image" id="signatureInput" accept="image/png,image/jpeg,image/jpg" class="input">
                </div>

                <div>
                    <label class="label">Authorized Person Name</label>
                    <input type="text" name="authorized_person_name" class="input" value="{{ old('authorized_person_name') }}">
                </div>

                <div>
                    <label class="label">Designation</label>
                    <input type="text" name="designation" class="input" value="{{ old('designation') }}">
                </div>

               
            </div>
        </div>
       <input type="hidden" name="document_type" id="documentTypeInput"
       value="{{ old('document_type') }}">


        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">
            <button type="submit"  class="btn-secondary" id="saveDraftBtn">
                Save Draft
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
                    'subscript','superscript',
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
    const draftBtn = document.getElementById('draftBtn');
    const attachBtn = document.getElementById('attachBtn');
    const draftMode = document.getElementById('draftMode');
    const attachMode = document.getElementById('attachMode');
    const modeInput = document.getElementById('modeInput');
    const statusInput = document.getElementById('statusInput');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const signatorySection = document.getElementById('signatorySection');
    const form = document.getElementById('create-notice-form');

    if (!form) return;

    const attachmentInput = document.getElementById('attachmentInput');
    const signatureInput = document.getElementById('signatureInput');
    const subjectInput = document.querySelector('input[name="subject"]');
    const noticeDateInput = document.querySelector('input[name="notice_date"]');

    // NEW FIELDS
    const actionTypeSelect = document.getElementById('actionType');
    const orgWrapper = document.getElementById('organizationWrapper');
    const documentTypeInput = document.getElementById('documentTypeInput');
    const department = document.getElementById('depart-ment');

    if (modeInput.value === 'attachment') {
        signatorySection.classList.add('hidden');
         syncActionOptions('attachment');
    } else {
        signatorySection.classList.remove('hidden');
    }


    /* ================= MODE-BASED ACTION OPTIONS ================= */
    function syncActionOptions(mode) {
        document.querySelectorAll('.attach-only').forEach(opt => {
            opt.classList.toggle('hidden', mode !== 'attachment');
        });

        if (mode === 'draft') {
            actionTypeSelect.value = 'notice_issued';
        } else {
            actionTypeSelect.value = '';
        }

        handleActionVisibility();
    }
        // Initial load
        handleActionVisibility();
        syncDocumentType();
    /* ================= ACTION TYPE VISIBILITY ================= */
    function handleActionVisibility() {
        const action = actionTypeSelect.value;

        // Organization input
        if (action.includes('received')) {
            orgWrapper.classList.remove('hidden');
            department.classList.add('hidden');
        } else {
            orgWrapper.classList.add('hidden');
            department.classList.remove('hidden');
        }
  
    }
    function syncDocumentType() {
            const action = actionTypeSelect.value;

            if (
                action === 'notice_issued' ||
                action === 'notice_received'
            ) {
                documentTypeInput.value = 'notice';
            }

            if (
                action === 'circular_issued' ||
                action === 'circular_received'
            ) {
                documentTypeInput.value = 'circular';
            }
    }


    /* ================= MODE TOGGLE ================= */
    draftBtn?.addEventListener('click', () => {
        modeInput.value = 'draft';
        draftBtn.classList.add('mode-active');
        attachBtn.classList.remove('mode-active');
        draftMode.classList.remove('hidden');
        attachMode.classList.add('hidden');
        signatorySection?.classList.remove('hidden');
        syncActionOptions('draft');
    });

    attachBtn?.addEventListener('click', () => {
        modeInput.value = 'attachment';
        attachBtn.classList.add('mode-active');
        draftBtn.classList.remove('mode-active');
        attachMode.classList.remove('hidden');
        draftMode.classList.add('hidden');
        signatorySection?.classList.add('hidden');
        syncActionOptions('attachment');
    });

    /* ================= ACTION CHANGE ================= */
    actionTypeSelect.addEventListener('change', () => {
        handleActionVisibility();
        syncDocumentType();
    });

    /* ================= FORM SUBMIT + VALIDATION ================= */
    form.addEventListener('submit', function (e) {
    console.log('check');
    
        e.preventDefault();

        toastr.clear();
        statusInput.value = 'draft';

        // BASIC
        if (!subjectInput?.value.trim()) {
            toastr.error('Subject is required');
            return;
        }

        if (!noticeDateInput?.value) {
            toastr.error('Issued / Received date is required');
            return;
        }

        const action = actionTypeSelect.value;
        const mode = modeInput.value;

        // ORGANIZATION VALIDATION
        if (action.includes('received')) {
            const orgInput = document.querySelector('input[name="organization_name"]');
            if (!orgInput || !orgInput.value.trim()) {
                toastr.error('Organization name is required');
                return;
            }
        }

    

        // ATTACHMENT VALIDATION
        if (mode === 'attachment') {
           
            if (!attachmentInput || attachmentInput.files.length === 0) {
                toastr.error('Please upload a PDF file');
                return;
            }

            const pdf = attachmentInput.files[0];

            if (pdf.type !== 'application/pdf') {
                toastr.error('Only PDF files are allowed');
                return;
            }

            if (pdf.size > 5 * 1024 * 1024) {
                toastr.error('PDF must be less than 5MB');
                return;
            }
        }

        // SIGNATURE VALIDATION
        if (signatureInput && signatureInput.files.length > 0) {
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
        saveDraftBtn.disabled = true;

        // FINAL SUBMIT
        form.submit();
    });

    /* ================= INITIAL LOAD ================= */



});
</script>

<style>
    
    /* CARD BASE */
.doc-card-inner {
    border-radius: 12px;
    padding: 20px;
    border: 2px solid transparent;
    background-color: #0f172a; /* dark card */
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
}

/* HEADER */
.doc-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.doc-title {
    font-weight: 600;
    letter-spacing: 0.05em;
    font-size: 0.85rem;
    color: #e5e7eb;
}

.check-icon {
    font-size: 1rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

/* BODY */
.doc-card-body {
    margin-top: 10px;
}

.doc-main {
    font-size: 1.8rem;
    font-weight: 700;
    color: #ffffff;
}

.doc-sub {
    font-size: 0.85rem;
    color: #94a3b8;
    margin-top: 4px;
}

/* NOTICE THEME */
.notice-theme {
    background-color: #052e1f;
}

.peer:checked + .notice-theme {
    border-color: #3b82f6;
    background-color: #0e1a33;
}

.peer:checked + .notice-theme .check-icon {
    color: #3b82f6;
    opacity: 1;
}

/* CIRCULAR THEME */
.circular-theme {
    background-color: #0b1220;
}

.peer:checked + .circular-theme {
    border-color: #3b82f6;
    background-color: #0e1a33;
}

.peer:checked + .circular-theme .check-icon {
    color: #3b82f6;
    opacity: 1;
}

/* HOVER */
.doc-card-inner:hover {
    transform: translateY(-2px);
}


</style>
@endsection
