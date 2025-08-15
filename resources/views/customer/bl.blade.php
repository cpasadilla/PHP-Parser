<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Create BL') }}
            </h2>
            <!-- CREATE CUSTOMER BUTTON -->
            <div x-data="{ openModal: false }">
                <button @click="openModal = true" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:ring focus:ring-blue-500">
                    + Create Customer
                </button>
                <!-- Modal -->
                <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50" x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative">
                        <!-- Modal Header with Close (×) Button -->
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white text-center w-full">Create Customer Account</h2>
                        </div>
                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="list-disc list-inside text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- Form -->
                        <form id="createCustomerForm" method="POST" action="{{ route('customers.store') }}">
                            @csrf
                            <input type="hidden" name="account_type" value="main"> <!-- Default to main account -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- First Name -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 dark:text-white" style="font-size: 16px;">First Name</label>
                                    <input type="text" name="first_name" style="font-size: 18px; height: 40px;"
                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200"
                                        oninput="updateAccountType()">
                                </div>
                                <!-- Last Name -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 dark:text-white" style="font-size: 16px;">Last Name</label>
                                    <input type="text" name="last_name" style="font-size: 18px; height: 40px;"
                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200"
                                        oninput="updateAccountType()">
                                </div>
                            </div>
                            <!-- Company Name -->
                            <div class="mt-2">
                                <label class="block text-gray-700 dark:text-white" style="font-size: 16px;">Company Name (if applicable)</label>
                                <input type="text" name="company_name" style="font-size: 18px; height: 40px;"
                                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200"
                                    oninput="updateAccountType()">
                            </div>
                            <!-- Phone -->
                            <div class="mt-2">
                                <label class="block text-gray-700 dark:text-white" style="font-size: 16px;">Phone Number</label>
                                <input type="text" name="phone" style="font-size: 18px; height: 40px;"
                                    pattern="^[0-9/\s]*$"
                                    title="Please enter numbers only, separate multiple numbers with /"
                                    oninput="this.value = this.value.replace(/[^0-9/\s]/g, '')"
                                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                            </div>
                            <!-- Hidden Fields -->
                            <div class="grid grid-cols-2 gap-4 mt-2" hidden>
                                <div class="col-span-1" hidden>
                                    <label class="block text-gray-700 dark:text-white" style="font-size: 16px;">Type</label>
                                    <select name="type" class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                        <option value="individual">Individual</option>
                                        <option value="company">Company</option>
                                    </select>
                                </div>
                                <div class="col-span-1" hidden>
                                    <label class="block text-gray-700 dark:text-white" style="font-size: 16px;">Email</label>
                                    <input type="text" name="email" class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                </div>
                            </div>
                            <!-- Submit & Close Buttons -->
                            <div class="flex justify-between mt-4">
                                <button type="button" @click="resetFields" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    Clear
                                </button>
                                <div class="flex space-x-2">
                                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-500 text-white rounded">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Response Message -->
                        <div id="responseMessage" class="mt-4 p-4 rounded-md hidden"></div>

                        <script>
                        document.getElementById('createCustomerForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            
                            const formData = new FormData(this);
                            
                            fetch('{{ route('customers.store') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                const messageDiv = document.getElementById('responseMessage');
                                messageDiv.classList.remove('hidden');
                                
                                if (data.success) {
                                    messageDiv.classList.remove('bg-red-100', 'text-red-700');
                                    messageDiv.classList.add('bg-green-100', 'text-green-700');
                                    messageDiv.textContent = 'Customer account created successfully!';
                                    
                                    // Clear the form
                                    document.getElementById('createCustomerForm').reset();
                                    
                                    // Close the modal after a short delay
                                    setTimeout(() => {
                                        document.querySelector('[x-data]').__x.$data.openModal = false;
                                        messageDiv.classList.add('hidden');
                                    }, 2000);
                                    
                                    // Refresh customer lists for both shipper and consignee
                                    const shipperInput = document.getElementById('shipper_name');
                                    const consigneeInput = document.getElementById('consignee_name');
                                    
                                    if (shipperInput) shipperInput.dispatchEvent(new Event('input'));
                                    if (consigneeInput) consigneeInput.dispatchEvent(new Event('input'));
                                } else {
                                    messageDiv.classList.remove('bg-green-100', 'text-green-700');
                                    messageDiv.classList.add('bg-red-100', 'text-red-700');
                                    messageDiv.textContent = data.message || 'An error occurred while creating the customer account.';
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                const messageDiv = document.getElementById('responseMessage');
                                messageDiv.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                                messageDiv.classList.add('bg-red-100', 'text-red-700');
                                messageDiv.textContent = 'An error occurred while creating the customer account.';
                            });
                        });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-end mb-4">
        </div>
        <!-- Form for pushOrder -->
        <form id= "myForm" method="POST" action="{{ route('pushOrder') }}" onsubmit="return validateForm();">
            @csrf
            <div id="printContainer" class="border p-6 shadow-lg text-black bg-white" style="display: flex; flex-direction: column; min-height: 11in;">
                <div style="flex: 1;">
                    <!-- Your existing content here -->
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
                <div style="display: flex; font-weight: bold; justify-content: flex-end; align-items: center; text-align: right; font-size: 11px;">
                    <select id="cargoType" name="cargoType"
                        style="width: 90px; height: 21px; border: 1px solid #ccc; background: white; text-transform: uppercase; font-family: Arial; font-size: 11px; text-align: right; padding: 1px; cursor: pointer;">
                        <option value=""> </option>
                        <option value="CHARTERED">CHARTERED</option>
                        <option value="LOOSE CARGO">LOOSE CARGO</option>
                        <option value="STUFFING">STUFFING</option>
                        <option value="BACKLOAD">BACKLOAD</option>
                    </select>
                </div>
                        <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; border: none;">
                            <tr style="border: none;">
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 95px; padding: 1px; border: none;">
                                    <strong>M/V EVERWIN STAR</strong>
                                </td>
                                <td style="font-family: Arial; font-size: 12px; width: 40px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase; padding: 0px;">
                                    <select id="ship_no" name="ship_no"
                                        style="width: 100%; height: 15px; border: none; background: transparent; text-align: center;
                                            font-family: Arial; font-size: 11px; text-transform: uppercase; padding: 0px; line-height: 1; cursor: pointer;"
                                        required>
                                        <option value="">Select Ship</option>
                                        @foreach ($ships as $ship)
                                            @if ($ship->status != 'DRYDOCKED' && $ship->status != 'STOP BL')
                                                <option value="{{ $ship->ship_number }}">{{ $ship->ship_number }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td style="font-family: Arial; font-size: 11px; text-align: right; width: 68px; padding: 1px; border: none;"><strong>VOYAGE NO.</strong></td>
                                <td style="font-family: Arial; font-size: 12px; border: none; width: 55px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;">
                                    <input type="hidden" id="voyage_no" name="voyage_no" value="" />
                                    <input type="hidden" id="selected_voyage_group" name="selected_voyage_group" value="" />
                                    <span id="voyage_display" style="display: inline;"></span>
                                    <select id="voyage_select" name="voyage_select" style="display: none; width: 100%; height: 15px; border: none; background: transparent; text-align: center; font-family: Arial; font-size: 11px; text-transform: uppercase; padding: 0px; line-height: 1; cursor: pointer;">
                                        <option value="">Select Voyage</option>
                                    </select>
                                </td>
                                <td style="font-family: Arial; font-size: 11px; text-align: right; width: 80px; padding: 2px; border: none;"><strong>CONTAINER NO.</strong></td>
                                <td style="font-family: Arial; font-size: 10px; border: none; width: 200px; border-bottom: 1px solid black; text-align: center; padding: 0px; min-height: 15px;">
                                    <input id="container_no" name="container_no"
                                        class="block w-full text-center border-none focus:ring focus:ring-indigo-300" type="text"
                                        style="font-family: Arial; font-size: 10px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; line-height: 1;" />
                                </td>
                                <td style="font-family: Arial; font-size: 11px; text-align: right; width: 42px; padding: 2px; border: none;"><strong>BL NO.</strong></td>
                                <td style="font-family: Arial; font-size: 12px; border: none; width: 40px; border-bottom: 1px solid black; text-align: center;"></td>
                            </tr>
                        </table>

                        <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <tr>
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 40px; padding: 2px;"><strong>ORIGIN:</strong></td>
                                <td style="font-family: Arial; font-size: 12px; width: 270px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase; padding: 0px; min-height: 15px;">
                                    <select id="origin" name="origin"
                                        style="width: 100%; height: 15px; border: none; background: transparent; text-align: center;
                                            font-family: Arial; font-size: 11px; text-transform: uppercase; padding: 0px; line-height: 1; cursor: pointer;"
                                        required>
                                        <option value="">Select Origin</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->location }}">{{ $location->location }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="font-family: Arial; font-size: 11px; text-align: right; width: 72px; padding: 2px;"><strong>DESTINATION:</strong></td>
                                <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase; padding: 0px; min-height: 15px;">
                                    <select id="destination" name="destination"
                                        style="width: 100%; height: 15px; border: none; background: transparent; text-align: center;
                                            font-family: Arial; font-size: 11px; text-transform: uppercase; padding: 0px; line-height: 1; cursor: pointer;"
                                        required>
                                        <option value="">Select Destination</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->location }}">{{ $location->location }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="font-family: Arial; font-size: 11px; text-align: right; width: 35px; padding: 2px;"><strong>DATE:</strong></td>
                                <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;">
                                    <span id="currentDate"></span>
                                </td>
                            </tr>
                        </table>
                        <table class="w-full text-sm text-center" style="border-collapse: collapse; width: 100%; table-layout: fixed; margin-top: 8px;">
                            <tr style="height: 15px;"> <!-- Set row height -->
                                <!-- SHIPPER NAME AUTOCOMPLETE -->
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 61.34px; padding: 2px; height: 15px;"><strong>SHIPPER:</strong></td>
                                <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase; height: 15px;">
                                    <input type="text" id="shipper_name" name="shipper_name" list="shipperList"
                                        class="w-full text-center focus:ring focus:ring-indigo-300"
                                        placeholder="Type to search..." autocomplete="off"
                                        style="height: 15px; line-height: 15px; font-size: 12px; border: none; outline: none;">
                                    <datalist id="shipperList"></datalist>
                                </td>

                                <!-- CONSIGNEE NAME AUTOCOMPLETE -->
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 79.97px; padding: 2px; height: 15px;"><strong>CONSIGNEE:</strong></td>
                                <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase; height: 15px;">
                                    <input type="text" id="consignee_name" name="consignee_name" list="consigneeList"
                                        class="w-full text-center focus:ring focus:ring-indigo-300"
                                        placeholder="Type to search..." autocomplete="off"
                                        style="height: 15px; line-height: 15px; font-size: 12px; border: none; outline: none;">
                                    <datalist id="consigneeList"></datalist>
                                </td>
                            </tr>
                        </table>
                        <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed;">
                            <tr style="height: 15px;"> <!-- Set row height -->
                                <!-- SHIPPER CONTACT NUMBER -->
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 38px; padding: 2px; height: 15px;"><strong>CONTACT NO.</strong></td>
                                <td style="font-family: Arial; font-size: 12px; width: 108px; height: 15px; border-bottom: 1px solid black; text-align: center;">
                                    <input type="text" id="shipper_contact" name="shipper_contact"
                                        class="w-full text-center focus:ring focus:ring-indigo-300"
                                        
                                        style="height: 15px; line-height: 15px; font-size: 12px; border: none; outline: none;">
                                </td>
                                <!-- CONSIGNEE CONTACT NUMBER -->
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 38px; padding: 2px; height: 15px;"><strong>CONTACT NO.</strong></td>
                                <td style="font-family: Arial; font-size: 12px; width: 115px; height: 15px; border-bottom: 1px solid black; text-align: center;">
                                    <input type="text" id="consignee_contact" name="consignee_contact"
                                        class="w-full text-center focus:ring focus:ring-indigo-300"
                                        readonly
                                        style="height: 15px; line-height: 15px; font-size: 12px; border: none; outline: none;">
                                </td>
                            </tr>
                        </table>
                        <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed;">
                            <tr>
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 42px; padding: 2px;"><strong>GATE PASS NO.</strong></td>
                                <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; width: 111px; text-align: center; padding: 0px; min-height: 22px;">
                                <input id="gate_pass_no" name="gate_pass_no"
                                    class="block w-full text-center border-none focus:ring focus:ring-indigo-300"
                                    type="text"
                                    style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; line-height: 1;" />
                                </td>
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 25px; padding: 2px;"><strong>REMARK:</strong></td>
                                <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; width: 135px; text-align: center; padding: 0px; min-height: 22px;">
                                <input id="remark" name="remark"
                                    class="block w-full text-center border-none focus:ring focus:ring-indigo-300"
                                    type="text"
                                    style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; line-height: 1;" />
                                </td>
                            </tr>
                        </table>
                        <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed;">
                            <tr>
                                <td style="font-family: Arial; font-size: 11px; text-align: left; width: 10px; padding: 2px;"><strong>VALUE:</strong></td>
                                <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; width: 133px; text-align: center; padding: 0px; min-height: 22px;">
                                    <input id="value" name="value" class="block w-full text-center border-none focus:ring focus:ring-indigo-300"
                                        type="text"
                                        oninput="formatValueInput(this)"
                                        style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; line-height: 1;" />
                                    <input type="hidden" id="valueRaw" name="value_backup" />
                                </td>
                            </tr>
                        </table>

                        <input type="hidden" id="cartData" name="cartData">
                        <input type="hidden" id="cartTotal" name="cartTotal">
                        <input type="hidden" id="shipperId" name="shipperId">
                        <input type="hidden" id="consigneeId" name="consigneeId">

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
                                        <button id="buts" type="button" onclick="openModal(event)" style="background: none; border: none; font-size: 20px; line-height: 1;">+</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="mainTableBody">
                                <tr class="border border-gray">
                                    <td class="p-2 text-center" style="font-family: Arial; font-size: 13px; text-align: center;"></td>
                                    <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                    <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: left;"></td>
                                    <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                    <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                    <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right;"></td>
                                    <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right;"></td>
                                </tr>
                                <tr class="border border-gray">
                                    <td class="p-2"></td>
                                    <td class="p-2"></td>
                                    <td class="p-2"></td>
                                    <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; height: 30px;"><span id="valueDisplay"></span></td>
                                    <td class="p-2"></td>
                                    <td class="p-2"></td>
                                    <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">₱</td>
                                    <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;"></td>
                                </tr>
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
                            <td style="width: 25%; border-bottom: 1px solid black; font-family: Arial; font-size: 12px;  text-align: center; position: relative;">
                                <input type="text" id="freight" name="freight" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1'); formatValueInput(this);" 
                                    style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; text-align: center; line-height: 1; background: transparent; position: absolute; left: 0; right: 0; top: 0; bottom: 0;"/>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-family: Arial; font-size: 12px;">Received on board vessel in apparent good condition.</td>
                            <td></td>
                            <td style="text-align: left; font-family: Arial; font-size: 12px;">Valuation:</td>
                            <td style="border-bottom: 1px solid black; font-family: Arial; font-size: 12px;  text-align: center; position: relative;">
                                <input type="text" id="value" name="value" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1'); formatValueInput(this);" 
                                    style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; text-align: center; line-height: 1; background: transparent; position: absolute; left: 0; right: 0; top: 0; bottom: 0;"/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="text-align: left; font-family: Arial; font-size: 12px;">
                                <span id="wharfageLabel">
                                    @{{ (document.getElementById('origin') && document.getElementById('origin').value.toUpperCase() === 'BATANES') ? 'Wharfage Batanes:' : 'Wharfage:' }}
                                </span>
                            </td>
                            <td style="width: 25%; border-bottom: 1px solid black; font-family: Arial; font-size: 12px; text-align: center; position: relative;">
                                <input type="text" id="wharfage" name="wharfage" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1'); formatValueInput(this);"
                                    style="font-family: Arial; font-size: 11px; width: 100%; height: 15px; border: none; outline: none; padding: 0px; text-align: center; line-height: 1; background: transparent; position: absolute; left: 0; right: 0; top: 0; bottom: 0;"/>
                            </td>
                        </tr>
                        <tr>
                            <!-- Add the checker name dropdown in the checkerNameDisplay <td> -->
                            <td id="checkerNameDisplay" style="font-family: Arial; font-size: 12px; width: 150px; border-bottom: 1px solid black; text-align: center;">
                                <select id="checkerName" name="checkerName[]" multiple style="width: 100%; min-height: 11px; border: none; background: transparent; text-align: center; font-family: Arial; font-size: 12px; font-weight: bold; padding: 0px; line-height: 1; cursor: pointer;" autofocus>
                                    <option value="" selected>-- Select Checker --</option>
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
                                <input type="text" id="other" name="other" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')"
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
                            <td style="border-bottom: 1px solid black; font-weight: bold; font-family: Arial; font-size: 12px;  text-align: center; color: black;"></td>
                        </tr>
                    </table>
                </footer>
            </div>
            <div class="flex justify-end mt-3">
                <x-button class="justify-center w-full gap-2" type="submit" id="submitOrder" disabled>
                    <x-heroicon-o-shopping-cart class="w-6 h-6" aria-hidden="true" />
                    <span>{{ __('Submit Order') }}</span>
                </x-button>
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
                            class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
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
                            class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">

                        <label class="block font-medium text-gray-900 dark:text-gray-200">Item Name:</label>
                        <input type="text" id="itemName" name="itemName"
                            class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">

                        <label class="block font-medium text-gray-900 dark:text-gray-200">Category:</label>
                        <input type="text" id="category" name="category" readonly
                            class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">

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
                                <input type="text" id="price" name="price" oninput="formatPriceInput(this)" class="w-full p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-900 dark:text-gray-200">Weight:</label>
                                <input type="text" id="weight" name="weight" class="w-full p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-900 dark:text-gray-200">Quantity:</label>
                                <input type="text" id="quantity" name="quantity" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="w-full p-1 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <!--label class="block font-medium text-gray-900 dark:text-gray-200">Declared Value:</label>
                        <input type="text" id="value" name="value"
                            class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"-->
                            <!-- Measurements -->
                        <div class="space-y-2" id="measurements">
                            <label class="block font-medium text-gray-900 dark:text-gray-200">Measurements (L × W × H):</label>
                            <div class="grid grid-cols-4 gap-2 items-center">
                                <input type="number" id="length" name="length"
                                    class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                    placeholder="Length" min="0" step="0.01">

                                <input type="number" id="width" name="width"
                                    class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                    placeholder="Width" min="0" step="0.01">

                                <input type="number" id="height" name="height"
                                    class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                    placeholder="Height" min="0" step="0.01">

                                <input list="multipliers" id="multiplier" name="multiplier" type="number"
                                    class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                    placeholder="Multiplier" min="0" step="0.01">
                            </div>
                        </div>

                        <label class="block font-medium text-gray-900 dark:text-gray-200">Description:</label>
                        <textarea id="description" name="description"
                            class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white h-20 focus:ring focus:ring-indigo-200">
                        </textarea>
                    </div>

                    <!-- Right Column: Table -->
                    <div class="w-full lg:w-2/3">
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
                                                @foreach($lists->pluck('category')->unique() as $category)
                                                    <option value="{{ $category }}">{{ $category }}</option>
                                                @endforeach
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
                    <button type="button" onclick="clearFields()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Clear
                    </button>

                    <!-- Right Side: Other Buttons -->
                    <div class="flex space-x-2">
                        <button type="button" onclick="closeModal(event); clearFields()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                        <button id="addToCart" type="button" onclick="addToCart()" class="px-4 py-2 bg-emerald-500 text-white rounded-md
                            hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-700 flex items-center space-x-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Add to Cart</span>
                        </button>
                        <button id="saveButton" type="button" onclick="saveUpdatedItem()" class="px-4 py-2 bg-emerald-500 text-white
                            rounded-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-700 flex items-center
                            space-x-2 hidden">
                            <i class="fas fa-floppy-disk"></i>
                            <span>Update</span>
                        </button>
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
    <br>
