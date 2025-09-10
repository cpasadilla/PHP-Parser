<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Crew;
use App\Models\CrewLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveCreditsController extends Controller
{
    /**
     * Display a listing of crew members with their leave credits.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $department = $request->get('department', '');
        $year = $request->get('year', date('Y'));
        
        $crews = Crew::with(['leaves' => function($query) use ($year) {
                $query->where('year', $year);
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('employee_id', 'LIKE', "%{$search}%")
                      ->orWhere('position', 'LIKE', "%{$search}%");
                });
            })
            ->when($department, function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->where('employment_status', 'active')
            ->orderBy('employee_id')
            ->paginate(20);

        $departments = Crew::distinct()->pluck('department')->filter()->sort();
        
        return view('leave-credits.index', compact('crews', 'search', 'department', 'year', 'departments'));
    }

    /**
     * Show the form for editing leave credits for a specific crew member.
     */
    public function edit(Crew $crew)
    {
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1); // Previous 2 years and next year
        
        // Get leave credits for all years
        $leaveCredits = CrewLeave::where('crew_id', $crew->id)
            ->orderBy('year', 'desc')
            ->orderBy('leave_type')
            ->get()
            ->groupBy('year');
            
        return view('leave-credits.edit', compact('crew', 'leaveCredits', 'years', 'currentYear'));
    }

    /**
     * Update leave credits for a crew member.
     */
    public function update(Request $request, Crew $crew)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'leave_credits' => 'required|array',
            'leave_credits.*.leave_type' => 'required|string',
            'leave_credits.*.credits' => 'required|numeric|min:0|max:365',
            'leave_credits.*.notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $crew) {
            $year = $request->year;
            
            // Delete existing leave credits for this year
            CrewLeave::where('crew_id', $crew->id)
                ->where('year', $year)
                ->delete();
            
            // Create new leave credits
            foreach ($request->leave_credits as $leaveData) {
                if ($leaveData['credits'] > 0) { // Only create if credits > 0
                    CrewLeave::create([
                        'crew_id' => $crew->id,
                        'year' => $year,
                        'leave_type' => $leaveData['leave_type'],
                        'credits' => $leaveData['credits'],
                        'notes' => $leaveData['notes'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('leave-credits.edit', $crew)
            ->with('success', "Leave credits for {$request->year} updated successfully!");
    }

    /**
     * Bulk update leave credits for multiple crew members.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'leave_type' => 'required|string',
            'credits' => 'required|numeric|min:0|max:365',
            'department' => 'nullable|string',
            'crew_ids' => 'nullable|array',
            'crew_ids.*' => 'exists:crews,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $query = Crew::where('employment_status', 'active');
        
        if ($request->department) {
            $query->where('department', $request->department);
        }
        
        if ($request->crew_ids) {
            $query->whereIn('id', $request->crew_ids);
        }
        
        $crews = $query->get();
        
        DB::transaction(function () use ($request, $crews) {
            foreach ($crews as $crew) {
                // Check if leave credit already exists for this year and type
                $existingCredit = CrewLeave::where('crew_id', $crew->id)
                    ->where('year', $request->year)
                    ->where('leave_type', $request->leave_type)
                    ->first();
                
                if ($existingCredit) {
                    // Update existing
                    $existingCredit->update([
                        'credits' => $request->credits,
                        'notes' => $request->notes,
                    ]);
                } else {
                    // Create new
                    CrewLeave::create([
                        'crew_id' => $crew->id,
                        'year' => $request->year,
                        'leave_type' => $request->leave_type,
                        'credits' => $request->credits,
                        'notes' => $request->notes,
                    ]);
                }
            }
        });

        $count = $crews->count();
        return redirect()
            ->route('leave-credits.index')
            ->with('success', "Leave credits updated for {$count} crew members!");
    }

    /**
     * Show bulk update form.
     */
    public function bulkEdit()
    {
        $departments = Crew::distinct()->pluck('department')->filter()->sort();
        $crews = Crew::where('employment_status', 'active')
            ->orderBy('employee_id')
            ->get();
            
        return view('leave-credits.bulk-edit', compact('departments', 'crews'));
    }
}
