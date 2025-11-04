<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing Hollowblock Balances...\n\n";

// Fix balances for each size
$sizes = ['4_inch', '5_inch', '6_inch'];

foreach ($sizes as $size) {
    echo "Processing {$size}...\n";
    
    $entries = App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
        ->where('hollowblock_size', $size)
        ->orderBy('date', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    if ($entries->isEmpty()) {
        echo "  No entries found for {$size}\n\n";
        continue;
    }

    $balanceField = 'hollowblock_' . $size . '_balance';
    $lastBalance = null;
    
    foreach ($entries as $idx => $entry) {
        if ($idx === 0) {
            // Preserve the first entry's balance
            $lastBalance = $entry->$balanceField;
            echo "  Entry {$entry->id} (first): Balance = {$lastBalance}\n";
            continue;
        }

        $outValue = floatval($entry->out ?? 0);
        $inValue = floatval($entry->in ?? 0);
        $newBalance = floatval($lastBalance) - $outValue + $inValue;

        $oldBalance = $entry->$balanceField;
        
        if ($oldBalance != $newBalance) {
            echo "  Entry {$entry->id}: OLD={$oldBalance}, NEW={$newBalance} (IN={$inValue}, OUT={$outValue})\n";
            $entry->$balanceField = $newBalance;
            $entry->save();
        }
        
        $lastBalance = $newBalance;
    }
    
    echo "  {$size} complete!\n\n";
}

echo "\nRecalculation complete! Running verification...\n\n";

// Verify the fix
$entries = App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
    ->orderBy('date', 'asc')
    ->orderBy('id', 'asc')
    ->get([
        'id', 
        'date', 
        'hollowblock_size', 
        'in',
        'out',
        'hollowblock_4_inch_balance', 
        'hollowblock_5_inch_balance', 
        'hollowblock_6_inch_balance'
    ]);

echo "Latest balances:\n";
$latest4 = $entries->where('hollowblock_size', '4_inch')->last();
$latest5 = $entries->where('hollowblock_size', '5_inch')->last();
$latest6 = $entries->where('hollowblock_size', '6_inch')->last();

if ($latest4) echo "  4-inch: {$latest4->hollowblock_4_inch_balance}\n";
if ($latest5) echo "  5-inch: {$latest5->hollowblock_5_inch_balance}\n";
if ($latest6) echo "  6-inch: {$latest6->hollowblock_6_inch_balance}\n";

echo "\nDone!\n";
