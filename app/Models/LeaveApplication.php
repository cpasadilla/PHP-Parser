<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'crew_id',
        'date_applied',
        'leave_type',
        'other_leave_type',
        'others_specify',
        'reason',
        'start_date',
        'end_date',
        'days_requested',
        'department_head_name',
        'captain_master_name',
        'manager_name',
        'leave_credits_as_of',
        'vacation_leave_credits',
        'sick_leave_credits',
        'filled_out_by',
        'filled_out_position',
        'approved_days_with_pay',
        'approved_days_without_pay',
        'disapproved_reason',
        'deferred_until',
        'final_approved_by',
        'final_approved_position',
        'status',
        'processed_by',
        'processed_at',
        'file_path',
        'notes',
        'approved_by',
        'noted_by_captain',
        'noted_by_manager',
        'hr_vacation_credits',
        'hr_sick_credits',
        'hr_filled_by',
        'hr_title',
        'ops_approved_by',
        'ops_title'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime'
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

    const STATUS = [
        'pending' => 'Pending',
        'hr_review' => 'HR Review',
        'approved' => 'Approved',
        'disapproved' => 'Disapproved',
        'deferred' => 'Deferred'
    ];

    public function crew()
    {
        return $this->belongsTo(Crew::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getLeaveTypeNameAttribute()
    {
        return self::LEAVE_TYPES[$this->leave_type] ?? $this->leave_type;
    }

    public function getStatusNameAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
