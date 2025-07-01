<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class parcel extends Model
{
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
        'desc',
        'total',
        'unit',
        'weight',

    ];
}
