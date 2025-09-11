<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Total leave applications: ' . App\Models\LeaveApplication::count() . PHP_EOL;
echo 'Leave applications with missing crew: ' . App\Models\LeaveApplication::whereDoesntHave('crew')->count() . PHP_EOL;
echo 'Leave applications with valid crew: ' . App\Models\LeaveApplication::whereHas('crew')->count() . PHP_EOL;

$orphaned = App\Models\LeaveApplication::whereDoesntHave('crew')->get(['id', 'crew_id']);
if ($orphaned->count() > 0) {
    echo 'Orphaned applications:' . PHP_EOL;
    foreach ($orphaned as $app) {
        echo '  ID: ' . $app->id . ', crew_id: ' . $app->crew_id . PHP_EOL;
    }
}
