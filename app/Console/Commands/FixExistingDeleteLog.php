<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderDeleteLog;

class FixExistingDeleteLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:existing-delete-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the existing delete log with correct data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing existing delete log...');
        
        // Find the delete log for BL 001
        $log = OrderDeleteLog::where('bl_number', '001')->first();
        
        if ($log) {
            $log->update([
                'shipper_name' => 'TESS VARGAS',
                'consignee_name' => 'ISLA GAS CORP',
                'total_amount' => 2219.70,
            ]);
            
            $this->info('Fixed delete log for BL 001');
        } else {
            $this->error('Delete log for BL 001 not found');
        }
        
        return 0;
    }
}
