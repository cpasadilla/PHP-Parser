<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <!-- Left Section (Back Button + Title) -->
            <h2 class="text-xl font-semibold leading-tight flex items-center">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700">←</button>
                {{ __('Order Page') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <form method="POST" action="{{route('pushOrder')}}">
                    @csrf
                    <!-- Shipper ID -->
                    <div class="space-y-2">
                        <x-form.label for="shipper_id" :value="__('Shipper ID')"/>
                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-hashtag aria-hidden="true" class="w-5 h-5" />
                            </x-slot>
                            <x-form.input withicon id="shipper_id" class="block w-full focus:ring focus:ring-indigo-300" type="text"
                                name="shipper_id" :value="$data['customer_id']" required autofocus readonly
                                placeholder="{{ __('Shipper ID') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <!-- Shipper Name -->
                    <div class="space-y-2">
                        <x-form.label for="shipper_name" :value="__('Shipper Name')" />
                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-user aria-hidden="true" class="w-5 h-5" />
                            </x-slot>
                            <x-form.input withicon id="shipper_name" class="block w-full focus:ring focus:ring-indigo-300" type="text"
                                name="shipper_name" :value="$data['customer_name']" required autofocus readonly
                                placeholder="{{ __('Shipper Name') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <!-- Shipper Number -->
                    <div class="space-y-2">
                        <x-form.label for="shipper_number" :value="__('Shipper Number')" />
                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-phone aria-hidden="true" class="w-5 h-5" />
                            </x-slot>
                            <x-form.input withicon id="shipper_number" class="block w-full focus:ring focus:ring-indigo-300" type="text"
                                name="shipper_number" :value="$data['customer_no']" autofocus readonly
                                placeholder="{{ __('Shipper Number') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>
            </div>

            <div>
                <!-- Consignee ID -->
                <div class="space-y-2">
                    <x-form.label for="consignee_id" :value="__('Consignee ID')" />
                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon">
                            <x-heroicon-o-hashtag aria-hidden="true" class="w-5 h-5" />
                        </x-slot>
                        <x-form.input withicon id="consignee_id" class="block w-full focus:ring focus:ring-indigo-300" type="text"
                            name="consignee_id" :value="$data['receiver_id']" required autofocus readonly
                            placeholder="{{ __('Consignee ID') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>

                <!-- Consignee Name -->
                <div class="space-y-2">
                    <x-form.label for="consignee_name" :value="__('Consignee Name')" />
                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon">
                            <x-heroicon-o-user aria-hidden="true" class="w-5 h-5" />
                        </x-slot>
                        <x-form.input withicon id="consignee_name" class="block w-full focus:ring focus:ring-indigo-300" type="text"
                            name="consignee_name" :value="$data['receiver_name']" required autofocus readonly
                            placeholder="{{ __('Consignee Name') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>

                <!-- Consignee Number -->
                <div class="space-y-2">
                    <x-form.label for="consignee_number" :value="__('Consignee Number')" />
                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon">
                            <x-heroicon-o-phone aria-hidden="true" class="w-5 h-5" />
                        </x-slot>
                        <x-form.input withicon id="consignee_number" class="block w-full focus:ring focus:ring-indigo-300" type="text"
                            name="consignee_number" :value="$data['receiver_no']" autofocus readonly
                            placeholder="{{ __('Consignee Number') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>
            </div>

            <div>
                <!-- Origin -->
                <div class="space-y-2">
                    <x-form.label for="origin" :value="__('Origin')" />
                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon"></x-slot>
                        <select id="origin" name="origin"
                            class="block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                            <option value="">Select Origin</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->location }}">{{ $location->location }}</option>
                            @endforeach
                        </select>
                    </x-form.input-with-icon-wrapper>
                </div>

                <!-- Destination -->
                <div class="space-y-2">
                    <x-form.label for="destination" :value="__('Destination')" />
                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon"></x-slot>
                        <select id="destination" name="destination"
                            class="block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                            required>
                            <option value="">Select Destination</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location->location }}">{{ $location->location }}</option>
                            @endforeach
                        </select>
                    </x-form.input-with-icon-wrapper>
                </div>

                <!-- Button for adding Location -->
                @if (Auth::user()->roles->roles == 'Admin')
                <button onclick="openLocationModal()" class="mt-4 px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-700">+ Add Location</button>
                @endif
            </div>

            <!-- Modal for adding Location -->
            <div id="locationModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
                <div class="bg-white dark:bg-[#222738] rounded-lg shadow-lg p-6 w-1/3">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-4">Add New Location</h2>

                    <label for="newLocation" class="block font-medium text-gray-900 dark:text-gray-200">Location Name:</label>
                    <input type="text" id="newLocation" class="w-full p-2 border rounded-md text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 mb-4 focus:ring focus:ring-indigo-300">

                    <div class="flex justify-end">
                        <button onclick="closeLocationModal()" class="px-3 py-1 bg-gray-500 text-white dark:text-white rounded-md hover:bg-gray-700">Cancel</button>
                        <button onclick="addLocation()" class="px-3 py-1 ml-2 bg-blue-500 text-white dark:text-white rounded-md hover:bg-blue-700">Add</button>
                    </div>
                </div>
            </div>

            <div>
                <!-- Ship Number -->
                <div class="space-y-2">
                    <x-form.label for="ship_no" :value="__('Ship Number')" />
                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon"></x-slot>
                        <select id="ship_no" name="ship_no"
                            class="block w-full p-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-300 dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            @foreach ($ships as $ship)
                                @if ($ship->status == 'DRYDOCKED')

                                @else
                                    <option value={{$ship->ship_number}} >{{$ship->ship_number}}</option>

                                @endif
                            @endforeach
                        </select>
                    </x-form.input-with-icon-wrapper>
                </div>

                <!-- Voyage Number -->
                <div class="space-y-2">
                    <x-form.label for="voyage_no" :value="__('Voyage Number')" />
                    <div class="flex items-center gap-2"> <!-- Flex container for input and buttons -->
                        <!-- Input Field -->
                        <x-form.input-with-icon-wrapper class="flex-1 relative">
                            <x-slot name="icon">
                                <i class="fas fa-anchor absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                            </x-slot>
                            <input type="hidden" id="selected_voyage_group" name="selected_voyage_group" value="" />
                            <input id="voyage_no" name="voyage_no"
                                class="block w-full pl-10 p-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                type="text" required autofocus
                                placeholder="{{ __('Voyage Number') }}"
                            />
                            <select id="voyage_select_order" name="voyage_select_order" style="display: none;"
                                class="block w-full pl-10 p-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-300 dark:bg-gray-800 dark:text-white dark:border-gray-600">
                                <option value="">Select Voyage</option>
                            </select>
                        </x-form.input-with-icon-wrapper>

                        <!-- Buttons (Only for Ship I or II) -->
                        <div id="voyageButtons" class="flex gap-2"> <!-- Buttons container -->
                            <button type="button" onclick="setVoyageSuffix('IN')" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">IN</button>
                            <button type="button" onclick="setVoyageSuffix('OUT')" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm">OUT</button>
                        </div>
                    </div>
                </div>

                <!-- Container Number -->
                <div class="space-y-2">
                    <x-form.label for="container_no" :value="__('Container Number')" />
                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon">
                            <i class="fas fa-box w-5 h-5"></i>
                        </x-slot>
                        <x-form.input withicon id="container_no" class="block w-full focus:ring focus:ring-indigo-300" type="text"
                            name="container_no" :value="old('container_no')"
                            placeholder="{{ __('Container Number') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>
            </div>
        </div><br>

        <div class="row justify-content-center">
            <div class="col-lg-12 col-12  min-vh-500">
                <div class="box">
                    <div class="col-sm-12 text-center">
                        <img src="{{ asset('images/line_5.png') }}" class="mx-auto w-full mt-2">
                    </div>
                </div>
            </div>
        </div><br>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <!-- Cargo Status -->
            <div class="space-y-2">
                <x-form.label for="cargo_status" :value="__('Cargo Status')" />
                <x-form.input-with-icon-wrapper>
                    <x-slot name="icon"></x-slot>
                    <select id="cargo_status" name="cargo_status"
                        class="block w-full p-2 h-10 border rounded-md shadow-sm focus:ring focus:ring-indigo-300 dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        <option value="CHARTERED" {{ $data['customer_no'] == 'CHARTERED' ? 'selected' : '' }}>CHARTERED</option>
                        <option value="LOOSE CARGO" {{ $data['customer_no'] == 'LOOSE CARGO' ? 'selected' : '' }}>LOOSE CARGO</option>
                        <option value="STUFFING" {{ $data['customer_no'] == 'STUFFING' ? 'selected' : '' }}>STUFFING</option>
                    </select>
                </x-form.input-with-icon-wrapper>
            </div>

            <!-- Gate Pass Number -->
            <div class="space-y-2">
                <x-form.label for="gatepass" :value="__('Gate Pass Number')" />
                <x-form.input-with-icon-wrapper>
                    <x-slot name="icon">
                        <i class="fas fa-ticket-alt w-5 h-5"></i>
                    </x-slot>
                    <x-form.input withicon id="gatepass" class="block w-full h-10 focus:ring focus:ring-indigo-300" type="text"
                        name="gatepass" :value="old('gatepass')"
                        placeholder="{{ __('Gate Pass Number') }}" />
                </x-form.input-with-icon-wrapper>
            </div>

            <!-- Checker Name -->
            <div class="space-y-2">
                <x-form.label for="checker" :value="__('Checker Name')" />
                <x-form.input-with-icon-wrapper class="flex-1">
                    <x-slot name="icon"></x-slot>
                    <select id="checker" name="checker"
                        class="block w-full p-2 h-10 border rounded-md shadow-sm focus:ring focus:ring-indigo-300 dark:bg-gray-800 dark:text-white dark:border-gray-600">
                        <option value="">Select Checker</option>
                    </select>
                </x-form.input-with-icon-wrapper>
            </div>
            <!-- Buttons (Only for Admin) -->
            @if (Auth::user()->roles->roles == 'Admin')
                <div class="flex gap-2"> <!-- Buttons container -->
                    <button type="button" id="addChecker" class="w-24 py-1 h-10 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Add</button>
                    <button type="button" id="deleteChecker" class="w-24 py-1 h-10 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Delete</button>
                </div>
            @endif

            <!-- Remark -->
            <div class="space-y-2">
                <x-form.label for="remark" :value="__('Remark')" />
                <x-form.input-with-icon-wrapper>
                    <x-slot name="icon">
                        <i class="fas fa-book w-5 h-5"></i>
                    </x-slot>
                    <x-form.input withicon id="remark" class="block w-full h-10 focus:ring focus:ring-indigo-300" type="text"
                        name="remark" :value="old('remark')"
                        placeholder="{{ __('Remark') }}" />
                </x-form.input-with-icon-wrapper>
            </div>

            <!-- Value -->
            <div class="space-y-2">
                <x-form.label for="valuation" :value="__('Value')" />
                <x-form.input-with-icon-wrapper>
                    <x-slot name="icon">
                        <i class="fas fa-dollar w-5 h-5"></i>
                    </x-slot>
                    <x-form.input withicon id="valuation" class="block w-full h-10 focus:ring focus:ring-indigo-300"
                        type="text" name="valuation"
                        :value="old('valuation')" autofocus
                        placeholder="{{ __('Value') }}" oninput="formatNumberInput(this)" />
                </x-form.input-with-icon-wrapper>
            </div>

            <!-- Other Charges -->
            <div class="space-y-2">
                <x-form.label for="other" :value="__('Other Charges')" />
                <x-form.input-with-icon-wrapper>
                    <x-slot name="icon">
                        <i class="fas fa-dollar w-5 h-5"></i>
                    </x-slot>
                    <x-form.input withicon id="other" class="block w-full h-10 focus:ring focus:ring-indigo-300"
                        type="text" name="other"
                        :value="old('other')" autofocus
                        placeholder="{{ __('Other Charges') }}" oninput="formatNumberInput(this)" />
                </x-form.input-with-icon-wrapper>
            </div>
        </div>
    </div><br>

    <input type="hidden" id="cartData" name="cartData">
    <input type="hidden" id="cartTotal" name="cartTotal">

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="card-header flex justify-between items-center">
            <h5 class="font-semibold flex items-center">
                <x-heroicon-o-shopping-cart class="w-5 h-5 mr-1" /> Cart
            </h5>

            <span onclick="openModal()" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-700">Open Listing</span>
        </div>
        <br>
        <table class="w-full border-collapse">
            <thead class="bg-gray-200 dark:bg-dark-eval-0">
                <tr>
                    <th class="p-2 text-black-700 dark:text-white-700">Quantity</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Item Name</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Category</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Description</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Unit</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Measurements (L X W H )</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Multiplier</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Rate</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Total</th>
                    <th class="p-2 text-black-700 dark:text-white-700">Edit</th>
                    <th class="p-2 text-center text-black-700 dark:text-white-700">Delete</th>
                </tr>
            </thead>
            <tbody id="cartTableBody">
                <!-- Items will be added here dynamically -->
            </tbody>
        </table>
        <br>
        <h6 class="font-semibold text-black-700 dark:text-white-700">
            Total Price: <span id="totalItems">0</span>
        </h6>
        <div class="flex justify-end mt-3">
            <x-button class="justify-center w-full gap-2" type="submit">
                <x-heroicon-o-shopping-cart class="w-6 h-6" aria-hidden="true" />
                <span>{{ __('Submit Order') }}</span>
            </x-button>
        </div>
    </div><br>
                </form>

    <!-- Modal -->
    <div id="addToCartModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="bg-white dark:bg-[#222738] rounded-lg shadow-lg p-4 w-1/2 max-w-5xl max-h-[80vh] overflow-auto">
            <!-- Top Row: Listing and Search Bar -->
            <div class="flex justify-between items-center mb-2">
                <!-- Left Side: Listing -->
                <h5 class="font-semibold text-lg">Listing</h5>

                <!-- Right Side: Search Bar with Button -->
                <div class="flex items-center space-x-2">
                    <input type="text" id="searchInput" placeholder="Search items..."
                    class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                </div>
            </div>

            <div class="flex gap-4">
                <!-- Left Column: Input Fields -->
                <div class="w-1/2">
                    <label class="block font-medium text-gray-900 dark:text-gray-200">Item Code:</label>
                    <input type="text" id="itemCode" name="itemCode" readonly
                        class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">

                    <label class="block font-medium text-gray-900 dark:text-gray-200">Item Name:</label>
                    <input type="text" id="itemName" name="itemName" readonly
                        class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">

                    <label class="block font-medium text-gray-900 dark:text-gray-200">Unit:</label>
                    <div class="flex items-center gap-2">
                        <select id="unit" name="unit"
                            class="w-full p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                            <option value="">Select Unit</option>
                            <option value="PCS">PCS</option>
                            <option value="PC">PC</option>
                            <option value="BOX">BOX</option>
                            <option value="BOXS">BOXS</option>
                            <option value="CASE">CASE</option>
                            <option value="BUNDLE">BUNDLE</option>
                            <option value="CRATE">CRATE</option>
                            <option value="PAIL">PAIL</option>
                            <option value="PLASTIC">PLASTIC</option>
                            <option value="UNIT">UNIT</option>
                            <option value="BAG">BAG</option>
                            <option value="BAGS">BAGS</option>
                            <option value="ROLL">ROLL</option>
                            <option value="SACK">SACK</option>
                            <option value="SACKS">SACKS</option>
                            <option value="BALE">BALE</option>
                            <option value="BALES">BALES</option>
                            <option value="CBM">CBM</option>
                            <option value="TIN">TIN</option>
                            <option value="TINS">TINS</option>
                            <option value="SET">SET</option>
                            <option value="SETS">SETS</option>
                            <option value="CTN">CTN</option>
                            <option value="CYL">CYL</option>
                            <option value="KG">KG</option>
                            <option value="RIMS">RIMS</option>
                        </select>
                        <button type="button" id="addUnit"
                            class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Add
                        </button>
                    </div>

                    <label class="block font-medium text-gray-900 dark:text-gray-200">Category:</label>
                    <input type="text" id="category" name="category" readonly
                        class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                    <label class="block font-medium text-gray-900 dark:text-gray-200">Weight:</label>
                    <input type="text" id="weight" name="weight"
                        class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                    <!-- Measurements -->
                    <div class="space-y-2" id="measurements">
                        <label class="block font-medium text-gray-900 dark:text-gray-200">Measurements (L × W × H):</label>
                        <div class="grid grid-cols-4 gap-2 items-center">
                            <input type="number" id="length" name="length"
                                class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                placeholder="Length" min="0" step="0.01">

                            <input type="number" id="width" name="width"
                                class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                placeholder="Width" min="0" step="0.01">

                            <input type="number" id="height" name="height"
                                class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                placeholder="Height" min="0" step="0.01">

                            <input list="multipliers" id="multiplier" name="multiplier" type="number"
                                class="p-2 border rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200"
                                placeholder="Multiplier" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="fix" id="fixed" >
                        <label class="block font-medium text-gray-900 dark:text-gray-200">Price:</label>
                        <input type="text" id="price" name="price" readonly
                            class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                    </div>
                    <label class="block font-medium text-gray-900 dark:text-gray-200">Quantity:</label>
                    <input type="text" id="quantity" name="quantity"
                        class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">

                    <label class="block font-medium text-gray-900 dark:text-gray-200">Description:</label>
                    <textarea id="description" name="description"
                        class="w-full p-2 border rounded-md mb-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white h-20 focus:ring focus:ring-indigo-200">
                    </textarea>
                </div>

                <!-- Right Column: Table -->
                <div class="w-2/3">
                    <div id="tableContainer"
                        class="border border-gray-300 dark:border-gray-600 resize-y overflow-auto p-2"
                        style="min-height: 100px; max-height: 600px; height: 1100px;">
                        <table class="w-full border-collapse border border-gray-300 dark:border-gray-600 text-sm">
                            <thead class="bg-gray-200 dark:bg-dark-eval-0">
                                <tr>
                                    <th class="p-3 border">Item Code</th>
                                    <th class="p-3 border">Item Name</th>
                                    <th class="p-3 border ">Category
                                        <select id="categoryFilter"
                                            class="w-full p-2 border rounded-md font-small bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200">
                                            <option value="">Show All</option>
                                            @foreach($lists->pluck('category')->unique() as $category)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="itemTableBody">
                                @foreach($lists as $list)
                                <tr class="item-row" data-category="{{ $list->category }}">
                                    <td class="p-3 border">{{ $list->item_code }}</td>
                                    <td class="p-3 border">{{ $list->item_name }}</td>
                                    <td class="p-3 border">{{ $list->category }}</td>
                                    <td class="p-3 border" hidden>{{ number_format($list->price, 2) }}</td>
                                    <td class="p-3 border" hidden>{{ $list->multiplier }}</td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <button onclick="clearFields()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Clear Fields</button>

            <!-- Bottom Right Buttons -->
            <div class="flex justify-end mt-3">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500">Cancel</button>
                <button id="addToCart" onclick="addToCart()" class="px-4 py-2 ml-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-700 flex items-center space-x-2">
                    <i class="fas fa-shopping-cart"></i>
                    <span> Add to Cart</span>
                </button>
                <button id="saveButton" onclick="saveUpdatedItem()" class="px-4 py-2 ml-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-700 flex items-center space-x-2 hidden">
                    <i class="fas fa-floppy-disk"></i>
                    <span>Update</span>
                </button>
            </div>
        </div>
    </div>

    <!--td class="p-2 text-center">${item.description} - Weight ${item.weight}</td-->
    <script>
        let cart = [];
        let totalPrice = 0;
        let edit = 0;

        function openModal() {
            document.getElementById('addToCartModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('addToCart').classList.remove('hidden'); // Show Add to Cart
            document.getElementById('saveButton').classList.add('hidden'); // Hide Save Button
            document.getElementById('addToCartModal').classList.add('hidden');
        }

        function addToCart() {
            let l = parseFloat(document.getElementById('length').value) || 1;
            let w = parseFloat(document.getElementById('width').value) || 1;
            let h = parseFloat(document.getElementById('height').value) || 1;
            let m = parseFloat(document.getElementById('multiplier').value) || 'N/A';
            let price = parseFloat(document.getElementById('price').value); // Ensure price is a number
            let quantity = parseFloat(document.getElementById('quantity').value) || 1;
            let total = 0;

            if (m == 'N/A' || m == '' || m == 0) {
                total = price * quantity;
            } else {
                price = l * w * h * m;
                total = price * quantity;
            }

            totalPrice += total;
            const item = {
                itemCode: document.getElementById('itemCode').value,
                itemName: document.getElementById('itemName').value,
                unit: document.getElementById('unit').value,
                category: document.getElementById('category').value,
                weight: document.getElementById('weight').value,
                length: l,
                width: w,
                height: h,
                multiplier: m,
                price: price,
                description: document.getElementById('description').value,
                quantity: quantity,
                total: total
            };

            cart.push(item);
            updateCartTable();
            clearFields(); // Clear all fields after adding to cart
            closeModal();
        }

        function formatPrice(value) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
        }

        function updateCartTable() {
            const cartTableBody = document.getElementById('cartTableBody');
            cartTableBody.innerHTML = '';

            const cartDataInput = document.getElementById('cartData');
            cartDataInput.value = JSON.stringify(cart);

            document.getElementById('totalItems').textContent = formatPrice(totalPrice);
            document.getElementById('cartTotal').value = formatPrice(totalPrice);


            cart.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = "border-b";

                row.innerHTML = `
                    <td class="p-2 text-center">${item.quantity}</td>
                    <td class="p-2 text-center">${item.itemName}</td>
                    <td class="p-2 text-center">${item.category}</td>
                    <td class="p-2 text-center">${item.description} - Weight ${item.weight}</td>
                    <td class="p-2 text-center">${item.unit}</td>
                    `
                    if (item.multiplier == 'N/A') {
                        row.innerHTML += `
                        <td class="p-2 text-center">N/A</td>
                        `
                    }
                    else{
                        row.innerHTML += `
                        <td class="p-2 text-center">${item.length} × ${item.width} × ${item.height}</td>
                        `
                    }

                    row.innerHTML += `
                    <td class="p-2 text-center">${Number(item.multiplier).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="p-2 text-center">${Number(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="p-2 text-center">${Number(item.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td class="p-2 text-center">
                        <a href="#" class="text-blue-500 text-center" onclick="openEditModal(${index})">
                            <x-button variant="warning" class="items-center max-w-xs gap-2">
                                <x-heroicon-o-pencil class="w-6 h-6" aria-hidden="true" />
                            </x-button>
                        </a>
                    </td>
                    <td class="p-2 text-center">
                        <a href="#" class="text-blue-500 text-center" onclick="deleteItem(${index})">
                            <x-button variant="danger" class="items-center max-w-xs gap-2">
                                <x-heroicon-o-trash class="w-6 h-6" aria-hidden="true" />
                            </x-button>
                        </a>
                    </td>
                `;
                cartTableBody.appendChild(row);
            });
            toggleButtonsVisibility();
        }

        function deleteItem(index) {
            totalPrice -= cart[index].total;
            document.getElementById('totalItems').textContent = formatPrice(totalPrice);
            cart.splice(index, 1);
            updateCartTable();
        }

        function toggleButtonsVisibility() {
            const editButtons = document.querySelectorAll("[variant='warning']");
            const deleteButtons = document.querySelectorAll("[variant='danger']");

            const visibility = cart.length > 0 ? "" : "hidden";

            editButtons.forEach(btn => btn.style.visibility = visibility);
            deleteButtons.forEach(btn => btn.style.visibility = visibility);
        }

        function openEditModal(index) {
            document.getElementById('addToCart').classList.add('hidden'); // Hide Add to Cart
            document.getElementById('saveButton').classList.remove('hidden'); // Show Save Button

            const item = cart[index];

            document.getElementById('itemCode').value = item.itemCode;
            document.getElementById('itemName').value = item.itemName;
            document.getElementById('unit').value = item.unit;
            document.getElementById('category').value = item.category;
            document.getElementById('weight').value = item.weight;
            document.getElementById('length').value = item.length;
            document.getElementById('width').value = item.width;
            document.getElementById('height').value = item.height;
            document.getElementById('multiplier').value = item.multiplier;
            document.getElementById('price').value = item.price;
            document.getElementById('description').value = item.description;
            document.getElementById('quantity').value = item.quantity;
            edit = item.total;
            // Store the index for updating later
            document.getElementById('saveButton').setAttribute('data-index', index);

            // Show the modal
            document.getElementById('addToCartModal').classList.remove('hidden');
        }

        // Function to save the updated data
        function saveUpdatedItem() {
            document.getElementById('addToCart').classList.remove('hidden'); // Show Add to Cart
            document.getElementById('saveButton').classList.add('hidden'); // Hide Save Button
            const currentEditIndex = document.getElementById('saveButton').getAttribute('data-index'); // Get stored index

            if (currentEditIndex !== null) {
                let l = parseFloat(document.getElementById('length').value) || 1;
                let w = parseFloat(document.getElementById('width').value) || 1;
                let h = parseFloat(document.getElementById('height').value) || 1;
                let m = parseFloat(document.getElementById('multiplier').value) || 'N/A';
                let price = parseFloat(document.getElementById('price').value); // Ensure price is a number
                let quantity = parseFloat(document.getElementById('quantity').value) || 1;
                let total = 0;
                if (m == 'N/A' || m == '' || m == 0) {
                total = price * quantity;
                }
                else if (m !=  '' || m != '' || m != 0){
                    price = l * w * h * m;
                    total = price * quantity;
                }
                else {
                }
                totalPrice -= edit;
                totalPrice += total;
                document.getElementById('totalItems').textContent = formatPrice(totalPrice);

                cart[currentEditIndex] = {
                    itemCode: document.getElementById('itemCode').value,
                    itemName: document.getElementById('itemName').value,
                    unit: document.getElementById('unit').value,
                    category: document.getElementById('category').value,
                    weight: document.getElementById('weight').value,
                    length: l,
                    width: w,
                    height: h,
                    multiplier: m,
                    price: price,
                    description: document.getElementById('description').value,
                    quantity: quantity,
                    total: total
                };

                // Update the displayed table with new data
                updateCartTable();
                closeModal();
            }
        }

        // Populate form fields on row click
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.getElementById('itemTableBody');

            tableBody.addEventListener('click', (event) => {
                const row = event.target.closest('tr');
                if (!row) return;

                // Extract data from the clicked row
                const cells = row.getElementsByTagName('td');
                const itemCode = cells[0].textContent.trim();
                const itemName = cells[1].textContent.trim();
                const category = cells[2].textContent.trim();
                const price = cells[3].textContent.trim();
                const multiplier = cells[4].textContent.trim();

                // Populate the input fields
                document.getElementById('itemCode').value = itemCode;
                document.getElementById('itemName').value = itemName;
                document.getElementById('category').value = category;
                document.getElementById('price').value = price;
                document.getElementById('multiplier').value = multiplier;

            });
        });

        // Search function
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('#itemTableBody .item-row');
            searchInput.addEventListener('keyup', () => {
                const query = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    const itemCode = row.children[0].textContent.toLowerCase();
                    const itemName = row.children[1].textContent.toLowerCase();
                    const category = row.children[2].textContent.toLowerCase();

                    // Check if search matches any column
                    if (itemCode.includes(query) || itemName.includes(query) || category.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Populate form on row click
            document.getElementById('itemTableBody').addEventListener('click', (event) => {
                const row = event.target.closest('tr');
                if (!row) return;

                document.getElementById('itemCode').value = row.children[0].textContent.trim();
                document.getElementById('itemName').value = row.children[1].textContent.trim();
                document.getElementById('category').value = row.children[2].textContent.trim();
                document.getElementById('price').value = row.children[3].textContent.trim();
            });
        });

        // JavaScript for Filtering
        document.getElementById("categoryFilter").addEventListener("change", function() {
            let selectedCategory = this.value.toLowerCase();
            document.querySelectorAll("#itemTableBody .item-row").forEach(row => {
                let rowCategory = row.getAttribute("data-category").toLowerCase();
                if (selectedCategory === "" || rowCategory === selectedCategory) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shipNoSelect = document.getElementById('ship_no');
            const voyageNoInput = document.getElementById('voyage_no');
            const voyageSelectOrder = document.getElementById('voyage_select_order');
            const selectedVoyageGroupInput = document.getElementById('selected_voyage_group');
            const voyageButtons = document.getElementById('voyageButtons');
            const originSelect = document.getElementById('origin');
            const destinationSelect = document.getElementById('destination');

            // Function to fetch and update voyages
            function updateVoyageOptions() {
                const selectedShip = shipNoSelect.value;
                const selectedOrigin = originSelect ? originSelect.value : '';
                const selectedDestination = destinationSelect ? destinationSelect.value : '';

                if (selectedShip) {
                    let apiUrl = `/api/available-voyages/${selectedShip}`;
                    let params = new URLSearchParams();
                    
                    if (selectedOrigin) params.append('origin', selectedOrigin);
                    if (selectedDestination) params.append('destination', selectedDestination);
                    
                    if (params.toString()) {
                        apiUrl += '?' + params.toString();
                    }

                    // Fetch available voyages for the selected ship
                    fetch(apiUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const voyages = data.voyages;
                                
                                if (voyages.length === 1) {
                                    // Single voyage - use existing behavior
                                    const voyage = voyages[0];
                                    
                                    if (voyageNoInput) {
                                        voyageNoInput.value = voyage.voyage_number;
                                        voyageNoInput.style.display = 'block';
                                    }
                                    
                                    if (voyageSelectOrder) {
                                        voyageSelectOrder.style.display = 'none';
                                    }
                                    
                                    if (selectedVoyageGroupInput) {
                                        selectedVoyageGroupInput.value = voyage.voyage_group || '';
                                    }
                                } else if (voyages.length > 1) {
                                    // Multiple voyages - show dropdown
                                    if (voyageNoInput) {
                                        voyageNoInput.style.display = 'none';
                                    }
                                    
                                    if (voyageSelectOrder) {
                                        // Clear existing options
                                        voyageSelectOrder.innerHTML = '<option value="">Select Voyage</option>';
                                        
                                        // Add voyage options
                                        voyages.forEach(voyage => {
                                            const option = document.createElement('option');
                                            option.value = voyage.voyage_number;
                                            option.textContent = voyage.label;
                                            option.setAttribute('data-voyage-group', voyage.voyage_group || '');
                                            
                                            // Highlight matching route voyages
                                            if (voyage.matches_route) {
                                                option.style.fontWeight = 'bold';
                                                option.style.color = '#059669'; // Green color for matching routes
                                            }
                                            
                                            voyageSelectOrder.appendChild(option);
                                        });
                                        
                                        voyageSelectOrder.style.display = 'block';
                                        
                                        // Auto-select the first matching route if available
                                        const matchingVoyage = voyages.find(v => v.matches_route);
                                        if (matchingVoyage && selectedOrigin && selectedDestination) {
                                            voyageSelectOrder.value = matchingVoyage.voyage_number;
                                            
                                            // Trigger change event to update hidden fields
                                            voyageSelectOrder.dispatchEvent(new Event('change'));
                                        }
                                    }
                                } else {
                                    // No voyages available
                                    if (voyageNoInput) {
                                        voyageNoInput.value = 'No voyages available';
                                        voyageNoInput.style.display = 'block';
                                    }
                                    
                                    if (voyageSelectOrder) {
                                        voyageSelectOrder.style.display = 'none';
                                    }
                                }
                            } else {
                                console.error('Error fetching voyages:', data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching voyages:', error);
                        });
                }

                // Handle IN/OUT buttons for Ships I and II
                if (selectedShip === 'I' || selectedShip === 'II') {
                    // Show buttons for IN and OUT
                    voyageButtons.classList.remove('hidden');
                } else {
                    // Hide buttons for other ships
                    voyageButtons.classList.add('hidden');
                    // Remove the suffix logic
                    voyageNoInput.removeEventListener('input', () => {});
                }
            }

            // Event listeners
            shipNoSelect.addEventListener('change', updateVoyageOptions);
            
            if (originSelect) {
                originSelect.addEventListener('change', updateVoyageOptions);
            }
            
            if (destinationSelect) {
                destinationSelect.addEventListener('change', updateVoyageOptions);
            }

            // Handle voyage selection change for dropdown
            if (voyageSelectOrder) {
                voyageSelectOrder.addEventListener('change', function() {
                    const selectedVoyage = this.value;
                    const selectedOption = this.options[this.selectedIndex];
                    const voyageGroup = selectedOption.getAttribute('data-voyage-group') || '';
                    
                    if (selectedVoyage) {
                        if (voyageNoInput) {
                            voyageNoInput.value = selectedVoyage;
                        }
                        
                        if (selectedVoyageGroupInput) {
                            selectedVoyageGroupInput.value = voyageGroup;
                        }
                    }
                });
            }

            // Function to set the voyage suffix
            window.setVoyageSuffix = function (suffix) {
                const currentVoyage = voyageSelectOrder && voyageSelectOrder.style.display !== 'none' 
                    ? voyageSelectOrder.value 
                    : voyageNoInput.value.trim();
                    
                const voyageValue = currentVoyage.replace(/ - (IN|OUT)$/, '');
                const newVoyageValue = voyageValue + ' - ' + suffix;
                
                if (voyageSelectOrder && voyageSelectOrder.style.display !== 'none') {
                    // Update the selected option in dropdown
                    const selectedOption = voyageSelectOrder.options[voyageSelectOrder.selectedIndex];
                    if (selectedOption) {
                        selectedOption.value = newVoyageValue;
                        selectedOption.textContent = selectedOption.textContent.replace(/ - (IN|OUT)$/, '') + ' - ' + suffix;
                        voyageSelectOrder.value = newVoyageValue;
                    }
                }
                
                voyageNoInput.value = newVoyageValue;
            };
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkerSelect = document.getElementById("checker");
            const addCheckerBtn = document.getElementById("addChecker");
            const deleteCheckerBtn = document.getElementById("deleteChecker");

            // Checker options based on origin
            const checkerOptions = {
                "MANILA": ["", "ALDAY", "ANCHETA", "BERNADOS", "CACHO", "ESGUERRA", "MORENO", "VICTORIANO", "YUMUL", "ZERRUDO"],
                "BATANES": ["", "SOL", "TIRSO", "VARGAS", "NICK", "JOSIE", "JEN"]
            };

            function populateCheckerOptions(origin) {
                checkerSelect.innerHTML = ""; // Clear existing options
                let options = checkerOptions[origin] || [];
                options.forEach(option => {
                    let opt = document.createElement("option");
                    opt.value = option;
                    opt.textContent = option;
                    checkerSelect.appendChild(opt);
                });
            }

            function updateCheckerOptions() {
                let origin = document.getElementById("origin")?.value?.toUpperCase();
                if (origin && checkerOptions[origin]) {
                    populateCheckerOptions(origin);
                }
            }

            // Update options when origin changes
            document.getElementById("origin")?.addEventListener("change", updateCheckerOptions);

            // Load default options on page load
            updateCheckerOptions();
        });
    </script>

    <script>
        function formatNumberInput(input) {
            // Remove non-numeric characters except the decimal point
            let value = input.value.replace(/,/g, '');
            if (!isNaN(value) && value !== '') {
                let parts = value.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ","); // Add commas for thousands
                input.value = parts.join('.'); // Rejoin integer and decimal parts
            }
        }

        // Ensure raw value is submitted without commas
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('valuation').value = document.getElementById('valuation').value.replace(/,/g, '');
            document.getElementById('other').value = document.getElementById('other').value.replace(/,/g, '');
        });
    </script>

    <script>
        // For add new Unit button
        document.getElementById('addUnit').addEventListener('click', function () {
            let unitSelect = document.getElementById('unit');
            let newUnit = prompt("Enter new unit:");

            if (newUnit) {
                newUnit = newUnit.toUpperCase();

                // Add to local storage
                let storedUnits = JSON.parse(localStorage.getItem("customUnits")) || [];
                if (!storedUnits.includes(newUnit)) {
                    storedUnits.push(newUnit);
                    localStorage.setItem("customUnits", JSON.stringify(storedUnits));
                }

                // Add to dropdown
                let option = document.createElement('option');
                option.value = newUnit;
                option.text = newUnit;
                unitSelect.appendChild(option);
                unitSelect.value = newUnit;
            }
        });

        // Load saved units from local storage on page load
        document.addEventListener("DOMContentLoaded", function () {
            let unitSelect = document.getElementById('unit');
            let storedUnits = JSON.parse(localStorage.getItem("customUnits")) || [];

            storedUnits.forEach(unit => {
                let option = document.createElement('option');
                option.value = unit;
                option.text = unit;
                unitSelect.appendChild(option);
            });
        });

        // Function to Clear All Input Fields
        function clearFields() {
            document.getElementById('itemCode').value = "";
            document.getElementById('itemName').value = "";
            document.getElementById('unit').value = "";
            document.getElementById('category').value = "";
            document.getElementById('weight').value = "";
            document.getElementById('length').value = "";
            document.getElementById('width').value = "";
            document.getElementById('height').value = "";
            document.getElementById('multiplier').value = "";
            document.getElementById('price').value = "";
            document.getElementById('quantity').value = "";
            document.getElementById('description').value = "";
        }
    </script>

    <!-- LOCATION FUNCTION -->
    @if (Auth::user()->roles->roles == 'Admin')
        <script>
            function openLocationModal() {
                let modal = document.getElementById("locationModal");
                modal.classList.add("show");
                modal.classList.remove("hidden");

                // Disable inputs outside modal
                document.querySelectorAll("input, select, button").forEach(el => {
                    if (!modal.contains(el)) {
                        el.disabled = true;
                    }
                });
            }

            function closeLocationModal() {
                let modal = document.getElementById("locationModal");
                modal.classList.remove("show");
                modal.classList.add("hidden");

                // Enable inputs outside modal
                document.querySelectorAll("input, select, button").forEach(el => {
                    if (!modal.contains(el)) {
                        el.disabled = false;
                    }
                });
            }

            // Function to add location via AJAX request
            function addLocation() {
                const locationInput = document.getElementById("newLocation").value.trim();

                if (locationInput) {
                    // Send location to backend
                    fetch('/add-location/' + encodeURIComponent(locationInput), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update dropdowns with new data
                        updateDropdowns(data);

                        // Clear input and close modal
                        document.getElementById("newLocation").value = "";
                        closeLocationModal();
                    })
                    .catch(error => console.error('Error:', error));
                }
            }

            // Helper function to update Origin and Destination dropdowns
            function updateDropdowns(locations) {
                const originSelect = document.getElementById("origin");
                const destinationSelect = document.getElementById("destination");

                // Clear existing options
                originSelect.innerHTML = "";
                destinationSelect.innerHTML = "";

                // Add updated locations to dropdowns
                locations.forEach(location => {
                    let option = new Option(location.location, location.location);
                    originSelect.add(option);
                    destinationSelect.add(new Option(location.location, location.location));
                });
            }
        </script>

        <script>
            // For Checker based on the Origin
            document.addEventListener("DOMContentLoaded", function () {
                const checkerSelect = document.getElementById("checker");
                const addCheckerBtn = document.getElementById("addChecker");
                const deleteCheckerBtn = document.getElementById("deleteChecker");

                // Checker options based on origin
                const checkerOptions = {
                    "MANILA": ["", "ALDAY", "ANCHETA", "BERNADOS", "CACHO", "ESGUERRA", "MORENO", "VICTORIANO", "YUMUL", "ZERRUDO"],
                    "BATANES": ["", "SOL", "TIRSO", "VARGAS", "NICK", "JOSIE", "JEN"]
                };

                function getSavedCheckers(origin) {
                    return JSON.parse(localStorage.getItem(`checkers_${origin}`)) || [];
                }

                function saveChecker(origin, name) {
                    let savedCheckers = getSavedCheckers(origin);
                    if (!savedCheckers.includes(name)) {
                        savedCheckers.push(name);
                        localStorage.setItem(`checkers_${origin}`, JSON.stringify(savedCheckers));
                    }
                }

                function deleteChecker(origin, name) {
                    let savedCheckers = getSavedCheckers(origin);
                    let updatedCheckers = savedCheckers.filter(checker => checker !== name);
                    localStorage.setItem(`checkers_${origin}`, JSON.stringify(updatedCheckers));
                }

                function populateCheckerOptions(origin) {
                    checkerSelect.innerHTML = ""; // Clear existing options
                    let options = (checkerOptions[origin] || []).concat(getSavedCheckers(origin));
                    options.forEach(option => {
                        let opt = document.createElement("option");
                        opt.value = option;
                        opt.textContent = option;
                        checkerSelect.appendChild(opt);
                    });
                }

                function updateCheckerOptions() {
                    let origin = document.getElementById("origin")?.value?.toUpperCase();
                    if (origin && checkerOptions[origin]) {
                        populateCheckerOptions(origin);
                    }
                }

                // Add a new checker manually
                addCheckerBtn.addEventListener("click", function () {
                    let origin = document.getElementById("origin")?.value?.toUpperCase();
                    if (!origin || !checkerOptions[origin]) {
                        alert("Please select a valid origin first.");
                        return;
                    }

                    let newChecker = prompt("Enter new checker name:");
                    if (newChecker) {
                        newChecker = newChecker.toUpperCase();
                        saveChecker(origin, newChecker);
                        populateCheckerOptions(origin);
                        checkerSelect.value = newChecker; // Select the newly added checker
                    }
                });

                // Delete selected checker
                deleteCheckerBtn.addEventListener("click", function () {
                    let origin = document.getElementById("origin")?.value?.toUpperCase();
                    let selectedChecker = checkerSelect.value;

                    if (!origin || !checkerOptions[origin]) {
                        alert("Please select a valid origin first.");
                        return;
                    }

                    if (!selectedChecker) {
                        alert("Please select a checker to delete.");
                        return;
                    }

                    let defaultCheckers = checkerOptions[origin] || [];

                    if (defaultCheckers.includes(selectedChecker)) {
                        alert("Cannot delete default checkers.");
                        return;
                    }

                    if (confirm(`Are you sure you want to delete "${selectedChecker}"?`)) {
                        deleteChecker(origin, selectedChecker);
                        populateCheckerOptions(origin);
                    }
                });

                // Update options when origin changes
                document.getElementById("origin")?.addEventListener("change", updateCheckerOptions);

                // Load default options on page load
                updateCheckerOptions();
            });
        </script>
    @endif
</x-app-layout>
<style>
    .bg-emerald-600 {
        background-color: #059669 !important; /* Emerald-600 Hex Code */
    }

    #locationModal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5); /* Dark background */
        z-index: 50; /* Ensure it appears on top */
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none; /* Prevent clicks when hidden */
        opacity: 0; /* Make it fully hidden */
        transition: opacity 0.3s ease-in-out;
    }

    #locationModal.show {
        pointer-events: auto; /* Enable clicks when shown */
        opacity: 1; /* Make it fully visible */
    }
</style>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
