<x-app-layout>
    <x-slot name="header"></x-slot>
    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-end mb-4"></div>

        <!-- Form for updating BL -->
        <form id="myForm" method="POST" action="{{ route('masterlist.update-bl', $order->id) }}">
            @csrf
            <div id="printContainer" class="border p-6 shadow-lg text-black bg-white" style="display: flex; flex-direction: column; min-height: 11in;">
                <div style="flex: 1;">
                    <!-- Header -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <img style="width: 500px; height: 70px;" src="{{ asset('images/logo-sfx.png') }}" alt="Logo">
                        <div style="font-family: Arial; font-size: 12px; line-height: 1.2; margin-top: 3px;">
                            <p style="margin: 0;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                            <p style="margin: 0;">Cellphone Nos.: 0908-815-9300 / 0999-889-5848 / 0999-889-5849</p>
                            <p style="margin: 0;">Email Address: fxavier_2015@yahoo.com.ph</p>
                            <p style="margin: 0;">TIN: 009-081-111-000</p>
                        </div>
                    </div>

                    <!-- Title -->
                    <div style="display: flex; justify-content: center; margin-top: 5px;">
                        <span style="font-family: Arial; font-weight: bold; font-size: 17px;">BILL OF LADING</span>
                    </div>

                    <!-- Cargo Type -->
                    <div style="display: flex; font-weight: bold; justify-content: flex-end; align-items: center; text-align: right; font-size: 11px;">
                        <select id="cargoType" name="cargoType"
                            style="width: 90px; height: 21px; border: 1px solid #ccc; background: white; text-transform: uppercase; font-family: Arial; font-size: 11px; text-align: right; padding: 1px;">
                            <option value="" {{ $order->cargoType == '' ? 'selected' : '' }}></option>
                            <option value="CHARTERED" {{ $order->cargoType == 'CHARTERED' ? 'selected' : '' }}>CHARTERED</option>
                            <option value="LOOSE CARGO" {{ $order->cargoType == 'LOOSE CARGO' ? 'selected' : '' }}>LOOSE CARGO</option>
                            <option value="STUFFING" {{ $order->cargoType == 'STUFFING' ? 'selected' : '' }}>STUFFING</option>
                            <option value="BACKLOAD" {{ $order->cargoType == 'BACKLOAD' ? 'selected' : '' }}>BACKLOAD</option>
                        </select>
                    </div>
                    <!-- Form Fields -->
                    <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <tr>
                            <td style="font-family: Arial; font-size: 11px; text-align: left; width: 95px; padding: 1px;"><strong>M/V EVERWIN STAR</strong></td>
                            <td style="width: 45px;">
                                <input id="ship_no" name="ship_no" value="{{ $order->shipNum }}" required 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="width: 70px;"><strong>VOYAGE NO.:</strong></td>
                            <td style="width: 70px;">
                                <input id="voyage_no" name="voyage_no" value="{{ $order->voyageNum }}" 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="width: 85px;"><strong>CONTAINER NO.:</strong></td>
                            <td style="width: 145px;">
                                <input id="container_no" name="container_no" value="{{ $order->containerNum }}" 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="width: 40px;"><strong>BL NO.</strong></td>
                            <td style="width: 40px;">
                                <input id="orderId" name="orderId" value="{{ $order->orderId }}" required 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                        </tr>
                    </table>
                    <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <tr>
                            <td style="width: 45px;"><strong>ORIGIN:</strong></td>
                            <td>
                                <input id="origin" name="origin" value="{{ $order->origin }}" required 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="width: 65px;"><strong>DESTINATION:</strong></td>
                            <td>
                                <input id="destination" name="destination" value="{{ $order->destination }}" required 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="width: 45px;"><strong>DATE:</strong></td>
                            <td>
                                <input id="created_at" name="created_at" value="{{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y') }}" 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                        </tr>
                    </table>
                    <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; margin-top: 5px;">
                        <tr>
                            <td style="width: 45px;"><strong>SHIPPER:</strong></td>
                            <td>
                            <input id="shipperName" name="shipperName" list="shipperList" value="{{ $order->shipperName }}" required 
                                style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            <datalist id="shipperList">
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->company_name ?? $customer->first_name . ' ' . $customer->last_name }}">
                                @endforeach
                            </datalist>
                        </td>
                        <td style="width: 65px;"><strong>CONSIGNEE:</strong></td>
                        <td>
                            <input id="recName" name="recName" list="consigneeList" value="{{ $order->recName }}" required 
                                style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                                                        <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    // Function to fetch details for SHIPPER and CONSIGNEE
                                    function fetchDetails(inputId, phoneId, hiddenId, route) {
                                        const inputElement = document.getElementById(inputId);
                                        const phoneElement = document.getElementById(phoneId);
                                        const hiddenElement = document.getElementById(hiddenId);
                            
                                        inputElement.addEventListener("change", function () {
                                            const selectedName = inputElement.value;
                            
                                            if (selectedName) {
                                                // Make an AJAX request to fetch details
                                                fetch(route + "?name=" + encodeURIComponent(selectedName))
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            phoneElement.value = data.phone || ""; // Update phone number
                                                            hiddenElement.value = data.id || ""; // Update hidden ID
                                                        } else {
                                                            alert(data.message || "No matching record found for the selected name.");
                                                            phoneElement.value = "";
                                                            hiddenElement.value = "";
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error("Error fetching details:", error);
                                                        alert("An error occurred while fetching details.");
                                                    });
                                            }
                                        });
                                    }
                            
                                    // Attach the fetchDetails function to SHIPPER and CONSIGNEE fields
                                    fetchDetails("shipperName", "shipperNum", "shipperId", "{{ route('masterlist.search-customer-details') }}");
                                    fetchDetails("recName", "recNum", "consigneeId", "{{ route('masterlist.search-customer-details') }}");
                                });
                            </script><datalist id="consigneeList">
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->company_name ?? $customer->first_name . ' ' . $customer->last_name }}">
                                @endforeach
                            </datalist>
                        </td>
                        </tr>
                    </table>
                    <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <tr>
                            <td style="width: 85px; text-align: left;"><strong>CONTACT NO.</strong></td>
                            <td>
                                <input id="shipperNum" name="shipperNum" value="{{ $order->shipperNum }}" 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="width: 85px; text-align: left;"><strong>CONTACT NO.</strong></td>
                            <td>
                                <input id="recNum" name="recNum" value="{{ $order->recNum }}" 
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                        </tr>
                    </table>
                    <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <tr>
                            <td style="width: 53px; text-align: left;"><strong>GATE PASS NO.</strong></td>
                            <td style="width: 142px;">
                                <input id="gatePass" name="gatePass" value="{{ $order->gatePass }}"
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="width: 40px; text-align: left;"><strong>REMARK:</strong></td>
                            <td style="width: 150px;">
                                <input id="remark" name="remark" value="{{ $order->remark }}"
                                    style="width: 100%; border: none; border-bottom: 1px solid black; text-align: center; font-family: Arial; font-size: 11px;">
                            </td>
                            <td style="font-family: Arial; font-size: 11px; text-align: left; width: 20px; padding: 2px;"><strong>VALUE:</strong></td>
                            <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; width: 133px; text-align: center; padding: 0px; min-height: 22px;">
                                <input id="value" name="value" value="{{ $order->value }}"
                                    class="block w-full text-center border-none focus:ring focus:ring-indigo-300"
                                    type="text"
                                    oninput="formatValueInput(this)"
                                    style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; line-height: 1;"/>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" id="cartData" name="cartData">
                    <input type="hidden" id="cartTotal" name="cartTotal">
                    <input type="hidden" id="shipperId" name="shipperId" value="{{ $order->shipperId }}">
                    <input type="hidden" id="consigneeId" name="consigneeId" value="{{ $order->recId }}">
                    <input type="hidden" id="wharfage_manual" name="wharfage_manual" value="{{ $order->wharfage_manual ?? 0 }}">
                    <input type="hidden" id="freight_manual" name="freight_manual" value="{{ $order->freight_manual ?? 0 }}">

                    <!-- Main Table -->
                    <table class="w-full border-collapse text-sm" style="padding: 0 5px; margin-top: 20px;">
                        <thead class="text-white border border-gray" style="background-color: #78BF65;">
                            <tr class="border border-gray">
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">QTY</th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">UNIT</th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">DESCRIPTION</th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">VALUE</th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">WEIGHT</th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">MEASUREMENT</th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">RATE</th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">FREIGHT</th>
                                <th></th>
                                <th class="p-2" style="font-family: Arial; font-size: 13px;">
                                    <button id = "buts" onclick="openModal()" style="background: none; border: none; font-size: 20px; line-height: 1;">+</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="mainTableBody">

                        </tbody>
                    </table>
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
                        <td style="width: 25%; border-bottom: 1px solid black; font-family: Arial; font-size: 12px; text-align: center; position: relative;">
                            <input id="freight" name="freight" value="{{ $order->freight }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                class="freight-input"
                                style="font-family: Arial; font-size: 11px; width: 100%; height: 18px; border: none; outline: none; padding: 0px 72px 0px 0px; text-align: center; line-height: 1; background: transparent; display: block;"/>
                        </td>
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
                            <span id="wharfageLabel">
                                {{ strtoupper($order->origin) === 'BATANES' ? 'Wharfage Batanes:' : 'Wharfage:' }}
                            </span>
                        </td>
                        <td style="width: 25%; border-bottom: 1px solid black; font-family: Arial; font-size: 12px; text-align: center; position: relative;">
                            <input id="wharfage" name="wharfage" value="{{ $order->wharfage }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                class="wharfage-input"
                                style="font-family: Arial; font-size: 11px; width: 100%; height: 18px; border: none; outline: none; padding: 0px 72px 0px 0px; text-align: center; line-height: 1; background: transparent; display: block;"/>
                        </td>
                    </tr>
                    <tr>
                        <td id="checkerNameDisplay" style="font-family: Arial; font-size: 12px; width: 150px; border-bottom: 1px solid black; text-align: center;">
                            <select id="checkerName" name="checkerName[]" multiple style="width: 100%; min-height: 11px; border: none; background: transparent; text-align: center; font-family: Arial; font-size: 12px; font-weight: bold; padding: 0px; line-height: 1;" autofocus>
                                <option value="" {{ $order->checkName == '' ? 'selected' : '' }}>-- Select Checker --</option>
                                @if($order->checkName)
                                    @foreach(explode('/', $order->checkName) as $selectedChecker)
                                        <option value="{{ $selectedChecker }}" selected>{{ $selectedChecker }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">VAT:</td>
                        <td style="border-bottom: 1px solid black; text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-family: Arial; font-size: 12px;">Vessel's Checker or Authorized Representative</td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">Other Charges:</td>
                        <td style="width: 25%; border-bottom: 1px solid black; font-family: Arial; font-size: 12px; text-align: center; position: relative;">
                            <input id="other" name="other" value="{{ $order->other }}"
                                style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; text-align: center; line-height: 1; background: transparent; position: absolute; left: 0; right: 0; top: 0; bottom: 0;"/>
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
                        <td style="border-bottom: 1px solid black; font-weight: bold; font-family: Arial; font-size: 12px;  text-align: center; color: black;">
                            {{ number_format($order->totalAmount, 2) }}
                        </td>
                    </tr>
                </table>
            </footer>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-4">
                <button id="submitOrder" type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update BL</button>
            </div>

        </form>
            <!-- Modal -->
            <div id="addToCartModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50 p-4 z-50">
                <div class="bg-white dark:bg-[#222738] rounded-lg shadow-lg p-6 w-3/4 max-w-6xl max-h-[90vh] overflow-auto mx-4">
                    <!-- Top Row: Listing and Search Bar -->
                    <div class="flex justify-between items-center mb-2">
                        <!-- Left Side: Listing -->
                        <h5 class="font-semibold text-lg">Listing</h5>

                        <!-- Right Side: Search Bar with Button -->
                        <div class="flex items-center space-x-2">
                            <input type="text" id="searchInput" placeholder="Search items..."
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200" style="height: 32px;">
                            <button type="button" id="createNewItemBtn" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 w-32 whitespace-nowrap">
                                + New Item
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <!-- Left Column: Input Fields -->
                        <div class="w-1/2">
                            <label class="block font-medium text-gray-900 dark:text-gray-200">Item Code:</label>
                            <input type="text" id="itemCode" name="itemCode" readonly
                                class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200" style="height: 32px;">

                            <label class="block font-medium text-gray-900 dark:text-gray-200">Item Name:</label>
                            <input type="text" id="itemName" name="itemName" 
                                class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200" style="height: 32px;">

                            <label class="block font-medium text-gray-900 dark:text-gray-200">Category:</label>
                            <input type="text" id="category" name="category" readonly
                                class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200" style="height: 32px;">

                            <label class="block font-medium text-gray-900 dark:text-gray-200">Unit:</label>
                            <div class="flex items-center gap-2">
                                <select id="unit" name="unit"
                                    class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                                    <option value="">Select Unit</option>
                                    <option value=" "> </option>
                                    <option value="PC">PC</option>
                                    <option value="PCS">PCS</option>
                                    <option value="BALE">BALE</option>
                                    <option value="BALES">BALES</option>
                                    <option value="BOX">BOX</option>
                                    <option value="BXS">BXS</option>
                                    <option value="BAG">BAG</option>
                                    <option value="BAGS">BAGS</option>
                                    <option value="DRUM">DRUM</option>
                                    <option value="DRUMS">DRUMS</option>
                                    <option value="PAIL">PAIL</option>
                                    <option value="PAILS">PAILS</option>
                                    <option value="ROLL">ROLL</option>
                                    <option value="ROLL">ROLLS</option>
                                    <option value="SET">SET</option>
                                    <option value="SETS">SETS</option>
                                    <option value="SK">SK</option>
                                    <option value="SKS">SKS</option>
                                    <option value="TIN">TIN</option>
                                    <option value="TINS">TINS</option>
                                    <option value="UNIT">UNIT</option>
                                    <option value="UNITS">UNITS</option>
                                    <option value="BASKET">BASKET</option>
                                    <option value="BDLE">BDLE</option>
                                    <option value="CBM">CBM</option>
                                    <option value="CS">CS</option>
                                    <option value="CSK">CSK</option>
                                    <option value="CAN">CAN</option>
                                    <option value="CTN">CTN</option>
                                    <option value="CYL">CYL</option>
                                    <option value="CRATE">CRATE</option>
                                    <option value="GAL">GAL</option>
                                    <option value="HEAD">HEAD</option>
                                    <option value="PACK">PACK</option>
                                    <option value="PACKS">PACKS</option>
                                    <option value="PLSTC">PLSTC</option>
                                    <option value="PALLET">PALLET</option>
                                    <option value="KG">KG</option>
                                    <option value="NET">NET</option>
                                    <option value="RIM">RIM</option>
                                    <option value="RIMS">RIMS</option>
                                    <option value="SACK">SACK</option>
                                    <option value="TALE">TALE</option>
                                    <option value="TANK">TANK</option>
                                    <option value="TUB">TUB</option>
                                </select>

                                <button type="button" id="addUnit"class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add</button>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block font-medium text-gray-900 dark:text-gray-200">Price:</label>
                                    <input type="text" id="price" name="price" class="w-full p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-900 dark:text-gray-200">Weight:</label>
                                    <input type="text" id="weight" name="weight" class="w-full p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-900 dark:text-gray-200">Quantity:</label>
                                    <input type="text" id="quantity" name="quantity" class="w-full p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                            </div>

                            <!--label class="block font-medium text-gray-900 dark:text-gray-200">Declared Value:</label>
                            <input type="text" id="value" name="value"
                                class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"-->
                                <!-- Measurements -->
                            <div class="space-y-2" id="measurementsContainer">
                                <div class="flex justify-between items-center">
                                    <label class="block font-medium text-gray-900 dark:text-gray-200">Measurements (L × W × H):</label>
                                    <button type="button" id="addMeasurementBtn" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm mt-2">
                                        + Add Measurement
                                    </button>
                                </div>
                                <div id="measurementsList">
                                    <!-- Measurement entries will be added here -->
                                </div>
                            </div>

                            <label class="block font-medium text-gray-900 dark:text-gray-200">Description:</label>
                            <textarea id="description" name="description"
                                class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white h-20 focus:ring focus:ring-indigo-200">
                            </textarea>
                        </div>

                        <!-- Right Column: Table -->
                        <div class="w-2/3">
                            <div id="tableContainer"
                                class="border border-gray-300 dark:border-gray-600 resize-y overflow-auto p-2"
                                style="min-height: 100px; max-height: 415px; height: 500px;">
                                <table class="w-full border-collapse border border-gray-300 dark:border-gray-600 text-sm">
                                    <thead class="bg-gray-200 dark:bg-dark-eval-0">
                                        <tr>
                                            <th class="p-3 border">Item Code</th>
                                            <th class="p-3 border">Item Name</th>
                                            <th class="p-3 border ">Category
                                                <select id="categoryFilter"
                                                    class="w-full p-2 border rounded-md font-small bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                                                    <option value="">Show All</option>
                                                    @isset($lists)
                                                        @foreach($lists->pluck('category')->unique() as $category)
                                                            <option value="{{ $category }}">{{ $category }}</option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemTableBody">
                                        @if(isset($lists) && $lists->isNotEmpty())
                                            @foreach($lists as $list)
                                                <tr class="item-row" data-category="{{ $list->category }}">
                                                    <td class="p-3 border" style="text-align: left;">{{ $list->item_code }}</td>
                                                    <td class="p-3 border" style="text-align: left;">{{ $list->item_name }}</td>
                                                    <td class="p-3 border">{{ $list->category }}</td>
                                                    <td class="p-3 border" hidden>{{ number_format($list->price, 2) }}</td>
                                                    <td class="p-3 border" hidden>{{ $list->multiplier }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="p-3 border">No items found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-3">
                        <!-- Left Side: Clear Fields Button -->
                        <button type='button' onclick="clearFields()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Clear Fields
                        </button>

                        <!-- Right Side: Other Buttons -->
                        <div class="flex space-x-2">
                            <button  type='button' onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700
                                dark:bg-gray-600 dark:hover:bg-gray-500">
                                Cancel
                            </button>
                            <button  type='button' id="addToCart" onclick="addToCart()" class="px-4 py-2 bg-emerald-500 text-white rounded-md
                                hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-700 flex items-center space-x-2">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Add to Cart</span>
                            </button>
                            <button type='button' id="saveButton" onclick="saveUpdatedItem()" class="px-4 py-2 bg-emerald-500 text-white
                                rounded-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-700 flex items-center
                                space-x-2 hidden">
                                <i class="fas fa-floppy-disk"></i>
                                <span>Update</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Create Price List Item Modal -->
        <div id="createPriceListItemModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-[#222738] rounded-lg shadow-lg p-6 w-full max-w-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Create New Item</h2>
                    <button type="button" onclick="closeCreateItemModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>

                <form id="priceListItemForm">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Item Name -->
                        <div class="col-span-2">
                            <label class="block text-gray-700 dark:text-gray-300 mb-1">Item Name</label>
                            <input type="text" name="item_name" id="new_item_name" required 
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Category -->
                        <div class="col-span-1">
                            <label class="block text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <select name="category" id="new_category" required 
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                                <option value="">Select Category</option>
                                <option value="GENERAL MERCHANDISE">GENERAL MERCHANDISE</option>
                                <option value="APPLIANCES / FURNITURES">APPLIANCES / FURNITURES</option>
                                <option value="CONSTRUCTION MATERIALS">CONSTRUCTION MATERIALS</option>
                                <option value="FUEL / LPG">FUEL / LPG</option>
                                <option value="PLYWOOD / LUMBER">PLYWOOD / LUMBER</option>
                                <option value="VEHICLES">VEHICLES</option>
                                <option value="BACKLOAD">BACKLOAD</option>
                                <option value="VOLUME">VOLUME</option>
                                <option value="SHEET">SHEET</option>
                                <option value="STEEL PRODUCTS">STEEL PRODUCTS</option>
                                <option value="VARIOUS">VARIOUS</option>
                                <option value="FROZEN">FROZEN</option>
                                <option value="PARCEL">PARCEL</option>
                                <option value="SAND">SAND</option>
                            </select>
                        </div>

                        <!-- Price/Multiplier Value -->
                        <div class="col-span-1">
                            <label class="block text-gray-700 dark:text-gray-300 mb-1" id="price_label">Price</label>
                            <input type="number" name="price" id="new_price" step="0.01" min="0"
                                class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Measurements (Show only when Multiplier is selected) -->
                        <div class="col-span-1 hidden" id="measurements_group">
                            <label class="block text-gray-700 dark:text-gray-300 mb-1">Sample Measurements</label>
                            <div class="grid grid-cols-3 gap-2">
                                <input type="number" name="length" id="new_length" min="0" step="0.01" placeholder="L"
                                    class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                                <input type="number" name="width" id="new_width" min="0" step="0.01" placeholder="W"
                                    class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                                <input type="number" name="height" id="new_height" min="0" step="0.01" placeholder="H"
                                    class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between mt-6">
                        <button type="button" onclick="closeCreateItemModal()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none">
                            Cancel
                        </button>
                        <button type="submit" id="submitNewItem"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none">
                            Create Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <datalist id="multipliers">
        <option value="3256">
        <option value="3000">
        <option value="2500">
        <option value="2000">
        <option value="1500">
        <option value="1000">
        <option value="500">
    </datalist>
    @include('masterlist.checker-styles')
</x-app-layout>
<!-- For date generate and fetching of shipper and consignee -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log(cart);
        if (!Array.isArray(cart)) {
            cart = [cart]; // Convert single object into an array
        }
        
        // Ensure all cart items have required properties
        cart.forEach(item => {
            if (!item.hasOwnProperty('category') || !item.category) {
                console.warn('Cart item missing category property:', item);
                
                // Try to find the category from the price list
                setTimeout(() => {
                    const tableRows = document.querySelectorAll('#itemTableBody tr.item-row');
                    for (let row of tableRows) {
                        if (row.cells[0].textContent.trim() === item.itemCode) {
                            item.category = row.cells[2].textContent.trim();
                            console.log('Found category for item', item.itemCode, ':', item.category);
                            break;
                        }
                    }
                }, 100); // Short delay to ensure table is loaded
            }
        });
        
        checkCart();
        updateMainTable();
        
        // Recalculate total from cart items and initialize cartTotal hidden input
        recalculateTotalPrice();
        document.getElementById('cartTotal').value = totalPrice.toFixed(2);
        
        // Calculate freight and wharfage after initial total calculation (only if not manual)
        const freightManualFlag = document.getElementById('freight_manual')?.value === '1';
        const wharfageManualFlag = document.getElementById('wharfage_manual')?.value === '1';
        
        if (typeof calculateFreight === 'function' && !freightManualFlag) {
            calculateFreight();
        }
        if (typeof calculateWharfage === 'function' && !wharfageManualFlag) {
            calculateWharfage();
        }
        
        // Debug: Check if hidden inputs are properly set
        console.log("Initial cart data:", document.getElementById('cartData').value);
        console.log("Initial cart total:", document.getElementById('cartTotal').value);
        console.log("Initial total price variable:", totalPrice);
        
        // Function to format the date
        function formatDate(date) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Set the current date
        const currentDateElement = document.getElementById("currentDate");
        if (currentDateElement) {
            currentDateElement.textContent = formatDate(new Date());
        }

        // Format value input on page load
        const valueInput = document.getElementById('value');
        if (valueInput && valueInput.value) {
            formatValueInput(valueInput);
        }
    });
