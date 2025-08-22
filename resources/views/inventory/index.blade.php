<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 gap-4">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Inventory</h1>
                        
                        <!-- Button Groups Container -->
                        <div class="flex flex-col sm:flex-row gap-3 lg:flex-shrink-0">
                            <!-- Export Group -->
                            <div class="flex gap-2">
                                <button id="exportExcel" class="flex items-center gap-2 px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-800 transition-colors duration-200 shadow-sm dark:shadow-gray-900/25">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                    </svg>
                                    Export Excel
                                </button>
                                <button id="exportPdf" class="flex items-center gap-2 px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-800 transition-colors duration-200 shadow-sm dark:shadow-gray-900/25">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                    </svg>
                                    Export PDF
                                </button>
                            </div>
                            
                            <!-- Action Group -->
                            <div class="flex gap-2">
                                @if(Auth::user()->hasPermission('inventory','create'))
                                <button onclick="document.getElementById('startingBalanceModal').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700 transition-colors duration-200 shadow-sm dark:shadow-gray-900/25 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                    Set Balance
                                </button>
                                @endif
                                @if(Auth::user()->hasPermission('inventory','create'))
                                <button onclick="document.getElementById('inventoryModal').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 transition-colors duration-200 shadow-sm dark:shadow-gray-900/25">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Add Entry
                                </button>
                                @endif
                            </div>
                            
                            <!-- Customer Group -->
                            @if(Auth::user()->hasPagePermission('customer') && Auth::user()->hasPermission('customer','create'))
                            <div x-data="{ openModal: false, isSubAccount: false }">
                                <button @click="openModal = true; isSubAccount = false" class="flex items-center gap-2 px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                    </svg>
                                    Create Customer
                                </button>

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
                                            <div class="alert alert-danger mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                                                <ul class="list-disc list-inside text-red-600 dark:text-red-400">
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
                                            <input type="hidden" name="redirect_to" value="{{ route('inventory') }}">

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
                                                <button type="button" @click="resetFields" class="px-4 py-2 bg-red-500 dark:bg-red-600 text-white rounded hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-200">
                                                    Clear
                                                </button>
                                                <div class="flex space-x-2">
                                                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors duration-200">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="px-4 py-2 text-white bg-blue-500 dark:bg-blue-600 rounded-md hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors duration-200">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="inventoryTabs">
                            @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M','HOLLOWBLOCKS'] as $idx => $item)
                                <li class="mr-2">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" id="tab{{ $idx }}" onclick="showInventoryTab('tabContent{{ $idx }}', 'tab{{ $idx }}')">
                                        {{ $item }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Tab Contents -->
                    @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M','HOLLOWBLOCKS'] as $idx => $item)
                        <div id="tabContent{{ $idx }}" class="tab-content p-4 bg-white rounded-md shadow-md dark:bg-gray-800 dark:shadow-gray-900/25" style="display: {{ $idx === 0 ? 'block' : 'none' }}; overflow-x: auto; white-space: nowrap;">
                            <h3 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ $item }}</h3>
                            <div class="flex flex-wrap items-end gap-3 mb-3">
                                <div>
                                    <label for="monthPicker{{ $idx }}" class="block text-xs text-gray-600 dark:text-gray-300">Select month</label>
                                    <input type="month" id="monthPicker{{ $idx }}" class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                                </div>
                                <button id="exportPdfMonth{{ $idx }}" data-tab-index="{{ $idx }}" class="inline-flex items-center gap-2 px-3 py-1 text-sm text-white bg-red-600 rounded-md hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 transition-colors duration-200 shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    Export PDF (Month)
                                </button>
                            </div>
                            <table class="w-full border-collapse mb-4 bg-white dark:bg-gray-800">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th colspan="10" class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-blue-700 dark:text-blue-400 font-bold text-center">{{ $item }}</th>
                                    <th colspan="6" class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-blue-700 dark:text-blue-400 font-bold text-center">ACTUAL BALANCED ONSITE</th>
                                </tr>
                            </thead>    
                            <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">DATE</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">CUSTOMER</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">SHIP#</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">VOYAGE#</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">IN</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">OUT</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">BALANCE</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">AMOUNT</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">OR/AR</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">DR#</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">DATE</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">IN</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">ACTUAL OUT</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">BALANCE</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">UPDATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentMonth = null;
                                        $itemEntries = $entries->where('item', $item)->sortBy('date');
                                    @endphp
                                    @foreach($itemEntries as $entry)
                                        @php
                                            $entryMonth = \Carbon\Carbon::parse($entry->date)->format('F Y');
                                        @endphp
                                        @if($currentMonth !== $entryMonth)
                                            @php $currentMonth = $entryMonth; @endphp
                                            <tr class="bg-gray-50 dark:bg-gray-700">
                                                <td colspan="16" class="border border-gray-300 dark:border-gray-600 px-2 py-1 font-bold text-center text-gray-900 dark:text-gray-100">{{ strtoupper($entryMonth) }}</td>
                                            </tr>
                                        @endif
                                    <tr class="{{ $entry->is_starting_balance ? 'bg-yellow-50 dark:bg-yellow-900/30' : 'bg-white dark:bg-gray-800' }}">
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">
                                            @if($entry->is_starting_balance)
                                                {{ strtoupper(\Carbon\Carbon::parse($entry->date)->format('F')) }}
                                            @else
                                                {{ \Carbon\Carbon::parse($entry->date)->format('m-d-Y') }}
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">
                                            @if($entry->is_starting_balance)
                                                <!-- Empty for starting balance -->
                                            @else
                                                {{ strtoupper($entry->customer->company_name ?: ($entry->customer->first_name . ' ' . $entry->customer->last_name)) }}
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->ship_number }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->voyage_number }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->in ? number_format($entry->in, 2) : '' }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->out ? number_format($entry->out, 3) : '' }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 font-semibold text-gray-900 dark:text-gray-100">{{ number_format($entry->balance, 2) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->amount ? number_format($entry->amount, 2) : '' }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->or_ar }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->dr_number }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">
                                            @if(!$entry->is_starting_balance && $entry->updated_onsite_date)
                                                {{ \Carbon\Carbon::parse($entry->updated_onsite_date)->format('m-d-Y') }}
                                            @elseif(!$entry->is_starting_balance && $entry->onsite_date)
                                                {{ \Carbon\Carbon::parse($entry->onsite_date)->format('m-d-Y') }}
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->onsite_in ? number_format($entry->onsite_in, 2) : '' }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">{{ $entry->actual_out ? number_format($entry->actual_out, 3) : '' }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 font-semibold text-gray-900 dark:text-gray-100">{{ $entry->onsite_balance !== null ? number_format($entry->onsite_balance, 2) : '' }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100">
                                            @if($entry->out && $entry->actual_out)
                                                {{ number_format($entry->out - $entry->actual_out, 3) }}
                                            @elseif($entry->out && !$entry->actual_out)
                                                {{ number_format($entry->out, 3) }}
                                            @else
                                                
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center">
                                            <div class="flex gap-1 justify-center">
                                                @if(Auth::user()->hasPermission('inventory','edit'))
                                                <button type="button" onclick="openEditModal({{ $entry->id }})" class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700 transition-colors duration-200">EDIT</button>
                                                @endif
                                                @if(Auth::user()->hasPermission('inventory','delete'))
                                                <button type="button" onclick="confirmDelete({{ $entry->id }})" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 transition-colors duration-200">DELETE</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($itemEntries->count())
                                        @php $finalEntry = $itemEntries->last(); @endphp
                                        <tr class="bg-blue-50 dark:bg-gray-700 font-semibold">
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- DATE -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- CUSTOMER -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- SHIP# -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- VOYAGE# -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- IN -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- OUT -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 font-bold text-gray-900 dark:text-gray-100">{{ number_format($finalEntry->balance, 2) }}</td> <!-- BALANCE -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- AMOUNT -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- OR/AR -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- DR# -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- ONSITE DATE -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- ONSITE IN -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- ACTUAL OUT -->
                                            @php
                                                // Find the latest (chronologically last in current ordering) entry that has a non-null onsite_balance
                                                $latestWithOnsite = $itemEntries->filter(function($e){ return $e->onsite_balance !== null; })->last();
                                                if($latestWithOnsite) {
                                                    $displayOnsiteBalance = number_format($latestWithOnsite->onsite_balance, 2);
                                                } else {
                                                    $firstEntry = $itemEntries->first();
                                                    $displayOnsiteBalance = $firstEntry ? number_format($firstEntry->balance, 2) : '';
                                                }
                                            @endphp
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 font-bold text-gray-900 dark:text-gray-100">{{ $displayOnsiteBalance }}</td> <!-- ONSITE BALANCE (latest non-null or first entry balance) -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-gray-900 dark:text-gray-100"></td> <!-- DIFF -->
                                            <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-gray-900 dark:text-gray-100"></td> <!-- UPDATE -->
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Add Entry -->
    @if(Auth::user()->hasPermission('inventory','create'))
    <div id="inventoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Add Inventory Entry</h3>
            <form method="POST" action="{{ route('inventory.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Item</label>
                    <select name="item" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" required onchange="updateBalance()">
                        @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M','HOLLOWBLOCKS'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Date</label>
                        <input type="date" name="date" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Customer</label>
                        <select name="customer_id" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" required>
                            <option value="">Select Customer or Sub Account</option>
                            <optgroup label="Main Accounts">
                                @foreach($customers as $customer)
                                    <option value="main-{{ $customer->id }}">
                                        {{ $customer->company_name ?: ($customer->first_name . ' ' . $customer->last_name) }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Sub Accounts">
                                @foreach($subAccounts as $sub)
                                    <option value="sub-{{ $sub->id }}">
                                        {{ $sub->company_name ?: ($sub->first_name . ' ' . $sub->last_name) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">IN</label>
                        <input type="number" step="0.0001" name="in" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" min="0" oninput="updateBalance()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">OUT</label>
                        <input type="number" step="0.0001" name="out" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" min="0" oninput="updateBalance()" />
                    </div>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">BALANCE</label>
                    <input type="number" step="0.01" name="balance" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100" min="0" max="999999.99" readonly />
                </div>
                
                <!-- Amount Calculation Fields -->
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Pickup/Delivery Type</label>
                    <select name="pickup_delivery_type" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" onchange="updateAmountCalculation()">
                        <option value="">Select Type</option>
                        <option value="pickup_pier">Pick up from Pier</option>
                        <option value="pickup_stockpile_delivered_pier">Pick up from Stock Pile & Delivered from Pier</option>
                        <option value="delivered_stockpile">Delivered from Stock Pile</option>
                        <option value="per_bag">Per Bag (Manual Amount)</option>
                    </select>
                </div>
                
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">VAT Type</label>
                        <select name="vat_type" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" onchange="updateAmountCalculation()">
                            <option value="">Select VAT</option>
                            <option value="with_vat">With VAT</option>
                            <option value="without_vat">Without VAT</option>
                        </select>
                    </div>
                    <div id="hollowblockSizeField" style="display: none;">
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Hollowblock Size</label>
                        <select name="hollowblock_size" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" onchange="updateAmountCalculation()">
                            <option value="">Select Size</option>
                            <option value="4_inch">4 inches</option>
                            <option value="5_inch">5 inches</option>
                            <option value="6_inch">6 inches</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="flex items-center justify-between">
                        <label id="amountLabel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">AMOUNT (Auto-calculated)</label>
                        <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                            <input type="checkbox" id="amountManualToggle" name="is_amount_manual" value="1" onchange="handleAmountManualToggle()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            Manual amount
                        </label>
                    </div>
                    <input type="number" step="0.01" name="amount" id="amountInput" class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100" min="0" max="999999.99" readonly />
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('inventoryModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors duration-200">Save</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    @if(Auth::user()->hasPermission('inventory','edit'))
    <div id="editInventoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Edit Inventory Entry</h3>
            <form id="editInventoryForm" method="POST">
                @csrf
                @method('PUT')
                <div id="editInventoryFields"></div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('editInventoryModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors duration-200">Save</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Starting Balance Modal -->
    @if(Auth::user()->hasPermission('inventory','create'))
    <div id="startingBalanceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Set Starting Balance</h3>
            <form method="POST" action="{{ route('inventory.set-starting-balance') }}">
                @csrf
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Item</label>
                    <select name="item" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" required>
                        @foreach(['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M','HOLLOWBLOCKS'] as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Date</label>
                        <input type="date" name="date" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Ship Number</label>
                        <input type="text" name="ship_number" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" required />
                    </div>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Voyage Number</label>
                    <input type="text" name="voyage_number" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" required />
                </div>
                <div class="mb-2 grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Starting BALANCE</label>
                        <input type="number" step="0.01" name="balance" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" min="0" max="999999.99" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Starting Onsite BALANCE</label>
                        <input type="number" step="0.01" name="onsite_balance" class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" min="0" max="999999.99" required />
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="document.getElementById('startingBalanceModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-700 transition-colors duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 dark:bg-green-700 text-white rounded hover:bg-green-700 dark:hover:bg-green-800 transition-colors duration-200">Set Starting Balance</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        function showInventoryTab(contentId, tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(tab) {
                tab.style.display = 'none';
            });
            // Remove active class from all tabs
            document.querySelectorAll('#inventoryTabs button').forEach(function(btn) {
                btn.classList.remove('border-blue-500', 'text-blue-500');
            });
            // Show selected tab content
            document.getElementById(contentId).style.display = 'block';
            // Add active class to selected tab
            document.getElementById(tabId).classList.add('border-blue-500', 'text-blue-500');
        }
        // Set first tab as active on page load
        document.addEventListener('DOMContentLoaded', function() {
            showInventoryTab('tabContent0', 'tab0');
            // Load current balances when modal opens
            document.querySelector('button[onclick*="inventoryModal"]').addEventListener('click', function() {
                loadCurrentBalances();
            });
        });

        function handleAmountManualToggle() {
            const manualToggle = document.getElementById('amountManualToggle');
            const amountInput = document.getElementById('amountInput');
            const label = document.getElementById('amountLabel');
            if (manualToggle && amountInput) {
                if (manualToggle.checked) {
                    amountInput.readOnly = false;
                    amountInput.classList.remove('bg-gray-100', 'dark:bg-gray-600');
                    amountInput.classList.add('bg-white', 'dark:bg-gray-700');
                    if (label) label.textContent = 'AMOUNT (Manual Entry)';
                } else {
                    amountInput.readOnly = true;
                    amountInput.classList.remove('bg-white', 'dark:bg-gray-700');
                    amountInput.classList.add('bg-gray-100', 'dark:bg-gray-600');
                    if (label) label.textContent = 'AMOUNT (Auto-calculated)';
                    updateAmountCalculation();
                }
            }
        }

        function handleEditAmountManualToggle() {
            const manualToggle = document.getElementById('editAmountManualToggle');
            const amountInput = document.getElementById('editAmountInput');
            const label = document.getElementById('editAmountLabel');
            if (manualToggle && amountInput) {
                if (manualToggle.checked) {
                    amountInput.readOnly = false;
                    amountInput.classList.remove('bg-gray-100', 'dark:bg-gray-600');
                    amountInput.classList.add('bg-white', 'dark:bg-gray-700');
                    if (label) label.textContent = 'AMOUNT (Manual Entry)';
                } else {
                    amountInput.readOnly = true;
                    amountInput.classList.remove('bg-white', 'dark:bg-gray-700');
                    amountInput.classList.add('bg-gray-100', 'dark:bg-gray-600');
                    if (label) label.textContent = 'AMOUNT (Auto-calculated)';
                    updateEditAmountCalculation();
                }
            }
        }

        // Global variables to store current balances
        var currentBalances = @json($entries->groupBy('item')->map(function($items) {
            $latest = $items->sortByDesc('date')->sortByDesc('created_at')->first();
            return [
                'balance' => $latest ? $latest->balance : 0,
                'onsite_balance' => $latest ? $latest->onsite_balance : 0
            ];
        }));

        function loadCurrentBalances() {
            var itemSelect = document.querySelector('#inventoryModal select[name="item"]');
            if (itemSelect) {
                itemSelect.addEventListener('change', function() {
                    updateBalance();
                });
                // Initial load
                updateBalance();
            }
        }

        function updateBalance() {
            var itemSelect = document.querySelector('#inventoryModal select[name="item"]');
            var inInput = document.querySelector('#inventoryModal input[name="in"]');
            var outInput = document.querySelector('#inventoryModal input[name="out"]');
            var balanceInput = document.querySelector('#inventoryModal input[name="balance"]');
            
            if (!itemSelect || !balanceInput) return;
            
            var item = itemSelect.value;
            var inValue = parseFloat(inInput.value) || 0;
            var outValue = parseFloat(outInput.value) || 0;
            var currentBalance = currentBalances[item] ? currentBalances[item].balance : 0;
            
            // Calculate main balance: current balance + IN - OUT
            var newBalance = currentBalance + inValue - outValue;
            balanceInput.value = newBalance.toFixed(2);
            
            // Update hollowblock size field visibility and calculate amount
            updateHollowblockSizeField();
            updateAmountCalculation();
        }

        function updateHollowblockSizeField() {
            var itemSelect = document.querySelector('#inventoryModal select[name="item"]');
            var hollowblockSizeField = document.getElementById('hollowblockSizeField');
            
            if (itemSelect && hollowblockSizeField) {
                if (itemSelect.value === 'HOLLOWBLOCKS') {
                    hollowblockSizeField.style.display = 'block';
                } else {
                    hollowblockSizeField.style.display = 'none';
                }
            }
        }

    function updateAmountCalculation() {
            var itemSelect = document.querySelector('#inventoryModal select[name="item"]');
            var outInput = document.querySelector('#inventoryModal input[name="out"]');
            var pickupDeliverySelect = document.querySelector('#inventoryModal select[name="pickup_delivery_type"]');
            var vatSelect = document.querySelector('#inventoryModal select[name="vat_type"]');
            var hollowblockSizeSelect = document.querySelector('#inventoryModal select[name="hollowblock_size"]');
            var amountInput = document.querySelector('#inventoryModal input[name="amount"]');
            var amountLabel = document.getElementById('amountLabel');
        var manualToggle = document.getElementById('amountManualToggle');
            
            if (!itemSelect || !outInput || !amountInput) return;
            
            var item = itemSelect.value;
            var outValue = parseFloat(outInput.value) || 0;
            var pickupDeliveryType = pickupDeliverySelect ? pickupDeliverySelect.value : '';
            var vatType = vatSelect ? vatSelect.value : '';
            var hollowblockSize = hollowblockSizeSelect ? hollowblockSizeSelect.value : '';
            
        // Manual amount if toggle is on OR per_bag type
        if ((manualToggle && manualToggle.checked) || pickupDeliveryType === 'per_bag') {
                // Make amount field editable for manual entry
                amountInput.readOnly = false;
                amountInput.classList.remove('bg-gray-100', 'dark:bg-gray-600');
                amountInput.classList.add('bg-white', 'dark:bg-gray-700');
                if (amountLabel) {
            amountLabel.textContent = 'AMOUNT (Manual Entry)';
                }
                return;
            } else {
                // Make amount field auto-calculated and read-only
                amountInput.readOnly = true;
                amountInput.classList.remove('bg-white', 'dark:bg-gray-700');
                amountInput.classList.add('bg-gray-100', 'dark:bg-gray-600');
                if (amountLabel) {
                    amountLabel.textContent = 'AMOUNT (Auto-calculated)';
                }
            }
            
            if (outValue <= 0) {
                amountInput.value = '0.00';
                return;
            }
            
            var priceMultiplier = 0;
            
            switch (item) {
                case 'SAND S1 M':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4336.20 : 4015.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4465.80 : 4135.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4595.40 : 4255.00;
                    }
                    break;
                    
                case 'VIBRO SAND':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4681.80 : 4335.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4811.40 : 4455.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4941.00 : 4575.00;
                    }
                    break;
                    
                case 'G1 CURRIMAO':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4082.40 : 3780.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4212.00 : 3900.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4341.60 : 4020.00;
                    }
                    break;
                    
                case 'G1 DAMORTIS':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4336.20 : 4015.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4465.80 : 4135.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4595.40 : 4255.00;
                    }
                    break;
                    
                case '3/4 GRAVEL':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4514.40 : 4180.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4644.00 : 4300.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4773.60 : 4420.00;
                    }
                    break;
                    
                case 'HOLLOWBLOCKS':
                    if (hollowblockSize === '4_inch') {
                        priceMultiplier = (vatType === 'with_vat') ? 73.92 : 66.00;
                    } else if (hollowblockSize === '5_inch') {
                        priceMultiplier = (vatType === 'with_vat') ? 80.08 : 71.00;
                    } else if (hollowblockSize === '6_inch') {
                        priceMultiplier = (vatType === 'with_vat') ? 86.24 : 77.00;
                    }
                    break;
                    
                default:
                    priceMultiplier = 0;
            }
            
            var calculatedAmount = outValue * priceMultiplier;
            amountInput.value = calculatedAmount.toFixed(2);
        }

    function openEditModal(id) {
            var entry = @json($entries);
            var customers = @json($customers);
            var isAdmin = @json(auth()->user()->roles && in_array(strtoupper(trim(auth()->user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR']));
            var found = entry.find(e => e.id === id);
            if (!found) return;
            var form = document.getElementById('editInventoryForm');
            form.action = '/inventory/' + id;
            var fields = `<div class='mb-2'>
                <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>Item</label>
                <input type='text' name='item' value='${found.item}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' required />
            </div>
            <div class='mb-2 grid grid-cols-2 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>Date</label>
                    <input type='date' name='date' value='${found.date}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' required />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>Customer</label>
                    <select name='customer_id' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' required>`;
            customers.forEach(function(c) {
                var selected = c.id === found.customer_id ? 'selected' : '';
                fields += `<option value='${c.id}' ${selected}>${c.company_name ? c.company_name : (c.first_name + ' ' + c.last_name)}</option>`;
            });
            fields += `</select></div></div>`;
            
            // Amount Calculation Fields
            fields += `<div class='mb-2'>
                <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>Pickup/Delivery Type</label>
                <select name='pickup_delivery_type' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' onchange='updateEditAmountCalculation()'>
                    <option value=''>Select Type</option>
                    <option value='pickup_pier' ${found.pickup_delivery_type === 'pickup_pier' ? 'selected' : ''}>Pick up from Pier</option>
                    <option value='pickup_stockpile_delivered_pier' ${found.pickup_delivery_type === 'pickup_stockpile_delivered_pier' ? 'selected' : ''}>Pick up from Stock Pile & Delivered from Pier</option>
                    <option value='delivered_stockpile' ${found.pickup_delivery_type === 'delivered_stockpile' ? 'selected' : ''}>Delivered from Stock Pile</option>
                    <option value='per_bag' ${found.pickup_delivery_type === 'per_bag' ? 'selected' : ''}>Per Bag (Manual Amount)</option>
                </select>
            </div>`;
            
            fields += `<div class='mb-2 grid grid-cols-2 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>VAT Type</label>
                    <select name='vat_type' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' onchange='updateEditAmountCalculation()'>
                        <option value=''>Select VAT</option>
                        <option value='with_vat' ${found.vat_type === 'with_vat' ? 'selected' : ''}>With VAT</option>
                        <option value='without_vat' ${found.vat_type === 'without_vat' ? 'selected' : ''}>Without VAT</option>
                    </select>
                </div>
                <div id='editHollowblockSizeField' style='display: ${found.item === 'HOLLOWBLOCKS' ? 'block' : 'none'}'>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>Hollowblock Size</label>
                    <select name='hollowblock_size' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' onchange='updateEditAmountCalculation()'>
                        <option value=''>Select Size</option>
                        <option value='4_inch' ${found.hollowblock_size === '4_inch' ? 'selected' : ''}>4 inches</option>
                        <option value='5_inch' ${found.hollowblock_size === '5_inch' ? 'selected' : ''}>5 inches</option>
                        <option value='6_inch' ${found.hollowblock_size === '6_inch' ? 'selected' : ''}>6 inches</option>
                    </select>
                </div>
            </div>`;
            
            const editManualChecked = (found.pickup_delivery_type === 'per_bag');
            fields += `<div class='mb-2'>
                <div class='flex items-center justify-between'>
                    <label id='editAmountLabel' class='block text-sm font-medium text-gray-700 dark:text-gray-300'>AMOUNT (Auto-calculated)</label>
                    <label class='inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300'>
                        <input type='checkbox' id='editAmountManualToggle' name='is_amount_manual' value='1' ${editManualChecked ? 'checked' : ''} onchange='handleEditAmountManualToggle()' class='rounded border-gray-300 text-blue-600 focus:ring-blue-500' />
                        Manual amount
                    </label>
                </div>
                <input type='number' step='0.01' name='amount' id='editAmountInput' value='${found.amount ?? ''}' class='w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-2 py-1 ${editManualChecked ? 'bg-white dark:bg-gray-700' : 'bg-gray-100 dark:bg-gray-600'} text-gray-900 dark:text-gray-100' min='0' max='999999.99' ${editManualChecked ? '' : 'readonly'} />
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-2 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>OR/AR</label>
                    <input type='text' name='or_ar' value='${found.or_ar ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>DR#</label>
                    <input type='text' name='dr_number' value='${found.dr_number ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
                </div>
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-3 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>IN</label>
                    <input type='number' step='0.0001' name='in' value='${found.in ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>OUT</label>
                    <input type='number' step='0.0001' name='out' value='${found.out ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' oninput="updateEditAmountCalculation()" />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>BALANCE</label>
                    <input type='number' step='0.0001' name='balance' value='${found.balance ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
                </div>
            </div>`;
            fields += `<div class='mb-2 grid grid-cols-3 gap-2'>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>ONSITE IN</label>
                    <input type='number' step='0.0001' name='onsite_in' value='${found.onsite_in ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>ACTUAL OUT</label>
                    <input type='number' step='0.0001' name='actual_out' value='${found.actual_out ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
                </div>
                <div>
                    <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>ONSITE BALANCE</label>
                    <input type='number' step='0.0001' name='onsite_balance' value='${found.onsite_balance ?? ''}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
                </div>
            </div>`;
            // Add Onsite Date field - accessible to everyone
            var currentDate = found.onsite_date || new Date().toISOString().split('T')[0];
            fields += `<div class='mb-2'>
                <label class='block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300'>Onsite Date</label>
                <input type='date' name='onsite_date' value='${currentDate}' class='w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400' />
            </div>`;
            document.getElementById('editInventoryFields').innerHTML = fields;
            document.getElementById('editInventoryModal').classList.remove('hidden');
            // Initialize edit amount state based on pickup_delivery_type or existing manual toggle
            updateEditAmountCalculation();
        }

        function updateEditAmountCalculation() {
            var itemInput = document.querySelector('#editInventoryModal input[name="item"]');
            var outInput = document.querySelector('#editInventoryModal input[name="out"]');
            var pickupDeliverySelect = document.querySelector('#editInventoryModal select[name="pickup_delivery_type"]');
            var vatSelect = document.querySelector('#editInventoryModal select[name="vat_type"]');
            var hollowblockSizeSelect = document.querySelector('#editInventoryModal select[name="hollowblock_size"]');
            var amountInput = document.querySelector('#editInventoryModal input[name="amount"]');
            var amountLabel = document.getElementById('editAmountLabel');
            var manualToggle = document.getElementById('editAmountManualToggle');
            
            if (!itemInput || !outInput || !amountInput) return;
            
            var item = itemInput.value;
            var outValue = parseFloat(outInput.value) || 0;
            var pickupDeliveryType = pickupDeliverySelect ? pickupDeliverySelect.value : '';
            var vatType = vatSelect ? vatSelect.value : '';
            var hollowblockSize = hollowblockSizeSelect ? hollowblockSizeSelect.value : '';
            
            // Manual amount if toggle is on OR per_bag type
            if ((manualToggle && manualToggle.checked) || pickupDeliveryType === 'per_bag') {
                // Make amount field editable for manual entry
                amountInput.readOnly = false;
                amountInput.classList.remove('bg-gray-100', 'dark:bg-gray-600');
                amountInput.classList.add('bg-white', 'dark:bg-gray-700');
                if (amountLabel) {
                    amountLabel.textContent = 'AMOUNT (Manual Entry)';
                }
                return;
            } else {
                // Make amount field auto-calculated and read-only
                amountInput.readOnly = true;
                amountInput.classList.remove('bg-white', 'dark:bg-gray-700');
                amountInput.classList.add('bg-gray-100', 'dark:bg-gray-600');
                if (amountLabel) {
                    amountLabel.textContent = 'AMOUNT (Auto-calculated)';
                }
            }
            
            if (outValue <= 0) {
                amountInput.value = '0.00';
                return;
            }
            
            var priceMultiplier = 0;
            
            switch (item) {
                case 'SAND S1 M':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4336.20 : 4015.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4465.80 : 4135.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4595.40 : 4255.00;
                    }
                    break;
                    
                case 'VIBRO SAND':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4681.80 : 4335.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4811.40 : 4455.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4941.00 : 4575.00;
                    }
                    break;
                    
                case 'G1 CURRIMAO':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4082.40 : 3780.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4212.00 : 3900.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4341.60 : 4020.00;
                    }
                    break;
                    
                case 'G1 DAMORTIS':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4336.20 : 4015.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4465.80 : 4135.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4595.40 : 4255.00;
                    }
                    break;
                    
                case '3/4 GRAVEL':
                    if (pickupDeliveryType === 'pickup_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4514.40 : 4180.00;
                    } else if (pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                        priceMultiplier = (vatType === 'with_vat') ? 4644.00 : 4300.00;
                    } else if (pickupDeliveryType === 'delivered_stockpile') {
                        priceMultiplier = (vatType === 'with_vat') ? 4773.60 : 4420.00;
                    }
                    break;
                    
                case 'HOLLOWBLOCKS':
                    if (hollowblockSize === '4_inch') {
                        priceMultiplier = (vatType === 'with_vat') ? 73.92 : 66.00;
                    } else if (hollowblockSize === '5_inch') {
                        priceMultiplier = (vatType === 'with_vat') ? 80.08 : 71.00;
                    } else if (hollowblockSize === '6_inch') {
                        priceMultiplier = (vatType === 'with_vat') ? 86.24 : 77.00;
                    }
                    break;
                    
                default:
                    priceMultiplier = 0;
            }
            
            var calculatedAmount = outValue * priceMultiplier;
            amountInput.value = calculatedAmount.toFixed(2);
        }

        // Delete confirmation function
        function confirmDelete(entryId) {
            if (confirm('Are you sure you want to delete this inventory entry? This action cannot be undone.')) {
                // Create and submit delete form
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/inventory/' + entryId;
                
                // Add CSRF token
                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method override for DELETE
                var methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Functions for Create Customer modal
        function updateAccountType() {
            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            const companyName = document.getElementById('company_name').value;
            const typeSelect = document.getElementById('type');
            
            if (companyName.trim()) {
                typeSelect.value = 'company';
            } else if (firstName.trim() || lastName.trim()) {
                typeSelect.value = 'individual';
            }
        }

        function resetFields() {
            document.getElementById('first_name').value = '';
            document.getElementById('last_name').value = '';
            document.getElementById('company_name').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('type').value = 'individual';
        }

        // Check for success message and close modal if customer was created
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success') && str_contains(session('success'), 'account created successfully'))
                // Close the modal
                const modal = document.querySelector('[x-data*="openModal"]');
                if (modal) {
                    // Trigger Alpine.js to close modal
                    modal.__x.$data.openModal = false;
                }
                
                // Optionally reload the page to refresh customer dropdowns
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            @endif
        });
    </script>

    <!-- Excel and PDF Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // Excel Export Function for Inventory
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('exportExcel').addEventListener('click', function () {
                console.log('Excel export clicked');
                
                // Create a new workbook
                const workbook = XLSX.utils.book_new();
                
                // Define the inventory items
                const inventoryItems = ['G1 DAMORTIS','G1 CURRIMAO','3/4 GRAVEL','VIBRO SAND','SAND S1 DAMORTIS','SAND S1 M','HOLLOWBLOCKS'];
                
                // Process each inventory item
                inventoryItems.forEach((itemName, index) => {
                    console.log(`Processing item: ${itemName}`);
                    
                    // Get the tab content for this item
                    const tabContent = document.getElementById(`tabContent${index}`);
                    if (!tabContent) {
                        console.log(`Tab content not found for ${itemName}`);
                        return;
                    }
                    
                    const table = tabContent.querySelector('table');
                    if (!table) {
                        console.log(`Table not found for ${itemName}`);
                        return;
                    }
                    
                    // Clone table to avoid modifying original
                    const clonedTable = table.cloneNode(true);
                    
                    // Remove the UPDATE column (last column) from all rows
                    const rows = clonedTable.querySelectorAll('tr');
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('th, td');
                        if (cells.length > 0) {
                            // Remove the last cell (UPDATE column)
                            cells[cells.length - 1].remove();
                        }
                    });
                    
                    // Remove month separator rows (they have class bg-gray-50)
                    const monthRows = clonedTable.querySelectorAll('tr.bg-gray-50');
                    monthRows.forEach(row => row.remove());
                    
                    // Convert table to worksheet
                    const worksheet = XLSX.utils.table_to_sheet(clonedTable, { raw: true });
                    
                    // Clean up sheet name for Excel compatibility
                    let sheetName = itemName.replace(/[^\w\s]/g, '').substring(0, 31);
                    console.log(`Adding sheet: ${sheetName}`);
                    
                    // Add worksheet to workbook
                    XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);
                });
                
                // Check if workbook has any sheets
                if (workbook.SheetNames.length === 0) {
                    console.error('No sheets to export');
                    alert('No data found to export');
                    return;
                }
                
                // Generate filename with current date
                const currentDate = new Date().toISOString().split('T')[0];
                const fileName = `Inventory_Report_${currentDate}.xlsx`;
                
                console.log(`Exporting file: ${fileName}`);
                console.log(`Sheets in workbook: ${workbook.SheetNames.join(', ')}`);
                
                // Export the workbook
                try {
                    XLSX.writeFile(workbook, fileName);
                    console.log('Export successful');
                } catch (error) {
                    console.error('Export failed:', error);
                    alert('Export failed: ' + error.message);
                }
            });
        });
    </script>

    <!-- PDF Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('exportPdf').addEventListener('click', function () {
                // Initialize jsPDF in landscape mode
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'landscape',
                    unit: 'pt',
                    format: 'a4',
                });
                
                let startY = 40; // Starting Y position
                const pageHeight = doc.internal.pageSize.height;
                
                // Get all tabs
                const tabs = document.querySelectorAll('.tab-content');
                
                tabs.forEach((tab, tabIndex) => {
                    const table = tab.querySelector('table');
                    if (!table) return;
                    
                    // Get item name
                    const itemName = tab.querySelector('h3').textContent.trim();
                    
                    // Add new page if not first tab
                    if (tabIndex > 0) {
                        doc.addPage();
                        startY = 40;
                    }
                    
                    // Add title for each item
                    doc.setFontSize(16);
                    doc.text(itemName, 40, startY);
                    startY += 30;
                    
                    // Prepare table data
                    const headers = [];
                    const tableData = [];
                    
                    // Get headers (exclude UPDATE column)
                    const headerRows = table.querySelectorAll('thead tr');
                    if (headerRows.length > 1) {
                        // Use the second header row (main headers)
                        const headerCells = headerRows[1].querySelectorAll('th');
                        headerCells.forEach((cell, index) => {
                            if (index < headerCells.length - 1) { // Exclude last column (UPDATE)
                                headers.push(cell.textContent.trim());
                            }
                        });
                    }
                    
                    // Get data rows (exclude month separator rows)
                    const dataRows = table.querySelectorAll('tbody tr');
                    dataRows.forEach(row => {
                        // Skip month separator rows
                        if (row.classList.contains('bg-gray-50')) return;
                        
                        const rowData = [];
                        const cells = row.querySelectorAll('td');
                        cells.forEach((cell, index) => {
                            if (index < cells.length - 1) { // Exclude last column (UPDATE)
                                rowData.push(cell.textContent.trim());
                            }
                        });
                        
                        if (rowData.length > 0) {
                            tableData.push(rowData);
                        }
                    });
                    
                    // Add table to PDF
                    if (headers.length > 0 && tableData.length > 0) {
                        doc.autoTable({
                            head: [headers],
                            body: tableData,
                            startY: startY,
                            margin: { top: 20, left: 20, right: 20, bottom: 20 },
                            styles: { 
                                fontSize: 8, 
                                textColor: [0, 0, 0],
                                cellPadding: 2
                            },
                            headStyles: {
                                fillColor: [100, 100, 100],
                                textColor: [255, 255, 255],
                                fontSize: 9
                            },
                            theme: 'grid',
                            columnStyles: {
                                0: { cellWidth: 60 },  // DATE
                                1: { cellWidth: 80 },  // CUSTOMER
                                2: { cellWidth: 40 },  // SHIP#
                                3: { cellWidth: 50 },  // VOYAGE#
                                4: { cellWidth: 40 },  // IN
                                5: { cellWidth: 40 },  // OUT
                                6: { cellWidth: 50 },  // BALANCE
                                7: { cellWidth: 50 },  // AMOUNT
                                8: { cellWidth: 50 },  // OR/AR
                                9: { cellWidth: 40 },  // DR#
                                10: { cellWidth: 60 }, // DATE (Onsite)
                                11: { cellWidth: 40 }, // IN (Onsite)
                                12: { cellWidth: 50 }, // ACTUAL OUT
                                13: { cellWidth: 50 }, // BALANCE (Onsite)
                                14: { cellWidth: 30 }, // Empty column
                            },
                        });
                        
                        // Update startY for next table
                        startY = doc.lastAutoTable.finalY + 30;
                    }
                });
                
                // Generate filename with current date
                const currentDate = new Date().toISOString().split('T')[0];
                const fileName = `Inventory_Report_${currentDate}.pdf`;
                
                // Save the PDF
                doc.save(fileName);
            });
            // Per-month PDF export per tab
            const { jsPDF } = window.jspdf;
            const monthButtons = document.querySelectorAll('[id^="exportPdfMonth"]');
            monthButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = this.getAttribute('data-tab-index');
                    const monthPicker = document.getElementById('monthPicker' + idx);
                    const monthValue = monthPicker && monthPicker.value ? monthPicker.value : '';
                    if (!monthValue) {
                        alert('Please select a month to export.');
                        return;
                    }

                    // monthValue is in format YYYY-MM; build a filter like 'MM-YYYY' or 'Month YYYY'
                    const [year, month] = monthValue.split('-');
                    // We'll detect month rows by their label (e.g., 'MARCH 2025') in the first cell spanning 16 cols
                    const monthNames = ["JANUARY","FEBRUARY","MARCH","APRIL","MAY","JUNE","JULY","AUGUST","SEPTEMBER","OCTOBER","NOVEMBER","DECEMBER"];
                    const monthLabel = monthNames[parseInt(month, 10) - 1] + ' ' + year;

                    const tab = document.getElementById('tabContent' + idx);
                    const table = tab ? tab.querySelector('table') : null;
                    if (!table) return;

                    // Build a filtered dataset for the selected month: include headers, and rows between the target month header and the next month header only
                    // Collect headers
                    const headerRows = table.querySelectorAll('thead tr');
                    const headers = [];
                    if (headerRows.length > 1) {
                        const headerCells = headerRows[1].querySelectorAll('th');
                        headerCells.forEach((cell, index) => {
                            if (index < headerCells.length - 1) {
                                headers.push(cell.textContent.trim());
                            }
                        });
                    }

                    // Traverse tbody rows and capture only the block under our month header
                    const dataRows = table.querySelectorAll('tbody tr');
                    const tableData = [];
                    let inTargetMonth = false;
                    dataRows.forEach(row => {
                        // Month header rows have class 'bg-gray-50' and a single td with colspan
                        if (row.classList.contains('bg-gray-50')) {
                            const td = row.querySelector('td');
                            const label = td ? (td.textContent || '').trim().toUpperCase() : '';
                            inTargetMonth = (label === monthLabel);
                            return; // skip the month header row itself
                        }
                        if (!inTargetMonth) return;

                        const cells = row.querySelectorAll('td');
                        const rowData = [];
                        cells.forEach((cell, index) => {
                            if (index < cells.length - 1) {
                                rowData.push(cell.textContent.trim());
                            }
                        });
                        if (rowData.length > 0) tableData.push(rowData);
                    });

                    if (headers.length === 0 || tableData.length === 0) {
                        alert('No entries found for ' + monthLabel + ' in this item.');
                        return;
                    }

                    const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
                    let startY = 40;

                    // Title with item name and month
                    const itemName = tab.querySelector('h3')?.textContent?.trim() || 'Inventory';
                    doc.setFontSize(14);
                    doc.text(itemName + ' — ' + monthLabel, 40, startY);
                    startY += 20;

                    doc.autoTable({
                        head: [headers],
                        body: tableData,
                        startY: startY,
                        margin: { top: 20, left: 20, right: 20, bottom: 20 },
                        styles: { fontSize: 8, textColor: [0, 0, 0], cellPadding: 2 },
                        headStyles: { fillColor: [100, 100, 100], textColor: [255, 255, 255], fontSize: 9 },
                        theme: 'grid',
                        columnStyles: {
                            0: { cellWidth: 60 }, 1: { cellWidth: 80 }, 2: { cellWidth: 40 }, 3: { cellWidth: 50 }, 4: { cellWidth: 40 }, 5: { cellWidth: 40 }, 6: { cellWidth: 50 }, 7: { cellWidth: 50 }, 8: { cellWidth: 50 }, 9: { cellWidth: 40 }, 10: { cellWidth: 60 }, 11: { cellWidth: 40 }, 12: { cellWidth: 50 }, 13: { cellWidth: 50 }, 14: { cellWidth: 30 }
                        },
                    });

                    const fileName = `${itemName.replace(/\s+/g,'_')}_${year}-${month}.pdf`;
                    doc.save(fileName);
                });
            });
        });
    </script>
</x-app-layout>
