<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <!--button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button-->
                {{ __('Container Reservation') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        @if(Auth::user()->hasSubpagePermission('masterlist', 'container-details', 'access'))
        <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Reserve Containers</h3>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        
        <form method="POST" action="{{ route('masterlist.reserve-container') }}" id="reserveContainerForm">
            @csrf
            <!-- Show debug info for CSRF token -->
            <div class="mb-4 p-2 bg-gray-100 dark:bg-gray-700 rounded text-xs" hidden>
                <p>Session ID: {{ session()->getId() }}</p>
                <p>CSRF Token: {{ csrf_token() }}</p>
            </div>
            <!-- Ship, Voyage, Origin, Destination in one row -->
            <div class="grid grid-cols-4 gap-4 mb-4">
                <div class="col-span-1">
                    <label for="ship" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ship</label>
                    <select id="ship" name="ship" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white">
                        @foreach ($ships as $ship)
                            <option value="{{ $ship->ship_number }}">M/V Everwin Star {{ $ship->ship_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-1">
                    <label for="voyage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Voyage</label>
                    <input type="text" id="voyage" name="voyage" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white" required>
                </div>

                <div class="col-span-1">
                    <label for="origin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Origin</label>
                    <select id="origin" name="origin" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white" required>
                        <option value="">Select Origin</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->location }}">{{ $location->location }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-1">
                    <label for="destination" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Destination</label>
                    <select id="destination" name="destination" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white" required>
                        <option value="">Select Destination</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->location }}">{{ $location->location }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <button type="button" onclick="viewContainerDetails()" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    View Container Details
                </button>
            </div>


            @if(Auth::user()->hasSubpagePermission('masterlist', 'container', 'create'))
            <!-- Container creation form fields -->
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="col-span-1">
                    <label for="container_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Container Size</label>
                    <select id="container_type" name="container_type" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white">
                        <option value="">Select Container Size</option>
                        <option value="20">20-Footer</option>
                        <option value="10">10-Footer</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                    <select id="customer_id" name="customer_id" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white">
                        <option value=""> </option>
                        @foreach ($customers->sortBy(function($customer) {
                            return $customer->first_name && $customer->last_name ? 
                                   $customer->first_name . ' ' . $customer->last_name : 
                                   $customer->company_name;
                        }) as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->first_name && $customer->last_name ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-1">
                    <label for="container_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Container Number <span class="text-xs text-gray-500">(optional)</span>
                    </label>
                    <input type="text" id="container_name" name="container_name" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <div hidden>
                <label for="container_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Containers</label>
                <input type="number" id="container_quantity" name="container_quantity" value="1" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white" min="1" readonly>
            </div>

            <div class="mt-4">
                <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">Reserve</button>
            </div>
            @else
            <!--div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 dark:bg-yellow-900/30 dark:border-yellow-800 dark:text-yellow-200">
                <p>You need create permission to reserve containers.</p>
            </div-->
            @endif
        </form>
        @else
        <!--div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 dark:bg-yellow-900/30 dark:border-yellow-800 dark:text-yellow-200">
            <p>You need access permission to view container details.</p>
        </div-->
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('reserveContainerForm');
                if (form) {
                    console.log('CSRF token present:', form.querySelector('input[name="_token"]') ? 'Yes' : 'No');
                }
            });
        </script>

        <h3 class="text-lg font-semibold mt-8 mb-4 dark:text-gray-200">Reserved Containers</h3>

        <div class="tabs">
            <ul class="flex border-b">
                @foreach ($reservations as $ship => $voyages)
                    <li class="mr-1">
                        <a href="#tab-{{ $ship }}" class="tab-link inline-block py-2 px-4 text-blue-500 hover:text-blue-800 rounded-t-md">M/V EVERWIN STAR {{ $ship }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content">
            @foreach ($reservations as $ship => $voyages)
                <div id="tab-{{ $ship }}" class="hidden">
                    <h3 class="text-lg font-semibold mt-8 mb-4 dark:text-gray-200">Ship: {{ $ship }}</h3>
                    <div class="accordion">
                        @foreach ($voyages as $voyage => $containers)
                            @php
                                // Get the origin and destination from the first container in this voyage
                                // (assuming all containers in a voyage have the same origin and destination)
                                $origin = count($containers) > 0 ? $containers[0]->origin : '';
                                $destination = count($containers) > 0 ? $containers[0]->destination : '';
                            @endphp
                            <div class="accordion-item border-b">
                                <button class="accordion-header w-full text-left py-2 px-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 dark:text-gray-200" onclick="toggleAccordion('voyage-{{ $ship }}-{{ $voyage }}')">
                                    Voyage: {{ $voyage }} ({{ $origin }} to {{ $destination }})
                                </button>
                                <div id="voyage-{{ $ship }}-{{ $voyage }}" class="accordion-content hidden">
                                    <table id="table-{{ $ship }}-{{ $voyage }}" class="w-full border-collapse mt-4">
                                        <thead class="bg-gray-200 dark:bg-dark-eval-0">
                                            <tr>
                                                <th class="p-2 text-gray-700 dark:text-white">Size</th>
                                                <th class="p-2 text-gray-700 dark:text-white">Container Number</th>
                                                <th class="p-2 text-gray-700 dark:text-white" hidden>Quantity</th>
                                                <th class="p-2 text-gray-700 dark:text-white">Customer</th>
                                                <th class="p-2 text-gray-700 dark:text-white">Origin</th>
                                                <th class="p-2 text-gray-700 dark:text-white">Destination</th>
                                                @if(Auth::user()->hasSubpagePermission('masterlist', 'container', 'edit') || Auth::user()->hasSubpagePermission('masterlist', 'container', 'delete'))
                                                <th class="p-2 text-gray-700 dark:text-white">Actions</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($containers as $reservation)
                                                <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <td class="p-2 text-center dark:text-gray-200">{{ $reservation->type }}</td>
                                                    <td class="p-2 text-center dark:text-gray-200">{{ $reservation->containerName }}</td>
                                                    <td class="p-2 text-center dark:text-gray-200" hidden>{{ $reservation->quantity }}</td>
                                                    <td class="p-2 text-center dark:text-gray-200">
                                                        @if ($reservation->customer)
                                                            {{ $reservation->customer->first_name && $reservation->customer->last_name ? $reservation->customer->first_name . ' ' . $reservation->customer->last_name : $reservation->customer->company_name }}
                                                        @else
                                                             
                                                        @endif
                                                    </td>
                                                    <td class="p-2 text-center dark:text-gray-200">{{ $reservation->origin }}</td>
                                                    <td class="p-2 text-center dark:text-gray-200">{{ $reservation->destination }}</td>
                                                    @if(Auth::user()->hasSubpagePermission('masterlist', 'container', 'edit') || Auth::user()->hasSubpagePermission('masterlist', 'container', 'delete'))
                                                    <td class="p-2 text-center">
                                                        <div class="flex justify-center space-x-2">
                                                            @if(Auth::user()->hasSubpagePermission('masterlist', 'container', 'delete'))
                                                            <form method="POST" action="{{ route('masterlist.delete-reservation', $reservation->id) }}" onsubmit="return confirm('Are you sure you want to delete this reservation?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="px-3 py-1 text-white bg-red-500 rounded-md hover:bg-red-600">Delete</button>
                                                            </form>
                                                            @endif
                                                            @if(Auth::user()->hasSubpagePermission('masterlist', 'container', 'edit'))
                                                            <button onclick="openEditModal({{ json_encode($reservation) }})" class="px-3 py-1 text-white bg-blue-500 rounded-md hover:bg-blue-600">Edit</button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mt-4">
                                        <button onclick="exportToExcel('{{ $ship }}', '{{ $voyage }}')" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">Export to Excel</button>
                                        <button onclick="exportToPDF('{{ $ship }}', '{{ $voyage }}')" class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">Export to PDF</button>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- SheetJS library for Excel export -->
        <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
        <!-- jsPDF library and plugins for PDF export -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
        
        <script>
            function toggleAccordion(id) {
                const content = document.getElementById(id);
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            }

            function exportToExcel(ship, voyage) {
                const tableId = `table-${ship}-${voyage}`;
                const table = document.getElementById(tableId);
                
                if (!table) {
                    alert('Table not found');
                    return;
                }
                
                // Get table headers (excluding the Actions column and Quantity column)
                const headers = [];
                const headerCells = table.querySelectorAll('thead th');
                // Use all header cells except the Actions column (last) and Quantity column (index 2)
                for (let i = 0; i < headerCells.length - 1; i++) {
                    if (i !== 2) { // Skip Quantity column (index 2)
                        headers.push(headerCells[i].innerText.trim());
                    }
                }
                
                // Get table data (excluding the Actions column and Quantity column)
                const rows = [];
                const dataCells = table.querySelectorAll('tbody tr');
                dataCells.forEach(row => {
                    const rowData = [];
                    const cells = row.querySelectorAll('td');
                    // Use all cells except the Actions column (last) and Quantity column (index 2)
                    for (let i = 0; i < cells.length - 1; i++) {
                        if (i !== 2) { // Skip Quantity column (index 2)
                            rowData.push(cells[i].innerText.trim());
                        }
                    }
                    rows.push(rowData);
                });
                
                // Create worksheet
                const worksheet = XLSX.utils.aoa_to_sheet([headers, ...rows]);
                
                // Create workbook
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, `${ship}-${voyage}`);
                
                // Generate Excel file
                const fileName = `MV Everwin Star ${ship} Voyage ${voyage}.xlsx`;
                XLSX.writeFile(workbook, fileName);
            }

            function exportToPDF(ship, voyage) {
                const tableId = `table-${ship}-${voyage}`;
                const table = document.getElementById(tableId);
                
                if (!table) {
                    alert('Table not found');
                    return;
                }
                
                // Get table headers (excluding the Actions column, Type column, and Quantity column)
                const headers = [];
                const headerCells = table.querySelectorAll('thead th');
                // Use all header cells except the Actions column (last), Type column (first), and Quantity column (index 2)
                for (let i = 1; i < headerCells.length - 1; i++) {
                    if (i !== 2) { // Skip Quantity column (index 2)
                        headers.push(headerCells[i].innerText.trim());
                    }
                }
                
                // Get table data and separate by type
                const rows10Footer = [];
                const rows20Footer = [];
                const dataCells = table.querySelectorAll('tbody tr');
                
                dataCells.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const type = cells[0].innerText.trim();
                    
                    // Create row data without the type, quantity, and action columns
                    const rowData = [];
                    for (let i = 1; i < cells.length - 1; i++) {
                        if (i !== 2) { // Skip Quantity column (index 2)
                            rowData.push(cells[i].innerText.trim());
                        }
                    }
                    
                    // Separate rows by container type
                    if (type === '10') {
                        rows10Footer.push(rowData);
                    } else if (type === '20') {
                        rows20Footer.push(rowData);
                    }
                });
                
                // Create PDF
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                // Add title to PDF
                doc.setFontSize(16);
                doc.text(`M/V Everwin Star ${ship} Voyage ${voyage}`, 14, 15);

                let currentY = 25;
                
                // Add 20-Footer container table
                if (rows20Footer.length > 0) {
                    doc.setFontSize(14);
                    doc.text('20-Footer Containers', 14, currentY);
                    currentY += 3; // Reduced spacing from 5 to 3
                    
                    doc.autoTable({
                        head: [headers],
                        body: rows20Footer,
                        startY: currentY,
                        theme: 'grid',
                        styles: {
                            fontSize: 12,
                            textColor: [0, 0, 0] // Black text for table body
                        },
                        headStyles: {
                            fillColor: [76, 175, 80], // Green header for 10-footer
                            textColor: [255, 255, 255], // White text for header
                            fontStyle: 'bold'
                        }
                    });
                    
                    currentY = doc.lastAutoTable.finalY + 10;
                }
                
                // Add 10-Footer container table
                if (rows10Footer.length > 0) {
                    doc.setFontSize(14);
                    doc.text('10-Footer Containers', 14, currentY);
                    currentY += 3; // Reduced spacing from 5 to 3
                    
                    doc.autoTable({
                        head: [headers],
                        body: rows10Footer,
                        startY: currentY,
                        theme: 'grid',
                        styles: {
                            fontSize: 12,
                            textColor: [0, 0, 0] // Black text for table body
                        },
                        headStyles: {
                            fillColor: [76, 175, 80], // Green header for 10-footer
                            textColor: [255, 255, 255], // White text for header
                            fontStyle: 'bold'
                        }
                    });
                }
                
                // Generate PDF file
                const fileName = `MV Everwin Star ${ship} Voyage ${voyage}.pdf`;
                doc.save(fileName);
            }

            document.addEventListener('DOMContentLoaded', function () {
                const shipTabs = document.querySelectorAll('.tabs a');
                const shipTabContents = document.querySelectorAll('.tab-content > div');

                shipTabs.forEach(tab => {
                    tab.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Hide all ship tab contents and remove active class from all ship tabs
                        shipTabs.forEach(t => {
                            t.classList.remove('text-blue-800');
                            t.classList.remove('shadow-md');
                            t.classList.remove('border-b-0');
                            t.classList.remove('bg-blue-100');
                            t.classList.add('bg-white');
                        });
                        shipTabContents.forEach(tc => tc.classList.add('hidden'));

                        // Show the selected ship tab content and mark the tab as active
                        this.classList.add('text-blue-800');
                        this.classList.add('shadow-md');
                        this.classList.add('border-b-0');
                        this.classList.add('bg-blue-100');
                        this.classList.remove('bg-white');
                        const targetContent = document.querySelector(this.getAttribute('href'));
                        if (targetContent) {
                            targetContent.classList.remove('hidden');
                        }
                    });
                });

                // Activate the first ship tab by default
                if (shipTabs.length > 0) {
                    shipTabs[0].classList.add('text-blue-800', 'shadow-md', 'border-b-0', 'bg-blue-100');
                    shipTabs[0].classList.remove('bg-white');
                    shipTabContents[0].classList.remove('hidden');
                }
            });
        </script>
    </div>

    <div id="editModal" class="hidden fixed z-10 inset-0 overflow-y-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Edit Reservation</h3>
                <form method="POST" action="{{ route('masterlist.update-reservation') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editReservationId" name="reservation_id">
                    <div class="mb-4">
                        <label for="editType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Size</label>
                        <select id="editType" name="type" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Container Size</option>
                            <option value="20">20-Footer</option>
                            <option value="10">10-Footer</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="editContainerName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Container Number</label>
                        <input type="text" id="editContainerName" name="containerName" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label for="editCustomerId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                        <select id="editCustomerId" name="customer_id" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white">
                            <option value=""> </option>
                            @foreach ($customers->sortBy(function($customer) {
                                return $customer->first_name && $customer->last_name ? 
                                       $customer->first_name . ' ' . $customer->last_name : 
                                       $customer->company_name;
                            }) as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->first_name && $customer->last_name ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md mr-2 hover:bg-gray-600" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openEditModal(reservation) {
            document.getElementById('editReservationId').value = reservation.id;
            document.getElementById('editType').value = reservation.type;
            document.getElementById('editContainerName').value = reservation.containerName;
            document.getElementById('editCustomerId').value = reservation.customer_id || '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function viewContainerDetails() {
            const ship = document.getElementById('ship').value;
            const voyage = document.getElementById('voyage').value;
            
            if (!ship || !voyage) {
                alert('Please select a ship and enter a voyage number');
                return;
            }
            
            window.location.href = `{{ route('masterlist.container-details') }}?ship=${encodeURIComponent(ship)}&voyage=${encodeURIComponent(voyage)}`;
        }
    </script>
</x-app-layout>
