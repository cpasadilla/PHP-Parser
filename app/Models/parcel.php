<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class parcel extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class, 'orderId', 'id');
    }

    protected $fillable = [
        'orderId',
        'itemId',
        'itemName',
        'itemPrice',
        'quantity',
        'length',
        'width',
        'height',
        'multiplier',
        'measurements',
        'desc',
        'total',
        'unit',
        'weight',
        'documents',
        'key',
        'date'
    ];

    protected $casts = [
        'measurements' => 'array',
    ];
}
