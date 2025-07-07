<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
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
        'padlock_fee', // Add padlock fee
        'shipperId',
        'recId',
        'value',
        'other',
        'wharfage',
        'cargoType',
        'blStatus',
        'discount', // Add this field
        'bir', // Ensure this field is fillable
        'OR', // Add OR field
        'AR', // Add AR field
        'or_ar_date', // Add this field
        'originalFreight', // Ensure this field is fillable
        'interest_start_date', // Field for tracking interest activation date
        'image',
        'note',
        'updated_by', // Add this field
        'dock_period', // Add dock_period field to track which dock period this order belongs to
        'dock_number', // Add dock_number field to track which dock cycle this order belongs to
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
