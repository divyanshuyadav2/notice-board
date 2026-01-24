<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationMaster extends Model
{
     protected $table = 'admn_orga_mast';
     protected $primaryKey = 'Orga_UIN';
    public $timestamps = false;
    // Reverse relationship
     public function userOrganizations()
     {
         return $this->hasMany(UserOrganization::class, 'Orga_UIN', 'Orga_UIN');
     }


}
