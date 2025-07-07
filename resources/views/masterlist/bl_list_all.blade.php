<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('All BL List') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <!-- Search and Filter Section -->
        <div class="mb-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search BL</label>
                    <input type="text" id="search" name="search" placeholder="Search by BL number, customer name, ship, voyage..."
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex gap-2">
                    <select id="ship-filter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Ships</option>
                        @foreach($ships as $ship)
                            <option value="{{ $ship->ship_number }}">{{ $ship->ship_number }}</option>
                        @endforeach
                    </select>
                    <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Status</option>
                        <option value="PAID">PAID</option>
                        <option value="UNPAID">UNPAID</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- BL List Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">BL Number</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Ship</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Voyage</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Customer</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Origin</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Destination</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Status</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Total Amount</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Created</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left text-gray-700 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            {{ $order->id }}
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            {{ $order->shipNum }}
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            {{ $order->voyageNum }}
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            @if($order->origin === 'Manila')
                                {{ $order->recName }}
                            @else
                                {{ $order->shipperName }}
                            @endif
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            {{ $order->origin }}
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            {{ $order->destination }}
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->blStatus === 'PAID' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $order->blStatus }}
                            </span>
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            ₱{{ number_format($order->totalAmount, 2) }}
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                            <div class="flex gap-2">
                                <a href="{{ route('masterlist.view-bl', ['shipNum' => $order->shipNum, 'voyageNum' => $order->voyageNum, 'orderId' => $order->id]) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                <a href="{{ route('masterlist.edit-bl', ['orderId' => $order->id]) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 text-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="border border-gray-300 dark:border-gray-600 px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No BL records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>

    <script>
        // Simple search functionality
        document.getElementById('search').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter by ship
        document.getElementById('ship-filter').addEventListener('change', function() {
            const shipValue = this.value;
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const shipCell = row.querySelector('td:nth-child(2)');
                if (shipCell) {
                    const shipText = shipCell.textContent.trim();
                    if (shipValue === '' || shipText === shipValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });

        // Filter by status
        document.getElementById('status-filter').addEventListener('change', function() {
            const statusValue = this.value;
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(7) span');
                if (statusCell) {
                    const statusText = statusCell.textContent.trim();
                    if (statusValue === '' || statusText === statusValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    </script>
</x-app-layout>
