<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrewLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'crew_id',
        'leave_type',
        'credits',
        'year',
        'notes'
    ];

    const LEAVE_TYPES = [
        'vacation' => 'Vacation Leave',
        'sick' => 'Sick Leave',
        'emergency' => 'Emergency Leave',
        'maternity' => 'Maternity Leave',
        'paternity' => 'Paternity Leave',
        'bereavement' => 'Bereavement Leave',
        'other' => 'Other'
    ];

    public function crew()
    {
        return $this->belongsTo(Crew::class);
    }

    public function getLeaveTypeNameAttribute()
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? $this->leave_type;
    }
}
