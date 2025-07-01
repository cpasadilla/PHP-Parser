<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            }
        }

        // Insert checkers
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
                }
            }
        }
    }
}
