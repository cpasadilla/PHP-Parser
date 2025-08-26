<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            ['name' => 'GENERAL MERCHANDISE', 'prefix' => 'GM'],
            ['name' => 'APPLIANCES / FURNITURES', 'prefix' => 'AF'],
            ['name' => 'CONSTRUCTION MATERIALS', 'prefix' => 'CM'],
            ['name' => 'FUEL / LPG', 'prefix' => 'FL'],
            ['name' => 'PLYWOOD / LUMBER', 'prefix' => 'PL'],
            ['name' => 'VEHICLES', 'prefix' => 'VHC'],
            ['name' => 'BACKLOAD', 'prefix' => 'BKL'],
            ['name' => 'VOLUME', 'prefix' => 'VOL'],
            ['name' => 'SHEET', 'prefix' => 'SH'],
            ['name' => 'STEEL PRODUCTS', 'prefix' => 'SP'],
            ['name' => 'VARIOUS', 'prefix' => 'VAR'],
            ['name' => 'FROZEN', 'prefix' => 'FRZ'],
            ['name' => 'PARCEL', 'prefix' => 'PAR'],
            ['name' => 'AGGREGATES', 'prefix' => 'AGG'],
            ['name' => 'SAND', 'prefix' => 'SND'],
        ];

        foreach ($defaultCategories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'prefix' => $category['prefix'],
                    'is_default' => true,
                ]
            );
        }
    }
}
