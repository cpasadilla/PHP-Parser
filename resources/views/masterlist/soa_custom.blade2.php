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
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; border: 1px solid #000; padding: 10px;">
                    <div style="flex: 0 0 auto; display: flex; align-items: flex-start; margin-left: 40px;">
                        <img style="height: 83px; width: auto;" src="{{ asset('images/logo.png') }}" alt="Logo">
                    </div>
                    <div style="flex: 1 1 auto; line-height: 1; text-align: center; margin-right: 40px;">
                        <p style="margin: 0; font-size: 23px; font-weight: bold; color: green; padding: 2px 5px;">ST. FRANCIS XAVIER STAR SHIPPING LINES, INC.</p>
                        <p style="margin: 0; font-size: 17px; padding: 2px 5px;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                        <p style="margin: 0; font-size: 17px; padding: 2px 5px;"> Vat Reg. TIN: 009-081-111-000 CP No: 0999-889-5851    Email Add: fxavier_2015@yahoo.com.ph</p>
                        <p style="margin: 0; margin-bottom: 0; padding: 2px 5px; line-height: 1; font-size: 20px; color: blue; text-decoration: underline;">STATEMENT OF ACCOUNT</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin: 0; border: 1px solid #000; border-top: none; line-height: 0; text-align: right; background-color: #e6f7e6;">
                    <p style="margin-bottom: 0; line-height: 1; font-size: 12px;"><strong>SOA NO.:</strong> 
                        <input type="text" id="soaNumberInput" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" placeholder="Enter SOA No." value="{{ $soaNumber ?? '' }}">
                        <span id="soaNumberStatus" style="font-size: 12px; color: green; display: none; margin-left: 5px;">✓ Saved</span>
                        <button type="button" onclick="saveSoaNumber()" style="margin-left: 5px; padding: 2px 5px; background: #007cba; color: white; border: none; border-radius: 3px; font-size: 10px; cursor: pointer;" title="Test Save">💾</button>
                    </p>
                </div>
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0; margin-bottom: 0; border: 1px solid #000; border-top: none; padding: 0;">
                    <div style="margin-left : 10px; flex: 0 0 8%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 12px; font-weight: bold;">BILLED TO: </p>
                        <p style="margin: 0; font-size: 12px; font-weight: bold;">ADDRESS:</p>
                        <p style="margin: 0; font-size: 12px; font-weight: bold;">CONSIGNEE:</p>
                    </div>
                    <div style="flex: 0 0 92%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 12px;"><span style="padding: 2px 5px;">{{ !empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}</span></p>
                        <p style="margin: 0; font-size: 12px;"><span style="padding: 2px 5px;">BASCO, BATANES</span></p>
                        <p style="margin: 0; font-size: 12px;"><span style="padding: 2px 5px;">{{ collect($orders)->pluck('recName')->filter()->unique()->implode(', ') }}</span></p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; border: 1px solid #000; border-top: none; padding: 0;">
                    <div style="flex: 0 0 20%; box-sizing: border-box; line-height: 1; text-align: center; border-right: 1px solid #000;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none;">VESSEL</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none;">M/V EVERWIN STAR {{ $ship }}</p>
                    </div>
                    <div style="flex: 0 0 25%; box-sizing: border-box; line-height: 1; text-align: center; border-right: 1px solid #000;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none;">VOYAGE #</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none;">{{ htmlspecialchars_decode($voyage, ENT_QUOTES) }} {{ $origin }} - {{ $destination }}</p>
                    </div>
                    <div style="flex: 0 0 10%; box-sizing: border-box; line-height: 1; text-align: center; border-right: 1px solid #000;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none;">BL #</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none;">000</p>
                    </div>
                    <div style="flex: 0 0 45%; box-sizing: border-box; line-height: 1; text-align: center;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none;">DESCRIPTION</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px;"></p>
                    </div>
                </div>







                
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; border: 1px solid #000; border-top: none; padding: 0;">
                    <div style="flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: left; border-right: 1px solid #000;">
                        <p style="margin: 0; font-size: 12px; font-weight: bold; height: 33px; padding: 2px 5px; background-color: #e6f7e6; border: 1px solid #000; border-top: none; border-left: none; border-right: none; display: flex; align-items: center; justify-content: left; text-align: left; width: 100%;">SAY IN PESOS: </p>
                        <p style="margin: 0; font-size: 12px; height: 37.5px; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none; border-bottom: none; display: flex; align-items: center; justify-content: center; text-align: center; width: 100%;">0.00</p>
                    </div>
                    <div style="flex: 0 0 20%; box-sizing: border-box; border-bottom: none; border-right: 1px solid #000; line-height: 1; text-align: left;">
                        <p style="margin: 0; font-size: 12px; font-weight: bold; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;"> FREIGHT :	</p>
                        <p style="margin: 0; font-size: 12px; font-weight: bold; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;"> INSURANCE : </p>
                        <p style="margin: 0; font-size: 12px; font-weight: bold; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;"> WHARFAGE : </p>
                        <p style="margin: 0; font-size: 12px; font-weight: bold; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;"> STUFFING : </p>
                        <p style="margin: 0; font-size: 12px; font-weight: bold; padding: 2px 5px; border: 1px solid #000; border-top: none; border-bottom: none; border-left: none; border-right: none; background-color: #ffff38ff;"> GRAND TOTAL : </p>
                    </div>
                    <div style="flex: 0 0 20%; box-sizing: border-box; line-height: 1; text-align: right;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;">0.00</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;">0.00</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;">0.00</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: 1px solid #000; border-top: none; border-left: none; border-right: none;">0.00</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: 1px solid #000; border-top: none; border-bottom: none; border-left: none; border-right: none; background-color: #ffff38ff;">0.00</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin: 0; border: 1px solid #000; border-top: none; line-height: 0; text-align: right; background-color: #e6f7e6;">
                    <div style="padding-left: 10px; flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: left;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; background-color: #e6f7e6; border: 1px solid #000; border-top: none; border-bottom: none; border-left: none; display: flex; ">PAYMENT INSTRUCTIONS:</p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: left;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; "></p>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin: 0; border: 1px solid #000; border-top: none; border-bottom: none; line-height: 0; text-align: right; ">
                    <div style="padding-left: 10px; flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: left;">
                        <p style="margin: 0; font-size: 12px;   border: none; display: flex; ">Kindly settle your account at St. Francis office, or by bank </p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: left; background-color: #e6f7e6; ">
                        <p style="margin: 0; font-size: 12px; font-weight: bold; ">PREPARED BY:</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin: 0; border: 1px solid #000; border-top: none; line-height: 0; text-align: right;">
                    <div style="padding-left: 10px; flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: left;">
                        <p style="margin: 0; font-size: 12px; color: red; text-decoration: underline; border: none; display: flex; ">transfer using the details below: </p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: left;">
                        <p style="margin: 0; font-size: 12px; font-weight: bold; "></p>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin: 0; border: 1px solid #000; border-top: none; border-bottom: none; line-height: 0; text-align: right; ">
                    <div style="padding-left: 10px; flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: left;">
                        <p style="margin: 0; font-size: 12px; color: red; text-decoration: underline; border: 1px solid #000; border-top: none; border-bottom: none; border-left: none; display: flex; ">vdsf</p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: left; background-color: #e6f7e6; ">
                        <p style="margin: 0; font-size: 12px; font-weight: bold; ">PREPARED BY:</p>
                    </div>
                </div>
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
                        <input type="text" id="soaNumberInput" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" placeholder="Enter SOA No." value="{{ $soaNumber ?? '' }}">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php 
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
                                    $interestAmount = 0;
                                    // Calculate interest if interest_start_date is set
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
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" style="font-family: Arial, sans-serif; font-size: 10px; line-height: 1.2;">
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
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                            </tr>
                            <tr class="font-semibold" style="line-height: 0.8; background-color:rgb(97, 175, 91); color: black;">                                <td colspan="4" class="px-4 py-1" style="text-align: center; font-weight: bold;">GRAND TOTAL:</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyageWharfage, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyagePadlockFee, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyagePpaManila, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyageTotal, 2) }}</td>
                            </tr>
                            @if($voyageInterest > 0)
                                <tr class="bg-gray-50 dark:bg-gray-900 font-semibold text-red-600" style="line-height: 0.8;">
                                    <td colspan="4" class="px-4 py-1 text-right" style="word-wrap: break-word; white-space: normal;">Interest (1% per month after 30 days):</td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right">{{ number_format($voyageInterest, 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-900 font-semibold font-bold" style="line-height: 0.8; color: green;">
                                    <td colspan="4" class="px-4 py-1 text-right" style="word-wrap: break-word; white-space: normal;">Total Amount Due:</td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right"></td>
                                    <td class="px-4 py-1 text-right ">{{ number_format($voyageTotal, 2) }}</td>
                                </tr>
                            @endif
                            @if($isEligible)
                                <tr class="font-semibold" style="line-height: 0; background-color:rgb(240, 240, 5); color: black;">
                                    <td colspan="4" class="px-4 py-2" style="text-align: left;">5% Discount on total freight if paid within <span style="font-weight: bold;">15 days</span> upon receipt of SOA</td>
                                    <td style="width: 100px; font-weight: bold;" class="px-4 py-2 text-right" >{{ number_format($discountedFreight, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageWharfage, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyagePadlockFee, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyagePpaManila, 2) }}</td>
                                    <td style="width: 100px; font-weight: bold;" class="px-4 py-2 text-right">{{ number_format($discountedTotal, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="font-semibold" style="line-height: 0.8; background-color:rgb(231, 15, 22); color: white;">
                                <td colspan="4" class="px-4 py-1" style="text-align: left; word-wrap: break-word; white-space: normal; color: white;">a PENALTY rate of 1% PER MONTH will be applied to total bills if not paid every after 30days</td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right"></td>
                                <td class="px-4 py-1 text-right" style="color: white;">
                                    @if($voyageInterest > 0)
                                    <span class="font-bold" style="color: white;">1% Interest: {{ number_format($voyageInterest, 2) }}</span>
                                    @else
                                    <span id="finalAmount">
                                        @if($isEligible)
                                        {{ number_format($discountedTotal, 2) }}
                                        @else
                                        {{ number_format($voyageTotal, 2) }}
                                        @endif
                                    </span>
                                    @endif
                                </td>
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
            const finalAmountElement = document.getElementById('finalAmount');
            const discountBtnText = document.getElementById('discountBtnText');
            const toggleDiscountBtn = document.getElementById('toggleDiscountBtn');
            const discountedTotal = {{ $discountedTotal }};
            const originalTotal = {{ $voyageTotal }};
            
            if (discountActive) {
                // Deactivate discount - show original total
                finalAmountElement.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(originalTotal);
                discountBtnText.innerHTML = '<i class="fas fa-percentage me-2"></i>Discount Deactivated';
                toggleDiscountBtn.className = 'btn btn-danger px-4 py-2';
                toggleDiscountBtn.style.backgroundColor = '#dc3545';
                toggleDiscountBtn.style.borderColor = '#dc3545';
                discountActive = false;
            } else {
                // Activate discount - show discounted total
                finalAmountElement.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(discountedTotal);
                discountBtnText.innerHTML = '<i class="fas fa-times me-2"></i>Discount Activated';
                toggleDiscountBtn.className = 'btn btn-success px-4 py-2';
                toggleDiscountBtn.style.backgroundColor = '#28a745';
                toggleDiscountBtn.style.borderColor = '#28a745';
                discountActive = true;
            }
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

        function updatePenaltyCalculation() {
            const months = parseInt(document.getElementById('penaltyMonths').value) || 1;
            const baseTotal = discountActive ? {{ $discountedTotal }} : {{ $voyageTotal }};
            const penaltyAmount = baseTotal * 0.01 * months;
            const totalWithPenalty = baseTotal + penaltyAmount;
            
            document.getElementById('penaltyAmount').textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(penaltyAmount);
            document.getElementById('totalWithPenalty').textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(totalWithPenalty);
        }

        function applyPenalty() {
            const months = parseInt(document.getElementById('penaltyMonths').value) || 1;
            const baseTotal = discountActive ? {{ $discountedTotal }} : {{ $voyageTotal }};
            currentPenaltyAmount = baseTotal * 0.01 * months;
            penaltyActive = true;
            
            updateFinalAmount();
            closePenaltyModal();
            
            // Update button text to show penalty is applied
            const penaltyBtn = document.getElementById('calculatePenaltyBtn');
            penaltyBtn.innerHTML = 'Remove 1% Penalty';
            penaltyBtn.onclick = removePenalty;
            penaltyBtn.className = 'btn btn-danger px-4 py-2';
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
            let baseTotal = discountActive ? {{ $discountedTotal }} : {{ $voyageTotal }};
            let finalTotal = baseTotal;
            
            if (penaltyActive) {
                finalTotal += currentPenaltyAmount;
            }
            
            finalAmountElement.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(finalTotal);
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
        });
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
                <p class="text-sm text-gray-600 dark:text-gray-400">Base Amount: <span class="font-semibold">{{ number_format($voyageTotal, 2) }}</span></p>
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
