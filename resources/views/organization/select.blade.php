<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Notice Management System')</title>

    <!-- jQuery MUST come first -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

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
</head>

<body class="font-sans antialiased bg-[#021420]">
    <style>

    #organization-select {
    background-color: #334155;
    border: 1px solid #475569;
    color: #f1f5f9;
    font-size: 1.125rem;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;

    width: 448px;          /* ✅ FIXED WIDTH */
    max-width: 100%;

    margin: 40px auto;        /* ✅ CENTER */
    display: block;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

        
        #organization-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }
        
        #organization-select option {
            background-color: #334155;
            color: #f1f5f9;
            padding: 0.5rem;
        }
        
        #organization-select option:first-child {
            color: #94a3b8;
            text-align: center;
        }
    </style>
    
    <main>
        <div class="min-h-screen flex justify-center bg-[#021420] pt-32 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div class="space-y-4">
                    <div class="w-full">
                        <select id="organization-select">
                            <option value="">-- Select Organization --</option>
                           @foreach($organizations as $org)
                                <option value="{{ $org->Orga_UIN }}">
                                    {{ $org->Orga_Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </main>

 <script>
    $(document).ready(function() {
        $('#organization-select').on('change', function() {
            var orgUin = $(this).val();
            
            console.log('Selected Organization:', orgUin); // Debug
            
            if (orgUin) {
                // Simple redirect to test
                var url = '{{ route("organization.store") }}?organization_uin=' + orgUin;
                console.log('Redirecting to:', url); // Debug
                window.location.href = url;
            }
        });
    });
</script> 
</body>
</html>