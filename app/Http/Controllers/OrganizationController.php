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
    $orgUin = $request->get('organization_uin');
        // Option 1: Get just the value (recommended for single column)
    $orgName = OrganizationMaster::where('Orga_UIN', $orgUin)->value('Orga_Name');

  
    
    if ($orgUin) {
        session([
            'organization_uin' => $orgUin,
            'org_name'=>$orgName
            ]);
        session()->save();
        
       
        return redirect()->route('notices.index');
    }
    
    \Log::info('No Organization UIN provided');
    return redirect()->route('organization.select')->with('error', 'Please select an organization');
    }
}

