@extends('layouts.layout')

@section('title', 'Notices')

@section('content')
<div class="text-gray-200">

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div id="successAlert"
             class="bg-green-600 text-white px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-white">Notices/Circular</h2>
          
        </div>

        {{-- ADD BUTTON --}}
        <a href="{{ route('notices.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700
                  text-white font-semibold px-4 py-2 rounded transition">
            <i class="bi bi-plus-circle"></i>
            <span>Add </span>
        </a>
    </div>

    {{-- DATATABLE CONTAINER --}}
    <div class="bg-[#021420] rounded-lg overflow-hidden">
        
        {{-- SEARCH ROW (Full Width) --}}
       <div id="searchRow" class="px-4 py-2 bg-[#021420] border-b border-[#1a3647]">
            <!-- DataTables will inject search box here -->
        </div>


        {{-- SHOW ENTRIES + PAGINATION ROW --}}
        <div id="topControlsRow" class="px-4 py-3 bg-[#021420] border-b border-[#1a3647] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- DataTables will inject show entries and pagination here -->
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table id="noticesTable" class="w-full text-sm stripe hover">
                <thead class="bg-[#0B2A3C] text-cyan-300 uppercase text-xs">
                    <tr>
                        
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Authorised Person</th>
                        <th class="px-4 py-3">Organization Name</th>
                        <th class="px-4 py-3">Effective /Published Date</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate rows here -->
                </tbody>
            </table>
        </div>

        {{-- BOTTOM ROW (Show entries + Pagination) --}}
        <div id="bottomRow" class="p-4 bg-[#0a1e2e] border-t border-[#1a3647]">
            <!-- DataTables info will go here -->
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
/* =========================================================
   DATATABLE – MODERN DARK ADMIN UI (FINAL MATCH)
   ========================================================= */

.dataTables_wrapper {
    color: #e5f1f8;
    position: relative;
}

/* ---------------------------------------------------------
   HIDE DEFAULT DATATABLE CONTROLS
--------------------------------------------------------- */
.dataTables_length,
.dataTables_filter,
.dataTables_info,
.dataTables_paginate {
    display: none !important;
}

/* ---------------------------------------------------------
   SEARCH BAR (TOP)
--------------------------------------------------------- */
#searchRow {
    display: flex;
    align-items: center;
    gap: 1rem;
}

#searchRow .search-container {
    flex: 1;
    position: relative;
}

#searchRow .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #7fa3b8;
    font-size: 1.05rem;
    pointer-events: none;
}

#searchRow input[type="search"] {
    width: 100%;
    height: 35px;
    background: #1b3444 !important;
    border: 1px solid #284e63 !important;
    color: #e5f1f8 !important;
    padding: 0 1rem 0 3rem !important;
    border-radius: 8px !important;
    font-size: 0.95rem;
    outline: none;
    transition: all 0.2s ease;
}

#searchRow input::placeholder {
    color: #7fa3b8;
}

#searchRow input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}

/* FILTER BUTTON */
#searchRow .filter-btn {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    background: #1b3444;
    border: 1px solid #284e63;
    color: #9fb8c8;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .2s;
}

#searchRow .filter-btn:hover {
    background: #25485c;
    color: #ffffff;
}

/* ---------------------------------------------------------
   TOP CONTROLS (SHOW + PAGINATION)
--------------------------------------------------------- */
#topControlsRow {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
}

#topControlsRow .length-container {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.85rem;
    color: #9fb8c8;
}

#topControlsRow select {
    height:35px;
    min-width: 72px;
    background: #0f2a3a !important;
    border: 1px solid #2a556b !important;
    border-radius: 10px !important;
    color: #e5f1f8 !important;
    font-size: 0.85rem;
    padding: 0 2rem 0 0.75rem !important;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%239fb8c8'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.6rem center;
    background-size: 1.2rem;
}

