<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'type',
        'share_holder',
        'email',
        //'password',
        'phone'
    ];

    // Define relationship with SubAccount model
    public function subAccounts() {
        return $this->hasMany(SubAccount::class, 'customer_id');
    }

    // Custom accessor to format ID with leading zeros
    public function getFormattedIdAttribute() {
        return str_pad($this->id - 1000, 4, '0', STR_PAD_LEFT);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'recId');
    }

}
