<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'inventory_entry_id',
        'action_type',
        'field_name',
        'old_value',
        'new_value',
        'updated_by',
        'entry_data'
    ];

    protected $casts = [
        'entry_data' => 'array'
    ];

    public function inventoryEntry()
    {
        return $this->belongsTo(InventoryEntry::class, 'inventory_entry_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
