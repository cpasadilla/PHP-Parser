<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crew extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'middle_name',
        'birthday',
        'position',
        'division',
        'department',
        'ship_id',
        'hire_date',
        'employment_status',
        'phone',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'sss_number',
        'pagibig_number',
        'philhealth_number',
        'tin_number',
        'seaman_book_number',
        'seaman_book_issue_date',
        'seaman_book_expiry_date',
        'basic_safety_training',
        'medical_certificate',
        'medical_certificate_issue_date',
        'dcoc_number',
        'dcoc_issue_date',
        'dcoc_expiry',
        'marina_license_number',
        'marina_license_issue_date',
        'marina_license_expiry',
        'contract_expiry',
        'notes',
        'srn'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birthday' => 'date',
        'contract_expiry' => 'date',
        'basic_safety_training' => 'date',
        'medical_certificate' => 'date',
        'medical_certificate_issue_date' => 'date',
        'seaman_book_issue_date' => 'date',
        'seaman_book_expiry_date' => 'date',
        'dcoc_issue_date' => 'date',
        'dcoc_expiry' => 'date',
        'marina_license_issue_date' => 'date',
        'marina_license_expiry' => 'date'
    ];

    public function ship()
    {
        return $this->belongsTo(Ship::class);
    }

    public function documents()
    {
        return $this->hasMany(CrewDocument::class);
    }

    public function leaves()
    {
        return $this->hasMany(CrewLeave::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    // Temporarily disabled until crew_embarkations table structure is fixed
    public function embarkations()
    {
        throw new \Exception('Embarkations relationship is temporarily disabled due to table structure issues');
    }

    public function currentEmbarkation()
    {
        throw new \Exception('CurrentEmbarkation relationship is temporarily disabled due to table structure issues');
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getTotalLeaveCreditsAttribute()
    {
        return $this->leaves->sum('credits');
    }

    public function getUsedLeaveCreditsAttribute()
    {
        return $this->leaveApplications()
            ->where('status', 'approved')
            ->sum('days_requested');
    }

    public function getAvailableLeaveCreditsAttribute()
    {
        return max(0, $this->total_leave_credits - $this->used_leave_credits);
    }

    public function scopeByShip($query, $shipId)
    {
        return $query->where('ship_id', $shipId);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeActive($query)
    {
        return $query->where('employment_status', 'active');
    }
}
