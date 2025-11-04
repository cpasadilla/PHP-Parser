@php
    $status = $status ?? '';
    $search = $search ?? '';
@endphp

@if(!auth()->user()->hasPermission('crew', 'access') && !auth()->user()->hasSubpagePermission('crew', 'leave-applications', 'access'))
    <x-app-layout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                        <h3 class="text-lg font-semibold mb-2">Access Denied</h3>
                        <p>You don't have permission to access Leave Applications.</p>
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
                {{ __('Leave Applications') }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->hasPermission('crew', 'create') || auth()->user()->hasSubpagePermission('crew', 'leave-applications', 'create'))
                <a href="{{ route('leave-applications.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    New Application
                </a>
                @endif
                @if(auth()->user()->hasPermission('crew', 'create') || auth()->user()->hasSubpagePermission('crew', 'upload-sick-leave', 'create'))
                <a href="{{ route('leave-applications.upload-sick-leave') }}" 
                   class="bg-green-500 hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Upload Sick Leave
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('leave-applications.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input type="text" name="search" id="search" value="{{ $search }}" 
                                   placeholder="Crew name, employee ID..."
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">All Status</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="hr_review" {{ $status == 'hr_review' ? 'selected' : '' }}>HR Review</option>
                                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="disapproved" {{ $status == 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                                <option value="deferred" {{ $status == 'deferred' ? 'selected' : '' }}>Deferred</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2 flex items-center justify-center">
                                Filter
                            </button>
                            <a href="{{ route('leave-applications.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded flex items-center justify-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Applications List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Crew Member
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Leave Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date Range
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Days
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Applied Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($applications as $application)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            @if($application->crew)
                                                {{ $application->crew->full_name }}
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $application->crew->employee_id }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Available: {{ $application->crew->available_leave_credits }} days
                                                </div>
                                            @else
                                                <span class="text-red-600 dark:text-red-400">Crew Record Not Found</span>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $application->crew_id }}</div>
                                                <div class="text-xs text-red-500 dark:text-red-400">
                                                    Data integrity issue
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $application->leave_type_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $application->start_date->format('M d, Y') }} - 
                                            {{ $application->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $application->days_requested }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $application->status == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                   ($application->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                   ($application->status == 'hr_review' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                                   ($application->status == 'disapproved' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                   ($application->status == 'deferred' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')))) }}">
                                                {{ $application->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $application->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('leave-applications.show', $application) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                                                @if($application->file_path)
                                                <a href="{{ route('leave-applications.download', $application) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Download</a>
                                                @endif
                                                @if($application->status == 'pending' && (auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'leave-applications', 'edit')))
                                                <button onclick="showApprovalModal({{ $application->id }})" 
                                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">Approve</button>
                                                <button onclick="showRejectionModal({{ $application->id }})" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Reject</button>
                                                @endif
                                                @if($application->status == 'pending' && (auth()->user()->hasPermission('crew', 'edit') || auth()->user()->hasSubpagePermission('crew', 'leave-applications', 'edit')))
                                                <a href="{{ route('leave-applications.edit', $application) }}" 
                                                   class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">Edit</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No leave applications found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $applications->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Approve Leave Application</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to approve this leave application?</p>
                <form id="approvalForm" method="POST">
                    @csrf
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideApprovalModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative w-full">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Reject Leave Application</h3>
                <form id="rejectionForm" method="POST">
                    @csrf
                    <div class="mb-4 text-left">
                        <label for="disapproved_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Disapproval *</label>
                        <textarea name="disapproved_reason" id="disapproved_reason" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-red-500 dark:focus:border-red-400 focus:ring-red-500 dark:focus:ring-red-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideRejectionModal()" 
                                class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showApprovalModal(applicationId) {
            document.getElementById('approvalForm').action = `/leave-applications/${applicationId}/approve`;
            document.getElementById('approvalModal').classList.remove('hidden');
        }

        function hideApprovalModal() {
            document.getElementById('approvalModal').classList.add('hidden');
        }

        function showRejectionModal(applicationId) {
            document.getElementById('rejectionForm').action = `/leave-applications/${applicationId}/reject`;
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function hideRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
@endif
