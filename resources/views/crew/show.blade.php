<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Crew Member Details') }} - {{ $crew->full_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('crew.edit', $crew) }}" 
                   class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('crew.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Personal & Employment Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Personal Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                        
                        <!-- ID Picture -->
                        @php
                            $idPicture = $crew->documents->where('document_type', 'id_picture')->first();
                        @endphp
                        @if($idPicture)
                            <div class="flex flex-col items-center mb-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="relative">
                                    <img src="{{ route('crew-documents.download', $idPicture) }}" 
                                         alt="Crew ID Picture" 
                                         class="w-40 h-40 object-cover rounded-xl shadow-lg border-2 border-white dark:border-gray-200">
                                    <div class="absolute inset-0 rounded-xl shadow-inner"></div>
                                </div>
                                <div class="mt-3 text-center">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $idPicture->document_name }}</p>
                                    <div class="mt-2 flex justify-center space-x-3">
                                        <a href="{{ route('crew-documents.download', $idPicture) }}" 
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-200">
                                            Download
                                        </a>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        <a href="{{ route('crew-documents.show', $idPicture) }}" 
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium transition-colors duration-200">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center mb-8 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="relative">
                                    <div class="w-40 h-40 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl flex items-center justify-center border-2 border-white dark:border-gray-200 shadow-lg">
                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="absolute inset-0 rounded-xl shadow-inner"></div>
                                </div>
                                <div class="mt-3 text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">No ID picture uploaded</p>
                                    <a href="{{ route('crew-documents.create', ['crew_id' => $crew->id]) }}?document_type=id_picture" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Upload Picture
                                    </a>
                                </div>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->employee_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->full_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->phone ?: 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->email ?: 'Not provided' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->address ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Position</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->position }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Division</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $crew->division)) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Department</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $crew->department)) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ship Assignment</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Employment Status</label>
                                <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $crew->employment_status == 'active' ? 'bg-green-100 text-green-800' : 
                                       ($crew->employment_status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($crew->employment_status) }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hire Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->hire_date->format('M d, Y') }}</p>
                            </div>
                            @if($crew->contract_expiry)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contract Expiry</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->contract_expiry->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Government ID Numbers -->
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Government ID Numbers</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">SSS Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $crew->sss_number ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pag-IBIG Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $crew->pagibig_number ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">PhilHealth Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $crew->philhealth_number ?: 'Not provided' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">TIN Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $crew->tin_number ?: 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact & Certificates -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Emergency Contact -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->emergency_contact_name ?: 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Phone</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->emergency_contact_phone ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificates -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Certificates & Documents</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Seaman Book Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $crew->seaman_book_number ?: 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Basic Safety Training Expiry</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $crew->basic_safety_training ? $crew->basic_safety_training->format('M d, Y') : 'Not set' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Medical Certificate Expiry</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $crew->medical_certificate ? $crew->medical_certificate->format('M d, Y') : 'Not set' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">DCOC Expiry</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $crew->dcoc_expiry ? $crew->dcoc_expiry->format('M d, Y') : 'Not set' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">MARINA License Expiry</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $crew->marina_license_expiry ? $crew->marina_license_expiry->format('M d, Y') : 'Not set' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Credits Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Credits Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $crew->total_leave_credits }}</p>
                            <p class="text-sm text-gray-600">Total Credits</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-red-600">{{ $crew->used_leave_credits }}</p>
                            <p class="text-sm text-gray-600">Used Credits</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $crew->available_leave_credits }}</p>
                            <p class="text-sm text-gray-600">Available Credits</p>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('leave-applications.create', ['crew_id' => $crew->id]) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Apply Leave
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Documents</h3>
                        <a href="{{ route('crew-documents.create', ['crew_id' => $crew->id]) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Upload Document
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($crew->documents as $document)
                                    <tr class="{{ $document->is_expired ? 'bg-red-50' : ($document->is_expiring_soon ? 'bg-yellow-50' : '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $document->document_type_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $document->document_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($document->expiry_date)
                                                {{ $document->expiry_date->format('M d, Y') }}
                                                @if($document->is_expired)
                                                    <span class="text-red-600 text-xs">(Expired)</span>
                                                @elseif($document->is_expiring_soon)
                                                    <span class="text-yellow-600 text-xs">(Expiring Soon)</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">No expiry</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $document->status == 'verified' ? 'bg-green-100 text-green-800' : 
                                                   ($document->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $document->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('crew-documents.show', $document) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                                            <a href="{{ route('crew-documents.download', $document) }}" class="text-blue-600 hover:text-blue-900">Download</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No documents uploaded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Leave Applications -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Leave Applications</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($crew->leaveApplications->take(10) as $application)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $application->leave_type_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $application->days_requested }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $application->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                                   ($application->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $application->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $application->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No leave applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Embarkation History -->
            @if($crew->division === 'ship_crew')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Embarkation History</h3>
                        <a href="{{ route('crew-embarkations.create', ['crew_id' => $crew->id]) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Embarkation
                        </a>
                    </div>

                    <!-- Current Active Embarkation -->
                    @if($crew->currentEmbarkation)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-md font-semibold text-green-800">Currently Aboard</h4>
                                    <p class="text-sm text-green-700">
                                        <strong>Ship:</strong> MV EVERWIN STAR {{ $crew->currentEmbarkation->ship->ship_number }}<br>
                                        <strong>Embarked:</strong> {{ $crew->currentEmbarkation->embark_date->format('M d, Y') }}<br>
                                        <strong>Days Aboard:</strong> {{ floor($crew->currentEmbarkation->embark_date->diffInDays(now())) }} days
                                        @if($crew->currentEmbarkation->embark_port)
                                            <br><strong>From:</strong> {{ $crew->currentEmbarkation->embark_port }}
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <button type="button" onclick="openDisembarkModal({{ $crew->currentEmbarkation->id }})"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Disembark
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Embarkation History Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ship</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Embark Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disembark Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ports</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($crew->embarkations as $embarkation)
                                    <tr class="{{ $embarkation->is_active ? 'bg-green-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            MV EVERWIN STAR {{ $embarkation->ship->ship_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $embarkation->embark_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $embarkation->disembark_date ? $embarkation->disembark_date->format('M d, Y') : 'Still aboard' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $embarkation->disembark_date ? $embarkation->duration . ' days' : '' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($embarkation->embark_port || $embarkation->disembark_port)
                                                {{ $embarkation->embark_port ?: 'N/A' }} → {{ $embarkation->disembark_port ?: 'N/A' }}
                                            @else
                                                Not specified
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $embarkation->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($embarkation->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('crew-embarkations.edit', $embarkation) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                            @if($embarkation->is_active)
                                                <button onclick="openDisembarkModal({{ $embarkation->id }})" 
                                                        class="text-red-600 hover:text-red-900">Disembark</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No embarkation records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($crew->notes)
            <!-- Notes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                    <div class="whitespace-pre-line text-sm text-gray-900">{{ $crew->notes }}</div>
                </div>
            </div>
            <br>
            @endif
        </div>
    </div>

    <!-- Disembark Modal -->
    <div id="disembarkModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Disembark Crew Member</h3>
                <form id="disembarkForm" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label for="disembark_date_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disembark Date *</label>
                        <input type="date" name="disembark_date" id="disembark_date_modal" value="{{ date('Y-m-d') }}" required
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div class="mb-4">
                        <label for="disembark_port_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disembark Port</label>
                        <input type="text" name="disembark_port" id="disembark_port_modal"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="e.g., Batanes Port">
                    </div>

                    <div class="mb-4">
                        <label for="remarks_modal" class="block text-sm font-medium text-gray-700">Remarks</label>
                        <textarea name="remarks" id="remarks_modal" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Any notes about the disembarkation..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeDisembarkModal()" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Disembark
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDisembarkModal(embarkationId) {
            const modal = document.getElementById('disembarkModal');
            const form = document.getElementById('disembarkForm');
            form.action = `/crew-embarkations/${embarkationId}/disembark`;
            modal.style.display = 'block';
        }

        function closeDisembarkModal() {
            const modal = document.getElementById('disembarkModal');
            modal.style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('disembarkModal');
            if (event.target == modal) {
                closeDisembarkModal();
            }
        }
    </script>
</x-app-layout>
