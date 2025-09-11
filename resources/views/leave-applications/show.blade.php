<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Leave Application Details') }}
            </h2>
            <div class="space-x-2">
                @if($leaveApplication->status === 'pending')
                    <a href="{{ route('leave-applications.edit', $leaveApplication) }}" 
                       class="bg-indigo-500 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endif
                <a href="{{ route('leave-applications.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Applications
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    
                    <!-- Status Badge -->
                    <div class="mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($leaveApplication->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @elseif($leaveApplication->status === 'hr_review') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                            @elseif($leaveApplication->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($leaveApplication->status === 'disapproved') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @elseif($leaveApplication->status === 'deferred') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $leaveApplication->status)) }}
                        </span>
                    </div>

                    <!-- Employee Information Section -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Employee Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name of Employee</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($leaveApplication->crew)
                                        {{ $leaveApplication->crew->full_name }}
                                    @else
                                        <span class="text-red-600 dark:text-red-400">Crew Record Not Found (ID: {{ $leaveApplication->crew_id }})</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Applied</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($leaveApplication->date_applied)->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Designation/Position</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($leaveApplication->crew)
                                        {{ $leaveApplication->crew->position }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">N/A</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vessel Number</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($leaveApplication->crew)
                                        {{ $leaveApplication->crew->ship?->name ?? 'N/A' }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Details Section -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Leave Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type of Leave</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ ucfirst($leaveApplication->leave_type) }}
                                    @if($leaveApplication->leave_type === 'others' && $leaveApplication->others_specify)
                                        ({{ $leaveApplication->others_specify }})
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($leaveApplication->start_date)->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($leaveApplication->end_date)->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Days</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->days_requested }} day{{ $leaveApplication->days_requested !== 1 ? 's' : '' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Leave</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->reason }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Section -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Approval Section</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved by (Department Head)</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->department_head_name ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Noted By (Captain/Master)</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->captain_master_name ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Noted by (Manager)</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->manager_name ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- HR Department Section -->
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">HR Department Section</h3>

                        @if($leaveApplication->status === 'pending')
                            <!-- HR Review Form -->
                            <form method="POST" action="{{ route('leave-applications.hr-review', $leaveApplication) }}" class="mb-6">
                                @csrf
                                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">HR Review</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div>
                                            <label for="leave_credits_as_of" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Available leave credit as of</label>
                                            <input type="date" name="leave_credits_as_of" id="leave_credits_as_of" 
                                                   value="{{ date('Y-m-d') }}" required
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label for="vacation_leave_credits" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vacation Leave (days)</label>
                                            <input type="number" name="vacation_leave_credits" id="vacation_leave_credits" 
                                                   value="{{ $availableVacationCredits }}" min="0" step="0.5" required
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label for="sick_leave_credits" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sick Leave (days)</label>
                                            <input type="number" name="sick_leave_credits" id="sick_leave_credits" 
                                                   value="{{ $availableSickCredits }}" min="0" step="0.5" required
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label for="filled_out_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filled out by</label>
                                            <input type="text" name="filled_out_by" id="filled_out_by" 
                                                   value="Carla E. Alcon"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        <div>
                                            <label for="filled_out_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                            <input type="text" name="filled_out_position" id="filled_out_position" 
                                                   value="HRMO"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">
                                            Submit HR Review
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @elseif($leaveApplication->status !== 'pending')
                            <!-- Display HR Review Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Available leave credit as of</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $leaveApplication->leave_credits_as_of ? \Carbon\Carbon::parse($leaveApplication->leave_credits_as_of)->format('M d, Y') : 'Not specified' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vacation Leave</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->vacation_leave_credits ?? 'Not specified' }} days</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sick Leave</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->sick_leave_credits ?? 'Not specified' }} days</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filled out by</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->filled_out_by ?: 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->filled_out_position ?: 'Not specified' }}</p>
                                </div>
                            </div>
                        @endif

                        @if($leaveApplication->status === 'hr_review')
                            <!-- Final Approval Form -->
                            <form method="POST" action="{{ route('leave-applications.final-approval', $leaveApplication) }}">
                                @csrf
                                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                    <h4 class="text-md font-medium text-green-800 dark:text-green-200 mb-3">Final Approval</h4>
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="approved_days_with_pay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved days with pay</label>
                                                <input type="number" name="approved_days_with_pay" id="approved_days_with_pay" 
                                                       min="0" max="{{ $leaveApplication->days_requested }}" step="0.5"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 dark:focus:border-green-400 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                            <div>
                                                <label for="approved_days_without_pay" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved days without pay</label>
                                                <input type="number" name="approved_days_without_pay" id="approved_days_without_pay" 
                                                       min="0" max="{{ $leaveApplication->days_requested }}" step="0.5"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 dark:focus:border-green-400 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="disapproved_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disapproved due to</label>
                                            <textarea name="disapproved_reason" id="disapproved_reason" rows="2"
                                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 dark:focus:border-green-400 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                                        </div>
                                        
                                        <div>
                                            <label for="deferred_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deferred until</label>
                                            <input type="date" name="deferred_until" id="deferred_until" 
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 dark:focus:border-green-400 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="final_approved_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved by</label>
                                                <input type="text" name="final_approved_by" id="final_approved_by" 
                                                       value="Antonio L. Castro"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 dark:focus:border-green-400 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                            <div>
                                                <label for="final_approved_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                                <input type="text" name="final_approved_position" id="final_approved_position" 
                                                       value="Operations Manager"
                                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-green-500 dark:focus:border-green-400 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </div>
                                        </div>
                                        
                                        <div class="flex space-x-4">
                                            <button type="submit" name="action" value="approve" 
                                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                                Approve
                                            </button>
                                            <button type="submit" name="action" value="disapprove" 
                                                    class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                                Disapprove
                                            </button>
                                            <button type="submit" name="action" value="defer" 
                                                    class="bg-yellow-600 hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                                Defer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <!-- Display Final Decision -->
                        @if($leaveApplication->status === 'approved')
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <h4 class="text-md font-medium text-green-800 dark:text-green-200 mb-2">Approved</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Days with pay</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->approved_days_with_pay ?? 0 }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Days without pay</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->approved_days_without_pay ?? 0 }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved by</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->final_approved_by }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->final_approved_position }}</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($leaveApplication->status === 'disapproved')
                            <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                                <h4 class="text-md font-medium text-red-800 dark:text-red-200 mb-2">Disapproved</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->disapproved_reason }}</p>
                                </div>
                            </div>
                        @elseif($leaveApplication->status === 'deferred')
                            <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                                <h4 class="text-md font-medium text-purple-800 dark:text-purple-200 mb-2">Deferred</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deferred until</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($leaveApplication->deferred_until)->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Supporting Documents -->
                    @if($leaveApplication->file_path)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Supporting Document</h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ basename($leaveApplication->file_path) }}</p>
                                </div>
                                <a href="{{ route('leave-applications.download', $leaveApplication) }}" 
                                   class="bg-indigo-500 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Download
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Additional Notes -->
                    @if($leaveApplication->notes)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Additional Notes</h3>
                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->notes }}</p>
                        </div>
                    @endif

                    <!-- Processing Information -->
                    @if($leaveApplication->processed_at)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Processing Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Processed by</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $leaveApplication->processedBy?->name ?? 'System' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Processed at</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($leaveApplication->processed_at)->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
