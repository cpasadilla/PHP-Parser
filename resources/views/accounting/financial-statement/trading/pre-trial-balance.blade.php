<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Financial Statement Trading - Pre-Trial Balance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Pre-Trial Balance - Trading</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Report Period</h3>
                            <div class="mt-2 space-y-2">
                                <input type="date" placeholder="Start Date" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700" />
                                <input type="date" placeholder="End Date" class="w-full px-3 py-2 border border-gray-300 rounded-md dark:border-gray-600 dark:bg-gray-700" />
                            </div>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Total Debits</h3>
                            <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">₱0.00</div>
                        </div>
                        
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Total Credits</h3>
                            <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">₱0.00</div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account Name</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Debit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Credit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td colspan="5" class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100">ASSETS</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No account data available. Please configure chart of accounts.
                                    </td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td colspan="5" class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100">LIABILITIES</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td colspan="5" class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100">EQUITY</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td colspan="5" class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100">REVENUE</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td colspan="5" class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100">EXPENSES</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Generate Pre-Trial Balance
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
