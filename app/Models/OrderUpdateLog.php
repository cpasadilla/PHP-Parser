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
        'updated_at', // Allow mass assignment of updated_at
    ];

    public $timestamps = false; // Disable automatic timestamps
    
    // Define which columns should be treated as dates with timezone casting
    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    // Override the created_at column name since we only have updated_at
    const CREATED_AT = 'updated_at';
    const UPDATED_AT = null;
}