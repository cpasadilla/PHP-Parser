<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.location.href='{{ route('masterlist.customer') }}';" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Orders for Customer: ') . $mainAccount->id . ' - ' . (!empty($mainAccount->first_name) || !empty($mainAccount->last_name) ? $mainAccount->first_name . ' ' . $mainAccount->last_name : $mainAccount->company_name) }}
            </h2>
            <a hidden href="{{ route('masterlist.soa_list', ['customer_id' => $mainAccount->id]) }}" class="text-blue-500 hover:underline">
                <x-button variant="primary" class="items-center max-w-xs gap-2">
                    <x-heroicon-o-document-text class="w-6 h-6" aria-hidden="true" />
                    <span>View SOA</span>
                </x-button>
            </a>
        </div>
    </x-slot>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <!-- Main Account Tab -->
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 rounded-t-lg text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" id="mainAccountTab" onclick="showTab('mainAccount', 'mainAccountTab')">
                    Main Account: {{ (!empty($mainAccount->first_name) || !empty($mainAccount->last_name) ? $mainAccount->first_name . ' ' . $mainAccount->last_name : $mainAccount->company_name) }}
                </button>
            </li>
            <!-- Sub-Accounts Tabs -->
            @foreach($subAccounts as $subAccount)
                <li class="mr-2">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" id="subAccountTab{{ $subAccount->id }}" onclick="showTab('subAccount{{ $subAccount->id }}', 'subAccountTab{{ $subAccount->id }}')">
                        Sub Account:
                        @if(!empty($subAccount->company_name))
                            {{ $subAccount->company_name }}
                        @else
                            {{ $subAccount->first_name }} {{ $subAccount->last_name }}
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Main Account Table -->
    <div id="mainAccount" class="tab-content p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="card-header">
            <h5 class="font-semibold">{{ __('Main Account: ') . $mainAccount->first_name . ' ' . $mainAccount->last_name }} (ID: {{ $mainAccount->id }})</h5>
            <br>
        </div>
        <table class="w-full border-collapse">
            <thead class="bg-gray-200 dark:bg-dark-eval-0">
                <tr>
                    <th class="p-2 text-black-700 dark:text-white-700">#</th>
                    <th class="p-2 text-black-700 dark:text-white-700 bl-number-column">BL #</th>
                    <th class="p-2 text-black-700 dark:text-white-700">
                        SHIP
                        <select id="shipFilter" class="ml-2 text-sm border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                            <option value="">All</option>
                            @foreach($mainAccount->orders->pluck('shipNum')->unique() as $ship)
                                <option value="{{ $ship }}">{{ $ship }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th class="p-2 text-black-700 dark:text-white-700">
                        VOYAGE #
                        <select id="voyageFilter" class="ml-2 text-sm border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                            <option value="">All</option>
                            @foreach($mainAccount->orders->pluck('voyageNum')->unique() as $voyage)
                                <option value="{{ $voyage }}">{{ $voyage }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th class="p-2 text-black-700 dark:text-white-700">ORIGIN</th>
                    <th class="p-2 text-black-700 dark:text-white-700">TOTAL AMOUNT</th>
                    <th class="p-2 text-black-700 dark:text-white-700">CARGO STATUS</th>
                    <th class="p-2 text-black-700 dark:text-white-700">
                        BL STATUS
                        <select id="blStatusFilter" class="ml-2 text-sm border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                            <option value="">All</option>
                            @foreach($mainAccount->orders->pluck('blStatus')->unique() as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th class="p-2 text-black-700 dark:text-white-700">DATE CREATED</th>
                    @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'edit'))
                    <th class="p-2 text-black-700 dark:text-white-700">UPDATE</th>
                    @endif
                    <th class="p-2 text-black-700 dark:text-white-700">VIEW BL</th>
                    @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'delete'))
                    <th class="p-2 text-black-700 dark:text-white-700">DELETE</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-gray-900 dark:text-gray-300">
                <!-- Loop through BLs for the main account -->
                @foreach($mainAccount->orders as $order)
                    <tr class="border-b border-gray-200 dark:border-gray-700" data-ship="{{ $order->shipNum }}" data-voyage="{{ $order->voyageNum }}" data-bl-status="{{ $order->blStatus }}">
                        <td class="p-2 text-center">{{ $loop->iteration }}</td>
                        <td class="p-2 text-center bl-number-column">{{ $order->orderId }}</td>
                        <td class="p-2 text-center">{{ $order->shipNum }}</td>
                        <td class="p-2 text-center">{{ $order->voyageNum }}</td>
                        <td class="p-2 text-center">{{ $order->origin }}</td>
                        <td class="p-2 text-center">{{ number_format($order->totalAmount, 2) }}</td>
                        <td class="p-2 text-center">{{ $order->cargoType }}</td>
                        <td class="p-2 text-center">{{ $order->blStatus }}</td>
                        <td class="p-2 text-center">{{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y') }}</td>
                        @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'edit'))
                        <td class="p-2 text-center">
                            <a href="{{ route('masterlist.edit-bl', $order->id) }}" class="text-blue-500 hover:underline flex items-center justify-center gap-2">
                                <x-heroicon-o-pencil-alt class="w-6 h-6" aria-hidden="true" />
                            </a>
                        </td>
                        @endif
                        <td class="p-2 text-center">
                            <a href="{{ route('orders.view', $order->id) }}" class="text-yellow-500 hover:underline flex items-center justify-center gap-2">
                                <x-heroicon-o-document class="w-6 h-6" aria-hidden="true" />
                            </a>
                        </td>
                        @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'delete'))
                        <td class="p-2 text-center">
                            <form action="{{ route('masterlist.delete-order', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order and all associated parcels?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center text-red-500 hover:underline justify-center">
                                <x-heroicon-o-trash class="w-6 h-6" aria-hidden="true" />
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Sub Accounts Tables -->
    @foreach($subAccounts as $subAccount)
        <div id="subAccount{{ $subAccount->id }}" class="tab-content p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1 hidden">
            <div class="card-header">
                <h5 class="font-semibold">
                    Sub Account (ID: {{ $subAccount->sub_account_number }}):
                    @if(!empty($subAccount->company_name))
                        {{ $subAccount->company_name }} (Company)
                    @else
                        {{ $subAccount->first_name }} {{ $subAccount->last_name }}
                    @endif
                </h5>
                <br>
            </div>
            <table class="w-full border-collapse">
                <thead class="bg-gray-200 dark:bg-dark-eval-0">
                    <tr>
                        <th class="p-2 text-black-700 dark:text-white-700">#</th>
                        <th class="p-2 text-black-700 dark:text-white-700 bl-number-column">BL #</th>
                        <th class="p-2 text-black-700 dark:text-white-700">
                            SHIP
                            <select id="shipFilter{{ $subAccount->id }}" class="sub-ship-filter ml-2 text-sm border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                                <option value="">All</option>
                                @foreach($subAccount->orders->pluck('shipNum')->unique() as $ship)
                                    <option value="{{ $ship }}">{{ $ship }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="p-2 text-black-700 dark:text-white-700">
                            VOYAGE #
                            <select id="voyageFilter{{ $subAccount->id }}" class="sub-voyage-filter ml-2 text-sm border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                                <option value="">All</option>
                                @foreach($subAccount->orders->pluck('voyageNum')->unique() as $voyage)
                                    <option value="{{ $voyage }}">{{ $voyage }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="p-2 text-black-700 dark:text-white-700">ORIGIN</th>
                        <th class="p-2 text-black-700 dark:text-white-700">TOTAL AMOUNT</th>
                        <th class="p-2 text-black-700 dark:text-white-700">CARGO STATUS</th>
                        <th class="p-2 text-black-700 dark:text-white-700">
                            BL STATUS
                            <select id="blStatusFilter{{ $subAccount->id }}" class="sub-bl-status-filter ml-2 text-sm border rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                                <option value="">All</option>
                                @foreach($subAccount->orders->pluck('blStatus')->unique() as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="p-2 text-black-700 dark:text-white-700">DATE CREATED</th>
                        @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'edit'))
                        <th class="p-2 text-black-700 dark:text-white-700">UPDATE</th>
                        @endif
                        <th class="p-2 text-black-700 dark:text-white-700">VIEW BL</th>
                        @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'delete'))
                        <th class="p-2 text-black-700 dark:text-white-700">DELETE</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-gray-900 dark:text-gray-300">
                    @foreach($subAccount->orders as $order)
                        <tr class="border-b border-gray-200 dark:border-gray-700" data-ship="{{ $order->shipNum }}" data-voyage="{{ $order->voyageNum }}" data-bl-status="{{ $order->blStatus }}">
                            <td class="p-2 text-center">{{ $loop->iteration }}</td>
                            <td class="p-2 text-center bl-number-column">{{ $order->orderId }}</td>
                            <td class="p-2 text-center">{{ $order->shipNum }}</td>
                            <td class="p-2 text-center">{{ $order->voyageNum }}</td>
                            <td class="p-2 text-center">{{ $order->origin }}</td>
                            <td class="p-2 text-center">{{ number_format($order->totalAmount, 2) }}</td>
                            <td class="p-2 text-center">{{ $order->cargoType }}</td>
                            <td class="p-2 text-center">{{ $order->blStatus }}</td>
                            <td class="p-2 text-center">{{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y') }}</td>
                            @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'edit'))
                            <td class="p-2 text-center">
                                <a href="{{ route('masterlist.edit-bl', $order->id) }}" class="text-blue-500 hover:underline flex items-center justify-center gap-2">
                                    <x-heroicon-o-pencil-alt class="w-6 h-6" aria-hidden="true" />
                                </a>
                            </td>
                            @endif
                            <td class="p-2 text-center">
                                <a href="{{ route('orders.view', $order->id) }}" class="text-yellow-500 hover:underline flex items-center justify-center gap-2">
                                    <x-heroicon-o-document class="w-6 h-6" aria-hidden="true" />
                                </a>
                            </td>
                            @if(Auth::user()->hasSubpagePermission('masterlist', 'customer', 'delete'))
                            <td class="p-2 text-center">
                                <form action="{{ route('masterlist.delete-order', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order and all associated parcels?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center text-red-500 hover:underline justify-center">
                                        <x-heroicon-o-trash class="w-6 h-6" aria-hidden="true" />
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if(!empty($subAccount->company_name))
            <div class="mt-4 p-3 bg-blue-50 dark:bg-gray-800 rounded text-sm" hidden>
                <p><strong>Debug Info - Company Sub-Account:</strong></p>
                <p>Orders Count: {{ $subAccount->orders->count() }}</p>
                <p>Account Number: {{ $subAccount->sub_account_number }}</p>
                <p>Company Name: {{ $subAccount->company_name }}</p>
                
                @if($subAccount->orders->count() == 0)
                <p class="mt-2 text-yellow-600 dark:text-yellow-400">
                    No orders found for this company sub-account.
                    If you expected orders to appear here, check that:
                    <ul class="list-disc ml-5">
                        <li>The orders have the correct sub-account number set in either recId or shipperId field</li>
                        <li>The origin field in the orders matches your business rules (Manila for recId, Batanes for shipperId)</li>
                    </ul>
                </p>
                @endif
            </div>
            @endif
        </div>
    @endforeach

    <script>
        function showTab(tabId, tabButtonId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));

            // Remove active class from all tabs
            document.querySelectorAll('[id^="mainAccountTab"], [id^="subAccountTab"]').forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-500', 'dark:text-blue-400', 'border-blue-400');
                tab.classList.add('text-gray-700', 'dark:text-gray-300');
            });

            // Show the selected tab content
            document.getElementById(tabId).classList.remove('hidden');

            // Add active class to the selected tab
            const selectedTab = document.getElementById(tabButtonId);
            selectedTab.classList.add('border-blue-500', 'dark:border-blue-400');
            selectedTab.classList.add('text-blue-500', 'dark:text-blue-400');
            selectedTab.classList.remove('text-gray-700', 'dark:text-gray-300');

            // Save the active tab to localStorage
            localStorage.setItem('activeTab', tabId);
            localStorage.setItem('activeTabButton', tabButtonId);
        }

        // Restore the active tab on page load
        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = localStorage.getItem('activeTab') || 'mainAccount';
            const activeTabButton = localStorage.getItem('activeTabButton') || 'mainAccountTab';
            showTab(activeTab, activeTabButton);
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Main account filtering
            const shipFilter = document.getElementById('shipFilter');
            const voyageFilter = document.getElementById('voyageFilter');
            const blStatusFilter = document.getElementById('blStatusFilter');

            function filterTable(tableId, shipFilterId, voyageFilterId, blStatusFilterId) {
                const shipFilterElem = document.getElementById(shipFilterId);
                const voyageFilterElem = document.getElementById(voyageFilterId);
                const blStatusFilterElem = document.getElementById(blStatusFilterId);
                
                if (!shipFilterElem || !voyageFilterElem || !blStatusFilterElem) return;
                
                const shipValue = shipFilterElem.value.toLowerCase();
                const voyageValue = voyageFilterElem.value.toLowerCase();
                const blStatusValue = blStatusFilterElem.value.toLowerCase();
                
                const table = document.getElementById(tableId);
                if (!table) return;
                
                table.querySelectorAll('tbody tr').forEach(row => {
                    const ship = row.getAttribute('data-ship')?.toLowerCase() || '';
                    const voyage = row.getAttribute('data-voyage')?.toLowerCase() || '';
                    const blStatus = row.getAttribute('data-bl-status')?.toLowerCase() || '';

                    row.style.display = 
                        (shipValue === '' || ship === shipValue) &&
                        (voyageValue === '' || voyage === voyageValue) &&
                        (blStatusValue === '' || blStatus === blStatusValue)
                            ? ''
                            : 'none';
                });
            }

            // Add event listeners for main account filters
            if (shipFilter && voyageFilter && blStatusFilter) {
                const filterMainTable = () => filterTable('mainAccount', 'shipFilter', 'voyageFilter', 'blStatusFilter');
                shipFilter.addEventListener('change', filterMainTable);
                voyageFilter.addEventListener('change', filterMainTable);
                blStatusFilter.addEventListener('change', filterMainTable);
            }
            
            // Setup filtering for sub-accounts
            document.querySelectorAll('[id^="subAccount"]').forEach(subAccountDiv => {
                const subAccountId = subAccountDiv.id.replace('subAccount', '');
                const subShipFilter = document.getElementById(`shipFilter${subAccountId}`);
                const subVoyageFilter = document.getElementById(`voyageFilter${subAccountId}`);
                const subBlStatusFilter = document.getElementById(`blStatusFilter${subAccountId}`);
                
                if (subShipFilter && subVoyageFilter && subBlStatusFilter) {
                    const filterSubTable = () => filterTable(`subAccount${subAccountId}`, 
                                                             `shipFilter${subAccountId}`, 
                                                             `voyageFilter${subAccountId}`, 
                                                             `blStatusFilter${subAccountId}`);
                    
                    subShipFilter.addEventListener('change', filterSubTable);
                    subVoyageFilter.addEventListener('change', filterSubTable);
                    subBlStatusFilter.addEventListener('change', filterSubTable);
                }
            });
        });
    </script>

    <style>
        .border-blue-500 {
            border-color: #3b82f6 !important; /* Blue border for active tab in light mode */
        }
        .text-blue-500 {
            color: #3b82f6 !important; /* Blue text for active tab in light mode */
        }
        .dark .dark\:border-blue-400 {
            border-color: #60a5fa !important; /* Slightly lighter blue border for active tab in dark mode */
        }
        .dark .dark\:text-blue-400 {
            color: #60a5fa !important; /* Slightly lighter blue text for active tab in dark mode */
        }
        
        /* Make BL # column wider */
        .bl-number-column {
            width: 120px !important;
            min-width: 120px !important;
        }
        
        /* Improve form controls in dark mode */
        .dark select option {
            background-color: #1f2937; /* Dark background for dropdown options */
        }
        
        /* Table hover effects for both modes */
        .tab-content tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05); /* Light hover effect */
        }
        .dark .tab-content tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05); /* Dark hover effect */
        }
        
        /* Company sub-account highlight */
        .company-tag {
            background-color: #eef2ff;
            color: #4f46e5;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 4px;
        }
        .dark .company-tag {
            background-color: #312e81;
            color: #a5b4fc;
        }
    </style>
    
    <script>
        // Add company tags to tabs for better visibility
        document.addEventListener('DOMContentLoaded', () => {
            // Find all the sub-account tabs that have company names
            document.querySelectorAll('[id^="subAccountTab"]').forEach(tab => {
                const tabText = tab.innerText;
                if (tabText.includes("Sub Account:") && !tabText.includes("(Company)")) {
                    // Look for company names that don't have first/last names
                    const textContent = tab.textContent.trim();
                    if (textContent.includes("Sub Account:") && !textContent.match(/[A-Z][a-z]+ [A-Z][a-z]+/)) {
                        // Add a visual indicator for company accounts
                        const span = document.createElement('span');
                        span.className = 'company-tag';
                        span.textContent = 'Company';
                        tab.appendChild(span);
                    }
                }
            });
        });
    </script>
</x-app-layout>
