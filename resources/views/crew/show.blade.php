<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight">
                    {{ $crew->full_name }}
                </h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('crew.edit', $crew) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('crew.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Personal & Employment Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Personal Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                        </div>
                        
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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Employee ID</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->employee_id }}</p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Full Name</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->full_name }}</p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Birthday</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->birthday ? $crew->birthday->format('M d, Y') : 'Not provided' }}</p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Phone</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->phone ?: 'Not provided' }}</p>
                            </div>
                            <div class="md:col-span-2 p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Email</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white break-all">{{ $crew->email ?: 'Not provided' }}</p>
                            </div>
                            <div class="md:col-span-2 p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Address</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->address ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Employment Information</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Position</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->position }}</p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Division</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $crew->division)) }}</p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Department</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $crew->department)) }}</p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Ship Assignment</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                    {{ $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore' }}
                                </p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Employment Status</label>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                    {{ $crew->employment_status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                       ($crew->employment_status == 'inactive' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
                                    {{ ucfirst($crew->employment_status) }}
                                </span>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Hire Date</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->hire_date ? $crew->hire_date->format('M d, Y') : 'Not set' }}</p>
                            </div>
                            @if($crew->contract_expiry)
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Contract Expiry</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->contract_expiry->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Government ID Numbers -->
                        <div class="mt-6">
                            <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                </svg>
                                Government ID Numbers
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">SSS Number</label>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->sss_number ?: 'Not provided' }}</p>
                                </div>
                                <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Pag-IBIG Number</label>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->pagibig_number ?: 'Not provided' }}</p>
                                </div>
                                <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">PhilHealth Number</label>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->philhealth_number ?: 'Not provided' }}</p>
                                </div>
                                <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">TIN Number</label>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->tin_number ?: 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact & Certificates -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Emergency Contact -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 mr-2 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Emergency Contact</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Contact Name</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->emergency_contact_name ?: 'Not provided' }}</p>
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Contact Phone</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $crew->emergency_contact_phone ?: 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificates -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Certificates & Documents</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">D-COC</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $crew->dcoc_number ?: ' ' }} | {{ $crew->dcoc_issue_date ? $crew->dcoc_issue_date->format('M d, Y') : 'Not set' }} | {{ $crew->dcoc_expiry ? $crew->dcoc_expiry->format('M d, Y') : 'Not set' }}
                                </p>
                            </div>
                            <div class="p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">MARINA License</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $crew->marina_license_number ?: ' ' }} | {{ $crew->marina_license_issue_date ? $crew->marina_license_issue_date->format('M d, Y') : 'Not provided' }} | {{ $crew->marina_license_expiry ? $crew->marina_license_expiry->format('M d, Y') : 'Not provided' }}
                                </p>
                            </div>
                            <div class="p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Seaman Book</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $crew->seaman_book_number ?: ' ' }} | {{ $crew->seaman_book_issue_date ? $crew->seaman_book_issue_date->format('M d, Y') : ' ' }} | {{ $crew->seaman_book_expiry_date ? $crew->seaman_book_expiry_date->format('M d, Y') : ' ' }}</p>
                            </div>
                            <div class="p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Medical Certificate</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $crew->medical_certificate_issue_date ? $crew->medical_certificate_issue_date->format('M d, Y') : ' ' }} | {{ $crew->medical_certificate ? $crew->medical_certificate->format('M d, Y') : ' ' }}
                                </p>
                            </div>
                            <div class="p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">SRN</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $crew->srn ?: 'Not provided' }}</p>
                            </div>
                            <!--div class="p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Basic Safety Training</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $crew->basic_safety_training ? $crew->basic_safety_training->format('M d, Y') : 'Not provided' }}
                                </p>
                            </div-->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Credits Summary -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Leave Credits Summary</h3>
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Documents</h3>
                        </div>
                        <a href="{{ route('crew-documents.create', ['crew_id' => $crew->id]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Upload Document
                        </a>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Document Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Document Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                @forelse($crew->documents as $document)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $document->is_expired ? 'bg-red-50 dark:bg-red-900/20' : ($document->is_expiring_soon ? 'bg-yellow-50 dark:bg-yellow-900/20' : '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $document->document_type_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $document->document_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($document->expiry_date)
                                                <div class="flex items-center">
                                                    @if($document->is_expired)
                                                        <svg class="w-4 h-4 mr-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @elseif($document->is_expiring_soon)
                                                        <svg class="w-4 h-4 mr-1 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                        </svg>
                                                    @endif
                                                    <span class="text-gray-900 dark:text-white">{{ $document->expiry_date->format('M d, Y') }}</span>
                                                </div>
                                                @if($document->is_expired)
                                                    <span class="text-red-600 dark:text-red-400 text-xs font-semibold">(Expired)</span>
                                                @elseif($document->is_expiring_soon)
                                                    <span class="text-yellow-600 dark:text-yellow-400 text-xs font-semibold">(Expiring Soon)</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500 italic">No expiry</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                                {{ $document->status == 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                                   ($document->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
                                                {{ $document->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-4">
                                                <a href="{{ route('crew-documents.show', $document) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">View</a>
                                                <a href="{{ route('crew-documents.download', $document) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors">Download</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No documents uploaded yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Leave Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                    <div class="flex items-center mb-6">
                        <svg class="w-6 h-6 mr-2 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Leave Applications</h3>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Leave Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date Range</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Days</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Applied Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                @forelse($crew->leaveApplications->take(10) as $application)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->leave_type_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $application->start_date->format('M d, Y') }} - {{ $application->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold rounded">
                                                {{ $application->days_requested }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                                {{ $application->status == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                                   ($application->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
                                                {{ $application->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $application->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No leave applications found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Embarkation History - Temporarily commented out until table structure is fixed --}}
            {{-- @if($crew->division === 'ship_crew')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Embarkation History</h3>
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
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ship</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Embark Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Disembark Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ports</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
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
            @endif --}}

            @if($crew->notes)
            <!-- Notes -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800">
                    <div class="flex items-center mb-6">
                        <svg class="w-6 h-6 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Notes</h3>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="whitespace-pre-line text-sm text-gray-900 dark:text-white leading-relaxed">{{ $crew->notes }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Disembark Modal - Temporarily commented out until embarkation table is fixed --}}
    {{-- <div id="disembarkModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50" style="display: none;">
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
    </div> --}}

    {{-- <script>
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
    </script> --}}
</x-app-layout>