</script>
<!-- For Checker Name-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkerNames = {
            "MANILA": [" ", "ABELLO", "ALDAY", "ANCHETA", "BERNADOS", "CACHO", "ESGUERRA", "MORENO", "NALLAS", "VIRGINIO", "VICTORIANO", "YUMUL", "ZERRUDO"],
            "BATANES": [" ", "SOL", "TIRSO", "VARGAS", "NICK", "JOSIE", "JEN"]
        };

        const originSelect = document.getElementById("origin");
        const checkerNameSelect = document.getElementById("checkerName");

        function updateCheckerNames() {
            const selectedOrigin = originSelect.value.toUpperCase();
            const names = checkerNames[selectedOrigin] || [];
            const currentCheckerNames = "{{ $order->checkName }}".split('/').map(name => name.trim()); // Split by '/'

            // Clear previous options
            checkerNameSelect.innerHTML = "";

            // Add blank option first
            const blankOption = document.createElement("option");
            blankOption.value = "";
            blankOption.textContent = "-- Select Checker --";
            checkerNameSelect.appendChild(blankOption);

            // Add checker name options
            names.forEach(name => {
                const option = document.createElement("option");
                option.value = name;
                option.textContent = name;
                if (currentCheckerNames.includes(name)) {
                    option.selected = true; // Select if it was previously selected
                }
                checkerNameSelect.appendChild(option);
            });
        }

        // Format selected options with '/' separator and update display
        function formatSelectedOptions() {
            const selectedOptions = Array.from(checkerNameSelect.selectedOptions).map(option => option.value);
            // Remove empty values
            const filteredOptions = selectedOptions.filter(option => option.trim() !== "");
            return filteredOptions.join(' / ');
        }

        // Add change event to update display when selections change
        checkerNameSelect.addEventListener('change', function() {
            // Create a hidden input to store formatted value
            let hiddenInput = document.getElementById('formattedCheckerNames');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'formattedCheckerNames';
                hiddenInput.name = 'checkerName';
                checkerNameSelect.parentNode.appendChild(hiddenInput);
            }
            hiddenInput.value = formatSelectedOptions();
        });

        // Run function on change
        originSelect.addEventListener("change", updateCheckerNames);

        // Run function on page load
        updateCheckerNames();
        
        // Initial formatting
        setTimeout(() => {
            checkerNameSelect.dispatchEvent(new Event('change'));
        }, 100);
    });
