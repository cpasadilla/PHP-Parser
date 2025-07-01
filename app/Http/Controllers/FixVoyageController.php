<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ship;
use App\Models\voyage;
use App\Models\order;

class FixVoyageController extends Controller
{
    /**
     * Quick diagnosis of the voyage status issue
     */
    public function checkVoyageStatus()
    {
        // Collect diagnostic information
        $diagnosticData = [
            'shipI_voyages' => voyage::where('ship', 'I')->orderBy('v_num')->get(),
            'shipII_voyages' => voyage::where('ship', 'II')->orderBy('v_num')->get(),
            'shipI_status' => Ship::where('ship_number', 'I')->first(),
            'shipII_status' => Ship::where('ship_number', 'II')->first(),
            'orders_shipI' => order::where('shipNum', 'I')->orderBy('created_at', 'desc')->limit(5)->get(),
            'orders_shipII' => order::where('shipNum', 'II')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];
        
        return view('fix-voyage', compact('diagnosticData'));
    }

    /**
     * Fix the voyage status issue by correctly ordering voyages
     */
    public function fixVoyageStatus()
    {
        // Update all voyages with STOP status to prioritize READY ones
        $ships = ['I', 'II', 'III', 'IV', 'V'];
        $updated = 0;

        foreach ($ships as $ship) {
            // Get all voyages for this ship
            $voyages = voyage::where('ship', $ship)->get();
            
            // Group by v_num (numeric part)
            $voyageGroups = [];
            foreach ($voyages as $voyage) {
                $vNum = $voyage->v_num;
                if (!isset($voyageGroups[$vNum])) {
                    $voyageGroups[$vNum] = [];
                }
                $voyageGroups[$vNum][] = $voyage;
            }
            
            // For each group, make sure they have consistent statuses
            foreach ($voyageGroups as $vNum => $voyagesInGroup) {
                // If any voyage in the group is READY, they should all be READY
                $hasReady = false;
                foreach ($voyagesInGroup as $voyage) {
                    if ($voyage->lastStatus === 'READY') {
                        $hasReady = true;
                        break;
                    }
                }
                
                // Update all voyages in the group to have the same status
                foreach ($voyagesInGroup as $voyage) {
                    if ($hasReady && $voyage->lastStatus !== 'READY') {
                        $voyage->lastStatus = 'READY';
                        $voyage->lastUpdated = now();
                        $voyage->save();
                        $updated++;
                    } elseif (!$hasReady && $voyage->lastStatus !== 'STOP') {
                        $voyage->lastStatus = 'STOP';
                        $voyage->lastUpdated = now();
                        $voyage->save();
                        $updated++;
                    }
                }
            }
        }
        
        return redirect()->back()->with('success', "Fixed $updated voyage status inconsistencies. New orders should now use the correct voyage numbers.");
    }
}
