<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SoaNumber;
use App\Models\Customer;

class ListSoaNumbers extends Command
{
    protected $signature = 'soa:list';
    
    protected $description = 'List all SOA numbers in the system';
    
    public function handle()
    {
        $soaNumbers = SoaNumber::orderBy('year')->orderBy('sequence')->get();
        
        if ($soaNumbers->isEmpty()) {
            $this->info('No SOA numbers found in the system.');
            return;
        }
        
        $headers = ['ID', 'SOA Number', 'Customer', 'Ship', 'Voyage', 'Year', 'Sequence', 'Created'];
        
        $rows = [];
        foreach ($soaNumbers as $soa) {
            $customer = Customer::find($soa->customer_id);
            $customerName = $customer ? 
                (!empty($customer->first_name) || !empty($customer->last_name) ? 
                    $customer->first_name . ' ' . $customer->last_name : 
                    $customer->company_name) : 
                'Unknown';
            
            $rows[] = [
                $soa->id,
                $soa->soa_number,
                $customerName,
                $soa->ship,
                $soa->voyage,
                $soa->year,
                $soa->sequence,
                $soa->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        $this->table($headers, $rows);
    }
}
