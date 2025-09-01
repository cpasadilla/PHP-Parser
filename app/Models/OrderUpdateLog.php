<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderUpdateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'updated_by',
        'field_name',
        'old_value',
        'new_value',
        'action_type',
    ];

    public $timestamps = false; // Disable automatic timestamps
    
    // Define which columns should be treated as dates
    protected $dates = ['updated_at'];
    
    // Override the created_at column name since we only have updated_at
    const CREATED_AT = 'updated_at';
    const UPDATED_AT = null;
}