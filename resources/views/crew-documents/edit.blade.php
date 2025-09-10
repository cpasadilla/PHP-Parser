<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Edit Document') }} - {{ $crewDocument->document_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('crew-documents.show', $crewDocument) }}" 
                   class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                    View Document
                </a>
                <a href="{{ route('crew-documents.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                    Back to Documents
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('crew-documents.update', $crewDocument) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="crew_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Crew Member *</label>
                                <select name="crew_id" id="crew_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Crew Member</option>
                                    @foreach($crews as $crewMember)
                                        <option value="{{ $crewMember->id }}" 
                                                {{ old('crew_id', $crewDocument->crew_id) == $crewMember->id ? 'selected' : '' }}>
                                            {{ $crewMember->full_name }} ({{ $crewMember->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('crew_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Type *</label>
                                <select name="document_type" id="document_type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Document Type</option>
                                    @foreach(\App\Models\CrewDocument::DOCUMENT_TYPES as $key => $label)
                                        <option value="{{ $key }}" {{ old('document_type', $crewDocument->document_type) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="document_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Name *</label>
                                <input type="text" name="document_name" id="document_name" 
                                       value="{{ old('document_name', $crewDocument->document_name) }}" 
                                       placeholder="e.g., Medical Certificate 2024"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('document_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                       value="{{ old('expiry_date', $crewDocument->expiry_date ? $crewDocument->expiry_date->format('Y-m-d') : '') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current File Information -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Document</h4>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>File:</strong> {{ $crewDocument->file_name }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Size:</strong> {{ number_format($crewDocument->file_size / 1024, 2) }} KB
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Uploaded:</strong> {{ $crewDocument->created_at->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <button type="button" onclick="viewAttachedFile('{{ $crewDocument->id }}')"
                                       class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full hover:bg-blue-200 dark:hover:bg-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Current
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="document_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Replace Document File (Optional)</label>
                            <input type="file" name="document_file" id="document_file" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-800">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave empty to keep current file. Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 10MB)</p>
                            @error('document_file')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      placeholder="Any additional information about this document..."
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('notes', $crewDocument->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('crew-documents.show', $crewDocument) }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                                Update Document
                            </button>
                        </div>
                    </form>
                </div>
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
                       class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center transition-colors duration-200">
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

        // Close modal when clicking outside
        document.getElementById('fileViewerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideFileViewer();
            }
        });
    </script>
</x-app-layout>
