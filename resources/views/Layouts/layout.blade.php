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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- VITE (AFTER jQuery) -->
    <!-- Tailwind CSS (CDN) -->
 
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Page-specific styles -->
    @stack('styles')
</head>

<body class="font-sans antialiased bg-[#021420] text-gray-200 min-h-screen flex flex-col">

    {{-- Top Header --}}
    <header class="border-b border-[#123C55] bg-[#021420]">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">

            {{-- Brand --}}
            <div>
                <h1 class="text-lg font-semibold text-white tracking-wide">
                    D S Computer Center
                </h1>
                <p class="text-xs text-gray-400">
                    Notice Management System
                </p>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-4 text-sm">

                @auth
                    <div class="text-gray-300">
                        {{ auth()->user()->name }}
                    </div>
                @else
                    <span class="text-gray-400">Admin Panel</span>
                @endauth

                {{-- Hamburger (future sidebar) --}}
                <button class="text-gray-400 hover:text-white focus:outline-none">
                    ☰
                </button>
            </div>

        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-6 py-4">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-[#123C55] bg-[#021420]">
        <div class="max-w-7xl mx-auto px-4py-3 text-xs text-gray-500 text-center">
            © {{ date('Y') }} Notice Management System
        </div>
    </footer>

    <script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: "4000",
        extendedTimeOut: "1000",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };

    document.addEventListener('DOMContentLoaded', function () {
        const alert = document.getElementById('autoDismissAlert');

        if (alert) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';

                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 1000);
        }
    });
    </script>

    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>