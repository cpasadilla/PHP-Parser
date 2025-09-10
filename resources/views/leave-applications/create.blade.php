<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Leave Application Form') }}
            </h2>
            <a href="{{ route('leave-applications.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Applications
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">LEAVE APPLICATION FORM</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Please fill out all required fields</p>
                    </div>

                    <form id="leaveApplicationForm" method="POST" action="{{ route('leave-applications.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div id="formErrors" class="mb-4 hidden">
                            <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-400 dark:border-red-600 p-4">
                                <p class="font-semibold text-red-700 dark:text-red-300">Please fix the following errors:</p>
                                <ul id="formErrorsList" class="list-disc list-inside text-sm text-red-700 dark:text-red-300 mt-2"></ul>
                            </div>
                        </div>

                        <!-- Employee Information Section -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">EMPLOYEE INFORMATION</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label for="crew_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name of Employee *</label>
                                    <select name="crew_id" id="crew_id" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Employee</option>
                                        @foreach($crews as $crewMember)
                                            <option value="{{ $crewMember->id }}" 
                                                    {{ ($crew && $crew->id == $crewMember->id) || old('crew_id') == $crewMember->id ? 'selected' : '' }}
                                                    data-position="{{ $crewMember->position }}"
                                                    data-ship="{{ $crewMember->ship ? 'MV EVERWIN STAR ' . $crewMember->ship->ship_number : '' }}"
                                                    data-department="{{ $crewMember->department }}"
                                                    data-vacation-credits="{{ $crewMember->leaves->where('leave_type', 'vacation')->sum('credits') - $crewMember->leaveApplications->where('status', 'approved')->where('leave_type', 'vacation')->sum('days_requested') }}"
                                                    data-sick-credits="{{ $crewMember->leaves->where('leave_type', 'sick')->sum('credits') - $crewMember->leaveApplications->where('status', 'approved')->where('leave_type', 'sick')->sum('days_requested') }}">
                                                {{ $crewMember->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('crew_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_applied" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Applied *</label>
                                    <input type="date" name="date_applied" id="date_applied" 
                                           value="{{ old('date_applied', date('Y-m-d')) }}" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('date_applied')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label for="position_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Designation/Position</label>
                                    <input type="text" id="position_display" readonly 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm text-gray-500 dark:text-gray-400"
                                           placeholder="Select employee to auto-fill">
                                </div>

                                <div>
                                    <label for="vessel_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vessel Number (if crew)</label>
                                    <input type="text" name="vessel_number" id="vessel_number" 
                                           value="{{ old('vessel_number') }}" 
                                           placeholder="Will auto-fill for ship crew"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                                    @error('vessel_number')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Leave Details Section -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">LEAVE DETAILS</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label for="leave_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type of Leave *</label>
                                    <select name="leave_type" id="leave_type" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Leave Type</option>
                                        <option value="vacation" {{ old('leave_type') == 'vacation' ? 'selected' : '' }}>Vacation</option>
                                        <option value="maternity" {{ old('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity</option>
                                        <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>Sick</option>
                                        <option value="paternity" {{ old('leave_type') == 'paternity' ? 'selected' : '' }}>Paternity</option>
                                        <option value="other" {{ old('leave_type') == 'other' ? 'selected' : '' }}>Others (Specify)</option>
                                    </select>
                                    @error('leave_type')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div id="other_leave_type_div" class="hidden">
                                    <label for="other_leave_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Specify Other Leave Type</label>
                                    <input type="text" name="other_leave_type" id="other_leave_type" 
                                           value="{{ old('other_leave_type') }}" 
                                           placeholder="Please specify the type of leave"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                                    @error('other_leave_type')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Leave *</label>
                                <textarea name="reason" id="reason" rows="3" 
                                          placeholder="Please provide a detailed reason for your leave request..."
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date of Leave *</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" 
                                           min="{{ date('Y-m-d') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('start_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date of Leave *</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" 
                                           min="{{ date('Y-m-d') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('end_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="calculated_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Days Leave</label>
                                    <input type="text" id="calculated_days" readonly 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm text-gray-500 dark:text-gray-400"
                                           placeholder="Auto-calculated">
                                </div>
                            </div>
                        </div>

                        <!-- Approval Section -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">APPROVAL SECTION</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <label for="approved_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved by (Department Head)</label>
                                    <input type="text" name="approved_by" id="approved_by" 
                                           value="{{ old('approved_by') }}" 
                                           placeholder="Department Head Name"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('approved_by')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="noted_by_captain" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Noted By (Captain/Master)</label>
                                    <input type="text" name="noted_by_captain" id="noted_by_captain" 
                                           value="{{ old('noted_by_captain') }}" 
                                           placeholder="Captain/Master Name"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('noted_by_captain')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="noted_by_manager" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Noted By (Manager)</label>
                                    <input type="text" name="noted_by_manager" id="noted_by_manager" 
                                           value="{{ old('noted_by_manager') }}" 
                                           placeholder="Manager Name"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('noted_by_manager')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- HR Department Section -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">HR DEPARTMENT SECTION (To be filled by HR)</h4>
                            
                            @php
                                $canEditHR = auth()->user()->roles && 
                                    (in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR', 'HRMO']) || 
                                     auth()->user()->hasPermission('crew', 'manage'));
                            @endphp
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Leave Credit as of: {{ date('F d, Y') }}</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Vacation Leave:</label>
                                        @if($canEditHR)
                                            <input type="number" name="hr_vacation_credits" id="hr_vacation_credits" 
                                                   value="{{ old('hr_vacation_credits') }}" 
                                                   placeholder="0"
                                                   class="text-sm font-bold text-blue-600 dark:text-blue-400 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-20 bg-white dark:bg-gray-700">
                                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">days</span>
                                        @else
                                            <span id="vacation_credits_display" class="text-sm font-bold text-blue-600 dark:text-blue-400">-- days</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Sick Leave:</label>
                                        @if($canEditHR)
                                            <input type="number" name="hr_sick_credits" id="hr_sick_credits" 
                                                   value="{{ old('hr_sick_credits') }}" 
                                                   placeholder="0"
                                                   class="text-sm font-bold text-blue-600 dark:text-blue-400 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-20 bg-white dark:bg-gray-700">
                                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">days</span>
                                        @else
                                            <span id="sick_credits_display" class="text-sm font-bold text-blue-600 dark:text-blue-400">-- days</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(!$canEditHR)
                                <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 dark:border-yellow-600 p-3 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                                <strong>Notice:</strong> This section can only be edited by HR personnel (Admin or HRMO). The leave credits will be automatically populated from the system.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <p><strong>Filled out by:</strong></p>
                                @if($canEditHR)
                                    <input type="text" name="hr_filled_by" id="hr_filled_by" 
                                           value="{{ old('hr_filled_by', 'Carla E. Alcon') }}" 
                                           placeholder="HR Officer Name"
                                           class="mt-1 block w-full max-w-xs rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <input type="text" name="hr_title" id="hr_title" 
                                           value="{{ old('hr_title', 'HRMO') }}" 
                                           placeholder="HR Title"
                                           class="mt-1 block w-full max-w-xs rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @else
                                    <p>Carla E. Alcon</p>
                                    <p>HRMO</p>
                                @endif
                            </div>
                        </div>

                        <!-- Operations Manager Section -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">OPERATIONS MANAGER SECTION (To be filled by Operations Manager)</h4>
                            
                            @php
                                $canEditOps = auth()->user()->roles && 
                                    (in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR', 'OPERATIONS MANAGER', 'MANAGER']) || 
                                     auth()->user()->hasPermission('crew', 'manage'));
                            @endphp
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label for="approved_days_with_pay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved for _____ days with pay</label>
                                    <input type="number" name="approved_days_with_pay" id="approved_days_with_pay" 
                                           value="{{ old('approved_days_with_pay') }}" 
                                           placeholder="Number of days"
                                           @if(!$canEditOps) readonly @endif
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 @if(!$canEditOps) bg-gray-100 dark:bg-gray-600 @endif bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>

                                <div>
                                    <label for="approved_days_without_pay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved for _____ days without pay</label>
                                    <input type="number" name="approved_days_without_pay" id="approved_days_without_pay" 
                                           value="{{ old('approved_days_without_pay') }}" 
                                           placeholder="Number of days"
                                           @if(!$canEditOps) readonly @endif
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 @if(!$canEditOps) bg-gray-100 dark:bg-gray-600 @endif bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label for="disapproved_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disapproved due to:</label>
                                    <textarea name="disapproved_reason" id="disapproved_reason" rows="2" 
                                              placeholder="Reason for disapproval (if applicable)"
                                              @if(!$canEditOps) readonly @endif
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 @if(!$canEditOps) bg-gray-100 dark:bg-gray-600 @endif bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('disapproved_reason') }}</textarea>
                                </div>

                                <div>
                                    <label for="deferred_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deferred until:</label>
                                    <input type="date" name="deferred_until" id="deferred_until" 
                                           value="{{ old('deferred_until') }}" 
                                           @if(!$canEditOps) readonly @endif
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 @if(!$canEditOps) bg-gray-100 dark:bg-gray-600 @endif bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                            </div>

                            @if(!$canEditOps)
                                <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 dark:border-blue-600 p-3 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400 dark:text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                                <strong>Notice:</strong> This section can only be edited by Operations Manager or Admin personnel.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                                <p><strong>Approved by:</strong></p>
                                @if($canEditOps)
                                    <input type="text" name="ops_approved_by" id="ops_approved_by" 
                                           value="{{ old('ops_approved_by', 'Antonio L. Castro') }}" 
                                           placeholder="Operations Manager Name"
                                           class="mt-1 block w-full max-w-xs rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <input type="text" name="ops_title" id="ops_title" 
                                           value="{{ old('ops_title', 'Operations Manager') }}" 
                                           placeholder="Operations Manager Title"
                                           class="mt-1 block w-full max-w-xs rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @else
                                    <p>Antonio L. Castro</p>
                                    <p>Operations Manager</p>
                                @endif
                            </div>
                        </div>

                        <!-- Supporting Documents -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">SUPPORTING DOCUMENTS</h4>
                            
                            <div class="mb-4">
                                <label for="supporting_document" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supporting Document (Optional)</label>
                                <input type="file" name="supporting_document" id="supporting_document" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-800">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload any supporting documents (medical certificate, etc.). Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)</p>
                                @error('supporting_document')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Notes</label>
                                <textarea name="notes" id="notes" rows="3" 
                                          placeholder="Any additional information..."
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('leave-applications.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calculate days between start and end date
        function calculateDays() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                
                if (end >= start) {
                    const timeDiff = end.getTime() - start.getTime();
                    const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 to include both start and end date
                    document.getElementById('calculated_days').value = daysDiff + ' days';
                } else {
                    document.getElementById('calculated_days').value = 'Invalid date range';
                }
            } else {
                document.getElementById('calculated_days').value = '';
            }
        }

        // Load crew leave credits when crew is selected
        async function loadCrewCredits() {
            const crewId = document.getElementById('crew_id').value;
            
            if (crewId) {
                try {
                    const response = await fetch(`/crew/${crewId}/leave-credits`);
                    const data = await response.json();
                    
                    if (data.success) {
                        document.getElementById('vacation_credits_display').textContent = data.vacation_credits + ' days';
                        document.getElementById('sick_credits_display').textContent = data.sick_credits + ' days';
                    } else {
                        document.getElementById('vacation_credits_display').textContent = '-- days';
                        document.getElementById('sick_credits_display').textContent = '-- days';
                    }
                } catch (error) {
                    console.error('Error loading crew credits:', error);
                    document.getElementById('vacation_credits_display').textContent = '-- days';
                    document.getElementById('sick_credits_display').textContent = '-- days';
                }
            } else {
                document.getElementById('vacation_credits_display').textContent = '-- days';
                document.getElementById('sick_credits_display').textContent = '-- days';
            }
        }

        // Toggle vessel field based on crew selection
        function toggleVesselField() {
            const crewId = document.getElementById('crew_id').value;
            const vesselField = document.getElementById('vessel_field');
            
            if (crewId) {
                // Check if selected crew is assigned to a vessel
                const selectedOption = document.querySelector(`#crew_id option[value="${crewId}"]`);
                if (selectedOption && selectedOption.dataset.ship) {
                    vesselField.style.display = 'block';
                    document.getElementById('vessel_number').value = selectedOption.dataset.ship;
                } else {
                    vesselField.style.display = 'none';
                    document.getElementById('vessel_number').value = '';
                }
            } else {
                vesselField.style.display = 'none';
                document.getElementById('vessel_number').value = '';
            }
        }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('start_date').addEventListener('change', calculateDays);
            document.getElementById('end_date').addEventListener('change', calculateDays);
            document.getElementById('crew_id').addEventListener('change', function() {
                loadCrewCredits();
                toggleVesselField();
            });
            
            // Set minimum end date based on start date
            document.getElementById('start_date').addEventListener('change', function() {
                const startDate = this.value;
                const endDateInput = document.getElementById('end_date');
                
                if (startDate) {
                    endDateInput.min = startDate;
                    if (endDateInput.value && endDateInput.value < startDate) {
                        endDateInput.value = startDate;
                    }
                }
            });
            
            // Initial load if crew is pre-selected
            if (document.getElementById('crew_id').value) {
                loadCrewCredits();
                toggleVesselField();
            }

            // Show/hide other leave type input when leave_type changes
            const leaveTypeSelect = document.getElementById('leave_type');
            const otherDiv = document.getElementById('other_leave_type_div');
            function toggleOtherLeaveType() {
                if (leaveTypeSelect && otherDiv) {
                    if (leaveTypeSelect.value === 'other') {
                        otherDiv.classList.remove('hidden');
                    } else {
                        otherDiv.classList.add('hidden');
                    }
                }
            }

            if (leaveTypeSelect) {
                leaveTypeSelect.addEventListener('change', toggleOtherLeaveType);
                // initial state
                toggleOtherLeaveType();
            }

            // Client-side required field check + fetch-based submit
            function validateRequiredFields(form) {
                const requiredSelectors = ['#crew_id', '#date_applied', '#leave_type', '#start_date', '#end_date', '#reason'];
                const missing = [];

                // clear previous highlights
                requiredSelectors.forEach(sel => {
                    const el = document.querySelector(sel);
                    if (el) el.classList.remove('ring-2', 'ring-red-400');
                });

                // special case: if leave_type == 'other' then other_leave_type is required
                const leaveTypeEl = document.getElementById('leave_type');
                const leaveTypeVal = leaveTypeEl ? leaveTypeEl.value : '';
                if (leaveTypeVal === 'other') {
                    requiredSelectors.push('#other_leave_type');
                }

                requiredSelectors.forEach(sel => {
                    const el = document.querySelector(sel);
                    if (!el) return;

                    const value = el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA'
                        ? el.value : '';

                    if (!value || value.trim() === '') {
                        missing.push(sel.replace('#', ''));
                        el.classList.add('ring-2', 'ring-red-400');
                    }
                });

                return missing;
            }

            try {
                const leaveForm = document.getElementById('leaveApplicationForm');
                if (leaveForm) {
                    leaveForm.addEventListener('submit', function(ev) {
                        ev.preventDefault();
                        ev.stopPropagation();

                        const submitBtn = leaveForm.querySelector('button[type="submit"]');

                        // run client-side validation
                        const missing = validateRequiredFields(leaveForm);
                        const errorsBox = document.getElementById('formErrors');
                        const errorsList = document.getElementById('formErrorsList');

                        if (missing.length > 0) {
                            errorsList.innerHTML = '';
                            missing.forEach(f => {
                                const li = document.createElement('li');
                                li.textContent = f.replace(/_/g, ' ');
                                errorsList.appendChild(li);
                            });
                            errorsBox.classList.remove('hidden');
                            errorsBox.scrollIntoView({ behavior: 'smooth' });
                            return;
                        } else {
                            errorsBox.classList.add('hidden');
                            errorsList.innerHTML = '';
                        }

                        // Check native validity too
                        if (!leaveForm.checkValidity()) {
                            leaveForm.reportValidity();
                            return;
                        }

                        // Build FormData (includes files)
                        const formData = new FormData(leaveForm);

                        // Fetch options
                        const action = leaveForm.getAttribute('action') || window.location.href;
                        const method = (leaveForm.getAttribute('method') || 'POST').toUpperCase();

                        // Add X-Requested-With for server-side detection
                        const headers = { 'X-Requested-With': 'XMLHttpRequest' };

                        // Show simple loading state
                        const originalText = submitBtn ? submitBtn.textContent : '';
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Submitting...';
                        }

                        fetch(action, {
                            method: method,
                            body: formData,
                            headers: headers,
                            credentials: 'same-origin'
                        })
                        .then(async (response) => {
                            if (response.redirected) {
                                window.location.href = response.url;
                                return;
                            }

                            const contentType = response.headers.get('content-type') || '';
                            if (contentType.includes('application/json')) {
                                const json = await response.json();
                                if (json.success) {
                                    if (json.redirect) {
                                        window.location.href = json.redirect;
                                    } else {
                                        window.location.reload();
                                    }
                                } else {
                                    // show validation errors
                                    const errors = json.errors || [json.message || 'Submission failed'];
                                    errorsList.innerHTML = '';
                                    errors.forEach(e => {
                                        const li = document.createElement('li');
                                        li.textContent = e;
                                        errorsList.appendChild(li);
                                    });
                                    errorsBox.classList.remove('hidden');
                                    if (submitBtn) {
                                        submitBtn.disabled = false;
                                        submitBtn.textContent = originalText;
                                    }
                                }
                            } else {
                                const text = await response.text();
                                const loc = response.headers.get('Location');
                                if (loc) {
                                    window.location.href = loc;
                                } else {
                                    document.open();
                                    document.write(text);
                                    document.close();
                                }
                            }
                        })
                        .catch((err) => {
                            console.error('Leave application submit error', err);
                            alert('An error occurred while submitting. Check console for details.');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalText;
                            }
                        });
                    });
                }
            } catch (err) {
                console.error('Leave form submit fetch fallback error:', err);
            }
        });
    </script>
</x-app-layout>
