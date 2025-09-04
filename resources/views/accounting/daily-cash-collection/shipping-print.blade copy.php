<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Daily Cash Collection Report - Shipping') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">
                Daily Cash Collection Report - Shipping
            </h3>
            <div class="flex gap-2">
                <button class="btn btn-primary" onclick="printContent('printContainer')">PRINT</button>
            </div>
        </div>
        
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white print-container" style="font-family: Arial, sans-serif;">
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
                    <span style="font-weight: bold; font-size: 17px;">DAILY CASH COLLECTION REPORT - SHIPPING</span>
                </div>
                
                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 12px; line-height: 0;">
                    <div style="width: 60%;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>DATE RANGE:</strong> 
                            <input type="date" id="startDate" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" value="{{ date('Y-m-d') }}">
                            to 
                            <input type="date" id="endDate" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" value="{{ date('Y-m-d') }}">
                        </p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>REPORT TYPE:</strong> Shipping Operations</p>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>GENERATED DATE:</strong> {{ date('F d, Y') }}</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>REPORT NO.:</strong> 
                            <input type="text" id="reportNumber" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" placeholder="Enter Report No.">
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="font-family: Arial, sans-serif; font-size: 11px; table-layout: fixed; width: 100%;">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <!--th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th-->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th colspan="5" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">FREIGHT </th>
                                <th colspan="5" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">OTHER INCOME</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"></th>
                            </tr>
                            <tr>
                                <!--th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th-->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">AR</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">OR</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 2</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 3</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 4</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 5</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 2</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 3</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 4</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MV EVERWIN STAR 5</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">WHARFAGE PAYABLES</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">INTEREST</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">TOTAL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remark</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Sample data rows - replace with actual data -->
                            <tr style="font-family: Arial, sans-serif; font-size: 10px; line-height: 1.2;">
                                <!--td class="px-4 py-2 text-center">{{ date('m/d/Y') }}</td-->
                                <td class="px-4 py-2">AR001</td>
                                <td class="px-4 py-2">OR001</td>
                                <td class="px-4 py-2">John Doe Shipping Co.</td>
                                <td class="px-4 py-2 text-right">5,000.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">10,000.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">15,000.00</td>
                                <td class="px-4 py-2">General Cargo</td>
                                <td class="px-4 py-2 text-center">Edit</td>
                            </tr>
                            <tr style="font-family: Arial, sans-serif; font-size: 10px; line-height: 1.2;">
                                <td class="px-4 py-2 text-center">{{ date('m/d/Y') }}</td>
                                <td class="px-4 py-2">AR002</td>
                                <td class="px-4 py-2">OR002</td>
                                <td class="px-4 py-2">ABC Trading Corporation</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">8,500.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">8,500.00</td>
                                <td class="px-4 py-2">Electronics</td>
                                <td class="px-4 py-2 text-center">Edit</td>
                            </tr>
                            <tr style="font-family: Arial, sans-serif; font-size: 10px; line-height: 1.2;">
                                <td class="px-4 py-2 text-center">{{ date('m/d/Y') }}</td>
                                <td class="px-4 py-2">AR003</td>
                                <td class="px-4 py-2">-</td>
                                <td class="px-4 py-2">XYZ Logistics Ltd.</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">22,750.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">22,750.00</td>
                                <td class="px-4 py-2">Construction Materials</td>
                                <td class="px-4 py-2 text-center">Edit</td>
                            </tr>
                            <tr style="font-family: Arial, sans-serif; font-size: 10px; line-height: 1.2;">
                                <td class="px-4 py-2 text-center">{{ date('m/d/Y') }}</td>
                                <td class="px-4 py-2">AR004</td>
                                <td class="px-4 py-2">OR004</td>
                                <td class="px-4 py-2">Pacific Freight Services</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">6,200.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">0.00</td>
                                <td class="px-4 py-2 text-right">6,200.00</td>
                                <td class="px-4 py-2">Household Items</td>
                                <td class="px-4 py-2 text-center">Edit</td>
                            </tr>
                            
                            <!-- Summary rows -->
                            <tr class="bg-gray-50 dark:bg-gray-900 font-semibold" style="line-height: 0.8;">
                                <td colspan="19" class="px-4 py-1 text-right"></td>
                            </tr>
                            <tr class="font-semibold" style="line-height: 0.8; background-color:rgb(97, 175, 91); color: black;">
                                <td colspan="4" class="px-4 py-1" style="text-align: center; font-weight: bold;">TOTAL COLLECTIONS:</td>
                                <td class="px-4 py-1 text-right">5,000.00</td>
                                <td class="px-4 py-1 text-right">14,700.00</td>
                                <td class="px-4 py-1 text-right">22,750.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">10,000.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">52,450.00</td>
                                <td colspan="2" class="px-4 py-1"></td>
                            </tr>
                            <tr class="font-semibold" style="line-height: 0.8; background-color:rgb(255, 255, 0); color: black;">
                                <td colspan="4" class="px-4 py-1" style="text-align: center; font-weight: bold;">PAID COLLECTIONS:</td>
                                <td class="px-4 py-1 text-right">5,000.00</td>
                                <td class="px-4 py-1 text-right">14,700.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">10,000.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">29,700.00</td>
                                <td colspan="2" class="px-4 py-1"></td>
                            </tr>
                            <tr class="font-semibold" style="line-height: 0.8; background-color:rgb(255, 165, 0); color: black;">
                                <td colspan="4" class="px-4 py-1" style="text-align: center; font-weight: bold;">PENDING COLLECTIONS:</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">22,750.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">0.00</td>
                                <td class="px-4 py-1 text-right">22,750.00</td>
                                <td colspan="2" class="px-4 py-1"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Signature section for screen display -->
        <div class="signature-section">
            <div style="margin: 40px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                <div style="width: 60%;">
                    <p style="margin: 0; line-height: 2;"><strong>PREPARED BY:</strong></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">CHERRY MAE E. CAMAYA</strong></p>
                    <p style="margin-top: 0; margin-left: 145px; font-size: 13px; line-height: 1;">Accounting Officer</p>
                </div>
                <div style="width: 35%; text-align: left; line-height: 1;">
                    <p style="margin-bottom: 0; line-height: 2;"><strong>REVIEWED BY:</strong></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                    <p style="margin-top: 0; margin-left: 100px; font-size: 13px; line-height: 1;">Signature over Printed Name</p>
                    <p style="margin-bottom: 0; line-height: 0;"><strong>DATE:</strong></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                    <p style="margin-top: 0; margin-left: 150px; font-size: 13px; line-height: 1;">MM/DD/YR</p>
                </div>
            </div>
        </div>
        
        <!-- Print-only signature section that will stick to bottom -->
        <div class="print-signature" style="display: none;">
            <div style="margin: 40px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 12px; line-height: 1;">
                <div style="width: 60%;">
                    <p style="margin: 0; line-height: 2;"><strong>PREPARED BY:</strong></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">CHERRY MAE E. CAMAYA</strong></p>
                    <p style="margin-top: 0; margin-left: 145px; font-size: 13px; line-height: 1;">Accounting Officer</p>
                </div>
                <div style="width: 35%; text-align: left; line-height: 1;">
                    <p style="margin-bottom: 0; line-height: 2;"><strong>REVIEWED BY:</strong></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                    <p style="margin-top: 0; margin-left: 100px; font-size: 13px; line-height: 1;">Signature over Printed Name</p>
                    <p style="margin-bottom: 0; line-height: 0;"><strong>DATE:</strong></p>
                    <p style="margin-bottom: 0; line-height: 1;"><strong style="margin-left: 100px; font-size: 12px; text-decoration: underline; font-size: 14px;">_________________________</strong></p>
                    <p style="margin-top: 0; margin-left: 150px; font-size: 13px; line-height: 1;">MM/DD/YR</p>
                </div>
            </div>
        </div>
    </div>

    <!-- For printing -->
    <script>
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
            
            // Replace input fields with spans containing the current values for print
            var inputsInClone = printClone.querySelectorAll('input');
            var originalInputsAll = printContainer.querySelectorAll('input');
            inputsInClone.forEach(function(inputInClone, index) {
                if (originalInputsAll[index]) {
                    var currentValue = originalInputsAll[index].value;
                    var span = document.createElement('span');
                    span.textContent = currentValue;
                    span.style.fontFamily = 'Arial, sans-serif';
                    span.style.fontSize = '12px';
                    span.style.fontWeight = 'normal';
                    span.style.color = 'black';
                    inputInClone.parentNode.replaceChild(span, inputInClone);
                }
            });

            // Get print content from the modified clone
            var printContents = printClone.innerHTML;

            // Open new print window
            var printWindow = window.open("", "", "width=1000,height=800");
            printWindow.document.write("<html><head><title>Daily Cash Collection Report - Shipping</title>");
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
        }
    </script>
</x-app-layout>
