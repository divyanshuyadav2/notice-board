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
            ])->orderBy('Ntic_Crcl_Dt', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()

                /* ================= TITLE COLUMN ================= */
                ->addColumn('title', function ($row) {

                    $initial = strtoupper(substr($row->Subj, 0, 1));

                    // Draft / Published dot
                    $dotColor = $row->Stau === 'published'
                        ? 'bg-green-500'
                        : 'bg-yellow-400';

                    $effectiveDate = $row->Eft_Dt
                        ? \Carbon\Carbon::parse($row->Eft_Dt)->format('d M Y')
                        : '-';

                    return '
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-cyan-600
                                        flex items-center justify-center
                                        text-white font-bold">
                                '.$initial.'
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-white font-medium">
                                        '.$row->Subj.'
                                    </span>

                                    <span class="w-2 h-2 rounded-full '.$dotColor.'"></span>
                                </div>

                                <div class="flex items-center gap-3 text-xs text-gray-400">
                                    <span>'.ucfirst($row->mode).'</span>
                                    <span>Effective: '.$effectiveDate.'</span>
                                </div>
                            </div>
                        </div>
                    ';
                })

                /* ================= DOCUMENT TYPE BADGE ================= */
                ->addColumn('document_type', function ($row) {

                    $color = $row->Stau === 'published'
                        ? 'bg-green-600 text-white'
                        : 'bg-yellow-500 text-black';

                    return '
                        <span class="px-3 py-1 text-xs rounded '.$color.'">
                            '.ucfirst($row->Docu_Type).'
                        </span>
                    ';
                })

                /* ================= DATE ================= */
                ->addColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->Ntic_Crcl_Dt)->format('d M Y');
                })

                /* ================= ACTION ================= */
                ->addColumn('action', function ($row) {
                    return '
                        <div class="text-right">
                            <a href="'.route('notices.show', $row->Ntic_Crcl_UIN).'"
                            class="text-gray-400 hover:text-white">
                            ⋮
                            </a>
                        </div>
                    ';
                })

                ->rawColumns(['title', 'document_type', 'action'])
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

            /* ================= VALIDATION ================= */

            $rules = [
                'organization_name'        => 'required|string|max:255',
                'notice_date'              => 'required|date',
                'subject'                  => 'required|string|max:255',
                'effective_date'           => 'nullable|date',

                'mode'                     => 'required|in:draft,attachment',
                'status'                   => 'required|in:draft,published',

                'signature_image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'authorized_person_name'   => 'nullable|string|max:255',
                'designation'              => 'nullable|string|max:255',
                'department'               => 'nullable|string|max:255',

                'document_type'            => 'required|in:notice,circular',
            ];

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
                'Orga_Name'        => $validated['organization_name'],
                'Ntic_Crcl_Dt'     => $validated['notice_date'],
                'Subj'             => $validated['subject'],
                'Eft_Dt'           => $validated['effective_date'] ?? null,

                /* CONTENT */
                'Cntn'             => $validated['mode'] === 'draft'
                                        ? $validated['content']
                                        : null,

                'Atch_Path'        => $validated['mode'] === 'attachment'
                                        ? $attachmentPath
                                        : null,

                'mode'             => $validated['mode'],
                'Stau'             => $validated['status'],
                'Docu_Type'        => $validated['document_type'],

                /* SIGNATORY */
                'Imgs_Sgnt'        => $signaturePath,
                'Athr_Pers_Name'   => $validated['authorized_person_name'] ?? null,
                'Dsig'             => $validated['designation'] ?? null,
                'Dept'             => $validated['department'] ?? null,

                /* AUDIT */
                'CrBy'            => auth()->id() ?? 1,
                'CrOn'            => now(),
                'Pbli_By'          => $validated['status'] === 'published'
                                        ? (auth()->id() ?? 1)
                                        : null,

                'Pbli_On'          => $validated['status'] === 'published'
                                        ? now()
                                        : null,
            ]);

            return redirect()
                ->route('notices.index')
                ->with('success', 'Notice saved successfully.');

        } catch (ValidationException $e) {

            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {

            Log::error('Notice Store Error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong while saving the notice.')
                ->withInput();
        }
    }


    public function show(AdmnTranNticCrcl $notice)
    {
        
        return view('notices.show', compact('notice'));
    }
    public function updateStatus(Request $request, AdmnTranNticCrcl $notice)
    {
        
        $validated = $request->validate([
            'status' => 'required|in:draft,published'
        ]);

        $data = [
            'Stau'   => $validated['status'],
            'MoBy'  => auth()->id() ?? 1,
            'MoOn'  => now(),
        ];

        // If publishing → set published info
        if ($validated['status'] === 'published') {
            $data['Pbli_By'] = auth()->id() ?? 1;
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



}
