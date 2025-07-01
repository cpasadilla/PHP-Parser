<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                {{ __('Dashboard') }}
            </h2>
            @if(auth()->user()->hasPermission('customer', 'create') || (auth()->user()->roles && in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])))
            <a href="{{ route('customer.bl') }}" class="text-blue-500">
                <x-button variant="primary" class="items-center max-w-xs gap-2">
                    <x-heroicon-o-shopping-cart class="w-6 h-6" aria-hidden="true" /> Create Order
                </x-button>
            </a>
            @endif
        </div>
    </x-slot>

    <style>
        .tab-link.active {
            color: #3B82F6;
            border-bottom-color: #3B82F6;
            border-bottom-width: 2px;
            font-weight: 600;
        }
        
        .dark .tab-link.active {
            color: #60A5FA;
            border-bottom-color: #60A5FA;
        }
        
        .subtab-link.active {
            color: #3B82F6;
            border-bottom-color: #3B82F6;
            border-bottom-width: 2px;
            font-weight: 600;
        }
        
        .dark .subtab-link.active {
            color: #60A5FA;
            border-bottom-color: #60A5FA;
        }
        
        .subtab-link {
            transition: all 0.2s ease;
            font-weight: 500;
            position: relative;
        }
        
        .subtab-link:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #3B82F6;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .subtab-link:hover:not(.active):after {
            width: 60%;
        }
        
        .ship-tab.active {
            background-color: #3B82F6;
            color: white;
            border-color: #2563EB;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
            transform: translateY(-1px);
        }
        
        .dark .ship-tab.active {
            background-color: #3B82F6;
            color: white;
            border-color: #2563EB;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.5);
        }
        
        .ship-tab {
            transition: all 0.2s ease;
            font-weight: 500;
            border-width: 1px;
        }
        
        .ship-tab:hover:not(.active) {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .tab-link {
            transition: all 0.2s ease;
            font-weight: 500;
            position: relative;
        }
        
        .tab-link:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #3B82F6;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .tab-link:hover:not(.active):after {
            width: 70%;
        }
        
        .chart-container {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }
        
        .dashboard-card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .dashboard-card:hover {
            box-shadow: 0 6px 24px -6px rgba(0, 0, 0, 0.12);
        }
        
        .dark .dashboard-card {
            border: 1px solid rgba(55, 65, 81, 0.5);
        }
        
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background-image: linear-gradient(to right, rgba(249, 250, 251, 0.8), rgba(243, 244, 246, 0.8));
        }
        
        .dark .card-header {
            border-bottom: 1px solid #374151;
            background-image: linear-gradient(to right, rgba(31, 41, 55, 0.8), rgba(17, 24, 39, 0.8));
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .branch-card {
            transition: transform 0.2s ease;
        }
        
        .branch-card:hover {
            transform: translateY(-3px);
        }
        
        .contact-item {
            transition: all 0.2s ease;
        }
        
        .contact-item:hover {
            transform: translateX(3px);
            background-color: #f9fafb;
        }
        
        .dark .contact-item:hover {
            background-color: #1f2937;
        }
        
        .ship-data {
            animation: fadeIn 0.4s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .bl-distribution-card {
            transition: all 0.3s ease;
        }
        
        .bl-distribution-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.1);
        }
    </style>

    <!-- Individual Graph Sections for Each Ship -->
    <div class="p-6" style="{{ auth()->user()->hasSubpagePermission('dashboard', 'ship-graphs') || (auth()->user()->roles && in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) ? '' : 'display: none;' }}">
        <!-- Ship Tabs -->
        <div class="bg-white dark:bg-dark-eval-1 rounded-lg shadow-md p-4 mb-6">
            <h2 class="text-xl font-bold mb-4 text-center text-gray-800 dark:text-gray-200">Select Ship</h2>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach($ships as $ship)
                <button id="shipTab{{ $ship->ship_number }}" 
                        class="ship-tab px-4 py-2 rounded-md border {{ $loop->first ? 'active' : '' }} hover:bg-gray-100 dark:hover:bg-gray-700"
                        onclick="showShipData('{{ $ship->ship_number }}')">
                    M/V Everwin Star {{ $ship->ship_number }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Individual Graph Sections for Each Ship -->
        @foreach($ships as $ship)
        <div id="shipData{{ $ship->ship_number }}" class="ship-data {{ $loop->first ? '' : 'hidden' }}">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">M/V Everwin Star {{ $ship->ship_number }}</h2>
            <div class="bg-white dark:bg-dark-eval-1 rounded-lg shadow-md p-6">
                <!-- Tab Navigation -->
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <a href="#" class="tab-link inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 active" 
                               data-target="blStatusPanel{{ $ship->ship_number }}" 
                               onclick="switchTab(event, 'blStatusPanel{{ $ship->ship_number }}', 'earningsPanel{{ $ship->ship_number }}')">
                                BL Status Per Voyage
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="#" class="tab-link inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                               data-target="earningsPanel{{ $ship->ship_number }}" 
                               onclick="switchTab(event, 'earningsPanel{{ $ship->ship_number }}', 'blStatusPanel{{ $ship->ship_number }}')">
                                Earnings Per Voyage
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Tab Content -->
                <div id="blStatusPanel{{ $ship->ship_number }}" class="tab-panel">
                    <h2 class="text-lg font-semibold text-center text-gray-800 dark:text-gray-200">BL Status Per Voyage</h2>
                    <div class="flex justify-center">
                        <canvas id="blStatusChart{{ $ship->ship_number }}" class="mt-4" style="max-width: 700px; height: 100px;"></canvas>
                    </div>
                </div>
                
                <div id="earningsPanel{{ $ship->ship_number }}" class="tab-panel hidden">
                    <h2 class="text-lg font-semibold text-center text-gray-800 dark:text-gray-200">Earnings Per Voyage</h2>
                    
                    @if($ship->ship_number == 'I' || $ship->ship_number == 'II')
                    <!-- For Ship I and II, show separate IN/OUT tabs -->
                    <div class="mb-4 mt-4 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                            <li class="mr-2">
                                <a href="#" class="subtab-link inline-block p-3 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 active" 
                                   data-target="inVoyagesPanel{{ $ship->ship_number }}" 
                                   onclick="switchSubTab(event, 'inVoyagesPanel{{ $ship->ship_number }}', 'outVoyagesPanel{{ $ship->ship_number }}')">
                                    Inbound Voyages (-IN)
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="#" class="subtab-link inline-block p-3 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                   data-target="outVoyagesPanel{{ $ship->ship_number }}" 
                                   onclick="switchSubTab(event, 'outVoyagesPanel{{ $ship->ship_number }}', 'inVoyagesPanel{{ $ship->ship_number }}')">
                                    Outbound Voyages (-OUT)
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Separate panels for IN and OUT voyages -->
                    <div id="inVoyagesPanel{{ $ship->ship_number }}" class="subtab-panel">
                        <div class="flex justify-center">
                            <canvas id="earningsChartIN{{ $ship->ship_number }}" class="mt-2" style="max-width: 700px; height: 100px;"></canvas>
                        </div>
                    </div>
                    
                    <div id="outVoyagesPanel{{ $ship->ship_number }}" class="subtab-panel hidden">
                        <div class="flex justify-center">
                            <canvas id="earningsChartOUT{{ $ship->ship_number }}" class="mt-2" style="max-width: 700px; height: 100px;"></canvas>
                        </div>
                    </div>
                    @else
                    <!-- For other ships, show the regular chart -->
                    <div class="flex justify-center">
                        <canvas id="earningsChart{{ $ship->ship_number }}" class="mt-4" style="max-width: 700px; height: 100px;"></canvas>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- All Ships Earnings Section -->
    <div class="p-6" hidden>
        <div class="bg-white dark:bg-dark-eval-1 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-center text-gray-800 dark:text-gray-200">All Ships - Earnings Per Voyage</h2>
            <div class="flex justify-center">
                <canvas id="allShipsEarningsChart" style="max-width: 1000px; height: 350px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie Chart Section -->
    <div class="p-6" style="{{ auth()->user()->hasSubpagePermission('dashboard', 'pie-charts') || (auth()->user()->roles && in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) ? '' : 'display: none;' }}">
        <div class="bg-white dark:bg-dark-eval-1 rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-center text-gray-800 dark:text-gray-200">Number of BL Per Ship</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                @foreach($ships as $ship)
                <div class="flex flex-col items-center">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200">M/V Everwin Star {{ $ship->ship_number }}</h3>
                    <canvas id="blDistributionChart{{ $loop->index }}" style="max-width: 250px; max-height: 250px;"></canvas>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- About and Branches Section -->
    <div class="p-6">
        <div class="bg-white dark:bg-dark-eval-1 rounded-lg shadow-md p-6">
            <!-- Logo Section -->
            <div class="row justify-content-center mb-6">
                <div class="col-lg-12 col-12 min-vh-500">
                    <div class="box">
                        <div class="col-sm-12 text-center">
                            <img src="{{ asset('images/logo.png') }}" class="mx-auto w-32"> <!-- Logo -->
                            <h1 class="text-lg font-bold mt-2 text-gray-800 dark:text-gray-200">ST. FRANCIS XAVIER STAR SHIPPING LINES INC.</h1>
                            <img src="{{ asset('images/line_5.png') }}" class="mx-auto w-full mt-2"> <!-- Decorative Line -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <!-- About Section -->
                <div class="bg-gray-100 dark:bg-dark-eval-2 rounded-lg p-6 shadow-md">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 text-center">ABOUT US</h2>
                    <div class="mt-6 space-y-6">
                        <!-- Vision Section -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 text-center">VISION</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-400 text-center">
                                It is our goal to be the best logistic solution provider in the Philippines.
                            </p>
                        </div>
                        <!-- Mission Section -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 text-center">MISSION</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-400 text-center">
                                To provide efficient shipping service through safe and effective transportation while fulfilling our promise of sustainable financial return to all our shareholders.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Branches Section -->
                <div class="space-y-8">
                    <!-- Tondo Branch -->
                    <div class="bg-gray-100 dark:bg-dark-eval-2 rounded-lg p-4 shadow-md">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center">TONDO, MANILA BRANCH</h2>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_location_icon_ek1.png') }}" class="w-5">
                                <span class="text-gray-600 dark:text-gray-400">Address: Pier 18 Vitas Tondo, Manila</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_phone_icon_ek1.png') }}" class="w-5">
                                <span class="text-gray-600 dark:text-gray-400">Contact: +63-999-889-5848 | +63-908-815-9300</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_email_icon_ek1.png') }}" class="w-5">
                                <a href="mailto:fxavier_2015@yahoo.com.ph" class="text-blue-500 hover:underline">fxavier_2015@yahoo.com.ph</a>
                            </div>
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_facebook_icon_ek1.png') }}" class="w-5">
                                <span class="text-gray-600 dark:text-gray-400">Facebook: St. Francis Xavier Star Shipping Lines Inc.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Basco Branch -->
                    <div class="bg-gray-100 dark:bg-dark-eval-2 rounded-lg p-4 shadow-md">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center">BASCO, BATANES BRANCH</h2>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_location_icon_ek1.png') }}" class="w-5">
                                <span class="text-gray-600 dark:text-gray-400">Address: National Rd. Brgy. Kaychanaryanan, Basco, Batanes</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_phone_icon_ek1.png') }}" class="w-5">
                                <span class="text-gray-600 dark:text-gray-400">Contact: +63-999-889-5849 | +63-999-889-5851</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_email_icon_ek1.png') }}" class="w-5">
                                <a href="mailto:stfrancisbasco@gmail.com" class="text-blue-500 hover:underline">stfrancisbasco@gmail.com</a>
                            </div>
                            <div class="flex items-center space-x-2">
                                <img src="{{ asset('images/tondo_facebook_icon.png') }}" class="w-5">
                                <span class="text-gray-600 dark:text-gray-400">Facebook: Sfxssli Batanes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Convert PHP data to JavaScript 
    const ships = @json($ships);
    const shipNumbers = @json($shipNumbers);
    const blStatusData = @json($blStatusData);
    const earningsData = @json($earningsData);
    const blCountData = @json($blCountData);
    const voyageMap = @json($voyageMap);
    const totalBlPerShip = @json($totalBlPerShip);

    // Debug info to console
    console.log('Ships:', ships);
    console.log('BL Status Data:', blStatusData);
    console.log('Voyage Map:', voyageMap);
    console.log('BL Count Data:', blCountData);
    console.log('Total BL Per Ship:', totalBlPerShip);

    // Filter voyage labels based on ship number
    function filterVoyagesForShip(ship) {
        // Get only voyages that belong to this specific ship
        const shipSpecificVoyages = voyageMap[ship] ? 
            voyageMap[ship].map(v => v.v_num) : 
            [];
        
        // For Ships I and II, only show voyages with -IN or -OUT suffixes
        if (ship === 'I' || ship === 'II') {
            return shipSpecificVoyages.filter(voyage => 
                voyage.includes('-IN') || voyage.includes('-OUT')
            );
        } 
        // For Ships III, IV, and V, only show voyages without -IN or -OUT suffixes
        else if (ship === 'III' || ship === 'IV' || ship === 'V') {
            return shipSpecificVoyages.filter(voyage => 
                !voyage.includes('-IN') && !voyage.includes('-OUT')
            );
        }
        
        return shipSpecificVoyages;
    }

    // BL Status Chart - Group data by ship for the bar chart
    const shipNames = Object.keys(blStatusData).length > 0 ? Object.keys(blStatusData) : [];
    const voyageLabels = new Set();
    const paidBLData = {};
    const unpaidBLData = {};

    // Initialize data structures
    shipNames.forEach(ship => {
        paidBLData[ship] = {};
        unpaidBLData[ship] = {};
        
        if (blStatusData[ship]) {
            blStatusData[ship].forEach(item => {
                voyageLabels.add(item.voyageNum);
                
                if (!paidBLData[ship][item.voyageNum]) {
                    paidBLData[ship][item.voyageNum] = 0;
                }
                if (!unpaidBLData[ship][item.voyageNum]) {
                    unpaidBLData[ship][item.voyageNum] = 0;
                }
                
                // Count paid vs unpaid (case-insensitive comparison)
                const blStatus = item.blStatus ? item.blStatus.toUpperCase() : '';
                if (blStatus === 'PAID') {
                    paidBLData[ship][item.voyageNum] += parseInt(item.count);
                } else {
                    unpaidBLData[ship][item.voyageNum] += parseInt(item.count);
                }
            });
        }
    });

    // Prepare datasets for BL Status Chart
    const sortedVoyageLabels = Array.from(voyageLabels).sort();
    const blStatusDatasets = [];

    // For each ship, create a pair of datasets (paid and unpaid)
    shipNames.forEach((ship, index) => {
        const shipDisplayName = `M/V Everwin Star ${ship}`;  // Add M/V prefix for display
        
        // Filter voyages for the specific ship
        const filteredVoyages = filterVoyagesForShip(ship);

        // Paid BL dataset
        blStatusDatasets.push({
            label: `${shipDisplayName} - Paid BL`,
            data: filteredVoyages.map(voyage => paidBLData[ship][voyage] || 0),
            backgroundColor: `hsla(${210 + index * 40}, 70%, 60%, 0.7)`,
            stack: ship
        });

        // Unpaid BL dataset
        blStatusDatasets.push({
            label: `${shipDisplayName} - Unpaid BL`,
            data: filteredVoyages.map(voyage => unpaidBLData[ship][voyage] || 0),
            backgroundColor: `hsla(${0 + index * 40}, 70%, 60%, 0.7)`,
            stack: ship
        });
    });

    // BL Status Chart
    ships.forEach(ship => {
        const shipNumber = ship.ship_number;
        const blStatusCtx = document.getElementById(`blStatusChart${shipNumber}`).getContext('2d');
        
        // Filter voyages for this specific ship
        const shipFilteredVoyages = filterVoyagesForShip(shipNumber);
        
        // Only use datasets for this specific ship
        const shipDatasets = blStatusDatasets.filter(dataset => dataset.stack === shipNumber);
        
        new Chart(blStatusCtx, {
            type: 'bar',
            data: {
                labels: shipFilteredVoyages,
                datasets: shipDatasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                return `Voyage: ${tooltipItems[0].label}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Voyage Number'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of BL'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });

    // Prepare data for the Earnings Chart
    const earningsDatasets = [];

    shipNames.forEach((ship, index) => {
        const shipEarningsData = {};
        // Get voyages filtered for this specific ship
        const filteredVoyages = filterVoyagesForShip(ship);
        
        if (earningsData[ship]) {
            earningsData[ship].forEach(item => {
                shipEarningsData[item.voyageNum] = parseFloat(item.earnings);
            });
        }
        
        earningsDatasets.push({
            label: `M/V Everwin Star ${ship} Earnings`,
            data: filteredVoyages.map(voyage => shipEarningsData[voyage] || 0),
            borderColor: `hsla(${120 + index * 40}, 70%, 50%, 1)`,
            backgroundColor: `hsla(${120 + index * 40}, 70%, 50%, 0.2)`,
            fill: true,
            shipNumber: ship, // Add explicit shipNumber property for accurate filtering
            filteredVoyages: filteredVoyages // Store the filtered voyages for this ship
        });
    });

    // Debug earnings datasets to verify data
    console.log('Earnings Datasets:', earningsDatasets);

    // Earnings Chart
    ships.forEach(ship => {
        const shipNumber = ship.ship_number;
        
        // For Ships I and II, create separate IN and OUT charts
        if (shipNumber === 'I' || shipNumber === 'II') {
            // Create separate datasets for IN and OUT voyages
            const inVoyages = filterVoyagesForShip(shipNumber).filter(v => v.includes('-IN'));
            const outVoyages = filterVoyagesForShip(shipNumber).filter(v => v.includes('-OUT'));
            
            console.log(`Ship ${shipNumber} IN voyages:`, inVoyages);
            console.log(`Ship ${shipNumber} OUT voyages:`, outVoyages);
            
            // Create the IN chart
            const inCtx = document.getElementById(`earningsChartIN${shipNumber}`).getContext('2d');
            const inEarningsData = {};
            
            // Process data for IN voyages
            if (earningsData[shipNumber]) {
                earningsData[shipNumber].forEach(item => {
                    if (item.voyageNum && item.voyageNum.includes('-IN')) {
                        inEarningsData[item.voyageNum] = parseFloat(item.earnings);
                    }
                });
            }
            
            // Create datasets for IN voyages
            const inDataset = {
                label: `M/V Everwin Star ${shipNumber} Inbound Earnings`,
                data: inVoyages.map(voyage => inEarningsData[voyage] || 0),
                borderColor: `hsla(120, 70%, 50%, 1)`,
                backgroundColor: `hsla(120, 70%, 50%, 0.2)`,
                fill: true
            };
            
            // Create the IN chart
            new Chart(inCtx, {
                type: 'line',
                data: {
                    labels: inVoyages,
                    datasets: [inDataset]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Voyage Number'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Earnings (PHP)'
                            },
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('en-PH', { 
                                        style: 'currency', 
                                        currency: 'PHP',
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });
            
            // Create the OUT chart
            const outCtx = document.getElementById(`earningsChartOUT${shipNumber}`).getContext('2d');
            const outEarningsData = {};
            
            // Process data for OUT voyages
            if (earningsData[shipNumber]) {
                earningsData[shipNumber].forEach(item => {
                    if (item.voyageNum && item.voyageNum.includes('-OUT')) {
                        outEarningsData[item.voyageNum] = parseFloat(item.earnings);
                    }
                });
            }
            
            // Create datasets for OUT voyages
            const outDataset = {
                label: `M/V Everwin Star ${shipNumber} Outbound Earnings`,
                data: outVoyages.map(voyage => outEarningsData[voyage] || 0),
                borderColor: `hsla(200, 70%, 50%, 1)`,
                backgroundColor: `hsla(200, 70%, 50%, 0.2)`,
                fill: true
            };
            
            // Create the OUT chart
            new Chart(outCtx, {
                type: 'line',
                data: {
                    labels: outVoyages,
                    datasets: [outDataset]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Voyage Number'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Earnings (PHP)'
                            },
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('en-PH', { 
                                        style: 'currency', 
                                        currency: 'PHP',
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });
            
        } else {
            // For other ships, create the regular chart
            const earningsCtx = document.getElementById(`earningsChart${shipNumber}`).getContext('2d');
            
            // Get filtered voyages specific to this ship
            const shipFilteredVoyages = filterVoyagesForShip(shipNumber);
            
            // Use exact shipNumber matching for more accurate filtering
            const filteredDatasets = earningsDatasets.filter(dataset => dataset.shipNumber === shipNumber);
            console.log(`Filtered datasets for Ship ${shipNumber}:`, filteredDatasets);
            
            new Chart(earningsCtx, {
                type: 'line',
                data: {
                    labels: shipFilteredVoyages,
                    datasets: filteredDatasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Voyage Number'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Earnings (PHP)'
                            },
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('en-PH', { 
                                        style: 'currency', 
                                        currency: 'PHP',
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        }
    });

    // BL Distribution Pie Charts for each ship
    ships.forEach((ship, index) => {
        const shipNumber = ship.ship_number;
        const chartId = `blDistributionChart${index}`;
        
        // Make sure the canvas element exists
        const canvas = document.getElementById(chartId);
        if (!canvas) {
            console.error(`Canvas element with ID ${chartId} not found`);
            return;
        }

        // Apply the ship-specific voyage filtering
        const shipVoyages = filterVoyagesForShip(shipNumber);
        
        console.log(`Ship ${shipNumber} filtered voyages for pie chart:`, shipVoyages);
        
        // Create datasets for the pie chart
        let hasData = false;
        let labels = [];
        let data = [];
        let backgroundColors = [];
        
        // Handle case where ship has voyages
        if (shipVoyages.length > 0) {
            // Get BL counts for each voyage of this ship
            shipVoyages.forEach((voyage, i) => {
                console.log(`Checking voyage ${voyage} for ship ${shipNumber}`);
                
                // Find the count for this voyage
                let count = 0;
                if (blCountData[shipNumber]) {
                    // Try exact match first
                    const exactMatchData = blCountData[shipNumber].find(item => {
                        return item.voyageNum === voyage;
                    });
                    
                    if (exactMatchData) {
                        console.log(`Found exact match for voyage ${voyage}:`, exactMatchData);
                        count = parseInt(exactMatchData.count);
                    } else {
                        // Try matching by base voyage number (without directional suffix)
                        const baseVoyage = voyage.split('-')[0];
                        console.log(`Looking for base voyage ${baseVoyage}`);
                        
                        const baseMatchData = blCountData[shipNumber].find(item => {
                            const itemBaseVoyage = item.voyageNum.split('-')[0];
                            return itemBaseVoyage === baseVoyage;
                        });
                        
                        if (baseMatchData) {
                            console.log(`Found match by base voyage ${baseVoyage}:`, baseMatchData);
                            count = parseInt(baseMatchData.count);
                        } else {
                            console.log(`No match found for voyage ${voyage}`);
                        }
                    }
                }
                
                // Always add the voyage to the chart, even if count is zero
                labels.push(voyage);
                data.push(count);
                backgroundColors.push(`hsla(${210 + i * 40}, 70%, 60%, 0.7)`);
                if (count > 0) hasData = true;
            });
            
            // Check if there are any BLs that don't have a voyage assignment
            const totalWithVoyage = data.reduce((sum, count) => sum + count, 0);
            const totalForShip = totalBlPerShip[shipNumber] || 0;
            const unassigned = totalForShip - totalWithVoyage;
            
            if (unassigned > 0) {
                labels.push('No Voyage Assigned');
                data.push(unassigned);
                backgroundColors.push('hsla(0, 70%, 60%, 0.7)');
                hasData = true;
            }
        }
        
        // If no data for this ship at all
        if (!hasData) {
            if (totalBlPerShip[shipNumber] && totalBlPerShip[shipNumber] > 0) {
                // Has BLs but no voyages
                labels = ['No Voyage Assigned'];
                data = [totalBlPerShip[shipNumber]];
                backgroundColors = ['hsla(0, 70%, 60%, 0.7)'];
            } else {
                // No BLs at all
                labels = ['No BLs'];
                data = [1];
                backgroundColors = ['#e0e0e0'];
            }
        }
        
        console.log(`Final data for chart ${chartId} (ship ${shipNumber}):`, { labels, data });

        // Create the pie chart
        try {
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: `BL Distribution for M/V Everwin Star ${shipNumber}`,
                        data: data,
                        backgroundColor: backgroundColors,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (labels[0] === 'No BLs') return 'No BL data';
                                    
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} BL (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error(`Error creating chart for M/V Everwin Star ${shipNumber}:`, error);
        }
    });

    // Tab switching logic
    function switchTab(event, showPanelId, hidePanelId) {
        event.preventDefault();
        document.getElementById(showPanelId).classList.remove('hidden');
        document.getElementById(hidePanelId).classList.add('hidden');
        const tabs = event.target.closest('ul').querySelectorAll('.tab-link');
        tabs.forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Sub-tab switching logic for Inbound/Outbound voyages
    function switchSubTab(event, showPanelId, hidePanelId) {
        event.preventDefault();
        document.getElementById(showPanelId).classList.remove('hidden');
        document.getElementById(hidePanelId).classList.add('hidden');
        const tabs = event.target.closest('ul').querySelectorAll('.subtab-link');
        tabs.forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Ship tab switching logic
    function showShipData(shipNumber) {
        // Hide all ship data sections
        document.querySelectorAll('.ship-data').forEach(section => {
            section.classList.add('hidden');
        });
        
        // Show the selected ship data section
        document.getElementById(`shipData${shipNumber}`).classList.remove('hidden');
        
        // Update tab active states
        document.querySelectorAll('.ship-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.getElementById(`shipTab${shipNumber}`).classList.add('active');
    }
</script>