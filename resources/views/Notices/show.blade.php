@extends('layouts.layout')

@section('title', 'View Notice')

@section('content')
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

</div>

<div class="flex justify-center py-10 ">


    {{-- ================= ATTACHMENT MODE ================= --}}
    @if ($notice->mode === 'attachment' && $notice->Atch_Path)
        <iframe
            src="{{ asset('storage/'.$notice->Atch_Path) }}"
            class="w-[210mm] h-[297mm] border shadow bg-white">
        </iframe>

    {{-- ================= DRAFT MODE ================= --}}
    @else
      <div class="bg-white text-black w-[210mm] min-h-[297mm] p-12 shadow-lg">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center gap-1 mb-6">
        {{-- LOGO --}}
        <img
            src="https://partakedigital.in/assets/images/PARTAKE.png"
            alt="Organization Logo"
            class="w-15 h-15 object-contain">

        {{-- ORG DETAILS --}}
        <div class="text-center flex-1">
            <h1 class="text-xl font-bold uppercase">
                {{ $notice->Orga_Name }}
            </h1>

            <p class="text-sm mt-1">
                {{ $notice->Dept ?? '—' }}
            </p>

            <p class="text-xs mt-1">
                Static Address Line 1, City – 123456
            </p>
        </div>
    </div>

    <hr class="border-black mb-6">

    {{-- ================= META ================= --}}
    <div class="flex justify-between text-sm mb-6">
        <div>
            @if ($notice->Docu_Type=="notice")
                <strong>Ref:</strong> NOTICE/{{ $notice->Ntic_Crcl_UIN }}
            @elseif ($notice->Docu_Type=="circular")
                <strong>Ref:</strong> CIRCULAR/{{ $notice->Ntic_Crcl_UIN }}
            @endif
        </div>
        <div>
            <strong>Date:</strong>
            {{ \Carbon\Carbon::parse($notice->Ntic_Crcl_Dt)->format('d M Y') }}
        </div>
    </div>

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

        <p class="mt-3 font-semibold uppercase">
            {{ $notice->Subj }}
        </p>
    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="text-sm leading-7 text-justify">
        {!! $notice->Cntn !!}
    </div>

    {{-- ================= SIGNATORY ================= --}}
    <div class="mt-24 flex justify-between items-end">
        <div>
            <p class="text-sm font-semibold">
                Copy to:
            </p>
            <ul class="text-sm list-disc ml-4 mt-1">
                <li>All Departments</li>
                <li>Notice Board</li>
            </ul>
        </div>

        <div class="text-right">
            @if ($notice->Imgs_Sgnt)
                <img
                    src="{{ asset('storage/'.$notice->Imgs_Sgnt) }}"
                    alt="Signature"
                    class="h-25  mb-1 ml-auto object-contain">
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
    <div class="border-t mt-12 pt-4 text-center text-xs">
        <p>
            {{ $notice->Orga_Name }} |
            Static Address, City – 123456 |
            Email: info@example.com
        </p>
    </div>

</div>

    @endif

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmStatusChange(noticeId, status) {

    const isPublish = status === 'published';

    Swal.fire({
        title: isPublish ? 'Publish Notice?' : 'Move to Draft?',
        text: isPublish
            ? 'This  will become publicly visible.'
            : 'This  will be hidden from public view.',
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
