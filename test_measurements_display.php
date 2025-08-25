<?php

// Test script to verify measurements display logic

$measurements = [
    [
        'length' => '0.54',
        'width' => '0.63', 
        'height' => '0.45',
        'multiplier' => '2000',
        'qty' => '1',
        'rate' => 545.00,
        'freight' => 545.00
    ],
    [
        'length' => '0.54',
        'width' => '0.45',
        'height' => '0.45', 
        'multiplier' => '3500',
        'qty' => '1',
        'rate' => 389.29,
        'freight' => 389.29
    ]
];

echo "Measurements:\n";
foreach ($measurements as $measurement) {
    if (!empty($measurement['length']) && !empty($measurement['width']) && !empty($measurement['height']) && 
        $measurement['length'] != '0' && $measurement['length'] != '0.00' && 
        $measurement['width'] != '0' && $measurement['width'] != '0.00' && 
        $measurement['height'] != '0' && $measurement['height'] != '0.00') {
        echo $measurement['length'] . ' × ' . $measurement['width'] . ' × ' . $measurement['height'] . "\n";
    }
}

echo "\nRates:\n";
foreach ($measurements as $measurement) {
    if (!empty($measurement['rate']) && $measurement['rate'] > 0) {
        echo number_format($measurement['rate'], 2) . "\n";
    }
}

echo "\nFreight:\n";
$totalFreight = 0;
$hasMultipleFreights = count($measurements) > 1;

foreach ($measurements as $measurement) {
    if (!empty($measurement['freight']) && $measurement['freight'] > 0) {
        echo number_format($measurement['freight'], 2) . "\n";
        $totalFreight += $measurement['freight'];
    }
}

if ($hasMultipleFreights && $totalFreight > 0) {
    echo "Total: " . number_format($totalFreight, 2) . "\n";
}

echo "\nExpected format:\n";
echo "Measurements:\n.54 × .63 × .45\n.54 × .45 × .45\n\n";
echo "Rate:\n545.00\n389.29\n\n";
echo "Freight:\n545.00\n389.29\nTotal: 934.29\n";
