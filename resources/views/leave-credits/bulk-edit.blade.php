@php
    use App\Models\CrewLeave;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Bulk Update Leave Credits
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('leave-credits.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Instructions -->
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400 dark:text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">How Bulk Update Works</h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Select employees by department or individual selection</li>
                                <li>Choose the leave type and credits to assign</li>
                                <li>This will <strong>update or create</strong> leave credits for the selected year</li>
                                <li>Existing credits for the same type and year will be overwritten</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Update Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6">Bulk Update Leave Credits</h3>
                    
                    <form method="POST" action="{{ route('leave-credits.bulk-update') }}" id="bulkUpdateForm">
                        @csrf
                        
                        <!-- Basic Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year *</label>
                                <select name="year" id="year" required
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            
                            <div>
                                <label for="leave_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Leave Type *</label>
                                <select name="leave_type" id="leave_type" required
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @foreach(CrewLeave::LEAVE_TYPES as $type => $label)
                                        <option value="{{ $type }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="credits" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Credits (days) *</label>
                                <input type="number" name="credits" id="credits" required
                                       min="0" max="365" step="0.5" value="15"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <!-- Selection Method -->
                        <div class="mb-8">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-4">Employee Selection</h4>
                            
                            <div class="space-y-4">
                                <!-- Department Selection -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="radio" name="selection_method" value="department" checked
                                               class="form-radio h-4 w-4 text-indigo-600 dark:text-indigo-400" onchange="toggleSelectionMethod()">
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Select by Department</span>
                                    </label>
                                    
                                    <div id="departmentSelection" class="mt-2 ml-6">
                                        <select name="department" id="department"
                                                class="block w-full md:w-auto rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept }}">{{ ucfirst(str_replace('_', ' ', $dept)) }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave empty to apply to all active employees</p>
                                    </div>
                                </div>
                                
                                <!-- Individual Selection -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="radio" name="selection_method" value="individual"
                                               class="form-radio h-4 w-4 text-indigo-600 dark:text-indigo-400" onchange="toggleSelectionMethod()">
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Select Individual Employees</span>
                                    </label>
                                    
                                    <div id="individualSelection" class="mt-2 ml-6 hidden">
                                        <div class="mb-3">
                                            <input type="text" id="employeeSearch" placeholder="Search employees..."
                                                   class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                   onkeyup="filterEmployees()">
                                        </div>
                                        
                                        <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md p-3">
                                            <div class="mb-2">
                                                <label class="flex items-center">
                                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                                           class="form-checkbox h-4 w-4 text-indigo-600 dark:text-indigo-400">
                                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Select All</span>
                                                </label>
                                            </div>
                                            
                                            <div class="space-y-2" id="employeesList">
                                                @foreach($crews as $crew)
                                                    <label class="flex items-center employee-item" data-name="{{ strtolower($crew->full_name) }}" data-id="{{ strtolower($crew->employee_id) }}" data-dept="{{ strtolower($crew->department) }}">
                                                        <input type="checkbox" name="crew_ids[]" value="{{ $crew->id }}"
                                                               class="form-checkbox h-4 w-4 text-indigo-600 dark:text-indigo-400 employee-checkbox">
                                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $crew->full_name }} ({{ $crew->employee_id }}) - {{ ucfirst(str_replace('_', ' ', $crew->department)) }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            Selected: <span id="selectedCount">0</span> employees
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" maxlength="500"
                                      placeholder="Optional notes for these leave credits..."
                                      class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span id="notesCharCount">0</span>/500 characters
                            </p>
                        </div>

                        <!-- Summary -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
                            <h4 class="font-semibold text-gray-800 dark:text-white mb-2">Update Summary</h4>
                            <div id="updateSummary" class="text-sm text-gray-600 dark:text-gray-300">
                                <p>Year: <span id="summaryYear">{{ date('Y') }}</span></p>
                                <p>Leave Type: <span id="summaryLeaveType">Vacation Leave</span></p>
                                <p>Credits: <span id="summaryCredits">15</span> days</p>
                                <p>Target: <span id="summaryTarget">All departments</span></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('leave-credits.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                    onclick="return confirmBulkUpdate()">
                                Apply Bulk Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="successMessage" class="fixed top-4 right-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded z-50">
            {{ session('success') }}
            <button onclick="this.parentElement.remove()" class="ml-2 text-green-700 dark:text-green-300 hover:text-green-900 dark:hover:text-green-100">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                const msg = document.getElementById('successMessage');
                if (msg) msg.remove();
            }, 5000);
        </script>
    @endif

    @if($errors->any())
        <div id="errorMessage" class="fixed top-4 right-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded z-50">
            <ul class="text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button onclick="this.parentElement.remove()" class="ml-2 text-red-700 dark:text-red-300 hover:text-red-900 dark:hover:text-red-100">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                const msg = document.getElementById('errorMessage');
                if (msg) msg.remove();
            }, 8000);
        </script>
    @endif

    <script>
        // Toggle between selection methods
        function toggleSelectionMethod() {
            const method = document.querySelector('input[name="selection_method"]:checked').value;
            const deptSelection = document.getElementById('departmentSelection');
            const indSelection = document.getElementById('individualSelection');
            
            if (method === 'department') {
                deptSelection.classList.remove('hidden');
                indSelection.classList.add('hidden');
            } else {
                deptSelection.classList.add('hidden');
                indSelection.classList.remove('hidden');
            }
            updateSummary();
        }
        
        // Filter employees in individual selection
        function filterEmployees() {
            const searchTerm = document.getElementById('employeeSearch').value.toLowerCase();
            const employees = document.querySelectorAll('.employee-item');
            
            employees.forEach(function(employee) {
                const name = employee.dataset.name;
                const id = employee.dataset.id;
                const dept = employee.dataset.dept;
                
                if (name.includes(searchTerm) || id.includes(searchTerm) || dept.includes(searchTerm)) {
                    employee.style.display = 'flex';
                } else {
                    employee.style.display = 'none';
                }
            });
        }
        
        // Toggle select all
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            
            checkboxes.forEach(function(checkbox) {
                if (checkbox.closest('.employee-item').style.display !== 'none') {
                    checkbox.checked = selectAll.checked;
                }
            });
            
            updateSelectedCount();
        }
        
        // Update selected count
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.employee-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = checked;
            updateSummary();
        }
        
        // Update summary
        function updateSummary() {
            const year = document.getElementById('year').value;
            const leaveType = document.getElementById('leave_type');
            const credits = document.getElementById('credits').value;
            const method = document.querySelector('input[name="selection_method"]:checked').value;
            
            document.getElementById('summaryYear').textContent = year;
            document.getElementById('summaryLeaveType').textContent = leaveType.options[leaveType.selectedIndex].text;
            document.getElementById('summaryCredits').textContent = credits;
            
            if (method === 'department') {
                const dept = document.getElementById('department');
                const target = dept.value ? dept.options[dept.selectedIndex].text : 'All departments';
                document.getElementById('summaryTarget').textContent = target;
            } else {
                const count = document.querySelectorAll('.employee-checkbox:checked').length;
                document.getElementById('summaryTarget').textContent = count + ' selected employees';
            }
        }
        
        // Character counter for notes
        document.getElementById('notes').addEventListener('input', function() {
            document.getElementById('notesCharCount').textContent = this.value.length;
        });
        
        // Update summary when form values change
        document.getElementById('year').addEventListener('change', updateSummary);
        document.getElementById('leave_type').addEventListener('change', updateSummary);
        document.getElementById('credits').addEventListener('input', updateSummary);
        document.getElementById('department').addEventListener('change', updateSummary);
        
        // Add event listeners to checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            
            updateSummary();
        });
        
        // Confirm bulk update
        function confirmBulkUpdate() {
            const method = document.querySelector('input[name="selection_method"]:checked').value;
            const year = document.getElementById('year').value;
            const leaveType = document.getElementById('leave_type');
            const credits = document.getElementById('credits').value;
            
            let targetCount;
            if (method === 'department') {
                const dept = document.getElementById('department').value;
                targetCount = dept ? 'employees in selected department' : 'all active employees';
            } else {
                targetCount = document.querySelectorAll('.employee-checkbox:checked').length + ' selected employees';
                
                if (targetCount === '0 selected employees') {
                    alert('Please select at least one employee.');
                    return false;
                }
            }
            
            const message = `Are you sure you want to update leave credits for ${targetCount}?\n\n` +
                           `Year: ${year}\n` +
                           `Leave Type: ${leaveType.options[leaveType.selectedIndex].text}\n` +
                           `Credits: ${credits} days\n\n` +
                           `This will overwrite existing credits for the same leave type and year.`;
            
            return confirm(message);
        }
    </script>
</x-app-layout>
