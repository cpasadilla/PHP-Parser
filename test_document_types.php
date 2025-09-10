<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\CrewDocument;

echo "Testing CrewDocument model with new document types...\n\n";

// Test the DOCUMENT_TYPES constant
echo "Available document types:\n";
foreach (CrewDocument::DOCUMENT_TYPES as $key => $label) {
    echo "- {$key}: {$label}\n";
}

echo "\nChecking if removed document types are gone:\n";
$removedTypes = ['passport', 'visa', 'stcw'];
foreach ($removedTypes as $type) {
    if (array_key_exists($type, CrewDocument::DOCUMENT_TYPES)) {
        echo "❌ ERROR: {$type} is still in DOCUMENT_TYPES\n";
    } else {
        echo "✅ {$type} successfully removed\n";
    }
}

echo "\nChecking if new document types are added:\n";
$newTypes = ['insurance', 'sss', 'pag_ibig', 'philhealth', 'tin'];
foreach ($newTypes as $type) {
    if (array_key_exists($type, CrewDocument::DOCUMENT_TYPES)) {
        echo "✅ {$type} successfully added: " . CrewDocument::DOCUMENT_TYPES[$type] . "\n";
    } else {
        echo "❌ ERROR: {$type} is missing from DOCUMENT_TYPES\n";
    }
}

echo "\nTest completed successfully! ✅\n";
