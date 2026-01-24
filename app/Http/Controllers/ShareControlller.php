<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\AdmnTranNticShare;
use App\Models\AdmnTranNticCrcl;
class ShareControlller extends Controller
{
    public function createShareLink($noticeId)
    {
        $notice = AdmnTranNticCrcl::findOrFail($noticeId);

        $share = AdmnTranNticShare::create([
            'Ntic_Crcl_UIN' => $notice->Ntic_Crcl_UIN,
            'Share_Token'   => Str::uuid(),
            'Expires_At'    => now()->addHours(24),
            'Created_By'    => session('User_UIN'),
            'Created_IP'    => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'share_url' => route('notices.share.view', $share->Share_Token)
        ]);
    }
      
    public function revokeShare($shareUin)
    {
        AdmnTranNticShare::where('Share_UIN', $shareUin)
            ->update(['Is_Active' => false]);

        return back()->with('success', 'Share link revoked');
    }

    public function viewSharedNotice($token)
    {
        $share = AdmnTranNticShare::where('Share_Token', $token)
            ->where('Is_Active', true)
            ->where('Expires_At', '>', now())
            ->firstOrFail();

        // increment access count (NOW WORKS)
        $share->increment('Access_Count');

        $notice = AdmnTranNticCrcl::findOrFail($share->Ntic_Crcl_UIN);

        return view('notices.shared', compact('notice', 'share'));
    }



}
