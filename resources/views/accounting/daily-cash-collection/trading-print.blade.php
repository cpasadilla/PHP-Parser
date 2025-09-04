<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Daily Cash Collection Report - Trading') }}
            </h2>
        </div>
    </x-slot>

    <style>
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        .btn-info {
            background-color: #06b6d4;
            color: white;
        }
        .btn-info:hover {
            background-color: #0891b2;
        }
    </style>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">
                Daily Cash Collection Report - Trading
            </h3>
            <div class="flex gap-2">
                <button class="btn btn-primary" onclick="printContent('printContainer')">PRINT</button>
                <button class="btn btn-secondary" onclick="openDccrModal()">Edit DCCR No.</button>
                <button class="btn btn-info" onclick="openCashierModal()">Edit Collected By</button>
                <button id="exportExcel" class="btn" style="background-color: #10b981; color: white;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">Export to Excel</button>
                <!--button class="btn btn-info" onclick="openCollectionModal()">Edit ADD: COLLECTION</button-->
            </div>
        </div>
        
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white print-container" style="font-family: Arial, sans-serif;">
            <div class="main-content">
                <div style="display: flex; align-items: flex-start; gap: 0; margin-bottom: 0;  padding: 10px;">
                    <div style="flex: 0 0 auto; display: flex; align-items: flex-start; margin-left: 40px;">
                        <img style="height: 90px; width: auto;" src="{{ asset('images/logo.png') }}" alt="Logo">
                    </div>
                    <div style="flex: 1 1 auto; line-height: 1; text-align: center; margin-right: 40px;">
                        <p style="margin: 0; font-size: 14px; font-weight: bold; color: #00B050; padding: 2px 5px; text-decoration: underline; font-family: 'Cambria', serif;">SAINT FRANCIS XAVIER STAR SHIPPING LINES INC.</p>
                        <p style="margin: 0; font-size: 11px; padding: 2px 5px; font-style: italic; font-family: 'Cambria', serif;">Basco Office: National Road Brgy. Kaychanarianan, Basco Batanes</p>
                        <p style="margin: 0; margin-bottom: 0; padding: 2px 5px; line-height: 1; font-size: 12px; font-weight: bold; font-family: 'Cambria', serif;">DAILY CASH COLLECTION REPORT</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; color: #00B050; font-weight: bold; font-family: 'Cambria', serif;">TRADING</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-family: 'Cambria', serif;">DATE: {{ $selectedDate ? strtoupper(\Carbon\Carbon::parse($selectedDate)->format('F d, Y')) : strtoupper(date('F d, Y')) }}</p>
                        <p style="margin: 0; font-size: 12px; padding: 2px 5px; font-family: 'Cambria', serif; text-align: right; font-weight: bold; ">DCCR No. : 
                        @if(isset($reportSettings) && $reportSettings && $reportSettings->dccr_number)
                            {{ $reportSettings->dccr_number }}
                        @else
                            ___________
                        @endif
                        </p>
                    </div>
                </div>
                
                <!--div style="margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 12px; line-height: 0;">
                    <div style="width: 60%;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>DATE RANGE:</strong> 
                            <input type="date" id="startDate" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" value="{{ date('Y-m-d') }}">
                            to 
                            <input type="date" id="endDate" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" value="{{ date('Y-m-d') }}">
                        </p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>REPORT TYPE:</strong> Trading Operations</p>
                    </div>
                    <div style="width: 35%; text-align: left; line-height: 1;">
                        <p style="margin-bottom: 0; line-height: 1;"><strong>GENERATED DATE:</strong> {{ date('F d, Y') }}</p>
                        <p style="margin-bottom: 0; line-height: 1;"><strong>REPORT NO.:</strong> 
                            <input type="text" id="reportNumber" style="border: 1px solid #ccc; padding: 2px 5px; width: 120px; font-family: Arial, sans-serif; font-size: 12px;" placeholder="Enter Report No.">
                        </p>
                    </div>
                </div-->

                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="font-family: 'Cambria', serif; font-size: 11px; table-layout: fixed; width: 100%;">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th style="width: 8%;" class="px-4 py-2">AR</th>
                                <th style="width: 8%;" class="px-4 py-2">OR</th>
                                <th style="width: 15%;" class="px-4 py-2">Name</th>
                                <th style="width: 10%;" class="px-4 py-2">Gravel & Sand</th>
                                <th style="width: 8%;" class="px-4 py-2">CHB</th>
                                <th style="width: 10%;" class="px-4 py-2">Other Income (Cement)</th>
                                <th style="width: 10%;" class="px-4 py-2">Other Income (DF)</th>
                                <th style="width: 8%;" class="px-4 py-2">Others</th>
                                <th style="width: 8%;" class="px-4 py-2">Interest</th>
                                <th style="width: 10%;" class="px-4 py-2">Total</th>
                                <th style="width: 15%;" class="px-4 py-2">Remark</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $totalGravelSand = 0;
                                $totalCHB = 0;
                                $totalCement = 0;
                                $totalDF = 0;
                                $totalOthers = 0;
                                $totalInterest = 0;
                                $grandTotal = 0;
                            @endphp
                            
                            @forelse($entries as $entry)
                                @php
                                    $totalGravelSand += $entry->gravel_sand;
                                    $totalCHB += $entry->chb;
                                    $totalCement += $entry->other_income_cement;
                                    $totalDF += $entry->other_income_df;
                                    $totalOthers += $entry->others;
                                    $totalInterest += $entry->interest;
                                    $grandTotal += $entry->total;
                                @endphp
                                <tr style="font-family: 'Cambria', serif; font-size: 10px; line-height: 1.2;">
                                    <td class="px-4 py-2 text-center">{{ $entry->ar }}</td>
                                    <td class="px-4 py-2 text-center">{{ $entry->or }}</td>
                                    <td class="px-4 py-2">{{ $entry->customer_name }}</td>
                                    <td class="px-4 py-2 text-right">{{ $entry->gravel_sand > 0 ? number_format($entry->gravel_sand, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-right">{{ $entry->chb > 0 ? number_format($entry->chb, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-right">{{ $entry->other_income_cement > 0 ? number_format($entry->other_income_cement, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-right">{{ $entry->other_income_df > 0 ? number_format($entry->other_income_df, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-right">{{ $entry->others > 0 ? number_format($entry->others, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-right">{{ $entry->interest > 0 ? number_format($entry->interest, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($entry->total, 2) }}</td>
                                    <td class="px-4 py-2">{{ $entry->remark }}</td>
                                </tr>
                            @empty
                                <tr style="font-family: 'Cambria', serif; font-size: 10px; line-height: 1.2;">
                                    <td colspan="11" class="px-4 py-2 text-center">No entries found for this period.</td>
                                </tr>
                            @endforelse
                            
                            @if($entries->count() > 0)
                                <!-- Grand Total Row -->
                                <tr class="font-semibold" style="line-height: 0.8; background-color: #92d050; color: black;">
                                    <td class="px-4 py-1 text-center"></td>
                                    <td class="px-4 py-1 text-center"></td>
                                    <td class="px-4 py-1" style="text-align: center; font-weight: bold;">TOTAL:</td>
                                    <td class="px-4 py-1 text-right">{{ $totalGravelSand > 0 ? number_format($totalGravelSand, 2) : '-' }}</td>
                                    <td class="px-4 py-1 text-right">{{ $totalCHB > 0 ? number_format($totalCHB, 2) : '-' }}</td>
                                    <td class="px-4 py-1 text-right">{{ $totalCement > 0 ? number_format($totalCement, 2) : '-' }}</td>
                                    <td class="px-4 py-1 text-right">{{ $totalDF > 0 ? number_format($totalDF, 2) : '-' }}</td>
                                    <td class="px-4 py-1 text-right">{{ $totalOthers > 0 ? number_format($totalOthers, 2) : '-' }}</td>
                                    <td class="px-4 py-1 text-right">{{ $totalInterest > 0 ? number_format($totalInterest, 2) : '-' }}</td>
                                    <td class="px-4 py-1 text-right">{{ number_format($grandTotal, 2) }}</td>
                                    <td class="px-4 py-1"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Print-only signature section that will be included in print -->
                <div class="print-signature" style="display: none;">
                    <div style="margin: 25px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 11px; line-height: 1;">
                        <div style="width: 28%; padding-left: 150px; font-family: 'Cambria', serif;">
                            <p style="margin: 0; line-height: 1.3;">CASH ON HAND BEGINNING:</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3;"><strong>ADD: COLLECTION</strong></p>
                            <p style="margin: 0; line-height: 1.3;">TOTAL CASH ON HAND:</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3;"><strong>LESS: DEPOSIT</strong></p>
                            <p style="margin: 0; line-height: 1.3;">LBP -Account</p>
                            <p style="margin: 0; line-height: 1.3;">PNB - Account</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3;">UnionBank - Account</p>
                            <p style="margin: 0; line-height: 1.3;">CASH ON HAND:</p>
                        </div>
                        <div style="width: 22%; padding: 3px; font-family: 'Cambria', serif;">
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; border-bottom: 2px solid black; padding-bottom: 3px; text-align: right;">{{ number_format($grandTotal, 2) }}</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; border-bottom: 2px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                        </div>
                        <div style="width: 25%; padding: 3px; text-align: right; font-family: 'Cambria', serif;">
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3;">Prepared By:</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3;">Collected By:</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3;">Noted By:</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                        </div>
                        <div style="width: 25%; padding: 3px;">
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center; font-family: 'Bookman Old Style', serif;"><strong>CHRISTY NUÑEZ</strong></p>
                            <p style="margin: 0; line-height: 1.3; text-align: center; font-size: 10px; font-family: 'Calibri', sans-serif;">Bookkeeper</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center; font-family: 'Bookman Old Style', serif;"><strong>
                                @if(isset($reportSettings) && $reportSettings && $reportSettings->collected_by_name)
                                    {{ strtoupper($reportSettings->collected_by_name) }}
                                @else
                                    RANDAL HERUELA
                                @endif
                            </strong></p>
                            <p style="margin: 0; line-height: 1.3; text-align: center; font-size: 10px; font-family: 'Calibri', sans-serif;">
                                @if(isset($reportSettings) && $reportSettings && $reportSettings->collected_by_title)
                                    {{ $reportSettings->collected_by_title }}
                                @else
                                    Cashier
                                @endif
                            </p>
                            <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                            <p style="margin: 0; line-height: 1.3; text-align: center; font-family: 'Bookman Old Style', serif;"><strong>ANTONIO L. CASTRO</strong></p>
                            <p style="margin: 0; line-height: 1.3; text-align: center; font-size: 10px; font-family: 'Calibri', sans-serif;">Operations Manager</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature section for screen display -->
        <div class="signature-section">
            <div style="margin: 25px 0 0 0; padding: 0; display: flex; justify-content: space-between; font-size: 11px; line-height: 1;">
                <div style="width: 28%; padding-left: 150px; font-family: 'Cambria', serif;">
                    <p style="margin: 0; line-height: 1.3;">CASH ON HAND BEGINNING:</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3;"><strong>ADD: COLLECTION</strong></p>
                    <p style="margin: 0; line-height: 1.3;">TOTAL CASH ON HAND:</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3;"><strong>LESS: DEPOSIT</strong></p>
                    <p style="margin: 0; line-height: 1.3;">LBP -Account</p>
                    <p style="margin: 0; line-height: 1.3;">PNB - Account</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3;">UnionBank - Account</p>
                    <p style="margin: 0; line-height: 1.3;">CASH ON HAND:</p>
                </div>
                <div style="width: 22%; padding: 3px; font-family: 'Cambria', serif;">
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; border-bottom: 2px solid black; padding-bottom: 3px; text-align: right;">{{ number_format($grandTotal, 2) }}</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; border-bottom: 2px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                </div>
                <div style="width: 25%; padding: 3px; text-align: right; font-family: 'Cambria', serif;">
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3;">Prepared By:</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3;">Collected By:</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3;">Noted By:</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                </div>
                <div style="width: 25%; padding: 3px;">
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center; font-family: 'Bookman Old Style', serif;"><strong>CHRISTY NUÑEZ</strong></p>
                    <p style="margin: 0; line-height: 1.3; text-align: center; font-size: 10px; font-family: 'Calibri', sans-serif;">Bookkeeper</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center; font-family: 'Bookman Old Style', serif;"><strong>
                        @if(isset($reportSettings) && $reportSettings && $reportSettings->collected_by_name)
                            {{ strtoupper($reportSettings->collected_by_name) }}
                        @else
                            RANDAL HERUELA
                        @endif
                    </strong></p>
                    <p style="margin: 0; line-height: 1.3; text-align: center; font-size: 10px; font-family: 'Calibri', sans-serif;">
                        @if(isset($reportSettings) && $reportSettings && $reportSettings->collected_by_title)
                            {{ $reportSettings->collected_by_title }}
                        @else
                            Cashier
                        @endif
                    </p>
                    <p style="margin: 0; line-height: 1.3; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; border-bottom: 1px solid black; padding-bottom: 3px; text-align: center;">&nbsp;</p>
                    <p style="margin: 0; line-height: 1.3; text-align: center; font-family: 'Bookman Old Style', serif;"><strong>ANTONIO L. CASTRO</strong></p>
                    <p style="margin: 0; line-height: 1.3; text-align: center; font-size: 10px; font-family: 'Calibri', sans-serif;">Operations Manager</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include XLSX library for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
                input.style.fontFamily = "'Cambria', serif";
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
            tempContainer.style.fontFamily = "'Cambria', serif";
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
                    span.style.fontFamily = "'Cambria', serif";
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
            printWindow.document.write("<html><head><title>Daily Cash Collection Report - Trading</title>");
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
                        font-family: 'Cambria', serif; 
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
                        font-family: 'Cambria', serif; 
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
                        font-family: 'Cambria', serif; 
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
                        font-family: 'Cambria', serif; 
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
                        font-family: 'Cambria', serif;
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

        // DCCR Modal Functions
        function openDccrModal() {
            document.getElementById('dccrModal').style.display = 'block';
            document.getElementById('dccrModalOverlay').style.display = 'block';
            loadCurrentDccrNumber();
        }

        function closeDccrModal() {
            document.getElementById('dccrModal').style.display = 'none';
            document.getElementById('dccrModalOverlay').style.display = 'none';
        }

        function loadCurrentDccrNumber() {
            const selectedDate = '{{ $selectedDate ?? date("Y-m-d") }}';
            
            fetch(`{{ route('accounting.daily-cash-collection.get-settings') }}?report_date=${selectedDate}&report_type=trading`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    document.getElementById('dccr_number').value = data.data.dccr_number || '';
                } else {
                    document.getElementById('dccr_number').value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('dccr_number').value = '';
            });
        }

        function saveDccrNumber() {
            const selectedDate = '{{ $selectedDate ?? date("Y-m-d") }}';
            const dccrNumber = document.getElementById('dccr_number').value;

            const formData = new FormData();
            formData.append('report_date', selectedDate);
            formData.append('report_type', 'trading');
            formData.append('dccr_number', dccrNumber);
            // Only send dccr_number field

            fetch('{{ route("accounting.daily-cash-collection.store-settings") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('DCCR Number saved successfully!');
                    closeDccrModal();
                    location.reload();
                } else {
                    alert('Error saving DCCR Number: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving DCCR Number');
            });
        }

        // Cashier Modal Functions
        function openCashierModal() {
            document.getElementById('cashierModal').style.display = 'block';
            document.getElementById('cashierModalOverlay').style.display = 'block';
            loadCurrentCashier();
        }

        function closeCashierModal() {
            document.getElementById('cashierModal').style.display = 'none';
            document.getElementById('cashierModalOverlay').style.display = 'none';
        }

        function loadCurrentCashier() {
            const selectedDate = '{{ $selectedDate ?? date("Y-m-d") }}';
            
            fetch(`{{ route('accounting.daily-cash-collection.get-settings') }}?report_date=${selectedDate}&report_type=trading`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const collectedByName = data.data.collected_by_name || 'RANDAL HERUELA';
                    const collectedByTitle = data.data.collected_by_title || 'Cashier';
                    
                    // Set the selected values
                    document.getElementById('collected_by_name').value = collectedByName;
                    document.getElementById('collected_by_title').value = collectedByTitle;
                    
                    // Update title dropdown based on name selection
                    updateTitleOptions();
                } else {
                    document.getElementById('collected_by_name').value = 'RANDAL HERUELA';
                    document.getElementById('collected_by_title').value = 'Cashier';
                    updateTitleOptions();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('collected_by_name').value = 'RANDAL HERUELA';
                document.getElementById('collected_by_title').value = 'Cashier';
                updateTitleOptions();
            });
        }

        function updateTitleOptions() {
            const collectedByName = document.getElementById('collected_by_name').value;
            const titleSelect = document.getElementById('collected_by_title');
            
            // Clear existing options
            titleSelect.innerHTML = '';
            
            if (collectedByName === 'RANDAL HERUELA') {
                titleSelect.innerHTML = '<option value="Cashier">Cashier</option>';
                titleSelect.value = 'Cashier';
            } else if (collectedByName === 'CHERRY MAE E. CAMAYA') {
                titleSelect.innerHTML = '<option value="Billing Officer">Billing Officer</option>';
                titleSelect.value = 'Billing Officer';
            }
        }

        function saveCashier() {
            const selectedDate = '{{ $selectedDate ?? date("Y-m-d") }}';
            const collectedByName = document.getElementById('collected_by_name').value;
            const collectedByTitle = document.getElementById('collected_by_title').value;

            const formData = new FormData();
            formData.append('report_date', selectedDate);
            formData.append('report_type', 'trading');
            formData.append('collected_by_name', collectedByName);
            formData.append('collected_by_title', collectedByTitle);
            // Only send collected_by fields

            fetch('{{ route("accounting.daily-cash-collection.store-settings") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Collected By information saved successfully!');
                    closeCashierModal();
                    location.reload();
                } else {
                    alert('Error saving Collected By information: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving Collected By information');
            });
        }

        // Collection Modal Functions
        function openCollectionModal() {
            document.getElementById('collectionModal').style.display = 'block';
            document.getElementById('collectionModalOverlay').style.display = 'block';
            loadCurrentCollection();
        }

        function closeCollectionModal() {
            document.getElementById('collectionModal').style.display = 'none';
            document.getElementById('collectionModalOverlay').style.display = 'none';
        }

        function loadCurrentCollection() {
            const selectedDate = '{{ $selectedDate ?? date("Y-m-d") }}';
            
            fetch(`{{ route('accounting.daily-cash-collection.get-settings') }}?report_date=${selectedDate}&report_type=trading`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    document.getElementById('add_collection').value = data.data.add_collection || '';
                } else {
                    document.getElementById('add_collection').value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('add_collection').value = '';
            });
        }

        function saveCollection() {
            const selectedDate = '{{ $selectedDate ?? date("Y-m-d") }}';
            const addCollection = document.getElementById('add_collection').value;

            const formData = new FormData();
            formData.append('report_date', selectedDate);
            formData.append('report_type', 'trading');
            formData.append('add_collection', addCollection);
            // Only send add_collection field

            fetch('{{ route("accounting.daily-cash-collection.store-settings") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('ADD: COLLECTION saved successfully!');
                    closeCollectionModal();
                    location.reload();
                } else {
                    alert('Error saving ADD: COLLECTION: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving ADD: COLLECTION');
            });
        }

        // Close modal when clicking outside
        // Close modal when clicking outside
        window.onclick = function(event) {
            const dccrModal = document.getElementById('dccrModal');
            const collectionModal = document.getElementById('collectionModal');
            const cashierModal = document.getElementById('cashierModal');
            const dccrOverlay = document.getElementById('dccrModalOverlay');
            const collectionOverlay = document.getElementById('collectionModalOverlay');
            const cashierOverlay = document.getElementById('cashierModalOverlay');
            
            if (event.target == dccrOverlay) {
                closeDccrModal();
            }
            if (event.target == collectionOverlay) {
                closeCollectionModal();
            }
            if (event.target == cashierOverlay) {
                closeCashierModal();
            }
        }
    </script>

    <!-- Export to Excel functionality -->
    <script>
        // Function to export table to Excel
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('exportExcel').addEventListener('click', function () {
                // Get the table element
                const table = document.querySelector('#printContainer table');
                
                if (!table) {
                    alert('No table found to export');
                    return;
                }

                // Get current data for calculations and information
                const selectedDate = '{{ $selectedDate ?? date("Y-m-d") }}';
                const reportDate = selectedDate || '{{ date("Y-m-d") }}';
                const formattedDate = new Date(reportDate).toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });

                // Get DCCR Number
                const dccrNumber = '{{ isset($reportSettings) && $reportSettings && $reportSettings->dccr_number ? $reportSettings->dccr_number : "___________" }}';
                
                // Get Collected By information
                const collectedByName = '{{ isset($reportSettings) && $reportSettings && $reportSettings->collected_by_name ? strtoupper($reportSettings->collected_by_name) : "RANDAL HERUELA" }}';
                const collectedByTitle = '{{ isset($reportSettings) && $reportSettings && $reportSettings->collected_by_title ? $reportSettings->collected_by_title : "Cashier" }}';

                // Calculate totals from PHP data
                const totalGravelSand = {{ $totalGravelSand ?? 0 }};
                const totalCHB = {{ $totalCHB ?? 0 }};
                const totalCement = {{ $totalCement ?? 0 }};
                const totalDF = {{ $totalDF ?? 0 }};
                const totalOthers = {{ $totalOthers ?? 0 }};
                const totalInterest = {{ $totalInterest ?? 0 }};
                const grandTotal = {{ $grandTotal ?? 0 }};

                // Create workbook and worksheet
                const workbook = XLSX.utils.book_new();
                
                // Create header data
                const headerData = [
                    ['SAINT FRANCIS XAVIER STAR SHIPPING LINES INC.'],
                    ['Basco Office: National Road Brgy. Kaychanarianan, Basco Batanes'],
                    ['DAILY CASH COLLECTION REPORT'],
                    ['TRADING'],
                    ['DATE: ' + formattedDate.toUpperCase()],
                    ['DCCR No.: ' + dccrNumber],
                    [''], // Empty row
                    [''], // Empty row
                ];

                // Get table headers
                const headerRow = table.querySelector('thead tr');
                const headers = [];
                for (let i = 0; i < headerRow.children.length; i++) {
                    headers.push(headerRow.children[i].textContent.trim());
                }
                headerData.push(headers);

                // Get table body data
                const bodyRows = table.querySelectorAll('tbody tr');
                const bodyData = [];
                
                bodyRows.forEach(row => {
                    if (row.children.length > 1 && !row.textContent.includes('No entries found')) {
                        const rowData = [];
                        for (let i = 0; i < row.children.length; i++) {
                            let cellText = row.children[i].textContent.trim();
                            
                            // Clean up numeric data - remove commas but keep numbers as text for proper formatting
                            if (cellText.includes(',') && !isNaN(cellText.replace(/,/g, ''))) {
                                cellText = parseFloat(cellText.replace(/,/g, ''));
                            }
                            
                            rowData.push(cellText);
                        }
                        bodyData.push(rowData);
                    }
                });

                // Footer data with cash flow section
                const footerData = [
                    [''], // Empty row
                    [''], // Empty row
                    ['CASH FLOW SUMMARY:'],
                    ['CASH ON HAND BEGINNING:', ''],
                    ['ADD: COLLECTION', grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})],
                    ['TOTAL CASH ON HAND:', ''],
                    [''], // Empty row
                    ['LESS: DEPOSIT'],
                    ['LBP - Account', ''],
                    ['PNB - Account', ''],
                    ['UnionBank - Account', ''],
                    ['CASH ON HAND:', ''],
                    [''], // Empty row
                    [''], // Empty row
                    ['SIGNATURE SECTION:'],
                    [''], // Empty row
                    ['Prepared By:', '', '', 'Collected By:', '', '', 'Noted By:'],
                    [''], // Empty row
                    [''], // Empty row
                    [''], // Empty row
                    ['CHRISTY NUÑEZ', '', '', collectedByName, '', '', 'ANTONIO L. CASTRO'],
                    ['Bookkeeper', '', '', collectedByTitle, '', '', 'Operations Manager'],
                    [''], // Empty row
                    [''], // Empty row
                    ['Generated on: ' + new Date().toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })],
                ];

                // Combine all data
                const allData = [...headerData, ...bodyData, ...footerData];

                // Create worksheet from array
                const worksheet = XLSX.utils.aoa_to_sheet(allData);

                // Set column widths
                const colWidths = [
                    { wch: 8 },  // AR
                    { wch: 8 },  // OR
                    { wch: 25 }, // Name
                    { wch: 15 }, // Gravel & Sand
                    { wch: 10 }, // CHB
                    { wch: 20 }, // Other Income (Cement)
                    { wch: 20 }, // Other Income (DF)
                    { wch: 10 }, // Others
                    { wch: 10 }, // Interest
                    { wch: 12 }, // Total
                    { wch: 30 }  // Remark
                ];
                worksheet['!cols'] = colWidths;

                // Style the header rows
                const range = XLSX.utils.decode_range(worksheet['!ref']);
                
                // Style company name (row 1)
                if (worksheet['A1']) {
                    worksheet['A1'].s = {
                        font: { bold: true, sz: 16, color: { rgb: "00B050" } },
                        alignment: { horizontal: "center" }
                    };
                }

                // Style other header rows (2-6)
                for (let i = 2; i <= 6; i++) {
                    const cellRef = 'A' + i;
                    if (worksheet[cellRef]) {
                        worksheet[cellRef].s = {
                            font: { bold: true, sz: 12 },
                            alignment: { horizontal: "center" }
                        };
                    }
                }

                // Style table headers (row 9)
                const headerRowIndex = 9;
                for (let col = 0; col < headers.length; col++) {
                    const cellRef = XLSX.utils.encode_cell({ r: headerRowIndex - 1, c: col });
                    if (worksheet[cellRef]) {
                        worksheet[cellRef].s = {
                            font: { bold: true },
                            fill: { fgColor: { rgb: "F2F2F2" } },
                            border: {
                                top: { style: "thin" },
                                bottom: { style: "thin" },
                                left: { style: "thin" },
                                right: { style: "thin" }
                            }
                        };
                    }
                }

                // Find and style totals row (look for "TOTAL:" in the data)
                const totalsRowIndex = headerData.length + bodyData.length - 1;
                for (let col = 0; col < headers.length; col++) {
                    const cellRef = XLSX.utils.encode_cell({ r: totalsRowIndex, c: col });
                    if (worksheet[cellRef]) {
                        worksheet[cellRef].s = {
                            font: { bold: true },
                            fill: { fgColor: { rgb: "92D050" } }
                        };
                    }
                }

                // Style cash flow summary section
                const cashFlowStartRow = headerData.length + bodyData.length + 3;
                for (let i = 0; i < 12; i++) {
                    const cellRef = 'A' + (cashFlowStartRow + i);
                    if (worksheet[cellRef]) {
                        worksheet[cellRef].s = {
                            font: { bold: i === 0 || i === 2 || i === 7 ? true : false },
                            fill: i === 0 ? { fgColor: { rgb: "E6F3FF" } } : undefined
                        };
                    }
                }

                // Style signature section
                const signatureStartRow = headerData.length + bodyData.length + 15;
                const signatureHeaderRef = 'A' + signatureStartRow;
                if (worksheet[signatureHeaderRef]) {
                    worksheet[signatureHeaderRef].s = {
                        font: { bold: true, sz: 12 },
                        fill: { fgColor: { rgb: "E6F3FF" } }
                    };
                }

                // Style signature names (make them bold)
                const nameRowIndex = signatureStartRow + 6;
                ['A', 'D', 'G'].forEach(col => {
                    const cellRef = col + nameRowIndex;
                    if (worksheet[cellRef]) {
                        worksheet[cellRef].s = {
                            font: { bold: true }
                        };
                    }
                });

                // Merge cells for header sections
                worksheet['!merges'] = [
                    { s: { r: 0, c: 0 }, e: { r: 0, c: headers.length - 1 } }, // Company name
                    { s: { r: 1, c: 0 }, e: { r: 1, c: headers.length - 1 } }, // Address
                    { s: { r: 2, c: 0 }, e: { r: 2, c: headers.length - 1 } }, // Report title
                    { s: { r: 3, c: 0 }, e: { r: 3, c: headers.length - 1 } }, // Trading
                    { s: { r: 4, c: 0 }, e: { r: 4, c: headers.length - 1 } }, // Date
                    { s: { r: 5, c: 0 }, e: { r: 5, c: headers.length - 1 } }  // DCCR Number
                ];

                // Add the worksheet to the workbook
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Trading Report');

                // Generate filename with current date
                const dateStr = reportDate.replace(/-/g, '');
                const filename = `Daily_Cash_Collection_Trading_${dateStr}.xlsx`;

                // Export the workbook to an Excel file
                XLSX.writeFile(workbook, filename);
            });
        });
    </script>

    <!-- DCCR Modal -->
    <div id="dccrModalOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-40"></div>
    <div id="dccrModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-lg max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Edit DCCR Number</h3>
                        <button type="button" onclick="closeDccrModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form onsubmit="event.preventDefault(); saveDccrNumber();">
                        <div class="mb-4">
                            <label for="dccr_number" class="block text-sm font-medium text-gray-700 mb-2">DCCR Number</label>
                            <input type="text" id="dccr_number" name="dccr_number" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter DCCR Number">
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeDccrModal()" 
                                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Modal -->
    <div id="collectionModalOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-40"></div>
    <div id="collectionModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-lg max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Edit ADD: COLLECTION</h3>
                        <button type="button" onclick="closeCollectionModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form onsubmit="event.preventDefault(); saveCollection();">
                        <div class="mb-4">
                            <label for="add_collection" class="block text-sm font-medium text-gray-700 mb-2">ADD: COLLECTION Amount</label>
                            <input type="number" id="add_collection" name="add_collection" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter collection amount">
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeCollectionModal()" 
                                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cashier Modal -->
    <div id="cashierModalOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-40"></div>
    <div id="cashierModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-lg max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Collected By Information</h3>
                        <button type="button" onclick="closeCashierModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form onsubmit="event.preventDefault(); saveCashier();">
                        <div class="mb-4">
                            <label for="collected_by_name" class="block text-sm font-medium text-gray-700 mb-2">Collected By Name</label>
                            <select id="collected_by_name" name="collected_by_name" onchange="updateTitleOptions()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="RANDAL HERUELA">RANDAL HERUELA</option>
                                <option value="CHERRY MAE E. CAMAYA">CHERRY MAE E. CAMAYA</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="collected_by_title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <select id="collected_by_title" name="collected_by_title" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Cashier">Cashier</option>
                                <option value="Billing Officer">Billing Officer</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeCashierModal()" 
                                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
