<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubAccount; 
use App\Models\Customer;

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

    /**
     * Relationship for Main Accounts
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault([
            'company_name' => 'Starting Balance',
            'first_name' => '',
            'last_name' => ''
        ]);
    }

    /**
     * Relationship for Sub Accounts
     */
    public function subAccount()
    {
        return $this->belongsTo(SubAccount::class, 'customer_id');
    }

    /**
     * Accessor to get the name regardless of type (Main or Sub)
     * Usage: $inventoryEntry->customer_name
     */
    public function getCustomerNameAttribute()
    {
        // Select the relationship based on the customer_type column
        $account = ($this->customer_type === 'main') 
            ? $this->customer 
            : $this->subAccount;

        // If the relationship returns null (especially for SubAccounts)
        if (!$account) {
            return '';
        }

        // Return Company Name, or fallback to First + Last Name
        return $account->company_name ?: trim($account->first_name . ' ' . $account->last_name);
    }
}