<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCashCollectionEntry extends Model
{
    protected $fillable = [
        'type',
        'entry_date',
        'ar',
        'or',
        'customer_name',
        'customer_id',
        'gravel_sand',
        'chb',
        'other_income_cement',
        'other_income_df',
        'others',
        'interest',
        'vessel',
        'container_parcel',
        'payment_method',
        'status',
        'total',
        'remark'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'gravel_sand' => 'decimal:2',
        'chb' => 'decimal:2',
        'other_income_cement' => 'decimal:2',
        'other_income_df' => 'decimal:2',
        'others' => 'decimal:2',
        'interest' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