</x-app-layout>

<script>
    // For printing
    function printContent(divId) {
        console.log("printContent function called");
        var printContainer = document.getElementById(divId);
        if (!printContainer) {
            console.error("Print container not found");
            return;
        }

        // Replace dropdowns with selected text
        function replaceDropdown(selectId) {
            let select = document.getElementById(selectId);
            if (!select) {
                console.error(`Dropdown with id ${selectId} not found`);
                return null;
            }
            
            // Special handling for multiple select checkerName
            if (selectId === "checkerName") {
                let selectedValues = Array.from(select.selectedOptions).map(option => option.textContent);
                let formattedText = selectedValues.filter(text => text.trim() !== "" && text !== "-- Select Checker --").join(' / ');
                
                let span = document.createElement("span");
                span.textContent = formattedText || "";
                span.style.fontFamily = "Arial";
                span.style.fontSize = "12px";
                span.style.textTransform = "uppercase";
                select.parentNode.replaceChild(span, select);
                return { originalElement: select, newElement: span };
            }
            
            // Regular handling for other dropdowns
            let selectedText = select.options[select.selectedIndex].text;
            let span = document.createElement("span");
            span.textContent = selectedText;
            span.style.fontFamily = "Arial";
            span.style.fontSize = "12px";
            span.style.textTransform = "uppercase";
            select.parentNode.replaceChild(span, select);
            return { originalElement: select, newElement: span };
        }

        // Replace text inputs with static text
        function replaceInput(inputId) {
            let input = document.getElementById(inputId);
            if (!input) {
                console.error(`Input with id ${inputId} not found`);
                return null;
            }
            let inputValue = input.value.trim() || " "; // Use "N/A" if empty
            let span = document.createElement("span");
            span.textContent = inputValue;
            span.style.fontFamily = "Arial";
            span.style.fontSize = "12px";
            span.style.textTransform = "uppercase";
            input.parentNode.replaceChild(span, input);
            return { originalElement: input, newElement: span };
        }

        // Store replaced elements for restoration
        let replacedElements = [];
        replacedElements.push(replaceDropdown("ship_no"));
        replacedElements.push(replaceDropdown("origin"));
        replacedElements.push(replaceDropdown("destination"));
        replacedElements.push(replaceDropdown("checkerName")); // Add checkerName to be replaced
        replacedElements.push(replaceInput("voyage_no"));
        replacedElements.push(replaceInput("container_no"));
        replacedElements.push(replaceInput("shipper_name"));  // NEWLY ADDED
        replacedElements.push(replaceInput("consignee_name")); // NEWLY ADDED
        replacedElements.push(replaceInput("shipper_contact"));  // NEWLY ADDED
        replacedElements.push(replaceInput("consignee_contact")); // NEWLY ADDED
        replacedElements.push(replaceInput("gate_pass_no")); // ✅ Added GATE PASS NO.
        replacedElements.push(replaceInput("remark")); // ✅ Added REMARK

        // Filter out null values
        replacedElements = replacedElements.filter(element => element !== null);

        // Get updated print content
        var printContents = printContainer.innerHTML;

        // Open new print window
        var printWindow = window.open("", "", "width=1000,height=800");
        printWindow.document.write("<html><head><title>Print</title>");
        printWindow.document.write("<style>");
        printWindow.document.write(`
            @media print {
                body { -webkit-print-color-adjust: exact; print-color-adjust: exact; margin: 0; }
                #printContainer { position: relative; width: 100%; min-height: 11in; }
                img { display: block !important; margin: 0 auto; }
                footer { position: absolute; bottom: 0; left: 0; width: 100%; }
                table { border-collapse: collapse; width: 100%; }
                thead { background-color: #78BF65 !important; color: white !important; }
                button { display: none; }
            }
        `);
        printWindow.document.write("</style></head><body>");
        printWindow.document.write(printContents);
        printWindow.document.write("</body></html>");
        printWindow.document.close();
        printWindow.focus();

        // Print and restore elements after printing
        printWindow.onload = function () {
            printWindow.print();
            printWindow.close();

            // Restore original elements
            replacedElements.forEach(({ originalElement, newElement }) => {
                newElement.parentNode.replaceChild(originalElement, newElement);
            });
        };
    }