</script>

<!-- For wharfage calculation -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Reference to important elements
        const freightInput = document.getElementById("freight");
        const valueInput = document.getElementById("value");
        const wharfageInput = document.getElementById("wharfage");
        const containerInput = document.getElementById("container_no");
        
        // Ensure there's a hidden input to tell server whether wharfage was manually overridden
        let manualFlagInput = document.getElementById('wharfage_manual');
        if (!manualFlagInput) {
            manualFlagInput = document.createElement('input');
            manualFlagInput.type = 'hidden';
            manualFlagInput.name = 'wharfage_manual';
            manualFlagInput.id = 'wharfage_manual';
            manualFlagInput.value = '0';
            const formEl = document.getElementById('myForm');
            if (formEl) formEl.appendChild(manualFlagInput);
        }

        // Local flag that determines whether automatic calculation should overwrite the field
        let manualWharfage = manualFlagInput.value === '1';

        // Initialize wharfage controls based on database value
        if (manualWharfage) {
            // If this is a manual wharfage from database, set the UI to manual mode
            try { wharfageInput.readOnly = false; } catch (e) {}
        } else {
            // Auto mode - input might be readonly for display
            try { wharfageInput.readOnly = true; } catch (e) {}
        }

        // Add a small icon button inside the existing TD so user can toggle manual/auto without breaking layout
        if (wharfageInput && wharfageInput.parentNode) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.id = 'manualWharfageToggle';
            btn.className = 'btn manual-wharfage-btn';
            btn.title = manualWharfage ? 'Using manual wharfage (click to reset to auto)' : 'Use manual wharfage (click to edit)';

            btn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" fill="currentColor"/>
                    <path d="M20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" fill="currentColor"/>
                </svg>
            `;

            // Append button into the parent TD (TD already has position: relative in the markup)
            const td = wharfageInput.parentNode;
            td.appendChild(btn);

            // Create a small status badge to show current mode (Auto / Manual)
            const status = document.createElement('span');
            status.id = 'wharfageStatus';
            status.style.position = 'absolute';
            status.style.right = '34px';
            status.style.top = '50%';
            status.style.transform = 'translateY(-50%)';
            status.style.fontSize = '10px';
            status.style.padding = '2px 6px';
            status.style.borderRadius = '10px';
            status.style.color = '#ffffff';
            status.style.background = manualWharfage ? '#e11d48' : '#10b981'; // red for manual, green for auto
            status.style.cursor = 'default';
            status.setAttribute('aria-live', 'polite');
            status.textContent = manualWharfage ? 'Manual' : 'Auto';
            td.appendChild(status);

            // Style the button to appear on the right side of the TD
            btn.style.position = 'absolute';
            btn.style.right = '6px';
            btn.style.top = '50%';
            btn.style.transform = 'translateY(-50%)';
            btn.style.border = 'none';
            btn.style.background = 'transparent';
            btn.style.cursor = 'pointer';
            btn.style.padding = '2px';
            btn.style.color = manualWharfage ? '#e11d48' : '#374151'; // red when manual, gray otherwise

            // Make sure the input has enough right padding so text doesn't go under the icon
            wharfageInput.style.paddingRight = '66px';

            // Set initial editable state: if automatic, prevent typing; if manual, allow typing
            try {
                wharfageInput.readOnly = !manualWharfage;
            } catch (e) {
                // ignore if element doesn't support readOnly
            }

            // When clicked: toggle manual/auto and update the status badge and input editability
            btn.addEventListener('click', function () {
                const statusEl = document.getElementById('wharfageStatus');
                if (manualWharfage) {
                    // Switch back to automatic
                    manualWharfage = false;
                    manualFlagInput.value = '0';
                    btn.style.color = '#374151';
                    if (statusEl) {
                        statusEl.style.background = '#10b981';
                        statusEl.textContent = 'Auto';
                    }
                    // Make input readonly and recalc value
                    try { wharfageInput.readOnly = true; } catch (e) {}
                    calculateWharfage();
                } else {
                    // Enable manual edit
                    manualWharfage = true;
                    manualFlagInput.value = '1';
                    btn.style.color = '#e11d48';
                    if (statusEl) {
                        statusEl.style.background = '#e11d48';
                        statusEl.textContent = 'Manual';
                    }
                    try { wharfageInput.readOnly = false; } catch (e) {}
                    wharfageInput.focus();
                }
                btn.title = manualWharfage ? 'Using manual wharfage (click to reset to auto)' : 'Use manual wharfage (click to edit)';
            });

            // If user types into wharfage, treat it as manual override and update status
            wharfageInput.addEventListener('input', function () {
                manualWharfage = true;
                manualFlagInput.value = '1';
                const btnEl = document.getElementById('manualWharfageToggle');
                const statusEl = document.getElementById('wharfageStatus');
                if (btnEl) btnEl.style.color = '#e11d48';
                if (statusEl) {
                    statusEl.textContent = 'Manual';
                    statusEl.style.background = '#e11d48';
                }
                try { wharfageInput.readOnly = false; } catch (e) {}
            });
        }

        // ========== FREIGHT MANUAL/AUTO FUNCTIONALITY ==========
        // Ensure there's a hidden input to tell server whether freight was manually overridden
        let freightManualFlagInput = document.getElementById('freight_manual');
        if (!freightManualFlagInput) {
            freightManualFlagInput = document.createElement('input');
            freightManualFlagInput.type = 'hidden';
            freightManualFlagInput.name = 'freight_manual';
            freightManualFlagInput.id = 'freight_manual';
            freightManualFlagInput.value = '0';
            const formEl = document.getElementById('myForm');
            if (formEl) formEl.appendChild(freightManualFlagInput);
        }

        // Local flag that determines whether automatic calculation should overwrite the freight field
        let manualFreight = freightManualFlagInput.value === '1';

        // Initialize freight controls based on database value
        if (manualFreight) {
            // If this is a manual freight from database, set the UI to manual mode
            try { freightInput.readOnly = false; } catch (e) {}
        } else {
            // Auto mode - input might be readonly for display
            try { freightInput.readOnly = true; } catch (e) {}
        }

        // Add manual/auto toggle for freight
        if (freightInput && freightInput.parentNode) {
            const freightBtn = document.createElement('button');
            freightBtn.type = 'button';
            freightBtn.id = 'manualFreightToggle';
            freightBtn.className = 'btn manual-freight-btn';
            freightBtn.title = manualFreight ? 'Using manual freight (click to reset to auto)' : 'Use manual freight (click to edit)';

            freightBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" fill="currentColor"/>
                    <path d="M20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" fill="currentColor"/>
                </svg>
            `;

            // Append button into the parent TD
            const freightTd = freightInput.parentNode;
            freightTd.appendChild(freightBtn);

            // Create a small status badge to show current mode (Auto / Manual)
            const freightStatus = document.createElement('span');
            freightStatus.id = 'freightStatus';
            freightStatus.style.position = 'absolute';
            freightStatus.style.right = '34px';
            freightStatus.style.top = '50%';
            freightStatus.style.transform = 'translateY(-50%)';
            freightStatus.style.fontSize = '10px';
            freightStatus.style.padding = '2px 6px';
            freightStatus.style.borderRadius = '10px';
            freightStatus.style.color = '#ffffff';
            freightStatus.style.background = manualFreight ? '#e11d48' : '#10b981'; // red for manual, green for auto
            freightStatus.style.cursor = 'default';
            freightStatus.setAttribute('aria-live', 'polite');
            freightStatus.textContent = manualFreight ? 'Manual' : 'Auto';
            freightTd.appendChild(freightStatus);

            // Style the button to appear on the right side of the TD
            freightBtn.style.position = 'absolute';
            freightBtn.style.right = '6px';
            freightBtn.style.top = '50%';
            freightBtn.style.transform = 'translateY(-50%)';
            freightBtn.style.border = 'none';
            freightBtn.style.background = 'transparent';
            freightBtn.style.cursor = 'pointer';
            freightBtn.style.padding = '2px';
            freightBtn.style.color = manualFreight ? '#e11d48' : '#374151'; // red when manual, gray otherwise

            // Make sure the input has enough right padding so text doesn't go under the icon
            freightInput.style.paddingRight = '66px';

            // Set initial editable state: if automatic, prevent typing; if manual, allow typing
            try {
                freightInput.readOnly = !manualFreight;
            } catch (e) {
                // ignore if element doesn't support readOnly
            }

            // When clicked: toggle manual/auto and update the status badge and input editability
            freightBtn.addEventListener('click', function () {
                const freightStatusEl = document.getElementById('freightStatus');
                if (manualFreight) {
                    // Switch back to automatic
                    manualFreight = false;
                    freightManualFlagInput.value = '0';
                    freightBtn.style.color = '#374151';
                    if (freightStatusEl) {
                        freightStatusEl.style.background = '#10b981';
                        freightStatusEl.textContent = 'Auto';
                    }
                    // Make input readonly and recalc value
                    try { freightInput.readOnly = true; } catch (e) {}
                    calculateFreight();
                } else {
                    // Enable manual edit
                    manualFreight = true;
                    freightManualFlagInput.value = '1';
                    freightBtn.style.color = '#e11d48';
                    if (freightStatusEl) {
                        freightStatusEl.style.background = '#e11d48';
                        freightStatusEl.textContent = 'Manual';
                    }
                    try { freightInput.readOnly = false; } catch (e) {}
                    freightInput.focus();
                }
                freightBtn.title = manualFreight ? 'Using manual freight (click to reset to auto)' : 'Use manual freight (click to edit)';
            });

            // If user types into freight, treat it as manual override and update status
            freightInput.addEventListener('input', function () {
                manualFreight = true;
                freightManualFlagInput.value = '1';
                const freightBtnEl = document.getElementById('manualFreightToggle');
                const freightStatusEl = document.getElementById('freightStatus');
                if (freightBtnEl) freightBtnEl.style.color = '#e11d48';
                if (freightStatusEl) {
                    freightStatusEl.textContent = 'Manual';
                    freightStatusEl.style.background = '#e11d48';
                }
                try { freightInput.readOnly = false; } catch (e) {}
                
                // Only recalculate wharfage if it's in auto mode
                if (!manualWharfage) {
                    calculateWharfage();
                }
            });
        }

        // Function to calculate freight. It will NOT overwrite user-entered freight when manualFreight==true
        function calculateFreight() {
            if (manualFreight) return; // Respect manual override

            // Calculate freight from cart total
            const cartTotal = parseFloat(document.getElementById('cartTotal')?.value || 0);
            
            // Format and set the freight value if element exists
            if (freightInput) {
                freightInput.value = cartTotal.toFixed(2);
            }
            
            // Only recalculate wharfage if it's in auto mode
            if (!manualWharfage) {
                calculateWharfage();
            }
        }

        // Function to calculate wharfage. It will NOT overwrite user-entered wharfage when manualWharfage==true
        function calculateWharfage() {
            console.log('calculateWharfage() called from:', new Error().stack.split('\n')[1]);
            
            if (manualWharfage) {
                console.log('Wharfage calculation skipped - manual mode');
                return; // Respect manual override
            }

            // Get current values - always read from input fields regardless of manual/auto mode
            let freight = parseFloat(freightInput ? freightInput.value : 0) || 0;
            const value = parseFloat(valueInput ? valueInput.value.replace(/,/g,'') : 0) || 0;

            // CRITICAL DEBUG: Log all the important values
            console.log('=== WHARFAGE CALCULATION DEBUG ===');
            console.log('manualFreight flag:', manualFreight);
            console.log('manualWharfage flag:', manualWharfage);
            console.log('freightInput.value (raw):', freightInput ? freightInput.value : 'no input');
            console.log('freight (parsed):', freight);
            console.log('value (parsed):', value);
            console.log('cartTotal:', document.getElementById('cartTotal')?.value);

            // Skip wharfage calculation if both value and freight are 0
            if (value <= 0 && freight <= 0) {
                if (wharfageInput) wharfageInput.value = '0.00';
                console.log('Wharfage set to 0.00 - both value and freight are 0');
                return;
            }

            // Check if the cart contains only GROCERIES
            let onlyGroceries = true;
            let hasNonGroceries = false;

            if (Array.isArray(cart)) {
                cart.forEach(function(item) {
                    const category = (item.category || '').toString().toUpperCase().trim();

                    // Check if category is not GROCERIES (including empty/undefined categories)
                    // Only consider it as GROCERIES if explicitly set to 'GROCERIES'
                    if (category !== 'GROCERIES') {
                        onlyGroceries = false;
                    }

                    if (category !== 'GROCERIES' && category !== '') {
                        hasNonGroceries = true;
                    }
                });
            }

            // Calculate wharfage based on rules
            let wharfage = 0;
            
            // Set wharfage to zero if both VALUE and FREIGHT are zero
            if (value <= 0 && freight <= 0) {
                wharfage = 0;
                console.log('Wharfage set to 0 - both value and freight are 0');
            } else if (freight == 0 && value > 0) {
                // If FREIGHT is 0 (but VALUE is not 0), set wharfage to 11.20
                wharfage = 11.20;
                console.log('Wharfage set to 11.20 - freight is 0 but value > 0');
            } else if (freight > 0) {
                // Calculate wharfage based on rules - default to 1200 formula unless ALL items are GROCERIES
                if (onlyGroceries) {
                    wharfage = (freight / 800) * 23; // Only when ALL items are GROCERIES
                    console.log('Wharfage calculated using GROCERIES formula (800):', wharfage);
                } else {
                    wharfage = (freight / 1200) * 23; // Default formula for mixed or non-grocery items
                    console.log('Wharfage calculated using default formula (1200):', wharfage);
                }
                
                // Set minimum wharfage to 11.20 if not zero
                if (wharfage > 0 && wharfage < 11.20) {
                    wharfage = 11.20;
                    console.log('Wharfage adjusted to minimum 11.20');
                }
            }

            console.log('Final wharfage value:', wharfage);

            // Format and set the wharfage value if element exists
            if (wharfageInput) {
                wharfageInput.value = wharfage.toFixed(2);
            }
        }

        // Add event listeners to recalculate wharfage when freight or value changes
        if (freightInput) {
            freightInput.addEventListener('change', calculateWharfage);
            // Note: freight input events are handled in the manual freight setup above
        }
        
        if (valueInput) {
            valueInput.addEventListener('change', function() {
                if (!manualWharfage) {
                    calculateWharfage();
                }
                if (!manualFreight) {
                    calculateFreight();
                }
            });
            valueInput.addEventListener('input', function() {
                if (!manualWharfage) {
                    calculateWharfage();
                }
                if (!manualFreight) {
                    calculateFreight();
                }
            });
        }
        
        // Calculate initial freight and wharfage when the page loads (only if not manual)
        if (!manualFreight) {
            calculateFreight();
        }
        if (!manualWharfage) {
            calculateWharfage();
        }
    });
