<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$entries = App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
    ->orderBy('date', 'asc')
    ->orderBy('id', 'asc')
    ->get([
        'id', 
        'date', 
        'hollowblock_size', 
        'in',
        'out',
        'balance',
        'hollowblock_4_inch_in', 
        'hollowblock_4_inch_out', 
        'hollowblock_4_inch_balance', 
        'hollowblock_5_inch_in', 
        'hollowblock_5_inch_out', 
        'hollowblock_5_inch_balance', 
        'hollowblock_6_inch_in', 
        'hollowblock_6_inch_out', 
        'hollowblock_6_inch_balance'
    ]);

echo "Total HOLLOWBLOCK entries: " . $entries->count() . "\n\n";

foreach ($entries as $entry) {
    echo "ID: {$entry->id} | Date: {$entry->date} | Size: {$entry->hollowblock_size}\n";
    echo "  Main: IN={$entry->in}, OUT={$entry->out}, BAL={$entry->balance}\n";
    echo "  4\": IN={$entry->hollowblock_4_inch_in}, OUT={$entry->hollowblock_4_inch_out}, BAL={$entry->hollowblock_4_inch_balance}\n";
    echo "  5\": IN={$entry->hollowblock_5_inch_in}, OUT={$entry->hollowblock_5_inch_out}, BAL={$entry->hollowblock_5_inch_balance}\n";
    echo "  6\": IN={$entry->hollowblock_6_inch_in}, OUT={$entry->hollowblock_6_inch_out}, BAL={$entry->hollowblock_6_inch_balance}\n";
    echo "---\n";
}
