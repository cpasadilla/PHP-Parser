@php
    $isSaverStar = $order->shipNum === 'SAVER';
    $logoPath = $isSaverStar ? 'images/logo-saver.jpg' : 'images/logo-sfx.png';
    $headerColor = $isSaverStar ? '#1c89ba' : '#78BF65';
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
            <button class="btn btn-success ml-4" onclick="printContent('printContainer')">PRINT</button>
        </div>

        <!-- For pushOrder -->
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white" style="display: flex; flex-direction: column; min-height: 11in;">
            <div style="flex: 1;">
                <!-- Your existing content here -->
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
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
            <div style="display: flex; justify-content: center; margin-top: 5px; position: relative;">
                <span style="font-family: Arial; font-weight: bold; font-size: 17px;">BILL OF LADING</span>
                
                @if($order->gatePasses->count() > 0)
                    @php
                        $firstGatePass = $order->gatePasses->sortBy('release_date')->first();
                        $releaseDate = \Carbon\Carbon::parse($firstGatePass->release_date)->format('M d, Y');
                    @endphp
                    <div style="position: absolute; right: 10px; top: -10px; transform: rotate(15deg); 
                                border: 3px solid #d32f2f; padding: 10px 20px; border-radius: 5px;
                                background-color: rgba(255, 255, 255, 0.9);">
                        <div style="font-family: Arial; font-weight: bold; font-size: 18px; color: #d32f2f; 
                                    text-align: center; letter-spacing: 3px;">
                            RELEASED
                        </div>
                        <div style="font-family: Arial; font-size: 10px; color: #d32f2f; text-align: center; margin-top: 2px;">
                            {{ $releaseDate }}
                        </div>
                    </div>
                @endif
            </div>

            <div style="display: flex; justify-content: right;">
                <span style="font-family: Arial; font-weight: bold; font-size: 12px;">{{$order->cargoType}}</span>
            </div>
            <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <tr>
                      <td style="font-family: Arial; font-size: 11px; text-align: left; width: 78px; padding: 1px;"><strong>{{ $shipName }}</strong></td>
                      <td style="font-family: Arial; font-size: 12px; width: 20px; border-bottom: 1px solid black; text-align: center;">{{ $isSaverStar ? '-' : ($displayShip ?? $order->shipNum) }}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 50px; padding: 1px;"><strong>VOYAGE NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 50px; border-bottom: 1px solid black; text-align: center;">{{ $displayVoyage ?? $order->voyageNum }}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 30px; padding: 1px;"><strong>CONTAINER NO.</strong></td>
                        <td style="font-family: Arial; font-size: 11px; width: 200px; border-bottom: 1px solid black; text-align: center;">{{$order->containerNum}}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 33px; padding: 2px;"><strong>BL NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 20px; border-bottom: 1px solid black; text-align: center;">{{$order->orderId}}</td>
                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 40px; padding: 2px;"><strong>ORIGIN:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;">{{$order->origin}}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 72px; padding: 2px;"><strong>DESTINATION:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;">{{$order->destination}}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 35px; padding: 2px;"><strong>DATE:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;">{{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y') }}</td>

                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed; margin-top: 8px;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 61.34px; padding: 2px;"><strong>SHIPPER:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;">{{$order->shipperName}}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 79.97px; padding: 2px;"><strong>CONSIGNEE:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;">{{$order->recName}}</td>
                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 34px; padding: 2px;"><strong>CONTACT NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 108px; border-bottom: 1px solid black; text-align: center; ">{{$order->shipperNum}}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 34px; padding: 2px;"><strong>CONTACT NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 115px; border-bottom: 1px solid black; text-align: center;">{{$order->recNum}}</td>
                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 42px; padding: 2px;"><strong>GATE PASS NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 111px; border-bottom: 1px solid black; text-align: center; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word; hyphens: auto;">{{$order->gatePass}}</td>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 25px; padding: 2px;"><strong>REMARK:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 135px; border-bottom: 1px solid black; text-align: center;">{{$order->remark}}</td>
                    </tr>
                </table>

                <!-- Main Table -->
                <table class="w-full border-collapse text-sm main-table" style="padding: 0 5px; margin-top: 20px;">
                    <thead class="text-white border border-gray" style="background-color: {{ $headerColor }};">
                        <tr class="border border-gray">
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">QTY</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px; width: 70px;">UNIT</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">DESCRIPTION</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">VALUE</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">WEIGHT</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px; width: 140px;">MEASUREMENT</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">RATE</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">FREIGHT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parcels as $parcel)
                        <tr class="border-gray" style="border-bottom: 1px solid #cccccc;">
                            <td class="p-2 text-center" style="font-family: Arial; font-size: 13px; text-align: center;">{{$parcel->quantity}}</td>
                            <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: center; width: 70px;">{{$parcel->unit}}</td>
                            <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: left;">
                                {{$parcel->itemName}}{{ !empty($parcel->desc) ? ' - '.$parcel->desc : '' }}
                            </td>
                            <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                            <td class="p-2" style="font-family: Arial; font-size: 13px; width: 60px;">
                                @if ($parcel->weight && $parcel->weight != '0' && $parcel->weight != '0.00')
                                    {{$parcel->weight}}
                                @endif
                            </td>
                            <td class="p-2" style="font-family: Arial; font-size: 13px;  width: 150px;">
                                @if (!empty($parcel->measurements) && is_array($parcel->measurements))
                                    @foreach($parcel->measurements as $measurement)
                                        {{$measurement['length']}} × {{$measurement['width']}} × {{$measurement['height']}} ({{$measurement['quantity']}})<br>
                                    @endforeach
                                @elseif (!empty($parcel->length) && !empty($parcel->width) && !empty($parcel->height) && 
                                    $parcel->length != '0' && $parcel->length != '0.00' && 
                                    $parcel->width != '0' && $parcel->width != '0.00' && 
                                    $parcel->height != '0' && $parcel->height != '0.00')
                                    {{$parcel->length}} × {{$parcel->width}} × {{$parcel->height}}
                                @endif
                            </td>
                            <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right; width: 100px;">
                                @if (!empty($parcel->measurements) && is_array($parcel->measurements))
                                    @foreach($parcel->measurements as $measurement)
                                        {{ number_format(is_array($measurement) ? $measurement['rate'] : $measurement->rate, 2) }}<br>
                                    @endforeach
                                @else
                                    {{ number_format($parcel->itemPrice, 2) }}
                                @endif
                            </td>
                            <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right; width: 100px;">
                                @if (!empty($parcel->measurements) && is_array($parcel->measurements))
                                    @foreach($parcel->measurements as $measurement)
                                        {{ number_format(is_array($measurement) ? $measurement['freight'] : $measurement->freight, 2) }}<br>
                                    @endforeach
                                @else
                                    {{ number_format($parcel->total, 2) }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr class="border-gray" style="border-bottom: none;">
                            <td class="p-2"></td>
                            <td class="p-2"></td>
                            <td class="p-2"></td>
                            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; height: 30px;">VALUE: {{ number_format($order->value, 2) }}</td>
                            <td class="p-2"></td>
                            <td class="p-2"></td>
                            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">₱</td>
                            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">{{ number_format($order->freight, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                @if($order->blStatus === 'PAID')
                    <div class="paid-stamp" style="position: absolute; bottom: 210px; right: 10px; color: red; border: 3px solid rgb(128, 0, 0); color: rgb(128, 0, 0); font-size: 16px; font-weight: bold; font-family: 'Bebas Neue', sans-serif; padding: 5px; text-align: center; background-color: none; z-index: 1000; width: 220px; height: 50px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <span>PAID IN {{ strtoupper($order->display_updated_location ?? $order->updated_location ?? '') }}</span>
                        @if(!empty($order->AR) && !empty($order->OR))
                            <span>OR#: {{ $order->OR }} | AR#: {{ $order->AR }}</span>
                        @elseif(!empty($order->AR))
                            <span>AR#: {{ $order->AR }}</span>
                        @elseif(!empty($order->OR))
                            <span>OR#: {{ $order->OR }}</span>
                        @endif
                    </div>
                @endif

                <script>
                    function removePaddingForPrint() {
                        const paidStamp = document.querySelector('.paid-stamp');
                        if (paidStamp) {
                            paidStamp.style.padding = '0';
                        }
                    }

                    document.querySelectorAll('button').forEach(button => {
                        button.addEventListener('click', removePaddingForPrint);
                    });
                </script>
            </div>
            <footer style="margin-top: auto;">
                <!-- Your existing footer content here -->
                <table class="w-full text-xs border-collapse">
                    <tr>
                        <td colspan="6" style="text-align: left; font-family: Arial; font-size: 12px; font-weight: bold; padding: 2px;">Terms and Conditions:</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: left; font-family: Arial; font-size: 12px; padding-left: 11px; padding-top: 2px;">
                            1. We are not responsible for losses and damages due to improper packing.<br>
                            2. Claims on cargo losses and/or damages must be filed within five (5) days after unloading.<br>
                            3. Unclaimed cargoes shall be considered forfeited after thirty (30) days upon unloading.
                        </td>
                    </tr>
                </table>

                <table class="w-full text-xs border-collapse mt-2">
                    <tr>
                        <td style="width: 50%;"></td> <!-- Adjusted size -->
                        <td style="width: 20%;">
                        <td style="width: 15%;  text-align: left; font-family: Arial; font-size: 12px;">Freight:</td>
                        <td style="width: 25%; border-bottom: 1px solid black; font-family: Arial; font-size: 12px;  text-align: center;">{{ number_format($order->freight, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-family: Arial; font-size: 12px;">Received on board vessel in apparent good condition.</td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">Valuation:</td>
                        <td style="border-bottom: 1px solid black; font-family: Arial; font-size: 12px;  text-align: center;">{{ number_format($order->valuation, 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">
                            Wharfage:
                        </td>
                        <td style="border-bottom: 1px solid black; font-family: Arial; font-size: 12px; text-align: center;">
                            {{ $order->wharfage != 0 ? number_format($order->wharfage, 2) : '' }}
                        </td>
                    </tr>
                    <tr>
                    <td style="text-align: center; font-family: Arial; font-size: 14px; font-weight: bold; border-bottom: 1px solid black;">{{$order->checkName}} </td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">VAT:</td>
                        <td style="border-bottom: 1px solid black; text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-family: Arial; font-size: 12px;">Vessel's Checker or Authorized Representative</td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">Other Charges:</td>
                        <td style="border-bottom: 1px solid black; font-family: Arial; font-size: 12px; text-align: center;">
                            {{ $order->other != 0 ? number_format($order->other, 2) : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">Stuffing/Stippings:</td>
                        <td style="border-bottom: 1px solid black; text-align: center;"> </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px; font-weight: bold; color: black;">TOTAL:</td>
                        <td style="border-bottom: 1px solid black; font-family: Arial; font-size: 14px; font-weight: bold;  text-align: center; color: black;">
                            {{ number_format($order->totalAmount, 2) }}
                        </td>
                    </tr>
                </table>
            </footer>
        </div>
    </div>
</x-app-layout>
<!-- For printing -->
<script>
    function printContent(divId) {
        console.log("printContent function called");
        var printContainer = document.getElementById(divId);
        if (!printContainer) {
            console.error("Print container not found");
            return;
        }

        // Replace dropdowns with selected text
        function replaceDropdown(selectId) {
    let select = document.getElementById(selectId);
    if (!select) {
        console.error(`Dropdown with id ${selectId} not found`);
        return null;
    }
    let selectedText = select.options[select.selectedIndex].text;
    let span = document.createElement("span");
    span.textContent = selectedText;
    span.style.fontFamily = "Arial";
    span.style.fontSize = "12px";
    span.style.textTransform = "uppercase";
    select.parentNode.replaceChild(span, select);
    return { originalElement: select, newElement: span };
}

        // Replace text inputs with static text
        function replaceInput(inputId) {
            let input = document.getElementById(inputId);
            if (!input) {
                console.error(`Input with id ${inputId} not found`);
                return null;
            }
            let inputValue = input.value.trim() || " "; // Use "N/A" if empty
            let span = document.createElement("span");
            span.textContent = inputValue;
            span.style.fontFamily = "Arial";
            span.style.fontSize = "12px";
            span.style.textTransform = "uppercase";
            input.parentNode.replaceChild(span, input);
            return { originalElement: input, newElement: span };
        }

        // Store replaced elements for restoration
        let replacedElements = [];
        replacedElements.push(replaceDropdown("ship_no"));
        replacedElements.push(replaceDropdown("origin"));
        replacedElements.push(replaceDropdown("destination"));
        replacedElements.push(replaceDropdown("checkerName")); // Add checkerName to be replaced
        replacedElements.push(replaceInput("voyage_no"));
        replacedElements.push(replaceInput("container_no"));
        replacedElements.push(replaceInput("shipper_name"));  // NEWLY ADDED
        replacedElements.push(replaceInput("consignee_name")); // NEWLY ADDED
        replacedElements.push(replaceInput("shipper_contact"));  // NEWLY ADDED
        replacedElements.push(replaceInput("consignee_contact")); // NEWLY ADDED
        replacedElements.push(replaceInput("gate_pass_no")); // ✅ Added GATE PASS NO.
        replacedElements.push(replaceInput("remark")); // ✅ Added REMARK

        // Filter out null values
        replacedElements = replacedElements.filter(element => element !== null);

        // Get updated print content
        var printContents = printContainer.innerHTML;

        // Open new print window
        var printWindow = window.open("", "", "width=1000,height=800");
        printWindow.document.write("<html><head><title>Bill of Lading - BL#{{$order->orderId}}</title>");
        printWindow.document.write("<style>");
        printWindow.document.write(`
            @page {
                margin: 0.5in;
                size: auto;
            }
            
            @media print {
                body { 
                    -webkit-print-color-adjust: exact; 
                    print-color-adjust: exact; 
                    margin: 0; 
                    padding: 0;
                }
                #printContainer { 
                    position: relative; 
                    width: 100%; 
                    min-height: 11in; 
                    margin: 0;
                    padding: 0;
                }
                img { display: block !important; margin: 0 auto; }
                footer { position: absolute; bottom: 0; left: 0; width: 100%; }
                table { border-collapse: collapse; width: 100%; }
                thead { background-color: {{ $headerColor }} !important; color: white !important; }
                button { display: none; }
                
                /* Paid stamp print styles */
                .paid-stamp {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    opacity: 0.8 !important;
                    background: linear-gradient(45deg, #ffebee 25%, transparent 25%, transparent 75%, #ffebee 75%, #ffebee), 
                                linear-gradient(45deg, #ffebee 25%, transparent 25%, transparent 75%, #ffebee 75%, #ffebee) !important;
                    background-size: 8px 8px !important;
                    background-position: 0 0, 4px 4px !important;
                    border: 3px dashed #d32f2f !important;
                    color: #d32f2f !important;
                }
                
                /* Main table styles for print */
                .main-table {
                    border-collapse: collapse !important;
                }
                
                .main-table tr {
                    border-bottom: 1px solid #cccccc !important;
                }
                
                /* Remove border from last row (VALUE row) */
                .main-table tr:last-child {
                    border-bottom: none !important;
                }
                
                /* Ensure only row borders are visible in print */
                .border-gray {
                    border-bottom: 1px solid #cccccc !important;
                }
                
                /* Remove border from VALUE row specifically */
                .border-gray[style*="border-bottom: none"] {
                    border-bottom: none !important;
                }
            }
        `);
        printWindow.document.write("</style></head><body>");
        printWindow.document.write(printContents);
        printWindow.document.write("</body></html>");
        printWindow.document.close();
        printWindow.focus();

        // Print and restore elements after printing
        printWindow.onload = function () {
            // Add a small delay to ensure content is fully loaded
            setTimeout(function() {
                printWindow.print();
                
                // Use a different approach - don't auto-close, let user close manually
                printWindow.addEventListener('beforeunload', function() {
                    // Restore original elements when window closes
                    replacedElements.forEach(({ originalElement, newElement }) => {
                        if (newElement && newElement.parentNode) {
                            try {
                                newElement.parentNode.replaceChild(originalElement, newElement);
                            } catch (e) {
                                console.log("Element already restored or not found");
                            }
                        }
                    });
                });
                
                // Alternative: Use media query change detection
                const mediaQueryList = printWindow.matchMedia('print');
                mediaQueryList.addListener(function(mql) {
                    if (!mql.matches) {
                        // User finished printing or cancelled
                        setTimeout(function() {
                            if (!printWindow.closed) {
                                printWindow.close();
                            }
                            // Restore elements
                            replacedElements.forEach(({ originalElement, newElement }) => {
                                if (newElement && newElement.parentNode) {
                                    try {
                                        newElement.parentNode.replaceChild(originalElement, newElement);
                                    } catch (e) {
                                        console.log("Element already restored");
                                    }
                                }
                            });
                        }, 1000);
                    }
                });
                
            }, 500); // 500ms delay to ensure content is ready
        };
    }
</script>

<!-- Style -->
<style>
    /* General Styles */
    body {
        background: white;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    #printContainer {
        width: 8.5in; /* Letter size */
        min-height: 11in; /* Ensure container is at least the height of the page */
        padding: 0.5in;
        margin: auto;
        box-sizing: border-box;
        background: white;
        position: relative;
    }

    /* Table Layout */
    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        max-width: 7.5in;
    }

    /* Reduce Row Height */
    th, td {
        font-family: Arial; font-size: 11px; /* Keep text size small */
        padding: 2px 4px !important; /* Reduce padding */
        line-height: 1 !important; /* Set line height to 1 */
        text-align: center;
        max-width: 1.5in; /* Prevent text from stretching columns */
        overflow: hidden;
        vertical-align: middle; /* Align text to middle */
    }

    /* Modal Table Font Size */
    #addToCartModal table th, #addToCartModal table td {
        font-size: 14px; /* Increase font size for better readability */
    }
    
    /* Main Table Border Styles */
    .main-table {
        border-collapse: collapse;
    }
    
    .main-table tr {
        border-bottom: 1px solid #cccccc;
    }
    
    /* Screen Display */
    @media screen {
        #printContainer {
            border: 1px solid black; /* Visual border for preview */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
    }
</style>
