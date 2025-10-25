@php
    use App\Models\CrewLeave;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Edit Leave Credits - {{ $crew->full_name }}
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
            <!-- Crew Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Employee Information Grid -->
                        <div class="flex-1 pr-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Employee Information</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Employee ID: {{ $crew->employee_id }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Name: {{ $crew->full_name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Position: {{ $crew->position }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Department: {{ ucfirst(str_replace('_', ' ', $crew->department)) }}</p>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Current Status</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Employment Status: 
                                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                            {{ ucfirst($crew->employment_status) }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Hire Date: {{ $crew->hire_date?->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Current Year Credits</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Credits ({{ $currentYear }}): 
                                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ $crew->total_leave_credits }} days</span>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Available Credits: 
                                        <span class="font-bold text-green-600 dark:text-green-400">{{ $crew->available_leave_credits }} days</span>
                                    </p>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Ship Assignment</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Crew ID Picture -->
                        <div class="flex-shrink-0 ml-16 mr-8">
                            @php
                                $idPicture = $crew->documents->where('document_type', 'id_picture')->first();
                            @endphp
                            @if($idPicture)
                                <div class="relative">
                                    <img src="{{ route('crew-documents.download', $idPicture) }}" 
                                         alt="Crew ID Picture" 
                                         class="w-48 h-48 object-cover rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-600">
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center max-w-[192px]">{{ $idPicture->document_name }}</p>
                            @else
                                <div class="w-48 h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg flex items-center justify-center border-2 border-gray-200 dark:border-gray-600 shadow-md">
                                    <svg class="w-20 h-20 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center max-w-[192px]">No ID Picture</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Credits History -->
            @if($leaveCredits->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Leave Credits History</h3>
                        
                        @foreach($leaveCredits as $year => $yearCredits)
                            <div class="mb-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ $year }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                    @foreach($yearCredits as $credit)
                                        <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $credit->leave_type_name }}:</span>
                                            <span class="text-sm text-blue-600 dark:text-blue-400 font-bold">{{ $credit->credits }} days</span>
                                            @if($credit->notes)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $credit->notes }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Edit Leave Credits Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Edit Leave Credits</h3>
                    
                    <form method="POST" action="{{ route('leave-credits.update', $crew) }}" id="leaveCreditsForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Year</label>
                            <select name="year" id="year" onchange="loadExistingCredits()" 
                                    class="block w-full md:w-auto rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="leaveCreditsContainer">
                            @foreach(CrewLeave::LEAVE_TYPES as $type => $label)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ $label }}</h4>
                                    
                                    <input type="hidden" name="leave_credits[{{ $loop->index }}][leave_type]" value="{{ $type }}">
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Credits (days)</label>
                                        <input type="number" 
                                               name="leave_credits[{{ $loop->index }}][credits]" 
                                               id="credits_{{ $type }}"
                                               min="0" 
                                               max="365" 
                                               step="0.5"
                                               value="0"
                                               class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                                        <textarea name="leave_credits[{{ $loop->index }}][notes]" 
                                                  id="notes_{{ $type }}"
                                                  rows="2" 
                                                  maxlength="500"
                                                  placeholder="Optional notes for this leave type..."
                                                  class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('leave-credits.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Leave Credits
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="successMessage" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
            {{ session('success') }}
            <button onclick="this.parentElement.remove()" class="ml-2 text-green-700 hover:text-green-900">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                const msg = document.getElementById('successMessage');
                if (msg) msg.remove();
            }, 5000);
        </script>
    @endif

    @if($errors->any())
        <div id="errorMessage" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
            <ul class="text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button onclick="this.parentElement.remove()" class="ml-2 text-red-700 hover:text-red-900">&times;</button>
        </div>
        <script>
            setTimeout(function() {
                const msg = document.getElementById('errorMessage');
                if (msg) msg.remove();
            }, 8000);
        </script>
    @endif

    <script>
        // Existing leave credits data
        const leaveCreditsData = @json($leaveCredits);
        
        function loadExistingCredits() {
            const selectedYear = document.getElementById('year').value;
            const yearCredits = leaveCreditsData[selectedYear] || [];
            
            // Reset all fields
            @foreach(array_keys(CrewLeave::LEAVE_TYPES) as $type)
                document.getElementById('credits_{{ $type }}').value = 0;
                document.getElementById('notes_{{ $type }}').value = '';
            @endforeach
            
            // Populate existing data
            yearCredits.forEach(function(credit) {
                const creditsField = document.getElementById('credits_' + credit.leave_type);
                const notesField = document.getElementById('notes_' + credit.leave_type);
                
                if (creditsField) {
                    creditsField.value = credit.credits;
                }
                if (notesField && credit.notes) {
                    notesField.value = credit.notes;
                }
            });
        }
        
        // Load existing credits on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadExistingCredits();
        });
        
        // Form validation
        document.getElementById('leaveCreditsForm').addEventListener('submit', function(e) {
            let totalCredits = 0;
            let hasAnyCredits = false;
            
            @foreach(array_keys(CrewLeave::LEAVE_TYPES) as $type)
                const credits{{ ucfirst($type) }} = parseFloat(document.getElementById('credits_{{ $type }}').value) || 0;
                totalCredits += credits{{ ucfirst($type) }};
                if (credits{{ ucfirst($type) }} > 0) hasAnyCredits = true;
            @endforeach
            
            if (totalCredits > 365) {
                e.preventDefault();
                alert('Total leave credits cannot exceed 365 days in a year.');
                return false;
            }
            
            if (!hasAnyCredits) {
                if (!confirm('No leave credits were entered. This will remove all existing leave credits for the selected year. Continue?')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
    </script>
</x-app-layout>
