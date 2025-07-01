<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Voyage Statement of Account: M/V Everwin Star ') . $ship . ' - Voyage ' . $voyage }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">M/V Everwin Star {{ $ship }} - Voyage {{ $voyage }} ({{ $origin }} to {{ $destination }})</h3>
            <button class="btn btn-success" onclick="printContent('printContainer')">PRINT</button>
        </div>
        
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white" style="font-family: Arial, sans-serif;">
            <div style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 20px;">
                <img style="width: 500px; height: 70px;" src="{{ asset('images/logo-sfx.png') }}" alt="Logo">
                <div style="font-size: 12px; line-height: 1; margin-top: 3px;">
                    <p style="margin: 0;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                    <p style="margin: 0;">Cellphone Nos.: 0908-815-9300 / 0999-889-5848 / 0999-889-5849</p>
                    <p style="margin: 0;">Email Address: fxavier_2015@yahoo.com.ph</p>
                    <p style="margin: 0;">TIN: 009-081-111-00000</p>
                </div>
            </div>
            
            <div style="display: flex; justify-content: center; margin: 20px 0;">
                <span style="font-weight: bold; font-size: 17px;">STATEMENT OF ACCOUNT</span>
            </div>
            <div style="margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                <div style="width: 60%;">
                    <p style="margin-bottom: 0; line-height: 1;"><strong>BILLED TO:</strong> <span style="padding: 2px 5px;">{{ !empty($customer->first_name) || !empty($customer->last_name) ? $customer->first_name . ' ' . $customer->last_name : $customer->company_name }}</span></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong>VESSEL:</strong> M/V EVERWIN STAR {{ $ship }}</p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong>VOYAGE NO.:</strong> {{ $voyage }} {{ $origin }} - {{ $destination }}</p>
                </div>
                <div style="width: 35%; text-align: left; line-height: 1;">
                    <p style="margin-bottom: 0; line-height: 1;"><strong>DATE:</strong> {{ date('F d, Y') }}</p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong>SOA NO.:</strong></p>
                </div>
            </div>            <div id="voyage-{{ $ship }}-{{ $voyage }}" class="accordion-content">
                <div class="overflow-x-auto mt-4">
                    <!-- Customer Summary Table -->
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-6" style="font-family: Arial, sans-serif; font-size: 12px; line-height: 1.3;">
                        <thead class="bg-gray-100 dark:bg-gray-800" style="background-color: #f3f4f6; color: #111827;">
                            <tr>
                                <th style="width: 40px;" class="px-4 py-2 text-left">No.</th>
                                <th style="width: 250px;" class="px-4 py-2 text-left">Customer Name</th>
                                <th style="width: 100px;" class="px-4 py-2 text-left">Customer ID</th>
                                <th style="width: 100px;" class="px-4 py-2 text-right">Freight (₱)</th>
                                <th style="width: 100px;" class="px-4 py-2 text-right">Valuation (₱)</th>
                                <th style="width: 100px;" class="px-4 py-2 text-right">Total (₱)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php $counter = 1; @endphp
                            @foreach($groupedByCustomer as $customerId => $data)
                                <tr>
                                    <td class="px-4 py-2">{{ $counter++ }}</td>
                                    <td class="px-4 py-2">{{ $data['name'] }}</td>
                                    <td class="px-4 py-2">{{ $customerId }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['freight'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['valuation'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['total'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-semibold" style="font-weight: bold; background-color: #f9fafb;">
                                <td class="px-4 py-2" colspan="3">TOTAL</td>
                                <td class="px-4 py-2 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($voyageTotal, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Orders Detail Table -->
                    <h4 class="text-lg font-semibold mb-3">Order Details</h4>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="font-family: Arial, sans-serif; font-size: 11px; line-height: 1;">
                        <thead class="bg-gray-100 dark:bg-gray-800">                            
                            <tr>
                                <th style="width: 70px;" class="px-4 py-2">BL No.</th>
                                <th style="width: 100px;" class="px-4 py-2">Consignee</th>
                                <th style="width: 100px;" class="px-4 py-2">Shipper</th>
                                <th style="width: 375px;" class="px-4 py-2">Description</th>
                                <th style="width: 100px;" class="px-4 py-2">Freight</th>
                                <th style="width: 100px;" class="px-4 py-2">Valuation</th>
                                <th style="width: 100px;" class="px-4 py-2">Padlock Fee</th>
                                <th style="width: 100px;" class="px-4 py-2">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                // These variables are already defined in the controller
                                // $voyageFreight = 0;
                                // $voyageValuation = 0;
                                $voyagePadlockFee = 0; // Initialize padlock fee
                            @endphp
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" style="font-family: Arial, sans-serif; font-size: 12px; line-height: 0;">
                                    <td style="width: 70px;" class="px-4 py-2 text-center">{{ $order->orderId }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-center">{{ $order->recName }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-center">{{ $order->shipperName }}</td>
                                    <td style="width: 375px;" class="px-4 py-2 text-center">
                                        @foreach ($order->parcels as $parcel)
                                            <span>{{ $parcel->quantity }} {{ $parcel->unit }} {{ $parcel->itemName }} {{$parcel->desc}}</span><br>
                                        @endforeach
                                    </td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($order->freight, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($order->valuation, 2) }}</td>                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($order->padlock_fee ?? 0, 2) }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($order->totalAmount, 2) }}</td>
                                </tr>
                                @php
                                    $voyageTotal += $order->totalAmount;
                                    $voyageFreight += $order->freight; 
                                    $voyageValuation += $order->valuation;
                                    $voyagePadlockFee += ($order->padlock_fee ?? 0);
                                @endphp
                            @endforeach
                            <tr class="bg-gray-50 dark:bg-gray-900 font-semibold" style="line-height: 0;">
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-900 font-semibold" style="line-height: 0;">
                                <td colspan="4" class="px-4 py-2 text-right">Grand Total:</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyagePadlockFee ?? 0, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageTotal, 2) }}</td>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-900 font-semibold" style="line-height: 0;">
                                <td colspan="4" class="px-4 py-2 text-right">5% Discount on total freight if paid within 15 days upon receipt of SOA</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageTotal, 2) }}</td>
                            </tr>
                            V@php
                                // Check if any order has interest_start_date set
                                $hasInterest = $orders->first(function($order) {
                                    return !is_null($order->interest_start_date);
                                });
                                
                                // Calculate interest if interest is active
                                $interestAmount = 0;
                                $daysSinceActivation = 0;
                                $monthsSinceActivation = 0;
                                $totalWithInterest = $voyageTotal;
                                
                                if ($hasInterest) {
                                    $startDate = new \DateTime($hasInterest->interest_start_date);
                                    $currentDate = new \DateTime();
                                    $interval = $startDate->diff($currentDate);
                                    
                                    // Calculate days since activation
                                    $daysSinceActivation = $interval->days;
                                    
                                    // Calculate months (rounded down)
                                    $monthsSinceActivation = floor($daysSinceActivation / 30);
                                    
                                    // Only apply interest if at least 30 days have passed
                                    if ($daysSinceActivation >= 30) {
                                        // Calculate 1% per month interest
                                        $interestAmount = $voyageTotal * (0.01 * $monthsSinceActivation);
                                        $totalWithInterest = $voyageTotal + $interestAmount;
                                    }
                                }
                            @endphp

                            <!-- Interest Warning Row -->
                            <tr class="bg-gray-50 dark:bg-gray-900 font-semibold" style="line-height: 0;">
                                <td colspan="4" class="px-4 py-2 text-right text-red-600">A PENALTY rate of 1% PER MONTH will be applied to total bills if not paid every after 30 days</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageFreight, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageValuation, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyagePadlockFee ?? 0, 2) }}</td>
                                <td style="width: 100px;" class="px-4 py-2 text-right">{{ number_format($voyageTotal, 2) }}</td>
                            </tr>

                            @if($hasInterest)
                                <!-- Interest Information Row -->
                                <tr class="bg-red-100 dark:bg-red-900 font-semibold" style="line-height: 0;">
                                    <td colspan="4" class="px-4 py-2 text-right text-red-600">Interest ({{ $daysSinceActivation }} days since activation): {{ $monthsSinceActivation > 0 ? ($monthsSinceActivation . ' month(s) @ 1% per month') : 'Not yet applicable' }}</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right text-red-600"></td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right text-red-600"></td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right text-red-600"></td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right text-red-600">{{ number_format($interestAmount, 2) }}</td>
                                </tr>
                                
                                <!-- Total with Interest Row -->
                                <tr class="bg-gray-100 dark:bg-gray-800 font-bold" style="line-height: 0;">
                                    <td colspan="4" class="px-4 py-2 text-right">TOTAL WITH INTEREST:</td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right"></td>
                                    <td style="width: 100px;" class="px-4 py-2 text-right font-bold">{{ number_format($totalWithInterest, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <br>
            <div style="margin-bottom: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
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
            <div style="margin-bottom: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                <div style="width: 25%;">
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
    </div><br>

    <!-- For printing -->
    <script>
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
                    table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
                    th, td { border: 1px solid #ddd; padding: 8px; font-family: Arial, sans-serif; }
                    thead { background-color: #f2f2f2 !important; }
                    button { display: none; }
                    input { font-family: Arial, sans-serif; font-size: 12px; }
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
    </script>
</x-app-layout>
