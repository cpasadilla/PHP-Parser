<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrewDeleteLog extends Model
{
    protected $fillable = [
        'crew_id',
        'employee_id',
        'full_name',
        'position',
        'department',
        'ship_name',
        'employment_status',
        'deleted_by',
        'crew_data',
        'documents_data',
        'leaves_data',
        'restored_at',
        'restored_by',
        'restored_crew_id',
    ];

    protected $casts = [
        'crew_data' => 'array',
        'documents_data' => 'array',
        'leaves_data' => 'array',
        'restored_at' => 'datetime',
    ];

    public function restoredCrew()
    {
        return $this->belongsTo(Crew::class, 'restored_crew_id');
    }
}
