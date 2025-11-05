@php
    $isSaverStar = $gatePass->order->shipNum === 'SAVER';
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
                <a href="{{ route('gatepass.edit', $gatePass->id) }}" class="btn btn-warning">EDIT</a>
                <a href="{{ route('gatepass.summary', ['order_id' => $gatePass->order_id]) }}" class="btn btn-info" target="_blank">VIEW SUMMARY</a>
                <button class="btn btn-success" onclick="printContent('printContainer')">PRINT</button>
            </div>
        </div>

        <!-- Printable Gate Pass -->
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
            <div style="text-align: center; margin-bottom: 15px;">
                <h2 style="font-family: Arial; font-weight: bold; font-size: 20px; margin: 0;">GATE PASS</h2>
            </div>

            <!-- Gate Pass Info -->
            <table style="width: 100%; font-family: Arial; font-size: 12px; margin-bottom: 15px;">
                <tr>
                    <td style="width: 15%; padding: 3px;"><strong>Gate Pass No.:</strong></td>
                    <td style="width: 20%; padding: 3px; border-bottom: 1px solid black;">{{ $gatePass->gate_pass_no }}</td>
                    <td style="width: 30%; padding: 3px; text-align: right;"><strong>Date:</strong></td>
                    <td style="width: 30%; padding: 3px; border-bottom: 1px solid black;">{{ \Carbon\Carbon::parse($gatePass->release_date)->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="width: 15%; padding: 3px;"><strong>BL Number:</strong></td>
                    <td style="width: 20%; padding: 3px; border-bottom: 1px solid black;">{{ $gatePass->order->orderId }}</td>
                    <td style="width: 20%; padding: 3px; text-align: right;"><strong>Container No.:</strong></td>
                    <td style="width: 25%; padding: 3px; border-bottom: 1px solid black;">{{ $gatePass->container_number }}</td>
                </tr>
                <tr>
                    <td style="width: 15%; padding: 3px;"><strong>Shipper:</strong></td>
                    <td style="width: 28%; padding: 3px; border-bottom: 1px solid black;">{{ $gatePass->shipper_name }}</td>
                    <td style="width: 25%; padding: 3px; text-align: right;"><strong>Consignee:</strong></td>
                    <td style="width: 25%; padding: 3px; border-bottom: 1px solid black;">{{ $gatePass->consignee_name }}</td>
                </tr>
            </table>

            <!-- Items Released -->
            <div style="margin-bottom: 20px;">
                <h3 style="font-family: Arial; font-size: 14px; font-weight: bold; margin-bottom: 10px;">PARTICULARS (Items Released):</h3>
                <table style="width: 100%; border-collapse: collapse; font-family: Arial; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="border: 1px solid black; padding: 8px; text-align: center; width: 15%;">Quantity</th>
                            <th style="border: 1px solid black; padding: 8px; text-align: center; width: 10%;">Unit</th>
                            <th style="border: 1px solid black; padding: 8px; text-align: center;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gatePass->items as $item)
                            <tr>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ number_format($item->released_quantity, 2) }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->unit }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $item->item_description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Notes/Remarks -->
            @if($gatePass->checker_notes)
            <div style="margin-bottom: 20px;">
                <h3 style="font-family: Arial; font-size: 12px; font-weight: bold; margin-bottom: 5px;">Notes/Remarks:</h3>
                <p style="font-family: Arial; font-size: 11px; padding: 8px; border: 1px solid #ccc; background-color: #f9f9f9; min-height: 50px;">
                    {{ $gatePass->checker_notes }}
                </p>
            </div>
            @endif

            <!-- Signatures -->
            <div style="margin-top: 40px;">
                <table style="width: 100%; font-family: Arial; font-size: 12px;">
                    <tr>
                        <td style="width: 50%; padding-right: 20px; text-align: center; vertical-align: bottom;">
                            <div style="margin-bottom: 50px;"></div>
                            <div style="border-top: 1px solid black; padding-top: 5px; display: inline-block; min-width: 200px;">
                                {{ $gatePass->checker_name }}
                            </div>
                            <div style="margin-top: 3px;">Checker Name/Signature</div>
                        </td>
                        <td style="width: 50%; padding-left: 20px; text-align: center; vertical-align: bottom;">
                            <div style="margin-bottom: 50px;"></div>
                            <div style="border-top: 1px solid black; padding-top: 5px; display: inline-block; min-width: 200px;">
                                {{ $gatePass->receiver_name }}
                            </div>
                            <div style="margin-top: 3px;">Receiver Name/Signature</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer Note -->
             <br>
            <div style="margin-top: 30px; padding: 10px; background-color: #f0f0f0; border-left: 4px solid #4CAF50;" hidden>
                <p style="font-family: Arial; font-size: 10px; margin: 0;">
                    <strong>Note:</strong> This gate pass serves as proof of cargo release from {{ $shipName }} 
                    for BL# {{ $gatePass->order->orderId }}, Container# {{ $gatePass->container_number }}.
                    Please retain for your records.
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
