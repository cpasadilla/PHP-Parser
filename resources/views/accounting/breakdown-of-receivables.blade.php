<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Breakdown of Receivables') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Breakdown of Receivables</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">As of Date</h3>
                            <div class="mt-2">
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700" />
                            </div>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Total Receivables</h3>
                            <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">₱0.00</div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Current (0-30 days)</h3>
                            <div class="mt-2 text-2xl font-bold text-yellow-600 dark:text-yellow-400">₱0.00</div>
                        </div>
                        
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Overdue (30+ days)</h3>
                            <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">₱0.00</div>
                        </div>
                    </div>

                    <!-- Aging Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold mb-4">Aging Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="text-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Current</div>
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">₱0.00</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">1-30 Days</div>
                                <div class="text-lg font-bold text-yellow-600 dark:text-yellow-400">₱0.00</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">31-60 Days</div>
                                <div class="text-lg font-bold text-orange-600 dark:text-orange-400">₱0.00</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">61-90 Days</div>
                                <div class="text-lg font-bold text-red-600 dark:text-red-400">₱0.00</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">90+ Days</div>
                                <div class="text-lg font-bold text-red-800 dark:text-red-300">₱0.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Receivables Details -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Invoice No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Invoice Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Days Overdue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No receivables data available. Please implement data integration.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Generate Breakdown Report
                        </button>
                        <div class="flex space-x-2">
                            <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Export to Excel
                            </button>
                            <button class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                Print Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
