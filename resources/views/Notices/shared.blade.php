<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shared Document</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Tailwind (same theme colors assumed) -->
    @vite(['resources/css/app.css'])

    <style>
        @media print {
            body { 
                background: white !important; 
                margin: 0;
                padding: 0;
            }
            .no-print { 
                display: none !important; 
            }
            
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
</head>

<body class="bg-[#021420] text-gray-200 min-h-screen">

<!-- ================= TOP BAR ================= -->
<div class="no-print flex justify-between items-center px-6 py-4 border-b border-[#123C55] bg-[#021420]">
    <div class="text-sm text-gray-400">
        Shared Document
    </div>
</div>

<!-- ================= CONTENT ================= -->
<div class="flex justify-center py-10">

    {{-- ========== ATTACHMENT MODE ========== --}}
    @if ($notice->mode === 'attachment' && $notice->Atch_Path)
        <iframe
            src="{{ asset('storage/'.$notice->Atch_Path) }}"
            class="w-[210mm] h-[297mm] border shadow bg-white">
        </iframe>

    {{-- ========== DRAFT MODE ========== --}}
    @else
        <div id="notice-container">
            <!-- Pages will be generated here dynamically -->
            <div class="notice-page bg-white text-black w-[210mm] h-[297mm] p-12 shadow-lg mb-4 relative overflow-hidden" style="page-break-after: always;">
                
                {{-- HEADER --}}
                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold uppercase">
                        {{ $notice->Orga_Name }}
                    </h1>

                    <p class="text-sm">
                        {{ $notice->Dept ?? '—' }}
                    </p>
                </div>

                <hr class="border-black mb-6">

                {{-- TITLE --}}
                <div class="text-center mb-6">
                    <h2 class="font-bold underline text-lg uppercase">
                        {{ strtoupper($notice->Docu_Type) }}
                    </h2>
                </div>

                {{-- META --}}
                <div class="flex justify-between text-sm mb-6">
                    <div>
                        <strong>Date:</strong>
                        {{ \Carbon\Carbon::parse($notice->Ntic_Crcl_Dt)->format('d M Y') }}
                    </div>

                    <div class="text-right">
                        @if ($notice->Eft_Dt)
                            <div>
                                <strong>Effective:</strong>
                                {{ \Carbon\Carbon::parse($notice->Eft_Dt)->format('d M Y') }}
                            </div>
                        @endif

                        @if ($notice->Ref_No)
                            <div>
                                <strong>Ref No:</strong>
                                {{ $notice->Ref_No }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SUBJECT --}}
                <div class="text-center mb-6">
                    <p class="font-semibold uppercase">
                        {{ $notice->Subj }}
                    </p>
                </div>

                {{-- CONTENT --}}
                <div id="notice-content" class="text-sm leading-7 text-justify">
                    {!! $notice->Cntn !!}
                </div>

                {{-- SIGNATORY (Will be moved to last page) --}}
                <div id="signatory-section" class="absolute bottom-12 left-12 right-12 hidden">
                    <div class="mt-8">
                        @if ($notice->Imgs_Sgnt)
                            <img src="{{ asset('storage/'.$notice->Imgs_Sgnt) }}"
                                 class="h-20 mb-2 object-contain">
                        @endif

                        <p class="font-semibold">{{ $notice->Athr_Pers_Name }}</p>
                        <p class="text-sm">{{ $notice->Dsig }}</p>
                        <p class="text-sm">{{ $notice->Dept }}</p>
                    </div>

                    <hr class="border-black mt-12">

                    {{-- FOOTER --}}
                    <div class="text-center text-xs mt-4">
                        This document is shared digitally and is system-generated.
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- ================= ACCESS INFO ================= -->
<div class="no-print text-center text-xs text-gray-500 pb-6">
    Link valid until:
    {{ \Carbon\Carbon::parse($share->Expires_At)->format('d M Y, h:i A') }} |
    Views: {{ $share->Access_Count }}
</div>

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
            <div class="mt-8">
                @if ($notice->Imgs_Sgnt)
                    <img src="{{ asset('storage/'.$notice->Imgs_Sgnt) }}"
                         class="h-20 mb-2 object-contain">
                @endif

                <p class="font-semibold">{{ $notice->Athr_Pers_Name }}</p>
                <p class="text-sm">{{ $notice->Dsig }}</p>
                <p class="text-sm">{{ $notice->Dept }}</p>
            </div>

            <hr class="border-black mt-12">

            <div class="text-center text-xs mt-4">
                This document is shared digitally and is system-generated.
            </div>
        </div>
    `;
    
    return page;
}
</script>

</body>
</html>