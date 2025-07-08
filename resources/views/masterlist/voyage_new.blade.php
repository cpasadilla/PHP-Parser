<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('M/V Everwin Star ') . $ship->ship_number}}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="card-header">
            <h5 class="font-semibold">Voyage List by Dock</h5>
            <br>
        </div>

        <!-- Dock Tab Navigation -->
        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    @foreach($voyagesByDock as $dockNumber => $dockVoyages)
                    <button onclick="showDockTab({{ $dockNumber }})" id="dock-{{ $dockNumber }}-tab" class="dock-tab-button py-2 px-1 border-b-2 font-medium text-sm {{ $loop->first ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        DOCK {{ $dockNumber }}
                    </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Dock Content Tabs -->
        @foreach($voyagesByDock as $dockNumber => $dockVoyages)
        <div id="dock-{{ $dockNumber }}-content" class="dock-content {{ $loop->first ? '' : 'hidden' }}">
            <h6 class="font-medium mb-4 text-gray-700 dark:text-gray-300">DOCK {{ $dockNumber }} Voyages</h6>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse" id="dock-{{ $dockNumber }}-table">
                    <thead class="bg-gray-200 dark:bg-dark-eval-0">
                        <tr>
                            <th class="p-2 text-gray-700 dark:text-white cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-600" onclick="sortTable({{ $dockNumber }}, 0)" style="position: relative;">
                                Voyage Number 
                                <span id="dock-{{ $dockNumber }}-sort-indicator" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">▲</span>
                            </th>
                            <th class="p-2 text-gray-700 dark:text-white" hidden>Ship</th>
                            <th class="p-2 text-gray-700 dark:text-white">Origin</th>
                            <th class="p-2 text-gray-700 dark:text-white">Destination</th>
                            <th class="p-2 text-gray-700 dark:text-white">Status</th>
                            <th class="p-2 text-gray-700 dark:text-white">Last Updated</th>
                            <th class="p-2 text-gray-700 dark:text-white">Total of BL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dockVoyages as $voyage)
                        @php
                            if ($ship->ship_number == 'I' || $ship->ship_number == 'II') {
                                $key = $voyage->v_num . '-' . $voyage->inOut;
                            }
                            else{
                                $key = $voyage->v_num;
                            }
                            $orderCount = $orderCounts[$key . '_dock_' . $dockNumber] ?? 0; // Get the count or default to 0
                        @endphp

                        <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-2 text-center">
                                <a href="{{ route('masterlist.voyage-orders-by-id', ['voyageId' => $voyage->id]) }}" class="text-blue-500 hover:underline">
                                    {{ $key }}
                                </a>
                            </td>
                            <td class="p-2 text-center" hidden>{{ $voyage->ship }}</td>
                            <td class="p-2 text-center">{{ $voyageRoutes[$key . '_dock_' . $dockNumber]['origin'] ?? 'N/A' }}</td>
                            <td class="p-2 text-center">{{ $voyageRoutes[$key . '_dock_' . $dockNumber]['destination'] ?? 'N/A' }}</td>
                            <td class="p-2 text-center">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'voyage', 'edit'))
                                <form action="{{ route('voyage.update-status', ['id' => $voyage->id]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" class="px-4 py-2 pr-8 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[100px] appearance-none bg-no-repeat bg-right @if($voyage->lastStatus == 'READY') bg-green-100 text-green-800 @elseif($voyage->lastStatus == 'STOP') bg-red-100 text-red-800 @else bg-gray-100 @endif" style="background-image: url('data:image/svg+xml;charset=US-ASCII,<svg width=\"12\" height=\"12\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M4 8 0 4h8z\" fill=\"%23888\"/></svg>'); background-position: right 0.5rem center; background-size: 12px;">
                                        <option value="READY" {{ $voyage->lastStatus == 'READY' ? 'selected' : '' }}>READY</option>
                                        <option value="STOP" {{ $voyage->lastStatus == 'STOP' ? 'selected' : '' }}>STOP</option>
                                    </select>
                                </form>
                                @else
                                <span class="px-4 py-2 text-sm @if($voyage->lastStatus == 'READY') text-green-800 @elseif($voyage->lastStatus == 'STOP') text-red-800 @else text-gray-600 @endif">
                                    {{ $voyage->lastStatus }}
                                </span>
                                @endif
                            </td>
                            <td class="p-2 text-center">{{ \Carbon\Carbon::parse($voyage->lastUpdated)->format('F d, Y') }}</td>
                            <td class="p-2 text-center">{{ $orderCount }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                No voyages found for DOCK {{ $dockNumber }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
        
        @if(empty($voyagesByDock))
        <div class="text-center py-8">
            <p class="text-gray-500 dark:text-gray-400">No voyages found for this ship.</p>
        </div>
        @endif
    </div>

    <script>
        // Track sort directions for each dock
        let dockSortDirections = {};

        function showDockTab(dockNumber) {
            // Hide all dock content
            document.querySelectorAll('.dock-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.dock-tab-button').forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected dock content
            document.getElementById('dock-' + dockNumber + '-content').classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById('dock-' + dockNumber + '-tab');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-blue-500', 'text-blue-600');
        }

        function sortTable(dockNumber, columnIndex) {
            const tableId = 'dock-' + dockNumber + '-table';
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Initialize sort direction for this dock if not exists
            if (!dockSortDirections[dockNumber]) {
                dockSortDirections[dockNumber] = 'asc';
            }
            
            // Toggle sort direction
            dockSortDirections[dockNumber] = dockSortDirections[dockNumber] === 'asc' ? 'desc' : 'asc';
            const sortDirection = dockSortDirections[dockNumber];
            
            // Update sort indicator
            const indicator = document.getElementById('dock-' + dockNumber + '-sort-indicator');
            indicator.textContent = sortDirection === 'asc' ? '▲' : '▼';
            
            // Filter out empty rows (like "No voyages found" message)
            const dataRows = rows.filter(row => {
                const firstCell = row.cells[0];
                return firstCell && !firstCell.textContent.includes('No voyages found');
            });
            
            // Sort data rows based on voyage number
            dataRows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent.trim();
                const bValue = b.cells[columnIndex].textContent.trim();
                
                // Handle numeric sorting for voyage numbers like "43", "7-IN", "8-OUT", etc.
                const aNum = extractVoyageNumber(aValue);
                const bNum = extractVoyageNumber(bValue);
                
                if (sortDirection === 'asc') {
                    return aNum - bNum || aValue.localeCompare(bValue);
                } else {
                    return bNum - aNum || bValue.localeCompare(aValue);
                }
            });
            
            // Clear and re-append sorted rows
            tbody.innerHTML = '';
            dataRows.forEach(row => {
                tbody.appendChild(row);
            });
            
            // Re-append any non-data rows (like empty state messages)
            const nonDataRows = rows.filter(row => {
                const firstCell = row.cells[0];
                return firstCell && firstCell.textContent.includes('No voyages found');
            });
            nonDataRows.forEach(row => {
                tbody.appendChild(row);
            });
        }

        function extractVoyageNumber(voyageText) {
            // Extract the numeric part from voyage numbers like "43", "7-IN", "8-OUT"
            const match = voyageText.match(/(\d+)/);
            return match ? parseInt(match[1]) : 0;
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sort directions for all docks to ascending
            @foreach($voyagesByDock as $dockNumber => $dockVoyages)
            dockSortDirections[{{ $dockNumber }}] = 'asc';
            @endforeach
            
            // Set initial sort indicators to ascending
            setTimeout(() => {
                @foreach($voyagesByDock as $dockNumber => $dockVoyages)
                if (document.getElementById('dock-{{ $dockNumber }}-table')) {
                    sortTable({{ $dockNumber }}, 0);
                }
                @endforeach
            }, 100);
        });
    </script>
</x-app-layout>