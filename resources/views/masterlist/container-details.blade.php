<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                Container Details for M/V Everwin Star {{ $ship }} Voyage {{ $voyage }}
            </h2>
        </div>
    </x-slot>

    <style>
        /* Ensure table displays properly with fixed column widths */
        #containerTable {
            table-layout: fixed;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        #containerTable th, #containerTable td {
            border-right: 1px solid #e5e7eb;
            overflow: hidden;
            padding: 0.75rem 1rem;
        }
        
        #containerTable th:last-child, #containerTable td:last-child {
            border-right: none;
        }
        
        /* Make sure content in the Item/Parcel column wraps properly */
        .item-parcel-column {
            word-wrap: break-word;
            overflow-wrap: break-word;
            padding-right: 1rem;
        }

        /* Add some horizontal padding to list items for better readability */
        .item-parcel-column li {
            padding-right: 10px;
            margin-bottom: 4px;
            line-height: 1.4;
        }
        
        /* Wrap text in Container Number, Consignee and Container Owner columns */
        .container-column,
        .consignee-column,
        .owner-column,
        .bl-column,
        .cargo-column {
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
        }
        
        /* Enhanced table styles */
        #containerTable {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        #containerTable thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f3f4f6;
        }
        
        #containerTable tbody tr:hover {
            background-color: rgba(243, 244, 246, 0.5);
        }
        
        .dark #containerTable tbody tr:hover {
            background-color: rgba(55, 65, 81, 0.3);
        }
    </style>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <!-- Filter section -->
        <div class="mb-6">
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                    </svg>
                    Filter Options
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-md shadow-sm">
                        <label for="containerFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search by Container Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
                                </svg>
                            </div>
                            <input type="text" id="containerFilter" class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-600 dark:border-gray-600 dark:text-white" placeholder="Type container number">
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-md shadow-sm">
                        <label for="cargoTypeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Cargo Status</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <select id="cargoTypeFilter" class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-600 dark:border-gray-600 dark:text-white appearance-none">
                                <option value="">All Cargo Status</option>
                                @php
                                    $cargoTypes = [];
                                    foreach($containers as $container) {
                                        foreach($container->orders as $order) {
                                            if (!empty($order->cargoType) && !in_array($order->cargoType, $cargoTypes)) {
                                                $cargoTypes[] = $order->cargoType;
                                            }
                                        }
                                    }
                                    sort($cargoTypes);
                                @endphp
                                @foreach($cargoTypes as $cargoType)
                                    <option value="{{ $cargoType }}">{{ $cargoType }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-md shadow-sm">
                        <label for="consigneeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search by Consignee</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="consigneeFilter" class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-600 dark:border-gray-600 dark:text-white" placeholder="Type to search consignee">
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-md shadow-sm">
                        <label for="paginationSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Per page</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </div>
                            <select id="paginationSelect" class="w-full pl-10 pr-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-600 dark:border-gray-600 dark:text-white appearance-none">
                                <option value="all">Show All</option>
                                <option value="10">10 per page</option>
                                <option value="20">20 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-between items-center mb-4">
            <div class="text-sm text-gray-600 dark:text-gray-400 italic">
            </div>
            <button id="exportExcel" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export to Excel
            </button>
        </div>

        <div class="overflow-x-auto rounded-lg shadow">
            <table id="containerTable" class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer w-1/8" data-sort="container">
                            Container Number <span class="sort-icon">↕</span>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-3/8">
                            Item/Parcel
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer w-1/12" data-sort="cargo">
                            Cargo Status <span class="sort-icon">↕</span>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer w-1/8" data-sort="bl">
                            BL # <span class="sort-icon">↕</span>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer w-1/12" data-sort="consignee">
                            Consignee <span class="sort-icon">↕</span>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer w-1/12" data-sort="owner">
                            Container Owner <span class="sort-icon">↕</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($containers as $container)
                        {{-- Group orders by recName (consignee) --}}
                        @php
                            $consigneeGroups = [];
                            foreach($container->orders as $order) {
                                $consigneeName = $order->recName ?? 'N/A';
                                if (!isset($consigneeGroups[$consigneeName])) {
                                    $consigneeGroups[$consigneeName] = [
                                        'orders' => [],
                                        'parcels' => []
                                    ];
                                }
                                $consigneeGroups[$consigneeName]['orders'][] = $order;
                                foreach($order->parcels as $parcel) {
                                    $consigneeGroups[$consigneeName]['parcels'][] = $parcel;
                                }
                            }
                        @endphp

                        @foreach($consigneeGroups as $consigneeName => $group)                            <tr class="container-row" 
                                data-container="{{ $container->containerName ?? 'N/A' }}" 
                                data-cargo="{{ $group['orders'][0]->cargoType ?? 'N/A' }}" 
                                data-consignee="{{ $consigneeName }}"
                                data-owner="@php
                                        $firstOrder = $group['orders'][0];
                                        $ownerName = 'N/A';
                                        
                                        if($firstOrder->customer) {
                                            if(!empty($firstOrder->customer->company_name)) {
                                                $ownerName = $firstOrder->customer->company_name;
                                            } else {
                                                $ownerName = $firstOrder->customer->first_name . ' ' . $firstOrder->customer->last_name;
                                            }
                                        }
                                        
                                        echo $ownerName;
                                    @endphp"
                                data-bl="@php
                                        $blNumbers = array_unique(array_map(function($order) {
                                            return $order->orderId ?? 'N/A';
                                        }, $group['orders']));
                                        echo implode(', ', $blNumbers);
                                    @endphp">
                                <td class="px-4 py-4 w-1/8 container-column">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $container->containerName ?? 'N/A' }} 
                                        @if($container->type && $container->type != 'Special')
                                            ({{ $container->type }}-Footer)
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 w-3/8 item-parcel-column">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        <ul class="list-disc list-inside">
                                            @foreach($group['parcels'] as $parcel)
                                                <li>
                                                    @if(!empty($parcel->desc))
                                                        {{ $parcel->desc }}
                                                    @elseif(!empty($parcel->itemName))
                                                        {{ $parcel->itemName }}
                                                    @else
                                                        N/A
                                                    @endif
                                                    
                                                    @if(!empty($parcel->quantity) && !empty($parcel->unit))
                                                        ({{ $parcel->quantity }} {{ $parcel->unit }})
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                                <td class="px-4 py-4 w-1/12 cargo-column">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{-- Show cargo type from the first order in group --}}
                                        {{ $group['orders'][0]->cargoType ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 w-1/8 bl-column">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{-- Combine BL numbers with commas --}}
                                        @php
                                            $blNumbers = array_unique(array_map(function($order) {
                                                return $order->orderId ?? 'N/A';
                                            }, $group['orders']));
                                            echo implode(', ', $blNumbers);
                                        @endphp
                                    </div>
                                </td>
                                <td class="px-4 py-4 w-1/12 consignee-column">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $consigneeName }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 w-1/12 owner-column">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        @php
                                            $firstOrder = $group['orders'][0];
                                        @endphp
                                        @if($firstOrder->customer)
                                            @if(!empty($firstOrder->customer->company_name))
                                                {{ $firstOrder->customer->company_name }}
                                            @else
                                                {{ $firstOrder->customer->first_name }} {{ $firstOrder->customer->last_name }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="mt-6 flex justify-between items-center" id="paginationControls">
            <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-md">
                Showing <span id="pageStart" class="font-medium">1</span> to <span id="pageEnd" class="font-medium">10</span> of <span id="totalItems" class="font-medium">0</span> items
            </div>
            <div class="flex space-x-2">
                <button id="prevPage" class="px-3 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 dark:bg-indigo-800 dark:text-indigo-200 dark:hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous
                    </span>
                </button>
                <div id="pageNumbers" class="flex space-x-1">
                    <!-- Page numbers will be inserted here by JavaScript -->
                </div>
                <button id="nextPage" class="px-3 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 dark:bg-indigo-800 dark:text-indigo-200 dark:hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                    <span class="flex items-center">
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>

        @if(count($containers) === 0)
        <div class="text-center py-8 text-gray-600 dark:text-gray-400">
            No containers found for this ship and voyage.
        </div>
        @endif
    </div>
    <br>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const containerRows = document.querySelectorAll('.container-row');
            const containerFilter = document.getElementById('containerFilter');
            const cargoTypeFilter = document.getElementById('cargoTypeFilter');
            const consigneeFilter = document.getElementById('consigneeFilter');
            const paginationSelect = document.getElementById('paginationSelect');
            const sortHeaders = document.querySelectorAll('th[data-sort]');
            
            // Pagination elements
            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const pageNumbersContainer = document.getElementById('pageNumbers');
            const pageStartElement = document.getElementById('pageStart');
            const pageEndElement = document.getElementById('pageEnd');
            const totalItemsElement = document.getElementById('totalItems');
            
            let currentSort = null;
            let currentSortDirection = 'asc';
            let currentPage = 1;
            let itemsPerPage = 'all';
            let filteredRows = [];
            
            // Initialize pagination
            function initPagination() {
                // Get all visible rows after filtering
                filteredRows = Array.from(containerRows).filter(row => row.style.display !== 'none');
                
                // Set total items count
                totalItemsElement.textContent = filteredRows.length;
                
                // If showing all items, hide pagination controls
                if (itemsPerPage === 'all') {
                    document.getElementById('paginationControls').style.display = 'none';
                    filteredRows.forEach(row => {
                        row.style.display = '';
                    });
                    return;
                }
                
                // Show pagination controls
                document.getElementById('paginationControls').style.display = 'flex';
                
                // Calculate total pages
                const itemsPerPageNum = parseInt(itemsPerPage);
                const totalPages = Math.ceil(filteredRows.length / itemsPerPageNum);
                
                // Reset current page if needed
                if (currentPage > totalPages) {
                    currentPage = 1;
                }
                
                // Update pagination buttons state
                prevPageBtn.disabled = currentPage === 1;
                nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
                
                // Create page number buttons
                pageNumbersContainer.innerHTML = '';
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.textContent = i;
                    pageBtn.className = i === currentPage 
                        ? 'px-3 py-2 bg-indigo-600 text-white rounded-md font-medium' 
                        : 'px-3 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 dark:bg-indigo-800 dark:text-indigo-200 dark:hover:bg-indigo-700 transition-colors';
                    pageBtn.addEventListener('click', () => goToPage(i));
                    pageNumbersContainer.appendChild(pageBtn);
                }
                
                // Show current page of items
                const startIndex = (currentPage - 1) * itemsPerPageNum;
                const endIndex = Math.min(startIndex + itemsPerPageNum, filteredRows.length);
                
                // Hide all rows first
                filteredRows.forEach(row => {
                    row.style.display = 'none';
                });
                
                // Show only rows for current page
                for (let i = startIndex; i < endIndex; i++) {
                    filteredRows[i].style.display = '';
                }
                
                // Update page info text
                pageStartElement.textContent = filteredRows.length ? startIndex + 1 : 0;
                pageEndElement.textContent = endIndex;
                totalItemsElement.textContent = filteredRows.length;
            }
            
            // Go to specific page
            function goToPage(page) {
                currentPage = page;
                initPagination();
            }
            
            // Handle next/prev page
            prevPageBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    goToPage(currentPage - 1);
                }
            });
            
            nextPageBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(filteredRows.length / parseInt(itemsPerPage));
                if (currentPage < totalPages) {
                    goToPage(currentPage + 1);
                }
            });
            
            // Handle pagination dropdown change
            paginationSelect.addEventListener('change', function() {
                itemsPerPage = this.value;
                currentPage = 1;
                initPagination();
            });

            // Apply filters when input changes
            function applyFilters() {
                const containerValue = containerFilter.value.toLowerCase();
                const cargoTypeValue = cargoTypeFilter.value.toLowerCase();
                const consigneeValue = consigneeFilter.value.toLowerCase();

                containerRows.forEach(row => {
                    const containerText = row.getAttribute('data-container').toLowerCase();
                    const cargoTypeText = row.getAttribute('data-cargo').toLowerCase();
                    const consigneeText = row.getAttribute('data-consignee').toLowerCase();

                    // Check if the row matches all applied filters
                    const matchesContainer = containerText.includes(containerValue);
                    const matchesCargoType = cargoTypeValue === '' || cargoTypeText === cargoTypeValue;
                    const matchesConsignee = consigneeText.includes(consigneeValue);

                    // Show or hide the row based on the filter matches
                    if (matchesContainer && matchesCargoType && matchesConsignee) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Reset to first page when filters change
                currentPage = 1;
                initPagination();
            }

            // Sort the table by column
            function sortTable(sortBy) {
                const rows = Array.from(containerRows);
                
                // Reset all sort icons
                document.querySelectorAll('.sort-icon').forEach(icon => {
                    icon.textContent = '↕';
                });
                
                // If clicking the same header, toggle direction
                if (currentSort === sortBy) {
                    currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort = sortBy;
                    currentSortDirection = 'asc';
                }
                
                // Update the sort icon
                const currentHeader = document.querySelector(`th[data-sort="${sortBy}"] .sort-icon`);
                currentHeader.textContent = currentSortDirection === 'asc' ? '↑' : '↓';
                
                // Sort the rows
                rows.sort((a, b) => {
                    const aValue = a.getAttribute(`data-${sortBy}`).toLowerCase();
                    const bValue = b.getAttribute(`data-${sortBy}`).toLowerCase();
                    
                    if (aValue < bValue) {
                        return currentSortDirection === 'asc' ? -1 : 1;
                    } else if (aValue > bValue) {
                        return currentSortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
                
                // Re-append the sorted rows to the table
                const tbody = document.querySelector('tbody');
                rows.forEach(row => {
                    tbody.appendChild(row);
                });
                
                // Reset to first page when sorting changes
                currentPage = 1;
                initPagination();
            }

            // Add event listeners to filters
            containerFilter.addEventListener('input', applyFilters);
            cargoTypeFilter.addEventListener('change', applyFilters);
            consigneeFilter.addEventListener('input', applyFilters);
            
            // Add event listeners to sort headers
            sortHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const sortBy = header.getAttribute('data-sort');
                    sortTable(sortBy);
                });
            });

            // Export to Excel functionality
            document.getElementById('exportExcel').addEventListener('click', function() {
                // Show loading state
                const exportBtn = this;
                const originalText = exportBtn.innerHTML;
                exportBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Exporting...';
                exportBtn.disabled = true;
                
                // Small delay to show loading state
                setTimeout(() => {
                    try {
                        // Prepare data for export
                        let tableData = [];
                        
                        // Get headers - use exact column names
                        const headers = [
                            'Container Number',
                            'Item/Parcel', 
                            'Cargo Status',
                            'BL #',
                            'Consignee',
                            'Container Owner'
                        ];
                        tableData.push(headers);
                        
                        // Get visible rows (respect current filter but ignore pagination - export all filtered data)
                        Array.from(containerRows).forEach(row => {
                            // Include row if it matches filters (even if hidden by pagination)
                            const containerText = row.getAttribute('data-container').toLowerCase();
                            const cargoTypeText = row.getAttribute('data-cargo').toLowerCase();
                            const consigneeText = row.getAttribute('data-consignee').toLowerCase();
                            
                            const containerValue = containerFilter.value.toLowerCase();
                            const cargoTypeValue = cargoTypeFilter.value.toLowerCase();
                            const consigneeValue = consigneeFilter.value.toLowerCase();
                            
                            const matchesContainer = containerText.includes(containerValue);
                            const matchesCargoType = cargoTypeValue === '' || cargoTypeText === cargoTypeValue;
                            const matchesConsignee = consigneeText.includes(consigneeValue);
                    
                    if (matchesContainer && matchesCargoType && matchesConsignee) {
                        // Extract data using data attributes and specific cell targeting for accuracy
                        const containerNumber = row.getAttribute('data-container') || 'N/A';
                        const cargoStatus = row.getAttribute('data-cargo') || 'N/A';
                        const blNumber = row.getAttribute('data-bl') || 'N/A';
                        const consignee = row.getAttribute('data-consignee') || 'N/A';
                        const owner = row.getAttribute('data-owner') || 'N/A';
                        
                        // Get Item/Parcel data from the specific column
                        const itemParcelCell = row.querySelector('.item-parcel-column');
                        let itemParcelData = 'N/A';
                        if (itemParcelCell) {
                            const list = itemParcelCell.querySelector('ul');
                            if (list) {
                                const items = [];
                                list.querySelectorAll('li').forEach(li => {
                                    // Clean up the text by removing extra whitespace, line breaks, and normalizing spaces
                                    const itemText = li.textContent
                                        .replace(/\s+/g, ' ')  // Replace multiple whitespace chars (spaces, tabs, etc.) with single space
                                        .replace(/\n|\r/g, '') // Remove line breaks and carriage returns
                                        .trim();               // Trim leading/trailing spaces
                                    
                                    if (itemText && itemText !== '' && itemText !== 'N/A') {
                                        items.push(itemText);
                                    }
                                });
                                itemParcelData = items.length > 0 ? items.join('; ') : 'N/A';
                            } else {
                                // Clean up cell text the same way
                                const cellText = itemParcelCell.textContent
                                    .replace(/\s+/g, ' ')
                                    .replace(/\n|\r/g, '')
                                    .trim();
                                itemParcelData = cellText && cellText !== '' && cellText !== 'N/A' ? cellText : 'N/A';
                            }
                        }
                        
                        // Clean and validate data before adding to export
                        const cleanData = (data) => {
                            if (!data || data === 'undefined' || data === 'null') return 'N/A';
                            return String(data)
                                .replace(/\s+/g, ' ')  // Replace multiple whitespace with single space
                                .replace(/\n|\r/g, '') // Remove line breaks
                                .trim();               // Trim leading/trailing spaces
                        };
                        
                        // Create row data in exact column order
                        const rowData = [
                            cleanData(containerNumber),
                            cleanData(itemParcelData),
                            cleanData(cargoStatus),
                            cleanData(blNumber),
                            cleanData(consignee),
                            cleanData(owner)
                        ];
                        
                        tableData.push(rowData);
                    }
                });
                
                // Convert to CSV with proper escaping
                function escapeCSV(field) {
                    if (field == null) return '';
                    field = String(field);
                    if (field.includes(',') || field.includes('"') || field.includes('\n') || field.includes('\r')) {
                        field = '"' + field.replace(/"/g, '""') + '"';
                    }
                    return field;
                }
                
                let csvContent = tableData.map(row => 
                    row.map(field => escapeCSV(field)).join(',')
                ).join('\n');
                
                // Add BOM for proper Excel UTF-8 support
                const BOM = '\uFEFF';
                csvContent = BOM + csvContent;
                
                // Create download link
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `CONTAINER DETAILS - ES {{ $ship }} VOY {{ $voyage }}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                
                // Download file
                link.click();
                
                // Clean up
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                
                // Show success message
                exportBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Exported!';
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    exportBtn.innerHTML = originalText;
                    exportBtn.disabled = false;
                }, 2000);
                
            } catch (error) {
                console.error('Export failed:', error);
                exportBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Export Failed';
                setTimeout(() => {
                    exportBtn.innerHTML = originalText;
                    exportBtn.disabled = false;
                }, 2000);
            }
        }, 100);
            });
            
            // Initialize the table with pagination
            initPagination();
        });
    </script>
</x-app-layout>
