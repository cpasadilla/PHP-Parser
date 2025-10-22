<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Statement of Account for: ') . (!empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name) }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        @if($groupedOrders->isEmpty())
            <div class="text-center py-4">
                <p class="text-gray-600 dark:text-gray-400">No orders found for this customer.</p>
            </div>
        @else
            <!-- Ship Tabs -->
            <div class="tabs">
                <ul class="flex border-b">
                    @foreach($groupedOrders as $ship => $voyageGroups)
                        <li class="mr-1">
                            <a href="#tab-{{ $ship }}" class="tab-link inline-block py-2 px-4 text-blue-500 hover:text-blue-800 rounded-t-md">M/V Everwin Star {{ $ship }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Ship Content -->
            <div class="tab-content">
                @foreach($groupedOrders as $ship => $voyageGroups)
                    <div id="tab-{{ $ship }}" class="hidden">
                        <h3 class="text-lg font-semibold mt-8 mb-4 dark:text-gray-200">Ship: M/V Everwin Star {{ $ship }}</h3>
                        <div class="accordion">
                            @foreach($voyageGroups as $voyage => $orders)
                                @php
                                    $firstOrder = $orders->first();
                                    $origin = $firstOrder ? $firstOrder->origin : '';
                                    $destination = $firstOrder ? $firstOrder->destination : '';
                                @endphp
                                <div class="accordion-item border-b">
                                    <button class="accordion-header w-full text-left py-2 px-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-200" onclick="toggleAccordion('voyage-{{ $ship }}-{{ $voyage }}')">
                                        Voyage: {{ $voyage }} ({{ $origin }} to {{ $destination }})
                                    </button>
                                    <div id="voyage-{{ $ship }}-{{ $voyage }}" class="accordion-content hidden">
                                        <div class="flex justify-between items-center py-2">
                                            <div class="flex flex-col space-y-2 w-full">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('masterlist.soa_temp', [
                                                        'ship' => $ship, 
                                                        'voyage' => urlencode($voyage),
                                                        'customerId' => request('customer_id')
                                                        ]) }}" 
                                                        class="px-3 py-1 text-white bg-green-500 rounded hover:bg-green-700"
                                                        onclick="event.preventDefault(); openSOATemp('{{ $ship }}', '{{ urlencode($voyage) }}', '{{ request('customer_id') }}')">
                                                        Print Statement of Account
                                                    </a>
                                                    
                                                    <!-- Interest Calculation Button -->
                                                    <button id="interest-btn-{{ $ship }}-{{ Str::slug($voyage) }}"
                                                        class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-700"
                                                        onclick="activateInterest('{{ $ship }}', '{{ urlencode($voyage) }}', '{{ request('customer_id') }}', '{{ $ship }}-{{ Str::slug($voyage) }}')">
                                                        Activate 1% Interest
                                                    </button>
                                                    <div class="text-xs text-gray-600 italic mt-1">
                                                        Note: When the "Activate 1% Interest" button is clicked, the interest is NOT applied immediately. 1% interest only begins to apply after a 30-day grace period, and then it accrues at 1% per month.
                                                    </div>
                                                </div>
                                                
                                                <!-- Print SOA for Government - Per BL -->
                                                <div class="border-t pt-2">
                                                    <p class="text-sm font-semibold mb-2 dark:text-gray-200">Print SOA for Government (Per BL):</p>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($orders as $order)
                                                            <a href="{{ route('masterlist.soa_custom_per_bl', [
                                                                'ship' => $ship, 
                                                                'voyage' => urlencode($voyage),
                                                                'customerId' => request('customer_id'),
                                                                'orderId' => $order->id
                                                                ]) }}" 
                                                                class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-700 text-sm"
                                                                onclick="event.preventDefault(); openSOACustomPerBL('{{ $ship }}', '{{ urlencode($voyage) }}', '{{ request('customer_id') }}', '{{ $order->id }}')">
                                                                BL# {{ $order->orderId }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Interest Status Display -->
                                            <div id="interest-status-{{ $ship }}-{{ Str::slug($voyage) }}" class="text-red-600 font-bold hidden">
                                                1% Interest Active - <span class="days-counter">0</span> days since activation
                                                <button id="deactivate-interest-btn-{{ $ship }}-{{ Str::slug($voyage) }}"
                                                    class="ml-2 px-2 py-0.5 text-xs text-white bg-gray-500 rounded hover:bg-gray-700"
                                                    onclick="deactivateInterest('{{ $ship }}', '{{ urlencode($voyage) }}', '{{ request('customer_id') }}', '{{ $ship }}-{{ Str::slug($voyage) }}')">
                                                    Deactivate
                                                </button>
                                            </div>
                                        </div>
                                        <div class="overflow-x-auto mt-4">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-100 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-4 py-2">BL #</th>
                                                        <th class="px-4 py-2">Consignee</th>
                                                        <th class="px-4 py-2">Shipper</th>
                                                        <th class="px-4 py-2">Description</th>
                                                        <th class="px-4 py-2">Freight</th>
                                                        <th class="px-4 py-2">Valuation</th>
                                                        <th class="px-4 py-2">Wharfage</th>
                                                        <th class="px-4 py-2">Others Fee</th>
                                                        <th class="px-4 py-2">PPA Manila</th>
                                                        <th class="px-4 py-2">Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    @php 
                                                        $voyageTotal = 0;
                                                        $voyageFreight = 0;
                                                        $voyageValuation = 0;
                                                        $voyageWharfage = 0;
                                                        $voyagePadlockFee = 0;
                                                        $voyagePpaManila = 0;
                                                    @endphp
                                                    @foreach($orders as $order)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                            <td class="px-4 py-2 text-center">{{ $order->orderId }}</td>
                                                            <td class="px-4 py-2 text-center">{{ $order->recName }}</td>
                                                            <td class="px-4 py-2 text-center">{{ $order->shipperName }}</td>
                                                            <td class="px-4 py-2 text-center">
                                                                @foreach ($order->parcels as $parcel)
                                                                    <span>{{ $parcel->quantity }} {{ $parcel->unit }} {{ $parcel->itemName }} {{$parcel->desc}}</span><br>
                                                                @endforeach
                                                            </td>
                                                            <td class="px-4 py-2 text-right">{{ number_format($order->freight, 2) }}</td>
                                                            <td class="px-4 py-2 text-right">{{ number_format($order->valuation, 2) }}</td>
                                                            <td class="px-4 py-2 text-right">{{ number_format($order->wharfage ?? 0, 2) }}</td>
                                                            <td class="px-4 py-2 text-right">
                                                                <input type="number" 
                                                                    step="0.01" 
                                                                    min="0"
                                                                    style="width: 100px; border: none; outline: none; text-align:center;" 
                                                                    class="padlock-fee-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"
                                                                    data-order-id="{{ $order->id }}"
                                                                    value="{{ $order->padlock_fee ?? 0 }}"
                                                                    placeholder="Enter Padlock Fee"/>
                                                            </td>
                                                            <td class="px-4 py-2 text-right">
                                                                <input type="number" 
                                                                    step="0.01" 
                                                                    min="0"
                                                                    style="width: 100px; border: none; outline: none; text-align:center;" 
                                                                    class="ppa-manila-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"
                                                                    data-order-id="{{ $order->id }}"
                                                                    value="{{ $order->ppa_manila ?? 0 }}"
                                                                    placeholder="Enter PPA Manila"/>
                                                            </td>
                                                            <td class="px-4 py-2 text-right">{{ number_format(($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0)), 2) }}</td>
                                                        </tr>
                                                        @php $voyageTotal += ($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0));
                                                             $voyageFreight += $order->freight; 
                                                             $voyageValuation += $order->valuation;
                                                             $voyageWharfage += ($order->wharfage ?? 0);
                                                             $voyagePadlockFee += ($order->padlock_fee ?? 0);
                                                             $voyagePpaManila += ($order->ppa_manila ?? 0);
                                                        @endphp
                                                    @endforeach
                                                    <tr class="bg-gray-50 dark:bg-gray-900 font-semibold">
                                                        <td colspan="4" class="px-4 py-2 text-right">Grand Total:</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyageWharfage, 2) }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyagePadlockFee, 2) }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyagePpaManila, 2) }}</td>
                                                        <td class="px-4 py-2 text-right">{{ number_format($voyageTotal, 2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .tab-link.active {
            color: #1D4ED8;
            border-bottom: 2px solid #1D4ED8;
            background-color: #F3F4F6;
        }
        
        .dark .tab-link.active {
            color: #60A5FA;
            border-bottom: 2px solid #60A5FA;
            background-color: #374151;
        }

        .accordion-header {
            position: relative;
        }

        .accordion-header::after {
            content: '+';
            position: absolute;
            right: 1rem;
            transition: transform 0.2s ease-in-out;
        }

        .accordion-header.active::after {
            content: '-';
        }
    </style>

    <script>
        // Function to safely open the SOA temp page with special characters in voyage numbers
        function openSOATemp(ship, voyage, customerId) {
            // Create the URL with properly encoded parameters
            const baseUrl = "{{ url('/masterlist/soa_temp') }}";
            const url = `${baseUrl}/${ship}/${voyage}/${customerId}`;
            
            // Open in a new tab/window
            window.open(url, '_blank');
        }

        // Function to safely open the Custom SOA page with special characters in voyage numbers
        function openSOACustom(ship, voyage, customerId) {
            // Create the URL with properly encoded parameters
            const baseUrl = "{{ url('/masterlist/soa_custom') }}";
            const url = `${baseUrl}/${ship}/${voyage}/${customerId}`;
            
            // Open in a new tab/window
            window.open(url, '_blank');
        }

        // Function to open Custom SOA per BL
        function openSOACustomPerBL(ship, voyage, customerId, orderId) {
            // Create the URL with properly encoded parameters
            const baseUrl = "{{ url('/masterlist/soa_custom_per_bl') }}";
            const url = `${baseUrl}/${ship}/${voyage}/${customerId}/${orderId}`;
            
            // Open in a new tab/window
            window.open(url, '_blank');
        }

        // Function to check for previously activated interest on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Find all interest buttons and check if they were previously activated
            document.querySelectorAll('[id^="interest-btn-"]').forEach(button => {
                const uniqueId = button.id.replace('interest-btn-', '');
                
                // Check if interest has been activated for this voyage
                const storageKey = `interest_start_${uniqueId}`;
                const startDate = localStorage.getItem(storageKey);
                
                if (startDate) {
                    // Interest was previously activated - hide button, show status
                    button.style.display = 'none';
                    
                    const statusElement = document.getElementById(`interest-status-${uniqueId}`);
                    if (statusElement) {
                        statusElement.classList.remove('hidden');
                        
                        // Update the day counter
                        updateDayCounter(uniqueId);
                        
                        // Set interval to update the counter every hour
                        setInterval(() => updateDayCounter(uniqueId), 3600000);
                    }
                }
            });
        });

        // Function to activate 1% interest calculation
        function activateInterest(ship, voyage, customerId, uniqueId) {
            if (!confirm('Are you sure you want to activate 1% interest calculation?')) {
                return;
            }

            // Save the current date to localStorage first (to ensure UI still works if backend fails)
            const now = new Date().toISOString();
            const storageKey = `interest_start_${uniqueId}`;
            localStorage.setItem(storageKey, now);
            
            // Update UI immediately
            document.getElementById(`interest-btn-${uniqueId}`).style.display = 'none';
            const statusElement = document.getElementById(`interest-status-${uniqueId}`);
            statusElement.classList.remove('hidden');
            updateDayCounter(uniqueId);
            setInterval(() => updateDayCounter(uniqueId), 3600000);

            // Create the URL for the interest activation endpoint
            const baseUrl = "{{ url('/masterlist/activate-interest') }}";
            const url = `${baseUrl}/${ship}/${voyage}/${customerId}`;
            
            console.log('Sending request to:', url);
            
            // Make the POST request to activate interest
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Update stored start date with the one from server (more accurate)
                    const storageKey = `interest_start_${uniqueId}`;
                    localStorage.setItem(storageKey, data.start_date);
                    
                    // Update the counter with the precise server date
                    updateDayCounter(uniqueId);
                    
                    // Show success message
                    alert('Interest calculation activated successfully!');
                } else {
                    alert('Error: ' + data.message);
                    // The UI is already updated so we don't need to revert anything
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an issue communicating with the server, but interest calculation has been activated in the browser. The interest will be visible when you view the statement of account. Error: ' + error.message);
                // The UI is already updated, so we keep it functional despite the server error
            });
        }
        
        // Function to deactivate 1% interest calculation
        function deactivateInterest(ship, voyage, customerId, uniqueId) {
            if (!confirm('Are you sure you want to deactivate 1% interest calculation?')) {
                return;
            }
            
            // Create the URL for the interest deactivation endpoint
            const baseUrl = "{{ url('/masterlist/deactivate-interest') }}";
            const url = `${baseUrl}/${ship}/${voyage}/${customerId}`;
            
            console.log('Sending deactivation request to:', url);
            
            // Make the POST request to deactivate interest
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Remove the stored date
                    const storageKey = `interest_start_${uniqueId}`;
                    localStorage.removeItem(storageKey);
                    
                    // Update UI - show activate button, hide status
                    document.getElementById(`interest-btn-${uniqueId}`).style.display = 'inline-block';
                    document.getElementById(`interest-status-${uniqueId}`).classList.add('hidden');
                    
                    // Show success message
                    alert('Interest calculation deactivated successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an issue communicating with the server: ' + error.message);
            });
        }
        
        // Function to update the day counter
        function updateDayCounter(uniqueId) {
            const storageKey = `interest_start_${uniqueId}`;
            const startDateStr = localStorage.getItem(storageKey);
            
            console.log(`Updating counter for ${uniqueId}, start date: ${startDateStr}`);
            
            if (startDateStr) {
                const startDate = new Date(startDateStr);
                const currentDate = new Date();
                
                // Calculate days difference
                const diffTime = Math.abs(currentDate - startDate);
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                
                console.log(`Days since activation: ${diffDays}`);
                
                // Update the counter display
                const counterElement = document.querySelector(`#interest-status-${uniqueId} .days-counter`);
                if (counterElement) {
                    counterElement.textContent = diffDays;
                    console.log(`Counter element updated for ${uniqueId}`);
                } else {
                    console.warn(`Counter element not found for ${uniqueId}`);
                }
                
                // Show the status display if it's hidden
                document.getElementById(`interest-status-${uniqueId}`).classList.remove('hidden');
                
                // Hide the activate button and show the deactivate button
                document.getElementById(`interest-btn-${uniqueId}`).style.display = 'none';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content > div');

            // Show first tab by default
            if (tabLinks.length > 0) {
                tabLinks[0].classList.add('active');
                tabContents[0].classList.remove('hidden');
            }

            tabLinks.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    // Remove active class from all tabs and hide content
                    tabLinks.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.add('hidden'));
                    
                    // Add active class to clicked tab and show content
                    tab.classList.add('active');
                    const content = document.querySelector(tab.getAttribute('href'));
                    content.classList.remove('hidden');
                });
            });

            // Accordion functionality
            window.toggleAccordion = function(id) {
                const content = document.getElementById(id);
                const header = content.previousElementSibling;
                const isExpanded = !content.classList.contains('hidden');
                
                // Close all accordion items in the same ship tab
                const shipTab = content.closest('.tab-content > div');
                shipTab.querySelectorAll('.accordion-content').forEach(c => {
                    c.classList.add('hidden');
                    c.previousElementSibling.classList.remove('active');
                });
                
                // Toggle the clicked accordion item
                if (!isExpanded) {
                    content.classList.remove('hidden');
                    header.classList.add('active');
                }
            };
        });
    </script>

    <script>
        // Check for interest activations when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Checking for previously activated interest');
            
            // Collect all interest buttons
            const interestButtons = document.querySelectorAll('[id^="interest-btn-"]');
            console.log(`Found ${interestButtons.length} interest buttons`);
            
            // Check each button for previous activation
            interestButtons.forEach(button => {
                const uniqueId = button.id.replace('interest-btn-', '');
                const storageKey = `interest_start_${uniqueId}`;
                const startDate = localStorage.getItem(storageKey);
                
                console.log(`Checking button ${uniqueId}: ${startDate ? 'Activated' : 'Not activated'}`);
                
                if (startDate) {
                    // Interest was previously activated
                    button.style.display = 'none';
                    
                    const statusElement = document.getElementById(`interest-status-${uniqueId}`);
                    if (statusElement) {
                        statusElement.classList.remove('hidden');
                        
                        // Update the day counter
                        updateDayCounter(uniqueId);
                        
                        // Set interval to update the counter every hour
                        setInterval(() => updateDayCounter(uniqueId), 3600000);
                    }
                }
            });
        });
    </script>

    <script>
        // For handling padlock fee inputs
        document.addEventListener('DOMContentLoaded', function () {
            const padlockFeeInputs = document.querySelectorAll('.padlock-fee-input');
            const ppaManilaInputs = document.querySelectorAll('.ppa-manila-input');

            padlockFeeInputs.forEach(input => {
                input.addEventListener('input', function () {
                    const orderId = this.getAttribute('data-order-id');
                    const value = parseFloat(this.value) || 0;
                    
                    // Update the order field via AJAX
                    fetch(`/update-order-field/${orderId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            field: 'padlock_fee',
                            value: value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Padlock fee updated successfully');
                            updateRowTotal(orderId);
                        } else {
                            console.error('Failed to update padlock fee:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            ppaManilaInputs.forEach(input => {
                input.addEventListener('input', function () {
                    const orderId = this.getAttribute('data-order-id');
                    const value = parseFloat(this.value) || 0;
                    
                    // Update the order field via AJAX
                    fetch(`/update-order-field/${orderId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            field: 'ppa_manila',
                            value: value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('PPA Manila updated successfully');
                            updateRowTotal(orderId);
                        } else {
                            console.error('Failed to update PPA Manila:', data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            function updateRowTotal(orderId) {
                const row = document.querySelector(`[data-order-id="${orderId}"]`).closest('tr');
                const freight = parseFloat(row.cells[4].textContent.replace(/,/g, '')) || 0;
                const valuation = parseFloat(row.cells[5].textContent.replace(/,/g, '')) || 0;
                const wharfage = parseFloat(row.cells[6].textContent.replace(/,/g, '')) || 0;
                const padlockFee = parseFloat(row.querySelector('.padlock-fee-input').value) || 0;
                const ppaManila = parseFloat(row.querySelector('.ppa-manila-input').value) || 0;
                
                const newTotal = freight + valuation + wharfage + padlockFee + ppaManila;
                const totalCell = row.cells[9]; // Updated to the new position
                totalCell.textContent = new Intl.NumberFormat('en-US', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                }).format(newTotal);
            }
        });
    </script>

    <!-- Add a link to the Reset Interest page at the bottom of the SOA List page -->
    <div class="w-full text-center my-6" hidden>
        <a href="{{ route('masterlist.reset_interest') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            Reset Interest Activation
        </a>
    </div>
</x-app-layout>