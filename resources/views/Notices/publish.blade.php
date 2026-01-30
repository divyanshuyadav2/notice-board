@extends('layouts.layout')

@section('title', 'Publish Doc')

@section('content')


<div class="flex justify-center py-10">
    {{-- ================= ATTACHMENT MODE ================= --}}
    @if ($notice->mode === 'attachment' && $notice->Atch_Path)
        <iframe
            src="{{ asset('storage/'.$notice->Atch_Path) }}"
            class="w-[210mm] h-[297mm] border shadow bg-white">
        </iframe>

    {{-- ================= DRAFT MODE ================= --}}
    @else
        <div id="notice-container">
            <!-- Pages will be generated here dynamically -->
            <div class="notice-page bg-white text-black w-[210mm] h-[297mm] p-12 shadow-lg mb-4 relative overflow-hidden" style="page-break-after: always;">
                
                {{-- ================= HEADER ================= --}}
                <div class="flex items-center gap-1 mb-6">
                    <div class="text-center flex-1">
                        <h1 class="text-xl font-bold uppercase">
                            {{ $notice->Orga_Name }}
                        </h1>

                        <p class="text-sm mt-1">
                            {{ $notice->Dept ?? '—' }}
                        </p>
                    </div>
                </div>

                <hr class="border-black mb-6">

                {{-- ================= TITLE ================= --}}
                <div class="text-center mb-6">
                    @if ($notice->Docu_Type=="notice")
                        <h2 class="font-bold underline text-lg">
                            NOTICE
                        </h2>
                    @elseif ($notice->Docu_Type =="circular")
                        <h2 class="font-bold underline text-lg">
                            CIRCULAR
                        </h2>
                    @endif
                </div>

                {{-- ================= META ================= --}}
                <div class="flex justify-between text-sm mb-2">
                    <div>
                        <div>
                            <strong>Date:</strong>
                            {{ \Carbon\Carbon::parse($notice->Ntic_Crcl_Dt)->format('d M Y') }}
                        </div>
                    </div>
                    <div>
                        <div>
                            <strong>Date:</strong>
                            {{ \Carbon\Carbon::parse($notice->Eft_Dt)->format('d M Y') }}
                        </div>
                        <div>        
                            <strong>Ref_No: </strong>{{ $notice->Ref_No }}
                        </div>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <p class="mt-3 font-semibold uppercase">
                        {{ $notice->Subj }}
                    </p>
                </div>

                {{-- ================= CONTENT ================= --}}
                <div id="notice-content" class="text-sm leading-7 text-justify">
                    {!! $notice->Cntn !!}
                </div>

                {{-- ================= SIGNATORY (Will be moved to last page) ================= --}}
                <div id="signatory-section" class="absolute bottom-12 left-12 right-12 hidden">
                    <div class="mt-24 flex">
                        <div>
                            @if ($notice->Imgs_Sgnt)
                                <img
                                    src="{{ asset('storage/'.$notice->Imgs_Sgnt) }}"
                                    alt="Signature"
                                    class="h-25 mb-1 ml-auto object-contain">
                            @endif

                            <p class="font-semibold">
                                {{ $notice->Athr_Pers_Name }}
                            </p>
                            <p class="text-sm">
                                {{ $notice->Dsig }}
                            </p>
                            <p class="text-sm">
                                {{ $notice->Dept }}
                            </p>
                        </div>
                    </div>

                    {{-- ================= FOOTER ================= --}}
                    <div class="mt-12 pt-4 text-center text-xs">
                        <hr class="border-black mb-6">
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<div class="flex justify-end mb-6 gap-3">
    @if ($notice->Stau === 'draft')
        <button
            onclick="confirmStatusChange({{ $notice->Ntic_Crcl_UIN }}, 'published')"
            class="bg-green-600 hover:bg-green-700 text-white
                   px-5 py-2 rounded font-semibold shadow">
            Publish 
        </button>
    @else
        <button
            onclick="confirmStatusChange({{ $notice->Ntic_Crcl_UIN }}, 'draft')"
            class="bg-yellow-500 hover:bg-yellow-600 text-black
                   px-5 py-2 rounded font-semibold shadow">
            Move to Draft
        </button>
    @endif
     @if ($notice->Stau === 'draft')
  <a href="{{ route('notices.edit', $notice->Ntic_Crcl_UIN) }}"
    class="bg-blue-600 hover:bg-blue-700 text-white
            px-5 py-2 rounded font-semibold shadow">
        Edit
    </a>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
