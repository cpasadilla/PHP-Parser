<x-app-layout>
    @php
        // Check if customer is eligible for 5% discount
        $eligibleCustomerIds = [1001, 1002, 1003, 1004, 1005];
        $isEligible = in_array($customer->id, $eligibleCustomerIds);
        $discountedFreight = 0;
        $discountAmount = 0;
        $discountedTotal = 0;

        // Generate SOA number
        $soaNumber = App\Models\SoaNumber::generateSoaNumber($customer->id, $ship, $voyage);
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
                <button class="btn btn-primary" onclick="printContent('printContainer')">PRINT</button>
            </div>
        </div>
        
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white" style="font-family: Arial, sans-serif;" data-ship="{{ $ship }}" data-voyage="{{ htmlspecialchars_decode($voyage, ENT_QUOTES) }}">
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
                    <p style="margin-bottom: 0; line-height: 1;"><strong>SOA NO.:</strong> {{ $soaNumber }}</p>
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
                                <th style="width: 43%;" class="px-4 py-2">DESCRIPTION</th>
                                <th style="width: 10%;" class="px-4 py-2">FREIGHT</th>
                                <th style="width: 12%;" class="px-4 py-2">VALUATION</th>
                                <th style="width: 10%;" class="px-4 py-2">PADLOCK FEE</th>
                                <th style="width: 10%;" class="px-4 py-2">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">                            @php 
                                $voyageTotal = 0;
                                $voyageFreight = 0;
                                $voyageValuation = 0;
                                $voyageInterest = 0;
                                $voyagePadlockFee = 0;
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
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">{{ number_format($order->valuation, 2) }}</td>                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">{{ number_format($order->padlock_fee ?? 0, 2) }}</td>
                                    <td class="px-4 py-2 text-right" style="word-wrap: break-word;">
                                        {{ number_format($order->totalAmount, 2) }}
                                        @if($interestAmount > 0)
                                            <div class="text-red-600 text-xs font-semibold">
                                                +{{ number_format($interestAmount, 2) }} (interest)
                                            </div>
                                            <div class="font-bold">
                                                {{ number_format($totalWithInterest, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>                                @php                                    $voyageTotal += $order->totalAmount + $interestAmount;
                                    $voyageFreight += $order->freight; 
                                    $voyageValuation += $order->valuation;
                                    $voyagePadlockFee += ($order->padlock_fee ?? 0);
                                @endphp@endforeach                            @php                                // Calculate discounted values if customer is eligible AND voyageFreight is at least 50,000
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
                            </tr>
                            <tr class="font-semibold" style="line-height: 0.8; background-color:rgb(97, 175, 91); color: black;">                                <td colspan="4" class="px-4 py-1" style="text-align: center; font-weight: bold;">GRAND TOTAL:</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyagePadlockFee ?? 0, 2) }}</td>
                                <td class="px-4 py-1 text-right">{{ number_format($voyageTotal, 2) }}</td>
                            </tr>
                            @if($voyageInterest > 0)
                                <tr class="bg-gray-50 dark:bg-gray-900 font-semibold text-red-600" style="line-height: 0.8;">
                                    <td colspan="4" class="px-4 py-1 text-right" style="word-wrap: break-word; white-space: normal;">Interest (1% per month after 30 days):</td>
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
                                    <td class="px-4 py-1 text-right ">{{ number_format($voyageTotal, 2) }}</td>
                                </tr>
                            @endif
                            @if($isEligible)
                                <tr class="font-semibold" style="line-height: 0; background-color:rgb(240, 240, 5); color: black;">
                                    <td colspan="4" class="px-4 py-2" style="text-align: left;">5% Discount on total freight if paid within <span style="font-weight: bold;">15 days</span> upon receipt of SOA</td>                                    <td style="width: 100px; font-weight: bold;" class="px-4 py-2 text-right" >{{ number_format($discountedFreight, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyagePadlockFee ?? 0, 2) }}</td>
                                    <td style="width: 100px; font-weight: bold;" class="px-4 py-2 text-right">{{ number_format($discountedTotal, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="font-semibold" style="line-height: 0.8; background-color:rgb(231, 15, 22); color: white;">
                                <td colspan="4" class="px-4 py-1" style="text-align: left; word-wrap: break-word; white-space: normal; color: white;">a PENALTY rate of 1% PER MONTH will be applied to total bills if not paid every after 30days</td>
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
            
            <footer style="margin-top: auto;">
                <!-- Your existing footer content here -->
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
            </footer>
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

            // Fix input fields before printing to maintain font consistency
            var inputs = printContainer.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.style.fontFamily = "Arial, sans-serif";
                if (!input.style.fontSize) {
                    input.style.fontSize = "12px";
                }
            });

            // Get print content
            var printContents = printContainer.innerHTML;

            // Open new print window
            var printWindow = window.open("", "", "width=1000,height=800");
            printWindow.document.write("<html><head><title>Statement of Account</title>");
            printWindow.document.write("<style>");
            printWindow.document.write(`
                @media print {
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; margin: 0; font-family: Arial, sans-serif; }
                    img { display: block !important; margin: 0 auto; }
                    table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; table-layout: fixed; }
                    th, td { border: 1px solid #ddd; padding: 4px 8px; font-family: Arial, sans-serif; word-wrap: break-word; white-space: normal; line-height: 1.2; }
                    thead { background-color: #f2f2f2 !important; }
                    button { display: none; }
                    footer { position: absolute; bottom: 0; left: 0; width: 100%; }
                    .non-printable { display: none; }
                    input { font-family: Arial, sans-serif; font-size: 12px; }
                    .description-cell { word-wrap: break-word; white-space: normal; }
                    tr { page-break-inside: avoid; }
                    tbody tr { min-height: 24px; }
                    #printContainer > div { margin: 0 !important; padding: 0 !important; }
                    #printContainer > div + div { margin-top: -1px !important; }
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
    </script>
</x-app-layout>
