<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ship;
use App\Models\voyage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch all ships to ensure even those without orders are displayed
        $ships = Ship::all();
        $shipNumbers = $ships->pluck('ship_number')->toArray();

        // Get voyage data from the voyages table first (primary source)
        $voyageMap = [];
        foreach ($shipNumbers as $shipNum) {
            $voyageMap[$shipNum] = voyage::where('ship', $shipNum)
                ->select('v_num', 'inOut')
                ->get()
                ->map(function($voyage) {
                    // Format the voyage number with directional suffix if it exists
                    if (!empty($voyage->inOut)) {
                        return ['v_num' => $voyage->v_num . '-' . $voyage->inOut];
                    } else {
                        return ['v_num' => $voyage->v_num];
                    }
                })
                ->toArray();
        }

        // As a fallback, also get voyage numbers from orders if they're not in the voyages table
        $orderVoyages = Order::select('voyageNum', 'shipNum')
            ->whereNotNull('shipNum')
            ->whereNotNull('voyageNum')
            ->distinct()
            ->get();
        
        // Add any voyages from orders that aren't already in the voyage map
        foreach ($orderVoyages as $orderVoyage) {
            $shipNum = $orderVoyage->shipNum;
            $voyageNum = $orderVoyage->voyageNum;
            
            // Skip if this ship number doesn't exist in our mapping
            if (!isset($voyageMap[$shipNum])) {
                $voyageMap[$shipNum] = [];
            }
            
            // Check if this voyage is already in the map
            $voyageExists = false;
            foreach ($voyageMap[$shipNum] as $existingVoyage) {
                // Check exact match
                if ($existingVoyage['v_num'] === $voyageNum) {
                    $voyageExists = true;
                    break;
                }
                
                // Check if it's the same voyage but with/without direction suffix
                $baseExistingVoyage = preg_replace('/-.*$/', '', $existingVoyage['v_num']);
                $baseVoyageNum = preg_replace('/-.*$/', '', $voyageNum);
                
                if ($baseExistingVoyage === $baseVoyageNum) {
                    $voyageExists = true;
                    break;
                }
            }
            
            // If not in map, add it
            if (!$voyageExists) {
                $voyageMap[$shipNum][] = ['v_num' => $voyageNum];
            }
        }

        // Fetch Paid and Unpaid BL per Ship per Voyage
        $blStatusData = Order::select('shipNum', 'voyageNum', 'blStatus', DB::raw('COUNT(*) as count'))
            ->whereNotNull('shipNum')
            ->whereNotNull('voyageNum')
            ->groupBy('shipNum', 'voyageNum', 'blStatus')
            ->get()
            ->groupBy('shipNum');

        // Fetch Earnings per Ship per Voyage
        $earningsData = Order::select('shipNum', 'voyageNum', DB::raw('SUM(totalAmount) as earnings'))
            ->whereNotNull('shipNum')
            ->whereNotNull('voyageNum')
            ->groupBy('shipNum', 'voyageNum')
            ->get()
            ->groupBy('shipNum');

        // Fetch Number of BL per Ship per Voyage - improved query for accuracy
        $blCountData = Order::select('shipNum', 'voyageNum', DB::raw('COUNT(*) as count'))
            ->whereNotNull('shipNum')
            ->whereNotNull('voyageNum')
            ->groupBy('shipNum', 'voyageNum')
            ->get()
            ->groupBy('shipNum');
        
        // Get total count of BLs per ship (for pie chart accuracy)
        $totalBlPerShip = [];
        foreach ($shipNumbers as $shipNum) {
            $totalBlPerShip[$shipNum] = Order::where('shipNum', $shipNum)->count();
        }

        // Pass data to the view
        return view('dashboard', compact('ships', 'shipNumbers', 'voyageMap', 'blStatusData', 'earningsData', 'blCountData', 'totalBlPerShip'));
    }
}