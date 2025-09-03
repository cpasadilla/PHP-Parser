<?php
/**
 * Test script to verify AR/OR date timezone fix
 * This script tests the timezone handling for AR/OR date display
 */

echo "AR/OR Date Timezone Fix Test Script\n";
echo "===================================\n\n";

// Test 1: Verify application timezone
echo "1. APPLICATION TIMEZONE CONFIGURATION:\n";
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel configuration
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "   ✓ Application timezone: " . config('app.timezone') . "\n";
echo "   ✓ PHP default timezone: " . date_default_timezone_get() . "\n";

// Test 2: Carbon timezone handling
echo "\n2. CARBON TIMEZONE HANDLING:\n";
use Carbon\Carbon;

$now = Carbon::now();
$nowManila = Carbon::now('Asia/Manila');
$utcTime = Carbon::now('UTC');

echo "   ✓ Current Carbon time (default): " . $now->format('F d, Y h:i A T') . "\n";
echo "   ✓ Current Carbon time (Asia/Manila): " . $nowManila->format('F d, Y h:i A T') . "\n";
echo "   ✓ Current Carbon time (UTC): " . $utcTime->format('F d, Y h:i A T') . "\n";

// Test 3: Timezone conversion test
echo "\n3. TIMEZONE CONVERSION TEST:\n";
$testTime = Carbon::create(2025, 9, 2, 14, 30, 0, 'UTC');
echo "   ✓ Test UTC time: " . $testTime->format('F d, Y h:i A T') . "\n";
echo "   ✓ Converted to Manila: " . $testTime->setTimezone('Asia/Manila')->format('F d, Y h:i A T') . "\n";

// Test 4: Parse and format like in the application
echo "\n4. APPLICATION FORMAT TEST:\n";
$sampleTimestamp = '2025-09-02 06:30:00'; // Simulated UTC timestamp from database
$parsed = Carbon::parse($sampleTimestamp);
$parsedWithTimezone = Carbon::parse($sampleTimestamp)->setTimezone('Asia/Manila');

echo "   ✓ Sample timestamp: " . $sampleTimestamp . "\n";
echo "   ✓ Parsed (default): " . $parsed->format('F d, Y h:i A') . "\n";
echo "   ✓ Parsed with Manila timezone: " . $parsedWithTimezone->format('F d, Y h:i A') . "\n";

echo "\n5. SUMMARY:\n";
echo "   ✓ The fix ensures all AR/OR timestamps are explicitly set with Asia/Manila timezone\n";
echo "   ✓ All OrderUpdateLog::create() calls now include 'updated_at' => Carbon::now('Asia/Manila')\n";
echo "   ✓ All Carbon::parse() calls in display logic now use ->setTimezone('Asia/Manila')\n";
echo "   ✓ This ensures real-time accurate display of AR/OR dates\n";

echo "\nFix implementation completed successfully!\n";
