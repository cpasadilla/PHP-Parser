<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class ResetInterestActivation extends Command
{
    protected $signature = 'interest:reset {ship?} {voyage?} {customer_id?}';
    
    protected $description = 'Reset interest activation status for orders';
    
    public function handle()
    {
        $ship = $this->argument('ship');
        $voyage = $this->argument('voyage');
        $customerId = $this->argument('customer_id');
        
        $query = Order::query();
        
        if ($ship) {
            $query->where('shipNum', $ship);
        }
        
        if ($voyage) {
            $query->where('voyageNum', $voyage);
        }
        
        if ($customerId) {
            $query->where(function($q) use ($customerId) {
                $q->where('recId', $customerId)
                  ->orWhere('shipperId', $customerId);
            });
        }
        
        $count = $query->update(['interest_start_date' => null]);
        
        $this->info("{$count} orders have had their interest activation status reset.");
        $this->info("Please also clear your browser's localStorage to fully reset the interest activation status.");
        
        return 0;
    }
}
