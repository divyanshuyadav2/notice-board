<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
    'organization_name',
    'notice_date',
    'subject',
    'effective_date',
    'mode',
    'status',
    'content',
    'attachment_path',
    'signature_image',
    'authorized_person_name',
    'designation',
    'department',
    'document_type',
    'created_by',
];


    protected $casts = [
        'notice_date'   => 'date',
        'effective_date'=> 'date',
    ];

    /**
     * Scope for published notices
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for draft notices
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
