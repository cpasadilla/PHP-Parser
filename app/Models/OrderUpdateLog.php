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

    public $timestamps = false; // Disable timestamps
}