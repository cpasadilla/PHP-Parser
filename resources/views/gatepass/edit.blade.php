<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight flex items-center gap-2">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Edit Gate Pass') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- BL Information -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-gray-800 rounded-lg">
            <h3 class="text-lg font-semibold mb-3">Bill of Lading Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">BL Number</label>
                    <p class="text-lg font-semibold">{{ $gatePass->order->orderId }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Container Number</label>
                    <p class="text-lg font-semibold">{{ $gatePass->container_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipper</label>
                    <p class="text-lg font-semibold">{{ $gatePass->shipper_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Consignee</label>
                    <p class="text-lg font-semibold">{{ $gatePass->consignee_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ship Number</label>
                    <p class="text-lg font-semibold">M/V EVERWIN STAR {{ $gatePass->order->shipNum }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Voyage</label>
                    <p class="text-lg font-semibold">{{ $gatePass->order->voyageNum }}</p>
                </div>
            </div>
        </div>

        <!-- Gate Pass Form -->
        <form method="POST" action="{{ route('gatepass.update', $gatePass->id) }}" id="gatePassForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Gate Pass Number -->
                <div>
                    <label class="block text-sm font-medium mb-2">Gate Pass Number <span class="text-red-500">*</span></label>
                    <input type="text" name="gate_pass_no" value="{{ old('gate_pass_no', $gatePass->gate_pass_no) }}" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                           placeholder="Enter gate pass number" required>
                </div>

                <!-- Release Date -->
                <div>
                    <label class="block text-sm font-medium mb-2">Release Date <span class="text-red-500">*</span></label>
                    <input type="date" name="release_date" value="{{ old('release_date', $gatePass->release_date->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required>
                </div>

                <!-- Checker Name -->
                <div>
                    <label class="block text-sm font-medium mb-2">Checker Name</label>
                    <input type="text" name="checker_name" value="{{ old('checker_name', $gatePass->checker_name) }}" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                           placeholder="Name of checker">
                </div>

                <!-- Receiver Name -->
                <div>
                    <label class="block text-sm font-medium mb-2">Receiver Name</label>
                    <input type="text" name="receiver_name" value="{{ old('receiver_name', $gatePass->receiver_name) }}" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                           placeholder="Name of receiver">
                </div>

                <!-- Checker Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Checker Notes/Remarks</label>
                    <textarea name="checker_notes" rows="3" 
                              class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                              placeholder="e.g., Plate number of vehicle, special instructions, etc.">{{ old('checker_notes', $gatePass->checker_notes) }}</textarea>
                </div>
            </div>

            <!-- Items Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Items Released</h3>
                    <button type="button" onclick="addItemRow()" 
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        + Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse border border-gray-300" id="itemsTable">
                        <thead class="bg-gray-200 dark:bg-gray-700">
                            <tr>
                                <th class="p-2 border" style="width: 30%;">Item Description</th>
                                <th class="p-2 border" style="width: 15%;">Total Quantity</th>
                                <th class="p-2 border" style="width: 10%;">Unit</th>
                                <th class="p-2 border" style="width: 15%;">Released Quantity</th>
                                <th class="p-2 border" style="width: 15%;">Remaining</th>
                                <th class="p-2 border" style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            @foreach($gatePass->items as $index => $item)
                                <tr class="item-row">
                                    <td class="p-2 border">
                                        <input type="text" name="items[{{ $index }}][item_description]" 
                                               value="{{ old('items.'.$index.'.item_description', $item->item_description) }}" 
                                               class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                                               placeholder="Item name" required>
                                    </td>
                                    <td class="p-2 border">
                                        <input type="number" step="0.01" name="items[{{ $index }}][total_quantity]" 
                                               value="{{ old('items.'.$index.'.total_quantity', $item->total_quantity) }}" 
                                               class="w-full px-2 py-1 border rounded dark:bg-gray-700 total-qty" 
                                               placeholder="0" required onchange="calculateRemaining(this)">
                                    </td>
                                    <td class="p-2 border">
                                        <input type="text" name="items[{{ $index }}][unit]" 
                                               value="{{ old('items.'.$index.'.unit', $item->unit) }}" 
                                               class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                                               placeholder="e.g., sks, bxs" required>
                                    </td>
                                    <td class="p-2 border">
                                        <input type="number" step="0.01" name="items[{{ $index }}][released_quantity]" 
                                               value="{{ old('items.'.$index.'.released_quantity', $item->released_quantity) }}" 
                                               class="w-full px-2 py-1 border rounded dark:bg-gray-700 released-qty" 
                                               placeholder="0" required onchange="calculateRemaining(this)">
                                    </td>
                                    <td class="p-2 border">
                                        <input type="number" step="0.01" 
                                               value="{{ $item->total_quantity - $item->released_quantity }}" 
                                               class="w-full px-2 py-1 border rounded dark:bg-gray-700 remaining-qty bg-gray-100" 
                                               readonly>
                                    </td>
                                    <td class="p-2 border text-center">
                                        <button type="button" onclick="removeItemRow(this)" 
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Update Gate Pass
                </button>
                <a href="{{ route('gatepass.show', $gatePass->id) }}" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>

        <!-- Delete Form (separate from update form) -->
        <form method="POST" action="{{ route('gatepass.destroy', $gatePass->id) }}" class="mt-4" 
              onsubmit="return confirm('Are you sure you want to delete this gate pass?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Delete Gate Pass
            </button>
        </form>
    </div>

    <script>
        let itemRowIndex = {{ $gatePass->items->count() }};

        function addItemRow() {
            const tbody = document.getElementById('itemsTableBody');
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.innerHTML = `
                <td class="p-2 border">
                    <input type="text" name="items[${itemRowIndex}][item_description]" 
                           class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                           placeholder="Item name" required>
                </td>
                <td class="p-2 border">
                    <input type="number" step="0.01" name="items[${itemRowIndex}][total_quantity]" 
                           class="w-full px-2 py-1 border rounded dark:bg-gray-700 total-qty" 
                           placeholder="0" required onchange="calculateRemaining(this)">
                </td>
                <td class="p-2 border">
                    <input type="text" name="items[${itemRowIndex}][unit]" 
                           class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                           placeholder="e.g., sks, bxs" required>
                </td>
                <td class="p-2 border">
                    <input type="number" step="0.01" name="items[${itemRowIndex}][released_quantity]" 
                           class="w-full px-2 py-1 border rounded dark:bg-gray-700 released-qty" 
                           placeholder="0" required onchange="calculateRemaining(this)">
                </td>
                <td class="p-2 border">
                    <input type="number" step="0.01" 
                           class="w-full px-2 py-1 border rounded dark:bg-gray-700 remaining-qty bg-gray-100" 
                           readonly>
                </td>
                <td class="p-2 border text-center">
                    <button type="button" onclick="removeItemRow(this)" 
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                        Remove
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            itemRowIndex++;
        }

        function removeItemRow(button) {
            const tbody = document.getElementById('itemsTableBody');
            if (tbody.children.length > 1) {
                button.closest('tr').remove();
            } else {
                alert('At least one item is required');
            }
        }

        function calculateRemaining(input) {
            const row = input.closest('tr');
            const totalQty = parseFloat(row.querySelector('.total-qty').value) || 0;
            const releasedQty = parseFloat(row.querySelector('.released-qty').value) || 0;
            const remainingQty = totalQty - releasedQty;
            row.querySelector('.remaining-qty').value = remainingQty.toFixed(2);
            
            // Validate
            if (releasedQty > totalQty) {
                alert('Released quantity cannot exceed total quantity');
                row.querySelector('.released-qty').value = totalQty;
                row.querySelector('.remaining-qty').value = 0;
            }
        }

        // Initialize calculations on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.released-qty').forEach(input => {
                calculateRemaining(input);
            });
        });
    </script>
</x-app-layout>
