<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Crew;
use App\Models\CrewEmbarkation;
use App\Models\Ship;
use Illuminate\Http\Request;

class CrewEmbarkationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $embarkations = CrewEmbarkation::with(['crew', 'ship'])
            ->orderBy('embark_date', 'desc')
            ->paginate(20);

        return view('crew-embarkations.index', compact('embarkations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $crew = null;
        if ($request->has('crew_id')) {
            $crew = Crew::findOrFail($request->crew_id);
        }

        $crews = Crew::where('division', 'ship_crew')
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();
        
        $ships = Ship::orderBy('ship_number')->get();

        return view('crew-embarkations.create', compact('crews', 'ships', 'crew'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'crew_id' => 'required|exists:crews,id',
            'ship_id' => 'required|exists:ships,id',
            'embark_date' => 'required|date',
            'disembark_date' => 'nullable|date|after:embark_date',
            'embark_port' => 'nullable|string|max:255',
            'disembark_port' => 'nullable|string|max:255',
            'remarks' => 'nullable|string'
        ]);

        // Check if crew has active embarkation
        $activeEmbarkation = CrewEmbarkation::where('crew_id', $request->crew_id)
            ->where('status', 'active')
            ->whereNull('disembark_date')
            ->first();

        if ($activeEmbarkation) {
            return back()->withErrors(['crew_id' => 'This crew member already has an active embarkation.']);
        }

        $status = $request->disembark_date ? 'completed' : 'active';

        CrewEmbarkation::create(array_merge($request->all(), ['status' => $status]));

        return redirect()->route('crew.show', $request->crew_id)
            ->with('success', 'Embarkation record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CrewEmbarkation $crewEmbarkation)
    {
        $crewEmbarkation->load(['crew', 'ship']);
        return view('crew-embarkations.show', compact('crewEmbarkation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CrewEmbarkation $crewEmbarkation)
    {
        $crews = Crew::where('division', 'ship_crew')
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();
        
        $ships = Ship::orderBy('ship_number')->get();

        return view('crew-embarkations.edit', compact('crewEmbarkation', 'crews', 'ships'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CrewEmbarkation $crewEmbarkation)
    {
        $request->validate([
            'crew_id' => 'required|exists:crews,id',
            'ship_id' => 'required|exists:ships,id',
            'embark_date' => 'required|date',
            'disembark_date' => 'nullable|date|after:embark_date',
            'embark_port' => 'nullable|string|max:255',
            'disembark_port' => 'nullable|string|max:255',
            'remarks' => 'nullable|string'
        ]);

        $status = $request->disembark_date ? 'completed' : 'active';

        $crewEmbarkation->update(array_merge($request->all(), ['status' => $status]));

        return redirect()->route('crew.show', $crewEmbarkation->crew_id)
            ->with('success', 'Embarkation record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CrewEmbarkation $crewEmbarkation)
    {
        $crewId = $crewEmbarkation->crew_id;
        $crewEmbarkation->delete();

        return redirect()->route('crew.show', $crewId)
            ->with('success', 'Embarkation record deleted successfully.');
    }

    /**
     * Mark embarkation as disembarked
     */
    public function disembark(Request $request, CrewEmbarkation $crewEmbarkation)
    {
        $request->validate([
            'disembark_date' => 'required|date|after_or_equal:' . $crewEmbarkation->embark_date,
            'disembark_port' => 'nullable|string|max:255',
            'remarks' => 'nullable|string'
        ]);

        $crewEmbarkation->update([
            'disembark_date' => $request->disembark_date,
            'disembark_port' => $request->disembark_port,
            'remarks' => $request->remarks,
            'status' => 'completed'
        ]);

        return redirect()->route('crew.show', $crewEmbarkation->crew_id)
            ->with('success', 'Crew member disembarked successfully.');
    }
}