<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Notice Management System')</title>

    <!-- jQuery MUST come first -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>

    <!-- DataTables -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet"
          href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/super-build/ckeditor.js"></script>

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- VITE (AFTER jQuery) -->
    <!-- Tailwind CSS (CDN) -->
 
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Page-specific styles -->
    @stack('styles')
    <style>
    .blur-background {
        filter: blur(4px);
        transition: filter 0.2s ease;
    }
    .no-scroll {
        overflow: hidden;
    }
    </style>

</head>

<body class="font-sans antialiased bg-[#021420] text-gray-200 min-h-screen flex flex-col">

    <header class="border-b border-[#123C55] bg-[#021420] sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

        {{-- LEFT --}}
        <div class="flex flex-col leading-tight">
            <h1 class="text-lg font-semibold text-white">
                {{ session('org_name') }}
            </h1>

          
                <span class="text-xs text-gray-400">
                     {{ session('user_data.User_Name') }}
                </span>
           
        </div>

        {{-- RIGHT MENU BUTTON --}}
        <div class="relative">
            <button
                id="menuToggle"
                class="w-10 h-10 flex items-center justify-center text-white hover:bg-[#0c2438]"
                aria-label="Menu"
            >
                <i class="bi bi-list text-xl"></i>
            </button>
<!-- Overlay -->
<div id="menuOverlay" 
    class="hidden fixed inset-0 bg-black/80 z-[9998]">
</div>

<!-- Dropdown -->
<div id="menuDropdown"
    class="hidden fixed top-16 right-6 w-64
        shadow-2xl
        z-[9999]
        rounded-xl
        overflow-hidden
        "
    style="background-color: #0d2942;">

    <button
        type="button"
        id="openOrgModal"
        class="flex items-center gap-3 px-4 py-3.5 text-gray-200
            hover:bg-[#1a3d5c] transition cursor-pointer"
        style="background-color: #0d2942;">
        <i class="bi bi-arrow-repeat text-blue-400 text-lg"></i>
        <span>Change</span>
    </button>



    <!-- Back -->
    <a href="https://partakers.in/dashboard"
        id="menuBack"
        type="button"
        class="w-full flex items-center gap-3 px-4 py-3.5 text-left
               text-gray-200 hover:bg-[#1a3d5c] transition "
        style="background-color: #0d2942;">
        <i class="bi bi-arrow-left text-lg"></i>
        <span>Back</span>
    </a>

    <!-- Exit -->
    <form method="POST" >
        @csrf
        <button
            type="submit"
            class="w-full flex items-center gap-3 px-4 py-3.5 text-left
                   text-red-400 hover:bg-red-900/40 transition"
            style="background-color: #0d2942;">
            <i class="bi bi-box-arrow-right text-lg"></i>
            <span>Exit</span>
        </button>
    </form>
</div>
        </div>
    </div>
</header>






    {{-- Main Content --}}
    <main class="flex-1" id="pageContent">
        <div class="max-w-7xl mx-auto px-8 py-4">
            @yield('content')
        </div>
    </main>

    {{-- Footer
    <footer class="border-t border-[#123C55] bg-[#021420]">
        <div class="max-w-7xl mx-auto px-4py-3 text-xs text-gray-500 text-center">
            © {{ date('Y') }} Notice Management System
        </div>
    </footer>
     --}}
<!-- ================= SWITCH ORGANIZATION MODAL ================= -->
<div id="orgModal"
     class="fixed inset-0 z-50 hidden items-center justify-center">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         id="closeOrgModal"></div>

    <!-- Modal Box -->
    <div class="relative   rounded-xl bg-[#1b2b3f]
                border border-[#294a63] shadow-xl p-6" style="width:40%; --tw-bg-opacity: 1; background-color: rgb(30 41 59 / var(--tw-bg-opacity, 1));
">

        <h3 class="text-lg font-semibold text-white mb-4">
            Select Organization
        </h3>

        @if(isset($organizations) && $organizations->isNotEmpty())
         <form action="{{ route('organization.store') }}" method="GET" id="orgSwitchForm">

                @csrf

                @foreach($organizations as $org)
                    <label class="block p-4 border rounded mb-2 cursor-pointer">
                        <input type="radio"
                            name="organization_uin"
                            value="{{ $org->Orga_UIN }}"
                            {{ session('organization_uin') == $org->Orga_UIN ? 'checked' : '' }}>
                        {{ $org->Orga_Name }}
                    </label>
                @endforeach

                <button type="submit" class="btn-primary mt-4">Switch</button>

            </form>
        @endif


    </div>
</div>

   <script>

    toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: "4000",
    extendedTimeOut: "1000",
    preventDuplicates: true,
    newestOnTop: true,
    };
        document.addEventListener('DOMContentLoaded', () => {
           // Get elements
const menuBtn = document.getElementById('menuToggle'); // your hamburger button
const menuDropdown = document.getElementById('menuDropdown');
const menuOverlay = document.getElementById('menuOverlay');
const menuBack = document.getElementById('menuBack');

// Toggle menu
menuBtn.addEventListener('click', () => {
    menuDropdown.classList.toggle('hidden');
    menuOverlay.classList.toggle('hidden');
});

// Close on overlay click
menuOverlay.addEventListener('click', () => {
    menuDropdown.classList.add('hidden');
    menuOverlay.classList.add('hidden');
});

// Close on back button
menuBack.addEventListener('click', () => {
    menuDropdown.classList.add('hidden');
    menuOverlay.classList.add('hidden');
});
        

    const modal = document.getElementById('orgModal');
    const openBtn = document.getElementById('openOrgModal');
    const closeBtn = document.getElementById('closeOrgModal');
    const form = document.getElementById('orgSwitchForm');
    const pageContent = document.getElementById('pageContent');
    /* ================= OPEN MODAL ================= */
    openBtn?.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        menuDropdown.classList.add('hidden');
        // blur background + stop scroll
         pageContent.classList.add('blur-background');
         document.body.classList.add('no-scroll');
    });

    /* ================= CLOSE MODAL ================= */
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
         // remove blur + restore scroll
       pageContent.classList.remove('blur-background');
      document.body.classList.remove('no-scroll');
    }

    closeBtn?.addEventListener('click', closeModal);

    // Click outside modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });



    
   

});
</script>





    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>