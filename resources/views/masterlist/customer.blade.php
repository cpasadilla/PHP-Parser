<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <button onclick="window.location.href='{{ route('masterlist.customer') }}';" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Master List - Customers') }}
            </h2>
        </div>
    </x-slot>

    <div class="flex items-center justify-between w-full">
        <!-- SEARCH FORM -->
        <form method="GET" class="w-full max-w-xl">
            <div class="flex">
                <input type="text" name="search" placeholder="Search by Customer ID, First Name, Last Name"
                    class="w-full px-4 py-2 border rounded-l-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white">
                <button type="submit" class="px-4 py-2 text-white bg-emerald-500 rounded-r-md hover:bg-emerald-700">
                    SEARCH
                </button>
            </div>
        </form>
    </div>
    <br>

    @if (isset($searchMessage) && request()->has('search') && request()->search != '' && $customer->count() === 0)
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
            {{ $searchMessage }}
        </div>
    @endif

    <!-- Main Accounts Table -->
    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="card-header">
            <h5 class="font-semibold">MAIN ACCOUNTS</h5>
            <br>
        </div>
        <table class="w-full border-collapse">
            <thead class="bg-gray-200 dark:bg-dark-eval-0">
                <tr>
                    <th class="p-2 text-black-700 dark:text-white-700">CUSTOMER ID</th>
                    <th class="p-2 text-black-700 dark:text-white-700">FIRST NAME</th>
                    <th class="p-2 text-black-700 dark:text-white-700">LAST NAME</th>
                    <th class="p-2 text-black-700 dark:text-white-700">COMPANY NAME</th>
                    <th class="p-2 text-black-700 dark:text-white-700">PHONE NUMBER</th>
                    <th class="p-2 text-center text-black-700 dark:text-white-700">VIEW BL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer as $customers)
                    <tr class="border-b">
                        <td class="p-2 text-center">{{ $customers->id }}</td>
                        <td class="p-2 text-left">
                            @if(!empty($customers->first_name) || !empty($customers->last_name))
                                {{ $customers->first_name }} {{ $customers->last_name }}
                            @else
                                 <!-- Leave blank -->
                            @endif
                        </td>
                        <td class="p-2 text-left">
                            @if(!empty($customers->first_name) || !empty($customers->last_name))
                                {{ $customers->last_name }}
                            @else
                                <!-- Leave blank -->
                            @endif
                        </td>
                        <td class="p-2 text-center">{{ $customers->company_name }}</td>
                        <td class="p-2 text-center">{{ $customers->phone }}</td>
                        <td class="p-2 text-center">
                            <a href="{{ route('masterlist.bl_list', ['customer_id' => $customers->id]) }}" class="text-blue-500 text-center">
                                <x-button variant="primary" class="items-center max-w-xs gap-2">
                                    <x-heroicon-o-folder class="w-6 h-6" aria-hidden="true" />
                                </x-button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $customer->links() }}
    </div>
    <br>
</x-app-layout>
<style>
    .bg-emerald-600 {
        background-color: #059669 !important; /* Emerald-600 Hex Code */
    }
</style>