/* ---------------------------------------------------------
   PAGINATION
--------------------------------------------------------- */
.pagination-container {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.paginate_button {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #2a556b;
    background: transparent;
    color: #9fb8c8;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all .15s;
}

.paginate_button:hover:not(.current):not(.disabled) {
    background: #25485c;
    color: #ffffff;
}

.paginate_button.current {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 0 0 2px rgba(37,99,235,.25);
}

.paginate_button.disabled {
    opacity: .4;
    cursor: not-allowed;
}

/* ---------------------------------------------------------
   TABLE
--------------------------------------------------------- */
table.dataTable {
    width: 100%;
    border-collapse: collapse;
}

table.dataTable thead th {
    background: linear-gradient(180deg,#0e2a3a,#0b2230);
    color: #4fd1e8;
    font-size: 0.72rem;
    letter-spacing: .06em;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #1f4256;
    text-transform: uppercase;
}

table.dataTable tbody tr {
    border-bottom: 1px solid #173647;
    transition: background-color .15s ease;
}

table.dataTable tbody tr:hover {
    background: #0b2a3c;
}

table.dataTable tbody td {
    padding: 0.85rem 1rem;
    vertical-align: middle;
    font-size: 0.9rem;
    color: #e5f1f8;
}

/* ---------------------------------------------------------
   AVATAR
--------------------------------------------------------- */
.ui-avatar {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    background: linear-gradient(135deg,#22c1dc,#2b8cff);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    color: #001b2e;
}

/* ---------------------------------------------------------
   STATUS BADGE
--------------------------------------------------------- */
.badge-draft {
    background: #c98a14;
    color: #1b1300;
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
    font-weight: 600;
}

/* ---------------------------------------------------------
   INFO ROW
--------------------------------------------------------- */
#customInfo {
    font-size: 0.85rem;
    color: #9fb8c8;
}

/* ---------------------------------------------------------
   PROCESSING OVERLAY
--------------------------------------------------------- */
.dataTables_processing {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(6,24,38,.85);
    color: #9fb8c8;
    font-size: .9rem;
    border-radius: 12px;
}

/* ---------------------------------------------------------
   RESPONSIVE
--------------------------------------------------------- */
@media (max-width: 768px) {
    #searchRow {
        flex-direction: column;
    }

    .pagination-container {
        justify-content: center;
    }

    table.dataTable thead {
        display: none;
    }
}
@media (max-width: 480px) {
    #topControlsRow {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    console.log("DataTable initializing...");
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
      
    const table = $('#noticesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        pageLength: 100,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],

        ajax: {
            url: "{{ route('notices.index') }}",
            type: "GET",
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', xhr.responseText);
                toastr.error('Failed to load data. Check console for details.');
            }
        },

      columns: [
              

                {
                    data: 'title',                 
                    name: 'Subj',                  
                    orderable: false,
                    searchable: true
                },

                {
                    data: 'Athr_Pers_Name',             
                    searchable: true
                },

                {
                    data: 'Orga_Name',         // Badge (Notice / Circular)
                    name: 'Orga_Name',
                    orderable: false,
                    searchable: true
                },

                {
                    data: 'date',                  // Formatted date
                    name: 'Ntic_Crcl_Dt'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-right'
                }
            ],

        order: [[4, 'desc']],

        // Remove default DOM elements, we'll place them manually
        dom: 'rtip',

        language: {
            search: "",
            searchPlaceholder: "Search Notices/circulars...",
            lengthMenu: "Show _MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "‹",
                next: "›"
            },
            processing: "Loading..."
        },

        initComplete: function() {
            // Create search box in top row
            const searchHtml = `
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="search" 
                           id="customSearch" 
                           placeholder="Search notices/circulars..." 
                           aria-label="Search">
                </div>
               
            `;
            $('#searchRow').html(searchHtml);

            // Create top controls row (Show + Pagination)
            const lengthHtml = `
                <div class="length-container">
                    <label>
                        <span>Show</span>
                        <select id="customLength">
                            <option value="50" selected>50</option>
                            <option value="100" >100</option>
                             <option value="200" >200</option>
                        </select>
                    </label>
                </div>
            `;
            
            const paginationHtml = `<div class="pagination-container" id="customPagination"></div>`;
            
            $('#topControlsRow').html(lengthHtml + paginationHtml);

            // Create bottom info row
            const info = table.page.info();
            const infoHtml = `
                <div class="info-container" id="customInfo">
                    Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} entries
                </div>
            `;
            $('#bottomRow').html(infoHtml);

            // Bind custom search
            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Bind custom length
            $('#customLength').on('change', function() {
                table.page.len(parseInt(this.value)).draw();
            });

            // Build custom pagination
            buildPagination();
        },

        drawCallback: function() {
            buildPagination();
            updateInfo();
        }
    });

    function buildPagination() {
        const info = table.page.info();
        const currentPage = info.page;
        const totalPages = info.pages;
        
        let paginationHtml = '';

        // Previous button
        paginationHtml += `<button class="paginate_button ${currentPage === 0 ? 'disabled' : ''}" data-page="${currentPage - 1}">‹</button>`;

        // Page numbers (show current, +/- 2 pages)
        let startPage = Math.max(0, currentPage - 2);
        let endPage = Math.min(totalPages - 1, currentPage + 2);

        if (startPage > 0) {
            paginationHtml += `<button class="paginate_button" data-page="0">1</button>`;
            if (startPage > 1) {
                paginationHtml += `<span class="paginate_button disabled">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<button class="paginate_button ${i === currentPage ? 'current' : ''}" data-page="${i}">${i + 1}</button>`;
        }

        if (endPage < totalPages - 1) {
            if (endPage < totalPages - 2) {
                paginationHtml += `<span class="paginate_button disabled">...</span>`;
            }
            paginationHtml += `<button class="paginate_button" data-page="${totalPages - 1}">${totalPages}</button>`;
        }

        // Next button
        paginationHtml += `<button class="paginate_button ${currentPage === totalPages - 1 ? 'disabled' : ''}" data-page="${currentPage + 1}">›</button>`;

        $('#customPagination').html(paginationHtml);

        // Bind pagination clicks
        $('#customPagination .paginate_button:not(.disabled):not(.current)').on('click', function() {
            const page = parseInt($(this).data('page'));
            if (!isNaN(page)) {
                table.page(page).draw('page');
            }
        });
    }

    function updateInfo() {
        const info = table.page.info();
        const infoText = info.recordsTotal > 0 
            ? `Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} entries`
            : 'No entries to show';
        $('#customInfo').text(infoText);
    }

    console.log("DataTable initialized successfully");

    // Auto hide success alert
    setTimeout(() => {
        $('#successAlert').fadeOut();
    }, 2000);
});
document.addEventListener('click', function (e) {

    // Close all dropdowns first
    document.querySelectorAll('.action-menu').forEach(menu => {
        menu.classList.add('hidden');
    });

    // Toggle clicked one
    const btn = e.target.closest('.action-btn');
    if (btn) {
        e.preventDefault();

        const wrapper = btn.closest('div');
        const menu = wrapper.querySelector('.action-menu');

        menu.classList.toggle('hidden');
    }
});
$(document).on('click', '#noticesTable tbody tr', function (e) {
    // prevent click when clicking action menu or button
    if ($(e.target).closest('.action-btn, .action-menu, a').length) {
        return;
    }

    let url = $(this).data('href');
    if (url) {
        window.location.href = url;
    }
});
function generateShare(id) {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    fetch(`/notices/${id}/share`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Request failed');
        return res.json();
    })
    .then(data => {
        navigator.clipboard.writeText(data.share_url);
        toastr.success('Share link copied (valid for 30 days)');
    })
    .catch(() => {
        toastr.error('Failed to generate share link');
    });
}



</script>
@endpush