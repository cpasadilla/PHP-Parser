@php
    $ships = $ships ?? [];
    $selectedShip = $selectedShip ?? '';
    $selectedDepartment = $selectedDepartment ?? '';
    $search = $search ?? '';
@endphp

@if(!auth()->user()->hasPermission('crew', 'access') && !auth()->user()->hasSubpagePermission('crew', 'crew-management', 'access'))
    <x-app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                        <h3 class="text-lg font-semibold mb-2">Access Denied</h3>
                        <p>You don't have permission to access Crew Management.</p>
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
                {{ __('Crew Management') }}
            </h2>
            <div class="flex space-x-2">
                <div class="relative">
                    <button type="button" onclick="toggleExportDropdown()" 
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10 max-h-96 overflow-y-auto">
                        <div class="py-1">
                            <h4 class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600">Export All Crew</h4>
                            <a href="{{ route('crew.export.pdf') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Export to PDF
                            </a>
                            <a href="{{ route('crew.export.excel') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export to Excel
                            </a>
                            
                            <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>
                            <h4 class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">Export by Ship</h4>
                            @foreach($ships as $ship)
                                <div class="px-4 py-1">
                                    <p class="text-xs font-medium text-gray-600 mb-1">MV EVERWIN STAR {{ $ship->ship_number }}</p>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('crew.export.pdf', ['ship' => $ship->id]) }}" 
                                           class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                        <a href="{{ route('crew.export.excel', ['ship' => $ship->id]) }}" 
                                           class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>
                            <h4 class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">Export by Department</h4>
                            
                            <!-- Office/Shore Personnel -->
                            <div class="px-4 py-1">
                                <p class="text-xs font-medium text-gray-600 mb-1">Office/Shore Personnel</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('crew.export.pdf', ['department' => 'office_shore']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                    <a href="{{ route('crew.export.excel', ['department' => 'office_shore']) }}" 
                                       class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                </div>
                            </div>
                            
                            <!-- Office Staff - Manila -->
                            <div class="px-4 py-1 ml-2">
                                <p class="text-xs font-medium text-gray-500 mb-1">• Office Staff - Manila</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('crew.export.pdf', ['division' => 'office_staff', 'department' => 'manila']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                    <a href="{{ route('crew.export.excel', ['division' => 'office_staff', 'department' => 'manila']) }}" 
                                       class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                </div>
                            </div>
                            
                            <!-- Office Staff - Batanes -->
                            <div class="px-4 py-1 ml-2">
                                <p class="text-xs font-medium text-gray-500 mb-1">• Office Staff - Batanes</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('crew.export.pdf', ['division' => 'office_staff', 'department' => 'batanes']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                    <a href="{{ route('crew.export.excel', ['division' => 'office_staff', 'department' => 'batanes']) }}" 
                                       class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                </div>
                            </div>
                            
                            <!-- Operations - Manila -->
                            <div class="px-4 py-1 ml-2">
                                <p class="text-xs font-medium text-gray-500 mb-1">• Operations - Manila</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('crew.export.pdf', ['division' => 'operations', 'department' => 'manila']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                    <a href="{{ route('crew.export.excel', ['division' => 'operations', 'department' => 'manila']) }}" 
                                       class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                </div>
                            </div>
                            
                            <!-- Operations - Batanes -->
                            <div class="px-4 py-1 ml-2">
                                <p class="text-xs font-medium text-gray-500 mb-1">• Operations - Batanes</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('crew.export.pdf', ['division' => 'operations', 'department' => 'batanes']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                    <a href="{{ route('crew.export.excel', ['division' => 'operations', 'department' => 'batanes']) }}" 
                                       class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                </div>
                            </div>
                            
                            <!-- Apprentice - Manila -->
                            <div class="px-4 py-1 ml-2">
                                <p class="text-xs font-medium text-gray-500 mb-1">• Apprentice - Manila</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('crew.export.pdf', ['division' => 'apprentice', 'department' => 'manila']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                    <a href="{{ route('crew.export.excel', ['division' => 'apprentice', 'department' => 'manila']) }}" 
                                       class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                </div>
                            </div>
                            
                            <!-- Apprentice - Batanes -->
                            <div class="px-4 py-1 ml-2">
                                <p class="text-xs font-medium text-gray-500 mb-1">• Apprentice - Batanes</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('crew.export.pdf', ['division' => 'apprentice', 'department' => 'batanes']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">PDF</a>
                                    <a href="{{ route('crew.export.excel', ['division' => 'apprentice', 'department' => 'batanes']) }}" 
                                       class="text-xs text-green-600 hover:text-green-800">Excel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(Auth::user()->hasPagePermission('leave-applications'))
                <a href="{{ route('leave-credits.index') }}" 
                   class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Manage Leave Credits
                </a>
                @endif
                
                @if(auth()->user()->hasPermission('crew', 'create') || auth()->user()->hasSubpagePermission('crew', 'crew-management', 'create'))
                <a href="{{ route('crew.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Crew Member
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                    <form method="GET" action="{{ route('crew.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input type="text" name="search" id="search" value="{{ $search }}" 
                                   placeholder="Name, Employee ID, Position, Department, Ship, Status..."
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Search by: Name, Employee ID, Position, Department (ship_crew, office_staff, laborer, apprentice), Ship (I, II, III, IV, V), Status (active, inactive, terminated, resigned)
                            </p>
                            <!-- Quick Search Buttons -->
                            <div class="mt-2 flex flex-wrap gap-1">
                                <button type="button" onclick="quickSearch(event, 'active')" 
                                        class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded hover:bg-green-200 dark:hover:bg-green-800">
                                    Active Only
                                </button>
                                <button type="button" onclick="quickSearch(event, 'ship_crew')" 
                                        class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-800">
                                    Ship Crew
                                </button>
                                <button type="button" onclick="quickSearch(event, 'office_staff')" 
                                        class="px-2 py-1 text-xs bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded hover:bg-purple-200 dark:hover:bg-purple-800">
                                    Office Staff
                                </button>
                                <button type="button" onclick="quickSearch(event, 'apprentice')" 
                                        class="px-2 py-1 text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 rounded hover:bg-yellow-200 dark:hover:bg-yellow-800">
                                    Apprentice
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label for="ship" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ship</label>
                            <select name="ship" id="ship" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Ships</option>
                                @foreach($ships as $ship)
                                    <option value="{{ $ship->id }}" {{ $selectedShip == $ship->id ? 'selected' : '' }}>
                                        MV EVERWIN STAR {{ $ship->ship_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                            <select name="department" id="department" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Departments</option>
                                <option value="ship_crew" {{ $selectedDepartment == 'ship_crew' ? 'selected' : '' }}>Ship Crew</option>
                                <option value="office_staff" {{ $selectedDepartment == 'office_staff' ? 'selected' : '' }}>Office Staff</option>
                                <option value="laborer" {{ $selectedDepartment == 'laborer' ? 'selected' : '' }}>Laborer</option>
                                <option value="apprentice" {{ $selectedDepartment == 'apprentice' ? 'selected' : '' }}>Apprentice</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Filter
                            </button>
                            <a href="{{ route('crew.index') }}" 
                               class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Crew List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Employee ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Position
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Department
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Ship Assignment
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Leave Credits
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                @forelse($crews as $crew)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $crew->employee_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $crew->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $crew->position }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ ucfirst(str_replace('_', ' ', $crew->department)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $crew->employment_status == 'active' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 
                                                       ($crew->employment_status == 'inactive' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200') }}">
                                                    {{ ucfirst($crew->employment_status) }}
                                                </span>
                                                <button onclick="showStatusChangeModal({{ $crew->id }}, '{{ $crew->employment_status }}', '{{ $crew->full_name }}')" 
                                                        class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 ml-1" title="Change Status">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $crew->available_leave_credits }} days
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('crew.show', $crew) }}" 
                                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">View</a>
                                                @if(auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'crew-management', 'edit'))
                                                <a href="{{ route('crew.edit', $crew) }}" 
                                                   class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">Edit</a>
                                                @endif
                                                @if(auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'crew-management', 'edit'))
                                                <button onclick="showTransferModal({{ $crew->id }}, '{{ $crew->full_name }}', '{{ $crew->ship ? 'MV EVERWIN STAR ' . $crew->ship->ship_number : 'Office/Shore' }}', {{ $crew->ship_id ?: 'null' }})" 
                                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">Transfer</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No crew members found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $crews->appends(request()->query())->links() }}
                    </div>
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

    <script>
        function toggleExportDropdown() {
            const dropdown = document.getElementById('exportDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('exportDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.onclick || button.onclick.toString().indexOf('toggleExportDropdown') === -1) {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });

        // Enhanced search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const searchExamples = [
                'John Doe',
                'EMP001', 
                'Captain',
                'ship_crew',
                'apprentice',
                'office_staff', 
                'laborer',
                'EVERWIN STAR I',
                'II',
                'active',
                'inactive'
            ];

            searchInput.addEventListener('focus', function() {
                this.setAttribute('data-original-placeholder', this.placeholder);
                let exampleIndex = 0;
                const rotateExamples = () => {
                    this.placeholder = `Try: "${searchExamples[exampleIndex]}"`;
                    exampleIndex = (exampleIndex + 1) % searchExamples.length;
                };
                rotateExamples();
                this.exampleInterval = setInterval(rotateExamples, 2000);
            });

            searchInput.addEventListener('blur', function() {
                if (this.exampleInterval) {
                    clearInterval(this.exampleInterval);
                }
                this.placeholder = this.getAttribute('data-original-placeholder') || 'Name, Employee ID, Position, Department, Ship, Status...';
            });
        });
    </script>

    <!-- Transfer Modal -->
    <div id="transferModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-2">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-md font-medium text-gray-900 dark:text-white">Transfer Crew Member</h3>
                    <button onclick="hideTransferModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p id="transferCrewInfo" class="text-xs text-gray-600 dark:text-gray-400 mb-3"></p>
                <form id="transferForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="transfer_ship_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Transfer to Ship</label>
                        <select name="ship_id" id="transfer_ship_id" 
                                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Office/Shore Assignment</option>
                            @foreach($ships as $ship)
                                <option value="{{ $ship->id }}">MV EVERWIN STAR {{ $ship->ship_number }}</option>
                            @endforeach
                        </select>
                        <div id="transfer_ship_error" class="text-red-600 dark:text-red-400 text-xs mt-1 hidden"></div>
                    </div>
                    <div class="mb-3">
                        <label for="transfer_notes" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Transfer Notes</label>
                        <textarea name="notes" id="transfer_notes" rows="2" maxlength="500"
                                  placeholder="Optional notes..."
                                  class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span id="notesCharCount">0</span>/500 characters
                        </div>
                        <div id="transfer_notes_error" class="text-red-600 dark:text-red-400 text-xs mt-1 hidden"></div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideTransferModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white text-sm font-medium py-1.5 px-3 rounded">
                            Cancel
                        </button>
                        <button type="submit" id="transferSubmitBtn"
                                class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-medium py-1.5 px-3 rounded">
                            Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function quickSearch(event, term) {
            // Prevent the button from submitting the form
            event.preventDefault();
            event.stopPropagation();
            
            // Build the URL with query parameters instead of form submission
            const baseUrl = "{{ route('crew.index') }}";
            const searchParams = new URLSearchParams();
            searchParams.set('search', term);
            
            // Preserve existing filters if they exist
            const shipSelect = document.getElementById('ship');
            const departmentSelect = document.getElementById('department');
            
            if (shipSelect && shipSelect.value) {
                searchParams.set('ship', shipSelect.value);
            }
            
            if (departmentSelect && departmentSelect.value) {
                searchParams.set('department', departmentSelect.value);
            }
            
            // Navigate to the new URL
            window.location.href = baseUrl + '?' + searchParams.toString();
        }

        function showTransferModal(crewId, crewName, currentAssignment, currentShipId) {
            // Set form action
            document.getElementById('transferForm').action = `/crew/${crewId}/transfer`;
            
            // Update crew info display
            document.getElementById('transferCrewInfo').textContent = 
                `Transferring: ${crewName} (Currently: ${currentAssignment})`;
            
            // Reset form
            document.getElementById('transfer_ship_id').value = '';
            document.getElementById('transfer_notes').value = '';
            
            // Hide any previous errors
            document.getElementById('transfer_ship_error').classList.add('hidden');
            document.getElementById('transfer_notes_error').classList.add('hidden');
            
            // If crew is currently assigned to a ship, don't pre-select it
            if (currentShipId) {
                // Optionally disable the current ship option
                const options = document.getElementById('transfer_ship_id').options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value == currentShipId) {
                        options[i].style.color = '#9CA3AF'; // Gray out current assignment
                        options[i].text = options[i].text + ' (Current)';
                    } else {
                        options[i].style.color = '';
                        options[i].text = options[i].text.replace(' (Current)', '');
                    }
                }
            }
            
            // Show modal
            document.getElementById('transferModal').classList.remove('hidden');
        }

        function hideTransferModal() {
            // Reset form and options
            const shipSelect = document.getElementById('transfer_ship_id');
            const options = shipSelect.options;
            for (let i = 0; i < options.length; i++) {
                options[i].style.color = '';
                options[i].text = options[i].text.replace(' (Current)', '');
            }
            
            document.getElementById('transferModal').classList.add('hidden');
        }

        // Enhanced form submission for transfer
        document.addEventListener('DOMContentLoaded', function() {
            const transferForm = document.getElementById('transferForm');
            const submitBtn = document.getElementById('transferSubmitBtn');
            const notesTextarea = document.getElementById('transfer_notes');
            const charCount = document.getElementById('notesCharCount');
            
            // Character counter for notes
            if (notesTextarea && charCount) {
                notesTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
            
            // Keyboard navigation for modal
            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('transferModal');
                if (!modal.classList.contains('hidden')) {
                    if (e.key === 'Escape') {
                        hideTransferModal();
                    } else if (e.key === 'Enter' && e.ctrlKey) {
                        e.preventDefault();
                        transferForm.submit();
                    }
                }
            });
            
            // Click outside to close modal
            const transferModal = document.getElementById('transferModal');
            transferModal.addEventListener('click', function(e) {
                if (e.target === transferModal) {
                    hideTransferModal();
                }
            });
            
            if (transferForm) {
                transferForm.addEventListener('submit', function(e) {
                    // Clear previous errors
                    document.getElementById('transfer_ship_error').classList.add('hidden');
                    document.getElementById('transfer_notes_error').classList.add('hidden');
                    
                    // Basic validation
                    let hasError = false;
                    
                    // You can add custom validation here if needed
                    
                    if (hasError) {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Transferring...';
                    
                    // Form will submit normally
                });
            }
        });

        function showStatusChangeModal(crewId, currentStatus, crewName) {
            document.getElementById('statusChangeForm').action = `/crew/${crewId}/status`;
            document.getElementById('statusChangeCrewName').textContent = crewName;
            document.getElementById('statusChangeSelect').value = currentStatus;
            document.getElementById('statusChangeModal').classList.remove('hidden');
        }

        function hideStatusChangeModal() {
            document.getElementById('statusChangeModal').classList.add('hidden');
        }
    </script>

    <!-- Status Change Modal -->
    <div id="statusChangeModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Change Employment Status</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Change status for: <span id="statusChangeCrewName" class="font-semibold text-gray-900 dark:text-white"></span>
                </p>
                <form id="statusChangeForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="statusChangeSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employment Status</label>
                        <select name="employment_status" id="statusChangeSelect" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                            <option value="resigned">Resigned</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="statusChangeReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason (Optional)</label>
                        <textarea name="status_change_reason" id="statusChangeReason" rows="3" 
                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                  placeholder="Enter reason for status change..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideStatusChangeModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
@endif
