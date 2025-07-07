<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\MasterListController;
use Illuminate\Http\Request;

echo "=== Testing SOA Controllers with Valid Customer ID ===\n\n";

// Test 1: MasterList soa_list with valid customer ID
echo "1. Testing MasterListController::soa_list() with customer ID 1001...\n";
try {
    $controller = new MasterListController();
    $request = new Request();
    $request->merge(['customer_id' => 1001]);
    $response = $controller->soa_list($request);
    echo "   ✓ MasterListController::soa_list: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::soa_list: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 2: MasterList soa_temp with valid customer ID
echo "\n2. Testing MasterListController::soa_temp() with customer ID 1001...\n";
try {
    $controller = new MasterListController();
    $response = $controller->soa_temp(new Request(), 'II', '11-OUT', 1001);
    echo "   ✓ MasterListController::soa_temp: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::soa_temp: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";

?>
