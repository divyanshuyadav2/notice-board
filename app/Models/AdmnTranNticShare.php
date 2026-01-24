<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmnTranNticShare extends Model
{
    protected $table='admn_tran_ntic_share';
    public $timestamps = false;
    protected $primaryKey = 'Share_UIN';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'Ntic_Crcl_UIN',
        'Share_Token',
        'Expires_At',
        'Created_By',
        'Created_IP',
        'Is_Active'
    ];
    protected $casts = [
        'Expires_At' => 'datetime',
        'Is_Active' => 'boolean'
    ];
}
