<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatePassItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'gate_pass_id',
        'item_description',
        'total_quantity',
        'unit',
        'released_quantity',
        'remaining_quantity',
    ];

    protected $casts = [
        'total_quantity' => 'decimal:2',
        'released_quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
    ];

    /**
     * Get the gate pass that this item belongs to
     */
    public function gatePass()
    {
        return $this->belongsTo(GatePass::class, 'gate_pass_id');
    }
}
