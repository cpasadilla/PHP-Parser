<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class CheckersController extends Controller
{
    /**
     * Store a new checker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'location' => 'required|string|exists:locations,location',
        ]);

        if ($validator->fails()) {
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to add checker: ' . $validator->errors()->first());
        }

        // Get the name and location
        $name = trim($request->input('name'));
        $location = $request->input('location');

        // Check if the checker already exists for this location
        $exists = DB::table('checkers')
            ->where('name', $name)
            ->where('location', $location)
            ->exists();

        if ($exists) {
            return Redirect::route('profile.edit')
                ->with('error', "Checker '{$name}' already exists for location '{$location}'.");
        }

        // Insert the new checker
        try {
            DB::table('checkers')->insert([
                'name' => $name,
                'location' => $location,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Redirect::route('profile.edit')
                ->with('status', 'checker-added')
                ->with('message', "Checker '{$name}' has been added to location '{$location}'.");
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to add checker: ' . $e->getMessage());
        }
    }
}
