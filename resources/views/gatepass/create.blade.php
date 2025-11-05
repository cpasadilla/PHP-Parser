<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight flex items-center gap-2">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Create Gate Pass') }}
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

        @if(!$order)
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                No BL selected. Please go back to the masterlist and select a BL to create a gate pass.
            </div>
            <a href="{{ route('masterlist') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Go to Masterlist
            </a>
        @else
            <!-- BL Information -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-gray-800 rounded-lg">
                <h3 class="text-lg font-semibold mb-3">Bill of Lading Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">BL Number</label>
                        <p class="text-lg font-semibold">{{ $order->orderId }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Container Number</label>
                        <p class="text-lg font-semibold">{{ $order->containerNum }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipper</label>
                        <p class="text-lg font-semibold">{{ $order->shipperName }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Consignee</label>
                        <p class="text-lg font-semibold">{{ $order->recName }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Origin</label>
                        <p class="text-lg font-semibold">{{ $order->origin }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Destination</label>
                        <p class="text-lg font-semibold">{{ $order->destination }}</p>
                    </div>
                </div>
            </div>

            <!-- Previous Gate Passes Summary -->
            @if($previousGatePasses->count() > 0)
                <div class="mb-6 p-4 bg-yellow-50 dark:bg-gray-800 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Previous Gate Passes for this BL</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto border-collapse border border-gray-300">
                            <thead class="bg-gray-200 dark:bg-gray-700">
                                <tr>
                                    <th class="p-2 border">Gate Pass No.</th>
                                    <th class="p-2 border">Release Date</th>
                                    <th class="p-2 border">Items Released</th>
                                    <th class="p-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previousGatePasses as $gp)
                                    <tr>
                                        <td class="p-2 border text-center">{{ $gp->gate_pass_no }}</td>
                                        <td class="p-2 border text-center">{{ \Carbon\Carbon::parse($gp->release_date)->format('M d, Y') }}</td>
                                        <td class="p-2 border">
                                            <ul class="list-disc list-inside text-sm">
                                                @foreach($gp->items as $item)
                                                    <li>{{ $item->released_quantity }} {{ $item->unit }} of {{ $item->item_description }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="p-2 border text-center">
                                            <a href="{{ route('gatepass.show', $gp->id) }}" 
                                               class="text-blue-600 hover:underline text-sm"
                                               target="_blank">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('gatepass.summary', ['order_id' => $order->id]) }}" 
                           class="text-blue-600 hover:underline font-semibold"
                           target="_blank">
                            View Complete Release Summary →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Gate Pass Form -->
            <form method="POST" action="{{ route('gatepass.store') }}" id="gatePassForm">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="container_number" value="{{ $order->containerNum }}">
                <input type="hidden" name="shipper_name" value="{{ $order->shipperName }}">
                <input type="hidden" name="consignee_name" value="{{ $order->recName }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Gate Pass Number -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Gate Pass Number <span class="text-red-500">*</span></label>
                        <input type="text" name="gate_pass_no" value="{{ old('gate_pass_no') }}" 
                               class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                               placeholder="Enter gate pass number" required>
                        <p class="text-xs text-gray-500 mt-1">Enter the gate pass number manually</p>
                    </div>

                    <!-- Release Date -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Release Date <span class="text-red-500">*</span></label>
                        <input type="date" name="release_date" value="{{ old('release_date', date('Y-m-d')) }}" 
                               class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" required>
                    </div>

                    <!-- Checker Name -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Checker Name <span class="text-red-500">*</span></label>
                        <input type="text" name="checker_name" value="{{ old('checker_name') }}" 
                               class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                               placeholder="Name of checker" required>
                    </div>

                    <!-- Receiver Name -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Receiver Name <span class="text-red-500">*</span></label>
                        <input type="text" name="receiver_name" value="{{ old('receiver_name') }}" 
                               class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                               placeholder="Name of receiver" required>
                    </div>

                    <!-- Checker Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Checker Notes/Remarks</label>
                        <textarea name="checker_notes" rows="3" 
                                  class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                                  placeholder="e.g., Plate number of vehicle, special instructions, etc.">{{ old('checker_notes') }}</textarea>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Items to Release</h3>
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
                                @if($order->parcels->count() > 0)
                                    @foreach($order->parcels as $index => $parcel)
                                        <tr class="item-row">
                                            <td class="p-2 border">
                                                <input type="text" name="items[{{ $index }}][item_description]" 
                                                       value="{{ $parcel->itemName }}" 
                                                       class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                                                       placeholder="Item name" required>
                                            </td>
                                            <td class="p-2 border">
                                                <input type="number" step="0.01" name="items[{{ $index }}][total_quantity]" 
                                                       value="{{ $parcel->quantity }}" 
                                                       class="w-full px-2 py-1 border rounded dark:bg-gray-700 total-qty" 
                                                       placeholder="0" required onchange="calculateRemaining(this)">
                                            </td>
                                            <td class="p-2 border">
                                                <input type="text" name="items[{{ $index }}][unit]" 
                                                       value="{{ $parcel->unit }}" 
                                                       class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                                                       placeholder="e.g., sks, bxs" required>
                                            </td>
                                            <td class="p-2 border">
                                                <input type="number" step="0.01" name="items[{{ $index }}][released_quantity]" 
                                                       value="0" 
                                                       class="w-full px-2 py-1 border rounded dark:bg-gray-700 released-qty" 
                                                       placeholder="0" required onchange="calculateRemaining(this)">
                                            </td>
                                            <td class="p-2 border">
                                                <input type="number" step="0.01" 
                                                       value="{{ $parcel->quantity }}" 
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
                                @else
                                    <tr class="item-row">
                                        <td class="p-2 border">
                                            <input type="text" name="items[0][item_description]" 
                                                   class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                                                   placeholder="Item name" required>
                                        </td>
                                        <td class="p-2 border">
                                            <input type="number" step="0.01" name="items[0][total_quantity]" 
                                                   class="w-full px-2 py-1 border rounded dark:bg-gray-700 total-qty" 
                                                   placeholder="0" required onchange="calculateRemaining(this)">
                                        </td>
                                        <td class="p-2 border">
                                            <input type="text" name="items[0][unit]" 
                                                   class="w-full px-2 py-1 border rounded dark:bg-gray-700" 
                                                   placeholder="e.g., sks, bxs" required>
                                        </td>
                                        <td class="p-2 border">
                                            <input type="number" step="0.01" name="items[0][released_quantity]" 
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
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create Gate Pass
                    </button>
                    <a href="{{ route('masterlist') }}" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        @endif
    </div>

    <script>
        let itemRowIndex = {{ $order ? $order->parcels->count() : 1 }};

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
