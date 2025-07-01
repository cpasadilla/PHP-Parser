<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\voyage;

class FixDockPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:dock-periods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix dock periods for existing orders based on voyage dock periods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix dock periods for existing orders...');
        
        // Get all orders without a dock_period
        $ordersToFix = Order::whereNull('dock_period')->get();
        
        $this->info("Found {$ordersToFix->count()} orders without dock_period");
        
        $fixedCount = 0;
        
        foreach ($ordersToFix as $order) {
            // Find the corresponding voyage for this order
            $voyageQuery = voyage::where('ship', $order->shipNum)
                ->where('v_num', explode('-', $order->voyageNum)[0]);
            
            // Handle ships I and II with inOut
            if (strpos($order->voyageNum, '-') !== false) {
                $parts = explode('-', $order->voyageNum);
                $voyageQuery->where('inOut', $parts[1]);
            } else {
                $voyageQuery->where('inOut', '');
            }
            
            $voyages = $voyageQuery->get();
            
            // If multiple voyages exist (pre-dock and post-dock), determine which one based on creation time
            if ($voyages->count() > 1) {
                // Find the dock transition timestamp
                $preDockVoyage = $voyages->where('dock_period', 'LIKE', 'pre_dock_%')->first();
                $postDockVoyage = $voyages->where('dock_period', 'LIKE', 'post_dock_%')->first();
                
                if ($preDockVoyage && $postDockVoyage) {
                    // Extract timestamp from pre_dock period
                    $dockTimestamp = str_replace('pre_dock_', '', $preDockVoyage->dock_period);
                    $dockDate = \Carbon\Carbon::createFromTimestamp($dockTimestamp);
                    
                    if ($order->created_at < $dockDate) {
                        // Order was created before dock transition - assign to pre-dock
                        $order->dock_period = $preDockVoyage->dock_period;
                    } else {
                        // Order was created after dock transition - assign to post-dock
                        $order->dock_period = $postDockVoyage->dock_period;
                    }
                    
                    $order->save();
                    $fixedCount++;
                    
                    $this->line("Fixed Order #{$order->id} - Ship {$order->shipNum} - Voyage {$order->voyageNum} - Assigned to: {$order->dock_period}");
                }
            } else if ($voyages->count() == 1) {
                // Only one voyage exists, assign its dock_period
                $voyage = $voyages->first();
                $order->dock_period = $voyage->dock_period;
                $order->save();
                $fixedCount++;
                
                $this->line("Fixed Order #{$order->id} - Ship {$order->shipNum} - Voyage {$order->voyageNum} - Assigned to: " . ($order->dock_period ?: 'NULL'));
            }
        }
        
        $this->info("Fixed {$fixedCount} orders");
        $this->info('Dock period fix completed!');
        
        return Command::SUCCESS;
    }
}