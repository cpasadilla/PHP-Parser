<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedCheckers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:checkers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with initial locations and checkers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting seeding of locations and checkers...');

        // First, ensure the tables exist
        if (!Schema::hasTable('locations')) {
            $this->error('Locations table does not exist. Run migrations first.');
            return 1;
        }

        if (!Schema::hasTable('checkers')) {
            $this->error('Checkers table does not exist. Run migrations first.');
            return 1;
        }

        // Define checkers by location
        $checkersByLocation = [
            "MANILA" => ["ABELLO", "ALDAY", "ANCHETA", "CACHO", "ESGUERRA", "MORENO", "NALLAS", "VICTORIANO", "ZERRUDO"],
            "BATANES" => ["SOL", "TIRSO", "VARGAS", "NICK", "JOSIE", "JEN"]
        ];

        // Make sure the locations exist first
        foreach (array_keys($checkersByLocation) as $location) {
            if (!DB::table('locations')->where('name', $location)->exists()) {
                DB::table('locations')->insert([
                    'name' => $location,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("Created location: {$location}");
            } else {
                $this->info("Location already exists: {$location}");
            }
        }

        // Insert checkers
        $addedCount = 0;
        $existingCount = 0;

        foreach ($checkersByLocation as $location => $checkers) {
            foreach ($checkers as $checker) {
                // Check if the combination already exists
                if (!DB::table('checkers')->where('name', $checker)->where('location', $location)->exists()) {
                    DB::table('checkers')->insert([
                        'name' => $checker,
                        'location' => $location,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->info("Added checker: {$checker} to {$location}");
                    $addedCount++;
                } else {
                    $this->line("Checker already exists: {$checker} in {$location}");
                    $existingCount++;
                }
            }
        }

        $this->info("Seeding completed. Added {$addedCount} new checkers. {$existingCount} checkers already existed.");
        return 0;
    }
}
