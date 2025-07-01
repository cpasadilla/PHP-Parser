<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight flex items-center">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 dark:text-blue-400 rounded-md hover:text-blue-700 dark:hover:text-blue-300">
                    ←
                </button>
                {{ __('Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="flex justify-center items-center p-6">
        <div class="w-full max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Details -->
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Customer Details</h3>
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <form method="POST" action="{{ route('customer.order') }}">
                        @csrf
                        <div class="grid gap-4">
                            <div>
                                <x-form.label for="customer_id" :value="__('Customer Name')" />
                                    <select id="customer_id" name="customer_id" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-300 bg-gray-100 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Customer</option>
                                        @foreach($consignee as $customer)
                                            <option value="{{ $customer->id }}">(Main) {{ $customer->first_name}} {{$customer->last_name}}</option>
                                        @endforeach
                                        @foreach($subs as $customer)
                                            @if ($customer->company_name != null)
                                                <option value="{{ $customer->sub_account_number }}">(Sub) {{ $customer->company_name}}</option>
                                            @else
                                                <option value="{{ $customer->sub_account_number }}">(Sub) {{ $customer->first_name}} {{$customer->last_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                            </div>

                            <div>
                                <x-form.label for="customer_no" :value="__('Customer Number')" />
                                <input type="text" id="customer_no" name="customer_no" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-300 bg-gray-100 dark:bg-gray-700 dark:text-white" maxlength="11" pattern="\d{1,11}" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)">
                            </div>

                            <div>
                                <x-form.label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-300 bg-gray-100 dark:bg-gray-700 dark:text-white" required>
                                    <option value="">Select Status</option>
                                    <option value="Shipper">Shipper</option>
                                    <option value="Consignee">Consignee</option>
                                </select>
                            </div>
                        </div>
                </div>

                <!-- Receiver Details -->
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Receiver Details</h3>
                    <div class="grid gap-4">
                        <div>
                            <x-form.label for="receiver_name" :value="__('Receiver Name')" />
                            <div class="flex gap-2">
                                <select id="receiver_name" name="receiver_name" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-300 bg-gray-100 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Receiver Name</option>
                                </select>
                                <button id="openReceiverModal" type="button" class="px-4 py-2 bg-indigo-500 dark:bg-indigo-600 text-white rounded-md">Select</button>
                            </div>
                        </div>

                        <div>
                            <x-form.label for="receiver_no" :value="__('Receiver Number')" />
                            <input type="text" id="receiver_no" name="receiver_no" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-300 bg-gray-100 dark:bg-gray-700 dark:text-white" maxlength="11" pattern="\d{1,11}" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)">
                        </div>
                    </div>
                </div>
            </div>
            <div id="receiverModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded-lg w-full max-w-lg dark:bg-gray-800">
                    <h2 class="text-lg font-semibold mb-4">Select Receiver</h2>

                    <!-- Search Bar -->
                    <input type="text" id="searchReceiver" placeholder="Search by name or ID..."
                        class="w-full p-2 mb-2 border rounded-md dark:bg-gray-700 dark:text-white"
                        onkeyup="filterReceivers()">

                    <!-- Receiver Table -->
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-black dark:text-white">
                                <th class="border p-2">ID</th>
                                <th class="border p-2">First Name</th>
                                <th class="border p-2">Last Name</th>
                            </tr>
                        </thead>
                        <tbody id="receiverTableBody">
                            @foreach($customers as $customer)
                            <tr class="cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-500 receiver-row"
                                onclick="selectReceiver('{{ $customer->id }}', '{{ $customer->first_name }} {{ $customer->last_name }}', '{{ $customer->phonenum }}')">
                                <td class="border p-2">{{ $customer->id }}</td>
                                <td class="border p-2">{{ $customer->first_name }}</td>
                                <td class="border p-2">{{ $customer->last_name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Controls -->
                    <div class="flex justify-between items-center mt-4">
                        <button id="prevPage" class="px-4 py-2 bg-blue-500 text-white rounded-md" onclick="changePage(-1)">Previous</button>
                        <span id="paginationInfo" class="text-sm text-gray-700 dark:text-gray-300"></span>
                        <button id="nextPage" class="px-4 py-2 bg-blue-500 text-white rounded-md" onclick="changePage(1)">Next</button>
                    </div>

                    <!-- Close Button -->
                    <div class="flex justify-end mt-4">
                        <button id="closeReceiverModal" class="px-4 py-2 bg-gray-500 text-white rounded-md">Close</button>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <x-button class="justify-center w-full gap-2" type="submit">
                    <x-heroicon-o-shopping-cart class="w-6 h-6" aria-hidden="true" />
                    <span>{{ __('Proceed') }}</span>
                </x-button>
            </div>
            </form>
        </div>
    </div>
    <script>
        let currentPage = 1;
        const rowsPerPage = 5; // Adjust for more or fewer rows
        const receiverRows = document.querySelectorAll(".receiver-row");
        const totalRows = receiverRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        function showPage(page) {
            receiverRows.forEach((row, index) => {
                row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? "" : "none";
            });

            document.getElementById("paginationInfo").innerText = `Page ${page} of ${totalPages}`;
            document.getElementById("prevPage").disabled = page === 1;
            document.getElementById("nextPage").disabled = page === totalPages;
        }

        function changePage(direction) {
            if ((direction === -1 && currentPage > 1) || (direction === 1 && currentPage < totalPages)) {
                currentPage += direction;
                showPage(currentPage);
            }
        }

        function filterReceivers() {
            const searchValue = document.getElementById("searchReceiver").value.toLowerCase();
            receiverRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchValue) ? "" : "none";
            });
        }

        // Show first page on load
        showPage(currentPage);
    </script>
    <script>
        let customerData = {
                    @foreach($subs as $sub)
                        "{{ $sub->sub_account_number }}": "{{ $sub->phone }}",
                    @endforeach
                    @foreach($consignee as $con)
                        "{{ $con->id }}": "{{ $con->phone}}",
                    @endforeach
                };
        let subAccountData = [];
        function fetchCustomerDetails(selectElement, nameFieldId, phoneFieldId) {
            let customerId = selectElement;
            if (customerId) {
                fetch(`/customer-details/${customerId}`)
                    .then(response => response.json())
                    .then(data => {
                        let nameSelect = document.getElementById(nameFieldId);
                        nameSelect.innerHTML = ""; // Clear existing options

                        if (data.error) {
                            console.error('Error:', data.error);
                            return;
                        }
                        // Populate dropdown with all names
                        data.customer_names.forEach(name => {
                            let option = document.createElement('option');
                            option.value = data.ids[name];
                            option.textContent = name;
                            nameSelect.appendChild(option);
                        });
                        subAccountData = data.sub;
                        // Set phone number
                        document.getElementById(phoneFieldId).value = data.phone || "";
                    })
                    .catch(error => console.error('Fetch error:', error));
            }
        }
        function selectReceiver(id, name, phone) {
                const receiverNameField = document.getElementById("receiver_name");
                const receiverNoField = document.getElementById("receiver_no");

                receiverNameField.innerHTML = `<option value="${id}" selected>${name}</option>`;

                fetchCustomerDetails(id, "receiver_name", "receiver_no");
                document.getElementById("receiverModal").classList.add("hidden");
            }

        function fetchNumber(selectElement, phoneFieldId) {
            let customerId = selectElement.value;
            let phoneField = document.getElementById(phoneFieldId);
            if (!phoneField) {
                console.error(`Element with ID ${phoneFieldId} not found.`);
                return;
            }
            // Check if the phone field is 'receiver_no' and use subAccount data
            if (phoneFieldId === 'receiver_no') {
                phoneField.value = customerId && subAccountData[customerId] ? subAccountData[customerId] : "N/A";
            } else {
                phoneField.value = customerId && customerData[customerId] ? customerData[customerId] : "N/A";
            }
        }
        // Attach event listeners after the page loads
        document.addEventListener("DOMContentLoaded", function() {
            //phone number for customer
            document.getElementById('customer_id').addEventListener('change', function() {
                fetchNumber(this, 'customer_no');
            });
            document.getElementById('receiver_name').addEventListener('change', function() {
                fetchNumber(this, 'receiver_no');
            });


            const openModalBtn = document.getElementById("openReceiverModal");
            const closeModalBtn = document.getElementById("closeReceiverModal");
            const modal = document.getElementById("receiverModal");

            openModalBtn.addEventListener("click", () => modal.classList.remove("hidden"));
            closeModalBtn.addEventListener("click", () => modal.classList.add("hidden"));

        });
    </script>
</x-app-layout>
