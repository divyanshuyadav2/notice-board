<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmnTranNticCrcl extends Model
{
    use HasFactory;

    protected $table = 'admn_tran_ntic_crcl';

    protected $primaryKey = 'Ntic_Crcl_UIN';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Orga_Name',
        'Ntic_Crcl_Dt',
        'Subj',
        'Eft_Dt',
        'Cntn',
        'Atch_Path',
        'mode',
        'Imgs_Sgnt',
        'Athr_Pers_Name',
        'Dsig',
        'Dept',
        'Stau',
        'Docu_Type',
        'CrBy',
        'CrOn',
        'MoBy',
        'MoOn',
        'Pbli_By',
        'Pbli_On',
        'Ref_No',
        'Action_Type',
         'Orga_UIN',

    ];
 
}
