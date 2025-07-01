<x-app-layout>    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                Container Details for {{ $ship }} - Voyage {{ $voyage }}
            </h2>
        </div>
    </x-slot>

    <style>
        /* Enforce table column widths */
        .table-fixed {
            table-layout: fixed;
        }
        
        /* Handle text overflow in cells */
        td div, th div {
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Ensure Item/Parcel column can expand vertically */
        .items-column {
            overflow: visible !important;
            word-wrap: break-word;
        }
    </style>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <!-- Filter section -->
        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="containerFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Container</label>
                    <input type="text" id="containerFilter" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Type to filter containers...">
                </div>
                <div>
                    <label for="cargoTypeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Cargo Type</label>
                    <select id="cargoTypeFilter" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Cargo Types</option>
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
                </div>
                <div>
                    <label for="consigneeFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Consignee</label>
                    <input type="text" id="consigneeFilter" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Type to filter consignees...">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mb-4">
            <button id="exportExcel" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 ml-2">
                Export to Excel
            </button>
        </div>

        <div class="overflow-x-auto">
            <table id="containerTable" class="min-w-full table-fixed bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="w-[180px] px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="container">
                            Container Number <span class="sort-icon">↕</span>
                        </th>
                        <th class="w-[350px] px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Item/Parcel
                        </th>
                        <th class="w-[100px] px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="cargo">
                            Cargo Type <span class="sort-icon">↕</span>
                        </th>
                        <th class="w-[150px] px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="bl">
                            BL Number <span class="sort-icon">↕</span>
                        </th>
                        <th class="w-[100px] px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="consignee">
                            Consignee <span class="sort-icon">↕</span>
                        </th>
                        <th class="w-[100px] px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" data-sort="owner">
                            Container Owner <span class="sort-icon">↕</span>
                        </th>
                    </tr>
                </thead>                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">                    @foreach($containers as $container)
                        {{-- Get container owner info --}}
                        @php
                            $firstOrder = $container->orders->first();
                            $ownerName = 'N/A';
                            if($firstOrder && $firstOrder->customer) {
                                if(!empty($firstOrder->customer->company_name)) {
                                    $ownerName = $firstOrder->customer->company_name;
                                } else {
                                    $ownerName = $firstOrder->customer->first_name . ' ' . $firstOrder->customer->last_name;
                                }
                            }
                            
                            // Get all distinct BL numbers for the container
                            $allBlNumbers = collect($container->orders)->pluck('orderId')->filter()->unique()->toArray();
                            $allBlNumbersString = implode(', ', $allBlNumbers);
                            
                            // Get all consignees for the container
                            $allConsignees = collect($container->orders)->pluck('recName')->filter()->unique()->toArray();
                            
                            // Group parcels by consignee
                            $parcelsByConsignee = [];
                            foreach($container->orders as $order) {
                                $consigneeName = $order->recName ?? 'N/A';
                                foreach($order->parcels as $parcel) {
                                    if(!isset($parcelsByConsignee[$consigneeName])) {
                                        $parcelsByConsignee[$consigneeName] = [];
                                    }
                                    $parcelsByConsignee[$consigneeName][] = $parcel;
                                }
                            }
                            
                            // Get the cargo type (use the first one)
                            $cargoType = $firstOrder ? ($firstOrder->cargoType ?? 'N/A') : 'N/A';
                        @endphp

                        <tr class="container-row" 
                            data-container="{{ $container->containerName ?? 'N/A' }}" 
                            data-cargo="{{ $cargoType }}"
                            data-consignee="{{ implode(', ', $allConsignees) }}"
                            data-owner="{{ $ownerName }}"
                            data-bl="{{ $allBlNumbersString }}">
                            <td class="w-[180px] px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $container->containerName ?? 'N/A' }} 
                                    @if($container->type && $container->type != 'Special')
                                        ({{ $container->type }}-Footer)
                                    @endif
                                </div>
                            </td>
                            <td class="w-[350px] px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100 items-column">
                                    @foreach($parcelsByConsignee as $consigneeName => $parcels)
                                        <div class="mb-2">
                                            <span class="font-semibold">{{ $consigneeName }}:</span>
                                            <ul class="list-disc list-inside ml-2 mt-1">
                                                @foreach($parcels as $parcel)
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
                                    @endforeach
                                </div>
                            </td>
                            <td class="w-[120px] px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $cargoType }}
                                </div>
                            </td>
                            <td class="w-[150px] px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $allBlNumbersString }}
                                </div>
                            </td>
                            <td class="w-[180px] px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    <ul class="list-none">
                                        @foreach($allConsignees as $consignee)
                                            <li>{{ $consignee }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                            <td class="w-[180px] px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    @php
                                        $firstOrder = $container->orders->first();
                                    @endphp
                                    @if($firstOrder && $firstOrder->customer)
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
                    @endforeach</tbody>
            </table>
        </div>

        @if(count($containers) === 0)
        <div class="text-center py-8 text-gray-600 dark:text-gray-400">
            No containers found for this ship and voyage.
        </div>
        @endif
    </div>    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const containerRows = document.querySelectorAll('.container-row');
            const containerFilter = document.getElementById('containerFilter');
            const cargoTypeFilter = document.getElementById('cargoTypeFilter');
            const consigneeFilter = document.getElementById('consigneeFilter');
            const sortHeaders = document.querySelectorAll('th[data-sort]');
            
            // Enforce column widths
            function enforceColumnWidths() {
                const headerWidths = Array.from(document.querySelectorAll('thead th')).map(th => th.offsetWidth);
                
                document.querySelectorAll('tbody tr').forEach(row => {
                    const cells = row.querySelectorAll('td');
                    cells.forEach((cell, index) => {
                        if (headerWidths[index]) {
                            cell.style.width = headerWidths[index] + 'px';
                        }
                    });
                });
            }
            
            // Call once on page load
            enforceColumnWidths();
            
            // Also call on window resize
            window.addEventListener('resize', enforceColumnWidths);
            
            let currentSort = null;
            let currentSortDirection = 'asc';

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
            });            // Export to Excel functionality
            document.getElementById('exportExcel').addEventListener('click', function() {
                // Prepare data for export
                let tableData = [];
                
                // Get headers
                const headers = [];
                document.querySelectorAll('#containerTable thead th').forEach(th => {
                    headers.push(th.textContent.trim().replace(/[↕↑↓]/g, '').trim());
                });
                tableData.push(headers);
                
                // Get visible row data
                containerRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const rowData = [];
                        row.querySelectorAll('td').forEach((td, index) => {
                            // Special handling for the items/parcels column
                            if (index === 1) { // Items/Parcels column
                                const formattedItems = [];
                                td.querySelectorAll('.font-semibold').forEach(consigneeEl => {
                                    const consignee = consigneeEl.textContent.trim().replace(':', '');
                                    const items = [];
                                    const itemsList = consigneeEl.closest('div').querySelectorAll('li');
                                    itemsList.forEach(li => {
                                        items.push(li.textContent.trim());
                                    });
                                    formattedItems.push(`${consignee}: ${items.join(', ')}`);
                                });
                                rowData.push(formattedItems.join(' | '));
                            } 
                            // Special handling for consignee column
                            else if (index === 4) { // Consignee column
                                const consignees = [];
                                td.querySelectorAll('li').forEach(li => {
                                    consignees.push(li.textContent.trim());
                                });
                                rowData.push(consignees.join(', '));
                            }
                            else {
                                rowData.push(td.textContent.trim());
                            }
                        });
                        tableData.push(rowData);
                    }
                });
                
                // Convert to CSV
                let csvContent = tableData.map(row => row.join(',')).join('\n');
                
                // Create download link
                const encodedUri = encodeURI('data:text/csv;charset=utf-8,' + csvContent);
                const link = document.createElement('a');
                link.setAttribute('href', encodedUri);
                link.setAttribute('download', `container_details_${new Date().toISOString().slice(0,10)}.csv`);
                document.body.appendChild(link);
                
                // Download file
                link.click();
                
                // Clean up
                document.body.removeChild(link);
            });
        });
    </script>
</x-app-layout>
