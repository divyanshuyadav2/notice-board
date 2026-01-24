<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganization extends Model
{
    protected $table = 'admn_user_orga_rela';

    protected $primaryKey = 'User_Assc_UIN';

    public $timestamps = false;

    protected $fillable = [
        'Orga_UIN',
        'User_UIN',
        'Stau_UIN',
        'Expy'
    ];
    public function organization()
    {
        return $this->belongsTo(OrganizationMaster::class, 'Orga_UIN', 'Orga_UIN');
    }

}
