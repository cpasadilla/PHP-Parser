<x-app-layout>
    <x-slot name="header"></x-slot>
    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-end mb-4">
            <button class="btn btn-success" onclick="printContent('printContainer')">PRINT</button>
        </div>
        <div id="printContainer" class="border p-6 shadow-lg text-black bg-white" style="display: flex; flex-direction: column; min-height: 11in;">
            <div style="flex: 1;">
                <!-- Your existing content here -->
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                <img style="width: 500px; height: 70px;" src="{{ asset('images/logo-sfx.png') }}" alt="Logo">
                    <div style="font-family: Arial; font-size: 12px; line-height: 1.2; margin-top: 3px;">
                        <p style="margin: 0;">National Road Brgy. Kaychanarianan, Basco Batanes</p>
                        <p style="margin: 0;">Cellphone Nos.: 0908-815-9300 / 0999-889-5848 / 0999-889-5849</p>
                        <p style="margin: 0;">Email Address: fxavier_2015@yahoo.com.ph</p>
                    </div>
                </div>
                <!-- Title -->
            <div style="display: flex; justify-content: center; margin-top: 5px;">
                <span style="font-family: Arial; font-weight: bold; font-size: 17px;">BILL OF LADING</span>
            </div>
                        <!-- Display the empty <p> tag when status is null or blank -->
                        <div style="display: flex; font-weight: bold; justify-content: flex-end; align-items: center; font-size: 11px; ">
                            <span style="text-transform: uppercase; color: white;">.</span>
                        </div>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <tr>
                        <td style="font-family: Arial; font-family: Arial; font-size: 11px; text-align: left; width: 105px; padding: 1px;"><strong>M/V EVERWIN STAR</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 60px; border-bottom: 1px solid black; text-align: center;"></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 73px; padding: 1px;"><strong>VOYAGE NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 80px; border-bottom: 1px solid black; text-align: center;"></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 87px; padding: 2px;"><strong>CONTAINER NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 115px; border-bottom: 1px solid black; text-align: center;"></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 42px; padding: 2px;"><strong>BL NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 70px; border-bottom: 1px solid black; text-align: center;"></td>
                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 40px; padding: 2px;"><strong>ORIGIN:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;"></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 72px; padding: 2px;"><strong>DESTINATION:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;"></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: right; width: 35px; padding: 2px;"><strong>DATE:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 170px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;"></td>

                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed; margin-top: 8px;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 61.34px; padding: 2px;"><strong>SHIPPER:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;"></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 79.97px; padding: 2px;"><strong>CONSIGNEE:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; border-bottom: 1px solid black; text-align: center; text-transform: uppercase;"></td>
                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 74px; padding: 2px;"><strong>SHIPPER CONTACT NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 117px; border-bottom: 1px solid black; text-align: center; "></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 84px; padding: 2px;"><strong>CONSIGNEE CONTACT NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 115px; border-bottom: 1px solid black; text-align: center;"></td>
                    </tr>
                </table>
                <table class="w-full text-sm text-center" style="border-collapse: collapse; border-spacing: 0; width: 100%; table-layout: fixed;">
                    <tr>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 42px; padding: 2px;"><strong>GATE PASS NO.</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 111px; border-bottom: 1px solid black; text-align: center;"></td>
                        <td style="font-family: Arial; font-size: 11px; text-align: left; width: 25px; padding: 2px;"><strong>REMARK:</strong></td>
                        <td style="font-family: Arial; font-size: 12px; width: 135px; border-bottom: 1px solid black; text-align: center;"></td>
                    </tr>
                </table>

                <!-- Main Table -->
                <table class="w-full border-collapse text-sm main-table" style="padding: 0 5px; margin-top: 20px;">
                    <thead class="text-white border border-gray" style="background-color: #78BF65;">
                        <tr class="border border-gray">
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">QTY</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px; width: 70px;">UNIT</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">DESCRIPTION</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">VALUE</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">WEIGHT</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px; width: 140px;">MEASUREMENT</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">RATE</th>
                            <th class="p-2" style="font-family: Arial; font-size: 13px;">FREIGHT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($parcels) && $parcels->count() > 0)
                            @foreach ($parcels as $parcel)
                            <tr class="border-gray" style="border-bottom: 1px solid #cccccc;">
                                <td class="p-2 text-center" style="font-family: Arial; font-size: 13px; text-align: center;">{{$parcel->quantity}}</td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: center; width: 70px;">{{$parcel->unit}}</td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: left;">
                                    {{$parcel->itemName}}{{ !empty($parcel->desc) ? ' - '.$parcel->desc : '' }}
                                </td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; width: 60px;">
                                    @if ($parcel->weight && $parcel->weight != '0' && $parcel->weight != '0.00')
                                        {{$parcel->weight}}
                                    @endif
                                </td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px;">
                                    @if (!empty($parcel->measurements) && is_array($parcel->measurements))
                                        @foreach($parcel->measurements as $measurement)
                                            {{is_array($measurement) ? $measurement['length'] : $measurement->length}} × {{is_array($measurement) ? $measurement['width'] : $measurement->width}} × {{is_array($measurement) ? $measurement['height'] : $measurement->height}} ({{is_array($measurement) ? $measurement['quantity'] : $measurement->quantity}})<br>
                                        @endforeach
                                    @elseif (!empty($parcel->length) && !empty($parcel->width) && !empty($parcel->height) && 
                                        $parcel->length != '0' && $parcel->length != '0.00' && 
                                        $parcel->width != '0' && $parcel->width != '0.00' && 
                                        $parcel->height != '0' && $parcel->height != '0.00')
                                        {{$parcel->length}} × {{$parcel->width}} × {{$parcel->height}}
                                    @endif
                                </td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right; width: 100px;">
                                    @if (!empty($parcel->measurements) && is_array($parcel->measurements))
                                        @foreach($parcel->measurements as $measurement)
                                            {{ number_format(is_array($measurement) ? $measurement['rate'] : $measurement->rate, 2) }}<br>
                                        @endforeach
                                    @else
                                        {{ number_format($parcel->itemPrice, 2) }}
                                    @endif
                                </td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right; width: 100px;">
                                    @if (!empty($parcel->measurements) && is_array($parcel->measurements))
                                        @foreach($parcel->measurements as $measurement)
                                            {{ number_format(is_array($measurement) ? $measurement['freight'] : $measurement->freight, 2) }}<br>
                                        @endforeach
                                    @else
                                        {{ number_format($parcel->total, 2) }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="border border-gray">
                                <td class="p-2 text-center" style="font-family: Arial; font-size: 13px; text-align: center;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: left;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right;"></td>
                                <td class="p-2" style="font-family: Arial; font-size: 13px; text-align: right;"></td>
                            </tr>
                        @endif
                        <tr class="border-gray" style="border-bottom: none;">
                            <td class="p-2"></td>
                            <td class="p-2"></td>
                            <td class="p-2"></td>
                            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; height: 30px;">VALUE: {{ isset($order) ? number_format($order->value, 2) : '' }}</td>
                            <td class="p-2"></td>
                            <td class="p-2"></td>
                            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">₱</td>
                            <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">{{ isset($order) ? number_format($order->freight, 2) : '' }}</td>
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
                        <td style="width: 25%; border-bottom: 1px solid black; font-family: Arial; font-size: 12px;  text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-family: Arial; font-size: 12px;">Received on board vessel in apparent good condition.</td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">Valuation:</td>
                        <td style="border-bottom: 1px solid black; font-family: Arial; font-size: 12px;  text-align: center;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">Wharfage:</td>
                        <td style="border-bottom: 1px solid black; text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-family: Arial; font-size: 12px; border-bottom: 1px solid black;"></td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">VAT:</td>
                        <td style="border-bottom: 1px solid black; text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-family: Arial; font-size: 12px;">Vessel's Checker or Authorized Representative</td>
                        <td></td>
                        <td style="text-align: left; font-family: Arial; font-size: 12px;">Other Charges:</td>
                        <td style="border-bottom: 1px solid black; font-family: Arial; font-size: 12px; text-align: center;"></td>
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
    </div>
</x-app-layout>

<!-- For printing -->
<script>
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
<!-- For date generate and fetching of shipper and consignee -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
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
            inputField.addEventListener("input", function () {
                let query = this.value;
                if (query.length < 2) return; // Only search when typing at least 2 characters

                console.log(`Fetching customers for query: ${query}`);
                fetch(`/search-customers?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Fetched data:", data);
                        dataList.innerHTML = "";
                        data.forEach(customer => {
                            let option = document.createElement("option");
                            option.value = customer.name; // Show customer name
                            option.dataset.phone = customer.phone; // Store phone in dataset
                            option.dataset.id = customer.id || customer.sub_account_number; // Store customer ID in dataset
                            dataList.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error fetching customers:", error));
            });

            inputField.addEventListener("change", function () {
                let selectedOption = [...dataList.options].find(option => option.value === inputField.value);
                contactField.value = selectedOption ? selectedOption.dataset.phone : "";
                idField.value = selectedOption ? selectedOption.dataset.id : "";
                console.log(`Selected customer: ${inputField.value}, Contact: ${contactField.value}, ID: ${idField.value}`);
            });
        }

        // Attach event listeners for both shipper and consignee
        fetchCustomers("shipper_name", "shipperList", "shipper_contact", "shipperId");
        fetchCustomers("consignee_name", "consigneeList", "consignee_contact", 'consigneeId');
    });
</script>
<!-- For add to cart modal -->
<script>
       // Parent container where buttons are dynamically inserted
       document.getElementById("myForm").addEventListener("click", function (event) {
        // Check if the clicked element has one of the target IDs
        if (event.target.id === "eds" || event.target.id === "dels" || event.target.id === "buts") {
            event.preventDefault(); // Prevent default behavior
        }
    });
    let cart = [];
    let totalPrice = 0;
    let totalValue = 0;
    function openModal() {
        document.getElementById('addToCartModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('addToCartModal').classList.add('hidden');
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
            const price = row.children[3].textContent.trim();
            const multiplier = row.children[4].textContent.trim();
            console.log('Selected item:', itemCode, itemName, category, price, multiplier);
            // Populate the input fields
            document.getElementById('itemCode').value = itemCode;
            document.getElementById('itemName').value = itemName;
            document.getElementById('category').value = category;
            document.getElementById('price').value = price;
            document.getElementById('multiplier').value = multiplier;
        });
    });

    function addToCart() { //ADD-ME

        let selectedValue = document.getElementById("unit").value;
        let description = document.getElementById("description").value;
        let value = document.getElementById("value").value || 1;
        if (!description) {
            alert("Please enter a description before adding to cart!");
            return;
        }
        if (!selectedValue) {
            alert("Please select an option before adding to cart!");
            return;
        }

        let l = parseFloat(document.getElementById('length').value) || 1;
        let w = parseFloat(document.getElementById('width').value) || 1;
        let h = parseFloat(document.getElementById('height').value) || 1;
        let m = parseFloat(document.getElementById('multiplier').value) || 'N/A';
        let price = parseFloat(document.getElementById('price').value); // Ensure price is a number
        let quantity = parseFloat(document.getElementById('quantity').value) || 1;
        let total = 0;
        console.log('Length:', l, 'Width:', w, 'Height:', h, 'Multiplier:', m, 'Price:', price, 'Quantity:', quantity);
        if (m == 'N/A' || m == '' || m == 0) {
            total = price * quantity;
        } else {
            price = l * w * h * m;
            total = price * quantity;
        }
        totalValue += value;
        totalPrice += total;
        const item = {
            itemCode: document.getElementById('itemCode').value,
            itemName: document.getElementById('itemName').value,
            unit: document.getElementById('unit').value,
            category: document.getElementById('category').value,
            weight: document.getElementById('weight').value,
            value: value,
            length: l,
            width: w,
            height: h,
            multiplier: m,
            price: price,
            description: description,
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
    function updateMainTable() {
        const mainTableBody = document.getElementById('mainTableBody');
        mainTableBody.innerHTML = '';

        const cartDataInput = document.getElementById('cartData');
        cartDataInput.value = JSON.stringify(cart);

        document.getElementById('cartTotal').value = formatPrice(totalPrice);
        document.getElementById('valuation').value = formatPrice(totalValue);


        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = "border-b";

            row.innerHTML = `
                <td class="p-2 text-center">${item.quantity}</td>
                <td class="p-2 text-center">${item.unit}</td>
                <td class="p-2 text-center">${item.itemName} - ${item.description}</td>
                <td class="p-2 text-center">${item.value}</td>
                <td class="p-2 text-center">${item.weight}</td>`
                if (item.multiplier == 'N/A') {
                        row.innerHTML += `
                        <td class="p-2 text-center">${item.length} × ${item.width} × ${item.height} </td>
                        `
                    }
                    else{
                        row.innerHTML += `
                        <td class="p-2 text-center">${item.length} × ${item.width} × ${item.height} x ${Number(item.multiplier).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        `
                    }

                row.innerHTML += `
                    <td class="p-2 text-center">${Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="p-2 text-center">${Number(item.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="p-2 text-center">
                        <a href="#" class="text-blue-500 text-center" onclick="openEditModal(${index})">
                            <x-button id="eds" variant="warning" class="items-center max-w-xs gap-2">
                                <x-heroicon-o-pencil class="w-6 h-6" aria-hidden="true" />
                            </x-button>
                        </a>
                    </td>
                    <td class="p-2 text-center">
                        <a href="#" class="text-blue-500 text-center" onclick="deleteItem(${index})">
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
                <td class= "p-2 text-center">VALUE:${Number(totalValue).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}  </td>
                <td class="p-2"></td>
                <td class="p-2"></td>
                <td class="p-2"></td>
                <td class="p-2" style="font-family: Arial; font-weight: bold; font-size: 13px; text-align: right; height: 30px;">₱
                    ${Number(totalPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} </td>

            `;

            mainTableBody.appendChild(summaryRow);
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
        document.getElementById('quantity').value = "";
        document.getElementById('description').value = "";
    }


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
<!--For Checker Name-->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkerNames = {
        "MANILA": ["", "ALDAY", "ANCHETA", "BERNADOS", "CACHO", "ESGUERRA", "MORENO", "VICTORIANO", "YUMUL", "ZERRUDO"],
        "BATANES": ["", "SOL", "TIRSO", "VARGAS", "NICK", "JOSIE", "JEN"]
    };

    const originSelect = document.getElementById("origin");
    const checkerNameSelect = document.getElementById("checkerName");

    function updateCheckerNames() {
        const selectedOrigin = originSelect.value.toUpperCase();
        const names = checkerNames[selectedOrigin] || [];

        checkerNameSelect.innerHTML = "";

        names.forEach(name => {
            const option = document.createElement("option");
            option.value = name;
            option.textContent = name;
            checkerNameSelect.appendChild(option);
        });

        // Ensure the first option is selected
        if (names.length > 0) {
            checkerNameSelect.value = names[0];
        }

        console.log(`Origin selected: ${selectedOrigin}`);
        console.log(`Checker names:`, names);
    }

    // Run function on change
    originSelect.addEventListener("change", updateCheckerNames);

    // Retain selected checker name
    checkerNameSelect.addEventListener("change", function () {
        console.log(`Checker selected: ${checkerNameSelect.value}`);
    });

    // Run function on page load
    updateCheckerNames();
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
