<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\Crew;

echo "Checking crew data...\n";

$crewCount = Crew::count();
echo "Total crew members: {$crewCount}\n";

if ($crewCount > 0) {
    echo "\nCrew by division and department:\n";
    $crewByDivision = Crew::selectRaw('division, department, COUNT(*) as count')
        ->groupBy('division', 'department')
        ->get();
    
    foreach ($crewByDivision as $group) {
        echo "- {$group->division} - {$group->department}: {$group->count} members\n";
    }
    
    echo "\nOffice/Shore personnel (ship_id is null):\n";
    $officeShore = Crew::whereNull('ship_id')->count();
    echo "- Office/Shore: {$officeShore} members\n";
    
    echo "\nShip assignments:\n";
    $shipCrew = Crew::with('ship')->whereNotNull('ship_id')->get();
    foreach ($shipCrew->groupBy('ship_id') as $shipId => $crewMembers) {
        $shipName = $crewMembers->first()->ship ? 'MV EVERWIN STAR ' . $crewMembers->first()->ship->ship_number : 'Unknown Ship';
        echo "- {$shipName}: {$crewMembers->count()} members\n";
    }
} else {
    echo "No crew data found. You may need to add some crew members first.\n";
}

echo "\nTesting export functionality is ready!\n";
