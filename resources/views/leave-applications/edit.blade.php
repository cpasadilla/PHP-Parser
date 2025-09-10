<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Edit Leave Application') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('leave-applications.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Applications
                </a>
                <a href="{{ route('leave-applications.show', $leaveApplication) }}" 
                   class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    View Details
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">EDIT LEAVE APPLICATION</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            Application #{{ $leaveApplication->id }} - Status: 
                            <span class="font-semibold text-{{ $leaveApplication->status == 'pending' ? 'yellow' : 'gray' }}-600 dark:text-{{ $leaveApplication->status == 'pending' ? 'yellow' : 'gray' }}-400">
                                {{ ucfirst($leaveApplication->status) }}
                            </span>
                        </p>
                    </div>

                    @if($leaveApplication->status !== 'pending')
                        <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded mb-6">
                            <strong class="font-bold">Note:</strong>
                            <span class="block sm:inline">This application has been processed and cannot be modified.</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('leave-applications.update', $leaveApplication) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Employee Information Section -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">EMPLOYEE INFORMATION</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label for="crew_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name of Employee *</label>
                                    <select name="crew_id" id="crew_id" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                            {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>
                                        @foreach($crews as $crewMember)
                                            <option value="{{ $crewMember->id }}" 
                                                    {{ $leaveApplication->crew_id == $crewMember->id ? 'selected' : '' }}
                                                    data-position="{{ $crewMember->position }}"
                                                    data-ship="{{ $crewMember->ship ? 'MV EVERWIN STAR ' . $crewMember->ship->ship_number : '' }}"
                                                    data-department="{{ $crewMember->department }}">
                                                {{ $crewMember->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('crew_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                    <input type="text" id="position_display" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm text-gray-500 dark:text-gray-400" 
                                           value="{{ $leaveApplication->crew->position }}" readonly>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="department_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                                    <input type="text" id="department_display" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm text-gray-500 dark:text-gray-400" 
                                           value="{{ $leaveApplication->crew->department_name }}" readonly>
                                </div>

                                <div id="vessel_field" style="display: {{ $leaveApplication->crew->ship ? 'block' : 'none' }};">
                                    <label for="vessel_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vessel Number</label>
                                    <input type="text" id="vessel_number" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm text-gray-500 dark:text-gray-400" 
                                           value="{{ $leaveApplication->crew->ship ? 'MV EVERWIN STAR ' . $leaveApplication->crew->ship->ship_number : '' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Leave Information Section -->
                        <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">LEAVE INFORMATION</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <div>
                                    <label for="leave_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type of Leave *</label>
                                    <select name="leave_type" id="leave_type" required
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                            {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>
                                        @foreach(['vacation' => 'Vacation Leave', 'sick' => 'Sick Leave', 'emergency' => 'Emergency Leave', 'maternity' => 'Maternity Leave', 'paternity' => 'Paternity Leave', 'bereavement' => 'Bereavement Leave', 'other' => 'Other'] as $value => $label)
                                            <option value="{{ $value }}" {{ $leaveApplication->leave_type == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('leave_type')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div id="other_leave_type_field" style="display: {{ $leaveApplication->leave_type == 'other' ? 'block' : 'none' }};">
                                    <label for="other_leave_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Specify Other Leave Type *</label>
                                    <input type="text" name="other_leave_type" id="other_leave_type" 
                                           value="{{ old('other_leave_type', $leaveApplication->other_leave_type) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>
                                    @error('other_leave_type')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date *</label>
                                    <input type="date" name="start_date" id="start_date" required
                                           value="{{ old('start_date', $leaveApplication->start_date->format('Y-m-d')) }}"
                                           min="{{ date('Y-m-d') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>
                                    @error('start_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date *</label>
                                    <input type="date" name="end_date" id="end_date" required
                                           value="{{ old('end_date', $leaveApplication->end_date->format('Y-m-d')) }}"
                                           min="{{ old('start_date', $leaveApplication->start_date->format('Y-m-d')) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                           {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>
                                    @error('end_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="days_requested_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Days Requested</label>
                                    <input type="number" id="days_requested_display" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 shadow-sm text-gray-500 dark:text-gray-400" 
                                           value="{{ $leaveApplication->days_requested }}" readonly>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Leave *</label>
                                <textarea name="reason" id="reason" rows="3" required
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                          {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>{{ old('reason', $leaveApplication->reason) }}</textarea>
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="supporting_document" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supporting Document</label>
                                @if($leaveApplication->file_path)
                                    <p class="text-sm text-gray-600 mb-2">
                                        Current file: 
                                        <a href="{{ route('leave-applications.download', $leaveApplication) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ basename($leaveApplication->file_path) }}
                                        </a>
                                    </p>
                                @endif
                                <input type="file" name="supporting_document" id="supporting_document" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-800"
                                       {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>
                                <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, PNG, DOC, DOCX (max 5MB)</p>
                                @error('supporting_document')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Notes</label>
                                <textarea name="notes" id="notes" rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                          {{ $leaveApplication->status !== 'pending' ? 'disabled' : '' }}>{{ old('notes', $leaveApplication->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        @if($leaveApplication->status === 'pending')
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">Note:</span> Only pending applications can be edited.
                            </div>
                            <div class="flex space-x-4">
                                <a href="{{ route('leave-applications.show', $leaveApplication) }}" 
                                   class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update Application
                                </button>
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-center text-gray-600 dark:text-gray-400">
                                This application cannot be edited because it has been {{ $leaveApplication->status }}.
                            </p>
                            <div class="flex justify-center mt-4">
                                <a href="{{ route('leave-applications.show', $leaveApplication) }}" 
                                   class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    View Application Details
                                </a>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateDays() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const timeDiff = end.getTime() - start.getTime();
                const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 to include both start and end dates
                
                document.getElementById('days_requested_display').value = daysDiff > 0 ? daysDiff : 0;
            }
        }

        function toggleOtherLeaveType() {
            const leaveType = document.getElementById('leave_type').value;
            const otherField = document.getElementById('other_leave_type_field');
            const otherInput = document.getElementById('other_leave_type');
            
            if (leaveType === 'other') {
                otherField.style.display = 'block';
                otherInput.required = true;
            } else {
                otherField.style.display = 'none';
                otherInput.required = false;
                otherInput.value = '';
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Only add event listeners if the form is editable
            @if($leaveApplication->status === 'pending')
            document.getElementById('start_date').addEventListener('change', calculateDays);
            document.getElementById('end_date').addEventListener('change', calculateDays);
            document.getElementById('leave_type').addEventListener('change', toggleOtherLeaveType);
            
            // Set minimum end date based on start date
            document.getElementById('start_date').addEventListener('change', function() {
                const startDate = this.value;
                const endDateInput = document.getElementById('end_date');
                
                if (startDate) {
                    endDateInput.min = startDate;
                    if (endDateInput.value && endDateInput.value < startDate) {
                        endDateInput.value = startDate;
                        calculateDays();
                    }
                }
            });
            @endif
            
            // Calculate initial days
            calculateDays();
        });
    </script>
</x-app-layout>
