<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrewDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'crew_id',
        'document_type',
        'document_name',
        'file_path',
        'file_name',
        'file_size',
        'expiry_date',
        'status',
        'uploaded_by',
        'verified_by',
        'verified_at',
        'notes'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'verified_at' => 'datetime'
    ];

    const DOCUMENT_TYPES = [
        'seaman_book' => 'Seaman Book',
        'medical_certificate' => 'Medical Certificate',
        'basic_safety_training' => 'Basic Safety Training',
        'coc' => 'Certificate of Competency',
        'dcoc' => 'Domestic Certificate of Competency (DCOC)',
        'marina_license' => 'MARINA License',
        'contract' => 'Employment Contract',
        'identification' => 'Government ID',
        'id_picture' => 'Crew ID Picture',
        'tax_certificate' => 'Tax Certificate',
        'resume' => 'Resume',
        'insurance' => 'Insurance',
        'sss' => 'SSS',
        'pag_ibig' => 'Pag-ibig',
        'philhealth' => 'Philhealth',
        'tin' => 'TIN',
        'other' => 'Other'
    ];

    const STATUS = [
        'pending' => 'Pending Review',
        'verified' => 'Verified',
        'rejected' => 'Rejected',
        'expired' => 'Expired'
    ];

    public function crew()
    {
        return $this->belongsTo(Crew::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getDocumentTypeNameAttribute()
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? $this->document_type;
    }

    public function getStatusNameAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
