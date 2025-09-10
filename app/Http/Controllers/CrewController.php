<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\Ship;
use App\Models\CrewLeave;
use App\Exports\CrewExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'employee_id' => 'required|unique:crews',
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
            'hire_date' => 'required|date',
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

    public function show(Crew $crew)
    {
        $crew->load(['ship', 'documents', 'leaves', 'leaveApplications', 'embarkations.ship', 'currentEmbarkation.ship']);
        return view('crew.show', compact('crew'));
    }

    public function edit(Crew $crew)
    {
        $ships = Ship::all();
        return view('crew.edit', compact('crew', 'ships'));
    }

    public function update(Request $request, Crew $crew)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:crews,employee_id,' . $crew->id,
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
            'hire_date' => 'required|date',
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
        $crew->delete();
        return redirect()->route('crew.index')->with('success', 'Crew member deleted successfully');
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
