<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SoaNumber;
use DB;
use Exception;

class ResetSoaNumbers extends Command
{
    protected $signature = 'soa:reset {--force : Force reset without confirmation}';
    
    protected $description = 'Reset all SOA numbers - FOR TESTING ONLY';
    
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL SOA numbers. Are you sure you want to continue? This cannot be undone.')) {
                $this->info('Operation cancelled.');
                return;
            }
        }
        
        try {
            DB::beginTransaction();
            
            // Get count before truncate
            $count = SoaNumber::count();
            
            // Truncate the SOA numbers table
            DB::statement('TRUNCATE TABLE soa_numbers');
            
            DB::commit();
            
            $this->info("All SOA numbers have been reset. {$count} records were deleted.");
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->error("Error resetting SOA numbers: " . $e->getMessage());
        }
    }
}
