<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDeleteLog;
use App\Models\Order;
use App\Models\Parcel;

class FixDeleteLogData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:delete-log-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix delete log that is missing order_data by reconstructing from existing order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing delete log data...');
        
        // Find delete logs without order_data
        $deleteLogs = OrderDeleteLog::whereNull('order_data')->get();
        
        foreach ($deleteLogs as $log) {
            $this->line("Checking delete log ID: {$log->id}, BL: {$log->bl_number}");
            
            // Try to find the original order if it still exists
            $order = Order::where('orderId', $log->bl_number)->first();
            
            if ($order) {
                $this->line("Found existing order, updating delete log with order data...");
                
                // Get parcels for this order
                $parcels = Parcel::where('orderId', $order->id)->get();
                
                // Update the delete log with the order and parcels data
                $log->update([
                    'order_data' => $order->toArray(),
                    'parcels_data' => $parcels->toArray(),
                ]);
                
                $this->info("Updated delete log ID: {$log->id} with order data");
            } else {
                // Order doesn't exist anymore, reconstruct basic data from what we have
                $this->line("Order no longer exists, creating basic order data...");
                
                $basicOrderData = [
                    'orderId' => $log->bl_number,
                    'shipperName' => $log->shipper_name,
                    'recName' => $log->consignee_name,
                    'totalAmount' => $log->total_amount,
                    'shipNum' => $log->ship_name,
                    'voyageNum' => $log->voyage_number,
                    // Add some default values for required fields
                    'freight' => 0,
                    'valuation' => 0,
                    'cargoType' => 'Unknown',
                    'shipperId' => '',
                    'shipperNum' => '',
                    'recId' => '',
                    'recNum' => '',
                    'origin' => 'Unknown',
                    'destination' => 'Unknown',
                    'orderCreated' => $log->created_at->format('Y-m-d'),
                    'creator' => $log->deleted_by,
                ];
                
                $log->update([
                    'order_data' => $basicOrderData,
                    'parcels_data' => [], // No parcels data available
                ]);
                
                $this->info("Created basic order data for delete log ID: {$log->id}");
            }
        }
        
        $this->info("Fixed {$deleteLogs->count()} delete log records.");
        
        return 0;
    }
}
