<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Voyage Status Diagnosis and Fix') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">The Problem</h3>
            <p class="mb-2">When you set a ship's status to "NEW VOYAGE" and create voyage 8-IN and 8-OUT, but then create a new order, it's still going back to voyage 7 because:</p>
            <ol class="list-decimal pl-6 mb-4">
                <li class="mb-1">The system is selecting the latest voyage based on <code>updated_at</code> timestamp, not based on voyage status.</li>
                <li class="mb-1">Even though the Ship status is set to "NEW VOYAGE", individual voyage records might still have their status as "READY".</li>
                <li class="mb-1">When creating a new order, the latest voyage with "READY" status is being chosen, regardless of number.</li>
            </ol>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">The Solution</h3>
            <p class="mb-2">This tool will ensure that:</p>
            <ol class="list-decimal pl-6 mb-4">
                <li class="mb-1">All voyages of the same number for a ship have consistent status (all READY or all STOP).</li>
                <li class="mb-1">Synchronize ship status with voyage status to prevent conflicts.</li>
            </ol>
            <div class="mt-4">
                <form action="{{ route('fix-voyage-status') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                        Fix Voyage Status Issues
                    </button>
                </form>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Diagnostic Information</h3>
            
            <div class="mb-4">
                <h4 class="font-semibold">Ship I Status: {{ $diagnosticData['shipI_status']->status ?? 'N/A' }}</h4>
                <h5 class="font-medium mt-2">Voyages:</h5>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Voyage #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Direction</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700 dark:divide-gray-600">
                            @forelse($diagnosticData['shipI_voyages'] as $voyage)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $voyage->v_num }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $voyage->inOut }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $voyage->lastStatus == 'READY' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $voyage->lastStatus }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $voyage->lastUpdated }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">No voyages found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mb-4">
                <h4 class="font-semibold">Ship II Status: {{ $diagnosticData['shipII_status']->status ?? 'N/A' }}</h4>
                <h5 class="font-medium mt-2">Voyages:</h5>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Voyage #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Direction</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700 dark:divide-gray-600">
                            @forelse($diagnosticData['shipII_voyages'] as $voyage)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $voyage->v_num }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $voyage->inOut }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $voyage->lastStatus == 'READY' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $voyage->lastStatus }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $voyage->lastUpdated }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">No voyages found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="font-semibold">Recent Orders (Ship I):</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Voyage #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700 dark:divide-gray-600">
                            @forelse($diagnosticData['orders_shipI'] as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $order->orderId }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->voyageNum }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mb-4">
                <h4 class="font-semibold">Recent Orders (Ship II):</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Voyage #</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-700 dark:divide-gray-600">
                            @forelse($diagnosticData['orders_shipII'] as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $order->orderId }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->voyageNum }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-2">Permanent Fix Recommendation</h3>
            <p class="mb-4">For a permanent solution, the code in <code>CustomerController.php</code> should be modified to prioritize voyages with a "READY" status instead of just using the latest by timestamp. This ensures new orders always go to the correct voyage.</p>
        </div>
    </div>
</x-app-layout>
