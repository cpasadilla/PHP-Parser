@php
    $deletedDocuments = $deletedDocuments ?? [];
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
                {{ __('Deleted Crew Documents') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('crew-documents.index') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Documents
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                    
                    @if($deletedDocuments->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No deleted documents</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No crew documents have been deleted.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Crew Member
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Employee ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Document Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Document Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            File Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Expiry Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Deleted Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Deleted By
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($deletedDocuments as $document)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $document->crew_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $document->employee_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                @php
                                                    $documentTypes = [
                                                        'seaman_book' => 'Seaman Book',
                                                        'medical_certificate' => 'Medical Certificate',
                                                        'basic_safety_training' => 'Basic Safety Training',
                                                        'coc' => 'Certificate of Competency',
                                                        'dcoc' => 'Domestic Certificate of Competency (DCOC)',
                                                        'marina_license' => 'MARINA License',
                                                        'contract' => 'Employment Contract',
                                                        'identification' => 'Government ID',
                                                        'id_picture' => 'Crew ID Picture',
                                                        'tax_certificate' => 'Tax Certificate',
                                                        'resume' => 'Resume',
                                                        'insurance' => 'Insurance',
                                                        'sss' => 'SSS',
                                                        'pag_ibig' => 'Pag-ibig',
                                                        'philhealth' => 'Philhealth',
                                                        'tin' => 'TIN',
                                                        'other' => 'Other'
                                                    ];
                                                @endphp
                                                {{ $documentTypes[$document->document_type] ?? ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $document->document_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $document->file_name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                @if($document->expiry_date)
                                                    {{ $document->expiry_date->format('M d, Y') }}
                                                    @if($document->expiry_date->isPast())
                                                        <span class="ml-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                            Expired
                                                        </span>
                                                    @elseif($document->expiry_date->diffInDays() <= 30)
                                                        <span class="ml-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                            Expiring Soon
                                                        </span>
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($document->status)
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                            'verified' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                            'expired' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$document->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst($document->status) }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $document->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $document->deleted_by }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @if(auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'document-management', 'edit'))
                                                    <button onclick="showRestoreModal({{ $document->id }}, '{{ $document->document_name }}', '{{ $document->crew_name }}', '{{ $document->employee_id }}')" 
                                                            class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                        Restore
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $deletedDocuments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="successMessage" class="fixed top-4 right-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded z-50">
            {{ session('success') }}
            <button onclick="this.parentElement.remove()" class="ml-2 text-green-700 dark:text-green-200 hover:text-green-900 dark:hover:text-green-100">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                const msg = document.getElementById('successMessage');
                if (msg) msg.remove();
            }, 5000);
        </script>
    @endif

    @if(session('error'))
        <div id="errorMessage" class="fixed top-4 right-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded z-50">
            {{ session('error') }}
            <button onclick="this.parentElement.remove()" class="ml-2 text-red-700 dark:text-red-200 hover:text-red-900 dark:hover:text-red-100">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                const msg = document.getElementById('errorMessage');
                if (msg) msg.remove();
            }, 5000);
        </script>
    @endif

    <!-- Restore Confirmation Modal -->
    <div id="restoreModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Restore Document</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Are you sure you want to restore the document "<span id="restoreDocumentName" class="font-semibold text-gray-900 dark:text-white"></span>" 
                    for <span id="restoreCrewName" class="font-semibold text-gray-900 dark:text-white"></span> 
                    (Employee ID: <span id="restoreEmployeeId" class="font-semibold text-gray-900 dark:text-white"></span>)?
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    This will create a new document record with the original data.
                </p>
                <form id="restoreForm" method="POST">
                    @csrf
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideRestoreModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Restore Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showRestoreModal(deleteLogId, documentName, crewName, employeeId) {
            document.getElementById('restoreForm').action = `/crew-documents/restore/${deleteLogId}`;
            document.getElementById('restoreDocumentName').textContent = documentName;
            document.getElementById('restoreCrewName').textContent = crewName;
            document.getElementById('restoreEmployeeId').textContent = employeeId;
            document.getElementById('restoreModal').classList.remove('hidden');
        }

        function hideRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('restoreModal');
            if (!modal.classList.contains('hidden') && e.key === 'Escape') {
                hideRestoreModal();
            }
        });

        // Close modal when clicking outside
        document.getElementById('restoreModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRestoreModal();
            }
        });
    </script>
</x-app-layout>
@endif
