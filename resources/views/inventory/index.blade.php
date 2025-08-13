<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">Inventory</h1>
                        <div class="flex gap-2">
                            <button onclick="document.getElementById('startingBalanceModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Set Starting Balance</button>
                            <button onclick="document.getElementById('inventoryModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Entry</button>
                            
                            <!-- CREATE CUSTOMER BUTTON - Only show if user has create permission -->
                            <div x-data="{ openModal: false, isSubAccount: false }">
                                @if(auth()->user()->hasPermission('customer', 'create'))
                                <button @click="openModal = true; isSubAccount = false" class="px-4 py-2 text-white bg-purple-500 rounded-md hover:bg-purple-600 focus:ring focus:ring-purple-500">
                                    + Create Customer
                                </button>
                                @endif

                                <!-- Modal -->
                                <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50" x-transition>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative">
                                        <!-- Modal Header with Close (×) Button -->
                                        <div class="flex justify-between items-center mb-4">
                                            <h2 class="text-xl font-bold text-gray-800 dark:text-white text-center w-full">
                                                <span x-show="!isSubAccount">Create Main Account</span>
                                                <span x-show="isSubAccount">Create Sub-Account</span>
                                            </h2>
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
                                        <form method="POST" action="{{ route('customers.store') }}">
                                            @csrf
                                            <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                            <input type="hidden" name="redirect_to" value="{{ route('inventory') }}">

                                            <div class="grid grid-cols-2 gap-4">
                                                <!-- First Name -->
                                                <div class="col-span-1">
                                                    <label class="block text-gray-700 dark:text-white">First Name</label>
                                                    <input type="text" name="first_name" id="first_name" 
                                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" 
                                                        oninput="updateAccountType()">
                                                </div>

                                                <!-- Last Name -->
                                                <div class="col-span-1">
                                                    <label class="block text-gray-700 dark:text-white">Last Name</label>
                                                    <input type="text" name="last_name" id="last_name" 
                                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" 
                                                        oninput="updateAccountType()">
                                                </div>
                                            </div>

                                            <!-- Company Name -->
                                            <div class="mt-2">
                                                <label class="block text-gray-700 dark:text-white">Company Name (if applicable)</label>
                                                <input type="text" name="company_name" id="company_name" 
                                                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" 
                                                    oninput="updateAccountType()">
                                            </div>

                                            <!-- Phone -->
                                            <div class="mt-2">
                                                <label class="block text-gray-700 dark:text-white">Phone Number</label>
                                                <input type="text" name="phone" id="phone"
                                                    pattern="^[0-9/\s]*$"
                                                    title="Please enter numbers only, separate multiple numbers with /"
                                                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200"
                                                    oninput="this.value = this.value.replace(/[^0-9/\s]/g, '')">
                                            </div>

                                            <div class="grid grid-cols-2 gap-4 mt-2" hidden>
                                                <!-- Share Holder -->
                                                <div class="col-span-1" hidden>
                                                    <label class="block text-gray-700 dark:text-white">Share Holder</label>
                                                    <select name="share_holder"
                                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4 mt-2" hidden>
                                                <!-- Account Type -->
                                                <div class="col-span-1" hidden>
                                                    <label class="block text-gray-700 dark:text-white">Type</label>
                                                    <input type="hidden" name="account_type" x-bind:value="isSubAccount ? 'sub' : 'main'">
                                                    <select name="type" id="type" 
                                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" readonly>
                                                        <option value="individual">Individual</option>
                                                        <option value="company">Company</option>
                                                    </select>
                                                </div>
                                                <!-- Email -->
                                                <div class="col-span-1" hidden>
                                                    <label class="block text-gray-700 dark:text-white">Email</label>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="inventoryTabs">
                            @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M'] as $idx => $item)
                                <li class="mr-2">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" id="tab{{ $idx }}" onclick="showInventoryTab('tabContent{{ $idx }}', 'tab{{ $idx }}')">
                                        {{ $item }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Tab Contents -->
                    @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M'] as $idx => $item)
                        <div id="tabContent{{ $idx }}" class="tab-content p-4 bg-white rounded-md shadow-md dark:bg-dark-eval-1" style="display: {{ $idx === 0 ? 'block' : 'none' }}; overflow-x: auto; white-space: nowrap;">
                            <h3 class="font-semibold mb-2">{{ $item }}</h3>
                            <table class="w-full border-collapse mb-4">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th colspan="10" class="border px-2 py-1 text-blue-700 font-bold text-center">{{ $item }}</th>
                                    <th colspan="6" class="border px-2 py-1 text-blue-700 font-bold text-center">ACTUAL BALANCED ONSITE</th>
                                </tr>
                            </thead>    
                            <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="border px-2 py-1">DATE</th>
                                        <th class="border px-2 py-1">CUSTOMER</th>
                                        <th class="border px-2 py-1">SHIP#</th>
                                        <th class="border px-2 py-1">VOYAGE#</th>
                                        <th class="border px-2 py-1">IN</th>
                                        <th class="border px-2 py-1">OUT</th>
                                        <th class="border px-2 py-1">BALANCE</th>
                                        <th class="border px-2 py-1">AMOUNT</th>
                                        <th class="border px-2 py-1">OR/AR</th>
                                        <th class="border px-2 py-1">DR#</th>
                                        <th class="border px-2 py-1">DATE</th>
                                        <th class="border px-2 py-1">IN</th>
                                        <th class="border px-2 py-1">ACTUAL OUT</th>
                                        <th class="border px-2 py-1">BALANCE</th>
                                        <th class="border px-2 py-1"></th>
                                        <th class="border px-2 py-1">UPDATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentMonth = null;
                                        $itemEntries = $entries->where('item', $item)->sortBy('date');
                                    @endphp
                                    @foreach($itemEntries as $entry)
                                        @php
                                            $entryMonth = \Carbon\Carbon::parse($entry->date)->format('F Y');
                                        @endphp
                                        @if($currentMonth !== $entryMonth)
                                            @php $currentMonth = $entryMonth; @endphp
                                            <tr class="bg-gray-50">
                                                <td colspan="16" class="border px-2 py-1 font-bold text-center">{{ strtoupper($entryMonth) }}</td>
                                            </tr>
                                        @endif
                                    <tr class="{{ $entry->is_starting_balance ? 'bg-yellow-50' : '' }}">
                                        <td class="border px-2 py-1">
                                            @if($entry->is_starting_balance)
                                                {{ strtoupper(\Carbon\Carbon::parse($entry->date)->format('F')) }}
                                            @else
                                                {{ \Carbon\Carbon::parse($entry->date)->format('m-d-Y') }}
                                            @endif
                                        </td>
                                        <td class="border px-2 py-1">
                                            @if($entry->is_starting_balance)
                                                <!-- Empty for starting balance -->
                                            @else
                                                {{ strtoupper($entry->customer->company_name ?: ($entry->customer->first_name . ' ' . $entry->customer->last_name)) }}
                                            @endif
                                        </td>
                                        <td class="border px-2 py-1">{{ $entry->ship_number }}</td>
                                        <td class="border px-2 py-1">{{ $entry->voyage_number }}</td>
                                        <td class="border px-2 py-1">{{ $entry->in ? number_format($entry->in, 2) : '' }}</td>
                                        <td class="border px-2 py-1">{{ $entry->out ? number_format($entry->out, 3) : '' }}</td>
                                        <td class="border px-2 py-1 font-semibold">{{ number_format($entry->balance, 2) }}</td>
                                        <td class="border px-2 py-1">{{ $entry->amount ? number_format($entry->amount, 2) : '' }}</td>
                                        <td class="border px-2 py-1">{{ $entry->or_ar }}</td>
                                        <td class="border px-2 py-1">{{ $entry->dr_number }}</td>
                                        <td class="border px-2 py-1">
                                            @if(!$entry->is_starting_balance && $entry->updated_onsite_date)
                                                {{ \Carbon\Carbon::parse($entry->updated_onsite_date)->format('m-d-Y') }}
                                            @elseif(!$entry->is_starting_balance && $entry->onsite_date)
                                                {{ \Carbon\Carbon::parse($entry->onsite_date)->format('m-d-Y') }}
                                            @endif
                                        </td>
                                        <td class="border px-2 py-1">{{ $entry->onsite_in ? number_format($entry->onsite_in, 2) : '' }}</td>
                                        <td class="border px-2 py-1">{{ $entry->actual_out ? number_format($entry->actual_out, 3) : '' }}</td>
                                        <td class="border px-2 py-1 font-semibold">{{ number_format($entry->onsite_balance, 2) }}</td>
                                        <td class="border px-2 py-1">0</td>
                                        <td class="border px-2 py-1 text-center">
                                            <button type="button" onclick="openEditModal({{ $entry->id }})" class="px-2 py-1 bg-yellow-500 text-white rounded text-xs">EDIT</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Add Entry -->
    <div id="inventoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-bold mb-4">Add Inventory Entry</h3>
            <form method="POST" action="{{ route('inventory.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Item</label>
                    <select name="item" class="w-full border rounded px-2 py-1" required>
                        @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Date</label>
                        <input type="date" name="date" class="w-full border rounded px-2 py-1" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Customer</label>
                        <select name="customer_id" class="w-full border rounded px-2 py-1" required>
                            <option value="">Select Customer or Sub Account</option>
                            <optgroup label="Main Accounts">
                                @foreach($customers as $customer)
                                    <option value="main-{{ $customer->id }}">
                                        {{ $customer->company_name ?: ($customer->first_name . ' ' . $customer->last_name) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Sub Accounts">
                                @foreach($subAccounts as $sub)
                                    <option value="sub-{{ $sub->id }}">
                                        {{ $sub->company_name ?: ($sub->first_name . ' ' . $sub->last_name) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">IN</label>
                        <input type="number" step="0.0001" name="in" class="w-full border rounded px-2 py-1" min="0" oninput="updateBalance()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">OUT</label>
                        <input type="number" step="0.0001" name="out" class="w-full border rounded px-2 py-1" min="0" oninput="updateBalance()" />
                    </div>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">BALANCE</label>
                    <input type="number" step="0.01" name="balance" class="w-full border rounded px-2 py-1 bg-gray-100" min="0" max="999999.99" readonly />
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">ONSITE BALANCE</label>
                    <input type="number" step="0.01" name="onsite_balance" class="w-full border rounded px-2 py-1 bg-gray-100" min="0" max="999999.99" readonly />
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">AMOUNT</label>
                    <input type="number" step="0.01" name="amount" class="w-full border rounded px-2 py-1" min="0" max="999999.99" />
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('inventoryModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editInventoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-bold mb-4">Edit Inventory Entry</h3>
            <form id="editInventoryForm" method="POST">
                @csrf
                @method('PUT')
                <div id="editInventoryFields"></div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('editInventoryModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Starting Balance Modal -->
    <div id="startingBalanceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-bold mb-4">Set Starting Balance</h3>
            <form method="POST" action="{{ route('inventory.set-starting-balance') }}">
                @csrf
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Item</label>
                    <select name="item" class="w-full border rounded px-2 py-1" required>
                        @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Date</label>
                        <input type="date" name="date" class="w-full border rounded px-2 py-1" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Ship Number</label>
                        <input type="text" name="ship_number" class="w-full border rounded px-2 py-1" required />
                    </div>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1">Voyage Number</label>
                    <input type="text" name="voyage_number" class="w-full border rounded px-2 py-1" required />
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Starting BALANCE</label>
                        <input type="number" step="0.01" name="balance" class="w-full border rounded px-2 py-1" min="0" max="999999.99" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Starting Onsite BALANCE</label>
                        <input type="number" step="0.01" name="onsite_balance" class="w-full border rounded px-2 py-1" min="0" max="999999.99" required />
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('startingBalanceModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Set Starting Balance</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showInventoryTab(contentId, tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(tab) {
                tab.style.display = 'none';
            });
            // Remove active class from all tabs
            document.querySelectorAll('#inventoryTabs button').forEach(function(btn) {
                btn.classList.remove('border-blue-500', 'text-blue-500');
            });
            // Show selected tab content
            document.getElementById(contentId).style.display = 'block';
            // Add active class to selected tab
            document.getElementById(tabId).classList.add('border-blue-500', 'text-blue-500');
        }
        // Set first tab as active on page load
        document.addEventListener('DOMContentLoaded', function() {
            showInventoryTab('tabContent0', 'tab0');
            // Load current balances when modal opens
            document.querySelector('button[onclick*="inventoryModal"]').addEventListener('click', function() {
                loadCurrentBalances();
            });
        });

        // Global variables to store current balances
        var currentBalances = @json($entries->groupBy('item')->map(function($items) {
            $latest = $items->sortByDesc('date')->sortByDesc('created_at')->first();
            return [
                'balance' => $latest ? $latest->balance : 0,
                'onsite_balance' => $latest ? $latest->onsite_balance : 0
            ];
        }));

        function loadCurrentBalances() {
            var itemSelect = document.querySelector('#inventoryModal select[name="item"]');
            if (itemSelect) {
                itemSelect.addEventListener('change', function() {
                    updateBalance();
                });
                // Initial load
                updateBalance();
            }
        }

        function updateBalance() {
            var itemSelect = document.querySelector('#inventoryModal select[name="item"]');
            var inInput = document.querySelector('#inventoryModal input[name="in"]');
            var outInput = document.querySelector('#inventoryModal input[name="out"]');
            var balanceInput = document.querySelector('#inventoryModal input[name="balance"]');
            var onsiteBalanceInput = document.querySelector('#inventoryModal input[name="onsite_balance"]');
            
            if (!itemSelect || !balanceInput) return;
            
            var item = itemSelect.value;
            var inValue = parseFloat(inInput.value) || 0;
            var outValue = parseFloat(outInput.value) || 0;
            var currentBalance = currentBalances[item] ? currentBalances[item].balance : 0;
            var currentOnsiteBalance = currentBalances[item] ? currentBalances[item].onsite_balance : 0;
            
            // Calculate main balance: current balance + IN - OUT
            var newBalance = currentBalance + inValue - outValue;
            balanceInput.value = newBalance.toFixed(2);
            
            // Calculate onsite balance: current onsite balance + IN - OUT (OUT goes to actual_out)
            var newOnsiteBalance = currentOnsiteBalance + inValue - outValue;
            if (onsiteBalanceInput) {
                onsiteBalanceInput.value = newOnsiteBalance.toFixed(2);
            }
        }

        function openEditModal(id) {
            var entry = @json($entries);
            var customers = @json($customers);
            var isAdmin = @json(auth()->user()->roles && in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR']));
            var found = entry.find(e => e.id === id);
            if (!found) return;
            var form = document.getElementById('editInventoryForm');
            form.action = '/inventory/' + id;
            var fields = `<div class='mb-2'>
                <label class='block text-sm font-medium mb-1'>Item</label>
                <input type='text' name='item' value='${found.item}' class='w-full border rounded px-2 py-1' required />
            </div>
            <div class='mb-2 grid grid-cols-2 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1'>Date</label>
                    <input type='date' name='date' value='${found.date}' class='w-full border rounded px-2 py-1' required />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1'>Customer</label>
                    <select name='customer_id' class='w-full border rounded px-2 py-1' required>`;
            customers.forEach(function(c) {
                var selected = c.id === found.customer_id ? 'selected' : '';
                fields += `<option value='${c.id}' ${selected}>${c.company_name ? c.company_name : (c.first_name + ' ' + c.last_name)}</option>`;
            });
            fields += `</select></div></div>`;
            fields += `<div class='mb-2'>
                <label class='block text-sm font-medium mb-1'>AMOUNT</label>
                <input type='number' step='0.01' name='amount' value='${found.amount ?? ''}' class='w-full border rounded px-2 py-1' min='0' max='999999.99' />
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-2 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1'>OR/AR</label>
                    <input type='text' name='or_ar' value='${found.or_ar ?? ''}' class='w-full border rounded px-2 py-1' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1'>DR#</label>
                    <input type='text' name='dr_number' value='${found.dr_number ?? ''}' class='w-full border rounded px-2 py-1' />
                </div>
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-3 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1'>IN</label>
                    <input type='number' step='0.0001' name='in' value='${found.in ?? ''}' class='w-full border rounded px-2 py-1' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1'>OUT ${isAdmin ? '' : '(Read-only)'}</label>
                    <input type='number' step='0.0001' name='out' value='${found.out ?? ''}' class='w-full border rounded px-2 py-1 ${isAdmin ? '' : 'bg-gray-100'}' ${isAdmin ? '' : 'readonly'} />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1'>BALANCE</label>
                    <input type='number' step='0.0001' name='balance' value='${found.balance ?? ''}' class='w-full border rounded px-2 py-1' />
                </div>
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-3 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1'>ONSITE IN</label>
                    <input type='number' step='0.0001' name='onsite_in' value='${found.onsite_in ?? ''}' class='w-full border rounded px-2 py-1' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1'>ACTUAL OUT</label>
                    <input type='number' step='0.0001' name='actual_out' value='${found.actual_out ?? ''}' class='w-full border rounded px-2 py-1' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1'>ONSITE BALANCE</label>
                    <input type='number' step='0.0001' name='onsite_balance' value='${found.onsite_balance ?? ''}' class='w-full border rounded px-2 py-1' />
                </div>
            </div>`;
            // Add Onsite Date field with permission check
            var onsiteDateReadonly = isAdmin ? '' : 'readonly';
            var onsiteDateBg = isAdmin ? '' : 'bg-gray-100';
            var currentDate = found.onsite_date || new Date().toISOString().split('T')[0];
            fields += `<div class='mb-2'>
                <label class='block text-sm font-medium mb-1'>Onsite Date ${isAdmin ? '' : '(Auto-generated)'}</label>
                <input type='date' name='onsite_date' value='${currentDate}' class='w-full border rounded px-2 py-1 ${onsiteDateBg}' ${onsiteDateReadonly} />
            </div>`;
            document.getElementById('editInventoryFields').innerHTML = fields;
            document.getElementById('editInventoryModal').classList.remove('hidden');
        }

        // Functions for Create Customer modal
        function updateAccountType() {
            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            const companyName = document.getElementById('company_name').value;
            const typeSelect = document.getElementById('type');
            
            if (companyName.trim()) {
                typeSelect.value = 'company';
            } else if (firstName.trim() || lastName.trim()) {
                typeSelect.value = 'individual';
            }
        }

        function resetFields() {
            document.getElementById('first_name').value = '';
            document.getElementById('last_name').value = '';
            document.getElementById('company_name').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('type').value = 'individual';
        }

        // Check for success message and close modal if customer was created
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success') && str_contains(session('success'), 'account created successfully'))
                // Close the modal
                const modal = document.querySelector('[x-data*="openModal"]');
                if (modal) {
                    // Trigger Alpine.js to close modal
                    modal.__x.$data.openModal = false;
                }
                
                // Optionally reload the page to refresh customer dropdowns
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            @endif
        });
    </script>
</x-app-layout>
