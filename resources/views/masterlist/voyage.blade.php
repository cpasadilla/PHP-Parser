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
            <h5 class="font-semibold">Voyage List</h5>
            <br>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('current')" id="current-tab" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600 dark:text-blue-400">
                        Current Voyages
                    </button>
                    @if(count($preDockerVoyages) > 0)
                    <button onclick="showTab('predock')" id="predock-tab" class="tab-button py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                        Pre-Dock Voyages
                    </button>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Current Voyages Tab -->
        <div id="current-voyages" class="tab-content">
            <h6 class="font-medium mb-4 text-gray-700 dark:text-gray-300">Active Voyages</h6>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-200 dark:bg-dark-eval-0">
                        <tr>
                            <th class="p-2 text-gray-700 dark:text-white">Voyage Number</th>
                            <th class="p-2 text-gray-700 dark:text-white" hidden>Ship</th>
                            <th class="p-2 text-gray-700 dark:text-white">Origin</th>
                            <th class="p-2 text-gray-700 dark:text-white">Destination</th>
                            <th class="p-2 text-gray-700 dark:text-white">Status</th>
                            <th class="p-2 text-gray-700 dark:text-white">Last Updated</th>
                            <th class="p-2 text-gray-700 dark:text-white">Total of BL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($currentVoyages as $voyage)
                        @php
                            if ($ship->ship_number == 'I' || $ship->ship_number == 'II') {
                                $key = $voyage->v_num . '-' . $voyage->inOut;
                            }
                            else{
                                $key = $voyage->v_num;
                            }
                            $orderCount = $orderCounts[$key] ?? 0; // Get the count or default to 0
                        @endphp

                        <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-2 text-center">
                                <a href="{{ route('masterlist.voyage-orders-by-id', ['voyageId' => $voyage->id]) }}" class="text-blue-500 hover:underline">
                                    {{ $key }}
                                </a>
                            </td>
                            <td class="p-2 text-center" hidden>{{ $voyage->ship }}</td>
                            <td class="p-2 text-center">{{ $voyageRoutes[$key]['origin'] }}</td>
                            <td class="p-2 text-center">{{ $voyageRoutes[$key]['destination'] }}</td>
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
                                No current voyages found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pre-Dock Voyages Tab -->
        @if(count($preDockerVoyages) > 0)
        <div id="predock-voyages" class="tab-content hidden">
            <h6 class="font-medium mb-4 text-gray-700 dark:text-gray-300">Pre-Dock Voyages (Historical)</h6>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-200 dark:bg-dark-eval-0">
                        <tr>
                            <th class="p-2 text-gray-700 dark:text-white">Voyage Number</th>
                            <th class="p-2 text-gray-700 dark:text-white" hidden>Ship</th>
                            <th class="p-2 text-gray-700 dark:text-white">Origin</th>
                            <th class="p-2 text-gray-700 dark:text-white">Destination</th>
                            <th class="p-2 text-gray-700 dark:text-white">Status</th>
                            <th class="p-2 text-gray-700 dark:text-white">Last Updated</th>
                            <th class="p-2 text-gray-700 dark:text-white">Total of BL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($preDockerVoyages as $voyage)
                        @php
                            if ($ship->ship_number == 'I' || $ship->ship_number == 'II') {
                                $key = $voyage->v_num . '-' . $voyage->inOut;
                            }
                            else{
                                $key = $voyage->v_num;
                            }
                            $orderCount = $orderCounts[$key] ?? 0; // Get the count or default to 0
                        @endphp

                        <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-2 text-center">
                                <a href="{{ route('masterlist.voyage-orders-by-id', ['voyageId' => $voyage->id]) }}" class="text-blue-500 hover:underline">
                                    {{ $key }}
                                </a>
                            </td>
                            <td class="p-2 text-center" hidden>{{ $voyage->ship }}</td>
                            <td class="p-2 text-center">{{ $voyageRoutes[$key]['origin'] }}</td>
                            <td class="p-2 text-center">{{ $voyageRoutes[$key]['destination'] }}</td>
                            <td class="p-2 text-center">
                                <span class="px-4 py-2 text-sm bg-gray-100 text-gray-600 rounded">
                                    {{ $voyage->lastStatus }} (Historical)
                                </span>
                            </td>
                            <td class="p-2 text-center">{{ \Carbon\Carbon::parse($voyage->lastUpdated)->format('F d, Y') }}</td>
                            <td class="p-2 text-center">{{ $orderCount }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    <br>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active styling from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });

            // Show selected tab content
            document.getElementById(tabName + '-voyages').classList.remove('hidden');

            // Add active styling to selected tab button
            const activeButton = document.getElementById(tabName + '-tab');
            activeButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        }

        // Initialize the page with current tab active
        document.addEventListener('DOMContentLoaded', function() {
            showTab('current');
        });
    </script>
</x-app-layout>