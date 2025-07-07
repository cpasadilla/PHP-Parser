<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;

$customers = Customer::select('id', 'first_name', 'last_name', 'company_name')
    ->orderBy('id')
    ->limit(10)
    ->get();

echo "Available customer IDs:\n";
foreach ($customers as $customer) {
    echo "ID: {$customer->id}, Name: {$customer->first_name} {$customer->last_name}, Company: {$customer->company_name}\n";
}

?>
