<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
    use HasFactory;

    protected $fillable = [
        'gate_pass_no',
        'order_id',
        'container_number',
        'shipper_name',
        'consignee_name',
        'checker_notes',
        'checker_name',
        'checker_signature',
        'receiver_name',
        'receiver_signature',
        'release_date',
        'created_by',
        'created_by_name',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    /**
     * Get the order (BL) that this gate pass belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the user who created this gate pass
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the items associated with this gate pass
     */
    public function items()
    {
        return $this->hasMany(GatePassItem::class, 'gate_pass_id');
    }
}
