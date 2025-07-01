@push('styles')
<style>
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    /* Style for active tab */
    .border-blue-500 {
        border-color: #3b82f6 !important;
    }
    .text-blue-500 {
        color: #3b82f6 !important;
    }
    .dark .dark\:border-blue-400 {
        border-color: #60a5fa !important;
    }
    .dark .dark\:text-blue-400 {
        color: #60a5fa !important;
    }
    
    /* Table hover effects */
    .tab-content tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    .dark .tab-content tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    
    /* Accordion transition */
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    /* Nested table styles */
    .nested-table {
        border-collapse: collapse;
        width: 100%;
    }
    .nested-table th,
    .nested-table td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
    }
    .nested-table th {
        background-color: #f2f2f2;
        text-align: left;
    }
</style>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Statement of Account') }}
            </h2>
        </div>
    </x-slot>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <!-- Customers Tab -->
            <li class="mr-2">
                <button class="inline-block p-4 border-b-2 rounded-t-lg text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" 
                        id="customersTab" 
                        onclick="showTab('customers', 'customersTab')">
                    Customers
                </button>
            </li>
            <!-- Ship Tabs -->
            @foreach($ships as $ship)
                <li class="mr-2" hidden>
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" 
                            id="shipTab{{ $ship->id }}" 
                            onclick="showTab('ship-{{ $ship->id }}', 'shipTab{{ $ship->id }}')">
                        M/V EVERWIN STAR {{ $ship->ship_number }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Tab Contents -->
    <!-- Customers Tab Content -->
    <div id="customers" class="tab-content p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <!-- Add Customer Button -->
        <div class="flex justify-end mb-4">
            <button onclick="openAddCustomerModal()" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-700">
                + Add Customer
            </button>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <input type="text" id="tableSearch" placeholder="Search in table..." 
                class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white">
        </div>

        <!-- Customers Table -->
        <div class="overflow-x-auto">
            <table id="soaCustomerTable" class="w-full border-collapse">
                <thead class="bg-gray-200 dark:bg-dark-eval-0">
                    <tr>
                        <th class="p-2 text-left">Customer ID</th>
                        <th class="p-2 text-left">First Name</th>
                        <th class="p-2 text-left">Last Name</th>
                        <th class="p-2 text-left">Company Name</th>
                        <th class="p-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="noCustomersRow">
                        <td colspan="5" class="p-4 text-center">No customers added yet</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ship Tab Contents -->
    @foreach($ships as $ship)
        <div id="ship-{{ $ship->id }}" class="tab-content p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <h3 class="text-lg font-semibold mb-4">Voyages for {{ $ship->ship_number }}</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-200 dark:bg-dark-eval-0">
                        <tr>
                            <th class="p-2 text-left">Voyage Number</th>
                            <th class="p-2 text-left">Direction</th>
                            <th class="p-2 text-left">Status</th>
                            <th class="p-2 text-left">Last Updated</th>
                            <th class="p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $shipVoyages = $voyages->where('ship', $ship->ship_number);
                        @endphp
                        @forelse($shipVoyages as $voyage)
                            <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="p-2">{{ $voyage->v_num }}</td>
                                <td class="p-2">{{ $voyage->inOut ?: 'N/A' }}</td>
                                <td class="p-2">{{ $voyage->lastStatus }}</td>
                                <td class="p-2">{{ $voyage->lastUpdated }}</td>
                                <td class="p-2 text-center">
                                    <div class="flex space-x-2 justify-center">
                                        <a href="{{ route('masterlist.voyage-orders', ['shipNum' => $voyage->ship, 'voyageNum' => $voyage->v_num]) }}" 
                                            class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-700">
                                            View Orders
                                        </a>
                                        <button class="px-3 py-1 text-white bg-green-500 rounded hover:bg-green-700"
                                                onclick="toggleAccordion('voyage-details-{{ $ship->ship_number }}-{{ $voyage->v_num }}')">
                                            Show Details
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr id="voyage-details-{{ $ship->ship_number }}-{{ $voyage->v_num }}" class="hidden">
                                <td colspan="5" class="p-4">
                                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="font-semibold">Customer Details for Voyage: {{ $voyage->v_num }}</h4>
                                            <a href="{{ route('masterlist.soa_voy_temp', ['ship' => $ship->ship_number, 'voyage' => $voyage->v_num]) }}" 
                                               class="px-3 py-1 text-white bg-green-500 rounded hover:bg-green-700">
                                                View Voyage SOA Report
                                            </a>
                                        </div>
                                        @php
                                            // Get all orders for this specific ship and voyage
                                            $voyageOrders = App\Models\Order::where('shipNum', $ship->ship_number)
                                                ->where('voyageNum', $voyage->v_num)
                                                ->with(['shipper', 'receiver', 'parcels'])
                                                ->get();
                                            
                                            // Group by customer (both shipper and receiver based on origin)
                                            $voyageCustomers = collect();
                                            
                                            foreach($voyageOrders as $order) {
                                                // Determine if customer is shipper or receiver based on origin
                                                $customerId = null;
                                                $customer = null;
                                                
                                                if($order->origin == 'Manila') {
                                                    $customerId = $order->recId;
                                                    $customer = $order->receiver;
                                                } else if($order->origin == 'Batanes') {
                                                    $customerId = $order->shipperId;
                                                    $customer = $order->shipper;
                                                }
                                                
                                                if($customer && !$voyageCustomers->has($customerId)) {
                                                    $voyageCustomers->put($customerId, [
                                                        'customer' => $customer,
                                                        'orders' => collect([$order]),
                                                        'blNumbers' => collect([$order->blNo]),
                                                        'voyageFreight' => $order->totalFreight,
                                                        'voyageValuation' => $order->valuationFee,
                                                        'voyageTotal' => $order->totalFreight + $order->valuationFee,
                                                    ]);
                                                } else if($customer) {
                                                    $customerData = $voyageCustomers->get($customerId);
                                                    $customerData['orders']->push($order);
                                                    $customerData['blNumbers']->push($order->blNo);
                                                    $customerData['voyageFreight'] += $order->totalFreight;
                                                    $customerData['voyageValuation'] += $order->valuationFee;
                                                    $customerData['voyageTotal'] += ($order->totalFreight + $order->valuationFee);
                                                    
                                                    $voyageCustomers->put($customerId, $customerData);
                                                }
                                            }
                                        @endphp
                                        
                                        @if($voyageCustomers->count() > 0)
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-100 dark:bg-gray-800">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left">Customer Name</th>
                                                            <th class="px-4 py-2 text-left">Customer ID</th>
                                                            <th class="px-4 py-2 text-left">BL Numbers</th>
                                                            <th class="px-4 py-2 text-right">Voyage Freight (₱)</th>
                                                            <th class="px-4 py-2 text-right">Voyage Valuation (₱)</th>
                                                            <th class="px-4 py-2 text-right">Voyage Total (₱)</th>
                                                            <th class="px-4 py-2 text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($voyageCustomers as $customerId => $data)
                                                            <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                <td class="px-4 py-2">
                                                                    {{ !empty($data['customer']->company_name) ? $data['customer']->company_name : $data['customer']->first_name . ' ' . $data['customer']->last_name }}
                                                                </td>
                                                                <td class="px-4 py-2">{{ $customerId }}</td>
                                                                <td class="px-4 py-2">{{ $data['blNumbers']->implode(', ') }}</td>
                                                                <td class="px-4 py-2 text-right">{{ number_format($data['voyageFreight'], 2) }}</td>
                                                                <td class="px-4 py-2 text-right">{{ number_format($data['voyageValuation'], 2) }}</td>
                                                                <td class="px-4 py-2 text-right">{{ number_format($data['voyageTotal'], 2) }}</td>
                                                                <td class="px-4 py-2 text-center">
                                                                    <a href="{{ route('masterlist.soa_temp', [
                                                                        'ship' => $ship->ship_number, 
                                                                        'voyage' => urlencode($voyage->v_num),
                                                                        'customerId' => $customerId
                                                                    ]) }}" 
                                                                    class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-700"
                                                                    target="_blank">
                                                                        View SOA
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-center py-4 text-gray-500 dark:text-gray-400">No customer data available for this voyage</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center">No voyages found for this ship</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Add Customer to SOA</h3>
            
            <form id="addCustomerForm">
                <!-- Search bar for filtering customers -->
                <div class="mb-4">
                    <label for="customerSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search Customer</label>
                    <input type="text" id="customerSearch" placeholder="Search by name or company..." 
                           class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white mb-2">
                </div>
                
                <div class="mb-4">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Customer</label>
                    <select id="customer_id" name="customer_id" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white">
                        <option value="">Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                data-first-name="{{ $customer->first_name }}" 
                                data-last-name="{{ $customer->last_name }}"
                                data-company-name="{{ $customer->company_name }}"
                                data-search-text="{{ strtolower($customer->first_name . ' ' . $customer->last_name . ' ' . $customer->company_name) }}">
                                {{ !empty($customer->company_name) ? $customer->company_name : $customer->first_name . ' ' . $customer->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end mt-4">
                    <button type="button" onclick="closeAddCustomerModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md mr-2 hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="button" onclick="addCustomerToTable()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Add Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for tab switching -->
    <script>
        const SOA_CUSTOMERS_KEY = 'soa_customers';
        let addedCustomers = new Set();
        
        // Permission check for delete functionality
        const canDeleteSOA = @json(Auth::user()->hasSubpagePermission('masterlist', 'soa', 'delete'));
        
        function loadCustomersFromStorage() {
            // Load added customers from localStorage
            const storedCustomers = localStorage.getItem(SOA_CUSTOMERS_KEY);
            if (storedCustomers) {
                addedCustomers = new Set(JSON.parse(storedCustomers));
            }
            
            // Get the table body
            const tableBody = document.querySelector('#soaCustomerTable tbody');
            tableBody.innerHTML = '';
            
            if (addedCustomers.size === 0) {
                // If no customers, show a message
                tableBody.innerHTML = `
                    <tr id="noCustomersRow">
                        <td colspan="5" class="p-4 text-center">No customers added yet</td>
                    </tr>
                `;
                return;
            }
            
            // For each customer ID in the set
            addedCustomers.forEach(customerId => {
                // Find the corresponding option in the select
                const customerOption = document.querySelector(`option[value="${customerId}"]`);
                if (!customerOption) return;
                
                // Get customer data from the option's data attributes
                const firstName = customerOption.dataset.firstName || '';
                const lastName = customerOption.dataset.lastName || '';
                const companyName = customerOption.dataset.companyName || '';
                
                // Create a row for the customer
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-100 dark:hover:bg-gray-700';
                row.innerHTML = `
                    <td class="p-2">${customerId}</td>
                    <td class="p-2">${firstName}</td>
                    <td class="p-2">${lastName}</td>
                    <td class="p-2">${companyName}</td>
                    <td class="p-2 text-center">
                        <button onclick="viewSOA('${customerId}')" class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-700">
                            View SOA
                        </button>
                        ${canDeleteSOA ? `<button onclick="removeCustomerRow('${customerId}')" class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-700 ml-2">
                            Remove
                        </button>` : ''}
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }

        function openAddCustomerModal() {
            document.getElementById('addCustomerModal').classList.remove('hidden');
            document.getElementById('customerSearch').focus();
        }

        function closeAddCustomerModal() {
            document.getElementById('addCustomerModal').classList.add('hidden');
            document.getElementById('customerSearch').value = '';
            document.getElementById('customer_id').value = '';
            refreshCustomerOptions();
        }

        function refreshCustomerOptions() {
            const searchInput = document.getElementById('customerSearch');
            const selectElement = document.getElementById('customer_id');
            const searchTerm = searchInput.value.toLowerCase();
            
            Array.from(selectElement.options).forEach(option => {
                if (option.value === '') return; // Skip the placeholder option
                
                const searchText = option.getAttribute('data-search-text');
                if (searchText && searchText.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
        
        // Set up event listener for search input
        document.getElementById('customerSearch').addEventListener('keyup', refreshCustomerOptions);

        // Initialize the first tab on page load
        document.addEventListener('DOMContentLoaded', function() {
            showTab('customers', 'customersTab');
        });

        function showTab(tabId, tabButtonId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));

            // Remove active class from all tabs
            document.querySelectorAll('[id^="customersTab"], [id^="shipTab"]').forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-500', 'dark:text-blue-400', 'border-blue-400');
                tab.classList.add('text-gray-700', 'dark:text-gray-300');
            });

            // Show the selected tab content
            document.getElementById(tabId).classList.remove('hidden');
            
            // Add active class to the selected tab button
            document.getElementById(tabButtonId).classList.remove('text-gray-700', 'dark:text-gray-300');
            document.getElementById(tabButtonId).classList.add('border-blue-500', 'text-blue-500', 'dark:text-blue-400', 'border-blue-400');
        }
        
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
            } else {
                content.classList.add('hidden');
            }
        }

        function addCustomerToTable() {
            const selectElement = document.getElementById('customer_id');
            
            if (!selectElement.value) {
                alert('Please select a customer');
                return;
            }
            
            const customerId = selectElement.value;
            
            if (addedCustomers.has(customerId)) {
                alert('This customer is already in the table');
                closeAddCustomerModal();
                return;
            }
            
            addedCustomers.add(customerId);
            localStorage.setItem(SOA_CUSTOMERS_KEY, JSON.stringify([...addedCustomers]));
            
            loadCustomersFromStorage();
            closeAddCustomerModal();
        }

        function removeCustomerRow(customerId) {
            if (confirm('Are you sure you want to remove this customer from the table?')) {
                addedCustomers.delete(customerId);
                localStorage.setItem(SOA_CUSTOMERS_KEY, JSON.stringify([...addedCustomers]));
                loadCustomersFromStorage();
            }
        }

        function viewSOA(customerId) {
            window.location.href = "{{ route('masterlist.soa_list') }}?customer_id=" + customerId;
        }

        // Table search functionality
        document.getElementById('tableSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#soaCustomerTable tbody tr:not(#noCustomersRow)');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Initialize table on page load
        document.addEventListener('DOMContentLoaded', loadCustomersFromStorage);
    </script>
</x-app-layout>
