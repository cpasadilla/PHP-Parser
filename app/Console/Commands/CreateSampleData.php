<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyCashCollectionEntry;

class CreateSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:sample-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample data for testing daily cash collection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create sample trading entries
        $entry1 = DailyCashCollectionEntry::create([
            'type' => 'trading',
            'entry_date' => '2025-09-03',
            'ar' => 'AR001',
            'or' => 'OR001',
            'customer_name' => 'Test Customer One',
            'gravel_sand' => 5000.00,
            'chb' => 3000.00,
            'other_income_cement' => 2000.00,
            'other_income_df' => 1500.00,
            'others' => 500.00,
            'interest' => 100.00,
            'total' => 12100.00,
            'remark' => 'Test entry for trading'
        ]);

        $entry2 = DailyCashCollectionEntry::create([
            'type' => 'trading',
            'entry_date' => '2025-09-03',
            'ar' => 'AR002',
            'or' => 'OR002',
            'customer_name' => 'Test Customer Two',
            'gravel_sand' => 7500.00,
            'chb' => 4000.00,
            'other_income_cement' => 1800.00,
            'other_income_df' => 2200.00,
            'others' => 750.00,
            'interest' => 250.00,
            'total' => 16500.00,
            'remark' => 'Second test entry'
        ]);

        $this->info('Sample data created successfully!');
        $this->info("Entry 1 ID: {$entry1->id}");
        $this->info("Entry 2 ID: {$entry2->id}");
        $this->info("Total entries: " . DailyCashCollectionEntry::count());
    }
}
