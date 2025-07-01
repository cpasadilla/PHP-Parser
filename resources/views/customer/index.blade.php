<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <button onclick="window.location.href='{{ route('customer.index') }}';" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Customers') }}
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

        <!-- CREATE CUSTOMER BUTTON - Only show if user has create permission -->
        <div class="ml-auto">
            <!-- Alpine.js Modal Component -->
            <div x-data="{ openModal: false, isSubAccount: false }">
                <!-- Open Modal Button - Only show if user has create permission -->
                <div class="flex justify-end mb-4 gap-4">
                    @if(auth()->user()->hasPermission('customer', 'create'))
                    <button @click="openModal = true; isSubAccount = false" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:ring focus:ring-blue-500">
                        + Create Customer
                    </button>
                    <a href="{{ route('customer.bl') }}" class="text-blue-500 text-center">
                        <x-button variant="primary" class="items-center max-w-xs gap-2">
                            <x-heroicon-o-shopping-cart class="w-6 h-6" aria-hidden="true" /> Create Order
                        </x-button>
                    </a>
                    @endif
                </div>

                <!-- Modal -->
                <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4 z-50" x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 max-w-lg relative">
                        <!-- Modal Header with Close (×) Button -->
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white text-center w-full">
                                <span x-show="!isSubAccount">Create Main Account</span>
                                <span x-show="isSubAccount">Create Sub-Account</span>
                            </h2>
                        </div>

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="list-disc list-inside text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('customers.store') }}">
                            @csrf
                            <input type="hidden" name="page" value="{{ request('page', 1) }}">

                            <div class="grid grid-cols-2 gap-4">
                                <!-- First Name -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 dark:text-white">First Name</label>
                                    <input type="text" name="first_name" id="first_name" 
                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" 
                                        oninput="updateAccountType()">
                                </div>

                                <!-- Last Name -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 dark:text-white">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" 
                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" 
                                        oninput="updateAccountType()">
                                </div>
                            </div>

                            <!-- Company Name -->
                            <div class="mt-2">
                                <label class="block text-gray-700 dark:text-white">Company Name (if applicable)</label>
                                <input type="text" name="company_name" id="company_name" 
                                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" 
                                    oninput="updateAccountType()">
                            </div>

                            <!-- Phone -->
                            <div class="mt-2">
                                <label class="block text-gray-700 dark:text-white">Phone Number</label>
                                <input type="text" name="phone" id="phone"
                                    pattern="^[0-9/\s]*$"
                                    title="Please enter numbers only, separate multiple numbers with /"
                                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200"
                                    oninput="this.value = this.value.replace(/[^0-9/\s]/g, '')">
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-2" hidden>
                                <!-- Share Holder -->
                                <div class="col-span-1" hidden>
                                    <label class="block text-gray-700 dark:text-white">Share Holder</label>
                                    <select name="share_holder"
                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-2" hidden>
                                <!-- Account Type -->
                                <div class="col-span-1" hidden>
                                    <label class="block text-gray-700 dark:text-white">Type</label>
                                    <input type="hidden" name="account_type" x-bind:value="isSubAccount ? 'sub' : 'main'">
                                    <select name="type" id="type" 
                                        class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" readonly>
                                        <option value="individual">Individual</option>
                                        <option value="company">Company</option>
                                    </select>
                                </div>
                                <!-- Email -->
                                <div class="col-span-1" hidden>
                                    <label class="block text-gray-700 dark:text-white">Email</label>
                                    <input type="text" name="email" class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                </div>
                            </div>

                            <!-- Submit & Close Buttons -->
                            <div class="flex justify-between mt-4">
                                <button type="button" @click="resetFields" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    Clear
                                </button>
                                <div class="flex space-x-2">
                                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-500 text-white rounded">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    @if (isset($searchMessage))
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
                    <th class="p-2 text-black-700 dark:text-white-700">
                        <a href="{{ route('customer', ['sort' => 'first_name', 'direction' => (request('sort') == 'first_name' && request('direction') == 'asc') ? 'desc' : 'asc', 'search' => request('search')]) }}" class="flex items-center justify-center">
                            FIRST NAME
                            @if(request('sort') == 'first_name')
                                <span class="ml-1">
                                    @if(request('direction') == 'asc')
                                        ▲
                                    @else
                                        ▼
                                    @endif
                                </span>
                            @endif
                        </a>
                    </th>
                    <th class="p-2 text-black-700 dark:text-white-700">
                        <a href="{{ route('customer', ['sort' => 'last_name', 'direction' => (request('sort') == 'last_name' && request('direction') == 'asc') ? 'desc' : 'asc', 'search' => request('search')]) }}" class="flex items-center justify-center">
                            LAST NAME
                            @if(request('sort') == 'last_name')
                                <span class="ml-1">
                                    @if(request('direction') == 'asc')
                                        ▲
                                    @else
                                        ▼
                                    @endif
                                </span>
                            @endif
                        </a>
                    </th>
                    <th class="p-2 text-black-700 dark:text-white-700">
                        <a href="{{ route('customer', ['sort' => 'company_name', 'direction' => (request('sort') == 'company_name' && request('direction') == 'asc') ? 'desc' : 'asc', 'search' => request('search')]) }}" class="flex items-center justify-center">
                            COMPANY NAME
                            @if(request('sort') == 'company_name')
                                <span class="ml-1">
                                    @if(request('direction') == 'asc')
                                        ▲
                                    @else
                                        ▼
                                    @endif
                                </span>
                            @endif
                        </a>
                    </th>
                    <th class="p-2 text-black-700 dark:text-white-700">PHONE NUMBER</th>
                    @if(auth()->user()->hasPermission('customer', 'edit'))
                    <th class="p-2 text-black-700 dark:text-white-700">EDIT</th>
                    @endif
                    @if(auth()->user()->hasPermission('customer', 'delete'))
                    <th class="p-2 text-black-700 dark:text-white-700">DELETE</th>
                    @endif
                    <th class="p-2 text-center text-black-700 dark:text-white-700">VIEW SUB ACCOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer as $customers)
                    <tr class="border-b">
                        <td class="p-2 text-center">{{ $customers->id }}</td>
                        <td class="p-2 text-left">{{ $customers->first_name }}</td>
                        <td class="p-2 text-left">{{ $customers->last_name }}</td>
                        <td class="p-2 text-center">{{ $customers->company_name }}</td>
                        <td class="p-2 text-center">{{ $customers->phone }}</td>
                        @if(auth()->user()->hasPermission('customer', 'edit'))
                        <td class="p-2 text-blue-500 cursor-pointer text-center"
                            onclick="openEditModal('{{ $customers->id }}', '{{ $customers->first_name }}', '{{ $customers->last_name }}', '{{ $customers->company_name }}', '{{ $customers->phone }}')">
                            <x-button
                                variant="warning"
                                class="items-center max-w-xs gap-2">
                                <x-heroicon-o-pencil class="w-6 h-6" aria-hidden="true" />
                            </x-button>
                        </td>
                        @endif
                        @if(auth()->user()->hasPermission('customer', 'delete'))
                        <td class="p-2 text-center">
                            <form action="/account/{{ $customers->id }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                <x-button
                                variant="danger"
                                class="items-center max-w-xs gap-2"
                                type="submit">
                                    <x-heroicon-o-trash class="w-6 h-6" aria-hidden="true" />
                                </x-button>
                            </form>
                        </td>
                        @endif
                        <td class="p-2 text-center">
                            <a href="#" class="text-blue-500 text-center" onclick="openSecond({{ $customers->id }})">
                                <x-button variant="info" class="items-center max-w-xs gap-2">
                                    <x-heroicon-o-users class="w-6 h-6" aria-hidden="true" />
                                </x-button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $customer->links() }}
    </div>

    <!-- Edit Main Account Modal -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg p-6 max-w-lg transition-all">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white text-center w-full">Edit Customer</h2>
            </div>
            
            <form method="POST" action="{{ route('customers.update') }}">
                @csrf
                <input type="hidden" id="edit_customer_id" name="id">
                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                <input type="hidden" name="search" value="{{ request('search', '') }}">

                <div class="grid grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div class="col-span-1">
                        <label for="edit_first_name" class="text-gray-700 dark:text-white">First Name</label>
                        <input type="text" id="edit_first_name" name="fname" class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                    </div>

                    <!-- Last Name -->
                    <div class="col-span-1">
                        <label for="edit_last_name" class="text-gray-700 dark:text-white">Last Name</label>
                        <input type="text" id="edit_last_name" name="lname" class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                    </div>
                </div>

                <!-- Company Name -->
                <label for="edit_company_name" class="text-gray-700 dark:text-white mt-2 block">Company Name (if applicable)</label>
                <input type="text" id="edit_company_name" name="cname" class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">

                <!-- Phone Number -->
                <label for="edit_phone" class="text-gray-700 dark:text-white mt-2 block">Phone Number</label>
                <input type="text" id="edit_phone" name="phone" 
                    pattern="^[0-9/\s]*$"
                    title="Please enter numbers only, separate multiple numbers with /"
                    oninput="this.value = this.value.replace(/[^0-9/\s]/g, '')"
                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">

                <!-- Submit & Close Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sub Account Modal -->
    <div id="subAccountModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50 ">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-gray-900 dark:text-gray-200" style="max-width: 720px;">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold dark:text-gray-100">Sub-Accounts</h2>

                <!-- Alpine.js Modal Component -->
                <div x-data="{ openSecondary: false, isSubAccount: false }">
                    <div class="flex items-center gap-4">
                        <!-- Open Modal Button - Only show if user has create permission -->
                        @if(auth()->user()->hasPermission('customer', 'create'))
                        <button @click="openSecondary = true; isSubAccount = true"
                            class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 transition">
                            + Add Account
                        </button>
                        @endif

                        <!-- Close Button -->
                        <button onclick="closeModal()"
                            class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-300 text-2xl font-semibold">
                            &times;
                        </button>
                    </div>

                    <!-- Secondary Modal -->
                    <div x-show="openSecondary" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4" x-transition>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg">
                            <!-- Modal Header -->
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-800 dark:text-white text-center w-full">Create Sub Account</h2>
                            </div>

                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-600 rounded">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Form -->
                            <form method="POST" action="{{ route('customers.store') }}">
                                @csrf
                                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                <input type="hidden" name="search" value="{{ request('search', '') }}">

                                <!-- Sub-Account Fields -->
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-gray-700 dark:text-white">Main Account ID</label>
                                        <input type="number" name="customer_id" id="main"
                                            class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200" readonly>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- First Name -->
                                        <div class="col-span-1">
                                            <label class="block text-gray-700 dark:text-white">First Name</label>
                                            <input type="text" name="sub_first_name"
                                                class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                        </div>

                                        <!-- Last Name -->
                                        <div class="col-span-1">
                                        <label class="block text-gray-700 dark:text-white">Last Name</label>
                                                <input type="text" name="sub_last_name"
                                                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 dark:text-white">Company Name (if applicable)</label>
                                        <input type="text" name="sub_company_name"
                                            class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 dark:text-white">Phone Number</label>
                                        <input type="text" name="sub_phone"
                                            pattern="^[0-9/\s]*$"
                                            title="Please enter numbers only, separate multiple numbers with /"
                                            oninput="this.value = this.value.replace(/[^0-9/\s]/g, '')"
                                            class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                                    </div>
                                </div>

                                <!-- Submit & Close Buttons -->
                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" @click="openSecondary = false"
                                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 transition">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <table class="w-full border-collapse border border-gray-300 dark:border-gray-600">
                <thead class="bg-gray-200 dark:bg-dark-eval-0">
                    <tr>
                        <th class="p-2 text-black-700 dark:text-white-700">SUB ID</th>
                        <th class="p-2 text-black-700 dark:text-white-700">MAIN CUSTOMER ID</th>
                        <th class="p-2 text-black-700 dark:text-white-700">FIRST NAME</th>
                        <th class="p-2 text-black-700 dark:text-white-700">LAST NAME</th>
                        <th class="p-2 text-black-700 dark:text-white-700">COMPANY NAME</th>
                        <th class="p-2 text-black-700 dark:text-white-700">PHONE NUMBER</th>
                        <th class="p-2 text-black-700 dark:text-white-700">ACTION</th>
                    </tr>
                </thead>
                <tbody id="subAccountList">
                    <!-- Sub-accounts will be inserted here -->

                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Sub Account Modal -->
    <div id="editSubAccountModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg p-6 max-w-lg transition-all">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white text-center w-full">Edit Sub Account</h2>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-600 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('customers.update') }}">
                @csrf
                <input type="hidden" name="sub_account_id" id="edit_sub_account_id">
                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                <input type="hidden" name="search" value="{{ request('search', '') }}">

                <div class="grid grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div class="col-span-1">
                        <label class="text-gray-700 dark:text-white">First Name</label>
                        <input type="text" name="sub_first_name" id="edit_sub_first_name"
                            class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                    </div>

                    <!-- Last Name -->
                    <div class="col-span-1">
                        <label class="text-gray-700 dark:text-white">Last Name</label>
                        <input type="text" name="sub_last_name" id="edit_sub_last_name"
                            class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">
                    </div>
                </div>

                <!-- Company Name -->
                <label class="text-gray-700 dark:text-white mt-2 block">Company Name</label>
                <input type="text" name="sub_company_name" id="edit_sub_company_name"
                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">

                <!-- Phone Number -->
                <label class="text-gray-700 dark:text-white mt-2 block">Phone Number</label>
                <input type="text" name="sub_phone" id="edit_sub_phone"
                    pattern="^[0-9/\s]*$"
                    title="Please enter numbers only, separate multiple numbers with /"
                    oninput="this.value = this.value.replace(/[^0-9/\s]/g, '')"
                    class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-blue-200">

                 <!-- Submit & Close Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeSubEditModal()"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    <br>
