<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        // Get applications with crew data, handling potential orphaned records
        $applications = LeaveApplication::with(['crew', 'approvedBy'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('crew', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('employee_id', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Log any orphaned leave applications for debugging
        $orphanedCount = LeaveApplication::whereDoesntHave('crew')->count();
        if ($orphanedCount > 0) {
            \Log::warning("Found {$orphanedCount} leave applications with missing crew records");
        }

        return view('leave-applications.index', compact('applications', 'status', 'search'));
    }

    public function create(Request $request)
    {
        $crewId = $request->get('crew_id');
        $crew = $crewId ? Crew::findOrFail($crewId) : null;
        $crews = Crew::active()->orderBy('first_name')->get();
        
        return view('leave-applications.create', compact('crew', 'crews'));
    }

    public function store(Request $request)
    {
        $rules = [
            'crew_id' => 'required|exists:crews,id',
            'leave_type' => 'required|in:vacation,sick,emergency,maternity,paternity,bereavement,other',
            'date_applied' => 'required|date',
            // other_leave_type is nullable by default; required only when leave_type == 'other'
            'other_leave_type' => 'nullable|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'notes' => 'nullable|string',
            // Approval fields
            'approved_by' => 'nullable|string|max:255',
            'noted_by_captain' => 'nullable|string|max:255',
            'noted_by_manager' => 'nullable|string|max:255',
            // HR fields (only if user has permission)
            'hr_vacation_credits' => 'nullable|numeric|min:0',
            'hr_sick_credits' => 'nullable|numeric|min:0',
            'hr_filled_by' => 'nullable|string|max:255',
            'hr_title' => 'nullable|string|max:255',
            // Operations Manager fields (only if user has permission)
            'approved_days_with_pay' => 'nullable|numeric|min:0',
            'approved_days_without_pay' => 'nullable|numeric|min:0',
            'disapproved_reason' => 'nullable|string',
            'deferred_until' => 'nullable|date',
            'ops_approved_by' => 'nullable|string|max:255',
            'ops_title' => 'nullable|string|max:255',
        ];

        // If leave type is 'other', make other_leave_type required
        if ($request->input('leave_type') === 'other') {
            $rules['other_leave_type'] = 'required|string|max:255';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->all()], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

    $validated = $validator->validated();

        // Calculate days requested
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $daysRequested = $startDate->diffInDays($endDate) + 1;

        $applicationData = [
            'crew_id' => $validated['crew_id'],
            'leave_type' => $validated['leave_type'],
            'date_applied' => $validated['date_applied'] ?? date('Y-m-d'),
            'other_leave_type' => $validated['other_leave_type'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_requested' => $daysRequested,
            'reason' => $validated['reason'],
            'status' => 'pending',
            'notes' => $validated['notes'],
            // Approval fields
            'approved_by' => $validated['approved_by'] ?? null,
            'noted_by_captain' => $validated['noted_by_captain'] ?? null,
            'noted_by_manager' => $validated['noted_by_manager'] ?? null,
        ];

        // Check if user can edit HR fields
        $canEditHR = auth()->user()->roles && 
            (in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR', 'HRMO']) || 
             auth()->user()->hasPermission('crew', 'manage'));

        if ($canEditHR) {
            $applicationData['hr_vacation_credits'] = $validated['hr_vacation_credits'] ?? null;
            $applicationData['hr_sick_credits'] = $validated['hr_sick_credits'] ?? null;
            $applicationData['hr_filled_by'] = $validated['hr_filled_by'] ?? null;
            $applicationData['hr_title'] = $validated['hr_title'] ?? null;
        }

        // Check if user can edit Operations Manager fields
        $canEditOps = auth()->user()->roles && 
            (in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR', 'OPERATIONS MANAGER', 'MANAGER']) || 
             auth()->user()->hasPermission('crew', 'manage'));

        if ($canEditOps) {
            $applicationData['approved_days_with_pay'] = $validated['approved_days_with_pay'] ?? null;
            $applicationData['approved_days_without_pay'] = $validated['approved_days_without_pay'] ?? null;
            $applicationData['disapproved_reason'] = $validated['disapproved_reason'] ?? null;
            $applicationData['deferred_until'] = $validated['deferred_until'] ?? null;
            $applicationData['ops_approved_by'] = $validated['ops_approved_by'] ?? null;
            $applicationData['ops_title'] = $validated['ops_title'] ?? null;
        }

        if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('leave-documents', $fileName, 'public');
            $applicationData['file_path'] = $filePath;
        }

        LeaveApplication::create($applicationData);

        return redirect()->route('leave-applications.index')->with('success', 'Leave application submitted successfully');
    }

    /**
     * Get crew leave credits for AJAX call
     */
    public function getCrewLeaveCredits($crewId)
    {
        try {
            $crew = Crew::findOrFail($crewId);
            
            // Get current year leave credits
            $currentYear = date('Y');
            $vacationCredits = $crew->leaves()
                ->where('year', $currentYear)
                ->where('leave_type', 'vacation')
                ->sum('credits');
            
            $sickCredits = $crew->leaves()
                ->where('year', $currentYear)
                ->where('leave_type', 'sick')
                ->sum('credits');

            return response()->json([
                'success' => true,
                'vacation_credits' => $vacationCredits,
                'sick_credits' => $sickCredits,
                'crew_name' => $crew->full_name,
                'position' => $crew->position,
                'ship' => $crew->ship ? $crew->ship->name : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading crew credits'
            ]);
        }
    }

    public function show(LeaveApplication $leaveApplication)
    {
        $leaveApplication->load(['crew']);
        
        // Calculate available leave credits for the crew member
        $currentYear = date('Y');
        $crew = $leaveApplication->crew;
        
        // Get total vacation credits earned this year
        $totalVacationCredits = $crew->leaves()
            ->where('year', $currentYear)
            ->where('leave_type', 'vacation')
            ->sum('credits');
            
        // Get total sick credits earned this year
        $totalSickCredits = $crew->leaves()
            ->where('year', $currentYear)
            ->where('leave_type', 'sick')
            ->sum('credits');
            
        // Get approved vacation days used this year
        $usedVacationDays = $crew->leaveApplications()
            ->where('status', 'approved')
            ->where('leave_type', 'vacation')
            ->whereYear('start_date', $currentYear)
            ->sum('days_requested');
            
        // Get approved sick days used this year
        $usedSickDays = $crew->leaveApplications()
            ->where('status', 'approved')
            ->where('leave_type', 'sick')
            ->whereYear('start_date', $currentYear)
            ->sum('days_requested');
            
        // Calculate available credits
        $availableVacationCredits = max(0, $totalVacationCredits - $usedVacationDays);
        $availableSickCredits = max(0, $totalSickCredits - $usedSickDays);
        
        return view('leave-applications.show', compact(
            'leaveApplication', 
            'availableVacationCredits', 
            'availableSickCredits'
        ));
    }

    public function edit(LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be edited');
        }

        $crews = Crew::active()->orderBy('first_name')->get();
        return view('leave-applications.edit', compact('leaveApplication', 'crews'));
    }

    public function update(Request $request, LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be updated');
        }

        $validated = $request->validate([
            'crew_id' => 'required|exists:crews,id',
            'leave_type' => 'required|in:vacation,sick,emergency,maternity,paternity,bereavement,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'notes' => 'nullable|string'
        ]);

        // Calculate days requested
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $daysRequested = $startDate->diffInDays($endDate) + 1;

        $validated['days_requested'] = $daysRequested;

        if ($request->hasFile('supporting_document')) {
            // Delete old file if exists
            if ($leaveApplication->file_path) {
                Storage::disk('public')->delete($leaveApplication->file_path);
            }

            $file = $request->file('supporting_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('leave-documents', $fileName, 'public');
            $validated['file_path'] = $filePath;
        }

        $leaveApplication->update($validated);

        return redirect()->route('leave-applications.index')->with('success', 'Leave application updated successfully');
    }

    public function destroy(LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be deleted');
        }

        if ($leaveApplication->file_path) {
            Storage::disk('public')->delete($leaveApplication->file_path);
        }

        $leaveApplication->delete();
        return redirect()->route('leave-applications.index')->with('success', 'Leave application deleted successfully');
    }

    public function approve(Request $request, LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->status !== 'pending') {
            return redirect()->back()->with('error', 'Application has already been processed');
        }

        $crew = $leaveApplication->crew;
        $availableCredits = $crew->available_leave_credits;

        if ($leaveApplication->days_requested > $availableCredits) {
            return redirect()->back()->with('error', 'Insufficient leave credits. Available: ' . $availableCredits . ' days');
        }

        $leaveApplication->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Leave application approved successfully');
    }

    public function reject(Request $request, LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->status !== 'pending') {
            return redirect()->back()->with('error', 'Application has already been processed');
        }

        $validated = $request->validate([
            'disapproved_reason' => 'required|string'
        ]);

        $leaveApplication->update([
            'status' => 'disapproved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'disapproved_reason' => $validated['disapproved_reason']
        ]);

        return redirect()->back()->with('success', 'Leave application disapproved');
    }

    public function hrReview(Request $request, LeaveApplication $leaveApplication)
    {
        // Check if application is in the correct status for HR review
        if ($leaveApplication->status !== 'pending') {
            return redirect()->back()->with('error', 'Application has already been processed');
        }

        $validated = $request->validate([
            'hr_vacation_credits' => 'nullable|numeric|min:0',
            'hr_sick_credits' => 'nullable|numeric|min:0',
            'hr_filled_by' => 'required|string|max:255',
            'hr_title' => 'required|string|max:255',
        ]);

        $leaveApplication->update([
            'status' => 'hr_review',
            'hr_vacation_credits' => $validated['hr_vacation_credits'],
            'hr_sick_credits' => $validated['hr_sick_credits'],
            'hr_filled_by' => $validated['hr_filled_by'],
            'hr_title' => $validated['hr_title'],
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return redirect()->route('leave-applications.show', $leaveApplication)
                       ->with('success', 'Leave application moved to HR review successfully');
    }

    public function download(LeaveApplication $leaveApplication)
    {
        if (!$leaveApplication->file_path || !Storage::disk('public')->exists($leaveApplication->file_path)) {
            return redirect()->back()->with('error', 'File not found');
        }

        return Storage::disk('public')->download($leaveApplication->file_path);
    }

    public function uploadSickLeave(Request $request)
    {
        $crews = Crew::active()->orderBy('first_name')->get();
        
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'crew_id' => 'required|exists:crews,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'required|string',
                'sick_leave_form' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
                'notes' => 'nullable|string'
            ]);

            // Calculate days
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $endDate = \Carbon\Carbon::parse($validated['end_date']);
            $daysRequested = $startDate->diffInDays($endDate) + 1;

            // Upload file
            $file = $request->file('sick_leave_form');
            $fileName = time() . '_sick_leave_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('leave-documents', $fileName, 'public');

            // Create leave application
            LeaveApplication::create([
                'crew_id' => $validated['crew_id'],
                'leave_type' => 'sick',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'days_requested' => $daysRequested,
                'reason' => $validated['reason'],
                'status' => 'approved', // Auto-approve sick leave with form
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'file_path' => $filePath,
                'notes' => $validated['notes']
            ]);

            return redirect()->route('leave-applications.upload-sick-leave')->with('success', 'Sick leave form uploaded and applied successfully');
        }

        return view('leave-applications.upload-sick-leave', compact('crews'));
    }

    public function finalApproval(Request $request, LeaveApplication $leaveApplication)
    {
        // Check if application is in the correct status for final approval
        if ($leaveApplication->status !== 'hr_review') {
            return redirect()->back()->with('error', 'Application is not ready for final approval');
        }

        $action = $request->input('action');
        
        if ($action === 'approve') {
            $validated = $request->validate([
                'approved_days_with_pay' => 'nullable|numeric|min:0',
                'approved_days_without_pay' => 'nullable|numeric|min:0',
                'final_approved_by' => 'required|string|max:255',
                'final_approved_position' => 'required|string|max:255',
            ]);

            $leaveApplication->update([
                'status' => 'approved',
                'approved_days_with_pay' => $validated['approved_days_with_pay'] ?? 0,
                'approved_days_without_pay' => $validated['approved_days_without_pay'] ?? 0,
                'ops_approved_by' => $validated['final_approved_by'],
                'ops_title' => $validated['final_approved_position'],
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            return redirect()->route('leave-applications.show', $leaveApplication)
                           ->with('success', 'Leave application approved successfully');
                           
        } elseif ($action === 'disapprove') {
            $validated = $request->validate([
                'disapproved_reason' => 'required|string',
                'deferred_until' => 'nullable|date|after:today',
                'final_approved_by' => 'required|string|max:255',
                'final_approved_position' => 'required|string|max:255',
            ]);

            $status = $validated['deferred_until'] ? 'deferred' : 'disapproved';
            
            $leaveApplication->update([
                'status' => $status,
                'disapproved_reason' => $validated['disapproved_reason'],
                'deferred_until' => $validated['deferred_until'] ?? null,
                'ops_approved_by' => $validated['final_approved_by'],
                'ops_title' => $validated['final_approved_position'],
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            $statusMessage = $status === 'deferred' ? 'deferred' : 'disapproved';
            return redirect()->route('leave-applications.show', $leaveApplication)
                           ->with('success', "Leave application {$statusMessage} successfully");
        }

        return redirect()->back()->with('error', 'Invalid action');
    }
}