@media print {
    .notice-page {
        page-break-after: always;
        box-shadow: none !important;
        margin: 0 !important;
    }
    
    .notice-page:last-child {
        page-break-after: auto;
    }
    
    @page {
        size: A4;
        margin: 0;
    }
}

.notice-page {
    box-sizing: border-box;
}
</style>

<script>
// Paginate content on page load
document.addEventListener('DOMContentLoaded', function() {
    paginateContent();
});

function paginateContent() {
    const container = document.getElementById('notice-container');
    if (!container) return;
    
    const firstPage = container.querySelector('.notice-page');
    const content = document.getElementById('notice-content');
    const signatorySection = document.getElementById('signatory-section');
    
    if (!content) return;
    
    // Get all content elements
    const contentElements = Array.from(content.children);
    
    // Page height calculation (A4 height - padding - header space)
    const pageHeight = 297; // mm
    const mmToPx = 3.7795275591; // conversion factor
    const availableHeight = (pageHeight - 100) * mmToPx; // Reserve space for header/footer
    
    let currentPage = firstPage;
    let currentHeight = 0;
    let pageContent = currentPage.querySelector('#notice-content');
    pageContent.innerHTML = '';
    
    contentElements.forEach((element, index) => {
        const clone = element.cloneNode(true);
        pageContent.appendChild(clone);
        
        const elementHeight = clone.offsetHeight;
        currentHeight += elementHeight;
        
        // Check if we need a new page
        if (currentHeight > availableHeight && contentElements.length > index + 1) {
            // Create new page
            const newPage = createNewPage();
            container.appendChild(newPage);
            currentPage = newPage;
            pageContent = currentPage.querySelector('.page-content');
            currentHeight = elementHeight;
            
            // Move current element to new page
            pageContent.appendChild(clone);
            const prevPageContent = currentPage.previousElementSibling.querySelector('#notice-content, .page-content');
            prevPageContent.removeChild(prevPageContent.lastChild);
        }
    });
    
    // Add signatory to last page
    const lastPage = container.querySelector('.notice-page:last-child');
    const lastPageSignatory = lastPage.querySelector('#signatory-section');
    if (lastPageSignatory) {
        lastPageSignatory.classList.remove('hidden');
    }
}

function createNewPage() {
    const page = document.createElement('div');
    page.className = 'notice-page bg-white text-black w-[210mm] h-[297mm] p-12 shadow-lg mb-4 relative overflow-hidden';
    page.style.pageBreakAfter = 'always';
    
    page.innerHTML = `
        {{-- Continued header --}}
        <div class="text-right text-sm text-gray-600 mb-4">
            <em>(Continued...)</em>
        </div>
        
        <div class="page-content text-sm leading-7 text-justify"></div>
        
        {{-- Signatory section for last page --}}
        <div id="signatory-section" class="absolute bottom-12 left-12 right-12 hidden">
            <div class="mt-24 flex">
                <div>
                    @if ($notice->Imgs_Sgnt)
                        <img src="{{ asset('storage/'.$notice->Imgs_Sgnt) }}" 
                             alt="Signature"
                             class="h-25 mb-1 ml-auto object-contain">
                    @endif
                    <p class="font-semibold">{{ $notice->Athr_Pers_Name }}</p>
                    <p class="text-sm">{{ $notice->Dsig }}</p>
                    <p class="text-sm">{{ $notice->Dept }}</p>
                </div>
            </div>
            <div class="mt-12 pt-4 text-center text-xs">
                <hr class="border-black mb-6">
            </div>
        </div>
    `;
    
    return page;
}

function confirmStatusChange(noticeId, status) {
    const isPublish = status === 'published';

    Swal.fire({
        title: isPublish ? 'Publish Document?' : 'Move to Draft?',
        text: isPublish
            ? 'This will become publicly visible.'
            : 'This will be hidden from public view.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isPublish ? '#16a34a' : '#eab308',
        cancelButtonColor: '#6b7280',
        confirmButtonText: isPublish ? 'Yes, Publish' : 'Yes, Move to Draft'
    }).then((result) => {
        if (result.isConfirmed) {
            updateNoticeStatus(noticeId, status);
        }
    });
}

function updateNoticeStatus(noticeId, status) {
    fetch(`/notices/${noticeId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Error', data.message ?? 'Something went wrong', 'error');
        }
    })
    .catch(() => {
        Swal.fire('Error', 'Server error occurred', 'error');
    });
}
</script>
@endsection