@section('scripts')
<script src="{{ asset('js/interest-reset.js') }}"></script>
@endsection

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Reset Interest Activation') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="max-w-xl mx-auto">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Interest Activation Reset Options
            </h3>

            <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-md mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-100">
                            Important Note
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-200">                            <p>
                                This page allows you to reset the interest activation status. After reset, the "Activate 1% Interest" 
                                button will be visible again in the SOA list page. Note that this will remove any previously 
                                calculated interest.
                            </p>
                            <p class="mt-2">
                                You can also activate or deactivate interest on each individual voyage directly from the SOA list page using the 
                                "Activate 1% Interest" and "Deactivate" buttons.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 p-4 border border-gray-200 rounded-md dark:border-gray-700">
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                    Reset Browser Storage
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    This clears the browser's localStorage entries that track interest activation status.
                </p>
                <button onclick="clearInterestActivation()" 
                    class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-700">
                    Reset Browser Storage
                </button>
            </div>

            <div class="mb-6 p-4 border border-gray-200 rounded-md dark:border-gray-700">
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                    Reset Database Records
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    This clears the interest_start_date field in the orders table. Please contact your system administrator.
                </p>
                <p class="text-sm italic text-gray-500 dark:text-gray-400">
                    Command to run: <code class="bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded">php artisan interest:reset</code>
                </p>
            </div>

            <div class="mt-8 flex justify-end">
                <button onclick="window.history.back();" 
                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">
                    Return to Previous Page
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
