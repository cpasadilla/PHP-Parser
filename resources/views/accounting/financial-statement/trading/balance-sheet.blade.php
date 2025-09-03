<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Balance Sheet - Trading') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Balance Sheet - Trading</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">As of Date</h3>
                            <div class="mt-2">
                                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700" />
                            </div>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Total Assets</h3>
                            <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">₱0.00</div>
                        </div>
                        
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-purple-800 dark:text-purple-200">Total Liabilities & Equity</h3>
                            <div class="mt-2 text-2xl font-bold text-purple-600 dark:text-purple-400">₱0.00</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Assets -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">ASSETS</h3>
                            <div class="space-y-2">
                                <div class="font-medium">Current Assets</div>
                                <div class="pl-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex justify-between">
                                        <span>Cash and Cash Equivalents</span>
                                        <span>₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Accounts Receivable</span>
                                        <span>₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Inventory</span>
                                        <span>₱0.00</span>
                                    </div>
                                </div>
                                <div class="font-medium border-t pt-2">Non-Current Assets</div>
                                <div class="pl-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex justify-between">
                                        <span>Property, Plant & Equipment</span>
                                        <span>₱0.00</span>
                                    </div>
                                </div>
                                <div class="font-bold border-t pt-2 flex justify-between">
                                    <span>TOTAL ASSETS</span>
                                    <span>₱0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Liabilities & Equity -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">LIABILITIES & EQUITY</h3>
                            <div class="space-y-2">
                                <div class="font-medium">Current Liabilities</div>
                                <div class="pl-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex justify-between">
                                        <span>Accounts Payable</span>
                                        <span>₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Accrued Liabilities</span>
                                        <span>₱0.00</span>
                                    </div>
                                </div>
                                <div class="font-medium border-t pt-2">Equity</div>
                                <div class="pl-4 text-sm text-gray-600 dark:text-gray-400">
                                    <div class="flex justify-between">
                                        <span>Capital</span>
                                        <span>₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Retained Earnings</span>
                                        <span>₱0.00</span>
                                    </div>
                                </div>
                                <div class="font-bold border-t pt-2 flex justify-between">
                                    <span>TOTAL LIABILITIES & EQUITY</span>
                                    <span>₱0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Generate Balance Sheet
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
