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
                        <button onclick="document.getElementById('inventoryModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Entry</button>
                    </div>
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="inventoryTabs">
                            @foreach(['G1 DAMORTIS','G1 CURRIMAO','GRAVEL 34','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M'] as $idx => $item)
                                <li class="mr-2">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" id="tab{{ $idx }}" onclick="showInventoryTab('tabContent{{ $idx }}', 'tab{{ $idx }}')">
                                        {{ $item }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Tab Contents -->
                    @foreach(['G1 DAMORTIS','G1 CURRIMAO','GRAVEL 34','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M'] as $idx => $item)
                        <div id="tabContent{{ $idx }}" class="tab-content p-4 bg-white rounded-md shadow-md dark:bg-dark-eval-1" style="display: {{ $idx === 0 ? 'block' : 'none' }}; overflow-x: auto; white-space: nowrap;">
                            <h3 class="font-semibold mb-2">{{ $item }}</h3>
                            <table class="w-full border-collapse mb-4">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th colspan="8" class="border px-2 py-1 text-blue-700 font-bold text-center">{{ $item }}</th>
                                    <th colspan="6" class="border px-2 py-1 text-blue-700 font-bold text-center">ACTUAL BALANCED ONSITE</th>
                                </tr>
                            </thead>    
                            <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="border px-2 py-1">DATE</th>
                                        <th class="border px-2 py-1">CUSTOMER</th>
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
                                    @foreach($entries->where('item', $item) as $entry)
                                    <tr>
                                        <td class="border px-2 py-1">{{ $entry->date }}</td>
                                        <td class="border px-2 py-1">{{ $entry->customer->company_name ?: ($entry->customer->first_name . ' ' . $entry->customer->last_name) }}</td>
                                        <td class="border px-2 py-1">{{ $entry->in }}</td>
                                        <td class="border px-2 py-1">{{ number_format($entry->out, 3) }}</td>
                                        <td class="border px-2 py-1">{{ number_format($entry->balance, 2) }}</td>
                                        <td class="border px-2 py-1">{{ number_format($entry->amount, 2) }}</td>
                                        <td class="border px-2 py-1">{{ $entry->or_ar }}</td>
                                        <td class="border px-2 py-1">{{ $entry->dr_number }}</td>
                                        <td class="border px-2 py-1">{{ $entry->updated_onsite_date ?? $entry->onsite_date }}</td>
                                        <td class="border px-2 py-1">{{ $entry->onsite_in }}</td>
                                        <td class="border px-2 py-1">{{ number_format($entry->actual_out, 3) }}</td>
                                        <td class="border px-2 py-1">{{ number_format($entry->onsite_balance, 2) }}</td>
                                        <td class="border px-2 py-1">0</td>
                                        <td class="border px-2 py-1 text-center">
                                            <button type="button" onclick="openEditModal({{ $entry->id }})" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</button>
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
                        @foreach(['G1 DAMORTIS','G1 CURRIMAO','GRAVEL 34','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M'] as $item)
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
                <div class="mb-2 grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">IN</label>
                            <input type="number" step="1" name="in" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">OUT</label>
                            <input type="number" step="0.001" name="out" class="w-full border rounded px-2 py-1" min="0" max="999999.999" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">BALANCE</label>
                            <input type="number" step="0.01" name="balance" class="w-full border rounded px-2 py-1" min="0" max="999999.99" />
                    </div>
                </div>
                <div class="mb-2 grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">AMOUNT</label>
                            <input type="number" step="0.01" name="amount" class="w-full border rounded px-2 py-1" min="0" max="999999.99" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">OR/AR</label>
                        <input type="number" step="1" name="or_ar" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">DR#</label>
                        <input type="number" step="1" name="dr_number" class="w-full border rounded px-2 py-1" />
                    </div>
                </div>
                <div class="mb-2 grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">IN</label>
                        <input type="number" step="1" name="onsite_in" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">ACTUAL OUT</label>
                        <input type="number" step="0.001" name="actual_out" class="w-full border rounded px-2 py-1" min="0" max="999999.999" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">BALANCE</label>
                        <input type="number" step="0.01" name="onsite_balance" class="w-full border rounded px-2 py-1" min="0" max="999999.99" />
                    </div>
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
        });

        function openEditModal(id) {
            var entry = @json($entries);
            var customers = @json($customers);
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
            fields += `<div class='mb-2 grid grid-cols-3 gap-2'>
                <div><label class='block text-sm font-medium mb-1'>IN</label><input type='number' step='0.0001' name='in' value='${found.in ?? ''}' class='w-full border rounded px-2 py-1' /></div>
                <div><label class='block text-sm font-medium mb-1'>OUT</label><input type='number' step='0.0001' name='out' value='${found.out ?? ''}' class='w-full border rounded px-2 py-1' /></div>
                <div><label class='block text-sm font-medium mb-1'>BALANCE</label><input type='number' step='0.0001' name='balance' value='${found.balance ?? ''}' class='w-full border rounded px-2 py-1' /></div>
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-3 gap-2'>
                <div><label class='block text-sm font-medium mb-1'>AMOUNT</label><input type='number' step='0.0001' name='amount' value='${found.amount ?? ''}' class='w-full border rounded px-2 py-1' /></div>
                <div><label class='block text-sm font-medium mb-1'>OR/AR</label><input type='text' name='or_ar' value='${found.or_ar ?? ''}' class='w-full border rounded px-2 py-1' /></div>
                <div><label class='block text-sm font-medium mb-1'>DR#</label><input type='text' name='dr_number' value='${found.dr_number ?? ''}' class='w-full border rounded px-2 py-1' /></div>
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-3 gap-2'>
                <div><label class='block text-sm font-medium mb-1'>IN</label><input type='number' step='0.0001' name='onsite_in' value='${found.onsite_in ?? ''}' class='w-full border rounded px-2 py-1' /></div>
                <div><label class='block text-sm font-medium mb-1'>ACTUAL OUT</label><input type='number' step='0.0001' name='actual_out' value='${found.actual_out ?? ''}' class='w-full border rounded px-2 py-1' /></div>
                <div><label class='block text-sm font-medium mb-1'>BALANCE</label><input type='number' step='0.0001' name='onsite_balance' value='${found.onsite_balance ?? ''}' class='w-full border rounded px-2 py-1' /></div>
            </div>`;
            document.getElementById('editInventoryFields').innerHTML = fields;
            document.getElementById('editInventoryModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
