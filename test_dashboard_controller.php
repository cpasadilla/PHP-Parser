<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DashboardController;
use App\Models\Ship;
use App\Models\order;
use App\Models\voyage;

try {
    echo "Testing DashboardController...\n";
    
    $controller = new DashboardController();
    $response = $controller->index();
    
    echo "DashboardController: OK\n";
    echo "Response type: " . get_class($response) . "\n";
    
    // Check if it's a view response
    if (method_exists($response, 'getData')) {
        $data = $response->getData();
        echo "View data keys: " . implode(', ', array_keys($data)) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

?>
