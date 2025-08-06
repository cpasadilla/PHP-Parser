<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class locations extends Model
{
    protected $fillable = [
        'location',
        'name'
    ];

    /**
     * Set the location to uppercase before saving
     */
    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = strtoupper($value);
    }

    /**
     * Set the name to uppercase before saving
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
}
