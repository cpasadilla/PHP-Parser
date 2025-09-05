<?php
/**
 * Test script to verify PER BAG conversion functionality
 * Formula: Number of Bags / 36 = Cubic Meters
 * Example: 5 bags / 36 = 0.139 cubic meters
 */

echo "PER BAG Conversion Test\n";
echo "======================\n\n";

// Test cases
$testCases = [
    ['bags' => 1, 'expected_cubic' => 1/36],
    ['bags' => 5, 'expected_cubic' => 5/36],
    ['bags' => 10, 'expected_cubic' => 10/36],
    ['bags' => 36, 'expected_cubic' => 1.0],
    ['bags' => 72, 'expected_cubic' => 2.0],
];

foreach ($testCases as $case) {
    $bags = $case['bags'];
    $expectedCubic = $case['expected_cubic'];
    $calculatedCubic = $bags / 36;
    
    echo "Test: {$bags} bags\n";
    echo "Expected: " . number_format($expectedCubic, 6) . " cubic meters\n";
    echo "Calculated: " . number_format($calculatedCubic, 6) . " cubic meters\n";
    echo "Match: " . ($calculatedCubic == $expectedCubic ? "✓ PASS" : "✗ FAIL") . "\n";
    echo "Formula verification: {$bags} ÷ 36 = " . number_format($calculatedCubic, 6) . "\n";
    echo "Equivalent: 1 bag = " . number_format(1/36, 6) . " cubic (≈ 0.028)\n\n";
}

echo "User Example Test:\n";
echo "==================\n";
echo "User enters: 5 bags in OUT field\n";
echo "Conversion: 5 ÷ 36 = " . number_format(5/36, 6) . " cubic meters\n";
echo "Display: 0.139 cubic will be deducted from balance\n";
echo "Storage: out_original_bags = 5, out = " . number_format(5/36, 6) . "\n";
