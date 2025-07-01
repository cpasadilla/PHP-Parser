<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDeleteLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'bl_number',
        'ship_name',
        'voyage_number',
        'shipper_name',
        'consignee_name',
        'total_amount',
        'deleted_by',
        'order_data',
        'parcels_data',
        'restored_at',
        'restored_by',
        'restored_order_id',
    ];

    protected $casts = [
        'order_data' => 'array',
        'parcels_data' => 'array',
        'restored_at' => 'datetime',
    ];

    public function restoredOrder()
    {
        return $this->belongsTo(Order::class, 'restored_order_id');
    }
}