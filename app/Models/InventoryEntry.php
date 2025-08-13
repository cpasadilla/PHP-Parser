<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryEntry extends Model
{
    protected $fillable = [
        'item', 'date', 'customer_id', 'in', 'out', 'balance', 'amount', 'or_ar', 'dr_number',
        'onsite_date', 'onsite_in', 'actual_out', 'onsite_balance'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
