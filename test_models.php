<?php

use Illuminate\Foundation\Testing\TestCase;
use App\Models\Ship;
use App\Models\order;
use App\Models\voyage;

class ControllerTest extends TestCase
{
    public function test_dashboard_models()
    {
        // Test if we can access the models
        try {
            $ships = Ship::all();
            echo "Ships model: OK (found " . $ships->count() . " ships)\n";
        } catch (Exception $e) {
            echo "Ships model failed: " . $e->getMessage() . "\n";
        }

        try {
            $orders = order::all();
            echo "Orders model: OK (found " . $orders->count() . " orders)\n";
        } catch (Exception $e) {
            echo "Orders model failed: " . $e->getMessage() . "\n";
        }

        try {
            $voyages = voyage::all();
            echo "Voyages model: OK (found " . $voyages->count() . " voyages)\n";
        } catch (Exception $e) {
            echo "Voyages model failed: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new ControllerTest();
$test->test_dashboard_models();

?>
