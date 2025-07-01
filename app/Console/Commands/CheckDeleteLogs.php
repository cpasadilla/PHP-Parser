<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDeleteLog;
use App\Models\Order;

class CheckDeleteLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:delete-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check delete logs data and sample order data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Delete Logs Data...');
        
        // Check delete logs
        $deleteLogs = OrderDeleteLog::limit(5)->get();
        
        if ($deleteLogs->count() > 0) {
            $this->info('Recent Delete Logs:');
            foreach ($deleteLogs as $log) {
                $this->line("ID: {$log->id}, BL: {$log->bl_number}, Shipper: {$log->shipper_name}, Consignee: {$log->consignee_name}, Total: {$log->total_amount}");
                $this->line("  Has order_data: " . ($log->order_data ? 'YES' : 'NO'));
                if ($log->order_data) {
                    $orderData = $log->order_data;
                    $this->line("  Order data shipper: " . ($orderData['shipperName'] ?? 'NULL'));
                    $this->line("  Order data consignee: " . ($orderData['recName'] ?? 'NULL'));
                    $this->line("  Order data total: " . ($orderData['totalAmount'] ?? 'NULL'));
                }
                $this->line("---");
            }
        } else {
            $this->info('No delete logs found.');
        }
        
        // Check sample orders
        $this->info("\nChecking Sample Orders Data...");
        $orders = Order::limit(3)->get();
        
        if ($orders->count() > 0) {
            foreach ($orders as $order) {
                $this->line("Order ID: {$order->id}, BL: {$order->orderId}");
                $this->line("  Shipper Name: '{$order->shipperName}', Shipper Num: '{$order->shipperNum}'");
                $this->line("  Rec Name: '{$order->recName}', Rec Num: '{$order->recNum}'");
                $this->line("  Total Amount: '{$order->totalAmount}'");
                $this->line("---");
            }
        } else {
            $this->info('No orders found.');
        }
        
        return 0;
    }
}
