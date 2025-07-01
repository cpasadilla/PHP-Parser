<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    // Ensure the table name matches exactly
    protected $table = 'pricelists';

    protected $fillable = [
        'item_code',
        'item_name',
        'category',
        'price',
        'unit',
        'multiplier'
    ];

    public function mainAccount() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

