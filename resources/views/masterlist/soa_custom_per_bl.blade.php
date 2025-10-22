<x-app-layout>
    @php
        // Check if customer is eligible for 5% discount
        $eligibleCustomerIds = [1001, 1002, 1003, 1004, 1005];
        $isEligible = in_array($customer->id, $eligibleCustomerIds);
        $discountedFreight = 0;
        $discountAmount = 0;
        $discountedTotal = 0;
        
        // Initialize totals (this will be for a single BL/order)
        $voyageTotal = 0;
        $voyageFreight = 0;
        $voyageValuation = 0;
        $voyageWharfage = 0;
        $voyageInterest = 0;
        $voyagePadlockFee = 0;
        $voyagePpaManila = 0;
        
        // Calculate totals from orders (should be only one order)
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
                {{ __('Statement of Account (Government) - BL# ') . $orders->first()->orderId }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold dark:text-gray-200">
                M/V Everwin Star {{ $ship }} - Voyage {{ htmlspecialchars_decode($voyage, ENT_QUOTES) }} 
                ({{ $origin }} to {{ $destination }})
                <br><span class="text-blue-600">BL # {{ $orders->first()->orderId }}</span>
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
        
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white print-container" style="font-family: Arial, sans-serif;" data-ship="{{ $ship }}" data-voyage="{{ htmlspecialchars_decode($voyage, ENT_QUOTES) }}" data-order-id="{{ $orderId }}">
            <!-- Hidden per-order data for client-side recalculation -->
            <div id="soaOrderRows" style="display:none;">
                @foreach($orders as $order)
                    <div class="soa-order-row" 
                         data-freight="{{ $order->freight }}" 
                         data-valuation="{{ $order->valuation }}" 
                         data-wharfage="{{ $order->wharfage ?? 0 }}" 
                         data-padlock="{{ $order->padlock_fee ?? 0 }}" 
                         data-ppa="{{ $order->ppa_manila ?? 0 }}">
                    </div>
                @endforeach
            </div>
            <div class="main-content" style="position: relative;">
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; padding: 10px;">
                    <div style="flex: 0 0 auto; display: flex; align-items: flex-start; margin-left: 40px;">
                        <img style="height: 90px; width: auto;" src="{{ asset('images/logo.png') }}" alt="Logo">
                    </div>
                    <div style="flex: 1 1 auto; line-height: 1; text-align: center; margin-right: 40px;">
                        <p style="margin: 0; font-size: 23px; font-weight: bold; color: #00B050; padding: 2px 5px;">ST. FRANCIS XAVIER STAR SHIPPING LINES, INC.</p>
                        <p style="margin: 0; font-size: 14px; padding: 2px 5px;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                        <p style="margin: 0; font-size: 14px; padding: 2px 5px; font-style: italic;"> Vat Reg. TIN: 009-081-111-000 CP No: 0999-889-5851 Email Add: fxavier_2015@yahoo.com.ph</p>
                        <p style="margin: 0; margin-bottom: 0; padding: 2px 5px; line-height: 1; font-size: 20px; color: blue; text-decoration: underline;">STATEMENT OF ACCOUNT</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; align-items: center; margin: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; text-align: right; background-color: #A9D08E;">
                    <p style="margin: 0; line-height: 1; font-size: 14px; display: flex; align-items: center; gap: 6px; padding: 2px 6px;">
                        <strong>SOA NO.:</strong>
                        <input type="text" id="soaNumberInput" style="border: 1px solid #ccc; height: 20px; padding: 0 6px; padding-right: 50px; width: 120px; font-family: Arial, sans-serif; font-size: 12px; color: red;" placeholder="Enter SOA No." value="{{ $soaNumber ?? '' }}">
                        <span id="soaNumberStatus" style="font-size: 11px; color: green; display: none; margin-left: 6px;">✓ Saved</span>
                        <button type="button" onclick="saveSoaNumber()" style="margin-left: 6px; height: 20px; line-height: 18px; padding: 0 6px; background: #007cba; color: white; border: none; border-radius: 3px; font-size: 10px; cursor: pointer;" title="Save">💾</button>
                    </p>
                </div>
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; border-bottom: none; padding: 0;">
                    <div style="margin-left : 10px; flex: 0 0 8%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 14px; line-height: 1.2; font-weight: bold;">BILLED TO: </p>
                        <p style="margin: 0; font-size: 14px; line-height: 1.2; font-weight: bold;">ADDRESS:</p>
                    </div>
                    <div style="flex: 0 0 92%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px;"><span style="padding: 2px 5px; line-height: 1; font-family: 'Courier New', 'Consolas', 'Monaco', monospace;">{{ !empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}</span></p>
                        <p style="margin: 0; font-size: 15.5px;"><span style="padding: 2px 5px; font-family: 'Courier New', 'Consolas', 'Monaco', monospace;">BASCO, BATANES</span></p>
                    </div>
                </div>
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; padding: 0;">
                    <div style="margin-left : 10px; flex: 0 0 8%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 14px; line-height: 1.2; font-weight: bold;">CONSIGNEE:</p>
                    </div>
                    <div style="flex: 0 0 25%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px;"><span style="padding: 2px 5px; font-family: 'Courier New', 'Consolas', 'Monaco', monospace;">{{ collect($orders)->pluck('recName')->filter()->unique()->implode(', ') }}</span></p>
                    </div>
                    <div style="flex: 0 0 5%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 14px; line-height: 1.2; font-weight: bold;">SHIPPER:</p>
                    </div>
                    <div style="flex: 0 0 62%; line-height: 1; text-align: left; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px;"><span style="padding: 2px 5px; font-family: 'Courier New', 'Consolas', 'Monaco', monospace;">{{ collect($orders)->pluck('shipperName')->filter()->unique()->implode(', ') }}</span></p>
                    </div>
                </div>
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; padding: 0;">
                    <div style="flex: 0 0 13%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 14px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; background-color: #A9D08E;">VESSEL</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; border: none; min-height: 80px; display: flex; align-items: center; justify-content: center; flex: 1;">M/V EVERWIN STAR {{ $ship }}</p>
                    </div>
                    <div style="flex: 0 0 17%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 14px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; background-color: #A9D08E;">VOYAGE #</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; border: none; border-left: 2px solid #000; min-height: 80px; display: flex; align-items: center; justify-content: center; flex: 1;">{{ htmlspecialchars_decode($voyage, ENT_QUOTES) }} {{ $origin }} - {{ $destination }}</p>
                    </div>
                    <div style="flex: 0 0 15%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 14px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; background-color: #A9D08E;">BL #</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; border: none; border-left: 2px solid #000; min-height: 80px; display: flex; align-items: center; justify-content: center; flex: 1; word-wrap: break-word; word-break: break-all;">
                            @foreach($orders as $order)
                                {{ $order->orderId }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>
                    <div style="flex: 0 0 55%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 14px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: center; background-color: #A9D08E;">DESCRIPTION</p>
                        <p style="margin: 0; font-size: 14.5px; padding: 2px 5px; border-left: 2px solid #000; min-height: 80px; display: flex; align-items: center; justify-content: center; flex: 1; word-wrap: break-word; word-break: break-word;">
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
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; border-bottom: none; padding: 0;">
                    <div style="flex: 0 0 50%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #A9D08E; min-height: 17px; display: flex; align-items: center; justify-content: left; background-color: #A9D08E;">SAY IN PESOS:</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left; background-color: #A9D08E; color: #A9D08E;">.</p>
                        <p style="margin: 0; font-size: 13.5px; padding: 2px 5px; border-bottom: 2px solid #000; min-height: 63px; display: flex; align-items: center; justify-content: center; font-family: 'Courier New', 'Consolas', 'Monaco', monospace; text-transform: uppercase;">**{{ $amountInWords }}**</p>
                    </div>
                    <div style="flex: 0 0 25%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">FREIGHT :</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">INSURANCE :</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">PPA MNL :</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left;">WHARFAGE :</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: left; background-color: #ffff38ff;">GRAND TOTAL :</p>
                    </div>
                    <div style="flex: 0 0 25%; box-sizing: border-box; line-height: 1; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">{{ number_format($voyageFreight, 2) }}</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">{{ number_format($voyageValuation, 2) }}</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">{{ number_format($voyagePpaManila, 2) }}</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right;">{{ number_format($voyageWharfage, 2) }}</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 2px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 17px; display: flex; align-items: center; justify-content: right; background-color: #ffff38ff;">
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
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; border-bottom: none; padding: 0;">
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; background-color: #A9D08E;">PAYMENT INSTRUCTIONS:</p>
                    </div>
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; background-color: #A9D08E; color: #A9D08E;">.</p>
                    </div>
                </div>
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; border-bottom: none; padding: 0;">
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; min-height: 12px; display: flex; align-items: center; justify-content: left; color: red; text-decoration: underline; padding-bottom: 2px;">Kindly settle your account at St. Francis office, or by bank</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; min-height: 12px; display: flex; align-items: center; justify-content: left; color: red; text-decoration: underline; padding-bottom: 2px;">transfer using the details below:</p>
                    </div>
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; background-color: #A9D08E;">PREPARED BY:</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px 3px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; color: #fff;">.</p>
                    </div>
                </div>
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; border-bottom: none; padding: 0;">
                    <div style="flex: 0 0 30%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-top: 2px solid #000; border-bottom: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left;">ACCOUNT NAME:</p>
                        <p style="margin: 0; font-size: 13.5px; padding: 1px 5px; font-weight: light; border-bottom: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; padding-bottom: 2px; font-style: italic;">St. Francis Xavier Star Shipping lines, Inc.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left;">ACCOUNT NUMBER:</p>
                    </div>
                    <div style="flex: 0 0 20%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; color: #fff; padding-bottom: 2.5px;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; color: #fff; padding-bottom: 3px;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; color: #fff; padding-bottom: 3px;">.</p>
                    </div>
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px 3px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; text-decoration: underline;">CHERRY MAE E. CAMAYA</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; font-style: italic;">Billing Officer</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; background-color: #A9D08E;">RECEIVED BY:</p>
                    </div>
                </div>
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; border-bottom: none; padding: 0;">
                    <div style="flex: 0 0 10%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; padding-bottom: 1.5px;">PNB:</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; padding-bottom: 1.5px;">LBP:</p>
                    </div>
                    <div style="flex: 0 0 20%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; padding-bottom: 1.5px;">2277-7000-1147</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; padding-bottom: 1.5px;">1082-1039-76</p>
                    </div>
                    <div style="flex: 0 0 20%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; color: #fff; padding-bottom: 2.5px;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; color: #fff; padding-bottom: 2.5px;">.</p>
                    </div>
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px 3px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; color: #fff; padding-bottom: 3px;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; color: #fff;">.</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: stretch; gap: 0; margin-bottom: 0; border: 2px solid #000; border-right: 3px solid #000; border-top: none; border-bottom: none; padding: 0;">
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; min-height: 12px; display: flex; align-items: center; justify-content: left; padding-bottom: 3px; color: #fff;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; min-height: 12px; display: flex; align-items: center; justify-content: left; padding-bottom: 3px; color: #fff;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; min-height: 12px; display: flex; align-items: center; justify-content: left; padding-bottom: 2.5px; font-style: italic; ">Attached is photocopy of BL for your reference….</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; min-height: 12px; display: flex; align-items: center; justify-content: left; padding-bottom: 3px; color: #fff;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; border-bottom: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left color: #fff;">.</p>
                    </div>
                    <div style="flex: 0 0 50%; box-sizing: border-box; text-align: center; display: flex; flex-direction: column;">
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; background-color: #A9D08E; font-style: italic;">Signature Over Printed Name</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; background-color: #A9D08E;">DATE:</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: bold; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; padding-bottom: 3px;">{{ date('F d, Y') }}</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: left; color: #fff;">.</p>
                        <p style="margin: 0; font-size: 15.5px; padding: 1px 5px; font-weight: light; border-bottom: 2px solid #000; border-left: 2px solid #000; min-height: 12px; display: flex; align-items: center; justify-content: center; font-style: italic; background-color: #A9D08E;">MM / DD / YR</p>
                    </div>
                </div>
                @php
                    $order = $orders->first();
                @endphp
                @if($order && $order->blStatus === 'PAID')
                    <div class="paid-stamp" style="position: absolute; bottom: 210px; right: 10px; color: red; border: 3px solid rgb(128, 0, 0); color: rgb(128, 0, 0); font-size: 16px; font-weight: bold; font-family: 'Bebas Neue', sans-serif; padding: 5px; text-align: center; background-color: none; z-index: 1000; width: 220px; height: auto; min-height: 50px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <span>PAID IN {{ strtoupper($order->display_updated_location ?? $order->updated_location ?? '') }}</span>
                        @if(!empty($order->AR) && !empty($order->OR))
                            <span style="font-size: 14px;">OR#: {{ $order->OR }} | AR#: {{ $order->AR }}</span>
                        @elseif(!empty($order->AR))
                            <span style="font-size: 14px;">AR#: {{ $order->AR }}</span>
                        @elseif(!empty($order->OR))
                            <span style="font-size: 14px;">OR#: {{ $order->OR }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div><br>

    <!-- For printing and SOA save functionality -->    
    <script>
        // Remove padding from paid-stamp for print
        function removePaddingForPrint() {
            const paidStamp = document.querySelector('.paid-stamp');
            if (paidStamp) {
                paidStamp.style.padding = '0';
            }
        }

        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', removePaddingForPrint);
        });

        function printContent(divId) {
            console.log("printContent function called");
            var printContainer = document.getElementById(divId);
            if (!printContainer) {
                console.error("Print container not found");
                alert("Print container not found. Please contact support.");
                return;
            }

            // Fix input fields before printing
            var inputs = printContainer.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.style.fontFamily = "Arial, sans-serif";
                if (!input.style.fontSize) {
                    input.style.fontSize = "13px";
                }
                input.setAttribute('value', input.value);
            });

            // Clone for print
            var tempContainer = printContainer.cloneNode(true);
            tempContainer.style.position = 'absolute';
            tempContainer.style.visibility = 'hidden';
            tempContainer.style.width = '210mm';
            tempContainer.style.padding = '0.5in';
            tempContainer.style.fontFamily = 'Arial, sans-serif';
            document.body.appendChild(tempContainer);

            var mainContentHeight = tempContainer.offsetHeight;
            var maxSinglePageHeight = 700;
            var needsPageBreak = mainContentHeight > maxSinglePageHeight;

            document.body.removeChild(tempContainer);

            var allInputs = printContainer.querySelectorAll('input');
            allInputs.forEach(function(input) {
                input.setAttribute('value', input.value);
            });

            var printClone = printContainer.cloneNode(true);
            
            // Replace SOA input with span
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
                soaSpan.setAttribute('data-soa-number', 'true');
                soaInputInClone.parentNode.replaceChild(soaSpan, soaInputInClone);
            }
            
            // Remove status and button
            var statusSpan = printClone.querySelector('#soaNumberStatus');
            if (statusSpan) statusSpan.remove();
            var testButton = printClone.querySelector('button[title="Save"]');
            if (testButton) testButton.remove();

            var printContents = printClone.innerHTML;

            var printWindow = window.open("", "", "width=1000,height=800");
            if (!printWindow) {
                alert("Unable to open print window. Please disable your popup blocker and try again.");
                return;
            }
            printWindow.document.write("<html><head><title>Statement of Account - BL# " + "{{ $orders->first()->orderId }}" + "</title>");
            printWindow.document.write("<style>");
            printWindow.document.write(`
                @page { size: A4 landscape; margin: 0.5in; }
                @media print {
                    html, body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-family: Arial, sans-serif; }
                    .print-container { width: 100%; }
                    img { display: block !important; margin: 0 auto; max-width: 300px; }
                    button { display: none; }
                    input { font-family: Arial, sans-serif; font-size: 13px; border: none !important; background: transparent !important; }
                    span[data-soa-number] { color: red !important; font-size: 16px !important; font-weight: bold !important; }
                    /* Paid stamp print styles */
                    .paid-stamp {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color: rgb(128, 0, 0) !important;
                        border-color: rgb(128, 0, 0) !important;
                    }
                }
            `);
            printWindow.document.write("</style></head><body>");
            printWindow.document.write(printContents);
            printWindow.document.write("</body></html>");
            printWindow.document.close();
            printWindow.focus();

            printWindow.onload = function () {
                printWindow.print();
                printWindow.close();
            };
        }

        // SOA Number save functionality
        let soaNumberTimeout;
            
        function saveSoaNumber() {
            const soaInputElement = document.getElementById('soaNumberInput');
            const soaNumber = soaInputElement.value.trim();
            const statusElement = document.getElementById('soaNumberStatus');
            
            if (!soaNumber) return;
            
            statusElement.textContent = '💾 Saving...';
            statusElement.style.color = 'orange';
            statusElement.style.display = 'inline';
            
            const requestData = {
                customer_id: {{ $customer->id }},
                ship: '{{ $ship }}',
                voyage: '{{ $voyage }}',
                soa_number: soaNumber,
                order_id: {{ $orderId }} // Include order_id for per-BL SOA
            };
            
            fetch('{{ route("update-soa-number") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    soaInputElement.value = soaNumber;
                    soaInputElement.setAttribute('value', soaNumber);
                    soaInputElement.defaultValue = soaNumber;
                    
                    statusElement.textContent = '✓ Saved';
                    statusElement.style.color = 'green';
                    
                    setTimeout(() => {
                        statusElement.style.display = 'none';
                    }, 3000);
                } else {
                    statusElement.textContent = '✗ Error';
                    statusElement.style.color = 'red';
                    
                    setTimeout(() => {
                        statusElement.style.display = 'none';
                    }, 5000);
                }
            })
            .catch(error => {
                statusElement.textContent = '✗ Error';
                statusElement.style.color = 'red';
                console.error('Error saving SOA number:', error);
                
                setTimeout(() => {
                    statusElement.style.display = 'none';
                }, 5000);
            });
        }
        
        // Add event listener for SOA number input
        document.addEventListener('DOMContentLoaded', function() {
            const soaInput = document.getElementById('soaNumberInput');
            if (soaInput) {
                soaInput.addEventListener('input', function() {
                    clearTimeout(soaNumberTimeout);
                    soaNumberTimeout = setTimeout(saveSoaNumber, 1000);
                });
            }
        });

        // Discount and penalty functions (simplified for single BL)
        let discountActive = false;

        function toggleDiscount() {
            discountActive = !discountActive;
            const discountBtnText = document.getElementById('discountBtnText');
            const toggleDiscountBtn = document.getElementById('toggleDiscountBtn');
            if (discountActive) {
                if (discountBtnText) discountBtnText.innerHTML = '<i class="fas fa-times me-2"></i>Discount Activated';
                if (toggleDiscountBtn) {
                    toggleDiscountBtn.className = 'btn btn-success px-4 py-2';
                    toggleDiscountBtn.style.backgroundColor = '#28a745';
                }
            } else {
                if (discountBtnText) discountBtnText.innerHTML = '<i class="fas fa-percentage me-2"></i>Discount Deactivated';
                if (toggleDiscountBtn) {
                    toggleDiscountBtn.className = 'btn btn-danger px-4 py-2';
                    toggleDiscountBtn.style.backgroundColor = '#dc3545';
                }
            }
        }

        // Penalty functions
        let penaltyActive = false;
        let currentPenaltyAmount = 0;

        function parseFormattedNumber(str) {
            if (!str) return 0;
            return parseFloat(String(str).replace(/,/g, '')) || 0;
        }

        function getCurrentBaseAmount() {
            return {{ $voyageTotal }};
        }

        function calculatePenalty() {
            document.getElementById('penaltyModal').classList.remove('hidden');
            updatePenaltyCalculation();
        }

        function closePenaltyModal() {
            document.getElementById('penaltyModal').classList.add('hidden');
        }

        function updatePenaltyCalculation() {
            const months = parseInt(document.getElementById('penaltyMonths').value) || 1;
            const baseTotal = getCurrentBaseAmount();
            const penaltyAmount = baseTotal * 0.01 * months;
            const totalWithPenalty = baseTotal + penaltyAmount;

            document.getElementById('penaltyBaseAmount').textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(baseTotal);
            document.getElementById('penaltyAmount').textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(penaltyAmount);
            document.getElementById('totalWithPenalty').textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(totalWithPenalty);
        }

        function applyPenalty() {
            const months = parseInt(document.getElementById('penaltyMonths').value) || 1;
            const baseTotal = getCurrentBaseAmount();
            currentPenaltyAmount = baseTotal * 0.01 * months;
            penaltyActive = true;

            updateFinalAmount();
            closePenaltyModal();

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

            const penaltyBtn = document.getElementById('calculatePenaltyBtn');
            if (penaltyBtn) {
                penaltyBtn.innerHTML = 'Calculate 1% Penalty';
                penaltyBtn.onclick = calculatePenalty;
                penaltyBtn.className = 'btn btn-warning px-4 py-2';
            }
        }

        function updateFinalAmount() {
            const finalAmountElement = document.getElementById('finalAmount');
            const baseTotal = getCurrentBaseAmount();
            let finalTotal = baseTotal;

            if (penaltyActive) {
                finalTotal += currentPenaltyAmount;
            }

            if (finalAmountElement) {
                finalAmountElement.textContent = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(finalTotal);
            }
        }

        const penaltyMonthsEl = document.getElementById('penaltyMonths');
        if (penaltyMonthsEl) penaltyMonthsEl.addEventListener('input', updatePenaltyCalculation);
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