</script>

<!-- For add to cart modal -->
<script>
       // Parent container where buttons are dynamically inserted
       document.getElementById("myForm").addEventListener("click", function (event) {
        // Check if the clicked element has one of the target IDs
        if (event.target.id === "eds" || event.target.id === "dels" || event.target.id === "buts" || event.target.id === "addToCart") {
            event.preventDefault(); // Prevent default behavior
        }
    });
    let parcelsData = @json($parcels);
    console.log('Initial parcels data from server:', parcelsData);
    
    let cart = parcelsData || [];
    console.log('Initial cart data:', cart);
    
    // Ensure all cart items have a valid category property
    cart.forEach(item => {
        if (!item.category && item.category !== '') {
            console.log('Item missing category:', item);
            item.category = ''; // Set empty string as default
        }
    });
    
    let totalPrice = @json($total) || 0;
    
    // Function to recalculate total price from cart items
    function recalculateTotalPrice() {
        totalPrice = 0;
        cart.forEach(item => {
            totalPrice += parseFloat(item.total) || 0;
        });
        return totalPrice;
    }
    function openModal() {
        document.getElementById('addToCartModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('addToCartModal').classList.add('hidden');
    }

    // Ensure the event listener is attached after the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', () => {
        // Function to attach table click handler
        function attachTableClickHandler() {
            const tableBody = document.getElementById('itemTableBody');
            if (!tableBody) {
                console.log('Table body not found, will retry...');
                setTimeout(attachTableClickHandler, 500);
                return;
            }

            console.log('Attaching table click handler...');

            tableBody.addEventListener('click', (event) => {
                console.log('Table row clicked!'); // Simple test to see if handler is triggered
                const row = event.target.closest('tr');
                if (!row || !row.classList.contains('item-row')) {
                    console.log('Not a valid item row');
                    return;
                }

                // Get item code more reliably
                const cells = row.querySelectorAll('td');
                if (cells.length < 4) { // Updated to check for at least 4 cells (including hidden price column)
                    console.log('Row does not have enough cells');
                    return;
                }

                const itemCode = cells[0].textContent.trim();
                const itemName = cells[1].textContent.trim();
                const category = cells[2].textContent.trim();
                const price = cells[3].textContent.trim(); // Get the price from the hidden column

                console.log('Selected item:', itemCode, itemName, category, 'Price:', price);

                // Populate the input fields
                const itemCodeField = document.getElementById('itemCode');
                const itemNameField = document.getElementById('itemName');
                const categoryField = document.getElementById('category');
                const priceField = document.getElementById('price'); // Get the price input field
                const descriptionField = document.getElementById('description');

                if (itemCodeField) itemCodeField.value = itemCode;
                if (itemNameField) itemNameField.value = itemName;
                if (categoryField) categoryField.value = category;
                if (priceField) priceField.value = price; // Populate the price field

                // Auto-populate description for motorcycle items
                const motorcycleCodes = ["VHC-011", "VHC-012", "VHC-013", "VOL-013"];
                const trimmedItemCode = itemCode.trim().toUpperCase();

                console.log('Checking motorcycle codes:', trimmedItemCode, motorcycleCodes);

                // Check for exact match first
                if (motorcycleCodes.includes(trimmedItemCode)) {
                    console.log('Motorcycle item detected (exact match), setting description');
                    if (descriptionField) {
                        descriptionField.value = "MODEL: \nENGINE NO: \nCHASSIS NO: \nCOLOR: ";
                        console.log('Description field updated successfully');
                        // Trigger input event to ensure any listeners are notified
                        descriptionField.dispatchEvent(new Event('input', { bubbles: true }));
                    } else {
                        console.error('Description field not found!');
                    }
                } else {
                    // Check for partial matches (in case of extra spaces or characters)
                    const partialMatch = motorcycleCodes.some(code => trimmedItemCode.includes(code) || code.includes(trimmedItemCode));
                    if (partialMatch) {
                        console.log('Motorcycle item detected (partial match), setting description');
                        if (descriptionField) {
                            descriptionField.value = "MODEL: \nENGINE NO: \nCHASSIS NO: \nCOLOR: ";
                            console.log('Description field updated successfully');
                            // Trigger input event to ensure any listeners are notified
                            descriptionField.dispatchEvent(new Event('input', { bubbles: true }));
                        } else {
                            console.error('Description field not found!');
                        }
                    } else {
                        console.log('Not a motorcycle item, clearing description');
                        if (descriptionField) {
                            descriptionField.value = "";
                            // Trigger input event to ensure any listeners are notified
                            descriptionField.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                }
            });

            console.log('Table click handler attached successfully');
        }

        // Attach the handler
        attachTableClickHandler();

        // Also watch for dynamically added content
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Check if new table rows were added
                    const hasNewRows = Array.from(mutation.addedNodes).some(node =>
                        node.nodeType === Node.ELEMENT_NODE &&
                        (node.tagName === 'TR' || node.querySelector('tr'))
                    );
                    if (hasNewRows) {
                        console.log('New table rows detected, ensuring click handler is attached');
                        attachTableClickHandler();
                    }
                }
            });
        });

        // Observe changes to the table container
        const tableContainer = document.getElementById('tableContainer');
        if (tableContainer) {
            observer.observe(tableContainer, { childList: true, subtree: true });
        }
    });

    function updateTotalPrice() {
        const totalPriceElement = document.querySelector("td[style*='font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;']");
        totalPriceElement.innerHTML = `₱ ${Number(totalPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        
        // Also update the hidden cartTotal input
        document.getElementById('cartTotal').value = totalPrice.toFixed(2);
        
        // Calculate freight when cart total changes (if in auto mode)
        if (typeof calculateFreight === 'function') {
            calculateFreight();
        }
    }

    function addToCart() {
        let selectedValue = document.getElementById("unit").value;
        let description = document.getElementById("description").value || ""; // Allow empty description
        const itemCode = document.getElementById('itemCode').value;

        if (!selectedValue) {
            alert("Please select a unit before adding to cart!");
            return;
        }

        // Check if it's a motorcycle item and needs description
        const motorcycleCodes = ["VHC-011", "VHC-012", "VHC-013", "VOL-013"];
        if (motorcycleCodes.includes(itemCode.trim()) && !description.includes("MODEL:")) {
            description = "MODEL: \nENGINE NO: \nCHASSIS NO: \nCOLOR: ";
        }
        // Update the description input field
        document.getElementById("description").value = description;

        // Collect all measurements
        const measurementsList = document.getElementById('measurementsList');
        const measurementEntries = measurementsList.querySelectorAll('.measurement-entry');
        const measurements = [];
        let totalQuantity = 0;
        let hasValidMeasurements = false;

        measurementEntries.forEach((entry, index) => {
            const length = parseFloat(entry.querySelector(`[name="measurement_length_${index}"]`).value) || 0;
            const width = parseFloat(entry.querySelector(`[name="measurement_width_${index}"]`).value) || 0;
            const height = parseFloat(entry.querySelector(`[name="measurement_height_${index}"]`).value) || 0;
            const multiplier = parseFloat(entry.querySelector(`[name="measurement_multiplier_${index}"]`).value) || 0;
            const quantity = parseInt(entry.querySelector(`[name="measurement_quantity_${index}"]`).value) || 0;

            if (length > 0 && width > 0 && height > 0 && multiplier > 0 && quantity > 0) {
                const rate = length * width * height * multiplier;
                measurements.push({
                    length: length,
                    width: width,
                    height: height,
                    multiplier: multiplier,
                    quantity: quantity,
                    rate: rate,
                    freight: rate * quantity
                });
                totalQuantity += quantity;
                hasValidMeasurements = true;
            }
        });

        let weight = document.getElementById('weight').value ? parseFloat(document.getElementById('weight').value) : null;
        let price = parseFloat(document.getElementById('price').value.replace(/,/g, "")) || 0;
        let category = document.getElementById('category').value;
        let manualQuantity = parseInt(document.getElementById('quantity').value);
        let priceValue = document.getElementById('price').value;

        // Check if manual quantity and price are provided (allowing zero values)
        const hasManualValues = !isNaN(manualQuantity) && manualQuantity >= 0 && 
                               priceValue !== '' && priceValue !== null && priceValue !== undefined;

        // If no valid measurements but we have manual quantity and price, use those values
        if (!hasValidMeasurements && hasManualValues) {
            totalQuantity = manualQuantity;
            const manualTotal = price * manualQuantity;
            
            // Check if an item with the same itemCode, itemName, and unit already exists
            const existingItemIndex = cart.findIndex(item => 
                item.itemCode === document.getElementById('itemCode').value &&
                item.itemName === document.getElementById('itemName').value &&
                item.unit === document.getElementById('unit').value
            );

            console.log('Checking for existing manual item with:', {
                itemCode: document.getElementById('itemCode').value,
                itemName: document.getElementById('itemName').value,
                unit: document.getElementById('unit').value,
                foundIndex: existingItemIndex
            });

            if (existingItemIndex !== -1) {
                // Item exists, update the existing item
                const existingItem = cart[existingItemIndex];
                existingItem.quantity += totalQuantity;
                existingItem.total = (parseFloat(existingItem.total) + manualTotal).toFixed(2);
                // Update description if new one has more content
                if (description && description.length > existingItem.description.length) {
                    existingItem.description = description;
                }
                console.log('Updated existing manual item in cart:', existingItem);
            } else {
                // Item doesn't exist, create new item
                const item = {
                    itemCode: document.getElementById('itemCode').value,
                    itemName: document.getElementById('itemName').value,
                    unit: document.getElementById('unit').value,
                    category: document.getElementById('category').value || '',
                    weight: document.getElementById('weight').value || "",
                    value: document.getElementById('value').value || "",
                    measurements: [], // Empty measurements array for manual items
                    price: price.toFixed(2),
                    description: description,
                    quantity: totalQuantity,
                    total: manualTotal.toFixed(2)
                };

                console.log('Adding new manual item to cart:', item);
                cart.push(item);
            }
        } else if (hasValidMeasurements) {
            // Check if an item with the same itemCode, itemName, and unit already exists
            const existingItemIndex = cart.findIndex(item => 
                item.itemCode === document.getElementById('itemCode').value &&
                item.itemName === document.getElementById('itemName').value &&
                item.unit === document.getElementById('unit').value
            );

            console.log('Checking for existing measurement item with:', {
                itemCode: document.getElementById('itemCode').value,
                itemName: document.getElementById('itemName').value,
                unit: document.getElementById('unit').value,
                foundIndex: existingItemIndex,
                newMeasurements: measurements.length
            });

            if (existingItemIndex !== -1) {
                // Item exists, append measurements to existing item
                const existingItem = cart[existingItemIndex];
                existingItem.measurements = existingItem.measurements.concat(measurements);
                existingItem.quantity += totalQuantity;
                existingItem.total = (parseFloat(existingItem.total) + measurements.reduce((sum, m) => sum + m.freight, 0)).toFixed(2);
                // Update description if new one has more content
                if (description && description.length > existingItem.description.length) {
                    existingItem.description = description;
                }
                console.log('Updated existing measurement-based item in cart:', existingItem);
            } else {
                // Item doesn't exist, create new item
                const item = {
                    itemCode: document.getElementById('itemCode').value,
                    itemName: document.getElementById('itemName').value,
                    unit: document.getElementById('unit').value,
                    category: document.getElementById('category').value || '',
                    weight: document.getElementById('weight').value || "",
                    value: document.getElementById('value').value || "",
                    measurements: measurements,
                    price: price.toFixed(2),
                    description: description,
                    quantity: totalQuantity,
                    total: measurements.reduce((sum, m) => sum + m.freight, 0).toFixed(2)
                };

                console.log('Adding new measurement-based item to cart:', item);
                cart.push(item);
            }
        } else {
            // No valid measurements and no manual quantity/price
            alert("Please either add measurements or enter both quantity and price.");
            return;
        }

        // Update the hidden input field with the cart data
        document.getElementById('cartData').value = JSON.stringify(cart);

        // Recalculate total from cart items
        recalculateTotalPrice();
        document.getElementById('cartTotal').value = totalPrice.toFixed(2);

        updateMainTable();
        updateTotalPrice(); // Update the displayed total price (this will also call calculateFreight)
        clearFields(); // Clear all fields after adding to cart
        closeModal();
    }

    function formatPrice(value) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }
    function checkCart() {
        const inputField = document.getElementById('value');
        const submitBtn = document.getElementById('submitOrder');
        if (cart.length === 0) {
            submitBtn.disabled = true; // Disable the button
        } else {
            submitBtn.disabled = false; // Enable the button
        }
        // Lock the value input only when FROZEN or PARCEL items are present.
        // AGGREGATES items are allowed to have a VALUE entered but the server will still
        // ignore value for valuation calculation (valuation will be zero when AGGREGATES are present).
        const hasFrozenOrParcel = cart.some(item => item.category === 'FROZEN' || item.category === 'PARCEL');

        if (hasFrozenOrParcel) {
            inputField.value = ''; // Clear input
            inputField.readOnly = true; // Make it readonly
            isLocked = true; // Lock it permanently
        } else {
            // If only AGGREGATES or other categories exist, allow editing
            inputField.readOnly = false;
        }

    }
    function updateMainTable() { //multiply
        const mainTableBody = document.getElementById('mainTableBody');
        mainTableBody.innerHTML = '';
        checkCart();
        const cartDataInput = document.getElementById('cartData');
        cartDataInput.value = JSON.stringify(cart);

        // Recalculate total price from cart items to ensure accuracy
        recalculateTotalPrice();
        const cartTotalInput = document.getElementById('cartTotal');
        cartTotalInput.value = totalPrice.toFixed(2);

        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = "border-b";

            row.innerHTML = `
                <td class="p-2 text-center">${item.quantity}</td>
                <td class="p-2 text-center">${item.unit}</td>
                <td class="p-2 text-center">${item.itemName} ${item.description}</td>
                <td class="p-2 text-center"> </td>
                <td class="p-2 text-center">${item.weight}</td>
            `;

            // Check if item has multiple measurements
            if (item.measurements && item.measurements.length > 0) {
                let measurementHtml = '';
                let rateHtml = '';
                let freightHtml = '';
                item.measurements.forEach(measurement => {
                    measurementHtml += `${measurement.length} × ${measurement.width} × ${measurement.height} (${measurement.quantity})<br>`;
                    rateHtml += `${Number(measurement.rate).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}<br>`;
                    freightHtml += `${Number(measurement.freight).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}<br>`;
                });
                row.innerHTML += `<td class="p-2 text-center">${measurementHtml}</td>`;
                row.innerHTML += `<td class="p-2 text-center">${rateHtml}</td>`;
                row.innerHTML += `<td class="p-2 text-center">${freightHtml}</td>`;
            } else if (item.measurements && item.measurements.length === 0) {
                // Manual item with no measurements - show price as rate and total as freight
                row.innerHTML += `<td class="p-2 text-center">Manual Entry</td>`;
                row.innerHTML += `<td class="p-2 text-center">${Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                row.innerHTML += `<td class="p-2 text-center">${Number(item.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
            } else if (!item.multiplier || item.multiplier === 'N/A') {
                row.innerHTML += `<td class="p-2 text-center">${item.length || ''} × ${item.width || ''} × ${item.height || ''}</td>`;
                row.innerHTML += `<td class="p-2 text-center">${Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                row.innerHTML += `<td class="p-2 text-center">${Number(item.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
            } else {
                row.innerHTML += `<td class="p-2 text-center">${item.length || ''} × ${item.width || ''} × ${item.height || ''} × ${Number(item.multiplier).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                row.innerHTML += `<td class="p-2 text-center">${Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                row.innerHTML += `<td class="p-2 text-center">${Number(item.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
            }

            row.innerHTML += `
                <td class="p-2 text-center">
                    <a href="#" class="text-blue-500 text-center" onclick="openEditModal(${index})">
                        <x-button id="eds" variant="warning" class="items-center max-w-xs gap-2" type='button'>
                            <x-heroicon-o-pencil class="w-6 h-6" aria-hidden="true" />
                        </x-button>
                    </a>
                </td>
                <td class="p-2 text-center">
                    <a href="#" class="text-blue-500 text-center" onclick="deleteItem(${index})" type='button'>
                        <x-button id="dels" variant="danger" class="items-center max-w-xs gap-2">
                            <x-heroicon-o-trash class="w-6 h-6" aria-hidden="true" />
                        </x-button>
                    </a>
                </td>
            `;

            mainTableBody.appendChild(row);
        });

        // Remove any existing summary row before adding a new one
        let existingSummaryRow = document.getElementById("summaryRow");
        if (existingSummaryRow) {
            existingSummaryRow.remove();
        }

        // Append summary row at the end of the table
        let summaryRow = document.createElement("tr");
        summaryRow.id = "summaryRow"; // Give an ID to avoid duplicates
        summaryRow.classList.add("border", "border-gray");
        summaryRow.innerHTML = `
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">₱
                ${Number(totalPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        `;

        mainTableBody.appendChild(summaryRow);

        // Recalculate total price to ensure it's up to date
        recalculateTotalPrice();

        toggleButtonsVisibility();
    }

        function deleteItem(index) {
            cart.splice(index, 1);
            updateMainTable();
            updateTotalPrice(); // Update the displayed total price
            
            // Recalculate and update cartTotal hidden input
            recalculateTotalPrice();
            document.getElementById('cartTotal').value = totalPrice.toFixed(2);
        }

        function toggleButtonsVisibility() {
            const editButtons = document.querySelectorAll("[variant='warning']");
            const deleteButtons = document.querySelectorAll("[variant='danger']");

            const visibility = cart.length > 0 ? "" : "hidden";

            editButtons.forEach(btn => btn.style.visibility = visibility);
            deleteButtons.forEach(btn => btn.style.visibility = visibility);
        }

        function openEditModal(index) {
            document.getElementById('addToCart').classList.add('hidden'); // Hide Add to Cart
            document.getElementById('saveButton').classList.remove('hidden'); // Show Save Button

            const item = cart[index];
            
            // Debug log the item and its properties
            console.log('Editing item:', item);
            console.log('Item category:', item.category);
            
            // If the category is missing or empty, try to fetch it from the price list
            if (!item.category || item.category === '') {
                // Try to find the category from the itemTableBody
                const tableRows = document.querySelectorAll('#itemTableBody tr.item-row');
                for (let row of tableRows) {
                    if (row.cells[0].textContent.trim() === item.itemCode) {
                        item.category = row.cells[2].textContent.trim();
                        console.log('Found category from price list:', item.category);
                        break;
                    }
                }
            }

            document.getElementById('itemCode').value = item.itemCode;
            document.getElementById('itemName').value = item.itemName;
            document.getElementById('unit').value = item.unit;
            document.getElementById('category').value = item.category || ''; // Make sure it's never undefined
            document.getElementById('weight').value = item.weight;
            document.getElementById('price').value = item.price;
            document.getElementById('description').value = item.description;
            document.getElementById('quantity').value = item.quantity;
            edit = item.total;
            // Store the index for updating later
            document.getElementById('saveButton').setAttribute('data-index', index);

            // Handle measurements
            const measurementsList = document.getElementById('measurementsList');
            measurementsList.innerHTML = ''; // Clear existing

            if (item.measurements && item.measurements.length > 0) {
                // Load multiple measurements
                item.measurements.forEach(measurement => {
                    addMeasurementEntry(measurement.length, measurement.width, measurement.height, measurement.multiplier, measurement.quantity);
                });
            } else {
                // Load single measurement for backward compatibility
                addMeasurementEntry(item.length || '', item.width || '', item.height || '', item.multiplier || '', item.quantity || 1);
            }

            // Show the modal
            document.getElementById('addToCartModal').classList.remove('hidden');
        }

        // Function to save the updated data
        function saveUpdatedItem() { //minus
            document.getElementById('addToCart').classList.remove('hidden'); // Show Add to Cart
            document.getElementById('saveButton').classList.add('hidden'); // Hide Save Button
            const currentEditIndex = document.getElementById('saveButton').getAttribute('data-index'); // Get stored index

            if (currentEditIndex !== null) {
                // Collect all measurements
                const measurementsList = document.getElementById('measurementsList');
                const measurementEntries = measurementsList.querySelectorAll('.measurement-entry');
                const measurements = [];
                let totalQuantity = 0;
                let hasValidMeasurements = false;

                measurementEntries.forEach((entry, index) => {
                    const length = parseFloat(entry.querySelector(`[name="measurement_length_${index}"]`).value) || 0;
                    const width = parseFloat(entry.querySelector(`[name="measurement_width_${index}"]`).value) || 0;
                    const height = parseFloat(entry.querySelector(`[name="measurement_height_${index}"]`).value) || 0;
                    const multiplier = parseFloat(entry.querySelector(`[name="measurement_multiplier_${index}"]`).value) || 0;
                    const quantity = parseInt(entry.querySelector(`[name="measurement_quantity_${index}"]`).value) || 0;

                    if (length > 0 && width > 0 && height > 0 && multiplier > 0 && quantity > 0) {
                        const rate = length * width * height * multiplier;
                        measurements.push({
                            length: length,
                            width: width,
                            height: height,
                            multiplier: multiplier,
                            quantity: quantity,
                            rate: rate,
                            freight: rate * quantity
                        });
                        totalQuantity += quantity;
                        hasValidMeasurements = true;
                    }
                });

                let price = parseFloat(document.getElementById('price').value.replace(/,/g, '')) || 0; // Handle thousand separators
                let manualQuantity = parseInt(document.getElementById('quantity').value);
                let priceValue = document.getElementById('price').value;

                // Check if manual quantity and price are provided (allowing zero values)
                const hasManualValues = !isNaN(manualQuantity) && manualQuantity >= 0 && 
                                       priceValue !== '' && priceValue !== null && priceValue !== undefined;

                // If no valid measurements but we have manual quantity and price, use those values
                if (!hasValidMeasurements && hasManualValues) {
                    totalQuantity = manualQuantity;
                    const manualTotal = price * manualQuantity;
                    
                    cart[currentEditIndex] = {
                        itemCode: document.getElementById('itemCode').value,
                        itemName: document.getElementById('itemName').value,
                        unit: document.getElementById('unit').value,
                        category: document.getElementById('category').value || '',
                        weight: document.getElementById('weight').value,
                        measurements: [], // Empty measurements array for manual items
                        price: price.toFixed(2),
                        description: document.getElementById('description').value,
                        quantity: totalQuantity,
                        total: manualTotal.toFixed(2)
                    };
                } else if (hasValidMeasurements) {
                    // Use measurement-based calculations
                    cart[currentEditIndex] = {
                        itemCode: document.getElementById('itemCode').value,
                        itemName: document.getElementById('itemName').value,
                        unit: document.getElementById('unit').value,
                        category: document.getElementById('category').value || '',
                        weight: document.getElementById('weight').value,
                        measurements: measurements,
                        price: price.toFixed(2),
                        description: document.getElementById('description').value,
                        quantity: totalQuantity,
                        total: measurements.reduce((sum, m) => sum + m.freight, 0).toFixed(2)
                    };
                } else {
                    // No valid measurements and no manual quantity/price
                    alert("Please either add measurements or enter both quantity and price.");
                    return;
                }

                console.log('Updated item in cart:', cart[currentEditIndex]);

                // Update the displayed table with new data
                updateMainTable();
                updateTotalPrice(); // Update the displayed total price

                // Recalculate and update cartTotal hidden input
                recalculateTotalPrice();
                document.getElementById('cartTotal').value = totalPrice.toFixed(2);

                closeModal();
            }
        }

    function clearFields() {
        document.getElementById('itemCode').value = "";
        document.getElementById('itemName').value = "";
        document.getElementById('unit').value = "";
        document.getElementById('category').value = "";
        document.getElementById('weight').value = "";
        document.getElementById('price').value = "";
        document.getElementById('description').value = "";
        document.getElementById('quantity').value = "";
        // Clear measurements and reinitialize with one empty entry
        initializeMeasurements();
    }

    // Add this script to your existing JavaScript code
    document.addEventListener("DOMContentLoaded", function () {
        const addUnitButton = document.getElementById("addUnit");

        if (addUnitButton) {
            addUnitButton.addEventListener("click", function () {
                const unitInput = document.getElementById("unit");
                const newUnit = prompt("Enter a new unit:");

                if (newUnit) {
                    const option = document.createElement("option");
                    option.value = newUnit.toUpperCase();
                    option.textContent = newUnit.toUpperCase();
                    unitInput.appendChild(option);
                    unitInput.value = newUnit.toUpperCase(); // Set the newly added unit as selected
                    alert(`Unit "${newUnit}" added successfully!`);
                }
            });
        } else {
            console.error("addUnit button not found!");
        }
    });

    // Search function
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('#itemTableBody .item-row');
        searchInput.addEventListener('keyup', () => {
            const query = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const itemCode = row.children[0].textContent.toLowerCase();
                const itemName = row.children[1].textContent.toLowerCase();
                const category = row.children[2].textContent.toLowerCase();

                if (itemCode.includes(query) || itemName.includes(query) || category.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // JavaScript for Filtering
    document.getElementById("categoryFilter").addEventListener("change", function() {
        let selectedCategory = this.value.toLowerCase();
        document.querySelectorAll("#itemTableBody .item-row").forEach(row => {
            let rowCategory = row.getAttribute("data-category").toLowerCase();
            if (selectedCategory === "" || rowCategory === selectedCategory) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Function to fetch details for SHIPPER and CONSIGNEE
        function fetchDetails(inputId, phoneId, hiddenId, route) {
            const inputElement = document.getElementById(inputId);
            const phoneElement = document.getElementById(phoneId);
            const hiddenElement = document.getElementById(hiddenId);

            inputElement.addEventListener("change", function () {
                const selectedName = inputElement.value;

                if (selectedName) {
                    // Make an AJAX request to fetch details
                    fetch(route + "?name=" + encodeURIComponent(selectedName))
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                phoneElement.value = data.phone || ""; // Update phone number
                                hiddenElement.value = data.id || ""; // Update hidden ID
                            } else {
                                alert(data.message || "No matching record found for the selected name.");
                                phoneElement.value = "";
                                hiddenElement.value = "";
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching details:", error);
                            alert("An error occurred while fetching details.");
                        });
                }
            });
        }

        // Attach the fetchDetails function to SHIPPER and CONSIGNEE fields
        fetchDetails("shipperName", "shipperNum", "shipperId", "{{ route('masterlist.search-customer-details') }}");
        fetchDetails("recName", "recNum", "consigneeId", "{{ route('masterlist.search-customer-details') }}");
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkerNameSelect = document.getElementById("checkerName");
    const submitButton = document.getElementById("submitOrder");

    submitButton.addEventListener("click", function (event) {
        // Format selected checkers with '/' separator before submission
        const selectedOptions = Array.from(checkerNameSelect.selectedOptions).map(option => option.value);
        // Remove empty values and join with ' / '
        const formattedCheckerNames = selectedOptions.filter(name => name.trim() !== "").join(' / ');
        
        // Update or create hidden input for the formatted checker names
        let hiddenInput = document.getElementById('formattedCheckerNames');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'formattedCheckerNames';
            hiddenInput.name = 'checkerName';
            checkerNameSelect.parentNode.appendChild(hiddenInput);
        }
        hiddenInput.value = formattedCheckerNames;
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const submitButton = document.getElementById("submitOrder");

    submitButton.addEventListener("click", function (event) {
        // Update the cart data and total price before submitting the form
        const cartDataInput = document.getElementById('cartData');
        const cartTotalInput = document.getElementById('cartTotal');

        // Ensure cart data and total price are updated
        cartDataInput.value = JSON.stringify(cart);
        
        // Ensure cartTotal is never empty or NaN
        const finalTotal = isNaN(totalPrice) ? 0 : parseFloat(totalPrice);
        cartTotalInput.value = finalTotal.toFixed(2); // Ensure it's a clean numeric value

        // Log the updated values for debugging
        console.log("Updated Cart Data:", cartDataInput.value);
        console.log("Updated Cart Total:", cartTotalInput.value);
        console.log("Total Price Variable:", totalPrice);
        
        // Debug: Log all form data that will be submitted
        const formData = new FormData(document.getElementById('myForm'));
        console.log("All form data being submitted:");
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
    });
});
</script>
<script>
// Create New Item Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const createNewItemBtn = document.getElementById('createNewItemBtn');
    const createPriceListItemModal = document.getElementById('createPriceListItemModal');
    const priceListItemForm = document.getElementById('priceListItemForm');
    
    // Price type toggle
    const measurementsGroup = document.getElementById('measurements_group');
    const priceLabel = document.getElementById('price_label');
    
    // Open modal when clicking the "New Item" button
    if (createNewItemBtn) {
        createNewItemBtn.addEventListener('click', function() {
            // Hide the cart modal
            document.getElementById('addToCartModal').classList.add('hidden');
            // Show the create item modal
            createPriceListItemModal.classList.remove('hidden');
        });
    }
    
    // Close the create item modal
    window.closeCreateItemModal = function() {
        createPriceListItemModal.classList.add('hidden');
        // Show the cart modal again
        document.getElementById('addToCartModal').classList.remove('hidden');
    };
    
    // Handle form submission
    if (priceListItemForm) {
        priceListItemForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form data
            const formData = new FormData(priceListItemForm);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Send AJAX request
            fetch('{{ route("pricelist.store") }}', {
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
                    // Create a new row for the item table in the cart modal
                    const newItem = data.item;
                    
                    // Add new item to the table
                    const itemTableBody = document.getElementById('itemTableBody');
                    
                    // Create a new row
                    const newRow = document.createElement('tr');
                    newRow.className = 'item-row';
                    newRow.setAttribute('data-category', newItem.category);
                    
                    // Set the row content
                    newRow.innerHTML = `
                        <td class="p-3 border" style="text-align: left;">${newItem.item_code}</td>
                        <td class="p-3 border" style="text-align: left;">${newItem.item_name}</td>
                        <td class="p-3 border">${newItem.category}</td>
                        <td class="p-3 border" hidden>${newItem.price ? Number(newItem.price).toFixed(2) : 'N/A'}</td>
                        <td class="p-3 border" hidden>${newItem.multiplier || ''}</td>
                    `;
                    
                    // Add click event to select this item
                    newRow.addEventListener('click', function() {
                        document.getElementById('itemCode').value = newItem.item_code;
                        document.getElementById('itemName').value = newItem.item_name;
                        document.getElementById('category').value = newItem.category;
                        document.getElementById('price').value = newItem.price || '';
                        document.getElementById('multiplier').value = newItem.multiplier || '';
                    });
                    
                    // Add the new row at the beginning of the table
                    itemTableBody.insertBefore(newRow, itemTableBody.firstChild);
                    
                    // Share this new item with other tabs via local storage
                    localStorage.setItem('newPriceListItem', JSON.stringify({
                        timestamp: new Date().getTime(),
                        item: newItem
                    }));
                    
                    // Show notification
                    showNotification('Item created successfully: ' + newItem.item_name);
                    
                    // Close the create modal and show the cart modal again
                    closeCreateItemModal();
                    
                    // Clear form fields
                    priceListItemForm.reset();
                    
                    // Auto-select the new item
                    document.getElementById('itemCode').value = newItem.item_code;
                    document.getElementById('itemName').value = newItem.item_name;
                    document.getElementById('category').value = newItem.category;
                    document.getElementById('price').value = newItem.price || '';
                    document.getElementById('multiplier').value = newItem.multiplier || '';
                    
                } else {
                    // Show error
                    alert('Error creating item: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the item');
            });
        });
    }
    
    // Notification function
    window.showNotification = function(message) {
        // Create notification element if it doesn't exist
        let notification = document.getElementById('price-list-notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'price-list-notification';
            notification.style.position = 'fixed';
            notification.style.bottom = '20px';
            notification.style.right = '20px';
            notification.style.padding = '10px 20px';
            notification.style.backgroundColor = '#4CAF50';
            notification.style.color = 'white';
            notification.style.borderRadius = '5px';
            notification.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.3)';
            notification.style.zIndex = '1000';
            notification.style.transition = 'opacity 0.5s';
            document.body.appendChild(notification);
        }

        // Set notification message
        notification.textContent = message;
        notification.style.opacity = '1';

        // Hide after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 3000);
    };

    // Listen for storage events to update the table in real-time
    window.addEventListener('storage', function(event) {
        // Only respond to our specific event for new price list items
        if (event.key === 'newPriceListItem') {
            try {
                const data = JSON.parse(event.newValue);
                if (data && data.timestamp && data.item) {
                    const itemTableBody = document.getElementById('itemTableBody');
                    const newItem = data.item;

                    // Create a new row
                    const newRow = document.createElement('tr');
                    newRow.className = 'item-row';
                    newRow.setAttribute('data-category', newItem.category);
                    
                    // Set the row content
                    newRow.innerHTML = `
                        <td class="p-3 border" style="text-align: left;">${newItem.item_code}</td>
                        <td class="p-3 border" style="text-align: left;">${newItem.item_name}</td>
                        <td class="p-3 border">${newItem.category}</td>
                        <td class="p-3 border" hidden>${newItem.price ? Number(newItem.price).toFixed(2) : 'N/A'}</td>
                        <td class="p-3 border" hidden>${newItem.multiplier || ''}</td>
                    `;
                    
                    // Add the new row at the beginning of the table
                    itemTableBody.insertBefore(newRow, itemTableBody.firstChild);

                    // Add click event to select this item
                    newRow.addEventListener('click', function() {
                        document.getElementById('itemCode').value = newItem.item_code;
                        document.getElementById('itemName').value = newItem.item_name;
                        document.getElementById('category').value = newItem.category;
                        document.getElementById('price').value = newItem.price || '';
                        document.getElementById('multiplier').value = newItem.multiplier || '';
                    });

                    // Show notification
                    showNotification('New item added: ' + newItem.item_name);
                }
            } catch (error) {
                console.error('Error parsing price list update:', error);
            }
        }
    });
});
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
    /* Screen Display */
    @media screen {
        #printContainer {
            border: 1px solid black; /* Visual border for preview */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
    }
</style>
<style>
    label {
        line-height: 1.2; /* Reduce label spacing */
        font-size: 14px; /* Adjust label size */
    }

    input, select, textarea {
        padding: 6px 8px; /* Reduce padding */
        font-size: 14px; /* Adjust font size */
        height: 32px; /* Ensure uniform height */
        line-height: 1.2; /* Adjust line spacing */
    }

    select {
        appearance: none; /* Remove default styling */
    }

    textarea {
        height: 60px; /* Adjust height for textarea */
    }

    .grid input, .grid select {
        height: 32px; /* Ensure inputs in grids have the same height */
    }

    .btn {
        height: 32px;
        font-size: 14px;
        padding: 6px 12px;
    }
    #tableContainer {
        position: relative;
        overflow-y: auto;
        max-height: 415px; /* Adjust as needed */
        border: 1px solid #ccc;
    }

    /* Ensure table takes full width */
    #tableContainer table {
        width: 100%;
        border-collapse: collapse;
    }

    /* Sticky Header */
    /*#tableContainer thead {
        position: sticky;
        top: 0;
        z-index: 50; /* Ensures it stays above scrolling content */
    /*}*/

    #tableContainer thead tr {
        background-color: #e5e7eb; /* Light mode background */
        color: #000; /* Text color for readability */
    }

    /* Dark Mode Adaptation */
    .dark #tableContainer thead {
        background-color: #1f2937 !important; /* Dark mode */
        color: #ffffff !important; /* Ensure text remains visible */
    }

    /* Ensure header cells (th) also inherit the background */
    #tableContainer thead th {
        background-color: inherit; /* Inherit from thead */
        padding: 10px;
        border: 1px solid #ccc;
    }

    /* Add a shadow to separate header visually */
    #tableContainer thead tr {
        box-shadow: 0px 4px 5px rgba(0, 0, 0, 0.1);
    }

    /* Ensure table rows don't pass under the header */
    #tableContainer tbody tr {
        z-index: 1; /* Lower than header */
        position: relative;
    }

    input {
        height: 15px; /* Ensure the input fields are tall enough */
        font-size: 11px;
    }

    /* Wharfage control styles */
    .manual-wharfage-btn {
        z-index: 30; /* Above input */
        background: transparent;
    }

    #wharfageStatus {
        z-index: 20; /* Below icon but above input */
        text-align: center;
        min-width: 40px;
    }
