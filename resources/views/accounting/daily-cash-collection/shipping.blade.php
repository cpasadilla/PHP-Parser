<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Daily Cash Collection Report - Shipping') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">
                        Daily Cash Collection Report - Shipping
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
                            <h3 class="text-lg font-semibold">Shipping Entries</h3>
                            <button onclick="openAddShippingEntryModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Add Entry
                            </button>
                        </div>
                        
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <!--th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th-->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th colspan="5" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">FREIGHT </th>
                                <th colspan="5" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">OTHER INCOME</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                            </tr>
                               <tr>
                                <!--th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th-->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">AR</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">OR</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 2</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 3</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 4</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 5</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 2</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 3</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 4</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 5</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">WHARFAGE PAYABLES</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">INTEREST</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">TOTAL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remark</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($entries as $entry)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->ar ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->or ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->customer_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_1 > 0 ? '₱' . number_format($entry->mv_everwin_star_1, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_2 > 0 ? '₱' . number_format($entry->mv_everwin_star_2, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_3 > 0 ? '₱' . number_format($entry->mv_everwin_star_3, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_4 > 0 ? '₱' . number_format($entry->mv_everwin_star_4, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_5 > 0 ? '₱' . number_format($entry->mv_everwin_star_5, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_1_other > 0 ? '₱' . number_format($entry->mv_everwin_star_1_other, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_2_other > 0 ? '₱' . number_format($entry->mv_everwin_star_2_other, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_3_other > 0 ? '₱' . number_format($entry->mv_everwin_star_3_other, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_4_other > 0 ? '₱' . number_format($entry->mv_everwin_star_4_other, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->mv_everwin_star_5_other > 0 ? '₱' . number_format($entry->mv_everwin_star_5_other, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->wharfage_payables > 0 ? '₱' . number_format($entry->wharfage_payables, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ $entry->interest > 0 ? '₱' . number_format($entry->interest, 2) : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right font-semibold">
                                        ₱{{ number_format($entry->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->remark ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openEditShippingModal({{ $entry->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="18" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No shipping entries found. Click "Add Entry" to create your first entry.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <button onclick="openPrintModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
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

    <!-- Add Shipping Entry Modal -->
    <div id="addShippingEntryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border w-11/12 max-w-6xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Add New Shipping Entry</h3>
                    <button type="button" onclick="closeAddShippingEntryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="w-full h-px bg-gray-300 dark:bg-gray-600 mt-3"></div>
            </div>
            
            <div id="multipleEntriesContainer">
                <!-- Entry Counter Display -->
                <div class="mb-4 flex justify-between items-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span id="entryCounter">Entry 1 of 1</span>
                    </div>
                    <button type="button" onclick="addAnotherShippingEntry()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Another Entry
                    </button>
                </div>

                <form id="addShippingEntryForm">
                    @csrf
                    <input type="hidden" name="type" value="shipping">
                    
                    <div class="entry-form" data-entry-index="0">
                        <div class="entry-header flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Entry #1</h4>
                            <button type="button" onclick="removeShippingEntry(0)" class="remove-entry-btn px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Remove
                            </button>
                        </div>
                
                        <!-- Basic Information Section -->
                        <div class="mb-6">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Basic Information
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                                    <input type="date" name="entries[0][entry_date]" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">AR</label>
                                    <input type="text" name="entries[0][ar]" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter AR number...">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OR</label>
                                    <input type="text" name="entries[0][or]" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter OR number...">
                                </div>
                            </div>
                            
                            <div class="mt-4 relative">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name *</label>
                                <input type="text" name="entries[0][customer_name]" class="customer-name-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required autocomplete="off" placeholder="Start typing customer name...">
                                <div class="customer-dropdown absolute z-50 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg mt-1 hidden max-h-48 overflow-y-auto">
                                    <!-- Customer suggestions will appear here -->
                                </div>
                                <input type="hidden" name="entries[0][customer_id]" class="customer-id-input">
                            </div>
                        </div>
                
                        <!-- Financial Information Section -->
                        <div class="mb-6">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Financial Details
                            </h5>
                            
                            <!-- Freight Charges Row -->
                            <div class="mb-4">
                                <h6 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Freight Charges (₱)</h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 1</label>
                                        <input type="number" name="entries[0][mv_everwin_star_1]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 2</label>
                                        <input type="number" name="entries[0][mv_everwin_star_2]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 3</label>
                                        <input type="number" name="entries[0][mv_everwin_star_3]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 4</label>
                                        <input type="number" name="entries[0][mv_everwin_star_4]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 5</label>
                                        <input type="number" name="entries[0][mv_everwin_star_5]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Other Income Row -->
                            <div class="mb-4">
                                <h6 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Other Income (₱)</h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 1</label>
                                        <input type="number" name="entries[0][mv_everwin_star_1_other]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 2</label>
                                        <input type="number" name="entries[0][mv_everwin_star_2_other]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 3</label>
                                        <input type="number" name="entries[0][mv_everwin_star_3_other]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 4</label>
                                        <input type="number" name="entries[0][mv_everwin_star_4_other]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 5</label>
                                        <input type="number" name="entries[0][mv_everwin_star_5_other]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Charges Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wharfage Payables (₱)</label>
                                    <input type="number" name="entries[0][wharfage_payables]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Interest (₱)</label>
                                    <input type="number" name="entries[0][interest]" step="0.01" min="0" placeholder="0.00" class="financial-input w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEntryTotal(0)">
                                </div>
                            </div>
                            
                            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Amount (₱)</label>
                                <input type="number" class="entry-total w-full px-3 py-2.5 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-lg font-semibold text-gray-900 dark:text-white" name="entries[0][total]" step="0.01" readonly>
                            </div>
                        </div>
                        
                        <!-- Additional Information Section -->
                        <div class="mb-6">
                            <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                Additional Information
                            </h5>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remark</label>
                                <textarea name="entries[0][remark]" rows="3" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter any additional remarks or notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional entry forms will be added here -->
                </form>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button" onclick="closeAddShippingEntryModal()" class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitAllShippingEntries()" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    <span id="submitBtnText">Save Entry</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Shipping Entry Modal -->
    <div id="editShippingEntryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border w-11/12 max-w-5xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Edit Shipping Entry</h3>
                    <button type="button" onclick="closeEditShippingModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="w-full h-px bg-gray-300 dark:bg-gray-600 mt-3"></div>
            </div>
            
            <form id="editShippingEntryForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="entry_id" id="edit_shipping_entry_id">
                
                <!-- Basic Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Basic Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">AR</label>
                            <input type="text" name="ar" id="edit_shipping_ar" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter AR number...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">OR</label>
                            <input type="text" name="or" id="edit_shipping_or" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter OR number...">
                        </div>
                    </div>
                    
                    <div class="mt-4 relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" id="edit_shipping_customer_name" required autocomplete="off" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Start typing customer name...">
                        <div id="editShippingCustomerDropdown" class="absolute z-50 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg mt-1 hidden max-h-48 overflow-y-auto">
                            <!-- Customer suggestions will appear here -->
                        </div>
                        <input type="hidden" name="customer_id" id="edit_shipping_customer_id">
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
                    
                    <!-- Freight Charges Row -->
                    <div class="mb-4">
                        <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Freight Charges (₱)</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 1</label>
                                <input type="number" name="mv_everwin_star_1" id="edit_shipping_mv_everwin_star_1" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 2</label>
                                <input type="number" name="mv_everwin_star_2" id="edit_shipping_mv_everwin_star_2" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 3</label>
                                <input type="number" name="mv_everwin_star_3" id="edit_shipping_mv_everwin_star_3" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 4</label>
                                <input type="number" name="mv_everwin_star_4" id="edit_shipping_mv_everwin_star_4" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 5</label>
                                <input type="number" name="mv_everwin_star_5" id="edit_shipping_mv_everwin_star_5" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Other Income Row -->
                    <div class="mb-4">
                        <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Other Income (₱)</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 1</label>
                                <input type="number" name="mv_everwin_star_1_other" id="edit_shipping_mv_everwin_star_1_other" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 2</label>
                                <input type="number" name="mv_everwin_star_2_other" id="edit_shipping_mv_everwin_star_2_other" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 3</label>
                                <input type="number" name="mv_everwin_star_3_other" id="edit_shipping_mv_everwin_star_3_other" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 4</label>
                                <input type="number" name="mv_everwin_star_4_other" id="edit_shipping_mv_everwin_star_4_other" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">MV EVERWIN STAR 5</label>
                                <input type="number" name="mv_everwin_star_5_other" id="edit_shipping_mv_everwin_star_5_other" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Charges Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wharfage Payables (₱)</label>
                            <input type="number" name="wharfage_payables" id="edit_shipping_wharfage_payables" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Interest (₱)</label>
                            <input type="number" name="interest" id="edit_shipping_interest" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="calculateEditTotal()">
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Amount (₱)</label>
                        <input type="number" id="edit_shipping_total" name="total" step="0.01" readonly class="w-full px-3 py-2.5 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-lg font-semibold text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <!-- Additional Information Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Additional Information
                    </h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remark</label>
                        <textarea name="remark" id="edit_shipping_remark" rows="3" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Enter any additional remarks or notes..."></textarea>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" onclick="closeEditShippingModal()" class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
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
        // Modal functions for shipping
        let shippingEntryCount = 1;
        
        function openAddShippingEntryModal() {
            document.getElementById('addShippingEntryModal').classList.remove('hidden');
            // Set today's date as default for the first entry
            const firstDateInput = document.querySelector('input[name="entries[0][entry_date]"]');
            if (firstDateInput) {
                firstDateInput.value = new Date().toISOString().split('T')[0];
            }
            // Reset form
            resetShippingForm();
            updateShippingEntryCounter();
        }

        function closeAddShippingEntryModal() {
            document.getElementById('addShippingEntryModal').classList.add('hidden');
            // Reset to single entry
            resetShippingForm();
        }
        
        function resetShippingForm() {
            // Remove all additional entries
            const container = document.getElementById('multipleEntriesContainer');
            const form = document.getElementById('addShippingEntryForm');
            const additionalEntries = form.querySelectorAll('.entry-form:not([data-entry-index="0"])');
            additionalEntries.forEach(entry => entry.remove());
            
            // Reset first entry
            const firstEntry = form.querySelector('.entry-form[data-entry-index="0"]');
            if (firstEntry) {
                firstEntry.querySelectorAll('input, textarea').forEach(input => {
                    if (input.type !== 'hidden') {
                        input.value = '';
                    }
                });
                firstEntry.querySelector('.remove-entry-btn').classList.add('hidden');
            }
            
            shippingEntryCount = 1;
            updateShippingEntryCounter();
            updateShippingSubmitButton();
        }
        
        function addAnotherShippingEntry() {
            const form = document.getElementById('addShippingEntryForm');
            const entryIndex = shippingEntryCount;
            
            // Get the first entry as a template
            const firstEntry = form.querySelector('.entry-form[data-entry-index="0"]');
            const newEntry = firstEntry.cloneNode(true);
            
            // Update the new entry
            newEntry.setAttribute('data-entry-index', entryIndex);
            newEntry.querySelector('.entry-header h4').textContent = `Entry #${entryIndex + 1}`;
            
            // Update all input names and ids
            newEntry.querySelectorAll('input, textarea').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/entries\[0\]/, `entries[${entryIndex}]`);
                }
                if (input.id) {
                    input.id = input.id.replace(/0/, entryIndex);
                }
                // Clear values except hidden inputs
                if (input.type !== 'hidden') {
                    input.value = '';
                }
                // Set default date
                if (input.name.includes('entry_date')) {
                    input.value = new Date().toISOString().split('T')[0];
                }
                // Update onchange events
                if (input.classList.contains('financial-input')) {
                    input.setAttribute('onchange', `calculateEntryTotal(${entryIndex})`);
                }
            });
            
            // Update customer dropdown class
            const customerDropdown = newEntry.querySelector('.customer-dropdown');
            if (customerDropdown) {
                customerDropdown.classList.add('hidden');
                customerDropdown.innerHTML = '';
            }
            
            // Show remove button
            const removeBtn = newEntry.querySelector('.remove-entry-btn');
            removeBtn.classList.remove('hidden');
            removeBtn.setAttribute('onclick', `removeShippingEntry(${entryIndex})`);
            
            // Add border to separate entries
            newEntry.classList.add('border-t', 'border-gray-200', 'dark:border-gray-600', 'pt-6', 'mt-6');
            
            // Append the new entry
            form.appendChild(newEntry);
            
            shippingEntryCount++;
            updateShippingEntryCounter();
            updateShippingSubmitButton();
            
            // Show remove button for first entry if more than one entry
            if (shippingEntryCount > 1) {
                const firstRemoveBtn = form.querySelector('.entry-form[data-entry-index="0"] .remove-entry-btn');
                if (firstRemoveBtn) {
                    firstRemoveBtn.classList.remove('hidden');
                }
            }
            
            // Setup customer search for the new entry
            setupDynamicCustomerSearch();
        }
        
        function removeShippingEntry(entryIndex) {
            const form = document.getElementById('addShippingEntryForm');
            const entryToRemove = form.querySelector(`.entry-form[data-entry-index="${entryIndex}"]`);
            
            if (entryToRemove && shippingEntryCount > 1) {
                entryToRemove.remove();
                shippingEntryCount--;
                updateShippingEntryCounter();
                updateShippingSubmitButton();
                
                // Hide remove button for first entry if only one entry left
                if (shippingEntryCount === 1) {
                    const firstRemoveBtn = form.querySelector('.entry-form[data-entry-index="0"] .remove-entry-btn');
                    if (firstRemoveBtn) {
                        firstRemoveBtn.classList.add('hidden');
                    }
                }
                
                // Renumber remaining entries
                renumberShippingEntries();
            }
        }
        
        function renumberShippingEntries() {
            const form = document.getElementById('addShippingEntryForm');
            const entries = form.querySelectorAll('.entry-form');
            
            entries.forEach((entry, index) => {
                entry.setAttribute('data-entry-index', index);
                entry.querySelector('.entry-header h4').textContent = `Entry #${index + 1}`;
                
                // Update input names
                entry.querySelectorAll('input, textarea').forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/entries\[\d+\]/, `entries[${index}]`);
                    }
                    // Update onchange events for financial inputs
                    if (input.classList.contains('financial-input')) {
                        input.setAttribute('onchange', `calculateEntryTotal(${index})`);
                    }
                });
                
                // Update remove button onclick
                const removeBtn = entry.querySelector('.remove-entry-btn');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeShippingEntry(${index})`);
                }
            });
            
            shippingEntryCount = entries.length;
        }
        
        function updateShippingEntryCounter() {
            const counter = document.getElementById('entryCounter');
            if (counter) {
                counter.textContent = `Entry 1 of ${shippingEntryCount}`;
            }
        }
        
        function updateShippingSubmitButton() {
            const submitBtn = document.getElementById('submitBtnText');
            if (submitBtn) {
                submitBtn.textContent = shippingEntryCount > 1 ? `Save ${shippingEntryCount} Entries` : 'Save Entry';
            }
        }

        // Calculate total amount for a specific entry
        function calculateEntryTotal(entryIndex) {
            const form = document.getElementById('addShippingEntryForm');
            const entry = form.querySelector(`.entry-form[data-entry-index="${entryIndex}"]`);
            if (!entry) return;
            
            const mvStar1 = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_1]"]`)?.value) || 0;
            const mvStar2 = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_2]"]`)?.value) || 0;
            const mvStar3 = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_3]"]`)?.value) || 0;
            const mvStar4 = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_4]"]`)?.value) || 0;
            const mvStar5 = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_5]"]`)?.value) || 0;
            
            const mvStar1Other = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_1_other]"]`)?.value) || 0;
            const mvStar2Other = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_2_other]"]`)?.value) || 0;
            const mvStar3Other = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_3_other]"]`)?.value) || 0;
            const mvStar4Other = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_4_other]"]`)?.value) || 0;
            const mvStar5Other = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][mv_everwin_star_5_other]"]`)?.value) || 0;
            
            const wharfagePayables = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][wharfage_payables]"]`)?.value) || 0;
            const interest = parseFloat(entry.querySelector(`input[name="entries[${entryIndex}][interest]"]`)?.value) || 0;
            
            const total = mvStar1 + mvStar2 + mvStar3 + mvStar4 + mvStar5 + 
                         mvStar1Other + mvStar2Other + mvStar3Other + mvStar4Other + mvStar5Other +
                         wharfagePayables + interest;
            
            const totalField = entry.querySelector(`input[name="entries[${entryIndex}][total]"]`);
            if (totalField) {
                totalField.value = total.toFixed(2);
            }
        }

        // Calculate total amount (legacy function for compatibility)
        function calculateTotal() {
            calculateEntryTotal(0);
        }

        // Submit all shipping entries
        function submitAllShippingEntries() {
            const form = document.getElementById('addShippingEntryForm');
            const formData = new FormData(form);
            
            // Add multiple entries flag
            formData.append('multiple_entries', 'true');
            
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
                    alert(data.message || `${shippingEntryCount} entries created successfully!`);
                    closeAddShippingEntryModal();
                    location.reload();
                } else {
                    alert('Error creating entries: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating entries');
            });
        }

        // Dynamic customer search setup for all entries
        function setupDynamicCustomerSearch() {
            const form = document.getElementById('addShippingEntryForm');
            const customerInputs = form.querySelectorAll('.customer-name-input');
            
            customerInputs.forEach(input => {
                // Skip if already has event listeners
                if (input.hasAttribute('data-search-setup')) return;
                input.setAttribute('data-search-setup', 'true');
                
                const dropdown = input.parentNode.querySelector('.customer-dropdown');
                const idField = input.parentNode.querySelector('.customer-id-input');
                let searchTimeout;
                
                input.addEventListener('input', function() {
                    const query = this.value.trim();
                    
                    clearTimeout(searchTimeout);
                    
                    if (query.length === 0) {
                        dropdown.innerHTML = '';
                        dropdown.classList.add('hidden');
                        idField.value = '';
                        return;
                    }
                    
                    const delay = query.length === 1 ? 0 : 300;
                    
                    searchTimeout = setTimeout(() => {
                        fetch(`{{ route("accounting.search-customers") }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            dropdown.innerHTML = '';
                            
                            if (data.length === 0) {
                                dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500">No customers found</div>';
                                dropdown.classList.remove('hidden');
                                return;
                            }
                            
                            data.slice(0, 10).forEach(customer => {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600';
                                div.textContent = customer.name;
                                div.dataset.id = customer.id;
                                div.dataset.name = customer.name;
                                
                                div.addEventListener('click', function() {
                                    input.value = customer.name;
                                    idField.value = customer.id;
                                    dropdown.classList.add('hidden');
                                });
                                
                                dropdown.appendChild(div);
                            });
                            
                            dropdown.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            dropdown.innerHTML = '<div class="px-4 py-2 text-red-500">Error loading customers</div>';
                            dropdown.classList.remove('hidden');
                        });
                    }, delay);
                });
                
                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            });
        }

        function openEditShippingModal(entryId) {
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
                    document.getElementById('edit_shipping_entry_id').value = entry.id;
                    document.getElementById('edit_shipping_ar').value = entry.ar || '';
                    document.getElementById('edit_shipping_or').value = entry.or || '';
                    document.getElementById('edit_shipping_customer_name').value = entry.customer_name;
                    document.getElementById('edit_shipping_customer_id').value = entry.customer_id || '';
                    
                    // Populate all the MV EVERWIN STAR fields
                    document.getElementById('edit_shipping_mv_everwin_star_1').value = entry.mv_everwin_star_1 || '';
                    document.getElementById('edit_shipping_mv_everwin_star_2').value = entry.mv_everwin_star_2 || '';
                    document.getElementById('edit_shipping_mv_everwin_star_3').value = entry.mv_everwin_star_3 || '';
                    document.getElementById('edit_shipping_mv_everwin_star_4').value = entry.mv_everwin_star_4 || '';
                    document.getElementById('edit_shipping_mv_everwin_star_5').value = entry.mv_everwin_star_5 || '';
                    
                    // Populate other income fields
                    document.getElementById('edit_shipping_mv_everwin_star_1_other').value = entry.mv_everwin_star_1_other || '';
                    document.getElementById('edit_shipping_mv_everwin_star_2_other').value = entry.mv_everwin_star_2_other || '';
                    document.getElementById('edit_shipping_mv_everwin_star_3_other').value = entry.mv_everwin_star_3_other || '';
                    document.getElementById('edit_shipping_mv_everwin_star_4_other').value = entry.mv_everwin_star_4_other || '';
                    document.getElementById('edit_shipping_mv_everwin_star_5_other').value = entry.mv_everwin_star_5_other || '';
                    
                    // Populate additional fields
                    document.getElementById('edit_shipping_wharfage_payables').value = entry.wharfage_payables || '';
                    document.getElementById('edit_shipping_interest').value = entry.interest || '';
                    document.getElementById('edit_shipping_total').value = entry.total;
                    document.getElementById('edit_shipping_remark').value = entry.remark || '';
                    
                    document.getElementById('editShippingEntryModal').classList.remove('hidden');
                } else {
                    alert('Error loading entry data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading entry data');
            });
        }

        function closeEditShippingModal() {
            document.getElementById('editShippingEntryModal').classList.add('hidden');
        }

        // Customer search functionality for shipping
        function setupShippingCustomerSearch(inputId, dropdownId, customerIdField) {
            const input = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            const idField = document.getElementById(customerIdField);
            let searchTimeout;
            
            input.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Clear results if query is empty
                if (query.length === 0) {
                    dropdown.innerHTML = '';
                    dropdown.classList.add('hidden');
                    idField.value = '';
                    return;
                }
                
                // Search immediately for single characters, with slight delay for multiple characters
                const delay = query.length === 1 ? 0 : 300;
                
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route("accounting.search-customers") }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        
                        if (data.length === 0) {
                            dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">No customers found</div>';
                            dropdown.classList.remove('hidden');
                            return;
                        }
                        
                        // Sort results by relevance (exact matches first, then partial matches)
                        data.sort((a, b) => {
                            const aName = a.name.toLowerCase();
                            const bName = b.name.toLowerCase();
                            const queryLower = query.toLowerCase();
                            
                            // Exact match at start gets highest priority
                            const aStartsWithQuery = aName.startsWith(queryLower);
                            const bStartsWithQuery = bName.startsWith(queryLower);
                            
                            if (aStartsWithQuery && !bStartsWithQuery) return -1;
                            if (!aStartsWithQuery && bStartsWithQuery) return 1;
                            
                            // Then by word boundaries (whole word matches)
                            const aWordMatch = aName.includes(' ' + queryLower) || aName.startsWith(queryLower);
                            const bWordMatch = bName.includes(' ' + queryLower) || bName.startsWith(queryLower);
                            
                            if (aWordMatch && !bWordMatch) return -1;
                            if (!aWordMatch && bWordMatch) return 1;
                            
                            // Finally by alphabetical order
                            return aName.localeCompare(bName);
                        });
                        
                        // Limit results to top 10 for better performance
                        data.slice(0, 10).forEach(customer => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-600 last:border-b-0';
                            div.textContent = customer.name;
                            div.dataset.id = customer.id;
                            div.dataset.name = customer.name;
                            
                            // Highlight matching text
                            const nameText = customer.name;
                            const regex = new RegExp(`(${query})`, 'gi');
                            div.innerHTML = nameText.replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-600">$1</span>');
                            
                            div.addEventListener('click', function() {
                                input.value = customer.name;
                                idField.value = customer.id;
                                dropdown.classList.add('hidden');
                            });
                            
                            dropdown.appendChild(div);
                        });
                        
                        // Show the dropdown
                        dropdown.classList.remove('hidden');
                        
                        // Add a "showing X results" indicator if there are more than 10
                        if (data.length > 10) {
                            const moreDiv = document.createElement('div');
                            moreDiv.className = 'px-4 py-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-600';
                            moreDiv.textContent = `Showing 10 of ${data.length} results`;
                            dropdown.appendChild(moreDiv);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching customers:', error);
                        dropdown.innerHTML = '<div class="px-4 py-2 text-red-500">Error loading customers</div>';
                        dropdown.classList.remove('hidden');
                    });
                }, delay);
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Hide dropdown when input loses focus (but allow clicking on dropdown items)
            input.addEventListener('blur', function(e) {
                // Small delay to allow clicking on dropdown items
                setTimeout(() => {
                    if (!dropdown.contains(document.activeElement)) {
                        dropdown.classList.add('hidden');
                    }
                }, 200);
            });
            
            // Show dropdown when input gets focus and has value
            input.addEventListener('focus', function() {
                if (this.value.trim() && dropdown.children.length > 0) {
                    dropdown.classList.remove('hidden');
                }
            });
            
            // Clear selection when input is manually cleared
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    idField.value = '';
                }
            });
            
            // Handle keyboard navigation
            input.addEventListener('keydown', function(e) {
                const items = dropdown.querySelectorAll('div[data-id]');
                let selectedIndex = -1;
                
                // Find currently selected item
                items.forEach((item, index) => {
                    if (item.classList.contains('bg-blue-100')) {
                        selectedIndex = index;
                        item.classList.remove('bg-blue-100', 'dark:bg-blue-600');
                    }
                });
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = selectedIndex < items.length - 1 ? selectedIndex + 1 : 0;
                    if (items[selectedIndex]) {
                        items[selectedIndex].classList.add('bg-blue-100', 'dark:bg-blue-600');
                        items[selectedIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = selectedIndex > 0 ? selectedIndex - 1 : items.length - 1;
                    if (items[selectedIndex]) {
                        items[selectedIndex].classList.add('bg-blue-100', 'dark:bg-blue-600');
                        items[selectedIndex].scrollIntoView({ block: 'nearest' });
                    }
                } else if (e.key === 'Enter' && selectedIndex >= 0 && items[selectedIndex]) {
                    e.preventDefault();
                    const selectedItem = items[selectedIndex];
                    input.value = selectedItem.dataset.name;
                    idField.value = selectedItem.dataset.id;
                    dropdown.classList.add('hidden');
                } else if (e.key === 'Escape') {
                    dropdown.classList.add('hidden');
                }
            });
        }

        // Form submissions for shipping - legacy handler (kept for compatibility)
        document.getElementById('addShippingEntryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitAllShippingEntries();
        });

        document.getElementById('editShippingEntryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const entryId = document.getElementById('edit_shipping_entry_id').value;
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
                    closeEditShippingModal();
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
            setupDynamicCustomerSearch();
            setupShippingCustomerSearch('edit_shipping_customer_name', 'editShippingCustomerDropdown', 'edit_shipping_customer_id');
        });

        // Date filtering functions
        function filterByDate() {
            const selectedDate = document.getElementById('filterDate').value;
            if (selectedDate) {
                // Redirect with date parameter
                const url = new URL(window.location.href);
                url.searchParams.set('date', selectedDate);
                window.location.href = url.toString();
            }
        }

        function openPrintModal() {
            const selectedDate = document.getElementById('filterDate').value;
            let reportUrl = '{{ route("accounting.daily-cash-collection.shipping-print") }}';
            
            if (selectedDate) {
                reportUrl += '?date=' + selectedDate;
            }
            
            window.open(reportUrl, '_blank');
        }

        function clearDateFilter() {
            // Remove date parameter and reload
            const url = new URL(window.location.href);
            url.searchParams.delete('date');
            window.location.href = url.toString();
        }
    </script>
</x-app-layout>
