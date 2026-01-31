<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use App\Models\AdmnTranNticCrcl;
use Illuminate\Support\Str;

class NoticeController extends Controller
{
    /**
     * Show all notices
     */
    



        public function index(Request $request)
        {
            if ($request->ajax()) {

                $query = AdmnTranNticCrcl::select([
                    'Ntic_Crcl_UIN',
                    'Subj',
                    'Orga_Name',
                    'Stau',
                    'Ntic_Crcl_Dt',
                    'Mode',
                    'Docu_Type',
                    'Eft_Dt',
                    'Dept',
                    'Athr_Pers_Name',
                    'Pbli_On',
                    'Ref_No'
                ])->where('Orga_UIN', session('organization_uin'));

                return DataTables::of($query)
                    ->addIndexColumn()

                    /* ================= TITLE COLUMN ================= */
                    ->addColumn('title', function ($row) {

                        $initial = strtoupper(substr($row->Docu_Type, 0, 1));
                        $bgclass = $row->Docu_Type === 'circular' ? 'bg-green-600' : 'bg-yellow-500';
                        
                        // Truncate subject for mobile view
                        $originalSubject = $row->Subj;
                        $truncatedSubject = strlen($originalSubject) > 40
                            ? substr($originalSubject, 0, 40) . '...' 
                            : $originalSubject;
                        
                        // Draft / Published dot
                        $isPublished = $row->Stau === 'published';
                        if ($isPublished) {
                            $subjectHtml = '<a href="'.route('notices.show', $row->Ntic_Crcl_UIN).'">  
                                            <span class="text-white font-medium" title="'.$originalSubject.'">
                                                '.$truncatedSubject.'
                                            </span></a>';
                        } else {
                            $subjectHtml = '<span class="text-white font-medium" title="'.$originalSubject.'">
                                                '.$truncatedSubject.'
                                            </span>';
                        }
                        
                        $statusIcon = $row->Stau === 'draft'
                            ? '<i class="bi bi-pencil-square text-white-400 text-sm" title="Draft"></i>'
                            : '';

                        return '
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full '.$bgclass.'
                                            flex items-center justify-center
                                            text-white font-bold">
                                    '.$initial.'
                                </div>

                                <div>
                                <div class="flex items-center gap-2">
                                        '.$subjectHtml.'
                                        '.$statusIcon.'
                                    </div>

                                    <div class="flex items-center gap-3 text-xs text-gray-400">
                                        <span>Ref_No. '.$row->Ref_No.'</span>
                                    </div>
                                </div>
                            </div>
                        ';
                    })
                    
                    /* ================= AUTHORISED PERSON ================= */
                    ->addColumn('Athr_Pers_Name', function ($row) {
                        return '
                        <div>
                            <div class="text-white font-medium">
                            '.$row->Athr_Pers_Name.'
                            </div>
                            
                            <div class="text-gray-400 text-xs rounded">
                            '.ucfirst($row->Dept).'
                            </div>
                        </div>
                        ';
                    })

                    /* ================= ORGANIZATION NAME ================= */
                    ->addColumn('Orga_Name', function ($row) {
                        return '
                        <div class="flex text-left">
                            <span class="text-xs rounded">
                                '.ucfirst($row->Orga_Name).'
                            </span>
                        </div>
                        ';
                    })

                    /* ================= DATE ================= */
                    ->addColumn('date', function ($row) {

                        $effectiveDate = $row->Eft_Dt
                            ? \Carbon\Carbon::parse($row->Eft_Dt)->format('d M Y')
                            : '-';

                        $publishedDate = $row->Pbli_On
                            ? \Carbon\Carbon::parse($row->Pbli_On)->format('d M Y')
                            : '-';

                        return '
                            <div class="text-sm leading-tight">
                                <div class="text-gray-200">
                                    <span class="font-medium"></span> '.$effectiveDate.'
                                </div>
                                <div class="text-gray-400 text-xs">
                                    <span class="font-medium"></span> '.$publishedDate.'
                                </div>
                            </div>
                        ';
                    })

                    /* ================= ACTION ================= */
                    ->addColumn('action', function ($row) {
                        $checkStatus = $row->Stau === 'draft' ? 'Publish' : 'Unpublish';
                        $checkEdit = $row->Stau === 'published' ? 'hidden' : 'flex';
                        $checkShare = $row->Stau === 'draft' ? 'hidden' : 'flex';
                        
                        return '
                            <div class="relative inline-block text-left">
                                <button 
                                    class="action-btn text-gray-400 hover:text-white focus:outline-none cursor-pointer"
                                    data-id="'.$row->Ntic_Crcl_UIN.'"
                                >
                                    ⋮
                                </button>

                                <div class="action-menu hidden">
                                    <a href="'.route('notices.edit', $row->Ntic_Crcl_UIN).'"
                                    class="'.$checkEdit.' items-center gap-2 px-4 py-2 text-gray-200 hover:bg-slate-700">
                                        <i class="bi bi-pencil"></i>
                                        Edit
                                    </a>

                                    <a href="'.route('notices.publish', $row->Ntic_Crcl_UIN).'"
                                    class="flex items-center gap-2 px-4 py-2 text-gray-200 hover:bg-slate-700">
                                        <i class="bi bi-eye"></i>
                                        '.$checkStatus.'
                                    </a>
                                    
                                    <button
                                        onclick="generateShare('.$row->Ntic_Crcl_UIN.')"
                                        class="w-full '.$checkShare.' items-center gap-2 px-4 py-2 text-gray-200 hover:bg-slate-700 cursor-pointer">
                                        <i class="bi bi-share"></i>
                                        Share 
                                    </button>
                                </div>
                            </div>
                        ';
                    })

                    // OPTIMIZED SEARCH - Search in database columns directly
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $search = $request->search['value'];
                            
                            $query->where(function($q) use ($search) {
                                $q->where('Subj', 'like', "%{$search}%")
                                ->orWhere('Ref_No', 'like', "%{$search}%")
                                ->orWhere('Orga_Name', 'like', "%{$search}%")
                                ->orWhere('Athr_Pers_Name', 'like', "%{$search}%")
                                ->orWhere('Dept', 'like', "%{$search}%")
                                ->orWhereRaw("DATE_FORMAT(Eft_Dt, '%d %b %Y') like ?", ["%{$search}%"])
                                ->orWhereRaw("DATE_FORMAT(Pbli_On, '%d %b %Y') like ?", ["%{$search}%"]);
                            });
                        }
                    })

                    // Set order before DataTables applies its own ordering
                    ->order(function ($query) {
                        $query->orderBy('Eft_Dt', 'desc');
                    })

                    ->rawColumns(['title', 'Athr_Pers_Name', 'Orga_Name', 'date', 'action'])
                    ->make(true);
            }

            return view('notices.index');
        }


    /**
     * Show create notice page
     */
    public function create()
    {
        return view('notices.create');
    }

    /**
     * Store notice (Draft / Publish)
     */

    public function store(Request $request)
    {
      
        try {

       // dd($request);
                /* ================= VALIDATION ================= */

                $rules = [
                    'action_type'           => 'required|in:notice_issued,notice_received,circular_issued,circular_received',
                    'notice_date'           => 'required|date',
                    'subject'               => 'required|string|max:255',
                    'effective_date'        => 'nullable|date',
                    'ref_no'                =>'required|string|max:255|',
                    'mode'                  => 'required|in:draft,attachment',
                    'status'                => 'required|in:draft,published',

                    'document_type'         => 'required|in:notice,circular',
                    'department'            => 'nullable|string|max:255',

                    'signature_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    'authorized_person_name'=> 'nullable|string|max:255',
                    'designation'           => 'nullable|string|max:255',
                ];
                                // Normalize reference number (remove spaces & trim)
                if ($request->filled('ref_no')) {
                    $normalizedRef = preg_replace('/\s+/', '', $request->ref_no);
                    $request->merge([
                        'ref_no' => $normalizedRef
                    ]);
                }

                if ($request->filled('ref_no')) {

                    $exists = AdmnTranNticCrcl::where('Ref_No', $request->ref_no)->exists();

                    if ($exists) {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->withErrors([
                                'ref_no' => 'This reference number already exists.'
                            ]);
                    }
                }

                // Organization required only for RECEIVED
                if (str_contains($request->action_type, 'received')) {
                    $rules['organization_name'] = 'required|string|max:255';
                } else {
                    $rules['organization_name'] = 'nullable|string|max:255';
                }

                // Reference number required only for ISSUED
            
                if ($request->mode === 'attachment') {
                    $rules['attachment'] = 'required|file|mimes:pdf|max:5120';
                } else {
                    $rules['content'] = 'required|string';
                }

                $validated = $request->validate($rules);

                /* ================= FILE UPLOADS ================= */

                $attachmentPath = null;
                if ($request->mode === 'attachment' && $request->hasFile('attachment')) {
                    $attachmentPath = $request->file('attachment')
                        ->store('notices/attachments', 'public');
                }

                $signaturePath = null;
                if ($request->hasFile('signature_image')) {
                    $signaturePath = $request->file('signature_image')
                        ->store('notices/signatures', 'public');
                }

                /* ================= SAVE ================= */

                $notice = AdmnTranNticCrcl::create([

                    /* CORE */
                    'Orga_Name'        => $validated['organization_name'] ?? session('org_name'),
                    'Ntic_Crcl_Dt'     => $validated['notice_date'],
                    'Subj'             => $validated['subject'],
                    'Ref_No'           => $validated['ref_no'] ?? null,
                    'Eft_Dt'           => $validated['effective_date'] ?? null,

                    /* ACTION */
                    'Action_Type'      => $validated['action_type'],
                    'Docu_Type'        => $validated['document_type'],

                    /* CONTENT */
                    'Cntn'             => $validated['mode'] === 'draft'
                                            ? $validated['content']
                                            : null,

                    'Atch_Path'        => $validated['mode'] === 'attachment'
                                            ? $attachmentPath
                                            : null,

                    'mode'             => $validated['mode'],
                    'Stau'             => $validated['status'],

                    /* SIGNATORY */
                    'Imgs_Sgnt'        => $signaturePath,
                    'Athr_Pers_Name'   => $validated['authorized_person_name'] ?? null,
                    'Dsig'             => $validated['designation'] ?? null,
                    'Dept'             => $validated['department'] ?? null,

                    /* AUDIT */
                    'CrBy'             => session('User_UIN'),
                    'CrOn'             => now(),

                    'Pbli_By'          => $validated['status'] === 'published'
                                            ? session('User_UIN')
                                            : null,

                    'Pbli_On'          => $validated['status'] === 'published'
                                            ? now()
                                            : null,
                    'Orga_UIN'=>$request['Orga_UIN'],
                    
                ]);
               
                return redirect()
                    ->route('notices.index')
                    ->with('success', 'Notice saved successfully.');

            } catch (ValidationException $e) {

                return redirect()
                    ->back()
                    ->withErrors($e->getMessage())
                    ->withInput();

            } catch (\Exception $e) {

                Log::error('Notice Store Error', [
                    'message' => $e->getMessage(),
                ]);

                return redirect()
                    ->back()
                    ->with('error', $e->getMessage())
                    ->withInput();
            }
        }


        public function show(AdmnTranNticCrcl $notice)
        {
            
            return view('notices.show', compact('notice'));
        }
        public function edit(AdmnTranNticCrcl $notice){
            
            return view('notices.edit', compact('notice'));
        }
        public function updateStatus(Request $request, AdmnTranNticCrcl $notice)
        {
            
            $validated = $request->validate([
                'status' => 'required|in:draft,published'
            ]);

            $data = [
                'Stau'   => $validated['status'],
                'MoBy'  => session('User_UIN') ?? 1,
                'MoOn'  => now(),
            ];

            // If publishing → set published info
            if ($validated['status'] === 'published') {
                $data['Pbli_By'] = session('User_UIN') ?? 1;
                $data['Pbli_On'] = now();
            }

            $notice->update($data);

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'published'
                    ? 'Document published successfully'
                    : 'Document moved back to draft'
            ]);
        }
    // update function

    public function update(Request $request, $uin)
    {
            try {

        // dd($request);
                $notice = AdmnTranNticCrcl::where('Ntic_Crcl_UIN', $uin)->firstOrFail();

                /* ================= VALIDATION ================= */
                $rules = [
                    'document_type'          => 'required|in:notice,circular',
                    'action_type'            => 'required|in:notice_issued,notice_received,circular_issued,circular_received',
                    'subject'                => 'required|string|max:255',
                    'notice_date'            => 'required|date',
                    'effective_date'         => 'nullable|date',
                    'department'             => 'nullable|string|max:100',
                    
                    'mode'                   => 'required|in:draft,attachment',
                    'status'                 => 'required|in:draft,published',

                    'signature_image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    'authorized_person_name' => 'nullable|string|max:255',
                    'designation'            => 'nullable|string|max:255',
                    'organization_name'      => 'required'
                ];

            
        
                // ISSUED → reference no required & unique (ignore self)
            
                // Attachment vs Draft
                if ($request->mode === 'attachment') {
                    $rules['attachment'] = 'nullable|file|mimes:pdf|max:5120';
                } else {
                    $rules['content'] = 'required|string';
                }

                $validated = $request->validate($rules);

                /* ================= BASIC FIELDS ================= */
                $notice->Docu_Type     = $validated['document_type'];
                $notice->Action_Type  = $validated['action_type'];
                $notice->Subj          = $validated['subject'];
                $notice->Ref_No        = $request['ref_no'] ?? null;
                $notice->Orga_Name     = $validated['organization_name'] ?? null;
                $notice->Dept          = $validated['department'] ?? null;

                $notice->Ntic_Crcl_Dt  = $validated['notice_date'];
                $notice->Eft_Dt        = $validated['effective_date'] ?? null;

                $notice->mode          = $validated['mode'];
                $notice->Stau          = $validated['status'];
                $notice->Athr_Pers_Name     = $validated['authorized_person_name'] ?? null;
                $notice->Dsig =$validated['designation'] ?? null;
                /* ================= CONTENT / ATTACHMENT ================= */
              
                if ($request->mode === 'attachment') {

                    // Case 1: User clicked "Remove attachment"
                    if ($request->input('remove_attachment') == 1) {

                        if ($notice->Atch_Path && Storage::disk('public')->exists($notice->Atch_Path)) {
                            Storage::disk('public')->delete($notice->Atch_Path);
                        }

                        $notice->Atch_Path = null;
                    }

                    // Case 2: User uploaded a new attachment (replace or fresh)
                    if ($request->hasFile('attachment')) {

                        // Delete old file if exists
                        if ($notice->Atch_Path && Storage::disk('public')->exists($notice->Atch_Path)) {
                            Storage::disk('public')->delete($notice->Atch_Path);
                        }

                        $filename = 'notice_' . time() . '_' . Str::random(6) . '.pdf';

                        $notice->Atch_Path = $request->file('attachment')
                            ->storeAs('notices/attachments', $filename, 'public');
                    }

                    // Attachment mode never keeps content
                    $notice->Cntn = null;

                } else {
                    // Draft mode (text)
                    $notice->Cntn = $validated['content'];
                }


                /* ================= SIGNATURE IMAGE ================= */
              if ($request->hasFile('signature_image')) {

                    // delete old signature if exists
                    if ($notice->Imgs_Sgnt && Storage::disk('public')->exists($notice->Imgs_Sgnt)) {
                        Storage::disk('public')->delete($notice->Imgs_Sgnt);
                    }

                    $notice->Imgs_Sgnt = $request->file('signature_image')
                        ->store('notices/signatures', 'public');

                    
            }


                /* ================= AUDIT ================= */
                $notice->MoBy = session('User_UIN') ?? 1;
                $notice->MoOn = now();

                $notice->save();

                return redirect()
                    ->route('notices.index')
                    ->with('success', 'Notice updated successfully.');

            } catch (\Exception $e) {

                \Log::error('Notice Update Error', [
                    'uin' => $uin,
                    'error' => $e->getMessage()
                ]);

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error',$e->getMessage());
            }
    }
  public function publish(AdmnTranNticCrcl $notice)
    {
        
        return view('notices.publish', compact('notice'));
    }


}
