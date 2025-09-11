<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrewDocumentDeleteLog extends Model
{
    protected $fillable = [
        'document_id',
        'crew_id',
        'crew_name',
        'employee_id',
        'document_type',
        'document_name',
        'file_name',
        'file_path',
        'expiry_date',
        'status',
        'deleted_by',
        'document_data',
        'restored_at',
        'restored_by',
        'restored_document_id',
    ];

    protected $casts = [
        'document_data' => 'array',
        'expiry_date' => 'date',
        'restored_at' => 'datetime',
    ];

    public function crew()
    {
        return $this->belongsTo(Crew::class, 'crew_id');
    }

    public function restoredDocument()
    {
        return $this->belongsTo(CrewDocument::class, 'restored_document_id');
    }
}