</x-app-layout>

<!--EDIT MAIN ACCOUNT-->
<script>
    function openEditModal(id, firstName, lastName, companyName, phone) {
        // Set the values of the input fields in the modal
        document.getElementById('edit_first_name').value = firstName;
        document.getElementById('edit_last_name').value = lastName;
        document.getElementById('edit_company_name').value = companyName;
        document.getElementById('edit_phone').value = phone;

        // Set the hidden input field for the customer ID
        document.getElementById('edit_customer_id').value = id;

        // Show the modal
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        // Hide the modal
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
<!--VIEW SUB ACCOUNT-->
<script>
    function openSubAccountForm() {
        document.getElementById('subAccountForm').classList.remove('hidden');
    }

    function closeSubAccountForm() {
        document.getElementById('subAccountForm').classList.add('hidden');
    }

    function saveSubAccount() {
        const mainAccountId = document.getElementById('mainAccountId').value;
        const firstName = document.getElementById('subFirstName').value;
        const lastName = document.getElementById('subLastName').value;
        const companyName = document.getElementById('subCompanyName').value;
        const email = document.getElementById('subEmail').value;
        const phone = document.getElementById('subPhone').value;

        // Here you would typically send an AJAX request to store the new sub-account in the database
        console.log({ mainAccountId, firstName, lastName, companyName, email, phone });

        // Close form after saving
        closeSubAccountForm();
    }
</script>
<!--EDIT SUB ACCOUNT-->
<script>
    function openSecond(customerId) {
        document.getElementById('main').value = customerId;
        fetch(`/customer/${customerId}/subaccounts`)
            .then(response => response.json())
            .then(data => {
                let subAccountList = document.getElementById('subAccountList');
                subAccountList.innerHTML = '';
                data.forEach(sub => {
                let row = `
                    <tr>
                        <td class="p-2 text-center">${sub.sub_account_number}</td>
                        <td class="p-2 text-center">${sub.customer_id}</td>
                        <td class="p-2 text-center">${sub.first_name || ''}</td>
                        <td class="p-2 text-center">${sub.last_name || ''}</td>
                        <td class="p-2 text-center">${sub.company_name || ''}</td>
                        <td class="p-2 text-center">${sub.phone || ''}</td>
                        <td class="p-2 text-center">
                            <div class="flex justify-center gap-2">`;
                
                // Only show edit button if user has edit permission
                @if(auth()->user()->hasPermission('customer', 'edit'))
                row += `
                                <button onclick="openEditSubAccountModal('${sub.id}', '${sub.first_name || ''}', '${sub.last_name || ''}', '${sub.company_name || ''}', '${sub.phone || ''}')"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
                                    <x-heroicon-o-pencil class="w-6 h-6" aria-hidden="true" />
                                </button>`;
                @endif
                
                // Only show delete button if user has delete permission and is an admin
                @if(auth()->user()->hasPermission('customer', 'delete') && Auth::user()->roles->roles == 'Admin')
                row += `
                                <form action="/sub-account/${sub.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sub-account?')">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="page" value="{{ request('page', 1) }}">
                                    <input type="hidden" name="search" value="{{ request('search', '') }}">
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        <x-heroicon-o-trash class="w-6 h-6" aria-hidden="true" />
                                    </button>
                                </form>
                            `;
                @endif

                    row += `
                            </div>
                        </td>
                    </tr>`;
                subAccountList.innerHTML += row;
            });

                document.getElementById('subAccountModal').classList.remove('hidden');
            })
            .catch(error => console.error('Error fetching sub-accounts:', error));
    }

    function openEditSubAccountModal(id, firstName, lastName, companyName, phone) {
        document.getElementById('edit_sub_account_id').value = id;
        document.getElementById('edit_sub_first_name').value = firstName;
        document.getElementById('edit_sub_last_name').value = lastName;
        document.getElementById('edit_sub_company_name').value = companyName;
        document.getElementById('edit_sub_phone').value = phone;
        document.getElementById('editSubAccountModal').classList.remove('hidden');
    }

    function closeSubEditModal() {
        document.getElementById('editSubAccountModal').classList.add('hidden');
    }

        // Function to close modal
    function closeModal() {
        document.getElementById('subAccountModal').classList.add('hidden');
    }

    function updateAccountType() {
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const companyName = document.getElementById('company_name').value.trim();
        const typeField = document.getElementById('type');

        if (companyName) {
            typeField.value = 'company';
        } else if (firstName || lastName) {
            typeField.value = 'individual';
        } else {
            typeField.value = ''; // Reset if no fields are filled
        }
    }

    function resetFields() {
        document.getElementById('first_name').value = '';
        document.getElementById('last_name').value = '';
        document.getElementById('company_name').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('type').value = '';
    }
</script>
<style>
    .bg-emerald-600 {
        background-color: #059669 !important; /* Emerald-600 Hex Code */
    }
</style>
