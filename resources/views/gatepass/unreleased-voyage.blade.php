<style>
@media print {
    table, th, td { border: none !important; }
    table, th, td { line-height: 1 !important; }
}
</style>

@php
    $isSaverStar = $shipNum === 'SAVER';
    $shipName = $isSaverStar ? 'M/V SAVER STAR' : 'M/V EVERWIN STAR';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight flex items-center gap-2">
                <a href="{{ route('gatepass.unreleased.ships') }}" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </a>
                {{ __('Gate Pass Status - ') }} {{ $shipName }} {{ $shipNum }} - Voyage {{ $voyageNum }}
            </h2>
            <button onclick="printContent('printContainer')" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                Print Report
            </button>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <!-- Search Bar -->
            <div class="mb-4">
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <script>
                function filterTable() {
                    const input = document.getElementById('searchInput');
                    const filter = input.value.toLowerCase();
                    const rows = document.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
                    });
                }
            </script>
        <!-- Summary Statistics with Filter Buttons -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <button onclick="filterStatus('RELEASED')" class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer">
                <h3 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-1">Fully Released</h3>
                <p class="text-3xl font-bold text-green-600 dark:text-green-300">
                    {{ collect($allOrders)->where('releaseStatus', 'RELEASED')->count() }}
                </p>
            </button>
            <button onclick="filterStatus('NOT RELEASED')" class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-1">Not Released</h3>
                <p class="text-3xl font-bold text-red-600 dark:text-red-300">
                    {{ collect($allOrders)->where('releaseStatus', 'NOT RELEASED')->count() }}
                </p>
            </button>
            <button onclick="filterStatus('PARTIAL RELEASED')" class="bg-orange-50 dark:bg-orange-900 border border-orange-200 dark:border-orange-700 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer">
                <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-200 mb-1">Partial Released</h3>
                <p class="text-3xl font-bold text-orange-600 dark:text-orange-300">
                    {{ collect($allOrders)->where('releaseStatus', 'PARTIAL RELEASED')->count() }}
                </p>
            </button>
            <button onclick="filterStatus('ALL')" class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer">
                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-1">Total BLs</h3>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-300">
                    {{ count($allOrders) }}
                </p>
            </button>
        </div>

        <!-- Printable Report -->
        <div id="printContainer">
            <div class="print-header mb-4" style="display: none;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h1 style="font-size: 20px; font-weight: bold; margin: 0;">GATE PASS STATUS REPORT</h1>
                    <h2 style="font-size: 16px; margin: 5px 0;">{{ $shipName }} {{ $shipNum }} - Voyage {{ $voyageNum }}</h2>
                    <p style="font-size: 12px; color: #666; margin: 0;">Generated on {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}</p>
                </div>
            </div>

            @if(count($allOrders) > 0)
                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse border border-gray-300">
                        <thead class="bg-gray-200 dark:bg-dark-eval-0">
                            <tr>
                                <th class="p-3 border border-gray-300 text-left">BL #</th>
                                <th class="p-3 border border-gray-300 text-center">Container #</th>
                                <th class="p-3 border border-gray-300 text-center">Cargo Status</th>
                                <th class="p-3 border border-gray-300 text-left">Shipper</th>
                                <th class="p-3 border border-gray-300 text-left">Consignee</th>
                                <th class="p-3 border border-gray-300 text-center">Release Status</th>
                                <th class="p-3 border border-gray-300 text-center">Released Date</th>
                                <th class="p-3 border border-gray-300 text-left">Item Status Details</th>
                                <th class="p-3 border border-gray-300 text-center no-print">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allOrders as $item)
                                @php
                                    $order = $item['order'];
                                    $status = $item['releaseStatus'];
                                    $statusColors = [
                                        'RELEASED' => ['bg' => '#4CAF50', 'text' => 'white'],
                                        'NOT RELEASED' => ['bg' => '#f44336', 'text' => 'white'],
                                        'PARTIAL RELEASED' => ['bg' => '#FF9800', 'text' => 'white'],
                                    ];
                                    $colors = $statusColors[$status] ?? ['bg' => '#9E9E9E', 'text' => 'white'];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 status-row" data-status="{{ $status }}">
                                    <td class="p-3 border border-gray-300 font-semibold">
                                        {{ $order->orderId }}
                                    </td>
                                    <td class="p-3 border border-gray-300 text-center">
                                        {{ $order->containerNum }}
                                    </td>
                                    <td class="p-3 border border-gray-300 text-center">
                                        {{ $order->cargoType ?? 'N/A' }}
                                    </td>
                                    <td class="p-3 border border-gray-300">
                                        {{ $order->shipperName }}
                                    </td>
                                    <td class="p-3 border border-gray-300">
                                        {{ $order->recName }}
                                    </td>
                                    <td class="p-3 border border-gray-300 text-center">
                                        <span style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; white-space: nowrap;">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="p-3 border border-gray-300 text-center">
                                        @php
                                            // Get the most recent gate pass release date
                                            $latestReleaseDate = $order->gatePasses->sortByDesc('release_date')->first()?->release_date;
                                        @endphp
                                        @if($latestReleaseDate)
                                            {{ \Carbon\Carbon::parse($latestReleaseDate)->format('F d, Y') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 border border-gray-300">
                                        <ul class="text-sm space-y-1">
                                            @foreach($item['summary'] as $summaryItem)
                                                @php
                                                    $isFullyReleased = $summaryItem['remaining_quantity'] <= 0;
                                                    $textColor = $isFullyReleased ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                                                @endphp
                                                <li class="{{ $textColor }} font-medium">
                                                    @if($isFullyReleased)
                                                        ✓ {{ $summaryItem['item_description'] }} - Fully Released
                                                    @else
                                                        {{ number_format($summaryItem['remaining_quantity'], 2) }} {{ $summaryItem['unit'] }} 
                                                        remaining of {{ $summaryItem['item_description'] }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="p-3 border border-gray-300 text-center no-print">
                                        <div class="flex gap-2 justify-center flex-wrap">
                                            <a href="{{ route('gatepass.summary', ['order_id' => $order->id]) }}" 
                                               class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-base font-semibold shadow-lg transition-all duration-200"
                                               target="_blank">
                                                View Summary
                                            </a>
                                            <a href="{{ route('gatepass.create', ['order_id' => $order->id]) }}" 
                                               class="px-5 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-base font-semibold shadow-lg transition-all duration-200">
                                                Create Gate Pass
                                            </a>
                                            @if($order->gatePasses->count() > 0)
                                                @php
                                                    $latestGatePass = $order->gatePasses->sortByDesc('created_at')->first();
                                                @endphp
                                                <a href="{{ route('gatepass.edit', $latestGatePass->id) }}" 
                                                   class="px-5 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-base font-semibold shadow-lg transition-all duration-200">
                                                    Edit
                                                </a>
                                                <form action="{{ route('gatepass.destroy', $latestGatePass->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this gate pass?');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="px-5 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-base font-semibold shadow-lg transition-all duration-200">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @push('styles')
                        <style>
                        @media print {
                            table, th, td { border: none !important; }
                            table, th, td { line-height: 1 !important; }
                            * { font-size: 14px !important; }
                        }
                        </style>
                    @endpush
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            * {
                line-height: 1 !important;
            }
            .no-print {
                display: none !important;
            }
            th.no-print, td.no-print {
                display: none !important;
            }
            .print-header {
                display: block !important;
                margin-bottom: 10px !important;
            }
            .print-header h1 {
                line-height: 1.1 !important;
                margin: 0 0 3px 0 !important;
                padding: 0 !important;
            }
            .print-header h2 {
                line-height: 1.1 !important;
                margin: 0 0 2px 0 !important;
                padding: 0 !important;
            }
            .print-header p {
                line-height: 1.1 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            body {
                background: white;
                margin: 0 !important;
                padding: 10px !important;
            }
            .p-6 {
                padding: 0 !important;
            }
            table {
                page-break-inside: auto;
                border-collapse: collapse !important;
                width: 100% !important;
                margin: 0 !important;
                table-layout: auto !important;
                border: 2px solid #000 !important;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            td, th {
                padding: 3px 5px !important;
                line-height: 1.1 !important;
                font-size: 10px !important;
                border: 2px solid #000 !important;
                vertical-align: top !important;
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
            }
            th {
                font-weight: bold !important;
                background-color: #e8e8e8 !important;
            }
            ul {
                padding: 0 0 0 12px !important;
                margin: 0 !important;
                list-style-position: inside !important;
            }
            li {
                line-height: 1.1 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            span {
                padding: 2px 6px !important;
                font-size: 9px !important;
                line-height: 1 !important;
                display: inline-block !important;
            }
            .overflow-x-auto {
                overflow: visible !important;
            }
            .mb-6, .mb-4, .space-y-1 {
                margin: 0 !important;
            }
            @page {
                size: landscape;
                margin: 10mm;
            }
        }
    </style>

    <script>
        function filterStatus(status) {
            const rows = document.querySelectorAll('.status-row');
            
            rows.forEach(row => {
                if (status === 'ALL') {
                    row.style.display = '';
                } else if (row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function printContent(el) {
            // Get the container element
            var printContainer = document.getElementById(el);
            
            // Clone the container
            var clonedContainer = printContainer.cloneNode(true);
            
            // Remove all hidden rows from the cloned container
            var rows = clonedContainer.querySelectorAll('.status-row');
            rows.forEach(row => {
                if (row.style.display === 'none') {
                    row.remove();
                }
            });
            
            // Remove the Actions column (last column) from cloned table
            var table = clonedContainer.querySelector('table');
            if (table) {
                // Remove header cell (last th)
                var headerRow = table.querySelector('thead tr');
                if (headerRow) {
                    var lastTh = headerRow.querySelector('th:last-child');
                    if (lastTh) {
                        lastTh.remove();
                    }
                }
                
                // Remove data cells (last td in each row)
                var bodyRows = table.querySelectorAll('tbody tr');
                bodyRows.forEach(row => {
                    var lastTd = row.querySelector('td:last-child');
                    if (lastTd) {
                        lastTd.remove();
                    }
                });
            }
            
            // Store original content
            var originalContent = document.body.innerHTML;
            
            // Replace body with cloned (filtered) content
            document.body.innerHTML = clonedContainer.innerHTML;
            
            // Print
            window.print();
            
            // Restore original content
            document.body.innerHTML = originalContent;
            
            // Reload to restore event listeners
            location.reload();
        }
    </script>
</x-app-layout>
