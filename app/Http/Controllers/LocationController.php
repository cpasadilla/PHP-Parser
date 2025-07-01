<?php

namespace App\Http\Controllers;

use App\Models\locations;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Store a newly created location resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'location' => 'required|string|max:255|unique:locations,location',
        ]);

        try {
            // Create the new location
            locations::create([
                'location' => $request->location,
            ]);

            // Redirect with success message
            return redirect()->back()->with('status', 'location-added');
        } catch (\Exception $e) {
            // Redirect with error message
            return redirect()->back()->with('error', 'Failed to add location: ' . $e->getMessage());
        }
    }
}