</style>

<script>
    function formatValueInput(input) {
        // Remove all non-numeric characters except the decimal point
        let rawValue = input.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
        
        // If the value is empty or NaN, just clear the input
        if (rawValue === '' || isNaN(parseFloat(rawValue))) {
            input.value = '';
            // Remove any existing hidden input
            const existingHidden = document.getElementById('valueRaw');
            if (existingHidden) existingHidden.parentNode.removeChild(existingHidden);
            return;
        }
        
        // Check if the value has decimal places
        const hasDecimal = rawValue.includes('.');
        const decimalParts = hasDecimal ? rawValue.split('.') : [rawValue, ''];
        const integerPart = decimalParts[0];
        let decimalPart = decimalParts[1];
        
        // Format the integer part with commas
        const formattedInteger = parseInt(integerPart).toLocaleString('en-US');
        
        // Create the final formatted value based on whether there's a decimal part
        let formattedValue = formattedInteger;
        if (hasDecimal) {
            // Limit decimal part to 2 digits
            decimalPart = decimalPart.substring(0, 2);
            formattedValue += '.' + decimalPart;
        }
        
        // Update the input field with the formatted value
        input.value = formattedValue;

        // Store the raw value in a hidden input for submission
        let hiddenInput = document.getElementById('valueRaw');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'valueRaw';
            hiddenInput.name = 'value';
            input.parentNode.appendChild(hiddenInput);
            // Remove the name attribute from the visible input to prevent it from being submitted
            input.removeAttribute('name');
        }
        // Save the raw numeric value for form submission
        hiddenInput.value = rawValue;
    }

    // Initialize measurements
    function initializeMeasurements() {
        const measurementsList = document.getElementById('measurementsList');
        measurementsList.innerHTML = ''; // Clear existing
        addMeasurementEntry(); // Add first measurement entry
    }

    function addMeasurementEntry(length = '', width = '', height = '', multiplier = '', quantity = '') {
        const measurementsList = document.getElementById('measurementsList');
        const measurementIndex = measurementsList.children.length;

        const measurementDiv = document.createElement('div');
        measurementDiv.className = 'measurement-entry flex items-center gap-1 mb-2 p-1 border rounded';
        measurementDiv.innerHTML = `
            <input type="number" name="measurement_length_${measurementIndex}"
                class="p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200 h-10"
                style="width: 80px;" placeholder="L" min="0" step="0.01" value="${length}" onchange="updateMeasurementRate(${measurementIndex})" oninput="updateMeasurementRate(${measurementIndex})">
            <input type="number" name="measurement_width_${measurementIndex}"
                class="p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200 h-10"
                style="width: 80px;" placeholder="W" min="0" step="0.01" value="${width}" onchange="updateMeasurementRate(${measurementIndex})" oninput="updateMeasurementRate(${measurementIndex})">
            <input type="number" name="measurement_height_${measurementIndex}"
                class="p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200 h-10"
                style="width: 80px;" placeholder="H" min="0" step="0.01" value="${height}" onchange="updateMeasurementRate(${measurementIndex})" oninput="updateMeasurementRate(${measurementIndex})">
            <input list="multipliers" name="measurement_multiplier_${measurementIndex}" type="number"
                class="p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200 h-10"
                style="width: 110px;" placeholder="×" min="0" step="0.01" value="${multiplier}" onchange="updateMeasurementRate(${measurementIndex})" oninput="updateMeasurementRate(${measurementIndex})">
            <input type="number" name="measurement_quantity_${measurementIndex}"
                class="p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200 h-10"
                style="width: 50px;" placeholder="Qty" min="1" value="${quantity || 1}">
            <span id="rate_display_${measurementIndex}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900 px-2 py-1 rounded" style="min-width: 80px; text-align: center;">Rate: 0.00</span>
            <button type="button" class="remove-measurement px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm font-bold h-10"
                onclick="removeMeasurementEntry(this)">×</button>
        `;
        measurementsList.appendChild(measurementDiv);
        
        // Calculate initial rate if values are provided
        if (length && width && height && multiplier) {
            updateMeasurementRate(measurementIndex);
        }
    }

    function updateMeasurementRate(measurementIndex) {
        const lengthInput = document.querySelector(`[name="measurement_length_${measurementIndex}"]`);
        const widthInput = document.querySelector(`[name="measurement_width_${measurementIndex}"]`);
        const heightInput = document.querySelector(`[name="measurement_height_${measurementIndex}"]`);
        const multiplierInput = document.querySelector(`[name="measurement_multiplier_${measurementIndex}"]`);
        const rateDisplay = document.getElementById(`rate_display_${measurementIndex}`);

        if (lengthInput && widthInput && heightInput && multiplierInput && rateDisplay) {
            const length = parseFloat(lengthInput.value) || 0;
            const width = parseFloat(widthInput.value) || 0;
            const height = parseFloat(heightInput.value) || 0;
            const multiplier = parseFloat(multiplierInput.value) || 0;

            const rate = length * width * height * multiplier;
            rateDisplay.textContent = `Rate: ${rate.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
    }

    function removeMeasurementEntry(button) {
        const measurementsList = document.getElementById('measurementsList');
        if (measurementsList.children.length > 1) {
            button.closest('.measurement-entry').remove();
        } else {
            alert('At least one measurement is required.');
        }
    }

    // Add event listener for add measurement button
    document.getElementById('addMeasurementBtn').addEventListener('click', function() {
        addMeasurementEntry();
    });

    // Test function for debugging motorcycle item detection
    window.testMotorcycleItems = function() {
        console.log('=== MOTORCYCLE ITEM TEST ===');

        // Check if description field exists
        const descField = document.getElementById('description');
        console.log('Description field found:', !!descField);

        // Check table body
        const tableBody = document.getElementById('itemTableBody');
        console.log('Table body found:', !!tableBody);

        if (tableBody) {
            const rows = tableBody.querySelectorAll('tr.item-row');
            console.log('Number of item rows found:', rows.length);

            // Check for motorcycle items
            const motorcycleCodes = ["VHC-011", "VHC-012", "VHC-013", "VOL-013"];
            let motorcycleItemsFound = [];

            rows.forEach((row, index) => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    const itemCode = cells[0].textContent.trim();
                    const trimmedItemCode = itemCode.trim().toUpperCase();

                    if (motorcycleCodes.includes(trimmedItemCode)) {
                        motorcycleItemsFound.push({
                            rowIndex: index,
                            itemCode: itemCode,
                            trimmed: trimmedItemCode
                        });
                    }
                }
            });

            console.log('Motorcycle items found in table:', motorcycleItemsFound);

            if (motorcycleItemsFound.length > 0) {
                console.log('✅ Motorcycle items are present in the table!');
                console.log('Try clicking on row index:', motorcycleItemsFound[0].rowIndex);
            } else {
                console.log('❌ No motorcycle items found in the current table view');
                console.log('Make sure the table is loaded and contains items with codes:', motorcycleCodes);
            }
        }

        console.log('=== END TEST ===');
    };

    console.log('Motorcycle item test function loaded. Run testMotorcycleItems() in console to debug.');
</script>
