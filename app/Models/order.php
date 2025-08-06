<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    /**
     * Set the origin to uppercase before saving
     */
    public function setOriginAttribute($value)
    {
        $this->attributes['origin'] = strtoupper($value);
    }

    /**
     * Set the destination to uppercase before saving
     */
    public function setDestinationAttribute($value)
    {
        $this->attributes['destination'] = strtoupper($value);
    }

    /**
     * Set the updated_location to uppercase before saving
     */
    public function setUpdatedLocationAttribute($value)
    {
        $this->attributes['updated_location'] = $value ? strtoupper($value) : null;
    }

    protected $fillable = [
        'orderId',
        'totalAmount',
        'shipperName',
        'shipperNum',
        'recName',
        'recNum',
        'origin',
        'destination',
        'shipNum',
        'voyageNum',
        'containerNum',
        'cargoStatus',
        'gatePass',
        'checkName',
        'remark',
        'orderCreated',
        'creator',
        'freight',
        'valuation',
        'padlock_fee',
        'shipperId',
        'recId',
        'value',
        'other',
        'wharfage',
        'cargoType',
        'blStatus',
        'discount',
        'bir',
        'OR',
        'AR',
        'or_ar_date',
        'originalFreight',
        'interest_start_date',
        'image',
        'note',
        'updated_by',
        'updated_location',
        'dock_period',
        'dock_number'
    ];

    protected $casts = [
        'image' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        // Set default values for nullable fields and blStatus
        static::creating(function ($order) {
            // Set default value for blStatus
            if (is_null($order->blStatus)) {
                $order->blStatus = 'UNPAID';
            }

            // Set default values for nullable fields
            if (is_null($order->shipperId)) {
                $order->shipperId = 0;
            }

            if (is_null($order->recNum)) {
                $order->recNum = '';
            }

            if (is_null($order->shipperNum)) {
                $order->shipperNum = '';
            }
        });
    }

    public function parcels()
    {
        return $this->hasMany(parcel::class, 'orderId', 'id');
    }
    
    public function shipper()
    {
        return $this->belongsTo(Customer::class, 'shipperId');
    }
    
    public function receiver()
    {
        return $this->belongsTo(Customer::class, 'recId');
    }
    
    public function customer()
    {
        // Determine which relationship to use (shipper or receiver) based on origin
        if ($this->origin === 'Manila') {
            return $this->receiver();
        } else {
            return $this->shipper();
        }
    }
}
