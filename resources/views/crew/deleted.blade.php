@php
    $deletedCrew = $deletedCrew ?? [];
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
                {{ __('Deleted Crew Members') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('crew.index') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Crew List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                    
                    @if($deletedCrew->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No deleted crew members</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No crew members have been deleted.</p>
                        </div>
                    @else
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
                                    @foreach($deletedCrew as $crew)
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
                                                {{ $crew->ship_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $crew->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $crew->deleted_by }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @if(auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'crew-management', 'edit'))
                                                    <button onclick="showRestoreModal({{ $crew->id }}, '{{ $crew->full_name }}', '{{ $crew->employee_id }}')" 
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
                            {{ $deletedCrew->links() }}
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Restore Crew Member</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Are you sure you want to restore <span id="restoreCrewName" class="font-semibold text-gray-900 dark:text-white"></span> 
                    (Employee ID: <span id="restoreEmployeeId" class="font-semibold text-gray-900 dark:text-white"></span>)?
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    This will create a new crew record with all associated documents and leave credits.
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
                            Restore Crew Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showRestoreModal(deleteLogId, crewName, employeeId) {
            document.getElementById('restoreForm').action = `/crew/restore/${deleteLogId}`;
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
