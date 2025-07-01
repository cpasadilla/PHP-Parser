<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDeleteLog;
use App\Models\Order;

class TestDeletion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:deletion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the deletion logging process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing deletion process...');
        
        // Get a sample order
        $order = Order::first();
        
        if (!$order) {
            $this->error('No orders found to test with.');
            return 1;
        }
        
        $this->line("Testing with Order ID: {$order->id}, BL: {$order->orderId}");
        $this->line("Shipper Name: '{$order->shipperName}'");
        $this->line("Rec Name: '{$order->recName}'");
        $this->line("Total Amount: '{$order->totalAmount}'");
        
        // Simulate the logic from destroyOrder
        $shipperName = 'Unknown';
        $consigneeName = 'Unknown';
        $totalAmount = 0;

        // Try to get shipper name
        if (!empty(trim($order->shipperName))) {
            $shipperName = trim($order->shipperName);
        }

        // Try to get consignee name
        if (!empty(trim($order->recName))) {
            $consigneeName = trim($order->recName);
        }

        // Try to get total amount
        if (!empty($order->totalAmount) && $order->totalAmount > 0) {
            $totalAmount = $order->totalAmount;
        }
        
        $this->line("Resolved Shipper: '{$shipperName}'");
        $this->line("Resolved Consignee: '{$consigneeName}'");
        $this->line("Resolved Total: '{$totalAmount}'");
        
        return 0;
    }
}
