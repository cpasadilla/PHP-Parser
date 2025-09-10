<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Edit Crew Member') }} - {{ $crew->full_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('crew.show', $crew) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    View Details
                </a>
                <a href="{{ route('crew.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Crew List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('crew.update', $crew) }}">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee ID *</label>
                                    <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $crew->employee_id) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('employee_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name *</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $crew->first_name) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name *</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $crew->last_name) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Middle Name</label>
                                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $crew->middle_name) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('middle_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $crew->phone) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $crew->email) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <textarea name="address" id="address" rows="3" 
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('address', $crew->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Employment Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Employment Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position *</label>
                                    <input type="text" name="position" id="position" value="{{ old('position', $crew->position) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('position')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="division" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Division *</label>
                                    <select name="division" id="division" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Division</option>
                                        <option value="ship_crew" {{ old('division', $crew->division) == 'ship_crew' ? 'selected' : '' }}>Ship Crew</option>
                                        <option value="office_staff" {{ old('division', $crew->division) == 'office_staff' ? 'selected' : '' }}>Office Staff</option>
                                        <option value="operations" {{ old('division', $crew->division) == 'operations' ? 'selected' : '' }}>Operations</option>
                                        <option value="apprentice" {{ old('division', $crew->division) == 'apprentice' ? 'selected' : '' }}>Apprentice</option>
                                    </select>
                                    @error('division')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department *</label>
                                    <select name="department" id="department" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Department</option>
                                    </select>
                                    @error('department')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ship_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ship Assignment</label>
                                    <select name="ship_id" id="ship_id" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Office/Shore Assignment</option>
                                        @foreach($ships as $ship)
                                            <option value="{{ $ship->id }}" {{ old('ship_id', $crew->ship_id) == $ship->id ? 'selected' : '' }}>
                                                MV EVERWIN STAR {{ $ship->ship_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ship_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hire Date *</label>
                                    <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $crew->hire_date->format('Y-m-d')) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('hire_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="employment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employment Status *</label>
                                    <select name="employment_status" id="employment_status" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('employment_status', $crew->employment_status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('employment_status', $crew->employment_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="terminated" {{ old('employment_status', $crew->employment_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                        <option value="resigned" {{ old('employment_status', $crew->employment_status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
                                    </select>
                                    @error('employment_status')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contract_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contract Expiry</label>
                                    <input type="date" name="contract_expiry" id="contract_expiry" 
                                           value="{{ old('contract_expiry', $crew->contract_expiry ? $crew->contract_expiry->format('Y-m-d') : '') }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('contract_expiry')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Government ID Numbers -->
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-4">Government ID Numbers</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div>
                                        <label for="sss_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SSS Number</label>
                                        <input type="text" name="sss_number" id="sss_number" 
                                               value="{{ old('sss_number', $crew->sss_number) }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                               placeholder="XX-XXXXXXX-X">
                                        @error('sss_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="pagibig_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pag-IBIG Number</label>
                                        <input type="text" name="pagibig_number" id="pagibig_number" 
                                               value="{{ old('pagibig_number', $crew->pagibig_number) }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                               placeholder="XXXX-XXXX-XXXX">
                                        @error('pagibig_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="philhealth_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PhilHealth Number</label>
                                        <input type="text" name="philhealth_number" id="philhealth_number" 
                                               value="{{ old('philhealth_number', $crew->philhealth_number) }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                               placeholder="XX-XXXXXXXXX-X">
                                        @error('philhealth_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="tin_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">TIN Number</label>
                                        <input type="text" name="tin_number" id="tin_number" 
                                               value="{{ old('tin_number', $crew->tin_number) }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                               placeholder="XXX-XXX-XXX-XXX">
                                        @error('tin_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" 
                                           value="{{ old('emergency_contact_name', $crew->emergency_contact_name) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('emergency_contact_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" 
                                           value="{{ old('emergency_contact_phone', $crew->emergency_contact_phone) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('emergency_contact_phone')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Certificates & Documents -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Certificates & Documents</h3>
                            
                            <!-- Seaman Book Section -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">Seaman Book</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="seaman_book_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Seaman Book Number</label>
                                        <input type="text" name="seaman_book_number" id="seaman_book_number" 
                                               value="{{ old('seaman_book_number', $crew->seaman_book_number) }}" 
                                               placeholder="e.g., SB-2024-001234"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('seaman_book_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="seaman_book_issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date</label>
                                        <input type="date" name="seaman_book_issue_date" id="seaman_book_issue_date" 
                                               value="{{ old('seaman_book_issue_date', $crew->seaman_book_issue_date ? $crew->seaman_book_issue_date->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('seaman_book_issue_date')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="seaman_book_expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                                        <input type="date" name="seaman_book_expiry_date" id="seaman_book_expiry_date" 
                                               value="{{ old('seaman_book_expiry_date', $crew->seaman_book_expiry_date ? $crew->seaman_book_expiry_date->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('seaman_book_expiry_date')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Certificate Section -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">Medical Certificate</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="medical_certificate_issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date</label>
                                        <input type="date" name="medical_certificate_issue_date" id="medical_certificate_issue_date" 
                                               value="{{ old('medical_certificate_issue_date', $crew->medical_certificate_issue_date ? $crew->medical_certificate_issue_date->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('medical_certificate_issue_date')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="medical_certificate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                                        <input type="date" name="medical_certificate" id="medical_certificate" 
                                               value="{{ old('medical_certificate', $crew->medical_certificate ? $crew->medical_certificate->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('medical_certificate')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- DCOC Section -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">Domestic Certificate of Competency (DCOC)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="dcoc_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DCOC Number</label>
                                        <input type="text" name="dcoc_number" id="dcoc_number" 
                                               value="{{ old('dcoc_number', $crew->dcoc_number) }}" 
                                               placeholder="e.g., DCOC-2024-001234"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('dcoc_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="dcoc_issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date</label>
                                        <input type="date" name="dcoc_issue_date" id="dcoc_issue_date" 
                                               value="{{ old('dcoc_issue_date', $crew->dcoc_issue_date ? $crew->dcoc_issue_date->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('dcoc_issue_date')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="dcoc_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                                        <input type="date" name="dcoc_expiry" id="dcoc_expiry" 
                                               value="{{ old('dcoc_expiry', $crew->dcoc_expiry ? $crew->dcoc_expiry->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('dcoc_expiry')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- MARINA License Section -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">MARINA License</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="marina_license_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">MARINA License Number</label>
                                        <input type="text" name="marina_license_number" id="marina_license_number" 
                                               value="{{ old('marina_license_number', $crew->marina_license_number) }}" 
                                               placeholder="e.g., ML-2024-001234"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('marina_license_number')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="marina_license_issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date</label>
                                        <input type="date" name="marina_license_issue_date" id="marina_license_issue_date" 
                                               value="{{ old('marina_license_issue_date', $crew->marina_license_issue_date ? $crew->marina_license_issue_date->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('marina_license_issue_date')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="marina_license_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                                        <input type="date" name="marina_license_expiry" id="marina_license_expiry" 
                                               value="{{ old('marina_license_expiry', $crew->marina_license_expiry ? $crew->marina_license_expiry->format('Y-m-d') : '') }}" 
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @error('marina_license_expiry')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-8">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('notes', $crew->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('crew.show', $crew) }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                                Update Crew Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Division-Department mapping
        const departmentMapping = {
            'ship_crew': [
                { value: 'engine', text: 'Engine' },
                { value: 'deck', text: 'Deck' }
            ],
            'office_staff': [
                { value: 'manila', text: 'Manila' },
                { value: 'batanes', text: 'Batanes' }
            ],
            'operations': [
                { value: 'manila', text: 'Manila' },
                { value: 'batanes', text: 'Batanes' }
            ],
            'apprentice': [
                { value: 'manila', text: 'Manila' },
                { value: 'batanes', text: 'Batanes' }
            ]
        };

        document.addEventListener('DOMContentLoaded', function() {
            const divisionSelect = document.getElementById('division');
            const departmentSelect = document.getElementById('department');
            const currentDepartment = '{{ old("department", $crew->department) }}';

            // Handle division change
            divisionSelect.addEventListener('change', function() {
                const selectedDivision = this.value;
                
                // Clear department options
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                
                if (selectedDivision && departmentMapping[selectedDivision]) {
                    // Enable department select
                    departmentSelect.disabled = false;
                    
                    // Add department options based on selected division
                    departmentMapping[selectedDivision].forEach(function(dept) {
                        const option = document.createElement('option');
                        option.value = dept.value;
                        option.textContent = dept.text;
                        
                        // Check if this was the old selected value
                        if (currentDepartment === dept.value) {
                            option.selected = true;
                        }
                        
                        departmentSelect.appendChild(option);
                    });
                } else {
                    // Disable department select if no division selected
                    departmentSelect.disabled = true;
                }
            });

            // Trigger change event on page load if division is already selected
            if (divisionSelect.value) {
                divisionSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>