</script>
<script>
    // For date generate and fetching of shipper and consignee
    document.addEventListener("DOMContentLoaded", function () {
        checkCart();

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

        // Function to fetch customer list based on user input
        function fetchCustomers(inputId, datalistId, contactFieldId, contactId) {
            let inputField = document.getElementById(inputId);
            let dataList = document.getElementById(datalistId);
            let contactField = document.getElementById(contactFieldId);
            let idField = document.getElementById(contactId);
            let debounceTimer;
            let customerCache = []; // Cache to store customer data locally

            inputField.addEventListener("input", function () {
                let query = this.value;
                if (query.length < 2) return; // Only search when typing at least 2 characters

                // Clear the previous timer to implement debounce
                clearTimeout(debounceTimer);
                
                // Set a new timer (300ms delay before API call)
                debounceTimer = setTimeout(() => {
                    console.log(`Fetching customers for query: ${query}`);
                    fetch(`/search-customers?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Fetched data:", data);
                        dataList.innerHTML = "";
                        // Update our local cache
                        customerCache = data;
                        
                        data.forEach(customer => {
                            let option = document.createElement("option");
                            option.value = customer.name; // Show customer name
                            option.dataset.phone = customer.phone; // Store phone in dataset
                            option.dataset.id = customer.id || customer.sub_account_number; // Store customer ID in dataset
                            // Add extra attributes to improve selection reliability
                            option.setAttribute('data-name', customer.name);
                            dataList.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error fetching customers:", error));
                }, 300); // 300ms debounce
            });

            // Keep the original change event for backward compatibility
            inputField.addEventListener("change", function () {
                let selectedOption = [...dataList.options].find(option => option.value === inputField.value);
                
                // Try to find in the cache if not found in options (more reliable)
                if (!selectedOption && customerCache.length > 0) {
                    const matchedCustomer = customerCache.find(customer => customer.name === inputField.value);
                    if (matchedCustomer) {
                        contactField.value = matchedCustomer.phone || "";
                        idField.value = matchedCustomer.id || matchedCustomer.sub_account_number || "";
                        console.log(`Selected customer from cache: ${inputField.value}, Contact: ${contactField.value}, ID: ${idField.value}`);
                        return;
                    }
                }
                
                contactField.value = selectedOption ? selectedOption.dataset.phone : "";
                idField.value = selectedOption ? selectedOption.dataset.id : "";
                console.log(`Selected customer: ${inputField.value}, Contact: ${contactField.value}, ID: ${idField.value}`);
            });
            
            // Add a focusout event which is more reliable for capturing the final selection
            inputField.addEventListener("focusout", function() {
                // Allow a small delay for the browser to update the field value
                setTimeout(() => {
                    const currentValue = inputField.value.trim();
                    if (!currentValue) return;
                    
                    // First check our local cache for an exact match (most reliable)
                    const cacheMatch = customerCache.find(customer => customer.name === currentValue);
                    if (cacheMatch) {
                        contactField.value = cacheMatch.phone || "";
                        idField.value = cacheMatch.id || cacheMatch.sub_account_number || "";
                        console.log(`Selected customer on focusout (cache): ${currentValue}, Contact: ${contactField.value}, ID: ${idField.value}`);
                        return;
                    }
                    
                    // Then try to find in datalist options
                    const optionMatch = [...dataList.options].find(option => option.value === currentValue);
                    if (optionMatch && optionMatch.dataset.phone && optionMatch.dataset.id) {
                        contactField.value = optionMatch.dataset.phone;
                        idField.value = optionMatch.dataset.id;
                        console.log(`Selected customer on focusout (option): ${currentValue}, Contact: ${contactField.value}, ID: ${idField.value}`);
                    }
                }, 100);
            });
        }

        // Attach event listeners for both shipper and consignee
        fetchCustomers("shipper_name", "shipperList", "shipper_contact", "shipperId");
        fetchCustomers("consignee_name", "consigneeList", "consignee_contact", 'consigneeId');
    });
</script>

<script>
    // For add to cart modal
    document.getElementById("myForm").addEventListener("click", function (event) {
        // Check if the clicked element has one of the target IDs or is an ancestor of these elements
        if (event.target.id === "eds" || event.target.id === "dels" || event.target.id === "buts" || 
            event.target.closest("#eds") || event.target.closest("#dels") || event.target.closest("#buts")) {
            event.preventDefault(); // Prevent default behavior
            event.stopPropagation(); // Stop event from bubbling up
        }
    });

    let cart = [];
    let totalPrice = 0;

    function openModal(e) {
        if (e) {
            e.preventDefault(); // Prevent default form submission
            e.stopPropagation(); // Stop event from bubbling up
        }
        document.getElementById('addToCartModal').classList.remove('hidden');
        return false; // Prevent form submission
    }

    function closeModal(e) {
        if (e) {
            e.preventDefault(); // Prevent default form submission
            e.stopPropagation(); // Stop event from bubbling up
        }
        document.getElementById('addToCartModal').classList.add('hidden');
        return false; // Prevent form submission
    }

    // Ensure the event listener is attached after the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.getElementById('itemTableBody');

        tableBody.addEventListener('click', (event) => {
            const row = event.target.closest('tr');
            if (!row) return;

            const itemCode = row.children[0].textContent;
            const itemName = row.children[1].textContent;
            const category = row.children[2].textContent;
            const price = row.children[3].textContent;
            const multiplier = row.children[4].textContent;
            console.log('Selected item:', itemCode, itemName, category, price, multiplier);

            // Populate the input fields
            document.getElementById('itemCode').value = itemCode;
            document.getElementById('itemName').value = itemName;
            document.getElementById('category').value = category;
            document.getElementById('price').value = price;
            document.getElementById('multiplier').value = multiplier;
            
            // Auto-populate description for motorcycle items
            const motorcycleCodes = ["VHC-011", "VHC-012", "VHC-013", "VOL-013"];
            if (motorcycleCodes.includes(itemCode.trim())) {
                document.getElementById('description').value = "MODEL: \nENGINE NO: \nCHASSIS NO: \nCOLOR: ";
            } else {
                document.getElementById('description').value = "";
            }
        });
    });

    function addToCart() {
        // Plus
        let selectedValue = document.getElementById("unit").value;
        let description = document.getElementById("description").value;
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

        // Handle empty or zero values to be null/empty for database
        let l = parseFloat(document.getElementById('length').value) || null;
        let w = parseFloat(document.getElementById('width').value) || null;
        let h = parseFloat(document.getElementById('height').value) || null;
        let weight = document.getElementById('weight').value ? parseFloat(document.getElementById('weight').value) : null;
        
        // Handle multiplier - convert 'N/A' or empty string to null
        let m = document.getElementById('multiplier').value;
        if (m === 'N/A' || m === '' || m === '0') {
            m = null;
        } else {
            m = parseFloat(m);
        }
        
        let price = parseFloat(document.getElementById('price').value.replace(/,/g, "")) || 0; // Ensure price is a number
        let quantity = parseFloat(document.getElementById('quantity').value) || 1;
        let total = 0;
        let category = document.getElementById('category').value;

        if (m === null) {
            total = price * quantity;
        } else {
            if(l === null || w === null || h === null) {
                alert("Please enter the measurements before adding to cart!");
                return;
            }
            price = l * w * h * m;
            total = price * quantity;
        }

        totalPrice += total;
        const item = {
            itemCode: document.getElementById('itemCode').value,
            itemName: document.getElementById('itemName').value,
            unit: document.getElementById('unit').value,
            category: category,
            weight: weight,
            length: l,
            width: w,
            height: h,
            multiplier: m,
            price: price,
            description: description || "", // Allow empty description
            quantity: quantity,
            total: total
        };

        cart.push(item);
        updateMainTable();
        clearFields(); // Clear all fields after adding to cart
        closeModal();
        console.log('Cart:', cart);
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

        // Always enable the submit button regardless of cart length
        submitBtn.disabled = false;

        // Check if there's at least one 'FROZEN' or 'PARCEL' in the cart
        const hasRestrictedItem = cart.some(item => item.category === 'FROZEN' || item.category === 'PARCEL');

        if (hasRestrictedItem) {
            inputField.value = ''; // Clear input
            inputField.readOnly = true; // Make it readonly
            isLocked = true; // Lock it permanently
        } else if (!hasRestrictedItem) {
            inputField.readOnly = false; // Allow editing if no restricted items
        }
    }

    function updateMainTable() {
        // Multiply
        const mainTableBody = document.getElementById('mainTableBody');
        mainTableBody.innerHTML = '';
        checkCart();
        const cartDataInput = document.getElementById('cartData');
        cartDataInput.value = JSON.stringify(cart);

        document.getElementById('cartTotal').value = formatPrice(totalPrice);

        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = "border-b";

            row.innerHTML = `
                <td class="p-2 text-center">${item.quantity}</td>
                <td class="p-2 text-center">${item.unit}</td>
                <td class="p-2 text-center">${item.itemName} ${item.description}</td>
                <td class="p-2 text-center"> </td>
                <td class="p-2 text-center">${item.weight !== null ? item.weight : ''}</td>
            `;

            // Check if multiplier is empty or null
            if (!item.multiplier || item.multiplier === 'N/A') {
                // Check if length, width, or height are null and display accordingly
                const length = item.length !== null ? item.length : '';
                const width = item.width !== null ? item.width : '';
                const height = item.height !== null ? item.height : '';
                
                if (length || width || height) {
                    row.innerHTML += `<td class="p-2 text-center">${length} × ${width} × ${height}</td>`;
                } else {
                    row.innerHTML += `<td class="p-2 text-center"></td>`;
                }
            } else {
                // Check if length, width, or height are null and display accordingly
                const length = item.length !== null ? item.length : '';
                const width = item.width !== null ? item.width : '';
                const height = item.height !== null ? item.height : '';
                
                if (length || width || height) {
                    row.innerHTML += `<td class="p-2 text-center">${length} × ${width} × ${height} × ${Number(item.multiplier).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                } else {
                    row.innerHTML += `<td class="p-2 text-center"></td>`;
                }
            }

            row.innerHTML += `
                <td class="p-2 text-center">${Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td class="p-2 text-center">${Number(item.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td class="p-2 text-center">
                    <a href="#" class="text-blue-500 text-center" onclick="openEditModal(${index}, event)">
                        <button id="eds" type="button" variant="warning" class="bg-yellow-500 hover:bg-yellow-600 text-white rounded p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                    </a>
                </td>
                <td class="p-2 text-center">
                    <a href="#" class="text-blue-500 text-center" onclick="deleteItem(${index}, event)">
                        <button id="dels" type="button" variant="danger" class="bg-red-500 hover:bg-red-600 text-white rounded p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
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
        
        // Make cart globally accessible for the wharfage calculator
        window.cart = cart;
        
        // Dispatch custom event for wharfage calculation
        document.dispatchEvent(new CustomEvent('cartUpdated'));

        // Append summary row at the end of the table
        let summaryRow = document.createElement("tr");
        summaryRow.id = "summaryRow"; // Give an ID to avoid duplicates
        summaryRow.classList.add("border", "border-gray");
        summaryRow.innerHTML = `
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; height: 30px;"><span id="valueDisplay"></span></td>
            <td class="p-2"></td>
            <td class="p-2"></td>
            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">₱</td>
            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">
                ${Number(totalPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        `;

               mainTableBody.appendChild(summaryRow);

        toggleButtonsVisibility();
    }

    function deleteItem(index, e) {
        e.preventDefault(); // Prevent default form submission
        e.stopPropagation(); // Stop event from bubbling up
        
        totalPrice -= cart[index].total;
        cart.splice(index, 1);
        updateMainTable();
        
        return false; // Prevent form submission
    }

    function toggleButtonsVisibility() {
        const editButtons = document.querySelectorAll("[variant='warning']");
        const deleteButtons = document.querySelectorAll("[variant='danger']");
        const visibility = cart.length > 0 ? "" : "hidden";

        editButtons.forEach(btn => btn.style.visibility = visibility);
        deleteButtons.forEach(btn => btn.style.visibility = visibility);
    }

    function openEditModal(index, e) {
        e.preventDefault(); // Prevent default form submission
        e.stopPropagation(); // Stop event from bubbling up
        
        document.getElementById('addToCart').classList.add('hidden'); // Hide Add to Cart
        document.getElementById('saveButton').classList.remove('hidden'); // Show Save Button

        const item = cart[index];

        document.getElementById('itemCode').value = item.itemCode;
        document.getElementById('itemName').value = item.itemName;
        document.getElementById('unit').value = item.unit;
        document.getElementById('category').value = item.category;
        document.getElementById('weight').value = item.weight;
        document.getElementById('length').value = item.length;
        document.getElementById('width').value = item.width;
        document.getElementById('height').value = item.height;
        document.getElementById('multiplier').value = item.multiplier;
        document.getElementById('price').value = item.price;
        document.getElementById('description').value = item.description;
        document.getElementById('quantity').value = item.quantity;
        edit = item.total;

        // Store the index for updating later
        document.getElementById('saveButton').setAttribute('data-index', index);

        // Show the modal
        document.getElementById('addToCartModal').classList.remove('hidden');
        
        return false; // Prevent form submission
    }

    // Function to save the updated data
    function saveUpdatedItem() {
        document.getElementById('addToCart').classList.remove('hidden'); // Show Add to Cart
        document.getElementById('saveButton').classList.add('hidden'); // Hide Save Button
        const currentEditIndex = document.getElementById('saveButton').getAttribute('data-index'); // Get stored index

        if (currentEditIndex !== null) {
            // Handle empty or zero values to be null/empty for database
            let l = parseFloat(document.getElementById('length').value) || null;
            let w = parseFloat(document.getElementById('width').value) || null;
            let h = parseFloat(document.getElementById('height').value) || null;
            let weight = document.getElementById('weight').value ? parseFloat(document.getElementById('weight').value) : null;
            
            // Handle multiplier - convert 'N/A' or empty string to null
            let m = document.getElementById('multiplier').value;
            if (m === 'N/A' || m === '' || m === '0') {
                m = null;
            } else {
                m = parseFloat(m);
            }
            
            let price = parseFloat(document.getElementById('price').value.replace(/,/g, '')) || 0; // Handle thousand separators
            let quantity = parseFloat(document.getElementById('quantity').value) || 1;
            let total = 0;

            if (m === null) {
                total = price * quantity;
            } else {
                // Only calculate if all dimensions are provided
                if (l !== null && w !== null && h !== null) {
                    price = l * w * h * m;
                    total = price * quantity;
                } else {
                    total = price * quantity;
                }
            }

            totalPrice -= parseFloat(cart[currentEditIndex].total);
            totalPrice += total;

            cart[currentEditIndex] = {
                itemCode: document.getElementById('itemCode').value,
                itemName: document.getElementById('itemName').value,
                unit: document.getElementById('unit').value,
                category: document.getElementById('category').value,
                weight: weight,
                length: l,
                width: w,
                height: h,
                multiplier: m,
                price: price.toFixed(2),
                description: document.getElementById('description').value || "",
                quantity: quantity,
                total: total.toFixed(2)
            };

            // Update the hidden input field with the cart data
            document.getElementById('cartData').value = JSON.stringify(cart);

            // Update the displayed table with new data
            updateMainTable();
            closeModal();
        }
    }

    function clearFields() {
        document.getElementById('itemCode').value = "";
        document.getElementById('itemName').value = "";
        document.getElementById('unit').value = "";
        document.getElementById('category').value = "";
        document.getElementById('weight').value = "";
        document.getElementById('length').value = "";
        document.getElementById('width').value = "";
        document.getElementById('height').value = "";
        document.getElementById('multiplier').value = "";
        document.getElementById('price').value = "";
        document.getElementById('description').value = "";
        document.getElementById('quantity').value = "";
        document.getElementById('description').value = "";
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

    // Real-time price list update functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Function to handle storage changes (for cross-tab communication)
        window.addEventListener('storage', function(event) {
            // Only respond to our specific event for new price list items
            if (event.key === 'newPriceListItem') {
                try {
                    const data = JSON.parse(event.newValue);
                    if (data && data.timestamp && data.item) {
                        addNewItemToModal(data.item);
                    }
                } catch (error) {
                    console.error('Error parsing price list update:', error);
                }
            }
        });

        // Function to add a new item to the price list modal
        function addNewItemToModal(item) {
            const itemTableBody = document.getElementById('itemTableBody');
            if (!itemTableBody) return;

            // Create a new row
            const newRow = document.createElement('tr');
            newRow.className = 'item-row';
            newRow.setAttribute('data-category', item.category);

            // Add cell content
            newRow.innerHTML = `
                <td class="p-3 border" style="text-align: left;">${item.item_code}</td>
                <td class="p-3 border" style="text-align: left;">${item.item_name}</td>
                <td class="p-3 border">${item.category}</td>
                <td class="p-3 border" hidden>${item.price ? Number(item.price).toFixed(2) : 'N/A'}</td>
                <td class="p-3 border" hidden>${item.multiplier || ''}</td>
            `;

            // Add the new row to the table
            itemTableBody.prepend(newRow);

            // Make the row clickable like existing rows
            newRow.addEventListener('click', function() {
                const itemCode = this.children[0].textContent;
                const itemName = this.children[1].textContent;
                const category = this.children[2].textContent;
                const price = this.children[3].textContent;
                const multiplier = this.children[4].textContent;

                // Populate the form fields
                document.getElementById('itemCode').value = itemCode;
                document.getElementById('itemName').value = itemName;
                document.getElementById('category').value = category;
                document.getElementById('price').value = price;
                document.getElementById('multiplier').value = multiplier;
            });

            // Also update the category filter dropdown if needed
            updateCategoryFilterIfNeeded(item.category);

            // Show notification to user
            showNotification(`New item added: ${item.item_name}`);
        }

        // Function to update the category filter dropdown if the new category doesn't exist
        function updateCategoryFilterIfNeeded(category) {
            const categoryFilter = document.getElementById('categoryFilter');
            if (!categoryFilter) return;

            // Check if this category already exists in the dropdown
            let exists = false;
            for (let i = 0; i < categoryFilter.options.length; i++) {
                if (categoryFilter.options[i].value === category) {
                    exists = true;
                    break;
                }
            }

            // If category doesn't exist in the dropdown, add it
            if (!exists) {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                categoryFilter.appendChild(option);
            }
        }

        // Function to show a brief notification
        function showNotification(message) {
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
        }

        // Check for direct URL updates with new item data (when returning from pricelist page)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('new_item')) {
            try {
                const newItemData = JSON.parse(atob(urlParams.get('new_item')));
                addNewItemToModal(newItemData);
                
                // Clean up the URL to avoid repeated additions
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            } catch (error) {
                console.error('Error processing new item from URL:', error);
            }
        }
    });
</script>

<script>
    // For Account Type
    function updateAccountType() {
        const firstName = document.querySelector('input[name="first_name"]').value.trim();
        const lastName = document.querySelector('input[name="last_name"]').value.trim();
        const companyName = document.querySelector('input[name="company_name"]').value.trim();
        const typeField = document.querySelector('select[name="type"]');

        if (companyName) {
            typeField.value = 'company';
        } else if (firstName || lastName) {
            typeField.value = 'individual';
        } else {
            typeField.value = ''; // Reset if no fields are filled
        }
    }

    // For Clear Fields
    function resetFields() {
        // Clear all input fields in the modal
        document.querySelectorAll('input').forEach(input => input.value = '');
        document.querySelectorAll('select').forEach(select => select.value = '');
        document.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
    }
</script>

<script>
    // For Checker Name
    document.addEventListener("DOMContentLoaded", function () {
        @php
        // Fetch all checkers grouped by location
        $checkersData = DB::table('checkers')
            ->select('name', 'location')
            ->orderBy('location')
            ->orderBy('name')
            ->get();
            
        // Group checkers by location
        $checkersByLocation = [];
        foreach ($checkersData as $checker) {
            $location = strtoupper($checker->location);
            if (!isset($checkersByLocation[$location])) {
                $checkersByLocation[$location] = [" "]; // Add empty option
            }
            $checkersByLocation[$location][] = $checker->name;
        }
        @endphp
        
        // Convert PHP array to JavaScript object
        const checkerNames = @json($checkersByLocation);
        console.log('Checker names loaded from database:', checkerNames);

        const originSelect = document.getElementById("origin");
        const checkerNameSelect = document.getElementById("checkerName");

        function updateCheckerNames() {
            const selectedOrigin = originSelect.value.toUpperCase();
            const names = checkerNames[selectedOrigin] || [];
            
            // Update wharfage label based on origin
            const wharfageLabel = document.querySelector('td:has(span#wharfageLabel) span') || document.getElementById('wharfageLabel');
            if (wharfageLabel) {
                wharfageLabel.textContent = selectedOrigin === 'BATANES' ? 'Wharfage Batanes:' : 'Wharfage:';
            }

            checkerNameSelect.innerHTML = "";
            
            // Add blank option first
            const blankOption = document.createElement("option");
            blankOption.value = "";
            blankOption.textContent = "-- Select Checker --";
            checkerNameSelect.appendChild(blankOption);

            names.forEach(name => {
                const option = document.createElement("option");
                option.value = name;
                option.textContent = name;
                checkerNameSelect.appendChild(option);
            });

            console.log(`Origin selected: ${selectedOrigin}`);
            console.log(`Checker names:`, names);
        }

        // Format selected options with '/' separator
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
            console.log(`Checker selected: ${formatSelectedOptions()}`);
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
    function formatValueInput(input) {
        // Remove all non-numeric characters except the decimal point
        let rawValue = input.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
        
        // If the value is empty or NaN, just clear the input
        if (rawValue === '' || isNaN(parseFloat(rawValue))) {
            input.value = '';
            let hiddenInput = document.getElementById('valueRaw');
            if (hiddenInput) hiddenInput.value = '';
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
            hiddenInput.name = 'value_backup';
            input.parentNode.appendChild(hiddenInput);
        }
        // Save the raw numeric value for form submission
        hiddenInput.value = rawValue;
    }
</script>

<script>
    function formatPriceInput(input) {
        // Remove all non-numeric characters except the decimal point
        let rawValue = input.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
        
        // If the value is empty or NaN, just clear the input
        if (rawValue === '' || isNaN(parseFloat(rawValue))) {
            input.value = '';
            // Remove any existing hidden input
            const existingHidden = document.getElementById('priceRaw_' + input.id);
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
        let hiddenInput = document.getElementById('priceRaw_' + input.id);
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'priceRaw_' + input.id;
            hiddenInput.name = input.name;
            input.parentNode.appendChild(hiddenInput);
            // Remove the name attribute from the visible input to prevent it from being submitted
            input.removeAttribute('name');
        }
        hiddenInput.value = rawValue;
    }
</script>

<script>
// Create New Item Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const createNewItemBtn = document.getElementById('createNewItemBtn');
    const createPriceListItemModal = document.getElementById('createPriceListItemModal');
    const priceListItemForm = document.getElementById('priceListItemForm');
    
    // Price type toggle
    const priceTypeRadios = document.getElementsByName('price_type');
    const measurementsGroup = document.getElementById('measurements_group');
    const priceLabel = document.getElementById('price_label');
    
    // Show/hide measurements based on price type
    priceTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'multiplier') {
                measurementsGroup.classList.remove('hidden');
                priceLabel.textContent = 'Multiplier';
            } else {
                measurementsGroup.classList.add('hidden');
                priceLabel.textContent = 'Price';
            }
        });
    });
    
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
});
</script>

<script>
    // Initialize wharfage calculation functionality
    document.addEventListener("DOMContentLoaded", function() {
        // Reference to important elements
        const freightInput = document.getElementById("freight");
        const valueInput = document.getElementById("value");
        const wharfageInput = document.getElementById("wharfage");
        const containerInput = document.getElementById("container_no");
        
        // Function to calculate wharfage
        function calculateWharfage() {
            // Get current values
            const freight = parseFloat(freightInput.value) || 0;
            const value = parseFloat(valueInput.value) || 0;
            
            // Skip wharfage calculation if both value and freight are 0
            if (value <= 0 && freight <= 0) {
                wharfageInput.value = "0.00";
                return;
            }
            
            // Check if the cart contains only GROCERIES
            let onlyGroceries = true;
            let hasNonGroceries = false;
            let hasGM019orGM020 = false;
            
            // Check each parcel item
            if (cart && cart.length > 0) {
                cart.forEach(function(item) {
                    if (item.category !== 'GROCERIES') {
                        onlyGroceries = false;
                        hasNonGroceries = true;
                    }
                    
                    // Check if the item is GM-019 or GM-020
                    // Look for both item_code and itemCode properties that might be used
                    const itemCode = item.item_code || item.itemCode || '';
                    if (itemCode === 'GM-019' || itemCode === 'GM-020') {
                        hasGM019orGM020 = true;
                    }
                });
            
                // Calculate wharfage based on parcel category
                let wharfage = 0;
                if (onlyGroceries || hasGM019orGM020) {
                    wharfage = freight / 800 * 23; // For GROCERIES only or when contains GM-019/GM-020
                } else {
                    wharfage = freight / 1200 * 23; // For other items
                }
                
                // Set minimum wharfage to 11.20 if not zero
                if (wharfage > 0 && wharfage < 11.20) {
                    wharfage = 11.20;
                }
                
                // Format and set the wharfage value
                wharfageInput.value = wharfage.toFixed(2);
            }
        }
        
        // Add event listeners to recalculate wharfage when freight or value changes
        if (freightInput) {
            freightInput.addEventListener("change", calculateWharfage);
            freightInput.addEventListener("input", calculateWharfage);
        }
        
        if (valueInput) {
            valueInput.addEventListener("change", calculateWharfage);
            valueInput.addEventListener("input", calculateWharfage);
        }
        
        // Calculate initial wharfage when the page loads
        if (freightInput && valueInput && wharfageInput) {
            calculateWharfage();
        }
    });
</script>

<style>
    /* Style for dropdown input fields */
    input[list], select {
        cursor: pointer !important;
    }
    
    /* Style for dropdown arrow in browsers */
    input::-webkit-calendar-picker-indicator {
        cursor: pointer !important;
    }
    
    /* Style for dropdown options */
    datalist option, select option {
        cursor: pointer !important;
    }
    
    /* Specific styling for shipper and consignee dropdowns */
    #shipperList option, #consigneeList option {
        cursor: pointer !important;
        padding: 5px;
    }

    /* Style for select arrow in various browsers */
    select::-ms-expand {
        cursor: pointer !important;
    }

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
    
    /* Custom styles for the multiple select checker dropdown */
    #checkerName {
        overflow: auto;
        min-height: 20px;
        height: auto !important;
        cursor: pointer;
    }
    
    #checkerName option {
        padding: 2px;
    }
    
    /* Style for the checker select on printout */
    @media print {
        #checkerName {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            border: none !important;
            overflow: visible;
            height: auto !important;
            min-height: 0 !important;
        }
        
        #checkerName option:not(:checked) {
            display: none;
        }
    }

    /* Screen Display */
    @media screen {
        #printContainer {
            border: 1px solid black; /* Visual border for preview */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }
    }
</style>

<!-- Include the prevent form submission script -->
<script src="{{ asset('js/prevent-form-submit.js') }}"></script>
<script src="{{ asset('js/form-validation.js') }}"></script>

<!-- Set current voyage for wharfage calculation -->
<script>
    // Make the current voyage available to the wharfage calculator
    // This is typically the latest voyage for the selected ship
    window.addEventListener('DOMContentLoaded', function() {
        const shipSelect = document.getElementById('ship_no');
        const voyageInput = document.getElementById('voyage_no');
        const voyageDisplay = document.getElementById('voyage_display');
        const voyageSelect = document.getElementById('voyage_select');
        const selectedVoyageGroupInput = document.getElementById('selected_voyage_group');
        const originSelect = document.getElementById('origin');
        const destinationSelect = document.getElementById('destination');
        
        // Function to fetch and update voyages
        function updateVoyageOptions() {
            const selectedShip = shipSelect.value;
            const selectedOrigin = originSelect.value;
            const selectedDestination = destinationSelect.value;
            
            if (selectedShip) {
                let apiUrl = `/api/available-voyages/${selectedShip}`;
                let params = new URLSearchParams();
                
                if (selectedOrigin) params.append('origin', selectedOrigin);
                if (selectedDestination) params.append('destination', selectedDestination);
                
                if (params.toString()) {
                    apiUrl += '?' + params.toString();
                }
                
                // Fetch available voyages for the selected ship with route info
                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const voyages = data.voyages;
                            
                            if (voyages.length === 1) {
                                // Single voyage - use existing behavior
                                const voyage = voyages[0];
                                window.currentVoyage = voyage.voyage_number;
                                
                                if (voyageInput) {
                                    voyageInput.value = voyage.voyage_number;
                                }
                                
                                if (voyageDisplay) {
                                    voyageDisplay.textContent = voyage.voyage_number;
                                    voyageDisplay.style.display = 'inline';
                                }
                                
                                if (voyageSelect) {
                                    voyageSelect.style.display = 'none';
                                }
                                
                                if (selectedVoyageGroupInput) {
                                    selectedVoyageGroupInput.value = voyage.voyage_group || '';
                                }
                                
                                // Trigger container usage check for wharfage calculation
                                document.dispatchEvent(new CustomEvent('voyageUpdated', { detail: voyage.voyage_number }));
                            } else if (voyages.length > 1) {
                                // Multiple voyages - show dropdown
                                if (voyageDisplay) {
                                    voyageDisplay.style.display = 'none';
                                }
                                
                                if (voyageSelect) {
                                    // Clear existing options
                                    voyageSelect.innerHTML = '<option value="">Select Voyage</option>';
                                    
                                    // Add voyage options
                                    voyages.forEach(voyage => {
                                        const option = document.createElement('option');
                                        option.value = voyage.voyage_number;
                                        option.textContent = voyage.label;
                                        option.setAttribute('data-voyage-group', voyage.voyage_group || '');
                                        
                                        // Highlight matching route voyages
                                        if (voyage.matches_route) {
                                            option.style.fontWeight = 'bold';
                                            option.style.color = '#059669'; // Green color for matching routes
                                        }
                                        
                                        voyageSelect.appendChild(option);
                                    });
                                    
                                    voyageSelect.style.display = 'inline';
                                    
                                    // Auto-select the first matching route if available
                                    const matchingVoyage = voyages.find(v => v.matches_route);
                                    if (matchingVoyage && selectedOrigin && selectedDestination) {
                                        voyageSelect.value = matchingVoyage.voyage_number;
                                        
                                        // Trigger change event to update hidden fields
                                        voyageSelect.dispatchEvent(new Event('change'));
                                    }
                                }
                            } else {
                                // No voyages available
                                if (voyageDisplay) {
                                    voyageDisplay.textContent = 'No voyages available';
                                    voyageDisplay.style.display = 'inline';
                                }
                                
                                if (voyageSelect) {
                                    voyageSelect.style.display = 'none';
                                }
                            }
                        } else {
                            console.error('Error fetching voyages:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching voyages:', error);
                        
                        // Fallback to existing behavior
                        @if(isset($voyages) && count($voyages) > 0)
                            // Get the latest voyage for the selected ship
                            const voyagesByShip = @json($voyages->groupBy('ship_number'));
                            if (voyagesByShip[selectedShip] && voyagesByShip[selectedShip].length > 0) {
                                // Sort by voyage number to get the latest
                                const latestVoyage = voyagesByShip[selectedShip][0].voyage_number;
                                window.currentVoyage = latestVoyage;
                                
                                if (voyageInput) {
                                    voyageInput.value = latestVoyage;
                                }
                                
                                if (voyageDisplay) {
                                    voyageDisplay.textContent = latestVoyage;
                                    voyageDisplay.style.display = 'inline';
                                }
                                
                                if (voyageSelect) {
                                    voyageSelect.style.display = 'none';
                                }
                                
                                // Trigger container usage check for wharfage calculation
                                document.dispatchEvent(new CustomEvent('voyageUpdated', { detail: latestVoyage }));
                            }
                        @endif
                    });
            }
        }
        
        // Event listeners
        if (shipSelect) {
            shipSelect.addEventListener('change', updateVoyageOptions);
        }
        
        if (originSelect) {
            originSelect.addEventListener('change', updateVoyageOptions);
        }
        
        if (destinationSelect) {
            destinationSelect.addEventListener('change', updateVoyageOptions);
        }
        
        // Handle voyage selection change
        if (voyageSelect) {
            voyageSelect.addEventListener('change', function() {
                const selectedVoyage = this.value;
                const selectedOption = this.options[this.selectedIndex];
                const voyageGroup = selectedOption.getAttribute('data-voyage-group') || '';
                
                if (selectedVoyage) {
                    window.currentVoyage = selectedVoyage;
                    
                    if (voyageInput) {
                        voyageInput.value = selectedVoyage;
                    }
                    
                    if (selectedVoyageGroupInput) {
                        selectedVoyageGroupInput.value = voyageGroup;
                    }
                    
                    // Trigger container usage check for wharfage calculation
                    document.dispatchEvent(new CustomEvent('voyageUpdated', { detail: selectedVoyage }));
                }
            });
        }
    });
</script>

<script src="{{ asset('js/wharfage-calculator.js') }}"></script>
