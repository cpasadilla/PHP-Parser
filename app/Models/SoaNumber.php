<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoaNumber extends Model
{
    protected $fillable = [
        'customer_id',
        'ship',
        'voyage',
        'soa_number',
        'year',
        'sequence'
    ];    /**
     * Generate a new SOA number or return an existing one
     * 
     * @param int $customerId The customer ID
     * @param string $ship The ship number/code
     * @param string $voyage The voyage number/code
     * @return string The SOA number in format YEAR-001
     */
    public static function generateSoaNumber($customerId, $ship, $voyage)
    {
        $year = date('Y');
        
        // Check if an SOA number already exists for this customer, ship, and voyage
        $existing = self::where('customer_id', $customerId)
            ->where('ship', $ship)
            ->where('voyage', $voyage)
            ->first();

        if ($existing) {
            return $existing->soa_number;
        }

        // Get the last sequence number for the current year - resets to 1 each new year
        $lastSequence = self::where('year', $year)->max('sequence') ?? 0;
        $newSequence = $lastSequence + 1;

        // Create the SOA number in format YEAR-001
        $soaNumber = $year . '-' . str_pad($newSequence, 3, '0', STR_PAD_LEFT);

        // Create new SOA number record
        self::create([
            'customer_id' => $customerId,
            'ship' => $ship,
            'voyage' => $voyage,
            'soa_number' => $soaNumber,
            'year' => $year,
            'sequence' => $newSequence
        ]);

        return $soaNumber;
    }    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Check if it's a new year and reset the sequence if needed
     * This can be run by a scheduled command at midnight on January 1st
     */
    public static function checkAndResetForNewYear()
    {
        $currentYear = date('Y');
        $lastRecord = self::orderBy('created_at', 'desc')->first();
        
        if ($lastRecord && $lastRecord->year < $currentYear) {
            // It's a new year, no need to do anything as the sequence will start from 1
            return "Sequence will reset for new year {$currentYear}";
        }
        
        return null; // No reset needed
    }
}
