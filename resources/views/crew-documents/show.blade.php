<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Document Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('crew-documents.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                    Back to Documents
                </a>
                @if($crewDocument->file_path)
                <a href="{{ route('crew-documents.download', $crewDocument) }}" 
                   class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                    Download
                </a>
                <button onclick="viewAttachedFile('{{ $crewDocument->id }}')" 
                        class="bg-indigo-500 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded">
                    View File
                </button>
                @endif
                <a href="{{ route('crew-documents.edit', $crewDocument) }}" 
                   class="bg-green-500 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-500 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-white">
                    
                    <!-- Document Status Alert -->
                    @if($crewDocument->is_expired)
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6">
                            <strong class="font-bold">⚠️ EXPIRED DOCUMENT</strong>
                            <span class="block sm:inline">This document expired {{ $crewDocument->expiry_date->diffForHumans() }}.</span>
                        </div>
                    @elseif($crewDocument->is_expiring_soon)
                        <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded mb-6">
                            <strong class="font-bold">⚠️ EXPIRING SOON</strong>
                            <span class="block sm:inline">This document will expire {{ $crewDocument->expiry_date->diffForHumans() }}.</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Crew Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Crew Member</h3>
                            <div class="space-y-2">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Name:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $crewDocument->crew->full_name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Employee ID:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $crewDocument->crew->employee_id }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Position:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $crewDocument->crew->position }}</span>
                                </div>
                                @if($crewDocument->crew->ship)
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Ship:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $crewDocument->crew->ship->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Document Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Document Information</h3>
                            <div class="space-y-2">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Document Type:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $crewDocument->document_type_name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Document Name:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $crewDocument->document_name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $crewDocument->status == 'verified' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 
                                           ($crewDocument->status == 'pending' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300' : 
                                           ($crewDocument->status == 'rejected' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300')) }}">
                                        {{ $crewDocument->status_name }}
                                    </span>
                                </div>
                                @if($crewDocument->expiry_date)
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Expiry Date:</span>
                                    <span class="text-gray-900 dark:text-white 
                                        {{ $crewDocument->is_expired ? 'text-red-600 dark:text-red-400 font-bold' : 
                                           ($crewDocument->is_expiring_soon ? 'text-yellow-600 dark:text-yellow-400 font-bold' : '') }}">
                                        {{ $crewDocument->expiry_date->format('M d, Y') }}
                                        @if($crewDocument->is_expired)
                                            (Expired)
                                        @elseif($crewDocument->is_expiring_soon)
                                            (Expires in {{ $crewDocument->expiry_date->diffInDays() }} days)
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- File Information -->
                    @if($crewDocument->file_path)
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">File Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">File Name:</span>
                                <span class="text-gray-900 dark:text-white break-all">{{ $crewDocument->file_name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">File Size:</span>
                                <span class="text-gray-900 dark:text-white">{{ number_format($crewDocument->file_size / 1024, 2) }} KB</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">File Type:</span>
                                <span class="text-gray-900 dark:text-white">{{ strtoupper(pathinfo($crewDocument->file_name, PATHINFO_EXTENSION)) }}</span>
                            </div>
                        </div>
                        
                        <!-- File Actions -->
                        <div class="flex flex-wrap gap-3">
                            <button onclick="viewAttachedFile('{{ $crewDocument->id }}')" 
                                    class="bg-indigo-500 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View File
                            </button>
                            <a href="{{ route('crew-documents.download', $crewDocument) }}" 
                               class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download
                            </a>
                            <button onclick="openFileInNewTab('{{ $crewDocument->id }}')" 
                                    class="bg-green-500 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-500 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open in New Tab
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Upload & Verification Information -->
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Upload & Verification</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Uploaded by:</span>
                                <span class="text-gray-900 dark:text-white">{{ $crewDocument->uploadedBy ? $crewDocument->uploadedBy->name : 'Unknown' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Upload Date:</span>
                                <span class="text-gray-900 dark:text-white">{{ $crewDocument->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            @if($crewDocument->verified_by)
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Verified by:</span>
                                <span class="text-gray-900 dark:text-white">{{ $crewDocument->verifiedBy->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Verification Date:</span>
                                <span class="text-gray-900 dark:text-white">{{ $crewDocument->verified_at->format('M d, Y g:i A') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($crewDocument->notes)
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Notes</h3>
                        <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $crewDocument->notes }}</p>
                    </div>
                    @endif

                    <!-- Actions for Admin/HR -->
                    @if(auth()->user()->roles && 
                        (in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR', 'HRMO']) || 
                         auth()->user()->hasPermission('crew-documents', 'manage')))
                        
                        @if($crewDocument->status == 'pending')
                        <div class="mt-6 bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Document Verification</h3>
                            <div class="flex space-x-4">
                                <button onclick="showVerificationModal('{{ $crewDocument->id }}')" 
                                        class="bg-green-500 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-500 text-white font-bold py-2 px-4 rounded">
                                    Verify Document
                                </button>
                                <button onclick="showRejectionModal('{{ $crewDocument->id }}')" 
                                        class="bg-red-500 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 text-white font-bold py-2 px-4 rounded">
                                    Reject Document
                                </button>
                            </div>
                        </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div id="verificationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Verify Document</h3>
                <form id="verificationForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="verification_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Verification Notes</label>
                        <textarea name="notes" id="verification_notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 dark:focus:border-green-400 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideVerificationModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" name="status" value="verified"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Verify
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Reject Document</h3>
                <form id="rejectionForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Rejection *</label>
                        <textarea name="notes" id="rejection_notes" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-red-500 dark:focus:border-red-400 focus:ring-red-500 dark:focus:ring-red-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideRejectionModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" name="status" value="rejected"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- File Viewer Modal -->
    <div id="fileViewerModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-7xl relative w-full" style="height: 95vh;">
            <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-600 pb-3">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $crewDocument->file_name ?? 'Document Viewer' }}</h3>
                </div>
                <button onclick="hideFileViewer()" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="fileViewerContent" class="w-full border rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-700 shadow-inner" style="height: calc(95vh - 120px);">
                <!-- File content will be loaded here -->
                <div class="flex items-center justify-center h-full">
                    <div class="text-gray-500 dark:text-gray-400 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium">Loading document...</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Please wait while we prepare the file for viewing</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Document:</span> {{ $crewDocument->document_name }}
                </div>
                <div class="flex space-x-3">
                    <a id="downloadLink" href="{{ route('crew-documents.download', $crewDocument) }}" target="_blank" 
                       class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download
                    </a>
                    <button onclick="hideFileViewer()" 
                            class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewAttachedFile(documentId) {
            const modal = document.getElementById('fileViewerModal');
            const content = document.getElementById('fileViewerContent');
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Get file extension to determine how to display
            const fileName = '{{ $crewDocument->file_name }}';
            const fileExtension = fileName.split('.').pop().toLowerCase();
            const viewUrl = `/crew-documents/${documentId}/view`;
            
            // Clear previous content with improved loading state
            content.innerHTML = `
                <div class="flex items-center justify-center h-full bg-gradient-to-br from-gray-50 to-gray-100">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                        <p class="text-lg font-medium text-gray-700">Loading document...</p>
                        <p class="text-sm text-gray-500 mt-1">Preparing ${fileName} for viewing</p>
                    </div>
                </div>
            `;
            
            // Display based on file type
            if (['pdf'].includes(fileExtension)) {
                // For PDF files, use embedded viewer with better styling
                content.innerHTML = `
                    <div class="w-full h-full bg-white rounded-lg overflow-hidden">
                        <iframe src="${viewUrl}" 
                                class="w-full h-full border-0" 
                                frameborder="0"
                                style="border-radius: 8px;">
                            <div class="flex items-center justify-center h-full bg-gray-100">
                                <div class="text-center text-gray-600">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mb-2">Your browser does not support PDF viewing.</p>
                                    <a href="${viewUrl}" target="_blank" class="text-blue-500 hover:text-blue-700 underline">Click here to view the document in a new tab</a>
                                </div>
                            </div>
                        </iframe>
                    </div>
                `;
            } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
                // For image files, display with better container
                content.innerHTML = `
                    <div class="flex items-center justify-center h-full bg-white rounded-lg p-4">
                        <img id="documentImage" src="${viewUrl}" 
                             alt="Document Preview" 
                             class="max-w-full max-h-full object-contain rounded-lg shadow-lg border border-gray-200"
                             style="background: white; display: none;">
                        <div id="imageLoadingError" class="text-center text-red-500" style="display: none;">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg font-medium">Unable to preview this image</p>
                            <p class="text-sm text-gray-500 mt-1">The image file may be corrupted or in an unsupported format</p>
                        </div>
                    </div>
                `;
                
                // Handle image loading events properly
                const img = content.querySelector('#documentImage');
                const errorDiv = content.querySelector('#imageLoadingError');
                
                img.onload = function() {
                    this.style.display = 'block';
                    errorDiv.style.display = 'none';
                };
                
                img.onerror = function() {
                    this.style.display = 'none';
                    errorDiv.style.display = 'block';
                };
            } else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(fileExtension)) {
                // For Office documents, improved styling
                content.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg">
                        <div class="text-center max-w-md">
                            <div class="bg-white rounded-full p-6 mx-auto mb-6 shadow-lg">
                                <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-800 mb-3">Microsoft Office Document</h4>
                            <p class="text-gray-600 mb-6 leading-relaxed">This ${fileExtension.toUpperCase()} document cannot be previewed directly in the browser. Click the button below to open it in a new tab.</p>
                            <a href="${viewUrl}" target="_blank" 
                               class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg inline-flex items-center transition-colors duration-200 shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open in New Tab
                            </a>
                        </div>
                    </div>
                `;
            } else {
                // For other file types, improved generic message
                content.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg">
                        <div class="text-center max-w-md">
                            <div class="bg-white rounded-full p-6 mx-auto mb-6 shadow-lg">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-700 mb-3">File Preview Not Available</h4>
                            <p class="text-gray-600 mb-2">This file type (<span class="font-medium uppercase">${fileExtension}</span>) cannot be previewed directly in the browser.</p>
                            <p class="text-gray-500 text-sm mb-6">Click the button below to download or open the file.</p>
                            <a href="${viewUrl}" target="_blank" 
                               class="bg-indigo-500 hover:bg-indigo-600 text-white font-medium py-3 px-6 rounded-lg inline-flex items-center transition-colors duration-200 shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open File
                            </a>
                        </div>
                    </div>
                `;
            }
        }

        function hideFileViewer() {
            document.getElementById('fileViewerModal').classList.add('hidden');
        }

        function openFileInNewTab(documentId) {
            const viewUrl = `/crew-documents/${documentId}/view`;
            window.open(viewUrl, '_blank');
        }

        function showVerificationModal(documentId) {
            document.getElementById('verificationForm').action = `/crew-documents/${documentId}/verify`;
            document.getElementById('verificationModal').classList.remove('hidden');
        }

        function hideVerificationModal() {
            document.getElementById('verificationModal').classList.add('hidden');
        }

        function showRejectionModal(documentId) {
            document.getElementById('rejectionForm').action = `/crew-documents/${documentId}/verify`;
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function hideRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('fileViewerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideFileViewer();
            }
        });
    </script>
</x-app-layout>
