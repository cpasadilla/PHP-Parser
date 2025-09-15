<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryEntry extends Model
{
    protected $fillable = [
        'item', 'date', 'customer_id', 'customer_type', 'ship_number', 'voyage_number', 
        'is_starting_balance', 'in', 'out', 'out_original_bags', 'balance', 'amount', 'or_ar', 'dr_number',
        'onsite_date', 'updated_onsite_date', 'onsite_in', 'actual_out', 'onsite_balance',
        'pickup_delivery_type', 'vat_type', 'hollowblock_size',
        'hollowblock_4_inch_in', 'hollowblock_4_inch_out', 'hollowblock_4_inch_balance',
        'hollowblock_5_inch_in', 'hollowblock_5_inch_out', 'hollowblock_5_inch_balance',
        'hollowblock_6_inch_in', 'hollowblock_6_inch_out', 'hollowblock_6_inch_balance'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class)->withDefault([
            'company_name' => 'Starting Balance',
            'first_name' => '',
            'last_name' => ''
        ]);
    }
}
