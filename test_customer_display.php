<?php

// Simple test script to show customer name formatting
use App\Models\Customer;

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CUSTOMER NAME DISPLAY FORMAT ===\n\n";

// Get all customers to show how names will be displayed
$customers = Customer::selectRaw("
    id,
    first_name,
    last_name, 
    company_name,
    type,
    COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS display_name
")->get();

echo "Total customers: " . $customers->count() . "\n\n";

if ($customers->count() > 0) {
    echo "HOW CUSTOMER NAMES WILL APPEAR IN SEARCH:\n";
    echo "========================================\n\n";
    
    foreach ($customers as $customer) {
        echo "Customer ID: {$customer->id}\n";
        echo "Type: " . ucfirst($customer->type) . "\n";
        
        if ($customer->type === 'company') {
            echo "Company Name: {$customer->company_name}\n";
            echo "Display Name: \"{$customer->display_name}\"\n";
        } else {
            echo "First Name: {$customer->first_name}\n";
            echo "Last Name: {$customer->last_name}\n";
            echo "Display Name: \"{$customer->display_name}\"\n";
        }
        echo "---\n";
    }
    
    echo "\n=== SEARCH EXAMPLES ===\n\n";
    
    // Test different search scenarios
    $searchTerms = ['ABC', 'Maria', 'Ship', 'Cruz', 'Trading'];
    
    foreach ($searchTerms as $searchTerm) {
        echo "Search for: \"{$searchTerm}\"\n";
        
        $results = Customer::where('first_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('company_name', 'LIKE', "%{$searchTerm}%")
            ->selectRaw("
                id,
                COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name
            ")
            ->get();
        
        if ($results->count() > 0) {
            echo "Results found:\n";
            foreach ($results as $result) {
                echo "  → \"{$result->name}\" (ID: {$result->id})\n";
            }
        } else {
            echo "No results found.\n";
        }
        echo "\n";
    }
} else {
    echo "No customers in database. Run the seeder first:\n";
    echo "php artisan db:seed --class=CustomerSeeder\n";
}
