<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAccount extends Model
{
    protected $fillable = [
        'customer_id',
        'first_name',
        'last_name',
        'sub_account_number',
        'company_name',
        'phone'
    ];

    public function mainAccount() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    /**
     * Determine if this sub-account is a company account
     * (has company_name but no first_name/last_name)
     */
    public function isCompany()
    {
        return !empty($this->company_name) && empty($this->first_name) && empty($this->last_name);
    }
    
    /**
     * Get the display name for this sub-account (company name or first+last name)
     */
    public function getDisplayName()
    {
        if (!empty($this->company_name)) {
            return $this->company_name;
        }
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get orders where this sub-account is either a receiver (Manila orders) 
     * or a shipper (Batanes orders)
     */
    public function orders()
    {
        // We manually handle this in the controller for more control
        // This relationship is maintained for backwards compatibility
        return $this->hasMany(Order::class, 'recId', 'sub_account_number');
    }
    
    /**
     * Get orders where this sub-account is the consignee (for Manila orders)
     */
    public function receivedOrders()
    {
        return $this->hasMany(Order::class, 'recId', 'sub_account_number')
                    ->where('origin', 'Manila');
    }
    
    /**
     * Get orders where this sub-account is the shipper (for Batanes orders)
     */
    public function shippedOrders()
    {
        return $this->hasMany(Order::class, 'shipperId', 'sub_account_number')
                    ->where('origin', 'Batanes');
    }
}
