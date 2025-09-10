<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CrewEmbarkation extends Model
{
    protected $fillable = [
        'crew_id',
        'ship_id',
        'embark_date',
        'disembark_date',
        'embark_port',
        'disembark_port',
        'status',
        'remarks'
    ];

    protected $casts = [
        'embark_date' => 'date',
        'disembark_date' => 'date',
    ];

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function ship(): BelongsTo
    {
        return $this->belongsTo(Ship::class);
    }

    // Scope for active embarkations (not yet disembarked)
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNull('disembark_date');
    }

    // Scope for completed embarkations
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')->whereNotNull('disembark_date');
    }

    // Get the duration of the embarkation in days
    public function getDurationAttribute()
    {
        if ($this->disembark_date) {
            return $this->embark_date->diffInDays($this->disembark_date);
        }
        return $this->embark_date->diffInDays(now());
    }

    // Check if the embarkation is currently active
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && is_null($this->disembark_date);
    }
}
