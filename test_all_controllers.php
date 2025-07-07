<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterListController;
use Illuminate\Http\Request;

echo "=== Testing Controllers for 500 Error Pages ===\n\n";

// Test 1: Dashboard Controller
echo "1. Testing DashboardController::index()...\n";
try {
    $controller = new DashboardController();
    $response = $controller->index();
    echo "   ✓ DashboardController: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ DashboardController: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 2: MasterList blListAll
echo "\n2. Testing MasterListController::blListAll()...\n";
try {
    $controller = new MasterListController();
    $response = $controller->blListAll(new Request());
    echo "   ✓ MasterListController::blListAll: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::blListAll: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 3: MasterList voyageOrders
echo "\n3. Testing MasterListController::voyageOrders()...\n";
try {
    $controller = new MasterListController();
    $response = $controller->voyageOrders(new Request(), 'II', '11-OUT');
    echo "   ✓ MasterListController::voyageOrders: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::voyageOrders: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 4: MasterList soa
echo "\n4. Testing MasterListController::soa()...\n";
try {
    $controller = new MasterListController();
    $response = $controller->soa(new Request());
    echo "   ✓ MasterListController::soa: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::soa: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 5: MasterList soa_list
echo "\n5. Testing MasterListController::soa_list()...\n";
try {
    $controller = new MasterListController();
    $request = new Request();
    $request->merge(['customer_id' => 1]); // Use customer ID 1 for testing
    $response = $controller->soa_list($request);
    echo "   ✓ MasterListController::soa_list: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::soa_list: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 6: MasterList soa_temp
echo "\n6. Testing MasterListController::soa_temp()...\n";
try {
    $controller = new MasterListController();
    $response = $controller->soa_temp(new Request(), 'II', '11-OUT', 1);
    echo "   ✓ MasterListController::soa_temp: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::soa_temp: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 7: MasterList soa_voy_temp
echo "\n7. Testing MasterListController::soa_voy_temp()...\n";
try {
    $controller = new MasterListController();
    $response = $controller->soa_voy_temp(new Request(), 'II', '11-OUT');
    echo "   ✓ MasterListController::soa_voy_temp: SUCCESS\n";
} catch (Exception $e) {
    echo "   ✗ MasterListController::soa_voy_temp: ERROR - " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";

?>
