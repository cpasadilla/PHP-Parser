@php
    $isSaverStar = $order->shipNum === 'SAVER';
    $logoPath = $isSaverStar ? 'images/logo-saver.jpg' : 'images/logo-sfx.png';
    $shipName = $isSaverStar ? 'M/V SAVER STAR' : 'M/V EVERWIN STAR';
@endphp
<x-app-layout>
    <x-slot name="header"></x-slot>
    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between items-center mb-4">
            <div class="text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex items-center mb-1">
                    <svg class="w-4 h-4 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <strong class="text-yellow-800">For clean printing:</strong>
                </div>
                <p class="text-xs text-yellow-700">In the print dialog, click "More settings" and uncheck "Headers and footers" to remove date/URL from printout.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('gatepass.create', ['order_id' => $order->id]) }}" class="btn btn-primary">CREATE NEW GATE PASS</a>
                <button class="btn btn-success" onclick="printContent('printContainer')">PRINT</button>
            </div>
        </div>

        <!-- Printable Summary Report -->
        <div id="printContainer" class="border p-8 shadow-lg text-black bg-white" style="max-width: 8.5in; margin: 0 auto;">
            <!-- Header -->
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 20px;">
                <img style="width: 500px; height: 70px;" src="{{ asset($logoPath) }}" alt="Logo">
                @if(!$isSaverStar)
                <div style="font-family: Arial; font-size: 12px; line-height: 1.2; margin-top: 3px;">
                    <p style="margin: 0;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                    <p style="margin: 0;">Cellphone Nos.: 0908-815-9300 / 0999-889-5848 / 0999-889-5849</p>
                    <p style="margin: 0;">Email Address: fxavier_2015@yahoo.com.ph</p>
                    <p style="margin: 0;">TIN: 009-081-111-000</p>
                </div>
                @endif
            </div>

            <!-- Title -->
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="font-family: Arial; font-weight: bold; font-size: 20px; margin: 0;">GATEPASS RELEASE SUMMARY REPORT</h2>
            </div>

            <!-- BL Information -->
            <div style="margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd;">
                <table style="width: 100%; font-family: Arial; font-size: 12px;">
                    <tr>
                        <td style="width: 25%; padding: 3px;"><strong>BL Number:</strong></td>
                        <td style="width: 25%; padding: 3px;">{{ $order->orderId }}</td>
                        <td style="width: 25%; padding: 3px;"><strong>Container No.:</strong></td>
                        <td style="width: 25%; padding: 3px;">{{ $order->containerNum }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px;"><strong>Shipper:</strong></td>
                        <td style="padding: 3px;">{{ $order->shipperName }}</td>
                        <td style="padding: 3px;"><strong>Consignee:</strong></td>
                        <td style="padding: 3px;">{{ $order->recName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px;"><strong>Origin:</strong></td>
                        <td style="padding: 3px;">{{ $order->origin }}</td>
                        <td style="padding: 3px;"><strong>Destination:</strong></td>
                        <td style="padding: 3px;">{{ $order->destination }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px;"><strong>Ship Number:</strong></td>
                        <td style="padding: 3px;">M/V EVERWIN STAR {{ $order->shipNum }}</td>
                        <td style="padding: 3px;"><strong>Voyage:</strong></td>
                        <td style="padding: 3px;">{{ $order->voyageNum }}</td>
                    </tr>
                </table>
            </div>

            <!-- Summary Table -->
            <div style="margin-bottom: 25px;">
                <h3 style="font-family: Arial; font-size: 14px; font-weight: bold; margin-bottom: 10px;">CARGO STATUS SUMMARY:</h3>
                <table style="width: 100%; border-collapse: collapse; font-family: Arial; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="border: 1px solid black; padding: 10px; text-align: left;">Item Description</th>
                            <th style="border: 1px solid black; padding: 10px; text-align: center; width: 12%;">Unit</th>
                            <th style="border: 1px solid black; padding: 10px; text-align: center; width: 15%;">Total Quantity</th>
                            <th style="border: 1px solid black; padding: 10px; text-align: center; width: 15%;">Released</th>
                            <th style="border: 1px solid black; padding: 10px; text-align: center; width: 15%;">Remaining</th>
                            <th style="border: 1px solid black; padding: 10px; text-align: center; width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allReleased = true;
                            $hasUnreleased = false;
                        @endphp
                        @forelse($summary as $item)
                            @php
                                $status = 'Fully Released';
                                $statusColor = '#4CAF50';
                                
                                if ($item['remaining_quantity'] > 0) {
                                    $status = 'Partially Released';
                                    $statusColor = '#FF9800';
                                    $allReleased = false;
                                    $hasUnreleased = true;
                                }
                                
                                if ($item['released_quantity'] == 0) {
                                    $status = 'Not Released';
                                    $statusColor = '#f44336';
                                    $allReleased = false;
                                    $hasUnreleased = true;
                                }
                            @endphp
                            <tr>
                                <td style="border: 1px solid black; padding: 8px;">{{ $item['item_description'] }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item['unit'] }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ number_format($item['total_quantity'], 2) }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ number_format($item['released_quantity'], 2) }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center; {{ $item['remaining_quantity'] > 0 ? 'font-weight: bold; color: #d32f2f;' : '' }}">
                                    {{ number_format($item['remaining_quantity'], 2) }}
                                </td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center; color: white; background-color: {{ $statusColor }}; font-weight: bold; font-size: 10px;">
                                    {{ $status }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="border: 1px solid black; padding: 20px; text-align: center; color: #999;">
                                    No cargo items found for this BL
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Gate Pass History -->
            @if($order->gatePasses->count() > 0)
            <div style="margin-bottom: 25px; page-break-before: avoid;">
                <h3 style="font-family: Arial; font-size: 14px; font-weight: bold; margin-bottom: 10px;">GATE PASS HISTORY:</h3>
                <table style="width: 100%; border-collapse: collapse; font-family: Arial; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="border: 1px solid black; padding: 8px; text-align: left; width: 12%;">Gate Pass No.</th>
                            <th style="border: 1px solid black; padding: 8px; text-align: center; width: 12%;">Release Date</th>
                            <th style="border: 1px solid black; padding: 8px; text-align: left; width: 25%;">Items Released</th>
                            <th style="border: 1px solid black; padding: 8px; text-align: left; width: 15%;">Checker</th>
                            <th style="border: 1px solid black; padding: 8px; text-align: left; width: 15%;">Receiver</th>
                            <th style="border: 1px solid black; padding: 8px; text-align: left; width: 21%;">Notes/Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->gatePasses->sortBy('release_date') as $gatePass)
                            <tr>
                                <td style="border: 1px solid black; padding: 8px; font-weight: bold;">{{ $gatePass->gate_pass_no }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                    {{ \Carbon\Carbon::parse($gatePass->release_date)->format('M d, Y') }}
                                </td>
                                <td style="border: 1px solid black; padding: 8px;">
                                    <ul style="margin: 0; padding-left: 20px; list-style-type: disc;">
                                        @foreach($gatePass->items as $item)
                                            <li>{{ number_format($item->released_quantity, 2) }} {{ $item->unit }} of {{ $item->item_description }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td style="border: 1px solid black; padding: 8px;">{{ $gatePass->checker_name }}</td>
                                <td style="border: 1px solid black; padding: 8px;">{{ $gatePass->receiver_name }}</td>
                                <td style="border: 1px solid black; padding: 8px; font-size: 10px;">
                                    {{ $gatePass->checker_notes ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Overall Status Banner -->
            <div style="margin-top: 20px; padding: 15px; border-radius: 5px; text-align: center; {{ $allReleased ? 'background-color: #C8E6C9; border: 2px solid #4CAF50;' : ($hasUnreleased && $order->gatePasses->count() === 0 ? 'background-color: #FFCDD2; border: 2px solid #f44336;' : 'background-color: #FFECB3; border: 2px solid #FF9800;') }}">
                <p style="font-family: Arial; font-size: 14px; font-weight: bold; margin: 0; color: {{ $allReleased ? '#2E7D32' : ($hasUnreleased && $order->gatePasses->count() === 0 ? '#C62828' : '#E65100') }};">
                    @if($allReleased)
                        ✓ ALL CARGO FULLY RELEASED
                    @elseif($hasUnreleased && $order->gatePasses->count() === 0)
                        ⚠ CARGO NOT RELEASED
                    @else
                        ⚠ CARGO PARTIALLY RELEASED - ITEMS REMAINING
                    @endif
                </p>
            </div>

            <!-- Footer -->
            <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc;">
                <p style="font-family: Arial; font-size: 10px; text-align: center; color: #666; margin: 0;">
                    Report generated on {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }} | 
                    {{ $shipName }} | 
                    For internal records only
                </p>
            </div>
        </div>
    </div>

    <script>
        function printContent(el) {
            var restorepage = document.body.innerHTML;
            var printcontent = document.getElementById(el).innerHTML;
            document.body.innerHTML = printcontent;
            window.print();
            document.body.innerHTML = restorepage;
            location.reload();
        }
    </script>
</x-app-layout>
