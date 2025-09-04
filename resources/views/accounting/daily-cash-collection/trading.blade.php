<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Daily Cash Collection Report - Trading') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">
                        Daily Cash Collection Report - Trading
                        @if($selectedDate)
                            <span class="text-lg font-normal text-blue-600 dark:text-blue-400">
                                ({{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }})
                            </span>
                        @endif
                    </h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Date Range</h3>
                            <div class="mt-2">
                                <input type="date" id="filterDate" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700" value="{{ $selectedDate ?? '' }}" onchange="filterByDate()" />
                                @if($selectedDate)
                                <button onclick="clearDateFilter()" class="mt-2 px-3 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Clear Filter
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Total Collections</h3>
                            <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($entries->sum('total'), 2) }}</div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Total Entries</h3>
                            <div class="mt-2 text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $entries->count() }}</div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-semibold">Trading Entries</h3>
                            <button onclick="openAddEntryModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Add Entry
                            </button>
                        </div>
                        
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">AR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">OR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gravel & Sand</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">CHB</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Other Income (Cement)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Other Income (DF)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Others</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Interest</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remark</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($entries as $entry)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->entry_date->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->ar }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->or }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->gravel_sand > 0 ? '₱' . number_format($entry->gravel_sand, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->chb > 0 ? '₱' . number_format($entry->chb, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->other_income_cement > 0 ? '₱' . number_format($entry->other_income_cement, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->other_income_df > 0 ? '₱' . number_format($entry->other_income_df, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->others > 0 ? '₱' . number_format($entry->others, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->interest > 0 ? '₱' . number_format($entry->interest, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($entry->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ Str::limit($entry->remark, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openEditModal({{ $entry->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No trading entries found. Click "Add Entry" to create your first entry.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <button onclick="generateReport()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Generate Report
                        </button>
                        <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Export to Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Entry Modal -->
    <div id="addEntryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border w-11/12 max-w-5xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Add New Trading Entry</h3>
                    <button type="button" onclick="closeAddEntryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="w-full h-px bg-gray-300 dark:bg-gray-600 mt-3"></div>
            </div>
            
            <form id="addEntryForm">
                @csrf
                <input type="hidden" name="type" value="trading">
                
                <!-- Basic Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Basic Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                            <input type="date" name="entry_date" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DCCR No.</label>
                            <input type="text" name="dccr_number" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">AR</label>
                            <input type="text" name="ar" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OR</label>
                            <input type="text" name="or" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" id="customer_name" required autocomplete="off" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter customer name...">
                        <datalist id="customerList"></datalist>
                        <input type="hidden" name="customer_id" id="customer_id">
                    </div>
                </div>
                
                <!-- Financial Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Financial Details
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gravel & Sand (₱)</label>
                            <input type="number" name="gravel_sand" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CHB (₱)</label>
                            <input type="number" name="chb" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Other Income (Cement) (₱)</label>
                            <input type="number" name="other_income_cement" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Other Income (DF) (₱)</label>
                            <input type="number" name="other_income_df" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Others (₱)</label>
                            <input type="number" name="others" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Interest (₱)</label>
                            <input type="number" name="interest" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateTotal()">
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Amount (₱)</label>
                        <input type="number" id="totalAmount" step="0.01" readonly class="w-full px-3 py-2.5 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-lg font-semibold text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <!-- Additional Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        Additional Information
                    </h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remark</label>
                        <textarea name="remark" rows="3" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter any additional remarks or notes..."></textarea>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" onclick="closeAddEntryModal()" class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Entry Modal -->
    <div id="editEntryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border w-11/12 max-w-5xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Edit Trading Entry</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="w-full h-px bg-gray-300 dark:bg-gray-600 mt-3"></div>
            </div>
            
            <form id="editEntryForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="entry_id" id="edit_entry_id">
                
                <!-- Basic Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Basic Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                            <input type="date" name="entry_date" id="edit_entry_date" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DCCR No.</label>
                            <input type="text" name="dccr_number" id="edit_dccr_number" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">AR</label>
                            <input type="text" name="ar" id="edit_ar" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OR</label>
                            <input type="text" name="or" id="edit_or" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" id="edit_customer_name" required autocomplete="off" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter customer name...">
                        <datalist id="editCustomerList"></datalist>
                        <input type="hidden" name="customer_id" id="edit_customer_id">
                    </div>
                </div>
                
                <!-- Financial Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Financial Details
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gravel & Sand (₱)</label>
                            <input type="number" name="gravel_sand" id="edit_gravel_sand" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CHB (₱)</label>
                            <input type="number" name="chb" id="edit_chb" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Other Income (Cement) (₱)</label>
                            <input type="number" name="other_income_cement" id="edit_other_income_cement" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Other Income (DF) (₱)</label>
                            <input type="number" name="other_income_df" id="edit_other_income_df" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Others (₱)</label>
                            <input type="number" name="others" id="edit_others" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Interest (₱)</label>
                            <input type="number" name="interest" id="edit_interest" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Amount (₱)</label>
                        <input type="number" id="editTotalAmount" step="0.01" readonly class="w-full px-3 py-2.5 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-lg font-semibold text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <!-- Additional Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        Additional Information
                    </h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remark</label>
                        <textarea name="remark" id="edit_remark" rows="3" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter any additional remarks or notes..."></textarea>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" onclick="closeEditModal()" class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Update Entry
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openAddEntryModal() {
            document.getElementById('addEntryModal').classList.remove('hidden');
            // Set today's date as default
            document.querySelector('input[name="entry_date"]').value = new Date().toISOString().split('T')[0];
        }

        function closeAddEntryModal() {
            document.getElementById('addEntryModal').classList.add('hidden');
            document.getElementById('addEntryForm').reset();
            document.getElementById('totalAmount').value = '';
        }

        function openEditModal(entryId) {
            // Fetch entry data and populate the edit modal
            fetch(`{{ route("accounting.daily-cash-collection.get", ":id") }}`.replace(':id', entryId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const entry = data.entry;
                    document.getElementById('edit_entry_id').value = entry.id;
                    document.getElementById('edit_entry_date').value = entry.entry_date;
                    document.getElementById('edit_dccr_number').value = entry.dccr_number || '';
                    document.getElementById('edit_ar').value = entry.ar || '';
                    document.getElementById('edit_or').value = entry.or || '';
                    document.getElementById('edit_customer_name').value = entry.customer_name;
                    document.getElementById('edit_customer_id').value = entry.customer_id || '';
                    document.getElementById('edit_gravel_sand').value = entry.gravel_sand;
                    document.getElementById('edit_chb').value = entry.chb;
                    document.getElementById('edit_other_income_cement').value = entry.other_income_cement;
                    document.getElementById('edit_other_income_df').value = entry.other_income_df;
                    document.getElementById('edit_others').value = entry.others;
                    document.getElementById('edit_interest').value = entry.interest;
                    document.getElementById('edit_remark').value = entry.remark || '';
                    
                    calculateEditTotal();
                    document.getElementById('editEntryModal').classList.remove('hidden');
                } else {
                    alert('Error loading entry data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading entry data');
            });
        }

        function closeEditModal() {
            document.getElementById('editEntryModal').classList.add('hidden');
        }

        // Customer search functionality
        function setupCustomerSearch(inputId, datalistId, customerIdField) {
            const input = document.getElementById(inputId);
            const datalist = document.getElementById(datalistId);
            const idField = document.getElementById(customerIdField);
            
            input.addEventListener('input', function() {
                const query = this.value;
                if (query.length < 2) return;
                
                fetch(`{{ route("accounting.search-customers") }}?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    datalist.innerHTML = '';
                    data.forEach(customer => {
                        const option = document.createElement('option');
                        option.value = customer.name;
                        option.dataset.id = customer.id;
                        datalist.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching customers:', error));
            });
            
            input.addEventListener('change', function() {
                const selectedOption = [...datalist.options].find(option => option.value === input.value);
                idField.value = selectedOption ? selectedOption.dataset.id : '';
            });
        }

        // Total calculation
        function calculateTotal() {
            const form = document.getElementById('addEntryForm');
            const gravelSand = parseFloat(form.gravel_sand.value) || 0;
            const chb = parseFloat(form.chb.value) || 0;
            const cement = parseFloat(form.other_income_cement.value) || 0;
            const df = parseFloat(form.other_income_df.value) || 0;
            const others = parseFloat(form.others.value) || 0;
            const interest = parseFloat(form.interest.value) || 0;
            
            const total = gravelSand + chb + cement + df + others + interest;
            document.getElementById('totalAmount').value = total.toFixed(2);
        }

        function calculateEditTotal() {
            const form = document.getElementById('editEntryForm');
            const gravelSand = parseFloat(form.gravel_sand.value) || 0;
            const chb = parseFloat(form.chb.value) || 0;
            const cement = parseFloat(form.other_income_cement.value) || 0;
            const df = parseFloat(form.other_income_df.value) || 0;
            const others = parseFloat(form.others.value) || 0;
            const interest = parseFloat(form.interest.value) || 0;
            
            const total = gravelSand + chb + cement + df + others + interest;
            document.getElementById('editTotalAmount').value = total.toFixed(2);
        }

        // Form submissions
        document.getElementById('addEntryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("accounting.daily-cash-collection.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Entry created successfully!');
                    closeAddEntryModal();
                    location.reload();
                } else {
                    alert('Error creating entry: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating entry');
            });
        });

        document.getElementById('editEntryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const entryId = document.getElementById('edit_entry_id').value;
            const formData = new FormData(this);
            formData.append('_method', 'PUT');
            
            fetch(`{{ route("accounting.daily-cash-collection.update", ":id") }}`.replace(':id', entryId), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Entry updated successfully!');
                    closeEditModal();
                    location.reload();
                } else {
                    alert('Error updating entry: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating entry');
            });
        });

        // Initialize customer search when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setupCustomerSearch('customer_name', 'customerList', 'customer_id');
            setupCustomerSearch('edit_customer_name', 'editCustomerList', 'edit_customer_id');
        });

        // Date filtering function
        function filterByDate() {
            const selectedDate = document.getElementById('filterDate').value;
            if (selectedDate) {
                // Redirect to current page with date filter
                const url = new URL(window.location.href);
                url.searchParams.set('date', selectedDate);
                window.location.href = url.toString();
            } else {
                // Remove date filter if no date selected
                const url = new URL(window.location.href);
                url.searchParams.delete('date');
                window.location.href = url.toString();
            }
        }

        // Generate report function
        function generateReport() {
            const selectedDate = document.getElementById('filterDate').value;
            let reportUrl = '{{ route('accounting.daily-cash-collection.trading-print') }}';
            
            if (selectedDate) {
                reportUrl += '?date=' + selectedDate;
            }
            
            window.open(reportUrl, '_blank');
        }

        // Clear date filter function
        function clearDateFilter() {
            const url = new URL(window.location.href);
            url.searchParams.delete('date');
            window.location.href = url.toString();
        }
    </script>
</x-app-layout>
