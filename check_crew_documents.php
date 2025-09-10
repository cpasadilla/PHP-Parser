<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\CrewDocument;

echo "Checking existing crew documents with removed document types...\n";

$removedTypes = ['passport', 'visa', 'stcw'];
$existingRecords = CrewDocument::whereIn('document_type', $removedTypes)->get();

echo "Found " . $existingRecords->count() . " records with removed document types:\n";

foreach ($existingRecords as $record) {
    echo "- ID: {$record->id}, Type: {$record->document_type}, Crew: {$record->crew_id}, Name: {$record->document_name}\n";
}

if ($existingRecords->count() > 0) {
    echo "\nNote: These records will still exist but the document types won't appear in new dropdown selections.\n";
} else {
    echo "\nNo existing records found with removed document types. Safe to proceed.\n";
}
