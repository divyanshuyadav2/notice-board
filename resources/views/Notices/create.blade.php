@extends('layouts.layout')

@section('title', 'Create Notice')

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

@if (session('success'))
    <div class="bg-green-600 text-white p-4 rounded mb-6 alert-dismissible">
        {{ session('success') }}
    </div>
@endif
    {{-- PAGE HEADER --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('notices.index') }}" class="text-cyan-400 text-xl">←</a>
        <h2 class="text-2xl font-semibold text-white">Create</h2>
    </div>

    {{-- MODE SWITCH --}}
    <div class="flex gap-2 mb-6">
        <button id="draftBtn" class="mode-btn mode-active">Draft Mode</button>
        <button id="attachBtn" class="mode-btn">Attachment Mode</button>
    </div>

 {{-- FORM --}}
    <form method="POST" action="{{ route('notices.store') }}"  enctype="multipart/form-data" class="space-y-6">
        @csrf
               <div>
    <label class="card-title">Document Type</label>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- NOTICE --}}
        <label class="doc-card">
            <input
                type="radio"
                name="document_type"
                value="notice"
                checked
                class="hidden peer"
            >

            <div class="doc-card-inner notice-theme">
                <div class="doc-card-header">
                    <span class="doc-title">NOTICE</span>
                    <span class="check-icon">✓</span>
                </div>

               
            </div>
        </label>

        {{-- CIRCULAR --}}
        <label class="doc-card">
            <input
                type="radio"
                name="document_type"
                value="circular"
                class="hidden peer"
            >

            <div class="doc-card-inner circular-theme">
                <div class="doc-card-header">
                    <span class="doc-title">CIRCULAR</span>
                    <span class="check-icon">✓</span>
                </div>

             
            </div>
        </label>
    </div>
</div>


        {{-- IMPORTANT HIDDEN FIELDS --}}
        <input type="hidden" name="mode" id="modeInput" value="draft">
        <input type="hidden" name="status" id="statusInput" value="draft">

        {{-- NOTICE DETAILS --}}
        <div class="card">
            <h3 class="card-title">Notice Details</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Organization Name</label>
                    <input type="text" name="organization_name" class="input" required>
                </div>
                 <div>
                    <label class="label">Department</label>
                    <input type="text" name="department" class="input">
                </div>
                <div>
                    <label class="label">Subject</label>
                    <input type="text" name="subject" class="input" required>
                </div>
                <div>
                    <label class="label">Notice Date</label>
                    <input type="date" name="notice_date" class="input" required>
                </div>
                <div>
                    <label class="label">Effective Date</label>
                    <input type="date" name="effective_date" class="input" required>
                </div>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="card">
            <h3 class="card-title">Notice Content</h3>

            {{-- TEXT MODE --}}
            <div id="draftMode">
                <textarea id="editor" name="content"></textarea>
            </div>

            {{-- ATTACHMENT MODE --}}
            <div id="attachMode" class="hidden">
                <label class="label">Upload PDF</label>
                <input type="file"
                       name="attachment"
                       accept="application/pdf"
                       class="input">
            </div>
        </div>

        {{-- SIGNATURE --}}
        <div class="card" id="signatorySection">
            <h3 class="card-title">Authorized Signatory</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Signature Image</label>
                    <input type="file" name="signature_image" accept="image/*" class="input">
                </div>

                <div>
                    <label class="label">Authorized Person Name</label>
                    <input type="text" name="authorized_person_name" class="input">
                </div>

                <div>
                    <label class="label">Designation</label>
                    <input type="text" name="designation" class="input">
                </div>

               
            </div>
        </div>


        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">
            <button type="button" class="btn-secondary" id="saveDraftBtn">
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
                    'Arial, Helvetica, sans-serif',
                    'Times New Roman, Times, serif',
                    'Georgia, serif',
                    'Courier New, Courier, monospace'
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
    const form = document.querySelector('form');

    if (!form) return;

    /* ================= MODE TOGGLE ================= */
    if (draftBtn && attachBtn) {
        draftBtn.addEventListener('click', () => {
            modeInput.value = 'draft';

            draftBtn.classList.add('mode-active');
            attachBtn.classList.remove('mode-active');

            draftMode.classList.remove('hidden');
            attachMode.classList.add('hidden');

            if (signatorySection) signatorySection.classList.remove('hidden');
        });

        attachBtn.addEventListener('click', () => {
            modeInput.value = 'attachment';

            attachBtn.classList.add('mode-active');
            draftBtn.classList.remove('mode-active');

            attachMode.classList.remove('hidden');
            draftMode.classList.add('hidden');

            if (signatorySection) signatorySection.classList.add('hidden');
        });
    }

    /* ================= SAVE AS DRAFT ================= */
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function () {
            statusInput.value = 'draft';

            // Prevent double submit
            saveDraftBtn.disabled = true;

            form.submit();
        });
    }

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
