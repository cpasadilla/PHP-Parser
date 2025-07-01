<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SoaNumber;

class CheckNewYear extends Command
{
    protected $signature = 'soa:check-new-year';
    
    protected $description = 'Check if it\'s a new year and reset the SOA numbering sequence';
    
    public function handle()
    {
        $result = SoaNumber::checkAndResetForNewYear();
        
        if ($result) {
            $this->info($result);
        } else {
            $this->info("No reset needed. Current year's sequence continues.");
        }
        
        return;
    }
}
