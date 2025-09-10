<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $result = DB::select("SHOW COLUMNS FROM leave_applications WHERE Field = 'status'");
    if (!empty($result)) {
        echo "Status column definition:\n";
        echo "Type: " . $result[0]->Type . "\n";
        echo "Null: " . $result[0]->Null . "\n";
        echo "Default: " . $result[0]->Default . "\n";
    } else {
        echo "Status column not found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
