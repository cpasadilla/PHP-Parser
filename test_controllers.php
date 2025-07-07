<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterListController;
use Illuminate\Http\Request;

try {
    // Test DashboardController
    echo "Testing DashboardController...\n";
    $controller = new DashboardController();
    $response = $controller->index();
    echo "DashboardController: OK\n";
    
    // Test MasterListController
    echo "Testing MasterListController...\n";
    $controller = new MasterListController();
    
    // Test blListAll method
    $request = new Request();
    $response = $controller->blListAll($request);
    echo "MasterListController blListAll: OK\n";
    
    // Test soa method
    $response = $controller->soa($request);
    echo "MasterListController soa: OK\n";
    
    // Test list method
    $response = $controller->list($request);
    echo "MasterListController list: OK\n";
    
    echo "All tests passed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

?>
