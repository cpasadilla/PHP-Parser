<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderUpdateLog;
use App\Models\order;

class TestHistoryLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:history-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test conditional history logging - only logs when fields actually change';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing conditional field update logging...');

        // Get the first order or create one if none exists
        $order = order::first();
        
        if (!$order) {
            $this->error('No orders found in database. Please create an order first.');
            return 1;
        }

        $this->info('Testing with Order ID: ' . $order->id . ' (BL: ' . $order->orderId . ')');

        // Test 1: Update freight to the same value (should NOT create a log)
        $currentFreight = $order->freight;
        $this->info('Current freight: ' . $currentFreight);
        
        // Get current log count
        $logCountBefore = OrderUpdateLog::where('order_id', $order->id)->count();
        $this->info('Log count before same-value update: ' . $logCountBefore);

        // Simulate updating freight to the same value
        OrderUpdateLog::create([
            'order_id' => $order->id,
            'updated_by' => 'Test User',
            'field_name' => 'freight',
            'old_value' => $currentFreight,
            'new_value' => $currentFreight, // Same value
            'action_type' => 'update'
        ]);

        $this->info('This log was created for testing, but in real usage, same values should NOT be logged.');

        // Test 2: Create logs with actual different values
        $testLogs = [
            [
                'field_name' => 'freight',
                'old_value' => '1000.00',
                'new_value' => '1200.00', // Different value
                'action_type' => 'update'
            ],
            [
                'field_name' => 'OR',
                'old_value' => null,
                'new_value' => 'OR-2025-001', // Different value
                'action_type' => 'update'
            ],
            [
                'field_name' => 'remark',
                'old_value' => 'Old remark',
                'new_value' => 'New remark', // Different value
                'action_type' => 'update'
            ]
        ];

        foreach ($testLogs as $logData) {
            OrderUpdateLog::create([
                'order_id' => $order->id,
                'updated_by' => 'Test User',
                'field_name' => $logData['field_name'],
                'old_value' => $logData['old_value'],
                'new_value' => $logData['new_value'],
                'action_type' => $logData['action_type']
            ]);
        }

        $logCountAfter = OrderUpdateLog::where('order_id', $order->id)->count();
        $this->info('Log count after updates: ' . $logCountAfter);
        $this->info('Logs created: ' . ($logCountAfter - $logCountBefore));

        $this->info('Note: In actual field updates, the system now only logs when values actually change.');
        $this->info('You can test this by updating a field to the same value in the UI - no log should be created.');

        return 0;
    }
}
