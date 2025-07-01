<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class LocationsController extends Controller
{
    /**
     * Store a new location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|regex:/^[a-zA-Z\s]+$/',
        ]);

        if ($validator->fails()) {
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to add location: ' . $validator->errors()->first());
        }

        // Get the location name
        $locationName = strtoupper(trim($request->input('location')));        // Check if the location already exists
        $exists = DB::table('locations')->where('location', $locationName)->exists();
        if ($exists) {
            return Redirect::route('profile.edit')
                ->with('error', "Location '{$locationName}' already exists.");
        }

        // Insert the new location
        try {
            DB::table('locations')->insert([
                'location' => $locationName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Redirect::route('profile.edit')
                ->with('status', 'location-added')
                ->with('message', "Location '{$locationName}' has been added.");
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to add location: ' . $e->getMessage());
        }
    }
}
