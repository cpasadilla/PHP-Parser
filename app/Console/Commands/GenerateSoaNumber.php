<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SoaNumber;
use App\Models\Customer;

class GenerateSoaNumber extends Command
{
    protected $signature = 'soa:generate {customerId} {ship} {voyage}';
    
    protected $description = 'Generate an SOA number for testing';
    
    public function handle()
    {
        $customerId = $this->argument('customerId');
        $ship = $this->argument('ship');
        $voyage = $this->argument('voyage');
        
        $customer = Customer::find($customerId);
        if (!$customer) {
            $this->error("Customer with ID {$customerId} not found.");
            return;
        }
        
        $soaNumber = SoaNumber::generateSoaNumber($customerId, $ship, $voyage);
        
        $this->info("Generated SOA Number: {$soaNumber}");
        $this->info("For Customer: " . (!empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name));
        $this->info("Ship: {$ship}");
        $this->info("Voyage: {$voyage}");
        
        return;
    }
}
