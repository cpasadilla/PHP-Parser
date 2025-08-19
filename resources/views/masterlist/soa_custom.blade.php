<x-app-layout>
    @php
        // Check if customer is eligible for 5% discount
        $eligibleCustomerIds = [1001, 1002, 1003, 1004, 1005];
        $isEligible = in_array($customer->id, $eligibleCustomerIds);
        $discountedFreight = 0;
        $discountAmount = 0;
        $discountedTotal = 0;
        
        // Initialize voyage totals
        $voyageTotal = 0;
        $voyageFreight = 0;
        $voyageValuation = 0;
        $voyageWharfage = 0;
        $voyageInterest = 0;
        $voyagePadlockFee = 0;
        $voyagePpaManila = 0;
        
        // Calculate totals from orders
        foreach($orders as $order) {
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
            }
            
            $voyageTotal += ($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0)) + $interestAmount;
            $voyageFreight += $order->freight; 
            $voyageValuation += $order->valuation;
            $voyageWharfage += ($order->wharfage ?? 0);
            $voyagePadlockFee += ($order->padlock_fee ?? 0);
            $voyagePpaManila += ($order->ppa_manila ?? 0);
            $voyageInterest += $interestAmount;
        }
        
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

        // SOA number is now passed from controller (either existing or empty for manual entry)
        
        // Function to convert number to words
        function numberToWords($number) {
            $number = number_format($number, 2, '.', '');
            $parts = explode('.', $number);
            $pesos = (int)$parts[0];
            $centavos = (int)$parts[1];
            
            $ones = array(
                '', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE',
                'TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN',
                'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'
            );
            
            $tens = array(
                '', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'
            );
            
            $result = '';
            
            // Handle millions
            if ($pesos >= 1000000) {
                $millions = intval($pesos / 1000000);
                
                // Convert millions group
                if ($millions >= 100) {
                    $result .= $ones[intval($millions / 100)] . ' HUNDRED ';
                    $millions %= 100;
                }
                if ($millions >= 20) {
                    $result .= $tens[intval($millions / 10)] . ' ';
                    $millions %= 10;
                }
                if ($millions > 0) {
                    $result .= $ones[$millions] . ' ';
                }
                
                $result .= 'MILLION ';
                $pesos %= 1000000;
            }
            
            // Handle thousands
            if ($pesos >= 1000) {
                $thousands = intval($pesos / 1000);
                
                // Convert thousands group
                if ($thousands >= 100) {
                    $result .= $ones[intval($thousands / 100)] . ' HUNDRED ';
                    $thousands %= 100;
                }
                if ($thousands >= 20) {
                    $result .= $tens[intval($thousands / 10)] . ' ';
                    $thousands %= 10;
                }
                if ($thousands > 0) {
                    $result .= $ones[$thousands] . ' ';
                }
                
                $result .= 'THOUSAND ';
                $pesos %= 1000;
            }
            
            // Handle hundreds, tens, and ones
            if ($pesos >= 100) {
                $result .= $ones[intval($pesos / 100)] . ' HUNDRED ';
                $pesos %= 100;
            }
            if ($pesos >= 20) {
                $result .= $tens[intval($pesos / 10)] . ' ';
                $pesos %= 10;
            }
            if ($pesos > 0) {
                $result .= $ones[$pesos] . ' ';
            }
            
            if (empty(trim($result))) {
                $result = 'ZERO';
            }
            
            $result = trim($result) . ' PESOS';
            
            if ($centavos > 0) {
                $result .= ' & ' . sprintf('%02d', $centavos) . '/100';
            } else {
                $result .= ' & 00/100';
            }
            
            $result .= ' ONLY';
            
            return $result;
        }
        
        // Get the final amount to convert to words
        $finalAmountForWords = 0;
        if ($voyageInterest > 0) {
            $finalAmountForWords = $voyageTotal + $voyageInterest;
        } else {
            if ($isEligible) {
                $finalAmountForWords = $discountedTotal;
            } else {
                $finalAmountForWords = $voyageTotal;
            }
        }
        
        $amountInWords = numberToWords($finalAmountForWords);
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
                        <p style="margin: 0; font-size: 23px; font-weight: bold; color: #00B050; padding: 2px 5px;">ST. FRANCIS XAVIER STAR SHIPPING LINES, INC.</p>
                        <p style="margin: 0; font-size: 17px; padding: 2px 5px;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                        <p style="margin: 0; font-size: 17px; padding: 2px 5px; font-style: italic;"> Vat Reg. TIN: 009-081-111-000 CP No: 0999-889-5851    Email Add: fxavier_2015@yahoo.com.ph</p>
                        <p style="margin: 0; margin-bottom: 0; padding: 2px 5px; line-height: 1; font-size: 20px; color: blue; text-decoration: underline;">STATEMENT OF ACCOUNT</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; margin: 0; border: 1px solid #000; border-top: none; text-align: right; background-color: #A9D08E;">
                    <p style="margin-bottom: 0; line-height: 1; font-size: 16px;"><strong>SOA NO.:</strong> 
                        <input type="text" id="soaNumberInput" style="border: 1px solid #ccc; padding: 2px 5px 2px 5px; padding-right: 50px; width: 120px; font-family: Arial, sans-serif; font-size: 12px; color: red;" placeholder="Enter SOA No." value="{{ $soaNumber ?? '' }}">
                        <span id="soaNumberStatus" style="font-size: 12px; color: green; display: none; margin-left: 5px;">✓ Saved</span>
                        <button type="button" onclick="saveSoaNumber()" style="margin-left: 5px; padding: 2px 5px; background: #007cba; color: white; border: none; border-radius: 3px; font-size: 10px; cursor: pointer;" title="Test Save">💾</button>
                    </p>
                </div>
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0; margin-bottom: 0; border: 1px solid #000; border-top: none; padding: 0;">
                    <div style="margin-left : 10px; flex: 0 0 8%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 12px; line-height: 1.2; font-weight: bold;">BILLED TO: </p>
                        <p style="margin: 0; font-size: 12px; line-height: 1.2; font-weight: bold;">ADDRESS:</p>
                        <p style="margin: 0; font-size: 12px; line-height: 1.2; font-weight: bold;">CONSIGNEE:</p>
                    </div>
                    <div style="flex: 0 0 92%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 13px;"><span style="padding: 2px 5px; font-family: 'Courier New', 'Consolas', 'Monaco', monospace;">{{ !empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}</span></p>
                        <p style="margin: 0; font-size: 13px;"><span style="padding: 2px 5px; font-family: 'Courier New', 'Consolas', 'Monaco', monospace;">BASCO, BATANES</span></p>
                        <p style="margin: 0; font-size: 13px;"><span style="padding: 2px 5px; line-height: 1.2; font-family: 'Courier New', 'Consolas', 'Monaco', monospace;">{{ collect($orders)->pluck('recName')->filter()->unique()->implode(', ') }}</span></p>
                    </div>
                </div>
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; padding: 0;">
                    <div style="flex: 0 0 20%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;">VESSEL</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; flex: 1;">M/V EVERWIN STAR {{ $ship }}</p>
                    </div>
                    <div style="flex: 0 0 25%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;">VOYAGE #</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; flex: 1;">{{ htmlspecialchars_decode($voyage, ENT_QUOTES) }} {{ $origin }} - {{ $destination }}</p>
                    </div>
                    <div style="flex: 0 0 10%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;">BL #</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; flex: 1; word-wrap: break-word; word-break: break-all;">
                            @foreach($orders as $order)
                                {{ $order->orderId }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>
                    <div style="flex: 0 0 45%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;">DESCRIPTION</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; flex: 1; word-wrap: break-word; word-break: break-word;">
                            @php 
                                $parcelItems = [];
                                foreach($orders as $order) {
                                    foreach($order->parcels as $parcel) {
                                        $parcelItems[] = trim($parcel->quantity . ' ' . $parcel->unit . ' ' . $parcel->itemName . ' ' . $parcel->desc);
                                    }
                                }
                                echo implode(', ', array_filter($parcelItems));
                            @endphp
                        </p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; padding: 0;">
                    <div style="flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; background-color: #A9D08E; font-weight: bold; border-bottom: 1px solid #A9D08E; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">SAY IN PESOS: </p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; background-color: #A9D08E; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 6px; display: flex; align-items: center; justify-content: center;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 39px; display: flex; align-items: center; justify-content: center; font-family: 'Courier New', 'Consolas', 'Monaco', monospace; text-transform: uppercase;">**{{ $amountInWords }}**</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 6px; display: flex; align-items: center; justify-content: center;"></p>
                        
                    </div>
                    <div style="flex: 0 0 20%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">FREIGHT :</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">INSURANCE :</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">ARRASTRE MNL :</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">WHARFAGE :</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left; background-color: #ffff38ff;">GRAND TOTAL :</p>
                    </div>
                    <div style="flex: 0 0 20%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">
                            {{ number_format($voyageFreight, 2) }}
                        </p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">
                            {{ number_format($voyageValuation, 2) }}
                        </p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">
                            {{ number_format($voyagePpaManila, 2) }}
                        </p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">
                            {{ number_format($voyageWharfage, 2) }}
                        </p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right; background-color: #ffff38ff;">
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
                        </p>
                        
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; padding: 0;">
                    <div style="flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; background-color: #A9D08E; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">PAYMENT INSTRUCTIONS:</p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; background-color: #A9D08E; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; padding: 0;">
                    <div style="flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left; color: red; text-decoration: underline;">Kindly settle your account at St. Francis office, or by bank </p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left; color: red; text-decoration: underline;">transfer using the details below:</p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; font-weight: bold; padding: 2px 5px; background-color: #A9D08E; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">PREPARED BY:</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; padding: 0;">
                    <div style="flex: 0 0 30%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; border-top: 1px solid #000;  min-height: 17px; display: flex; align-items: center; justify-content: left;">ACCOUNT NAME:</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left; font-style: italic;">St. Francis Xavier Star Shipping lines, Inc.</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">ACCOUNT NUMBER:</p>
                    </div>
                    <div style="flex: 0 0 30%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; text-decoration: underline;">CHERRY MAE E. CAMAYA</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; font-style: italic;">Billing Officer</p>
                        <p style="margin: 0; font-size: 12px; font-weight: bold; background-color: #A9D08E; padding: 2px 5px; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">RECEIVED BY:</p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; padding: 0;">
                    <div style="flex: 0 0 7.5%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">PNB:</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">LBP:</p>
                    </div>
                    <div style="flex: 0 0 22.5%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;">2277-7000-1147</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;">1082-1039-76</p>
                    </div>
                    <div style="flex: 0 0 30%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border: none; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;"></p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; padding: 0;">
                    <div style="flex: 0 0 60%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left; font-style: italic;">Attached is photocopy of BL for your reference….</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;"></p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;"></p>
                    </div>
                    <div style="flex: 0 0 40%; box-sizing: border-box; line-height: 1; text-align: center; min-height: 17px;">
                        <p style="margin: 0; font-size: 12px; background-color: #A9D08E; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; font-style: italic;">Signature Over Printed Name</p>
                        <p style="margin: 0; font-size: 12px; background-color: #A9D08E; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">DATE:</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #ffffffff; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center;">{{ date('F d, Y') }}</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-weight: bold; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;"></p>
                        <p style="margin: 0; font-size: 12px; background-color: #A9D08E; padding: 2px 5px; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; font-style: italic;">MM / DD / YR</p>
                    </div>
                </div>
            </div>





<br><br>



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
                soaSpan.style.fontSize = '18px';
                soaSpan.style.fontWeight = 'bold';
                soaSpan.style.color = 'red';
                soaSpan.style.paddingRight = '100px';
                soaSpan.setAttribute('data-soa-number', 'true'); // Add attribute for CSS targeting
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
                    /* Special styling for SOA number span */
                    span[data-soa-number] {
                        color: red !important;
                        padding-right: 100px;
                        font-size: 16px !important;
                        font-weight: bold !important;
                    }
                    /* Compact SOA section for print */
                    .print-container div[style*="background-color: #A9D08E"] {
                        line-height: 0.8 !important;
                        min-height: 12px !important;
                    }
                    .print-container div[style*="background-color: #A9D08E"] p {
                        line-height: 0.8 !important;
                        margin: 0 !important;
                        padding: 1px 5px !important;
                        font-size: 14px !important;
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
            const soaNumber = soaInputElement.value.trim();
            const statusElement = document.getElementById('soaNumberStatus');
            
            console.log('Attempting to save SOA number:', soaNumber);
            
            // Don't save if empty
            if (!soaNumber) {
                console.log('SOA number is empty, not saving');
                return;
            }
            
            // Show saving status
            statusElement.textContent = '💾 Saving...';
            statusElement.style.color = 'orange';
            statusElement.style.display = 'inline';
            
            // Prepare the data
            const requestData = {
                customer_id: {{ $customer->id }},
                ship: '{{ $ship }}',
                voyage: '{{ $voyage }}',
                soa_number: soaNumber
            };
            
            console.log('Request data:', requestData);
            console.log('Route URL:', '{{ route("update-soa-number") }}');
            
            fetch('{{ route("update-soa-number") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
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
                    }, 3000);
                } else {
                    statusElement.textContent = '✗ Error';
                    statusElement.style.color = 'red';
                    console.error('Error saving SOA number:', data.message || 'Unknown error');
                    console.error('Full response data:', data);
                    
                    setTimeout(() => {
                        statusElement.style.display = 'none';
                    }, 5000);
                }
            })
            .catch(error => {
                statusElement.textContent = '✗ Error';
                statusElement.style.color = 'red';
                console.error('Error saving SOA number:', error);
                console.error('Full error details:', {
                    message: error.message,
                    stack: error.stack,
                    requestData: requestData
                });
                
                // Show error message for longer time
                setTimeout(() => {
                    statusElement.style.display = 'none';
                }, 5000);
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
