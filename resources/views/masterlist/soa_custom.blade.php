<x-app-layout>
    @php
        // Check if customer is eligible for 5% discount
        $eligibleCustomerIds = [1001, 1002, 1003, 1004, 1005];
        $isEligible = in_array($customer->id, $eligibleCustomerIds);
        $discountedFreight = 0;
        $discountAmount = 0;
        $discountedTotal = 0;

        // SOA number will be manually entered - no auto-generation
        $soaNumber = '';
    @endphp
    
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Custom Statement of Account for: ') . (!empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name) }}
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
                    <span id="discountBtnText">Apply 5% Discount</span>
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
                        <p style="margin: 0;">Cellphone Nos.: 0908-815-9300 / 0999-889-5848 / 0999-889-5849</p>
                        <p style="margin: 0;">Email Address: fxavier_2015@yahoo.com.ph</p>
                        <p style="margin: 0;">TIN: 009-081-111-00000</p>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: center; margin: 5px 0; line-height: 0;">
                    <span style="font-weight: bold; font-size: 17px;">STATEMENT OF ACCOUNT</span>
                </div>
                
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 12px; line-height: 0;">
                    <div style="width: 60%;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>BILLED TO:</strong> <span style="padding: 2px 5px;">{{ !empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}</span></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>VESSEL:</strong> M/V EVERWIN STAR {{ $ship }}</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>VOYAGE NO.:</strong> {{ htmlspecialchars_decode($voyage, ENT_QUOTES) }} {{ $origin }} - {{ $destination }}</p>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>DATE:</strong> {{ date('F d, Y') }}</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>SOA NO.:</strong> 
                            <input type="text" id="soaNumberInput" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" placeholder="Enter SOA No." value="{{ $soaNumber }}">
                        </p>
                    </div>
                </div>
                
                <div id="voyage-{{ $ship }}-{{ Str::slug($voyage) }}" class="accordion-content">
                    <div class="overflow-x-auto mt-4">
                        @php 
                            $voyageTotal = 0;
                            $voyageFreight = 0;
                            $voyageValuation = 0;
                            $voyagePadlockFee = 0;
                            $voyageWharfage = 0;
                            $voyagePpaManila = 0;
                        @endphp
                        
                        <!-- Orders Detail Table -->
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="font-family: Arial, sans-serif; font-size: 11px; line-height: 1; border-collapse: collapse; border: 1px solid #000;">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">BL #</th>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">DESCRIPTION OF GOODS</th>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">FREIGHT</th>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">VALUATION</th>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">WHARFAGE</th>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">PADLOCK FEE</th>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">PPA MANILA</th>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold;">TOTAL AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $order->orderId }}</td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: left;">
                                            @foreach ($order->parcels as $parcel)
                                                <span>{{ $parcel->quantity }} {{ $parcel->unit }} {{ $parcel->itemName }} {{ $parcel->desc }}</span>@if(!$loop->last)<br>@endif
                                            @endforeach
                                        </td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($order->freight, 2) }}</td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($order->valuation, 2) }}</td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($order->wharfage ?? 0, 2) }}</td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   value="{{ $order->padlock_fee ?? 0 }}" 
                                                   style="width: 80px; border: none; text-align: right; font-family: Arial, sans-serif; font-size: 11px; background: transparent;"
                                                   class="padlock-fee-input"
                                                   data-order-id="{{ $order->id }}"
                                                   onchange="updatePadlockFee(this, {{ $order->id }})">
                                        </td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: right;">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   value="{{ $order->ppa_manila ?? 0 }}" 
                                                   style="width: 80px; border: none; text-align: right; font-family: Arial, sans-serif; font-size: 11px; background: transparent;"
                                                   class="ppa-manila-input"
                                                   data-order-id="{{ $order->id }}"
                                                   onchange="updatePpaManila(this, {{ $order->id }})">
                                        </td>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: right;" class="total-amount-cell" data-order-id="{{ $order->id }}">
                                            {{ number_format(($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0)), 2) }}
                                        </td>
                                    </tr>
                                    @php 
                                        $voyageFreight += $order->freight;
                                        $voyageValuation += $order->valuation;
                                        $voyageWharfage += ($order->wharfage ?? 0);
                                        $voyagePadlockFee += ($order->padlock_fee ?? 0);
                                        $voyagePpaManila += ($order->ppa_manila ?? 0);
                                        $voyageTotal += ($order->freight + $order->valuation + ($order->wharfage ?? 0) + ($order->padlock_fee ?? 0) + ($order->ppa_manila ?? 0));
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                                <tr>
                                    <td colspan="2" style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold;">TOTAL:</td>
                                    <td style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold;" id="totalFreight">{{ number_format($voyageFreight, 2) }}</td>
                                    <td style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold;" id="totalValuation">{{ number_format($voyageValuation, 2) }}</td>
                                    <td style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold;" id="totalWharfage">{{ number_format($voyageWharfage, 2) }}</td>
                                    <td style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold;" id="totalPadlockFee">{{ number_format($voyagePadlockFee, 2) }}</td>
                                    <td style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold;" id="totalPpaManila">{{ number_format($voyagePpaManila, 2) }}</td>
                                    <td style="border: 1px solid #000; padding: 8px; text-align: right; font-weight: bold; font-size: 14px;" id="finalAmount">{{ number_format($voyageTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        @php
                            $discountedFreight = $voyageFreight * 0.95; 
                            $discountAmount = $voyageFreight * 0.05;
                            $discountedTotal = $discountedFreight + $voyageValuation + $voyageWharfage + $voyagePadlockFee + $voyagePpaManila;
                        @endphp
                    </div>
                </div>
            </div>

            <!-- Signature section for screen display -->
            <div class="signature-section">
                <div style="margin: 20px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 60%;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>PREPARED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">CHERRY MAE E. CAMAYA</strong></p>
                        <p style="margin-top: 0; margin-left: 145px; font-size: 13px; line-height: 1;">Billing Officer</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">Kindly settle your account at St. Francis office, or by bank</strong></p>
                        <p style="margin-top: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">transfer using the details below:</strong></p>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>RECEIVED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 115px; font-size: 13px; line-height: 1;">Signature over Printed Name</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>DATE:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 165px; font-size: 13px; line-height: 1;">MM/DD/YR</p>
                    </div>
                </div>
                <div style="margin: 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 25%;">
                        <table style="border-collapse: collapse; width: 100%; font-size: 12px; line-height: 1;">
                            <tr>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center;"><strong>BPI</strong></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center;">0219-0019-25</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center;">ST. FRANCIS XAVIER SHIPPING CORP.</td>
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
                <div style="margin: 20px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 60%;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>PREPARED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">CHERRY MAE E. CAMAYA</strong></p>
                        <p style="margin-top: 0; margin-left: 145px; font-size: 13px; line-height: 1;">Billing Officer</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">Kindly settle your account at St. Francis office, or by bank</strong></p>
                        <p style="margin-top: 0; line-height: 1;"><strong style="font-size: 12px; text-decoration: underline; font-size: 14px; color: red;">transfer using the details below:</strong></p>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>RECEIVED BY:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 115px; font-size: 13px; line-height: 1;">Signature over Printed Name</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>DATE:</strong></p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                        <p style="margin-top: 0; margin-left: 165px; font-size: 13px; line-height: 1;">MM/DD/YR</p>
                    </div>
                </div>
                <div style="margin: 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                    <div style="width: 25%;">
                        <table style="border-collapse: collapse; width: 100%; font-size: 12px; line-height: 1;">
                            <tr>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center;"><strong>BPI</strong></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center;">0219-0019-25</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 4px; text-align: center;">ST. FRANCIS XAVIER SHIPPING CORP.</td>
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

    <!-- Penalty Calculation Modal -->
    <div id="penaltyModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">1% Monthly Penalty Calculation</h3>
            
            <div class="mb-4">
                <label for="penaltyMonths" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of months overdue:</label>
                <input type="number" id="penaltyMonths" min="1" max="12" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Original Amount: <span id="originalAmount" class="font-semibold">{{ number_format($voyageTotal, 2) }}</span></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Penalty Amount: <span id="penaltyAmount" class="font-semibold">0.00</span></p>
                <p class="text-sm text-gray-900 dark:text-gray-100 font-semibold">Total with Penalty: <span id="totalWithPenalty" class="text-red-600">{{ number_format($voyageTotal, 2) }}</span></p>
            </div>
            
            <div class="flex justify-end gap-2">
                <button onclick="closePenaltyModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                <button onclick="applyPenalty()" class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600">Apply Penalty</button>
            </div>
        </div>
    </div>

    <!-- For printing -->
    <script>
        // Store original values
        const originalTotal = {{ $voyageTotal }};
        const discountedTotal = {{ $discountedTotal }};
        let discountActive = false;
        let penaltyActive = false;
        let currentPenaltyAmount = 0;

        function printContent(divId) {
            console.log("printContent function called");
            var printContainer = document.getElementById(divId);
            if (!printContainer) {
                console.error("Print container not found");
                return;
            }

            // Fix input fields before printing to maintain font consistency
            var inputs = printContainer.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.style.fontFamily = "Arial, sans-serif";
                if (!input.style.fontSize) {
                    input.style.fontSize = "12px";
                }
            });

            // Clone the print container to measure ONLY the main content
            var tempContainer = printContainer.cloneNode(true);
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
            // A4 height minus margins = ~277mm - 25.4mm = ~251mm ≈ 950px
            var maxSinglePageHeight = 950;
            var needsPageBreak = totalContentHeight > maxSinglePageHeight;

            // Remove temp container
            document.body.removeChild(tempContainer);

            console.log('Main content height:', mainContentHeight);
            console.log('Total content height:', totalContentHeight);
            console.log('Max page height:', maxSinglePageHeight);
            console.log('Needs page break:', needsPageBreak);

            // Get print content
            var printContents = printContainer.innerHTML;

            // Open new print window
            var printWindow = window.open("", "", "width=1000,height=800");
            printWindow.document.write("<html><head><title>Custom Statement of Account</title>");
            printWindow.document.write("<style>");
            printWindow.document.write(`
                @page {
                    size: A4;
                    margin: 0.5in;
                }
                @media print {
                    body { 
                        -webkit-print-color-adjust: exact; 
                        print-color-adjust: exact; 
                        margin: 0; 
                        font-family: Arial, sans-serif; 
                        font-size: 12px;
                    }
                    img { 
                        display: block !important; 
                        margin: 0 auto; 
                    }
                    .signature-section { 
                        display: none !important; 
                    }
                    .print-signature { 
                        display: block !important; 
                        ${needsPageBreak ? 'page-break-before: always; position: fixed; bottom: 0; left: 0; right: 0;' : 'margin-top: 20px;'}
                    }
                    .main-content {
                        ${needsPageBreak ? 'page-break-after: always;' : ''}
                    }
                    input {
                        border: none !important;
                        background: transparent !important;
                        font-family: Arial, sans-serif !important;
                        font-size: 11px !important;
                    }
                    table {
                        page-break-inside: auto;
                    }
                    tr {
                        page-break-inside: avoid;
                        page-break-after: auto;
                    }
                }
                @media screen {
                    .print-signature { 
                        display: none !important; 
                    }
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
        }

        // Function to toggle between original and discounted totals
        function toggleDiscount() {
            const finalAmountElement = document.getElementById('finalAmount');
            const discountBtnText = document.getElementById('discountBtnText');
            const toggleDiscountBtn = document.getElementById('toggleDiscountBtn');
            
            if (discountActive) {
                // Remove discount
                discountActive = false;
                let newTotal = originalTotal;
                if (penaltyActive) {
                    newTotal += currentPenaltyAmount;
                }
                finalAmountElement.textContent = newTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                discountBtnText.textContent = 'Apply 5% Discount';
                toggleDiscountBtn.classList.remove('btn-danger');
                toggleDiscountBtn.classList.add('btn-success');
            } else {
                // Apply discount
                discountActive = true;
                let newTotal = discountedTotal;
                if (penaltyActive) {
                    newTotal += currentPenaltyAmount;
                }
                finalAmountElement.textContent = newTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                discountBtnText.textContent = 'Remove 5% Discount';
                toggleDiscountBtn.classList.remove('btn-success');
                toggleDiscountBtn.classList.add('btn-danger');
            }
        }

        // Function to calculate penalty
        function calculatePenalty() {
            document.getElementById('penaltyModal').classList.remove('hidden');
            updatePenaltyCalculation();
        }

        function closePenaltyModal() {
            document.getElementById('penaltyModal').classList.add('hidden');
        }

        function updatePenaltyCalculation() {
            const months = document.getElementById('penaltyMonths').value;
            const baseAmount = discountActive ? discountedTotal : originalTotal;
            const penalty = baseAmount * 0.01 * months;
            const totalWithPenalty = baseAmount + penalty;

            document.getElementById('originalAmount').textContent = baseAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('penaltyAmount').textContent = penalty.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('totalWithPenalty').textContent = totalWithPenalty.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        function applyPenalty() {
            const months = document.getElementById('penaltyMonths').value;
            const baseAmount = discountActive ? discountedTotal : originalTotal;
            currentPenaltyAmount = baseAmount * 0.01 * months;
            const totalWithPenalty = baseAmount + currentPenaltyAmount;

            document.getElementById('finalAmount').textContent = totalWithPenalty.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            penaltyActive = true;
            closePenaltyModal();
            
            // Update button text
            document.getElementById('calculatePenaltyBtn').textContent = `Remove ${months} Month${months > 1 ? 's' : ''} Penalty`;
            document.getElementById('calculatePenaltyBtn').onclick = function() { removePenalty(); };
        }

        function removePenalty() {
            const baseAmount = discountActive ? discountedTotal : originalTotal;
            document.getElementById('finalAmount').textContent = baseAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            penaltyActive = false;
            currentPenaltyAmount = 0;
            
            // Reset button
            document.getElementById('calculatePenaltyBtn').textContent = 'Calculate 1% Penalty';
            document.getElementById('calculatePenaltyBtn').onclick = function() { calculatePenalty(); };
        }

        // Event listener for penalty months input
        document.getElementById('penaltyMonths').addEventListener('input', updatePenaltyCalculation);

        // Function to update padlock fee
        function updatePadlockFee(input, orderId) {
            const newValue = parseFloat(input.value) || 0;
            updateRowTotal(orderId);
            updateColumnTotals();
        }

        // Function to update PPA Manila fee
        function updatePpaManila(input, orderId) {
            const newValue = parseFloat(input.value) || 0;
            updateRowTotal(orderId);
            updateColumnTotals();
        }

        // Function to update individual row total
        function updateRowTotal(orderId) {
            const row = document.querySelector(`tr:has([data-order-id="${orderId}"])`);
            if (!row) return;

            const freight = parseFloat(row.cells[2].textContent.replace(/,/g, '')) || 0;
            const valuation = parseFloat(row.cells[3].textContent.replace(/,/g, '')) || 0;
            const wharfage = parseFloat(row.cells[4].textContent.replace(/,/g, '')) || 0;
            const padlockFee = parseFloat(row.querySelector('.padlock-fee-input').value) || 0;
            const ppaManila = parseFloat(row.querySelector('.ppa-manila-input').value) || 0;

            const total = freight + valuation + wharfage + padlockFee + ppaManila;
            const totalCell = row.querySelector('.total-amount-cell');
            totalCell.textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // Function to update column totals
        function updateColumnTotals() {
            let totalPadlockFee = 0;
            let totalPpaManila = 0;
            let grandTotal = 0;

            // Calculate totals from all rows
            document.querySelectorAll('.padlock-fee-input').forEach(input => {
                totalPadlockFee += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('.ppa-manila-input').forEach(input => {
                totalPpaManila += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('.total-amount-cell').forEach(cell => {
                grandTotal += parseFloat(cell.textContent.replace(/,/g, '')) || 0;
            });

            // Update footer totals
            document.getElementById('totalPadlockFee').textContent = totalPadlockFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('totalPpaManila').textContent = totalPpaManila.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('finalAmount').textContent = grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // For handling input changes for fees
        document.addEventListener('DOMContentLoaded', function () {
            const padlockFeeInputs = document.querySelectorAll('.padlock-fee-input');
            const ppaManilaInputs = document.querySelectorAll('.ppa-manila-input');

            padlockFeeInputs.forEach(input => {
                input.addEventListener('input', function() {
                    updatePadlockFee(this, this.dataset.orderId);
                });
            });

            ppaManilaInputs.forEach(input => {
                input.addEventListener('input', function() {
                    updatePpaManila(this, this.dataset.orderId);
                });
            });
        });
    </script>
</x-app-layout>
