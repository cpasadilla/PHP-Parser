<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::select('DESCRIBE crews');

foreach ($columns as $column) {
    if ($column->Field === 'srn') {
        echo "srn field found:\n";
        echo "  Type: {$column->Type}\n";
        echo "  Null: {$column->Null}\n";
        echo "  Default: {$column->Default}\n";
        break;
    }
}
