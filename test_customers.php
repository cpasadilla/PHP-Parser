<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Customer;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Customer Data Test\n";
echo "==================\n\n";

$customerCount = Customer::count();
echo "Total customers in database: {$customerCount}\n\n";

if ($customerCount > 0) {
    echo "Sample customers:\n";
    echo "-----------------\n";
    
    Customer::take(10)->get()->each(function($customer) {
        $name = $customer->company_name ?: "{$customer->first_name} {$customer->last_name}";
        echo "ID: {$customer->id} | Name: {$name} | Type: {$customer->type}\n";
    });
    
    echo "\nTesting search functionality:\n";
    echo "-----------------------------\n";
    
    // Test search for 'ABC'
    $searchTerm = 'ABC';
    $results = Customer::where('first_name', 'LIKE', "%{$searchTerm}%")
        ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
        ->orWhere('company_name', 'LIKE', "%{$searchTerm}%")
        ->selectRaw("id, COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name")
        ->get();
    
    echo "Search for '{$searchTerm}': " . $results->count() . " results\n";
    foreach ($results as $result) {
        echo "  - {$result->name} (ID: {$result->id})\n";
    }
    
    // Test search for 'Maria'
    $searchTerm = 'Maria';
    $results = Customer::where('first_name', 'LIKE', "%{$searchTerm}%")
        ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
        ->orWhere('company_name', 'LIKE', "%{$searchTerm}%")
        ->selectRaw("id, COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name")
        ->get();
    
    echo "Search for '{$searchTerm}': " . $results->count() . " results\n";
    foreach ($results as $result) {
        echo "  - {$result->name} (ID: {$result->id})\n";
    }
} else {
    echo "No customers found. The seeder may not have run successfully.\n";
}

echo "\n";
