<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\Ship;
use App\Models\CrewLeave;
use App\Models\CrewDeleteLog;
use App\Models\CrewDocument;
use App\Exports\CrewExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class CrewController extends Controller
{
    public function index(Request $request)
    {
        $ships = Ship::all();
        $selectedShip = $request->get('ship');
        $selectedDepartment = $request->get('department');
        $search = $request->get('search');

        $crews = Crew::with(['ship', 'documents', 'leaves'])
            ->when($selectedShip, function ($query) use ($selectedShip) {
                $query->where('ship_id', $selectedShip);
            })
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                $query->where('department', $selectedDepartment);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('employee_id', 'LIKE', "%{$search}%")
                      ->orWhere('position', 'LIKE', "%{$search}%")
                      ->orWhere('department', 'LIKE', "%{$search}%")
                      ->orWhere('employment_status', 'LIKE', "%{$search}%")
                      ->orWhereHas('ship', function ($shipQuery) use ($search) {
                          $shipQuery->where('ship_number', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->paginate(20);

        return view('crew.index', compact('crews', 'ships', 'selectedShip', 'selectedDepartment', 'search'));
    }

    public function create()
    {
        $ships = Ship::all();
        return view('crew.create', compact('ships'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'required|in:ship_crew,office_staff,operations,apprentice',
            'department' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $validDepartments = [
                        'ship_crew' => ['engine', 'deck'],
                        'office_staff' => ['manila', 'batanes'],
                        'operations' => ['manila', 'batanes'],
                        'apprentice' => ['manila', 'batanes']
                    ];
                    
                    $division = $request->input('division');
                    if ($division && isset($validDepartments[$division])) {
                        if (!in_array($value, $validDepartments[$division])) {
                            $fail('The selected department is not valid for the chosen division.');
                        }
                    }
                }
            ],
            'ship_id' => 'nullable|exists:ships,id',
            'hire_date' => 'nullable|date',
            'employment_status' => 'required|in:active,inactive,terminated,resigned',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'sss_number' => 'nullable|string|max:255',
            'pagibig_number' => 'nullable|string|max:255',
            'philhealth_number' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:255',
            'seaman_book_number' => 'nullable|string|max:255',
            'basic_safety_training' => 'nullable|date',
            'medical_certificate' => 'nullable|date',
            'dcoc_number' => 'nullable|string|max:255',
            'dcoc_expiry' => 'nullable|date',
            'marina_license_number' => 'nullable|string|max:255',
            'marina_license_expiry' => 'nullable|date',
            'contract_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
            'srn' => 'nullable|string|max:255',
        ]);

        $crew = Crew::create($validated);

        // Create default leave credits
        $currentYear = date('Y');
        CrewLeave::create([
            'crew_id' => $crew->id,
            'leave_type' => 'vacation',
            'credits' => 15,
            'year' => $currentYear,
            'notes' => 'Annual vacation leave allocation'
        ]);

        CrewLeave::create([
            'crew_id' => $crew->id,
            'leave_type' => 'sick',
            'credits' => 7,
            'year' => $currentYear,
            'notes' => 'Annual sick leave allocation'
        ]);

        return redirect()->route('crew.index')->with('success', 'Crew member created successfully');
    }

    public function show($id)
    {
        try {
            $crew = Crew::findOrFail($id);
            // Load relationships that exist, skip embarkations until table is properly set up
            $crew->load(['ship', 'documents', 'leaves', 'leaveApplications']);
            
            // TODO: Add back embarkations when table structure is fixed
            // 'embarkations.ship', 'currentEmbarkation.ship'
            
            return view('crew.show', compact('crew'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Crew member not found:', ['crew_id' => $id]);
            return redirect()->route('crew.index')->with('error', 'Crew member not found. The record may have been deleted or does not exist.');
        } catch (\Exception $e) {
            Log::error('Error loading crew details:', [
                'crew_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('crew.index')->with('error', 'Error loading crew member details: ' . $e->getMessage());
        }
    }

    public function edit(Crew $crew)
    {
        $ships = Ship::all();
        return view('crew.edit', compact('crew', 'ships'));
    }

    public function update(Request $request, Crew $crew)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|string|unique:crews,employee_id,' . $crew->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'required|in:ship_crew,office_staff,operations,apprentice',
            'department' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $validDepartments = [
                        'ship_crew' => ['engine', 'deck'],
                        'office_staff' => ['manila', 'batanes'],
                        'operations' => ['manila', 'batanes'],
                        'apprentice' => ['manila', 'batanes']
                    ];
                    
                    $division = $request->input('division');
                    if ($division && isset($validDepartments[$division])) {
                        if (!in_array($value, $validDepartments[$division])) {
                            $fail('The selected department is not valid for the chosen division.');
                        }
                    }
                }
            ],
            'ship_id' => 'nullable|exists:ships,id',
            'hire_date' => 'nullable|date',
            'employment_status' => 'required|in:active,inactive,terminated,resigned',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'sss_number' => 'nullable|string|max:255',
            'pagibig_number' => 'nullable|string|max:255',
            'philhealth_number' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:255',
            'seaman_book_number' => 'nullable|string|max:255',
            'basic_safety_training' => 'nullable|date',
            'medical_certificate' => 'nullable|date',
            'dcoc_number' => 'nullable|string|max:255',
            'dcoc_expiry' => 'nullable|date',
            'marina_license_number' => 'nullable|string|max:255',
            'marina_license_expiry' => 'nullable|date',
            'contract_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $crew->update($validated);

        return redirect()->route('crew.index')->with('success', 'Crew member updated successfully');
    }

    public function destroy(Crew $crew)
    {
        try {
            Log::info('Delete crew request received', [
                'crew_id' => $crew->id,
                'employee_id' => $crew->employee_id,
                'full_name' => $crew->full_name
            ]);

            // Check if crew is already deleted
            if ($crew->trashed()) {
                return redirect()->route('crew.index')->with('error', 'Crew member is already deleted.');
            }

            // Get authenticated user for logging
            $user = Auth::user();
            $deletedBy = $user ? $user->fName . ' ' . $user->lName : 'Unknown User';
            
            // Get ship name for logging
            $shipName = $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore';
            
            // Load related data before deletion
            $crew->load(['documents', 'leaves', 'leaveApplications']);
            
            // Create delete log before actual deletion
            // Handle null employee_id by using a generated placeholder
            $employeeIdForLog = $crew->employee_id ?? 'NO-ID-' . $crew->id;
            
            $deleteLog = CrewDeleteLog::create([
                'crew_id' => $crew->id,
                'employee_id' => $employeeIdForLog,
                'full_name' => $crew->full_name,
                'position' => $crew->position,
                'department' => $crew->department,
                'ship_name' => $shipName,
                'employment_status' => $crew->employment_status,
                'deleted_by' => $deletedBy,
                'crew_data' => $crew->toArray(), // Store complete crew data for restore
                'documents_data' => $crew->documents->toArray(), // Store documents data for restore
                'leaves_data' => $crew->leaves->toArray(), // Store leave credits data for restore
            ]);
            
            Log::info('Delete log created', ['log_id' => $deleteLog->id]);
            
            // Soft delete the crew record
            $crew->delete();
            
            Log::info('Crew soft deleted successfully', ['crew_id' => $crew->id]);
            
            return redirect()->route('crew.index')->with('success', 'Crew member deleted successfully. Record can be restored if needed.');
            
        } catch (\Exception $e) {
            Log::error('Error deleting crew member:', [
                'crew_id' => $crew->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('crew.index')->with('error', 'Error deleting crew member: ' . $e->getMessage());
        }
    }

    public function transfer(Request $request, Crew $crew)
    {
        $validated = $request->validate([
            'ship_id' => 'nullable|exists:ships,id',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if trying to transfer to the same ship
        if ($crew->ship_id == $validated['ship_id']) {
            return redirect()->back()->with('error', 'Crew member is already assigned to this ship.');
        }

        $oldShip = $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore';
        
        // Update ship assignment
        $crew->update(['ship_id' => $validated['ship_id']]);
        
        // Refresh the crew instance to get updated ship info
        $crew->refresh();
        $newShip = $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore';

        // Create transfer note
        $transferNote = "Transferred from {$oldShip} to {$newShip} on " . now()->format('M d, Y \a\t H:i');
        if (!empty($validated['notes'])) {
            $transferNote .= '. Note: ' . $validated['notes'];
        }

        // Append to existing notes
        $existingNotes = $crew->notes ? $crew->notes . "\n\n" : '';
        $crew->update(['notes' => $existingNotes . $transferNote]);

        return redirect()->back()->with('success', "Crew member {$crew->full_name} transferred successfully from {$oldShip} to {$newShip}.");
    }

    public function updateStatus(Request $request, Crew $crew)
    {
        $validated = $request->validate([
            'employment_status' => 'required|in:active,inactive,terminated,resigned',
            'status_change_reason' => 'nullable|string|max:500'
        ]);

        $oldStatus = $crew->employment_status;
        $newStatus = $validated['employment_status'];

        // Update the status
        $crew->update(['employment_status' => $newStatus]);

        // Add status change note
        $statusChangeNote = "Status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . " on " . date('Y-m-d H:i:s');
        
        if ($validated['status_change_reason']) {
            $statusChangeNote .= '. Reason: ' . $validated['status_change_reason'];
        }

        // Append to existing notes
        $existingNotes = $crew->notes ? $crew->notes . "\n" : '';
        $crew->update(['notes' => $existingNotes . $statusChangeNote]);

        return redirect()->back()->with('success', "Crew member status updated to " . ucfirst($newStatus) . " successfully");
    }

    public function restore($deleteLogId)
    {
        try {
            $deleteLog = CrewDeleteLog::findOrFail($deleteLogId);
            
            // Check if already restored
            if ($deleteLog->restored_at) {
                return redirect()->back()->with('error', 'This crew member has already been restored on ' . $deleteLog->restored_at->format('M d, Y H:i') . ' by ' . $deleteLog->restored_by);
            }
            
            // Check if we have crew data to restore
            if (!$deleteLog->crew_data) {
                return redirect()->back()->with('error', 'Cannot restore crew member: No crew data found in delete log!');
            }
            
            $user = Auth::user();
            $restoredBy = $user ? $user->fName . ' ' . $user->lName : 'Unknown User';
            
            // Start database transaction
            DB::beginTransaction();
            
            // Restore the crew member
            $crewData = $deleteLog->crew_data;
            
            // Ensure crewData is an array
            if (!is_array($crewData)) {
                return redirect()->back()->with('error', 'Cannot restore crew member: Invalid crew data format!');
            }
            
            // Remove fields that shouldn't be copied
            unset($crewData['id']); // Remove the original ID to create a new one
            unset($crewData['created_at']); // Let Laravel set new timestamps
            unset($crewData['updated_at']);
            unset($crewData['deleted_at']); // Remove soft delete timestamp
            
            $restoredCrew = Crew::create($crewData);
            
            // Restore the documents
            if ($deleteLog->documents_data && is_array($deleteLog->documents_data)) {
                foreach ($deleteLog->documents_data as $documentData) {
                    if (is_array($documentData)) {
                        // Remove fields that shouldn't be copied
                        unset($documentData['id']); // Remove the original ID
                        unset($documentData['created_at']);
                        unset($documentData['updated_at']);
                        unset($documentData['deleted_at']); // Remove soft delete timestamp
                        $documentData['crew_id'] = $restoredCrew->id; // Link to new crew
                        
                        CrewDocument::create($documentData);
                    }
                }
            }
            
            // Restore the leave credits
            if ($deleteLog->leaves_data && is_array($deleteLog->leaves_data)) {
                foreach ($deleteLog->leaves_data as $leaveData) {
                    if (is_array($leaveData)) {
                        // Remove fields that shouldn't be copied
                        unset($leaveData['id']); // Remove the original ID
                        unset($leaveData['created_at']);
                        unset($leaveData['updated_at']);
                        $leaveData['crew_id'] = $restoredCrew->id; // Link to new crew
                        
                        CrewLeave::create($leaveData);
                    }
                }
            }
            
            // Update the delete log to mark as restored
            $deleteLog->update([
                'restored_at' => now(),
                'restored_by' => $restoredBy,
                'restored_crew_id' => $restoredCrew->id,
            ]);
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->back()->with('success', 'Crew member "' . $deleteLog->full_name . '" restored successfully! New crew ID: ' . $restoredCrew->id . '. Employee ID: ' . ($restoredCrew->employee_id ?: 'Not set'));
            
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollback();
            
            Log::error('Error restoring crew member:', [
                'delete_log_id' => $deleteLogId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'crew_data' => $deleteLog->crew_data ?? null,
                'documents_data' => $deleteLog->documents_data ?? null,
                'leaves_data' => $deleteLog->leaves_data ?? null,
            ]);
            
            return redirect()->back()->with('error', 'Error restoring crew member: ' . $e->getMessage());
        }
    }

    public function deletedList()
    {
        $deletedCrew = CrewDeleteLog::whereNull('restored_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('crew.deleted', compact('deletedCrew'));
    }

    public function exportPdf(Request $request)
    {
        $shipId = $request->get('ship');
        $division = $request->get('division');
        $department = $request->get('department');
        
        $query = Crew::with(['ship']);
        $title = '';
        
        if ($shipId) {
            $query->where('ship_id', $shipId);
            $ship = Ship::find($shipId);
            $title = $ship ? "Crew List - MV EVERWIN STAR {$ship->ship_number}" : "Crew List";
        } elseif ($department === 'office_shore') {
            // Export all office/shore personnel (ship_id is null)
            $query->whereNull('ship_id');
            $title = "Office/Shore Personnel";
        } elseif ($division && $department) {
            // Export specific division and department
            $query->where('division', $division)->where('department', $department);
            $title = ucfirst(str_replace('_', ' ', $division)) . " - " . ucfirst($department);
        } else {
            $title = "All Crew Members";
        }
        
        $crews = $query->orderBy('division')->orderBy('department')->orderBy('last_name')->get();
        
        $pdf = Pdf::loadView('crew.pdf', compact('crews', 'title'));
        
        // Generate filename based on export type
        if ($shipId) {
            $filename = "crew-list-ship-{$shipId}.pdf";
        } elseif ($department === 'office_shore') {
            $filename = "crew-list-office-shore.pdf";
        } elseif ($division && $department) {
            $filename = "crew-list-{$division}-{$department}.pdf";
        } else {
            $filename = "all-crew-list.pdf";
        }
        
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $shipId = $request->get('ship');
        $division = $request->get('division');
        $department = $request->get('department');
        
        $query = Crew::with(['ship']);
        
        if ($shipId) {
            $query->where('ship_id', $shipId);
            $ship = Ship::find($shipId);
            $filename = $ship ? "crew-list-ship-{$ship->ship_number}.xlsx" : "crew-list.xlsx";
        } elseif ($department === 'office_shore') {
            // Export all office/shore personnel (ship_id is null)
            $query->whereNull('ship_id');
            $filename = "crew-list-office-shore.xlsx";
        } elseif ($division && $department) {
            // Export specific division and department
            $query->where('division', $division)->where('department', $department);
            $filename = "crew-list-{$division}-{$department}.xlsx";
        } else {
            $filename = "all-crew-list.xlsx";
        }
        
        $crews = $query->orderBy('division')->orderBy('department')->orderBy('last_name')->get();
        
        return Excel::download(new CrewExport($crews), $filename);
    }
}
