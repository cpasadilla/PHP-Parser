<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ship;
use App\Models\order;
use App\Models\voyage;

try {
    echo "Testing models...\n";
    
    $ships = Ship::all();
    echo "Ships model: OK (found " . $ships->count() . " ships)\n";
    
    $orders = order::all();
    echo "Orders model: OK (found " . $orders->count() . " orders)\n";
    
    $voyages = voyage::all();
    echo "Voyages model: OK (found " . $voyages->count() . " voyages)\n";
    
    echo "All models working!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

?>
