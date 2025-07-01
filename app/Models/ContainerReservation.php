<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ship',
        'voyage',
        'type',
        'quantity',
        'containerName',
        'origin',
        'destination',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders()
    {
        // This comprehensive relationship handles the following scenarios:
        // 1. Multiple container numbers separated by commas
        // 2. Special container designations like "PADALA CONTAINER" or "TEMPORARY CONTAINER"
        // 3. Standard single container numbers
        
        $query = $this->hasMany(Order::class, 'shipNum', 'ship')
                ->where('voyageNum', $this->voyage);
        
        // Check if this is a comma-separated container list or a special container designation
        if (strpos($this->containerName, ',') !== false) {
            // Handle multiple container numbers separated by commas
            $containerNumbers = array_map('trim', explode(',', $this->containerName));
            
            return $query->where(function($subQuery) use ($containerNumbers) {
                foreach ($containerNumbers as $index => $containerNumber) {
                    if ($index === 0) {
                        // First condition uses where to start the chain
                        $subQuery->where('containerNum', 'LIKE', '%' . $containerNumber . '%');
                    } else {
                        // Subsequent conditions use orWhere to add alternatives
                        $subQuery->orWhere('containerNum', 'LIKE', '%' . $containerNumber . '%');
                    }
                }
                
                // Also capture cases where the full comma-separated string is in containerNum
                $subQuery->orWhere('containerNum', 'LIKE', '%' . $this->containerName . '%');
            });
        } 
        elseif (strpos($this->containerName, 'PADALA CONTAINER') !== false || 
                strpos($this->containerName, 'TEMPORARY CONTAINER') !== false) {
            // Handle special container designations with exact matching
            return $query->where('containerNum', $this->containerName);
        } 
        else {
            // Normal single container number - use partial matching with wildcards
            return $query->where('containerNum', 'LIKE', '%' . $this->containerName . '%');
        }
    }
}
