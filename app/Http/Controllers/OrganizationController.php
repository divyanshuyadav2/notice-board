<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserOrganization;
use App\Models\OrganizationMaster;
class OrganizationController extends Controller
{
    public function index()
{
   
      $userUin = session('User_UIN');

      $organizations = UserOrganization::with('organization')
         ->where('User_UIN', $userUin)
        ->get()
        ->pluck('organization')
        ->filter(); // Remove any null values // Extract only the organization models
        // if ($organizations->isEmpty()) {
        //     return view('errors.no-organization');
        // }
      

        return view('organization.select', compact('organizations'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'organization_uin' => 'required|integer'
        ]);

        $orgUin  = $request->organization_uin;
        $orgName = OrganizationMaster::where('Orga_UIN', $orgUin)->value('Orga_Name');

        session([
            'organization_uin' => $orgUin,
            'org_name'         => $orgName,
        ]);

        session()->save();

        // 🔹 If AJAX (modal switch)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true
            ]);
        }

        // 🔹 Normal flow (first-time selection)
        return redirect()->route('notices.index');
    }

}

