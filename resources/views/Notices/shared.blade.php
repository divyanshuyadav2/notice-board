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
            body { background: white !important; }
            .no-print { display: none !important; }
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
        <div class="bg-white text-black w-[210mm] min-h-[297mm] p-12 shadow-lg">

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
            <div class="text-sm leading-7 text-justify">
                {!! $notice->Cntn !!}
            </div>

            {{-- SIGNATORY --}}
            <div class="mt-24">
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
    @endif
</div>

<!-- ================= ACCESS INFO ================= -->
<div class="no-print text-center text-xs text-gray-500 pb-6">
    Link valid until:
    {{ \Carbon\Carbon::parse($share->Expires_At)->format('d M Y, h:i A') }} |
    Views: {{ $share->Access_Count }}
</div>

</body>
</html>
