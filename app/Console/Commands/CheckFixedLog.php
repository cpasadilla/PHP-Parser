<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDeleteLog;

class CheckFixedLog extends Command
{
    protected $signature = 'check:fixed-log';
    protected $description = 'Check if the delete log was fixed';

    public function handle()
    {
        $log = OrderDeleteLog::first();
        
        if (!$log) {
            $this->error('No delete log found');
            return 1;
        }
        
        $this->info("Delete Log ID: {$log->id}");
        $this->info("BL Number: {$log->bl_number}");
        $this->info("Has order_data: " . ($log->order_data ? 'YES' : 'NO'));
        $this->info("Has parcels_data: " . ($log->parcels_data ? 'YES' : 'NO'));
        
        if ($log->order_data) {
            $orderData = $log->order_data;
            $this->info("Order data keys: " . implode(', ', array_keys($orderData)));
        }
        
        return 0;
    }
}
