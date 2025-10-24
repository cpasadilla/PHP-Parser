<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewDocument;
use App\Models\CrewDocumentDeleteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CrewDocumentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $documentType = $request->get('document_type');

        $documents = CrewDocument::with(['crew', 'uploadedBy', 'verifiedBy'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('crew', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('employee_id', 'LIKE', "%{$search}%");
                })
                ->orWhere('document_name', 'LIKE', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($documentType, function ($query) use ($documentType) {
                $query->where('document_type', $documentType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Check for expiring documents
        $expiringDocuments = CrewDocument::expiringSoon()->with('crew')->get();
        $expiredDocuments = CrewDocument::expired()->with('crew')->get();

        // Get crews for filter dropdown
        $crews = Crew::select('id', 'first_name', 'last_name', 'employee_id')
            ->orderBy('first_name')
            ->get();

        // Get document types
        $documentTypes = CrewDocument::DOCUMENT_TYPES;

        return view('crew-documents.index', compact('documents', 'expiringDocuments', 'expiredDocuments', 'search', 'status', 'documentType', 'crews', 'documentTypes'));
    }

    public function create(Request $request)
    {
        $crewId = $request->get('crew_id');
        $crew = $crewId ? Crew::findOrFail($crewId) : null;
        $crews = Crew::active()->orderBy('first_name')->get();
        $selectedDocumentType = $request->get('document_type');
        
        return view('crew-documents.create', compact('crew', 'crews', 'selectedDocumentType'));
    }

    public function store(Request $request)
    {
        // Dynamic validation based on document type
        $rules = [
            'crew_id' => 'required|exists:crews,id',
            'document_type' => 'required|in:' . implode(',', array_keys(CrewDocument::DOCUMENT_TYPES)),
            'document_name' => 'required|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string'
        ];

        // Set file validation based on document type
        if ($request->document_type === 'id_picture') {
            $rules['document_file'] = 'required|file|mimes:jpg,jpeg,png|max:5120'; // 5MB for images
        } else {
            $rules['document_file'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240'; // 10MB for documents
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('crew-documents', $fileName, 'public');

            CrewDocument::create([
                'crew_id' => $validated['crew_id'],
                'document_type' => $validated['document_type'],
                'document_name' => $validated['document_name'],
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
                'expiry_date' => $validated['expiry_date'],
                'status' => 'pending',
                'uploaded_by' => Auth::id(),
                'notes' => $validated['notes']
            ]);
        }

        return redirect()->route('crew.show', $validated['crew_id'])->with('success', 'Document uploaded successfully');
    }

    public function show(CrewDocument $crewDocument)
    {
        $crewDocument->load(['crew', 'uploadedBy', 'verifiedBy']);
        return view('crew-documents.show', compact('crewDocument'));
    }

    public function edit(CrewDocument $crewDocument)
    {
        $crews = Crew::active()->orderBy('first_name')->get();
        return view('crew-documents.edit', compact('crewDocument', 'crews'));
    }

    public function update(Request $request, CrewDocument $crewDocument)
    {
        $validated = $request->validate([
            'crew_id' => 'required|exists:crews,id',
            'document_type' => 'required|in:' . implode(',', array_keys(CrewDocument::DOCUMENT_TYPES)),
            'document_name' => 'required|string|max:255',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string'
        ]);

        if ($request->hasFile('document_file')) {
            // Delete old file
            if ($crewDocument->file_path) {
                Storage::disk('public')->delete($crewDocument->file_path);
            }

            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('crew-documents', $fileName, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $fileName;
            $validated['file_size'] = $file->getSize();
            $validated['status'] = 'pending'; // Reset status when new file is uploaded
        }

        $crewDocument->update($validated);

        return redirect()->route('crew.show', $crewDocument->crew_id)->with('success', 'Document updated successfully');
    }

    public function destroy(CrewDocument $crewDocument)
    {
        try {
            // Get authenticated user for logging
            $user = Auth::user();
            $deletedBy = $user ? $user->fName . ' ' . $user->lName : 'Unknown User';
            
            // Load crew information
            $crewDocument->load('crew');
            
            // Create delete log before actual deletion
            CrewDocumentDeleteLog::create([
                'document_id' => $crewDocument->id,
                'crew_id' => $crewDocument->crew_id,
                'crew_name' => $crewDocument->crew ? $crewDocument->crew->full_name : 'Unknown',
                'employee_id' => $crewDocument->crew ? $crewDocument->crew->employee_id : 'Unknown',
                'document_type' => $crewDocument->document_type,
                'document_name' => $crewDocument->document_name,
                'file_name' => $crewDocument->file_name,
                'file_path' => $crewDocument->file_path,
                'expiry_date' => $crewDocument->expiry_date,
                'status' => $crewDocument->status,
                'deleted_by' => $deletedBy,
                'document_data' => $crewDocument->toArray(), // Store complete document data for restore
            ]);
            
            // Note: We don't delete the actual file yet, in case we need to restore
            // The file will be deleted only when permanently removing or after a certain period
            
            // Soft delete the document record
            $crewDocument->delete();
            
            return redirect()->route('crew-documents.index')->with('success', 'Document deleted successfully. Document can be restored if needed.');
            
        } catch (\Exception $e) {
            Log::error('Error deleting crew document:', [
                'document_id' => $crewDocument->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('crew-documents.index')->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }

    public function verify(Request $request, CrewDocument $crewDocument)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string'
        ]);

        $crewDocument->update([
            'status' => $validated['status'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'notes' => $validated['notes']
        ]);

        $statusText = $validated['status'] === 'verified' ? 'verified' : 'rejected';
        return redirect()->back()->with('success', "Document {$statusText} successfully");
    }

    public function download(CrewDocument $crewDocument)
    {
        if (!$crewDocument->file_path || !Storage::disk('public')->exists($crewDocument->file_path)) {
            return redirect()->back()->with('error', 'File not found');
        }

        return Storage::disk('public')->download($crewDocument->file_path, $crewDocument->file_name);
    }

    public function expiring()
    {
        $expiringDocuments = CrewDocument::expiringSoon(30)->with(['crew', 'uploadedBy'])->get();
        $expiredDocuments = CrewDocument::expired()->with(['crew', 'uploadedBy'])->get();

        return view('crew-documents.expiring', compact('expiringDocuments', 'expiredDocuments'));
    }

    public function viewFile(CrewDocument $crewDocument)
    {
        if (!$crewDocument->file_path || !Storage::disk('public')->exists($crewDocument->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('public')->path($crewDocument->file_path);
        $mimeType = Storage::disk('public')->mimeType($crewDocument->file_path);
        
        // Set appropriate headers for inline viewing
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $crewDocument->file_name . '"'
        ]);
    }

    public function restore($deleteLogId)
    {
        try {
            $deleteLog = CrewDocumentDeleteLog::findOrFail($deleteLogId);
            
            // Check if already restored
            if ($deleteLog->restored_at) {
                return redirect()->back()->with('error', 'This document has already been restored!');
            }
            
            // Check if we have document data to restore
            if (!$deleteLog->document_data) {
                return redirect()->back()->with('error', 'Cannot restore document: No document data found in delete log!');
            }
            
            $user = Auth::user();
            $restoredBy = $user ? $user->fName . ' ' . $user->lName : 'Unknown User';
            
            // Restore the document
            $documentData = $deleteLog->document_data;
            
            // Ensure documentData is an array
            if (!is_array($documentData)) {
                return redirect()->back()->with('error', 'Cannot restore document: Invalid document data format!');
            }
            
            // Remove fields that shouldn't be copied
            unset($documentData['id']); // Remove the original ID to create a new one
            unset($documentData['created_at']); // Let Laravel set new timestamps
            unset($documentData['updated_at']);
            unset($documentData['deleted_at']); // Remove soft delete timestamp
            
            $restoredDocument = CrewDocument::create($documentData);
            
            // Update the delete log to mark as restored
            $deleteLog->update([
                'restored_at' => now(),
                'restored_by' => $restoredBy,
                'restored_document_id' => $restoredDocument->id,
            ]);
            
            return redirect()->back()->with('success', 'Document restored successfully! New Document ID: ' . $restoredDocument->id);
            
        } catch (\Exception $e) {
            Log::error('Error restoring crew document:', [
                'delete_log_id' => $deleteLogId,
                'error' => $e->getMessage(),
                'document_data' => $deleteLog->document_data ?? null,
            ]);
            
            return redirect()->back()->with('error', 'Error restoring document: ' . $e->getMessage());
        }
    }

    public function deletedList()
    {
        $deletedDocuments = CrewDocumentDeleteLog::whereNull('restored_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('crew-documents.deleted', compact('deletedDocuments'));
    }
}
