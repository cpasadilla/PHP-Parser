<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDeleteLog;

class FixDeleteLogsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:delete-logs-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix delete logs that have Unknown data by using stored order_data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing Delete Logs Data...');
        
        $deleteLogs = OrderDeleteLog::where(function($query) {
            $query->where('shipper_name', 'Unknown')
                  ->orWhere('consignee_name', 'Unknown')
                  ->orWhere('total_amount', 0)
                  ->orWhereNull('total_amount');
        })->get();
        
        $fixed = 0;
        
        foreach ($deleteLogs as $log) {
            $updated = false;
            
            if ($log->order_data) {
                $orderData = $log->order_data;
                
                // Fix shipper name
                if ($log->shipper_name === 'Unknown' && !empty(trim($orderData['shipperName'] ?? ''))) {
                    $log->shipper_name = trim($orderData['shipperName']);
                    $updated = true;
                }
                
                // Fix consignee name
                if ($log->consignee_name === 'Unknown' && !empty(trim($orderData['recName'] ?? ''))) {
                    $log->consignee_name = trim($orderData['recName']);
                    $updated = true;
                }
                
                // Fix total amount
                if (($log->total_amount == 0 || is_null($log->total_amount)) && !empty($orderData['totalAmount'])) {
                    $log->total_amount = $orderData['totalAmount'];
                    $updated = true;
                }
                
                if ($updated) {
                    $log->save();
                    $fixed++;
                    $this->line("Fixed log ID: {$log->id} - BL: {$log->bl_number}");
                }
            }
        }
        
        $this->info("Fixed {$fixed} delete log records.");
        
        return 0;
    }
}
