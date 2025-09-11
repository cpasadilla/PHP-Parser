<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Checking crew_id 3...' . PHP_EOL;
$crew = App\Models\Crew::find(3);
if ($crew) {
    echo 'Crew found: ' . $crew->full_name . ' (Status: ' . $crew->status . ')' . PHP_EOL;
} else {
    echo 'Crew with ID 3 not found' . PHP_EOL;
}

echo PHP_EOL . 'Checking crew with soft deletes...' . PHP_EOL;
$crewWithTrashed = App\Models\Crew::withTrashed()->find(3);
if ($crewWithTrashed) {
    echo 'Crew found (including trashed): ' . $crewWithTrashed->full_name;
    if ($crewWithTrashed->deleted_at) {
        echo ' (DELETED at: ' . $crewWithTrashed->deleted_at . ')';
    }
    echo PHP_EOL;
} else {
    echo 'Crew with ID 3 not found even with trashed records' . PHP_EOL;
}

echo PHP_EOL . 'Checking leave application details...' . PHP_EOL;
$leaveApp = App\Models\LeaveApplication::find(1);
if ($leaveApp) {
    echo 'Leave application found:' . PHP_EOL;
    echo '  ID: ' . $leaveApp->id . PHP_EOL;
    echo '  crew_id: ' . $leaveApp->crew_id . PHP_EOL;
    echo '  status: ' . $leaveApp->status . PHP_EOL;
    echo '  created_at: ' . $leaveApp->created_at . PHP_EOL;
}
