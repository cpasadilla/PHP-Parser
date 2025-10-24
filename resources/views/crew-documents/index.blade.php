@php
    $search = $search ?? '';
    $status = $status ?? '';
    $documentType = $documentType ?? '';
@endphp

@if(!auth()->user()->hasPermission('crew', 'access') && !auth()->user()->hasSubpagePermission('crew', 'document-management', 'access'))
    <x-app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                        <h3 class="text-lg font-semibold mb-2">Access Denied</h3>
                        <p>You don't have permission to access Document Management.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@else

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Document Management') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('crew-documents.deleted') }}" 
                   class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Deleted Documents
                </a>
                @if(auth()->user()->hasPermission('crew', 'create') || auth()->user()->hasSubpagePermission('crew', 'document-management', 'create'))
                <a href="{{ route('crew-documents.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                    Upload Document
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notifications -->
            @if($expiringDocuments->count() > 0 || $expiredDocuments->count() > 0)
            <div class="mb-6">
                @if($expiredDocuments->count() > 0)
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">{{ $expiredDocuments->count() }} documents have expired!</strong>
                    <ul class="mt-2">
                        @foreach($expiredDocuments->take(5) as $doc)
                        <li>{{ $doc->crew ? $doc->crew->full_name : 'N/A' }} - {{ $doc->document_type_name }} (expired {{ $doc->expiry_date->diffForHumans() }})</li>
                        @endforeach
                        @if($expiredDocuments->count() > 5)
                        <li class="text-sm">...and {{ $expiredDocuments->count() - 5 }} more</li>
                        @endif
                    </ul>
                </div>
                @endif

                @if($expiringDocuments->count() > 0)
                <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">{{ $expiringDocuments->count() }} documents expiring soon!</strong>
                    <ul class="mt-2">
                        @foreach($expiringDocuments->take(5) as $doc)
                        <li>{{ $doc->crew ? $doc->crew->full_name : 'N/A' }} - {{ $doc->document_type_name }} (expires {{ $doc->expiry_date->diffForHumans() }})</li>
                        @endforeach
                        @if($expiringDocuments->count() > 5)
                        <li class="text-sm">...and {{ $expiringDocuments->count() - 5 }} more</li>
                        @endif
                    </ul>
                </div>
                @endif
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('crew-documents.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input type="text" name="search" id="search" value="{{ $search }}" 
                                   placeholder="Crew name, document name..."
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Status</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ $status == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="document_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Type</label>
                            <select name="document_type" id="document_type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Types</option>
                                @foreach(\App\Models\CrewDocument::DOCUMENT_TYPES as $key => $label)
                                    <option value="{{ $key }}" {{ $documentType == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-bold py-2 px-4 rounded mr-2">
                                Filter
                            </button>
                            <a href="{{ route('crew-documents.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Documents List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Crew Member
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Document Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Document Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Expiry Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Uploaded By
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($documents as $document)
                                    <tr class="{{ $document->is_expired ? 'bg-red-50 dark:bg-red-900' : ($document->is_expiring_soon ? 'bg-yellow-50 dark:bg-yellow-900' : '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $document->crew ? $document->crew->full_name : 'N/A' }}
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $document->crew ? $document->crew->employee_id : 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $document->document_type_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $document->document_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            @if($document->expiry_date)
                                                {{ $document->expiry_date->format('M d, Y') }}
                                                @if($document->is_expired)
                                                    <span class="text-red-600 dark:text-red-400 text-xs">(Expired)</span>
                                                @elseif($document->is_expiring_soon)
                                                    <span class="text-yellow-600 dark:text-yellow-400 text-xs">(Expiring Soon)</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">No expiry</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $document->status == 'verified' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 
                                                   ($document->status == 'pending' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300' : 
                                                   ($document->status == 'rejected' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300')) }}">
                                                {{ $document->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $document->uploadedBy ? $document->uploadedBy->name : 'Unknown' }}
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $document->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('crew-documents.show', $document) }}" 
                                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">View</a>
                                                <a href="{{ route('crew-documents.download', $document) }}" 
                                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">Download</a>
                                                @if($document->status == 'pending' && (auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'document-management', 'edit')))
                                                <button onclick="showVerifyModal({{ $document->id }})" 
                                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">Verify</button>
                                                @endif
                                                @if(auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'document-management', 'edit'))
                                                <a href="{{ route('crew-documents.edit', $document) }}" 
                                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300">Edit</a>
                                                @endif
                                                @if(auth()->user()->hasPermission('crew', 'delete') || auth()->user()->hasSubpagePermission('crew', 'document-management', 'delete'))
                                                <button onclick="showDeleteModal({{ $document->id }}, '{{ addslashes($document->document_name) }}', '{{ $document->crew ? addslashes($document->crew->full_name) : 'N/A' }}')" 
                                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Delete</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No documents found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $documents->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verify Modal -->
    <div id="verifyModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Verify Document</h3>
                <form id="verifyForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="verified" class="mr-2">
                                <span class="text-green-600 dark:text-green-400">Verified</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="rejected" class="mr-2">
                                <span class="text-red-600 dark:text-red-400">Rejected</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="verify_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="verify_notes" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideVerifyModal()" 
                                class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showVerifyModal(documentId) {
            document.getElementById('verifyForm').action = `/crew-documents/${documentId}/verify`;
            document.getElementById('verifyModal').classList.remove('hidden');
        }

        function hideVerifyModal() {
            document.getElementById('verifyModal').classList.add('hidden');
        }

        function showDeleteModal(documentId, documentName, crewName) {
            document.getElementById('deleteForm').action = `/crew-documents/${documentId}`;
            document.getElementById('deleteDocumentName').textContent = documentName;
            document.getElementById('deleteCrewName').textContent = crewName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            const verifyModal = document.getElementById('verifyModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (!verifyModal.classList.contains('hidden') && e.key === 'Escape') {
                hideVerifyModal();
            }
            
            if (!deleteModal.classList.contains('hidden') && e.key === 'Escape') {
                hideDeleteModal();
            }
        });

        // Close modals when clicking outside
        document.getElementById('verifyModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideVerifyModal();
            }
        });
        
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    </script>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Document</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Are you sure you want to delete the document "<span id="deleteDocumentName" class="font-semibold text-gray-900 dark:text-white"></span>" 
                    for <span id="deleteCrewName" class="font-semibold text-gray-900 dark:text-white"></span>?
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    This action will soft delete the document. The record can be restored later if needed.
                </p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideDeleteModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
@endif
