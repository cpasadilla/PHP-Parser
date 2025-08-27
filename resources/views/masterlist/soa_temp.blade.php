<x-app-layout>
    @php
        // Check if customer is eligible for 5% discount
        $eligibleCustomerIds = [1001, 1002, 1003, 1004, 1005];
        $isEligible = in_array($customer->id, $eligibleCustomerIds);
        $discountedFreight = 0;
        $discountAmount = 0;
        $discountedTotal = 0;

        // SOA number is now passed from controller (either existing or empty for manual entry)
    @endphp
    
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
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">
                M/V Everwin Star {{ $ship }} - Voyage {{ htmlspecialchars_decode($voyage, ENT_QUOTES) }} 
                ({{ $origin }} to {{ $destination }})
            </h3>
            <div class="flex gap-2">
                @if($isEligible)
                <button id="toggleDiscountBtn" class="btn btn-success px-4 py-2" onclick="toggleDiscount()">
                    <i class="fas fa-percentage me-2"></i>
                    <span id="discountBtnText">Discount Activated</span>
                </button>
                @endif
                <button id="calculatePenaltyBtn" class="btn btn-warning px-4 py-2" onclick="calculatePenalty()">
                    Calculate 1% Penalty
                </button>
                <button class="btn btn-primary" onclick="printContent('printContainer')">PRINT</button>
            </div>
        </div>
        
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white print-container" style="font-family: Arial, sans-serif;" data-ship="{{ $ship }}" data-voyage="{{ htmlspecialchars_decode($voyage, ENT_QUOTES) }}">
            <div class="main-content">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 20px;">
                <img style="width: 250px; height: 45px;" src="{{ asset('images/logo-sfx.png') }}" alt="Logo">
                <div style="font-size: 12px; line-height: 1; margin-top: 3px;">
                    <p style="margin: 0;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                    <p style="margin: 0;">Cellphone No.: 0999-889-5851</p>
                    <p style="margin: 0;">Email Address: fxavier_2015@yahoo.com.ph</p>
                    <p style="margin: 0;">TIN: 009-081-111-000</p>
                </div>
            </div>
              <div style="display: flex; justify-content: center; margin: 5px 0; line-height: 0;">
                <span style="font-weight: bold; font-size: 17px;">STATEMENT OF ACCOUNT</span>
            </div>
            <div style="margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 12px; line-height: 0;">
                <div style="width: 60%;">
                    <p style="margin-bottom: 0; line-height: 1;"><strong>BILLED TO:</strong> <span style="padding: 2px 5px;"> {{ !empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}</span></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong>VESSEL:</strong> M/V EVERWIN STAR {{ $ship }}</p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong>VOYAGE NO.:</strong> {{ htmlspecialchars_decode($voyage, ENT_QUOTES) }} {{ $origin }} - {{ $destination }}</p>
                </div>
                <div style="width: 35%; text-align: left; line-height: 1;">
                    <p style="margin-bottom: 0; line-height: 1;"><strong>DATE:</strong> {{ date('F d, Y') }}</p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong>SOA NO.:</strong> 
                        <input type="text" id="soaNumberInput" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" placeholder="Enter SOA No." value="{{ $soaNumber }}">
                        <span id="soaNumberStatus" style="font-size: 10px; color: green; display: none; margin-left: 5px;">✓ Saved</span>
                        <button type="button" onclick="saveSoaNumber()" style="margin-left: 5px; padding: 2px 5px; background: #007cba; color: white; border: none; border-radius: 3px; font-size: 10px; cursor: pointer;" title="Test Save">💾</button>
                    </p>
                    </p>
                </div>
            </div>
            <div id="voyage-{{ $ship }}-{{ Str::slug($voyage) }}" class="accordion-content">
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="font-family: Arial, sans-serif; font-size: 11px; table-layout: fixed; width: 100%;">
            <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th style="width: 5%;" class="px-4 py-2">BL #</th>
                                <th style="width: 12%;" class="px-4 py-2">CONSIGNEE</th>
                                <th style="width: 12%;" class="px-4 py-2">SHIPPER</th>
                                <th style="width: 33%;" class="px-4 py-2">DESCRIPTION</th>
                                <th style="width: 8%;" class="px-4 py-2">FREIGHT</th>
                                <th style="width: 8%;" class="px-4 py-2">VALUATION</th>
                                <th style="width: 8%;" class="px-4 py-2">WHARFAGE</th>
                                <th style="width: 8%;" class="px-4 py-2">PADLOCK FEE</th>
                                <th style="width: 8%;" class="px-4 py-2">PPA MANILA</th>
                <th style="width: 8%;" class="px-4 py-2">TOTAL</th>
                <th style="width: 4%;" class="px-2 py-2">&nbsp;</th>
                            </tr>
                        </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">                            @php 
                                $voyageTotal = 0;
                                $voyageFreight = 0;
                                $voyageValuation = 0;
                                $voyageWharfage = 0;
                                $voyageInterest = 0;
                                $voyagePadlockFee = 0;
                                $voyagePpaManila = 0;
                            @endphp
                            @foreach($orders as $order)
                                @php
                                    $interestAmount = 0;                                    // Calculate interest if interest_start_date is set
                                    if ($order->interest_start_date) {
                                        $startDate = \Carbon\Carbon::parse($order->interest_start_date);
                                        $currentDate = \Carbon\Carbon::now();
                                        $monthsDiff = $startDate->diffInDays($currentDate) / 30; // Convert days to months
                                        
                                        // Apply 1% per month after 30 days
                                        if ($monthsDiff > 1) {
                                            // Round down to full months past the first 30 days
                                            $monthsToCharge = floor($monthsDiff - 1);
                                            // Calculate interest: 1% per month of the total amount
                                            $interestAmount = $order->totalAmount * (0.01 * $monthsToCharge);
                                        }
                                    } else {
                                        // No interest should be calculated if interest has been deactivated
                                        $interestAmount = 0;
                                    }
                                    
                                    // Add interest to the total
                                    $totalWithInterest = $order->totalAmount + $interestAmount;
                                    $voyageInterest += $interestAmount;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 soa-order-row" style="font-family: Arial, sans-serif; font-size: 10px; line-height: 1.2;" data-freight="{{ $order->freight }}" data-valuation="{{ $order->valuation }}" data-wharfage="{{ $order->wharfage ?? 0 }}" data-padlock="{{ $order->padlock_fee ?? 0 }}" data-ppa="{{ $order->ppa_manila ?? 0 }}" data-interest="{{ $interestAmount }}">
                                    <td class="px-4 py-2 text-center" style="word-wrap: break-word; text-align: center;">{{ $order->orderId }}</td>
                                    <td class="px-4 py-2 text-center" style="word-wrap: break-word;">{{ $order->recName }}</td>
                                    <td class="px-4 py-2 text-center" style="word-wrap: break-word;">{{ $order->shipperName }}</td>
                                    <td class="px-4 py-2 text-center" style="word-wrap: break-word; white-space: normal;">
                                        @php 
                                            $parcelItems = [];
                                            foreach($order->parcels as $parcel) {
                                                $parcelItems[] = $parcel->quantity . ' ' . $parcel->unit . ' ' . $parcel->itemName . ' ' . $parcel->desc;
                                            }
                                            echo implode(', ', $parcelItems);
                                        @endphp
                                    </td>
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">{{ number_format($order->freight, 2) }}</td>
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">{{ number_format($order->valuation, 2) }}</td>
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">{{ number_format($order->wharfage ?? 0, 2) }}</td>
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">{{ number_format($order->padlock_fee ?? 0, 2) }}</td>
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">{{ number_format($order->ppa_manila ?? 0, 2) }}</td>
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">
                                        {{ number_format(($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0)), 2) }}
                                        @if($interestAmount > 0)
                                            <div class="text-red-600 text-xs font-semibold">
                                                +{{ number_format($interestAmount, 2) }} (interest)
                                            </div>
                                            <div class="font-bold">
                                                {{ number_format(($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0)) + $interestAmount, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded delete-row-btn" title="Remove from SOA" onclick="removeSoaRow(this)">X</button>
                                    </td>
                                </tr>
                                @php
                                $voyageTotal += ($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0)) + $interestAmount;
                                    $voyageFreight += $order->freight; 
                                    $voyageValuation += $order->valuation;
                                    $voyageWharfage += ($order->wharfage ?? 0);
                                    $voyagePadlockFee += ($order->padlock_fee ?? 0);
                                    $voyagePpaManila += ($order->ppa_manila ?? 0);
                                    $voyageInterest += $interestAmount;
                                @endphp@endforeach
                                @php
                                // Calculate discounted values if customer is eligible AND voyageFreight is at least 50,000
                                if ($isEligible && $voyageFreight >= 50000) {
                                    $discountAmount = $voyageFreight * 0.05;
                                    $discountedFreight = $voyageFreight - $discountAmount;
                                    $discountedTotal = $voyageTotal - $discountAmount;
                                } else {
                                    // No discount applied
                                    $discountAmount = 0;
                                    $discountedFreight = $voyageFreight;
                                    $discountedTotal = $voyageTotal;
                                    $isEligible = false; // Override eligibility if freight is less than 50,000
                                }
                                
                                // The padlock fee is already included in $voyageTotal and $discountedTotal
                            @endphp
                            <tr class="bg-gray-50 dark:bg-gray-900 font-semibold" style="line-height: 0.8;">
                                <td colspan="11" class="px-4 py-1 text-right"></td>
                            </tr>
                            <tr id="grandTotalRow" class="font-semibold" style="line-height: 0.8; background-color:rgb(97, 175, 91); color: black;">
                                <td colspan="4" class="px-4 py-1" style="text-align: center; font-weight: bold;">GRAND TOTAL:</td>
                                <td id="totalFreight" class="px-4 py-1 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                <td id="totalValuation" class="px-4 py-1 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                <td id="totalWharfage" class="px-4 py-1 text-right">{{ number_format($voyageWharfage, 2) }}</td>
                                <td id="totalPadlock" class="px-4 py-1 text-right">{{ number_format($voyagePadlockFee, 2) }}</td>
                                <td id="totalPpa" class="px-4 py-1 text-right">{{ number_format($voyagePpaManila, 2) }}</td>
                                <td id="totalOverall" class="px-4 py-1 text-right">{{ number_format($voyageTotal, 2) }}</td>
                                <td class="px-2 py-1"></td>
                            </tr>
                            @if($voyageInterest > 0)
                                <tr id="interestRow" class="bg-gray-50 dark:bg-gray-900 font-semibold text-red-600" style="line-height: 0.8;">
                                    <td colspan="9" class="px-4 py-1 text-right" style="word-wrap: break-word; white-space: normal;">Interest (1% per month after 30 days):</td>
                                    <td id="interestAmountCell" class="px-4 py-1 text-right">{{ number_format($voyageInterest, 2) }}</td>
                                    <td></td>
                                </tr>
                                <tr id="totalAmountDueRow" class="bg-gray-50 dark:bg-gray-900 font-semibold font-bold" style="line-height: 0.8; color: green;">
                                    <td colspan="9" class="px-4 py-1 text-right" style="word-wrap: break-word; white-space: normal;">Total Amount Due:</td>
                                    <td id="totalAmountDueValue" class="px-4 py-1 text-right ">{{ number_format($voyageTotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                            @if($isEligible)
                                <tr id="discountRow" class="font-semibold" style="line-height: 0; background-color:rgb(240, 240, 5); color: black;">
                                    <td colspan="4" class="px-4 py-2" style="text-align: left;">5% Discount on total freight if paid within <span style="font-weight: bold;">15 days</span> upon receipt of SOA</td>
                                    <td id="discountedFreightCell" style="width: 100px; font-weight: bold;" class="px-4 py-2 text-right">{{ number_format($discountedFreight, 2) }}</td>
                                    <td id="discountedValuationCell" style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                    <td id="discountedWharfageCell" style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageWharfage, 2) }}</td>
                                    <td id="discountedPadlockCell" style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyagePadlockFee, 2) }}</td>
                                    <td id="discountedPpaCell" style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyagePpaManila, 2) }}</td>
                                    <td id="discountedTotalCell" style="width: 100px; font-weight: bold;" class="px-4 py-2 text-right">{{ number_format($discountedTotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                            <tr id="penaltyInfoRow" class="font-semibold" style="line-height: 0.8; background-color:rgb(231, 15, 22); color: white;">
                                <td colspan="4" class="px-4 py-1" style="text-align: left; word-wrap: break-word; white-space: normal; color: white;">a PENALTY rate of 1% PER MONTH will be applied to total bills if not paid every after 30days</td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right" style="color: white;">
                                    <span id="finalAmount" class="font-bold" style="color: white;">
                                        @if($voyageInterest > 0)
                                            {{ number_format($voyageTotal, 2) }}
                                        @else
                                            @if($isEligible)
                                                {{ number_format($discountedTotal, 2) }}
                                            @else
                                                {{ number_format($voyageTotal, 2) }}
                                            @endif
                                        @endif
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

            <!-- Signature section for screen display -->
            <div class="signature-section">
                <div style="margin: -1px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 60%;">
                        <p style="margin: 0; line-height: 2;"><strong>PREPARED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">CHERRY MAE E. CAMAYA</strong></p>
                        <p style="margin-top: 0; margin-left: 145px; font-size: 13px; line-height: 1;">Billing Officer</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">Kindly settle your account at St. Francis office, or by bank</strong></p>
                        <p style="margin-top: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">transfer using the details below:</strong></p>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 2;"><strong>RECEIVED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 100px; font-size: 13px; line-height: 1;">Signature over Printed Name</p>
                        <p style="margin-bottom: 0; line-height: 0;"><strong>DATE:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 150px; font-size: 13px; line-height: 1;">MM/DD/YR</p>
                    </div>
                </div>
                <div style="margin: 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 35%;">
                        <table style="border-collapse: collapse; width: 100%; font-size: 12px; line-height: 1;">
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>ACCOUNT NAME:</strong></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;">St. Francis Xavier Star Shipping Lines, Inc.</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>ACCOUNT NUMBER:</strong></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>PNB:</strong> 2277-7000-1147</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>LBP:</strong> 1082-1039-76</td>
                            </tr>
                        </table>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong></strong></p>
                    </div>
                </div>
            </div>

            <!-- Print-only signature section that will stick to bottom -->
            <div class="print-signature" style="display: none;">
                <div style="margin: -1px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 60%;">
                        <p style="margin: 0; line-height: 2;"><strong>PREPARED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">CHERRY MAE E. CAMAYA</strong></p>
                        <p style="margin-top: 0; margin-left: 145px; font-size: 13px; line-height: 1;">Billing Officer</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">Kindly settle your account at St. Francis office, or by bank</strong></p>
                        <p style="margin-top: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">transfer using the details below:</strong></p>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 2;"><strong>RECEIVED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 100px; font-size: 13px; line-height: 1;">Signature over Printed Name</p>
                        <p style="margin-bottom: 0; line-height: 0;"><strong>DATE:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 150px; font-size: 13px; line-height: 1;">MM/DD/YR</p>
                    </div>
                </div>
                <div style="margin: 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 35%;">
                        <table style="border-collapse: collapse; width: 100%; font-size: 12px; line-height: 1;">
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>ACCOUNT NAME:</strong></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;">St. Francis Xavier Star Shipping Lines, Inc.</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>ACCOUNT NUMBER:</strong></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>PNB:</strong> 2277-7000-1147</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 5px; line-height: 1;"><strong>LBP:</strong> 1082-1039-76</td>
                            </tr>
                        </table>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div><br>

    <!-- For printing -->    <script>
        function printContent(divId) {
            console.log("printContent function called");
            var printContainer = document.getElementById(divId);
            if (!printContainer) {
                console.error("Print container not found");
                return;
            }

            // Fix input fields before printing to maintain font consistency and preserve values
            var inputs = printContainer.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.style.fontFamily = "Arial, sans-serif";
                if (!input.style.fontSize) {
                    input.style.fontSize = "12px";
                }
                // Ensure input value is preserved in the value attribute for printing
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             input.setAttribute('value', input.value);
            });

            // Clone the print container to measure ONLY the main content
            var tempContainer = printContainer.cloneNode(true);
            
            // Preserve input values in the cloned content
            var originalInputs = printContainer.querySelectorAll('input');
            var clonedInputs = tempContainer.querySelectorAll('input');
            originalInputs.forEach(function(originalInput, index) {
                if (clonedInputs[index]) {
                    clonedInputs[index].value = originalInput.value;
                    clonedInputs[index].setAttribute('value', originalInput.value);
                }
            });
            
            tempContainer.style.position = 'absolute';
            tempContainer.style.visibility = 'hidden';
            tempContainer.style.width = '210mm'; // A4 width
            tempContainer.style.padding = '0.5in'; // Page margins
            tempContainer.style.fontFamily = 'Arial, sans-serif';
            document.body.appendChild(tempContainer);

            // Hide BOTH signature sections to measure only main content
            var tempSignatureSection = tempContainer.querySelector('.signature-section');
            var tempPrintSignature = tempContainer.querySelector('.print-signature');
            if (tempSignatureSection) tempSignatureSection.style.display = 'none';
            if (tempPrintSignature) tempPrintSignature.style.display = 'none';

            // Measure only the main content height
            var mainContentHeight = tempContainer.offsetHeight;
            
            // Now add the signature to measure total height
            if (tempPrintSignature) {
                tempPrintSignature.style.display = 'block';
                tempPrintSignature.style.position = 'relative';
                tempPrintSignature.style.marginTop = '20px';
            }
            var totalContentHeight = tempContainer.offsetHeight;

            // Calculate if main content + signature fits on one page
            // A4 landscape height minus margins = ~210mm - 25.4mm = ~185mm ≈ 700px
            var maxSinglePageHeight = 700; // Reduced for landscape
            var needsPageBreak = totalContentHeight > maxSinglePageHeight;

            // Remove temp container
            document.body.removeChild(tempContainer);

            console.log('Main content height:', mainContentHeight);
            console.log('Total content height:', totalContentHeight);
            console.log('Max page height (landscape):', maxSinglePageHeight);
            console.log('Needs page break:', needsPageBreak);

            // Ensure all input values are set in the value attribute before getting innerHTML
            var allInputs = printContainer.querySelectorAll('input');
            allInputs.forEach(function(input) {
                input.setAttribute('value', input.value);
            });

            // Create a clone of the print container for print content
            var printClone = printContainer.cloneNode(true);
            
            // Replace the SOA number input with a span containing the current value for print
            var soaInputInClone = printClone.querySelector('#soaNumberInput');
            var originalSoaInput = printContainer.querySelector('#soaNumberInput');
            if (soaInputInClone && originalSoaInput) {
                var currentSoaValue = originalSoaInput.value;
                var soaSpan = document.createElement('span');
                soaSpan.textContent = currentSoaValue;
                soaSpan.style.fontFamily = 'Arial, sans-serif';
                soaSpan.style.fontSize = '12px';
                soaSpan.style.fontWeight = 'normal';
                soaSpan.style.color = 'black';
                soaInputInClone.parentNode.replaceChild(soaSpan, soaInputInClone);
                console.log('Replaced SOA input with span containing value:', currentSoaValue);
            }
            
            // Remove the status span and test button from print content
            var statusSpan = printClone.querySelector('#soaNumberStatus');
            if (statusSpan) {
                statusSpan.remove();
            }
            var testButton = printClone.querySelector('button[title="Test Save"]');
            if (testButton) {
                testButton.remove();
            }

            // Get print content from the modified clone
            var printContents = printClone.innerHTML;
            
            // Log the SOA number for debugging
            console.log('SOA number in print content:', originalSoaInput ? originalSoaInput.value : 'Not found');

            // Open new print window
            var printWindow = window.open("", "", "width=1000,height=800");
            printWindow.document.write("<html><head><title>Statement of Account</title>");
            printWindow.document.write("<style>");
            printWindow.document.write(`
                @page {
                    size: A4 landscape;
                    margin: 0.5in;
                }
                @media print {
                    html, body { 
                        margin: 0; 
                        padding: 0; 
                        -webkit-print-color-adjust: exact; 
                        print-color-adjust: exact; 
                        font-family: Arial, sans-serif; 
                    }
                    .print-container {
                        position: relative;
                        width: 100%;
                        ${needsPageBreak ? '' : 'min-height: calc(100vh - 1in);'}
                    }
                    .main-content {
                        position: relative;
                        width: 100%;
                    }
                    .signature-section {
                        display: none !important;
                    }
                    .print-signature {
                        display: block !important;
                        position: relative; 
                        margin-top: 20px; 
                        width: 100%;
                        page-break-inside: avoid;
                    }
                    img { 
                        display: block !important; 
                        margin: 0 auto; 
                        max-width: 300px;
                    }
                    table { 
                        border-collapse: collapse; 
                        width: 100%; 
                        font-family: Arial, sans-serif; 
                        table-layout: auto;
                        page-break-inside: auto;
                    }
                    thead {
                        display: table-header-group;
                        background-color: #f2f2f2 !important;
                    }
                    tbody {
                        display: table-row-group;
                    }
                    tbody tr {
                        page-break-inside: avoid;
                        break-inside: avoid;
                    }
                    /* Natural page breaks for long tables */
                    .accordion-content {
                        page-break-inside: auto;
                        margin-bottom: 20px;
                    }
                    th, td { 
                        border: 1px solid #ddd; 
                        padding: 3px 6px; 
                        font-family: Arial, sans-serif; 
                        word-wrap: break-word; 
                        white-space: normal; 
                        line-height: 1.2; 
                        font-size: 10px;
                    }
                    th {
                        font-size: 10px;
                        font-weight: bold;
                    }
                    button { display: none; }
                    .non-printable { display: none; }
                    input { 
                        font-family: Arial, sans-serif; 
                        font-size: 12px; 
                        border: none !important; 
                        background: transparent !important; 
                        outline: none !important;
                        color: black !important;
                        -webkit-appearance: none !important;
                        -moz-appearance: none !important;
                        appearance: none !important;
                    }
                    span {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        color: black !important;
                    }
                    #soaNumberStatus { display: none !important; }
                    button[title="Test Save"] { display: none !important; }
                    .description-cell { word-wrap: break-word; white-space: normal; }
                }
                @media screen {
                    .print-signature { display: none !important; }
                    .signature-section { display: block !important; }
                }
            `);
            printWindow.document.write("</style></head><body>");
            printWindow.document.write(printContents);
            printWindow.document.write("</body></html>");
            printWindow.document.close();
            printWindow.focus();

            // Print and close
            printWindow.onload = function () {
                printWindow.print();
                printWindow.close();
            };
        }        // Variable to track if discount is active
        let discountActive = false;

        // Function to toggle between original and discounted totals
        function toggleDiscount() {
            const discountBtnText = document.getElementById('discountBtnText');
            const toggleDiscountBtn = document.getElementById('toggleDiscountBtn');
            discountActive = !discountActive;

            if (discountActive) {
                if (discountBtnText) discountBtnText.innerHTML = '<i class="fas fa-times me-2"></i>Discount Activated';
                if (toggleDiscountBtn) {
                    toggleDiscountBtn.className = 'btn btn-success px-4 py-2';
                    toggleDiscountBtn.style.backgroundColor = '#28a745';
                    toggleDiscountBtn.style.borderColor = '#28a745';
                }
            } else {
                if (discountBtnText) discountBtnText.innerHTML = '<i class="fas fa-percentage me-2"></i>Discount Deactivated';
                if (toggleDiscountBtn) {
                    toggleDiscountBtn.className = 'btn btn-danger px-4 py-2';
                    toggleDiscountBtn.style.backgroundColor = '#dc3545';
                    toggleDiscountBtn.style.borderColor = '#dc3545';
                }
            }

            // Recalculate totals to reflect discount state
            recalcTotals();
        }

        // Penalty functionality
        let penaltyActive = false;
        let currentPenaltyAmount = 0;

        function calculatePenalty() {
            document.getElementById('penaltyModal').classList.remove('hidden');
            updatePenaltyCalculation();
        }

        function closePenaltyModal() {
            document.getElementById('penaltyModal').classList.add('hidden');
        }

        // Helper to parse a formatted number string like "1,234.56" into Number
        function parseFormattedNumber(str) {
            if (!str) return 0;
            return parseFloat(String(str).replace(/,/g, '')) || 0;
        }

        // Determine the current base amount for penalty calculations.
        // Prefer the live computed final amount (which reflects discounts and row removals).
        function getCurrentBaseAmount() {
            const finalEl = document.getElementById('finalAmount');
            if (finalEl) {
                return parseFormattedNumber(finalEl.textContent || finalEl.innerText);
            }
            // Fallback to server-side totals
            return {{ $voyageTotal }};
        }

        function updatePenaltyCalculation() {
            const months = parseInt(document.getElementById('penaltyMonths').value) || 1;
            const baseTotal = getCurrentBaseAmount();
            const penaltyAmount = baseTotal * 0.01 * months;
            const totalWithPenalty = baseTotal + penaltyAmount;

            const penaltyAmountEl = document.getElementById('penaltyAmount');
            const totalWithPenaltyEl = document.getElementById('totalWithPenalty');
            const baseAmountEl = document.getElementById('penaltyBaseAmount');

            if (baseAmountEl) baseAmountEl.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(baseTotal);
            if (penaltyAmountEl) penaltyAmountEl.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(penaltyAmount);
            if (totalWithPenaltyEl) totalWithPenaltyEl.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(totalWithPenalty);
        }

        function applyPenalty() {
            const months = parseInt(document.getElementById('penaltyMonths').value) || 1;
            const baseTotal = getCurrentBaseAmount();
            currentPenaltyAmount = baseTotal * 0.01 * months;
            penaltyActive = true;

            updateFinalAmount();
            closePenaltyModal();

            // Update button text to show penalty is applied
            const penaltyBtn = document.getElementById('calculatePenaltyBtn');
            if (penaltyBtn) {
                penaltyBtn.innerHTML = 'Remove 1% Penalty';
                penaltyBtn.onclick = removePenalty;
                penaltyBtn.className = 'btn btn-danger px-4 py-2';
            }
        }

        function removePenalty() {
            penaltyActive = false;
            currentPenaltyAmount = 0;
            
            updateFinalAmount();
            
            // Reset button
            const penaltyBtn = document.getElementById('calculatePenaltyBtn');
            penaltyBtn.innerHTML = 'Calculate 1% Penalty';
            penaltyBtn.onclick = calculatePenalty;
            penaltyBtn.className = 'btn btn-warning px-4 py-2';
        }

        function updateFinalAmount() {
            const finalAmountElement = document.getElementById('finalAmount');
            // Start from the live computed base (which reflects discount and removed rows)
            const baseTotal = getCurrentBaseAmount();
            let finalTotal = baseTotal;

            if (penaltyActive) {
                finalTotal += currentPenaltyAmount;
            }

            if (finalAmountElement) {
                finalAmountElement.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(finalTotal);
            }
        }

        // Event listener for penalty months input
        document.getElementById('penaltyMonths').addEventListener('input', updatePenaltyCalculation);

        // SOA Number auto-save functionality
        let soaNumberTimeout;
        
        function saveSoaNumber() {
            const soaInputElement = document.getElementById('soaNumberInput');
            const soaNumber = soaInputElement.value;
            const statusElement = document.getElementById('soaNumberStatus');
            
            console.log('Attempting to save SOA number:', soaNumber);
            
            // Show saving status
            statusElement.textContent = '💾 Saving...';
            statusElement.style.color = 'orange';
            statusElement.style.display = 'inline';
            
            fetch('{{ route("update-soa-number") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    customer_id: {{ $customer->id }},
                    ship: '{{ $ship }}',
                    voyage: '{{ $voyage }}',
                    soa_number: soaNumber
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Update the input field value to ensure it's saved and visible for print
                    soaInputElement.value = soaNumber;
                    soaInputElement.setAttribute('value', soaNumber);
                    
                    // Also update the defaultValue property to ensure consistency
                    soaInputElement.defaultValue = soaNumber;
                    
                    statusElement.textContent = '✓ Saved';
                    statusElement.style.color = 'green';
                    
                    // Log for debugging
                    console.log('SOA number saved successfully and input updated for print:', soaNumber);
                    console.log('Input value after save:', soaInputElement.value);
                    console.log('Input value attribute after save:', soaInputElement.getAttribute('value'));
                    
                    setTimeout(() => {
                        statusElement.style.display = 'none';
                    }, 2000);
                } else {
                    statusElement.textContent = '✗ Error';
                    statusElement.style.color = 'red';
                    console.error('Error saving SOA number:', data.message);
                }
            })
            .catch(error => {
                statusElement.textContent = '✗ Error';
                statusElement.style.color = 'red';
                console.error('Error saving SOA number:', error);
            });
        }
        
        // Add event listener for SOA number input
        document.addEventListener('DOMContentLoaded', function() {
            const soaInput = document.getElementById('soaNumberInput');
            if (soaInput) {
                console.log('SOA input element found, attaching event listener');
                soaInput.addEventListener('input', function() {
                    console.log('Input event triggered, value:', this.value);
                    // Clear existing timeout
                    clearTimeout(soaNumberTimeout);
                    
                    // Set new timeout to save after 1 second of no typing
                    soaNumberTimeout = setTimeout(saveSoaNumber, 1000);
                });
            } else {
                console.error('SOA number input element not found');
            }
            // Initialize recalculation after DOM load to ensure JS totals align
            recalcTotals();
        });

        // Remove a row from the SOA (front-end only) and recalculate totals
        function removeSoaRow(btn) {
            const row = btn.closest('tr.soa-order-row');
            if(!row) return;
            row.parentNode.removeChild(row);
            recalcTotals();
        }

        function formatNumber(val, digits=2) {
            return new Intl.NumberFormat('en-US', {minimumFractionDigits: digits, maximumFractionDigits: digits}).format(val);
        }

        function recalcTotals() {
            const rows = document.querySelectorAll('tr.soa-order-row');
            let freight=0, valuation=0, wharfage=0, padlock=0, ppa=0, interest=0;
            rows.forEach(r=>{
                freight += parseFloat(r.dataset.freight)||0;
                valuation += parseFloat(r.dataset.valuation)||0;
                wharfage += parseFloat(r.dataset.wharfage)||0;
                padlock += parseFloat(r.dataset.padlock)||0;
                ppa += parseFloat(r.dataset.ppa)||0;
                interest += parseFloat(r.dataset.interest)||0;
            });
            const total = freight + valuation + wharfage + padlock + ppa + interest;

            // Update grand total row
            const setText = (id,val)=>{ const el=document.getElementById(id); if(el) el.textContent = formatNumber(val); };
            setText('totalFreight', freight);
            setText('totalValuation', valuation);
            setText('totalWharfage', wharfage);
            setText('totalPadlock', padlock);
            setText('totalPpa', ppa);
            setText('totalOverall', total);

            // Discount logic: must meet original eligibility list & freight >= 50000
            const discountRow = document.getElementById('discountRow');
            const eligibleCustomerIds = @json($eligibleCustomerIds);
            const customerEligible = eligibleCustomerIds.includes({{ $customer->id }});
            let discountAmountLocal = 0;
            let discountedFreightLocal = freight;
            let discountedTotalLocal = total;
            if (customerEligible && freight >= 50000) {
                discountAmountLocal = freight * 0.05;
                discountedFreightLocal = freight - discountAmountLocal;
                discountedTotalLocal = total - discountAmountLocal; // interest already in total; discount only on freight

                if (discountRow) {
                    discountRow.style.display = '';

                    // Prefer updating by IDs (more robust than td indexes)
                    const df = document.getElementById('discountedFreightCell');
                    const dv = document.getElementById('discountedValuationCell');
                    const dw = document.getElementById('discountedWharfageCell');
                    const dp = document.getElementById('discountedPadlockCell');
                    const dpv = document.getElementById('discountedPpaCell');
                    const dt = document.getElementById('discountedTotalCell');

                    if (df) df.textContent = formatNumber(discountedFreightLocal);
                    if (dv) dv.textContent = formatNumber(valuation);
                    if (dw) dw.textContent = formatNumber(wharfage);
                    if (dp) dp.textContent = formatNumber(padlock);
                    if (dpv) dpv.textContent = formatNumber(ppa);
                    if (dt) dt.textContent = formatNumber(discountedTotalLocal);
                }
            } else {
                if (discountRow) discountRow.style.display = 'none';
                discountedTotalLocal = total; // no discount
            }

            // Interest rows
            const interestRow = document.getElementById('interestRow');
            const interestAmountCell = document.getElementById('interestAmountCell');
            const totalAmountDueRow = document.getElementById('totalAmountDueRow');
            const totalAmountDueValue = document.getElementById('totalAmountDueValue');
            if(interest > 0) {
                if(interestRow) interestRow.style.display='';
                if(interestAmountCell) interestAmountCell.textContent = formatNumber(interest);
                if(totalAmountDueRow) totalAmountDueRow.style.display='';
                if(totalAmountDueValue) totalAmountDueValue.textContent = formatNumber(total);
            } else {
                if(interestRow) interestRow.style.display='none';
                if(totalAmountDueRow) totalAmountDueRow.style.display='none';
            }

            // Final amount (penalty row)
            const finalAmountEl = document.getElementById('finalAmount');
            if(finalAmountEl) {
                // Decide the current base (after discount if active)
                const baseUsed = (customerEligible && discountActive) ? discountedTotalLocal : total;

                // If a penalty is already active, recompute penalty on the new base and update display
                if (penaltyActive) {
                    // Read months from the input (default to 1)
                    let months = 1;
                    try {
                        const monthsEl = document.getElementById('penaltyMonths');
                        months = monthsEl ? (parseInt(monthsEl.value) || 1) : 1;
                    } catch (e) { months = 1; }

                    // Recalculate current penalty amount based on new base
                    currentPenaltyAmount = baseUsed * 0.01 * months;

                    // Update final amount to include the freshly computed penalty
                    finalAmountEl.textContent = formatNumber(baseUsed + currentPenaltyAmount);

                    // Update penalty modal values (if open) so Modal shows fresh numbers
                    try { updatePenaltyCalculation(); } catch (e) { /* noop */ }
                } else {
                    // No penalty applied, show base only
                    finalAmountEl.textContent = formatNumber(baseUsed);
                }
            }
        }
    </script>

    <!-- Penalty Calculation Modal -->
    <div id="penaltyModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96 mx-4">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">1% Monthly Penalty Calculation</h3>
            
            <div class="mb-4">
                <label for="penaltyMonths" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of months overdue:</label>
                <input type="number" id="penaltyMonths" min="1" max="12" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                <p class="text-sm text-gray-600 dark:text-gray-400">Base Amount: <span id="penaltyBaseAmount" class="font-semibold">{{ number_format($voyageTotal, 2) }}</span></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Penalty Amount: <span id="penaltyAmount" class="font-semibold">0.00</span></p>
                <p class="text-sm text-gray-900 dark:text-gray-100 font-semibold">Total with Penalty: <span id="totalWithPenalty" class="text-red-600">{{ number_format($voyageTotal, 2) }}</span></p>
            </div>
            
            <div class="flex justify-end gap-2">
                <button onclick="closePenaltyModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                <button onclick="applyPenalty()" class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">Apply Penalty</button>
            </div>
        </div>
    </div>
</x-app-layout>
