<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight flex items-center gap-2">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __(' M/V Everwin Star Master List') }}
            </h2>
            <div class="flex gap-2">
                <!-- Export to Excel Button -->
                <button id="exportExcel" class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-700">
                    Export to Excel
                </button>
                <!-- Export to PDF Button -->
                <button id="exportPdf" class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-700">
                    Export to PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1 w-full overflow-hidden">
        <h1 class="text-lg font-bold text-center md:text-left">MASTER LIST FOR M/V EVERWIN STAR {{ $shipNum }} VOYAGE {{ $voyageNum }}</h1>
        <br>
        <!-- Orders Table -->
        <div class="table-container">
            <table id="ordersTable" class="table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-200 dark:bg-dark-eval-0">
                    <tr>
                        <th class="p-2 cursor-pointer" id="blHeader" style="position: relative;">
                            BL <span id="blSortIndicator" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">▲</span>
                        </th>
                        <th class="p-2">DATE</th>
                        <th class="p-2">CONTAINER</th>
                        <th class="p-2">CARGO STATUS</th>
                        <th class="p-2 cursor-pointer" id="shipperHeader" style="position: relative;">
                            SHIPPER <span id="shipperSortIndicator" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); display: none;">▲</span>
                        </th>
                        <th class="p-2 cursor-pointer" id="consigneeHeader" style="position: relative;">
                            CONSIGNEE <span id="consigneeSortIndicator" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); display: none;">▲</span>
                        </th>
                        <th class="p-2">CHECKER</th>
                        <th class="p-2">DESCRIPTION</th>
                        <th class="p-2">FREIGHT</th>
                        <!--th class="p-2">ORIGINAL FREIGHT</th-->
                        <th class="p-2">VALUATION</th>
                        <th class="p-2">VALUE</th>
                        <th class="p-2">WHARFAGE</th>
                        <th class="p-2">5% DISCOUNT</th>
                        <th class="p-2">BIR (2307)</th>
                        <th class="p-2">OTHERS</th>
                        <th class="p-2">TOTAL</th>
                        <th class="p-2">OR#</th>
                        <th class="p-2">AR#</th>
                        <th class="p-2">DATE PAID</th>
                        <th class="p-2">NOTED BY</th>
                        <th class="p-2">PAID IN</th>
                        <th class="p-2">BL STATUS</th>
                        <th class="p-2">REMARK</th>
                        <th class="p-2">NOTE</th>
                        <th class="p-2" style="text-align: center;">IMAGE</th>
                        <th class="p-2" style="width: 80px; text-align: center;">VIEW BL</th>
                        <th class="p-2" style="width: 100px; text-align: center;">NO-PRICE BL</th>
                        @if(Auth::user()->hasSubpagePermission('masterlist', 'edit-bl', 'edit') || Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                        <th class="p-2 update-bl-column">UPDATE BL</th>
                        @endif
                        @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'delete'))
                        <th class="p-2 delete-bl-column">DELETE BL</th>
                        @endif
                        <th hidden class="p-2">CREATED BY</th>
                    </tr>
                    <tr>
                        <th>
                            <select class="filter-dropdown" data-column="bl">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueOrderIds'] ?? []) as $bl)
                                    <option value="{{ $bl }}">{{ $bl }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th></th> <!-- DATE excluded -->
                        <th>
                            <select class="filter-dropdown" data-column="container">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueContainers'] ?? []) as $container)
                                    <option value="{{ $container }}">{{ $container }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="cargo_status">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueCargoTypes'] ?? []) as $cargoStatus)
                                    <option value="{{ $cargoStatus }}">{{ $cargoStatus }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="shipper">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueShippers'] ?? []) as $shipper)
                                    <option value="{{ $shipper }}">{{ $shipper }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="consignee">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueConsignees'] ?? []) as $consignee)
                                    <option value="{{ $consignee }}">{{ $consignee }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="checker">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueCheckers'] ?? []) as $checker)
                                    <option value="{{ $checker }}">{{ $checker }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <div class="flex flex-col gap-1">
                                <input type="text" class="filter-input mb-1" data-column="description" placeholder="Search description...">
                                <select class="filter-dropdown" data-column="description">
                                    <option value="">All</option>
                                    @foreach(($filterData['uniqueItemNames'] ?? []) as $itemName)
                                        <option value="{{ $itemName }}">{{ $itemName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </th>
                        <th></th> <!-- FREIGHT excluded -->
                        <!--th></th--> <!-- ORIGINAL FREIGHT excluded -->
                        <th></th> <!-- VALUATION excluded -->
                        <th></th> <!-- VALUE excluded -->
                        <th></th> <!-- WHARFAGE excluded -->
                        <th></th> <!-- 5% DISCOUNT excluded -->
                        <th></th> <!-- BIR excluded -->
                        <th></th> <!-- OTHERS excluded -->
                        <th></th> <!-- TOTAL excluded -->
                        <th>
                            <select class="filter-dropdown" data-column="or">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueORs'] ?? []) as $or)
                                    <option value="{{ $or }}">{{ $or }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="ar">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueARs'] ?? []) as $ar)
                                    <option value="{{ $ar }}">{{ $ar }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th><input type="text" class="filter-input" data-column="dp" placeholder="Date Paid"></th>
                        <th>
                            <select class="filter-dropdown" data-column="updated_by">
                                <option value="">All</option>
                                @foreach(($filterData['uniqueUpdatedBy'] ?? []) as $updatedBy)
                                    <option value="{{ $updatedBy }}">{{ $updatedBy }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="updated_location">
                                <option value="">All</option>
                                @foreach($orders->pluck('updated_location')->unique()->sort() as $updated_location)
                                    <option value="{{ $updated_location }}">{{ $updated_location }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="bl_status">
                                <option value="">All</option>
                                @foreach($orders->pluck('blStatus')->unique()->sort() as $blStatus)
                                    <option value="{{ $blStatus }}">{{ $blStatus }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="bl_remark">
                                <option value="">All</option>
                                @foreach($orders->pluck('remark')->unique()->sort() as $blRemark)
                                    <option value="{{ $blRemark }}">{{ $blRemark }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <select class="filter-dropdown" data-column="note">
                                <option value="">All</option>
                                @foreach($orders->pluck('note')->unique()->sort() as $note)
                                    <option value="{{ $note }}">{{ $note }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th></th> <!-- IMAGE excluded -->
                        <th></th> <!-- VIEW BL excluded -->
                        <th></th> <!-- VIEW NO-PRICE BL -->
                        <th></th> <!-- UPDATE BL -->
                        <th></th> <!-- DELETE BL -->
                        <th></th> <!-- ORIGINAL FREIGHT excluded -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr class="border-b">
                            <td class="p-2" data-column="bl">{{ $order->orderId }}</td>
                            <td class="p-2">{{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y') }}</td>
                            <td class="p-2 container-cell" data-column="container">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <textarea 
                                    class="container-textarea"
                                    data-order-id="{{ $order->id }}"
                                    style="border: none; width: 100%; min-height: 40px; text-align: center; background: transparent; word-wrap: break-word; white-space: normal; resize: none; overflow: hidden; padding: 5px; vertical-align: top;"
                                >{{ $order->containerNum }}</textarea>
                                @else
                                <div style="width: 100%; min-height: 40px; display: flex; align-items: center; justify-content: center; text-align: center; word-wrap: break-word; white-space: normal; padding: 5px;">
                                    {{ $order->containerNum }}
                                </div>
                                @endif
                            </td>
                            <td class="p-2 cargo-status-cell" data-column="cargo_status">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <textarea 
                                    class="cargo-status-textarea"
                                    data-order-id="{{ $order->id }}"
                                    style="border: none; width: 100%; min-height: 40px; text-align: center; background: transparent; word-wrap: break-word; white-space: normal; resize: none; overflow: hidden; padding: 5px; vertical-align: top;"
                                >{{ $order->cargoType }}</textarea>
                                @else
                                <div style="width: 100%; min-height: 40px; display: flex; align-items: center; justify-content: center; text-align: center; word-wrap: break-word; white-space: normal; padding: 5px;">
                                    {{ $order->cargoType }}
                                </div>
                                @endif
                            </td>
                            <td class="p-2" data-column="shipper">{{ $order->shipperName }}</td>
                            <td class="p-2" data-column="consignee">{{ $order->recName }}</td>
                            <td class="p-2 checker-cell" data-column="checker">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <textarea 
                                    class="checker-textarea"
                                    data-order-id="{{ $order->id }}"
                                    style="border: none; width: 100%; min-height: 40px; text-align: center; background: transparent; word-wrap: break-word; white-space: normal; resize: none; overflow: hidden; padding: 5px; vertical-align: top;"
                                >{{ $order->checkName ?? '' }}</textarea>
                                @else
                                <div style="width: 100%; min-height: 40px; display: flex; align-items: center; justify-content: center; text-align: center; word-wrap: break-word; white-space: normal; padding: 5px;">
                                    {{ $order->checkName }}
                                </div>
                                @endif
                            </td>
                            <td class="p-2" data-column="description">
                                @if ($order->parcels->isNotEmpty())
                                    @foreach ($order->parcels as $parcel)
                                        <span>{{ $parcel->quantity }} {{ $parcel->unit }} - {{ $parcel->itemName }}@if(!empty(trim($parcel->desc))) - {{$parcel->desc}}@endif</span><br>
                                    @endforeach
                                @else
                                    <span>No parcels</span>
                                @endif
                            </td>
                            <td class="freight text-right" data-column="freight">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input style="width: 100px; border: none; outline: none; text-align:center;" 
                                       class="freight-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"  
                                       data-order-id="{{ $order->id }}"  
                                       value="{{ number_format($order->freight, 2) }}"  
                                       placeholder="Enter Freight"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ number_format($order->freight, 2) }}
                                </span>
                                @endif
                            </td>
                            <!--td class="p-2" data-column="bl_status">{{ number_format($order->freight, 2) }}</td-->
                            <!--td class="original-freight text-right" data-column="originalFreight">
                                <input style="width: 100px; border: none; outline: none; text-align:center;" 
                                       class="original-freight-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"  
                                       data-order-id="{{ $order->id }}"  
                                       value="{{ number_format($order->originalFreight, 2) }}"  
                                       placeholder="Enter Original Freight"/>
                            </td-->
                            <td class="p-2" data-column="valuation">{{ $order->valuation ? number_format($order->valuation, 2) : ' ' }}</td>
                            <td class="value text-right" data-column="value">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input style="width: 100px; border: none; outline: none; text-align:center;" 
                                       class="value-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"  
                                       data-order-id="{{ $order->id }}"  
                                       value="{{ number_format($order->value, 2) }}"  
                                       placeholder="Enter Value"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ number_format($order->value, 2) }}
                                </span>
                                @endif
                            </td>
                            <td class="wharfage text-right" data-column="wharfage">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input style="width: 100px; border: none; outline: none; text-align:center;"
                                       class="wharfage-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"
                                       data-order-id="{{ $order->id }}"
                                       value="{{ number_format($order->wharfage, 2) }}"
                                       placeholder="Enter Wharfage"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ number_format($order->wharfage, 2) }}
                                </span>
                                @endif
                            </td>
                            <td class="discount text-right" data-column="discount">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input style="width: 100px; border: none; outline: none; text-align:center;" 
                                       class="discount-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"  
                                       data-order-id="{{ $order->id }}"  
                                       value="{{ number_format($order->discount, 2) }}"  
                                       placeholder="Enter Discount"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ number_format($order->discount, 2) }}
                                </span>
                                @endif
                            </td>
                            <td class="p-2" data-column="bir">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input type="text" style="width: 100px; border: none; outline: none; text-align:center;" class="bir-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"  
                                       data-order-id="{{ $order->id }}"  value="{{ number_format($order->bir, 2) }}"  placeholder="Enter BIR"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ number_format($order->bir, 2) }}
                                </span>
                                @endif
                            </td>
                            <td class="p-2" data-column="other">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input type="text" 
                                       style="width: 100px; border: none; outline: none; text-align:center;" 
                                       class="other-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white"  
                                       data-order-id="{{ $order->id }}"  
                                       value="{{ number_format($order->other, 2) }}"  
                                       placeholder="Enter Others"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ number_format($order->other, 2) }}
                                </span>
                                @endif
                            </td>
                            <td class="total text-right" data-column="total">{{ number_format($order->totalAmount, 2) }}</td>
                            <td class="p-2" data-column="or">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input type="text" style="width: 100px; border: none; outline: none;" class="or-input p-2 border rounded" 
                                    data-order-id="{{ $order->id }}"  value="{{ $order->OR }}"  placeholder="Enter OR#"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ $order->OR }}
                                </span>
                                @endif
                            </td>
                            <td class="p-2" data-column="ar">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <input type="text" style="width: 100px; border: none; outline: none;" class="ar-input p-2 border rounded" 
                                    data-order-id="{{ $order->id }}" value="{{ $order->AR }}" placeholder="Enter AR#"/>
                                @else
                                <span style="width: 100px; text-align:center; display: inline-block;">
                                    {{ $order->AR }}
                                </span>
                                @endif
                            </td>
                            <td class="p-2" data-column="dp">{{ $order->or_ar_date ? \Carbon\Carbon::parse($order->or_ar_date)->format('F d, Y h:i A') : ' ' }}</td>
                            <td class="p-2" data-column="updated_by">{{ $order->updated_by ?? ' ' }}</td>
                            <td class="p-2" data-column="updated_location">{{ $order->updated_location ?? ' ' }}</td>
                            <td class="p-2" data-column="bl_status">{{ $order->blStatus }}</td>
                            <td class="p-2 remark-cell" data-column="bl_remark">
                                @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                <textarea 
                                    class="remark-textarea"
                                    data-order-id="{{ $order->id }}"
                                    style="border: none; width: 100%; min-height: 40px; display: flex; align-items: center; justify-content: center; text-align: center; background: transparent; word-wrap: break-word; white-space: normal; resize: none; overflow: hidden; padding: 5px;"
                                >{{ $order->remark }}</textarea>
                                @else
                                <div style="width: 100%; display: flex; align-items: center; justify-content: center; text-align: center; word-wrap: break-word; white-space: normal; padding: 5px;">
                                    {{ $order->remark }}
                                </div>
                                @endif
                            </td>
                            <!--td class="p-2" data-column="bl_remark">
                                <input type="text" 
                                       class="remark-input p-2 border rounded bg-white text-black dark:bg-gray-700 dark:text-white" 
                                       style="width: 100%; border: none; outline: none; text-align:center;" 
                                       data-order-id="{{ $order->id }}" 
                                       value="{{ $order->remark }}" 
                                       placeholder="Enter remark"/>
                            </td-->
                            <td class="p-2" data-column="note"><textarea class="note-input w-full border rounded-md p-2" style="border: none; outline: none;" data-order-id="{{ $order->id }}" placeholder="Type your note here...">{{ $order->note }}</textarea></td>
                            <td class="p-2 text-center" style="width: 150px;">
                                @if($order->image)
                                    <!-- View Image Button -->
                                    <button type="button" 
                                            class="w-full px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700"
                                            onclick="openModal('{{ asset('storage/' . $order->image) }}')"
                                            title="Image: {{ $order->image }}">
                                        🖼️ View Image
                                    </button>

                                    @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                    <!-- Change Image and Remove Image Buttons -->
                                    <div class="flex justify-between items-center gap-2 mt-2"> <!-- Added mt-2 for top margin -->
                                        <form action="{{ url('/update-order-field/' . $order->id) }}" 
                                            method="POST" 
                                            enctype="multipart/form-data" 
                                            id="changeForm-{{ $order->id }}"
                                            class="image-change-form">
                                            @csrf
                                            <input type="hidden" name="field" value="image">
                                            <input type="file" name="image" 
                                                class="hidden change-input"
                                                data-order-id="{{ $order->id }}"
                                                id="fileInput-{{ $order->id }}">
                                            <button type="button" 
                                                    class="px-2 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-sm"
                                                    onclick="document.getElementById('fileInput-{{ $order->id }}').click()">
                                                Change
                                            </button>
                                        </form>
                                        <button type="button" 
                                                class="px-2 py-2 bg-red-500 text-white rounded hover:bg-red-700 text-sm"
                                                onclick="removeImage({{ $order->id }})">
                                            Remove
                                        </button>
                                    </div>
                                    @endif
                                @else
                                    @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                                    <!-- Upload Image Form -->
                                    <div class="w-full">
                                        <!-- For NEW image upload -->
                                        <form action="{{ url('/update-order-field/' . $order->id) }}" 
                                            method="POST" 
                                            enctype="multipart/form-data" 
                                            id="uploadForm-{{ $order->id }}"
                                            class="image-upload-form">
                                            @csrf
                                            <input type="hidden" name="field" value="image">
                                            <input type="file" name="image" 
                                                class="p-2 border rounded w-full upload-input"
                                                data-order-id="{{ $order->id }}">
                                            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 mt-2">
                                                Upload
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-gray-500">No image</span>
                                    @endif
                                @endif
                            </td>
                            <td class="p-2 text-center">
                                <a href="{{ route('masterlist.view-bl', ['shipNum' => $order->shipNum, 'voyageNum' => $order->voyageNum, 'orderId' => $order->id]) }}" class="text-blue-500 text-center">
                                    <x-button variant="primary" class="items-center max-w-xs gap-2">
                                        <x-heroicon-o-document class="w-6 h-6" aria-hidden="true" />
                                    </x-button>
                                </a>
                            </td>
                            <td class="p-2 text-center">
                                <a href="{{ route('masterlist.view-no-price-bl', ['shipNum' => $order->shipNum, 'voyageNum' => $order->voyageNum, 'orderId' => $order->id]) }}" class="text-green-500 text-center">
                                    <x-button variant="success" class="items-center max-w-xs gap-2">
                                        <x-heroicon-o-document-text class="w-6 h-6" aria-hidden="true" />
                                    </x-button>
                                </a>
                            </td>
                            @if(Auth::user()->hasSubpagePermission('masterlist', 'edit-bl', 'edit') || Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit'))
                            <td class="p-2 text-center update-bl-column">
                                <a href="{{ route('masterlist.edit-bl', $order->id) }}" class="text-blue-500 hover:underline flex items-center justify-center gap-2">
                                    <x-button variant="warning" class="items-center max-w-xs gap-2">
                                        <x-heroicon-o-pencil-alt class="w-6 h-6" aria-hidden="true" />
                                    </x-button>
                                </a>
                            </td>
                            @endif
                            @if(Auth::user()->hasSubpagePermission('masterlist', 'list', 'delete'))
                            <td class="p-2 text-center delete-bl-column">
                                <form action="{{ route('masterlist.delete-order', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order and all associated parcels?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline flex items-center justify-center gap-2">
                                        <x-button variant="danger" class="items-center max-w-xs gap-2">
                                            <x-heroicon-o-trash class="w-6 h-6" aria-hidden="true" />
                                        </x-button>
                                    </button>
                                </form>
                            </td>
                            @endif
                            <td hidden class="p-2">{{ $order->creator }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="text-right font-bold">Overall Total:</td>
                        <td id="totalFreight" class="font-bold text-right">0.00</td>
                        <td id="totalOriginalFreight" class="font-bold text-right" hidden>0.00</td>
                        <td id="totalValuation" class="font-bold text-right">0.00</td>
                        <td id="totalValue" class="font-bold text-right">0.00</td>
                        <td id="totalWharfage" class="font-bold text-right">0.00</td>
                        <td id="totalDiscount" class="font-bold text-right">0.00</td>
                        <td id="totalBir" class="font-bold text-right">0.00</td>
                        <td id="totalOthers" class="font-bold text-right">0.00</td>
                        <td id="totalAmount" class="font-bold text-right">0.00</td>
                        <td colspan="{{ 11 + ((Auth::user()->hasSubpagePermission('masterlist', 'edit-bl', 'edit') || Auth::user()->hasSubpagePermission('masterlist', 'list', 'edit')) ? 1 : 0) + (Auth::user()->hasSubpagePermission('masterlist', 'list', 'delete') ? 1 : 0) }}"></td>
                    </tr>
                </tfoot>
                <!-- Modal for Image Preview -->
                <div id="imageModal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-75 flex items-center justify-center z-50" style="z-index: 9999;">
                    <div class="relative max-w-[90vw] max-h-[90vh]">
                        <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-[80vh] rounded-md" style="object-fit: contain;">
                        <button 
                            class="absolute top-2 right-2 text-white text-2xl font-bold bg-black bg-opacity-50 rounded-full px-3 py-1 hover:bg-opacity-75"
                            onclick="closeModal()"
                            type="button"
                        >
                            &times;
                        </button>
                        <!-- Debug info -->
                        <div class="absolute bottom-2 left-2 text-white text-sm bg-black bg-opacity-75 px-3 py-2 rounded max-w-md" id="debugInfo">
                            📍 Image loading...
                        </div>
                    </div>
                </div>
            </table>
        </div>
    </div>
    <br>
    <!-- Sticky horizontal scroll bar -->
    <div class="sticky-scroll-container">
        <div class="sticky-scroll-bar">
            <div style="width: 2000px; height: 1px;"></div> <!-- Dummy element to create a scroll bar -->
        </div>
    </div>
</x-app-layout>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    /* Ensure the table layout is fixed with proper width */
    #ordersTable {
        table-layout: fixed;
        width: 4200px; /* Fixed width to accommodate all columns */
        border-collapse: collapse;
    }

    /* Define specific column widths */
    #ordersTable th, #ordersTable td {
        word-wrap: break-word; /* Prevent text overflow */
        text-align: center;
        padding: 8px;
        border: 1px solid #ddd;
    }

    #ordersTable th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    .saving {
        background-color: #f0f8ff; /* Light blue background */
        border-color: #007bff; /* Blue border */
        opacity: 0.7;
    }
    
    .dark .saving {
        background-color: #172234 !important; /* Dark blue background for saving indicator in dark mode */
        border-color: #3b82f6; /* Blue border for dark mode */
    }

    /* Style for the image preview */
    #imageModal {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.75); /* Semi-transparent background */
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    #imageModal img {
        max-width: 90%; /* Ensure the image fits within the modal */
        max-height: 80vh; /* Limit the height to 80% of the viewport */
        border-radius: 8px; /* Rounded corners for the image */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add a shadow for better visibility */
    }

    #imageModal button {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        border: none;
        font-size: 24px;
        color: white;
        padding: 5px 10px;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    #imageModal button:hover {
        background: rgba(255, 255, 255, 0.8); /* Lighten background on hover */
        color: black; /* Change text color on hover */
    }

    /* Ensure filter inputs match the column width */
    .filter-input {
        width: 100%; /* Match the width of the column */
        box-sizing: border-box; /* Include padding and border in width */
        padding: 4px; /* Add padding for better usability */
        border: 1px solid #ddd; /* Add a border for clarity */
        border-radius: 4px; /* Rounded corners */
        font-size: 14px; /* Match the font size of the table */
    }

    /* Align filter inputs with table headers */
    #ordersTable th {
        vertical-align: middle; /* Align headers and inputs */
    }

    /* Style for sortable headers and sort indicators */
    #blHeader, #shipperHeader, #consigneeHeader {
        cursor: pointer;
        user-select: none;
        position: relative;
        transition: background-color 0.2s;
    }
    
    #blHeader:hover, #shipperHeader:hover, #consigneeHeader:hover {
        background-color: #d1d5db;
    }
    
    #blSortIndicator, #shipperSortIndicator, #consigneeSortIndicator {
        font-size: 12px;
        color: #4b5563;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    /* Add spacing between filter inputs and headers */
    #ordersTable thead tr:nth-child(2) th {
        padding: 4px 8px; /* Adjust padding for filter row */
    }

    /* Ensure filter dropdowns match the column width */
    .filter-dropdown {
        width: 100%; /* Match the width of the column */
        box-sizing: border-box; /* Include padding and border in width */
        padding: 4px; /* Add padding for better usability */
        border: 1px solid #ddd; /* Add a border for clarity */
        border-radius: 4px; /* Rounded corners */
        font-size: 14px; /* Match the font size of the table */
        background-color: #fff; /* White background */
    }

    /* Align filter dropdowns with table headers */
    #ordersTable th {
        vertical-align: middle; /* Align headers and dropdowns */
    }

    /* Add spacing between filter dropdowns and headers */
    #ordersTable thead tr:nth-child(2) th {
        padding: 4px 8px; /* Adjust padding for filter row */
    }

    /* Set fixed pixel widths for columns */
    #ordersTable th:nth-child(1), #ordersTable td:nth-child(1) { width: 75px; }  /* BL */
    #ordersTable th:nth-child(2), #ordersTable td:nth-child(2) { width: 150px; } /* DATE */
    #ordersTable th:nth-child(3), #ordersTable td:nth-child(3) { width: 150px; }  /* CONTAINER */
    #ordersTable th:nth-child(4), #ordersTable td:nth-child(4) { width: 150px; } /* CARGO STATUS */
    #ordersTable th:nth-child(5), #ordersTable td:nth-child(5) { width: 230px; } /* SHIPPER */
    #ordersTable th:nth-child(6), #ordersTable td:nth-child(6) { width: 230px; } /* CONSIGNEE */
    #ordersTable th:nth-child(7), #ordersTable td:nth-child(7) { width: 140px; } /* CHECKER */
    #ordersTable th:nth-child(8), #ordersTable td:nth-child(8) { width: 300px; } /* DESCRIPTION */
    #ordersTable th:nth-child(9), #ordersTable td:nth-child(9) { width: 130px; }  /* FREIGHT */
    #ordersTable th:nth-child(10), #ordersTable td:nth-child(10) { width: 130px; } /* ORIGINAL FREIGHT */ 
    #ordersTable th:nth-child(11), #ordersTable td:nth-child(11) { width: 180px; } /* VALUATION */ 
    #ordersTable th:nth-child(12), #ordersTable td:nth-child(12) { width: 130px; } /* VALUE */
    #ordersTable th:nth-child(13), #ordersTable td:nth-child(13) { width: 130px; }  /* WHARFAGE */ 
    #ordersTable th:nth-child(14), #ordersTable td:nth-child(14) { width: 130px; } /* 5% DISCOUNT */ 
    #ordersTable th:nth-child(15), #ordersTable td:nth-child(15) { width: 130px; } /* BIR */ 
    #ordersTable th:nth-child(16), #ordersTable td:nth-child(16) { width: 130px; } /* OTHERS */ 
    #ordersTable th:nth-child(17), #ordersTable td:nth-child(17) { width: 130px; } /* TOTAL */ 
    #ordersTable th:nth-child(18), #ordersTable td:nth-child(18) { width: 120px; } /* OR# */
    #ordersTable th:nth-child(19), #ordersTable td:nth-child(19) { width: 110px; } /* AR# */ 
    #ordersTable th:nth-child(20), #ordersTable td:nth-child(20) { width: 150px; } /* DATE PAID */ 
    #ordersTable th:nth-child(21), #ordersTable td:nth-child(21) { width: 150px; } /* UPDATED BY */ 
    #ordersTable th:nth-child(22), #ordersTable td:nth-child(22) { width: 100px; } /* BL STATUS */ 
    #ordersTable th:nth-child(23), #ordersTable td:nth-child(23) { width: 250px; } /* BL REMARK */
    #ordersTable th:nth-child(24), #ordersTable td:nth-child(24) { width: 300px; } /* NOTE */ 
    #ordersTable th:nth-child(25), #ordersTable td:nth-child(25) { width: 170px; } /* IMAGE */ 
    #ordersTable th:nth-child(26), #ordersTable td:nth-child(26) { width: 100px; } /* VIEW BL */
    /* UPDATE and DELETE BL columns are conditionally displayed, so we use classes instead of fixed nth-child selectors */
    .update-bl-column { width: 100px; }
    .delete-bl-column { width: 100px; }
    /* Sticky horizontal scroll bar */
    .sticky-scroll-container {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #f9f9f9; /* Optional: Background color */
        z-index: 1000; /* Ensure it stays above other elements */
        border-top: 1px solid #ddd; /* Optional: Border for separation */
    }

    .sticky-scroll-bar {
        overflow-x: auto;
        height: 16px; /* Height of the scroll bar */
    }

    .sticky-scroll-bar::-webkit-scrollbar {
        height: 8px;
    }

    .sticky-scroll-bar::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 4px;
    }

    .sticky-scroll-bar::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }

    /* Make the table header and filter row sticky */
    thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f4f4f4; /* Light background for better visibility */
    }

    thead th {
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1); /* Add shadow for separation */
    }

    .dark thead {
        background-color: #2d3748; /* Dark mode background */
        color: #fff; /* Dark mode font color */
    }

    /* Ensure the table header and filter row are sticky */
    .table-container {
        position: relative;
        overflow-x: auto; /* Enable horizontal scrolling */
        overflow-y: auto; /* Enable vertical scrolling */
        max-height: 80vh; /* Limit the height of the table container */
    }

    thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Add background color to the sticky header for better visibility */
    thead.bg-gray-200 {
        background-color: #f4f4f4; /* Light mode background */
    }

    .dark thead.bg-gray-200 {
        background-color: #2d3748; /* Dark mode background */
    }
</style>
<style>
    /* Ensure the table layout is fixed */
    #ordersTable {
        table-layout: fixed;
        width: 4200px; /* Fixed width to accommodate all columns */
    }
    /* Dark mode styles for table headers */
    .dark #ordersTable th {
        background-color: #2d3748; /* Dark mode background */
        color: #fff; /* Dark mode font color */
    }

    /* Light and dark mode styles for table cells */
    #ordersTable td {
        background-color: #fff; /* Light mode background */
        color: #000; /* Light mode font color */
    }

    .dark #ordersTable td {
        background-color: #1a202c; /* Dark mode background */
        color: #fff; /* Dark mode font color */
    }

    /* Light and dark mode styles for filter inputs */
    .filter-input, .filter-dropdown {
        width: 100%; /* Match the width of the column */
        box-sizing: border-box; /* Include padding and border in width */
        padding: 4px; /* Add padding for better usability */
        border: 1px solid #ddd; /* Add a border for clarity */
        border-radius: 4px; /* Rounded corners */
        font-size: 14px; /* Match the font size of the table */
        background-color: #fff; /* Light mode background */
        color: #000; /* Light mode font color */
    }

    .dark .filter-input, .dark .filter-dropdown {
        background-color: #2d3748; /* Dark mode background */
        color: #fff; /* Dark mode font color */
        border: 1px solid #4a5568; /* Dark mode border color */
    }

    /* Align filter inputs with table headers */
    #ordersTable th {
        vertical-align: middle; /* Align headers and inputs */
    }

    /* Add spacing between filter inputs and headers */
    #ordersTable thead tr:nth-child(2) th {
        padding: 4px 8px; /* Adjust padding for filter row */
    }

    /* Table Header */
    #ordersTable th {
        background-color: #f4f4f4; /* Light mode background */
        color: #000; /* Light mode font color */
    }

    .dark #ordersTable th {
        background-color: #1a202c; /* Dark mode background */
        color: #fff; /* Dark mode font color */
    }

    /* Input Fields */
    .bir-input, .or-input, .ar-input, .note-input, .freight-input, .valuation-input, .value-input, .discount-input, .other-input, .original-freight-input {
        background-color: #fff; /* Light mode background */
        color: #000; /* Light mode font color */
        border: 1px solid #ddd; /* Light mode border */
    }

    .dark .bir-input, .dark .or-input, .dark .ar-input, .dark .note-input, .dark .freight-input, .dark .valuation-input, .dark .value-input, .dark .discount-input,  .dark .other-input, .dark .original-freight-input {
        background-color: #1a202c; /* Dark mode background */
        color: #fff; /* Dark mode font color */
        border: 1px solid #4a5568; /* Dark mode border */
    }

    .container-input {
        border: none;
        width: 100%;
        text-align: center;
        background: transparent;
        font-size: inherit;
        padding: 0;
    }

    .container-input:focus {
        outline: none;
        background: #f9f9f9; /* Optional: Add a subtle background on focus */
    }

    /* Ensure the table container allows horizontal scrolling */
    .table-container {
        overflow-x: auto;
        width: 100%;
    }

    /* Optional: Add a duplicate scroll bar at the top */
    .table-scroll-top {
        overflow-x: auto;
        margin-bottom: 10px;
    }

    .table-scroll-top::-webkit-scrollbar {
        height: 8px;
    }

    .table-scroll-top::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 4px;
    }

    .table-scroll-top::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }
    
    /* Styles for editable textareas */
    .cargo-status-textarea,
    .remark-textarea,
    .container-textarea,
    .checker-textarea {
        transition: background-color 0.3s;
    }
    
    /* Special handling for auto-resizing textareas */
    .remark-textarea,
    .container-textarea,
    .checker-textarea {
        min-height: 40px;
        overflow: hidden;
        transition: height 0.2s ease;
    }
    
    .cargo-status-textarea:focus,
    .remark-textarea:focus,
    .container-textarea:focus,
    .checker-textarea:focus {
        background-color: rgba(200, 200, 200, 0.2) !important;
        outline: 1px solid #4f46e5 !important;
    }
    
    .cargo-status-textarea.saving,
    .remark-textarea.saving,
    .container-textarea.saving,
    .checker-textarea.saving {
        background-color: rgba(79, 70, 229, 0.1) !important;
    }
    
    /* Custom styling for auto-resizing cells - maintains fixed column widths */
    .remark-cell,
    .container-cell,
    .checker-cell,
    .cargo-status-cell {
        vertical-align: top !important;
        transition: all 0.3s ease;
        word-wrap: break-word;
        overflow-wrap: break-word;
        height: auto !important;
        padding: 8px !important;
        /* Width is controlled by nth-child selectors above */
    }
    
    /* Reinforce original column widths for auto-resize columns */
    #ordersTable th:nth-child(3), #ordersTable td:nth-child(3) { width: 150px !important; }  /* CONTAINER */
    #ordersTable th:nth-child(4), #ordersTable td:nth-child(4) { width: 150px !important; } /* CARGO STATUS */
    #ordersTable th:nth-child(7), #ordersTable td:nth-child(7) { width: 140px !important; } /* CHECKER */
    
    /* Ensure textareas fit within their fixed column widths */
    .container-cell .container-textarea,
    .checker-cell .checker-textarea,
    .cargo-status-cell .cargo-status-textarea {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    /* Ensure textareas in auto-resize cells behave properly */
    .remark-cell textarea,
    .container-cell textarea,
    .checker-cell textarea,
    .cargo-status-cell textarea {
        display: block !important;
        vertical-align: top !important;
        line-height: 1.4 !important;
        font-size: 14px !important;
    }
    
    /* Ensure table rows can expand to accommodate content */
    #ordersTable tbody tr {
        height: auto !important;
        min-height: 40px;
    }
    
    /* Ensure all table cells can expand vertically while maintaining fixed widths */
    #ordersTable tbody td {
        vertical-align: top !important;
        height: auto !important;
        min-height: 40px;
        overflow: visible !important;
    }
    
    /* Specific styling for auto-resizing textareas */
    .container-textarea,
    .checker-textarea,
    .cargo-status-textarea,
    .remark-textarea {
        transition: height 0.2s ease, background-color 0.3s;
        line-height: 1.4 !important;
        font-family: inherit !important;
        font-size: 14px !important;
        box-sizing: border-box !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterInputs = document.querySelectorAll('.filter-input');
        const filterDropdowns = document.querySelectorAll('.filter-dropdown');

        // Function to filter rows based on all active filters
        function applyFilters() {
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            const activeFilters = {};

            // Collect active filters
            filterInputs.forEach(input => {
                const column = input.getAttribute('data-column');
                const value = input.value.trim().toLowerCase();
                if (value) activeFilters[column] = value;
            });

            filterDropdowns.forEach(dropdown => {
                const column = dropdown.getAttribute('data-column');
                const value = dropdown.value.trim().toLowerCase();
                if (value) activeFilters[column] = value;
            });

            rows.forEach(row => {
                let isVisible = true;

                Object.keys(activeFilters).forEach(column => {
                    const cell = row.querySelector(`td[data-column="${column}"]`);
                    const filterValue = activeFilters[column];
                    
                    if (cell && filterValue) {
                        // Special handling for description column
                        if (column === 'description') {
                            // Get all spans containing item descriptions
                            const spans = cell.querySelectorAll('span');
                            let hasMatch = false;
                            
                            spans.forEach(span => {
                                // Extract the full description text (e.g. "10 - Rice Cooker Large")
                                const text = span.textContent.trim().toLowerCase();
                                
                                // Extract just the item name from "quantity - itemName description"
                                const parts = text.split(' - ');
                                if (parts.length > 1) {
                                    // Get just the item name part without quantity
                                    const itemNamePart = parts[1].trim();
                                    
                                    // Check if the item name contains the search text
                                    if (itemNamePart.toLowerCase().includes(filterValue)) {
                                        hasMatch = true;
                                    }
                                }
                            });
                            
                            if (!hasMatch) {
                                isVisible = false;
                            }
                        } else if (column === 'bl_status') {
                            // Special handling for BL STATUS - exact match only
                            const cellValue = cell.textContent.trim().toLowerCase();
                            
                            // Use exact matching for BL STATUS
                            if (cellValue !== filterValue) {
                                isVisible = false;
                            }
                        } else {
                            // Normal handling for other columns (including checker)
                            const cellValue = cell.querySelector('input') 
                                ? cell.querySelector('input').value.trim().toLowerCase() 
                                : cell.textContent.trim().toLowerCase();

                            if (!cellValue.includes(filterValue)) {
                                isVisible = false;
                            }
                        }
                    }
                });

                row.style.display = isVisible ? '' : 'none';
            });

            // Recalculate totals after filtering
            calculateTotals();
            
            // Re-initialize auto-resize for visible textareas after filtering
            reinitializeAutoResize();
        }
        
        // Function to reinitialize auto-resize for visible textareas
        function reinitializeAutoResize() {
            const visibleTextareas = document.querySelectorAll('.container-textarea:not([style*="display: none"]), .checker-textarea:not([style*="display: none"]), .cargo-status-textarea:not([style*="display: none"]), .remark-textarea:not([style*="display: none"])');
            visibleTextareas.forEach(textarea => {
                // Only trigger auto-resize if textarea has content that might need resizing
                if (textarea.value && textarea.value.length > 0) {
                    // Trigger auto-resize by dispatching input event
                    const event = new Event('input', { bubbles: true });
                    textarea.dispatchEvent(event);
                }
            });
        }

        // Attach event listeners to filter inputs and dropdowns
        filterInputs.forEach(input => {
            input.addEventListener('input', applyFilters);
        });

        filterDropdowns.forEach(dropdown => {
            dropdown.addEventListener('change', applyFilters);
        });

        // Initial application of filters
        applyFilters();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Wait for a brief moment to ensure all DOM elements are properly loaded
        setTimeout(() => {
            console.log("DOM fully loaded, initializing sort");
            
            // Initialize variables for sort state
            currentSortColumn = 'bl';
            sortDirection = 'asc';
            
            // Make sure BL indicator is displayed and others are hidden
            if (document.getElementById('blSortIndicator')) {
                document.getElementById('blSortIndicator').style.display = 'inline';
            }
            if (document.getElementById('shipperSortIndicator')) {
                document.getElementById('shipperSortIndicator').style.display = 'none';
            }
            if (document.getElementById('consigneeSortIndicator')) {
                document.getElementById('consigneeSortIndicator').style.display = 'none';
            }
            
            // Sort table by BL column in ascending order when the page loads
            sortTableByBL('asc');
            
            // Add click event listener to BL header for sorting
            const blHeader = document.getElementById('blHeader');
            if (blHeader) {
                blHeader.addEventListener('click', function() {
                    // Toggle sort direction
                    console.log("BL header clicked, current direction:", sortDirection);
                    const newDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    sortTableByBL(newDirection);
                });
                
                // Add visual indication that header is clickable
                blHeader.style.transition = "background-color 0.3s";
                blHeader.title = "Click to sort";
            }
            
            // Add click event listeners for SHIPPER and CONSIGNEE headers
            const shipperHeader = document.getElementById('shipperHeader');
            if (shipperHeader) {
                shipperHeader.addEventListener('click', function() {
                    // Call the new general sort function
                    toggleColumnSort('shipper');
                });
                shipperHeader.style.transition = "background-color 0.3s";
                shipperHeader.title = "Click to sort";
            }
            
            const consigneeHeader = document.getElementById('consigneeHeader');
            if (consigneeHeader) {
                consigneeHeader.addEventListener('click', function() {
                    // Call the new general sort function
                    toggleColumnSort('consignee');
                });
                consigneeHeader.style.transition = "background-color 0.3s";
                consigneeHeader.title = "Click to sort";
            }
        }, 200);
        
        // The rest of your existing DOMContentLoaded script
        const tableContainer = document.querySelector('.table-container');
        const stickyScrollBar = document.querySelector('.sticky-scroll-bar');

        if (tableContainer && stickyScrollBar) {
            // Sync the scroll positions of the table and the sticky scroll bar
            stickyScrollBar.addEventListener('scroll', function () {
                tableContainer.scrollLeft = stickyScrollBar.scrollLeft;
            });

            tableContainer.addEventListener('scroll', function () {
                stickyScrollBar.scrollLeft = tableContainer.scrollLeft;
            });

            // Set the width and height of the sticky scroll bar to match the table's scrollable width
            stickyScrollBar.firstElementChild.style.width = `${tableContainer.scrollWidth}px`;
            stickyScrollBar.firstElementChild.style.height = '1px'; // Ensure height is consistent
        }
    });
    
    // Variables to track sort state
    let sortDirection = 'asc';
    let currentSortColumn = 'bl';
    
    // Function to toggle sort for any column
    function toggleColumnSort(column) {
        // Hide all sort indicators first
        document.getElementById('blSortIndicator').style.display = 'none';
        document.getElementById('shipperSortIndicator').style.display = 'none';
        document.getElementById('consigneeSortIndicator').style.display = 'none';
        
        // Reset background color of all headers
        document.getElementById('blHeader').style.backgroundColor = '';
        document.getElementById('shipperHeader').style.backgroundColor = '';
        document.getElementById('consigneeHeader').style.backgroundColor = '';
        
        // If clicking the same column, toggle direction
        if (currentSortColumn === column) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // If clicking a different column, default to ascending
            sortDirection = 'asc';
            currentSortColumn = column;
        }
        
        // Show the appropriate sort indicator
        const indicator = document.getElementById(`${column}SortIndicator`);
        indicator.style.display = 'inline';
        indicator.textContent = sortDirection === 'asc' ? '▲' : '▼';
        
        // Highlight the selected header
        document.getElementById(`${column}Header`).style.backgroundColor = '#e2e8f0';
        
        // Sort the table
        sortTable(column, sortDirection);
    }
    
    // General function to sort the table by any column
    function sortTable(column, direction = 'asc') {
        const tbody = document.querySelector('#ordersTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Determine which column index to sort by
        let columnIndex;
        switch(column) {
            case 'bl':
                columnIndex = 0; // BL is the 1st column (index 0)
                break;
            case 'shipper':
                columnIndex = 4; // SHIPPER is the 5th column (index 4)
                break;
            case 'consignee':
                columnIndex = 5; // CONSIGNEE is the 6th column (index 5)
                break;
            default:
                columnIndex = 0;
        }
        
        // Sort the rows
        rows.sort((a, b) => {
            const aValue = a.cells[columnIndex].textContent.trim().toLowerCase();
            const bValue = b.cells[columnIndex].textContent.trim().toLowerCase();
            
            // Special handling for the BL column (numeric sort)
            if (column === 'bl') {
                // Extract numeric parts if they exist
                const numA = parseInt(aValue.match(/\d+/)) || 0;
                const numB = parseInt(bValue.match(/\d+/)) || 0;
                
                return direction === 'asc' ? (numA - numB) : (numB - numA);
            } 
            // For text columns (like shipper and consignee), use localeCompare
            else {
                if (direction === 'asc') {
                    return aValue.localeCompare(bValue);
                } else {
                    return bValue.localeCompare(aValue);
                }
            }
        });
        
        // Remove existing rows
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        // Append sorted rows
        rows.forEach(row => {
            tbody.appendChild(row);
        });
        
        // Recalculate totals after sorting
        if (typeof calculateTotals === 'function') {
            calculateTotals();
        }
    }
    
    // Legacy function for BL column for backward compatibility
    function sortTableByBL(direction = 'asc') {
        // Update variables to maintain state
        sortDirection = direction;
        currentSortColumn = 'bl';
        
        // Hide all sort indicators first
        if (document.getElementById('shipperSortIndicator')) {
            document.getElementById('shipperSortIndicator').style.display = 'none';
        }
        if (document.getElementById('consigneeSortIndicator')) {
            document.getElementById('consigneeSortIndicator').style.display = 'none';
        }
        
        // Reset background color of all headers
        if (document.getElementById('shipperHeader')) {
            document.getElementById('shipperHeader').style.backgroundColor = '';
        }
        if (document.getElementById('consigneeHeader')) {
            document.getElementById('consigneeHeader').style.backgroundColor = '';
        }
        
        // Show BL sort indicator with correct direction
        const indicator = document.getElementById('blSortIndicator');
        if (indicator) {
            indicator.style.display = 'inline';
            indicator.textContent = direction === 'asc' ? '▲' : '▼';
        }
        
        // Highlight the BL header
        const blHeader = document.getElementById('blHeader');
        if (blHeader) {
            blHeader.style.backgroundColor = '#e2e8f0'; // Light gray background
        }
        
        try {
            console.log("Sorting table by BL column in", direction, "order");
            
            // Use the new generic sort function
            sortTable('bl', direction);
            
            console.log("Sort complete");
        } catch (error) {
            console.error("Error sorting table:", error);
        }
    }
</script>
<script>
    // Debug code to check sorting functionality
    console.log("Script loaded for sorting BL column");
    
    // Check if all required elements exist
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM Content Loaded");
        
        const table = document.getElementById('ordersTable');
        console.log("Orders table found:", !!table);
        
        const blHeader = document.getElementById('blHeader');
        console.log("BL header found:", !!blHeader);
        
        const blSortIndicator = document.getElementById('blSortIndicator');
        console.log("BL sort indicator found:", !!blSortIndicator);
        
        const shipperSortIndicator = document.getElementById('shipperSortIndicator');
        console.log("Shipper sort indicator found:", !!shipperSortIndicator);
        
        const consigneeSortIndicator = document.getElementById('consigneeSortIndicator');
        console.log("Consignee sort indicator found:", !!consigneeSortIndicator);
        
        const tbody = table ? table.querySelector('tbody') : null;
        console.log("Table body found:", !!tbody);
        
        const rows = tbody ? tbody.querySelectorAll('tr') : [];
        console.log("Number of rows found:", rows.length);
        
        // Check that calculateTotals function exists
        console.log("calculateTotals function exists:", typeof calculateTotals === 'function');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const containerTextareas = document.querySelectorAll('.container-textarea');
        
        // Function to send update to server
        function updateContainer(textarea) {
            const orderId = textarea.getAttribute('data-order-id');
            const newValue = textarea.value;
            
            // Add visual indication that saving is in progress
            textarea.classList.add('saving');
            
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    field: 'containerNum',
                    value: newValue
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Container updated successfully');
                    // Remove the saving indicator after a short delay
                    setTimeout(() => {
                        textarea.classList.remove('saving');
                    }, 500);
                } else {
                    console.error('Failed to update container:', data.message);
                    textarea.classList.remove('saving');
                    alert('Failed to save container information. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                textarea.classList.remove('saving');
                alert('Error saving container information. Please check your connection and try again.');
            });
        }
        
        // Debounce function to limit API calls
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
        
        // Create debounced update function
        const debouncedUpdate = debounce(updateContainer, 500);
        
        containerTextareas.forEach(textarea => {
            // Handle change event (when user clicks away)
            textarea.addEventListener('change', function() {
                updateContainer(this);
            });
            
            // Handle blur event (when focus leaves the textarea)
            textarea.addEventListener('blur', function() {
                updateContainer(this);
            });
            
            // Handle input event with debounce (for auto-save while typing)
            textarea.addEventListener('input', function() {
                debouncedUpdate(this);
            });
            
            // Handle keydown event for Enter key
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent adding a new line
                    updateContainer(this);
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calculateTotals = () => {
            let totalFreight = 0;
            let totalValuation = 0;
            let totalValue = 0;
            let totalWharfage = 0; // Added total Wharfage
            let totalDiscount = 0;
            let totalBir = 0; // Added total BIR
            let totalOthers = 0; // Added total OTHERS
            let totalAmount = 0;
            let totalOriginalFreight = 0; // Added total ORIGINAL FREIGHT

            // Iterate through visible rows and calculate totals
            document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
                if (row.style.display !== 'none') { // Only include visible rows
                    // Handle freight (can be input or span)
                    const freightInput = row.querySelector('.freight-input');
                    const freightValue = freightInput ? freightInput.value : row.querySelector('[data-column="freight"]')?.textContent || '0';
                    totalFreight += parseFloat(freightValue.replace(/,/g, '') || 0);
                    
                    totalOriginalFreight += parseFloat(row.querySelector('.original-freight-input')?.value.replace(/,/g, '') || 0); // Calculate total ORIGINAL FREIGHT
                    
                    // Valuation is always displayed as text, not input - get from data-column="valuation"
                    totalValuation += parseFloat(row.querySelector('[data-column="valuation"]')?.textContent.replace(/,/g, '') || 0);
                    
                    // Handle value (can be input or span)
                    const valueInput = row.querySelector('.value-input');
                    const valueValue = valueInput ? valueInput.value : row.querySelector('[data-column="value"]')?.textContent || '0';
                    totalValue += parseFloat(valueValue.replace(/,/g, '') || 0);
                    
                    // Handle wharfage (can be input or span)
                    const wharfageInput = row.querySelector('.wharfage-input');
                    const wharfageValue = wharfageInput ? wharfageInput.value : row.querySelector('[data-column="wharfage"]')?.textContent || '0';
                    totalWharfage += parseFloat(wharfageValue.replace(/,/g, '') || 0);
                    
                    // Handle discount (can be input or span)
                    const discountInput = row.querySelector('.discount-input');
                    const discountValue = discountInput ? discountInput.value : row.querySelector('[data-column="discount"]')?.textContent || '0';
                    totalDiscount += parseFloat(discountValue.replace(/,/g, '') || 0);
                    
                    // Handle BIR (can be input or span)
                    const birInput = row.querySelector('.bir-input');
                    const birValue = birInput ? birInput.value : row.querySelector('[data-column="bir"]')?.textContent || '0';
                    totalBir += parseFloat(birValue.replace(/,/g, '') || 0);
                    
                    // Handle others (can be input or span)
                    const otherInput = row.querySelector('.other-input');
                    const otherValue = otherInput ? otherInput.value : row.querySelector('[data-column="other"]')?.textContent || '0';
                    totalOthers += parseFloat(otherValue.replace(/,/g, '') || 0);
                    
                    totalAmount += parseFloat(row.querySelector('.total')?.textContent.replace(/,/g, '') || 0);
                }
            });

            // Update the footer totals with comma formatting
            document.getElementById('totalFreight').textContent = totalFreight.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('totalOriginalFreight').textContent = totalOriginalFreight.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); // Update total ORIGINAL FREIGHT
            document.getElementById('totalValuation').textContent = totalValuation.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('totalValue').textContent = totalValue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('totalWharfage').textContent = totalWharfage.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); // Update total Wharfage
            document.getElementById('totalDiscount').textContent = totalDiscount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('totalBir').textContent = totalBir.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); // Update total BIR
            document.getElementById('totalOthers').textContent = totalOthers.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); // Update total OTHERS
            document.getElementById('totalAmount').textContent = totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const applyFilters = () => {
            const filterInputs = document.querySelectorAll('.filter-input');
            const filterDropdowns = document.querySelectorAll('.filter-dropdown');

            document.querySelectorAll('#ordersTable tbody tr').forEach(row => {
                let isVisible = true;

                // Check all filter inputs
                filterInputs.forEach(input => {
                    const column = input.getAttribute('data-column');
                    const value = input.value.trim().toLowerCase();
                    const cell = row.querySelector(`[data-column="${column}"]`);

                    if (value && cell) {
                        const cellValue = cell.querySelector('input') 
                            ? cell.querySelector('input').value.trim().toLowerCase() 
                            : cell.textContent.trim().toLowerCase();

                        if (!cellValue.includes(value)) {
                            isVisible = false;
                        }
                    }
                });

                // Check all filter dropdowns
                filterDropdowns.forEach(dropdown => {
                    const column = dropdown.getAttribute('data-column');
                    const value = dropdown.value.trim().toLowerCase();
                    const cell = row.querySelector(`[data-column="${column}"]`);

                    if (value && cell) {
                        if (column === 'description') {
                            // Handle nested descriptions
                            const spans = cell.querySelectorAll('span');
                            let hasMatch = false;

                            spans.forEach(span => {
                                const text = span.textContent.trim().toLowerCase();
                                const parts = text.split(' - ');
                                if (parts.length > 1) {
                                    const itemNamePart = parts[1].trim();
                                    const itemName = itemNamePart.split(' ')[0];

                                    if (value === itemNamePart.toLowerCase() || itemNamePart.toLowerCase().includes(value)) {
                                        hasMatch = true;
                                    }
                                }
                            });

                            if (!hasMatch) {
                                isVisible = false;
                            }
                        } else {
                            const cellValue = cell.querySelector('input') 
                                ? cell.querySelector('input').value.trim().toLowerCase() 
                                : cell.textContent.trim().toLowerCase();

                            if (cellValue !== value) {
                                isVisible = false;
                            }
                        }
                    }
                });

                row.style.display = isVisible ? '' : 'none';
            });

            calculateTotals();
        };

        // Attach event listeners to filter inputs and dropdowns
        document.querySelectorAll('.filter-input, .filter-dropdown').forEach(input => {
            input.addEventListener('input', applyFilters);
            input.addEventListener('change', applyFilters);
        });

        // Initial calculation
        calculateTotals();
    });
</script>
<script>
    // For handling image uploads and changes
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all image uploads and changes
        document.querySelectorAll('.upload-input, .change-input').forEach(input => {
            input.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order-id');
                const form = this.closest('form');
                
                if (form && this.files.length > 0) {
                    // Show loading indicator
                    const loading = document.createElement('div');
                    loading.style.position = 'fixed';
                    loading.style.top = '0';
                    loading.style.left = '0';
                    loading.style.width = '100%';
                    loading.style.height = '100%';
                    loading.style.backgroundColor = 'rgba(0,0,0,0.5)';
                    loading.style.zIndex = '1000';
                    loading.style.display = 'flex';
                    loading.style.justifyContent = 'center';
                    loading.style.alignItems = 'center';
                    loading.innerHTML = '<div style="color:white; font-size:1.5rem;">Uploading image...</div>';
                    document.body.appendChild(loading);
                    
                    // Submit form via AJAX
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Force a complete page reload from server
                        window.location.href = window.location.href.split('?')[0] + '?refresh=' + new Date().getTime();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error uploading image');
                    })
                    .finally(() => {
                        document.body.removeChild(loading);
                    });
                }
            });
        });
    });
</script>
<script>
    // For updating OR and AR fields
    document.addEventListener('DOMContentLoaded', function () {
        const orInputs = document.querySelectorAll('.or-input');
        const arInputs = document.querySelectorAll('.ar-input');

        // Function to update the order field
        function updateOrderField(orderId, field, value) {
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ field, value }) // Send the correct field and value
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`${field} updated successfully`);
                } else {
                    console.error(`Failed to update ${field}`);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function submitFormAndRefresh(form) {
            form.submit(); // Submit the form
            setTimeout(() => {
                // Force a hard reload by appending a unique query parameter
                window.location.href = window.location.href.split('?')[0] + '?t=' + new Date().getTime();
            }, 500); // Add a slight delay to ensure the form submission completes
        }

        // Add event listeners to the OR and AR inputs
        orInputs.forEach(input => {
            input.addEventListener('input', function () {
                const orderId = this.getAttribute('data-order-id');
                const value = this.value;
                updateOrderField(orderId, 'OR', value); // Update the OR field
            });
        });

        arInputs.forEach(input => {
            input.addEventListener('input', function () {
                const orderId = this.getAttribute('data-order-id');
                const value = this.value;
                updateOrderField(orderId, 'AR', value); // Update the AR field
            });
        });
    });
</script>
<script>
    // For updating OR and AR fields with date
    document.addEventListener('DOMContentLoaded', function () {
        const orInputs = document.querySelectorAll('.or-input');
        const arInputs = document.querySelectorAll('.ar-input');

        // Function to update the order field
        function updateOrderField(orderId, field, value) {
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ field, value }) // Send the correct field and value
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`${field} updated successfully`);

                    // Update BL STATUS and DATE PAID dynamically
                    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                    if (row) {
                        const blStatusCell = row.querySelector('[data-column="bl_status"]');
                        const datePaidCell = row.querySelector('[data-column="dp"]');

                        if (blStatusCell) blStatusCell.textContent = data.blStatus;
                        if (datePaidCell) datePaidCell.textContent = data.or_ar_date || '';
                    }
                } else {
                    console.error(`Failed to update ${field}`);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Add event listeners to the OR and AR inputs
        orInputs.forEach(input => {
            input.addEventListener('input', function () {
                const orderId = this.getAttribute('data-order-id');
                const value = this.value;
                updateOrderField(orderId, 'OR', value); // Update the OR field
            });
        });

        arInputs.forEach(input => {
            input.addEventListener('input', function () {
                const orderId = this.getAttribute('data-order-id');
                const value = this.value;
                updateOrderField(orderId, 'AR', value); // Update the AR field
            });
        });
    });

    // Function to open the modal
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Add event listeners to the OR and AR inputs with debounce
    orInputs.forEach(input => {
        input.addEventListener('input', debounce(function () {
            const orderId = this.getAttribute('data-order-id');
            const value = this.value;
            updateOrderField(orderId, 'OR', value);
        }, 300)); // 300ms debounce
    });

    arInputs.forEach(input => {
        input.addEventListener('input', debounce(function () {
            const orderId = this.getAttribute('data-order-id');
            const value = this.value;
            updateOrderField(orderId, 'AR', value);
        }, 300)); // 300ms debounce
    });
</script>

<script>
    // For updating NOTE field
    document.addEventListener('DOMContentLoaded', function () {
        const noteInputs = document.querySelectorAll('.note-input');

        // Debounce function to limit the frequency of requests
        function debounce(func, delay) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Function to update the note field
        function updateNoteField(orderId, note) {
            fetch(`/update-note-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ note })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`Note updated successfully for Order ID: ${orderId}`);
                } else {
                    console.error(`Failed to update note for Order ID: ${orderId}`);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Add event listeners to note inputs with debounce
        noteInputs.forEach(input => {
            input.addEventListener('input', debounce(function () {
                const orderId = this.getAttribute('data-order-id');
                const note = this.value;
                updateNoteField(orderId, note);
            }, 500)); // 500ms debounce
        });
    });
</script>
<script>
    // Function to open the modal
    function openModal(imageSrc) {
        console.log('Opening modal with image:', imageSrc); // Debug log
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const debugInfo = document.getElementById('debugInfo');
        
        if (!modal) {
            console.error('Modal element not found!');
            alert('Modal element not found! Please refresh the page.');
            return;
        }
        
        if (!modalImage) {
            console.error('Modal image element not found!');
            alert('Modal image element not found! Please refresh the page.');
            return;
        }
        
        // Update debug info
        if (debugInfo) {
            debugInfo.innerHTML = `📍 Loading: <br><small>${imageSrc}</small>`;
        }
        
        modalImage.src = imageSrc; // Set the image source
        modal.classList.remove('hidden'); // Show the modal
        modal.style.display = 'flex'; // Ensure the modal is displayed
        
        console.log('Modal display set to flex, classes:', modal.className);
        
        // Add error handling for image loading
        modalImage.onload = function() {
            console.log('✅ Image loaded successfully');
            if (debugInfo) {
                debugInfo.innerHTML = `✅ Image loaded successfull`;
            }
        };
        
        modalImage.onerror = function() {
            console.error('❌ Failed to load image:', imageSrc);
            if (debugInfo) {
                debugInfo.innerHTML = `❌ Failed to load:<br><small>${imageSrc}</small><br><span style="color: #ff6b6b;">Check browser Network tab for details</span>`;
            }
            // Don't show alert for better UX - info is in debug div
        };
    }

    // Function to close the modal
    function closeModal() {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        
        if (modal) {
            modal.classList.add('hidden'); // Hide the modal
            modal.style.display = 'none'; // Ensure the modal is hidden
        }
        
        if (modalImage) {
            modalImage.src = ''; // Clear the image source
        }
    }
    
    // Close modal when clicking outside the image
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                // Close modal if clicking on the backdrop (not the image or close button)
                if (e.target === modal) {
                    closeModal();
                }
            });
        }
    });
    
    // Function to remove image
    function removeImage(orderId) {
        if (confirm('Are you sure you want to remove this image?')) {
            // Show loading indicator
            const loading = document.createElement('div');
            loading.style.position = 'fixed';
            loading.style.top = '0';
            loading.style.left = '0';
            loading.style.width = '100%';
            loading.style.height = '100%';
            loading.style.backgroundColor = 'rgba(0,0,0,0.5)';
            loading.style.zIndex = '1000';
            loading.style.display = 'flex';
            loading.style.justifyContent = 'center';
            loading.style.alignItems = 'center';
            loading.innerHTML = '<div style="color:white; font-size:1.5rem;">Removing image...</div>';
            document.body.appendChild(loading);
            
            fetch(`/remove-image/${orderId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Force a complete page reload from server
                    window.location.href = window.location.href.split('?')[0] + '?refresh=' + new Date().getTime();
                } else {
                    alert('Error: ' + (data.message || 'Failed to remove image'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing image');
            })
            .finally(() => {
                document.body.removeChild(loading);
            });
        }
    }
</script>
<script>
    // Function to filter table rows based on input values
    document.addEventListener('DOMContentLoaded', function () {
        const filterInputs = document.querySelectorAll('.filter-input');

        filterInputs.forEach(input => {
            input.addEventListener('input', function () {
                const column = this.getAttribute('data-column');
                const value = this.value.toLowerCase();
                const rows = document.querySelectorAll('#ordersTable tbody tr');

                rows.forEach(row => {
                    const cell = row.querySelector(`td[data-column="${column}"]`);
                    if (cell) {
                        const cellValue = cell.textContent.toLowerCase();
                        if (cellValue.includes(value)) {
                            row.style.display = ''; // Show row
                        } else {
                            row.styledisplay = 'none'; // Hide row
                        }
                    }
                });
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterInputs = document.querySelectorAll('.filter-input');
        const filterDropdowns = document.querySelectorAll('.filter-dropdown');

        // Function to filter rows based on all active filters
        function applyFilters() {
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            const activeFilters = {};

            // Collect active filters
            filterInputs.forEach(input => {
                const column = input.getAttribute('data-column');
                const value = input.value.trim().toLowerCase();
                if (value) activeFilters[column] = value;
            });

            filterDropdowns.forEach(dropdown => {
                const column = dropdown.getAttribute('data-column');
                const value = dropdown.value.trim().toLowerCase();
                if (value) activeFilters[column] = value;
            });

            rows.forEach(row => {
                let isVisible = true;

                Object.keys(activeFilters).forEach(column => {
                    const cell = row.querySelector(`td[data-column="${column}"]`);
                    const filterValue = activeFilters[column];
                    if (cell) {
                        if (column === 'bl_status') {
                            // Special handling for BL STATUS - exact match only
                            const cellValue = cell.textContent.trim().toLowerCase();
                            
                            // Use exact matching for BL STATUS
                            if (cellValue !== filterValue) {
                                isVisible = false;
                            }
                        } else {
                            const cellValue = cell.querySelector('input') 
 
                                ? cell.querySelector('input').value.trim().toLowerCase() 
                                : cell.textContent.trim().toLowerCase();

                            if (!cellValue.includes(filterValue)) {
                                isVisible = false;
                            }
                        }
                    }
                });

                row.style.display = isVisible ? '' : 'none';
            });

            updateDescriptionFilter();
        }

        // Function to update the "DESCRIPTION" filter dropdown dynamically
        function updateDescriptionFilter() {
            const descriptionFilter = document.querySelector('.filter-dropdown[data-column="description"]');
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            const uniqueDescriptions = new Set();

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const descriptionCell = row.querySelector('td[data-column="description"]');
                    if (descriptionCell) {
                        const descriptions = descriptionCell.textContent.split('\n').map(desc => desc.trim());
                        descriptions.forEach(desc => uniqueDescriptions.add(desc));
                    }
                }
            });

            // Clear and repopulate the dropdown
            const currentValue = descriptionFilter.value;
            descriptionFilter.innerHTML = '<option value="">All</option>';
            uniqueDescriptions.forEach(description => {
                const option = document.createElement('option');
                option.value = description;
                option.textContent = description;
                descriptionFilter.appendChild(option);
            });

            // Restore the current value if it still exists
            descriptionFilter.value = currentValue;
        }

        filterInputs.forEach(input => {
            input.addEventListener('input', applyFilters);
        });

        filterDropdowns.forEach(dropdown => {
            dropdown.addEventListener('change', applyFilters);
        });

        applyFilters();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const descSearchInput = document.querySelector('.filter-input[data-column="description"]');
        const descDropdown = document.querySelector('.filter-dropdown[data-column="description"]');
        
        if (descSearchInput && descDropdown) {
            // Function to clean description text by removing quantities (numbers followed by optional units)
            function cleanDescription(text) {
                // Skip empty or undefined text
                if (!text) return '';
                
                // Remove quantities like "10 - " to get just the item name
                const parts = text.split(' - ');
                if (parts.length > 1) {
                    return parts[1].trim();
                }
                return text.trim();
            }
            
            // Function to extract unique item descriptions from the table
            function extractUniqueDescriptions() {
                const rows = document.querySelectorAll('#ordersTable tbody tr');
                const uniqueDescriptions = new Set();
                
                rows.forEach(row => {
                    const descCell = row.querySelector('td[data-column="description"]');
                    if (descCell) {
                        const spans = descCell.querySelectorAll('span');
                        spans.forEach(span => {
                            const text = span.textContent.trim();
                            const cleanedDesc = cleanDescription(text);
                            if (cleanedDesc) {
                                uniqueDescriptions.add(cleanedDesc);
                            }
                        });
                    }
                });
                
                return Array.from(uniqueDescriptions).sort();
            }
            
            // Store all unique descriptions from the table
            const allDescriptions = extractUniqueDescriptions();
            
            // Function to populate dropdown with filtered descriptions
            function filterDropdownOptions() {
                const searchValue = descSearchInput.value.trim().toLowerCase();
                
                // Clear current dropdown options except for the first "All" option
                while (descDropdown.options.length > 1) {
                    descDropdown.remove(1);
                }
                
                // Filter descriptions based on search text
                const filteredDescriptions = searchValue ? 
                    allDescriptions.filter(desc => desc.toLowerCase().includes(searchValue)) : 
                    allDescriptions;
                
                // Add filtered options to dropdown
                filteredDescriptions.forEach(desc => {
                    const option = document.createElement('option');
                    option.value = desc;
                    option.textContent = desc;
                    descDropdown.appendChild(option);
                });
            }
            
            // Connect the search input to filter the dropdown
            descSearchInput.addEventListener('input', filterDropdownOptions);
            
            // When dropdown selection changes, update the search input if empty
            descDropdown.addEventListener('change', function() {
                if (this.value && !descSearchInput.value) {
                    descSearchInput.value = this.value;
                    // Trigger the main table filter
                    const event = new Event('input', { bubbles: true });
                    descSearchInput.dispatchEvent(event);
                }
            });
            
            // Initial population of dropdown
            filterDropdownOptions();
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const descSearchInput = document.querySelector('.filter-input[data-column="description"]');
        const descDropdown = document.querySelector('.filter-dropdown[data-column="description"]');
        
        if (descSearchInput && descDropdown) {
            // Create a container for the secondary dropdown
            const secondaryDropdown = document.createElement('select');
            secondaryDropdown.className = 'filter-dropdown secondary-filter-dropdown';
            secondaryDropdown.style.display = 'none';
            secondaryDropdown.style.width = '100%';
            secondaryDropdown.style.marginTop = '5px';
            
            // Insert the secondary dropdown after the main dropdown
            descDropdown.parentNode.insertBefore(secondaryDropdown, descDropdown.nextSibling);
            
            // Function to extract item categories
            function extractItemCategories() {
                const rows = document.querySelectorAll('#ordersTable tbody tr');
                const categories = new Set();
                
                rows.forEach(row => {
                    const descCell = row.querySelector('td[data-column="description"]');
                    if (descCell) {
                        const spans = descCell.querySelectorAll('span');
                        spans.forEach(span => {
                            const text = span.textContent.trim();
                            const parts = text.split(' - ');
                            if (parts.length > 1) {
                                // Extract first word of item (category)
                                const itemName = parts[1].trim();
                                const category = itemName.split(' ')[0].toUpperCase();
                                categories.add(category);
                            }
                        });
                    }
                });
                
                return Array.from(categories).sort();
            }
            
            // Function to get all items that belong to a specific category
            function getItemsInCategory(category) {
                const rows = document.querySelectorAll('#ordersTable tbody tr');
                const items = new Set();
                
                rows.forEach(row => {
                    const descCell = row.querySelector('td[data-column="description"]');
                    if (descCell) {
                        const spans = descCell.querySelectorAll('span');
                        spans.forEach(span => {
                            const text = span.textContent.trim();
                            const parts = text.split(' - ');
                            
                            if (parts.length > 1) {
                                const itemDesc = parts[1].trim();
                                
                                // Check if this item belongs to the selected category
                                if (itemDesc.toUpperCase().startsWith(category.toUpperCase())) {
                                    // Keep the original format with quantity
                                    items.add(text);
                                }
                            }
                        });
                    }
                });
                
                return Array.from(items).sort();
            }
            
            // Store all item categories
            const allCategories = extractItemCategories();
            
            // Function to populate the main dropdown with categories
            function updateMainDropdown() {
                const searchValue = descSearchInput.value.trim().toLowerCase();
                
                // Clear current dropdown options except for the first "All" option
                while (descDropdown.options.length > 1) {
                    descDropdown.remove(1);
                }
                
                // Filter categories based on search text
                const filteredCategories = searchValue ? 
                    allCategories.filter(cat => cat.toLowerCase().includes(searchValue)) : 
                    allCategories;
                
                // Add filtered categories to dropdown
                filteredCategories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    descDropdown.appendChild(option);
                });
            }
            
            // Function to update the secondary dropdown with items in the selected category
            function updateSecondaryDropdown(category) {
                // Get all items in the selected category
                const items = getItemsInCategory(category);
                
                // Clear the secondary dropdown
                secondaryDropdown.innerHTML = '';
                
                // Add a default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Select Item --';
                secondaryDropdown.appendChild(defaultOption);
                
                // Add items to the secondary dropdown
                items.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;
                    secondaryDropdown.appendChild(option);
                });
                
                // Show the secondary dropdown if there are items
                if (items.length > 0) {
                    secondaryDropdown.style.display = 'block';
                } else {
                    secondaryDropdown.style.display = 'none';
                }
            }
            
            // Connect the search input to update the main dropdown
            descSearchInput.addEventListener('input', function() {
                updateMainDropdown();
                secondaryDropdown.style.display = 'none';
            });
            
            // When main dropdown selection changes
            descDropdown.addEventListener('change', function() {
                if (this.value) {
                    // Update and show the secondary dropdown with items in the selected category
                    updateSecondaryDropdown(this.value);
                } else {
                    // Hide the secondary dropdown when "All" is selected
                    secondaryDropdown.style.display = 'none';
                }
            });
            
            // When secondary dropdown selection changes
            secondaryDropdown.addEventListener('change', function() {
                if (this.value) {
                    // Set the search input to the selected item's name (without quantity)
                    const parts = this.value.split(' - ');
                    if (parts.length > 1) {
                        const itemName = parts[1].trim();
                        descSearchInput.value = itemName;
                        
                        // Trigger filtering
                        const event = new Event('input', { bubbles: true });
                        descSearchInput.dispatchEvent(event);
                    }
                }
            });
            
            // Initial population of main dropdown
            updateMainDropdown();
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Wharfage input AJAX save and total update
        document.querySelectorAll('.wharfage-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order-id');
                const value = parseFloat(this.value.replace(/,/g, '')) || 0;
                this.classList.add('saving');
                fetch(`/update-order-field/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ field: 'wharfage', value: value })
                })
                .then(response => response.json())
                .then(data => {
                    this.classList.remove('saving');
                    // Optionally update the total column in the row
                    if (data && data.newTotal !== undefined) {
                        const row = this.closest('tr');
                        if (row) {
                            const totalCell = row.querySelector('.total');
                            if (totalCell) {
                                totalCell.textContent = parseFloat(data.newTotal).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            }
                        }
                    }
                    calculateTotals();
                })
                .catch(() => this.classList.remove('saving'));
            });
        });

        // Value input AJAX save and total update
        document.querySelectorAll('.value-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order-id');
                const value = parseFloat(this.value.replace(/,/g, '')) || 0;
                this.classList.add('saving');
                fetch(`/update-order-field/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ field: 'value', value: value })
                })
                .then(response => response.json())
                .then(data => {
                    this.classList.remove('saving');
                    // Optionally update the total column in the row
                    if (data && data.newTotal !== undefined) {
                        const row = this.closest('tr');
                        if (row) {
                            const totalCell = row.querySelector('.total');
                            if (totalCell) {
                                totalCell.textContent = parseFloat(data.newTotal).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            }
                        }
                    }
                    calculateTotals();
                })
                .catch(() => this.classList.remove('saving'));
            });
        });

        // Discount input AJAX save and total update
        document.querySelectorAll('.discount-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order-id');
                const value = parseFloat(this.value.replace(/,/g, '')) || 0;
                this.classList.add('saving');
                fetch(`/update-order-field/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ field: 'discount', value: value })
                })
                .then(response => response.json())
                .then(data => {
                    this.classList.remove('saving');
                    // Optionally update the total column in the row
                    if (data && data.newTotal !== undefined) {
                        const row = this.closest('tr');
                        if (row) {
                            const totalCell = row.querySelector('.total');
                            if (totalCell) {
                                totalCell.textContent = parseFloat(data.newTotal).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            }
                        }
                    }
                    calculateTotals();
                })
                .catch(() => this.classList.remove('saving'));
            });
        });

        // Other input AJAX save and total update
        document.querySelectorAll('.other-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order-id');
                const value = parseFloat(this.value.replace(/,/g, '')) || 0;
                this.classList.add('saving');
                fetch(`/update-order-field/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ field: 'other', value: value })
                })
                .then(response => response.json())
                .then(data => {
                    this.classList.remove('saving');
                    // Optionally update the total column in the row
                    if (data && data.newTotal !== undefined) {
                        const row = this.closest('tr');
                        if (row) {
                            const totalCell = row.querySelector('.total');
                            if (totalCell) {
                                totalCell.textContent = parseFloat(data.newTotal).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            }
                        }
                    }
                    calculateTotals();
                })
                .catch(() => this.classList.remove('saving'));
            });
        });

        // Freight input AJAX save and total update
        document.querySelectorAll('.freight-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order-id');
                const value = parseFloat(this.value.replace(/,/g, '')) || 0;
                this.classList.add('saving');
                fetch(`/update-order-field/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ field: 'freight', value: value })
                })
                .then(response => response.json())
                .then(data => {
                    this.classList.remove('saving');
                    // Optionally update the total column in the row
                    if (data && data.newTotal !== undefined) {
                        const row = this.closest('tr');
                        if (row) {
                            const totalCell = row.querySelector('.total');
                            if (totalCell) {
                                totalCell.textContent = parseFloat(data.newTotal).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            }
                            
                            // Also update valuation field if it exists
                            if (data.valuation !== undefined) {
                                const valuationInput = row.querySelector('.valuation-input');
                                if (valuationInput) {
                                    valuationInput.value = parseFloat(data.valuation).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                }
                            }
                            
                            // Also update wharfage field if it exists
                            if (data.wharfage !== undefined) {
                                const wharfageInput = row.querySelector('.wharfage-input');
                                if (wharfageInput) {
                                    wharfageInput.value = parseFloat(data.wharfage).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                                }
                            }
                        }
                    }
                    calculateTotals();
                })
                .catch(() => this.classList.remove('saving'));
            });
        });

        // BIR input AJAX save and total update
        document.querySelectorAll('.bir-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const orderId = this.getAttribute('data-order-id');
                const value = parseFloat(this.value.replace(/,/g, '')) || 0;
                
                // Format the displayed value with commas and two decimal places
                this.value = value.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                
                this.classList.add('saving');
                fetch(`/update-order-field/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ field: 'bir', value: value })
                })
                .then(response => response.json())
                .then(data => {
                    this.classList.remove('saving');
                    // Update the total column in the row
                    if (data && data.newTotal !== undefined) {
                        const row = this.closest('tr');
                        if (row) {
                            const totalCell = row.querySelector('.total');
                            if (totalCell) {
                                totalCell.textContent = parseFloat(data.newTotal).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            }
                        }
                    }
                    calculateTotals();
                })
                .catch(error => {
                    console.error('Error updating BIR:', error);
                    this.classList.remove('saving');
                });
            });
        });

        // Update calculateTotals to include wharfage
        window.calculateTotals = function() {
            let totalFreight = 0, totalOriginalFreight = 0, totalValuation = 0, totalValue = 0, totalWharfage = 0, totalDiscount = 0, totalBir = 0, totalOthers = 0, totalAmount = 0;
            document.querySelectorAll('#ordersTable tbody tr').forEach(function(row) {
                if (row.style.display === 'none') return;
                
                // Handle freight (can be input or span)
                const freightInput = row.querySelector('.freight-input');
                const freightValue = freightInput ? freightInput.value : (row.querySelector('[data-column="freight"]')||{}).textContent || '0';
                totalFreight += parseFloat(freightValue.replace(/,/g, '') || 0);
                
                // Valuation is always displayed as text, not input - get from data-column="valuation"
                totalValuation += parseFloat((row.querySelector('[data-column="valuation"]')||{}).textContent?.replace(/,/g,'')||0);
                
                // Handle value (can be input or span)
                const valueInput = row.querySelector('.value-input');
                const valueValue = valueInput ? valueInput.value : (row.querySelector('[data-column="value"]')||{}).textContent || '0';
                totalValue += parseFloat(valueValue.replace(/,/g, '') || 0);
                
                // Handle wharfage (can be input or span)
                const wharfageInput = row.querySelector('.wharfage-input');
                const wharfageValue = wharfageInput ? wharfageInput.value : (row.querySelector('[data-column="wharfage"]')||{}).textContent || '0';
                totalWharfage += parseFloat(wharfageValue.replace(/,/g, '') || 0);
                
                // Handle discount (can be input or span)
                const discountInput = row.querySelector('.discount-input');
                const discountValue = discountInput ? discountInput.value : (row.querySelector('[data-column="discount"]')||{}).textContent || '0';
                totalDiscount += parseFloat(discountValue.replace(/,/g, '') || 0);
                
                // Handle BIR (can be input or span)
                const birInput = row.querySelector('.bir-input');
                const birValue = birInput ? birInput.value : (row.querySelector('[data-column="bir"]')||{}).textContent || '0';
                totalBir += parseFloat(birValue.replace(/,/g, '') || 0);
                
                // Handle others (can be input or span)
                const otherInput = row.querySelector('.other-input');
                const otherValue = otherInput ? otherInput.value : (row.querySelector('[data-column="other"]')||{}).textContent || '0';
                totalOthers += parseFloat(otherValue.replace(/,/g, '') || 0);
                
                totalAmount += parseFloat((row.querySelector('.total')||{}).textContent?.replace(/,/g,'')||0);
            });
            document.getElementById('totalFreight').textContent = totalFreight.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('totalValuation').textContent = totalValuation.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('totalValue').textContent = totalValue.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('totalWharfage').textContent = totalWharfage.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('totalDiscount').textContent = totalDiscount.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('totalBir').textContent = totalBir.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits: 2});
            document.getElementById('totalOthers').textContent = totalOthers.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            document.getElementById('totalAmount').textContent = totalAmount.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
        };
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    // Function to export only visible (filtered) rows to Excel
    document.getElementById('exportExcel').addEventListener('click', function () {
        // Get the table element
        const table = document.getElementById('ordersTable');

        // Clone the table structure (header only)
        const clonedTable = table.cloneNode(false);
        const thead = table.querySelector('thead').cloneNode(true);
        const tbody = document.createElement('tbody');

        // Remove the second row (filter row) from the cloned table header
        const filterRow = thead.querySelector('tr:nth-child(2)');
        if (filterRow) {
            filterRow.remove();
        }

        // Remove sort indicator spans (dropdown icons) from header
        const sortIndicators = thead.querySelectorAll('span[id*="SortIndicator"]');
        console.log(`Excel Export - Found ${sortIndicators.length} sort indicator spans to remove`);
        sortIndicators.forEach((span, index) => {
            console.log(`Excel Export - Removing span ${index + 1}: "${span.textContent}" (ID: ${span.id})`);
            span.remove();
        });
        
        // Additional cleanup: remove any remaining spans with arrow symbols
        const arrowSpans = thead.querySelectorAll('span');
        arrowSpans.forEach(span => {
            if (span.textContent.includes('▲') || span.textContent.includes('▼')) {
                console.log(`Excel Export - Removing arrow span: "${span.textContent}"`);
                span.remove();
            }
        });

        // Add header to cloned table
        clonedTable.appendChild(thead);
        clonedTable.appendChild(tbody);

        // Get only visible rows from the original table
        const originalRows = table.querySelectorAll('tbody tr');
        const visibleRows = Array.from(originalRows).filter(row => {
            const style = window.getComputedStyle(row);
            return style.display !== 'none';
        });

        console.log(`Total rows: ${originalRows.length}, Visible rows: ${visibleRows.length}`);

        // Clone visible rows and update with current data
        visibleRows.forEach(originalRow => {
            const clonedRow = originalRow.cloneNode(true);
            const originalCells = originalRow.querySelectorAll('td');
            const clonedCells = clonedRow.querySelectorAll('td');

            // Update cloned cells with current input values
            originalCells.forEach((originalCell, cellIndex) => {
                const input = originalCell.querySelector('input');
                if (input) {
                    clonedCells[cellIndex].textContent = input.value; // Copy input value to cloned cell
                }
            });

            tbody.appendChild(clonedRow);
        });

        // Create a new workbook
        const workbook = XLSX.utils.book_new();

        console.log(`Excel Export - Final visible rows: ${visibleRows.length}`);

        // Convert the cloned table to a worksheet
        const worksheet = XLSX.utils.table_to_sheet(clonedTable, { raw: true });

        // Remove unwanted columns (BL STATUS, REMARK, NOTE, IMAGE, VIEW BL)
        const unwantedColumns = [24, 25, 26]; // Column indices to exclude (0-based index)

        // Remove unwanted columns
        const range = XLSX.utils.decode_range(worksheet['!ref']);
        for (let col = range.e.c; col >= range.s.c; col--) {
            if (unwantedColumns.includes(col)) {
                for (let row = range.s.r; row <= range.e.r; row++) {
                    const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                    delete worksheet[cellAddress];
                }
            }
        }

        // Update the worksheet range
        worksheet['!ref'] = XLSX.utils.encode_range({
            s: { r: range.s.r, c: range.s.c },
            e: { r: range.e.r, c: range.e.c - unwantedColumns.length }
        });

        // Add the worksheet to the workbook
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Orders');

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, 'MASTER LIST - ES {{ $shipNum }} VOY {{ $voyageNum }}.xlsx');
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('exportPdf').addEventListener('click', function () {
            // Get the table element
            const table = document.getElementById('ordersTable');

            // Initialize jsPDF in landscape mode with short/legal size paper
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'landscape',
                unit: 'pt',
                format: 'legal', // Legal size paper
            });

            // Define the columns to include (0-based index)
            const includedColumns = [0, 2, 3, 4, 5, 6, 7, 22]; // BL, CONTAINER, CARGO STATUS, SHIPPER, CONSIGNEE, CHECKER, DESCRIPTION, REMARK

            // Extract table data from currently visible rows only
            const filteredRows = [];
            const headers = [];
            const tableHeaders = table.querySelectorAll('thead tr:nth-child(1) th');

            // Get headers for included columns
            tableHeaders.forEach((th, index) => {
                if (includedColumns.includes(index)) {
                    // Clone the header to avoid modifying the original
                    const headerClone = th.cloneNode(true);
                    
                    // Remove sort indicator spans (dropdown icons)
                    const sortIndicators = headerClone.querySelectorAll('span[id*="SortIndicator"]');
                    console.log(`PDF Export - Found ${sortIndicators.length} sort indicators in header ${index}`);
                    sortIndicators.forEach((span, spanIndex) => {
                        console.log(`PDF Export - Removing span ${spanIndex + 1}: "${span.textContent}" (ID: ${span.id})`);
                        span.remove();
                    });
                    
                    // Additional cleanup: remove any remaining spans with arrow symbols
                    const arrowSpans = headerClone.querySelectorAll('span');
                    arrowSpans.forEach(span => {
                        if (span.textContent.includes('▲') || span.textContent.includes('▼')) {
                            console.log(`PDF Export - Removing arrow span: "${span.textContent}"`);
                            span.remove();
                        }
                    });
                    
                    // Get clean header text
                    const cleanHeader = headerClone.textContent.trim();
                    console.log(`PDF Export - Header ${index}: "${cleanHeader}"`);
                    headers.push(cleanHeader);
                }
            });

            // Get only visible rows from the table
            const tableRows = table.querySelectorAll('tbody tr');
            const visibleRows = Array.from(tableRows).filter(row => {
                const style = window.getComputedStyle(row);
                return style.display !== 'none';
            });

            console.log(`PDF Export - Total rows: ${tableRows.length}, Visible rows: ${visibleRows.length}`);

            // Process each visible row
            visibleRows.forEach(row => {
                const rowData = [];
                
                row.querySelectorAll('td').forEach((td, index) => {
                    if (includedColumns.includes(index)) {
                        // Special handling for DESCRIPTION column (index 7)
                        if (index === 7) {
                            // Get all item descriptions and format as comma-separated
                            const spans = td.querySelectorAll('span');
                            const items = [];
                            
                            spans.forEach(span => {
                                const itemText = span.textContent.trim();
                                if (itemText) {
                                    items.push(itemText);
                                }
                            });
                            
                            // Join items with commas and spaces
                            const formattedItems = items.join(', ');
                            console.log(`PDF Export - Description items: ${formattedItems}`);
                            rowData.push(formattedItems);
                        } else {
                            // For other columns, get input value or text content
                            const input = td.querySelector('input');
                            if (input) {
                                rowData.push(input.value.trim());
                            } else {
                                rowData.push(td.textContent.trim());
                            }
                        }
                    }
                });
                
                if (rowData.length > 0) {
                    filteredRows.push(rowData);
                }
            });

            // Sort rows by CONSIGNEE column (index 4 in our filtered data)
            filteredRows.sort((a, b) => a[4].localeCompare(b[4]));

            console.log(`PDF Export - Final filtered rows: ${filteredRows.length}`);

            // Add table to PDF with column width adjustments
            doc.autoTable({
                head: [headers],
                body: filteredRows,
                startY: 20, // Start below the top margin
                margin: { top: 20, left: 20, right: 20, bottom: 20 },
                styles: { fontSize: 10, textColor: [0, 0, 0] }, // Set font size and text color
                theme: 'grid',
                columnStyles: {
                    0: { cellWidth: 50 },  // BL
                    1: { cellWidth: 100 },  // CONTAINER
                    2: { cellWidth: 120 }, // CARGO STATUS
                    3: { cellWidth: 130 }, // SHIPPER
                    4: { cellWidth: 130 }, // CONSIGNEE
                    5: { cellWidth: 100 },  // CHECKER
                    6: { cellWidth: 230 }, // DESCRIPTION
                    7: { cellWidth: 110 }, // REMARK
                },
            });

            // Save the PDF
            doc.save('MASTER LIST - ES {{ $shipNum }} VOY {{ $voyageNum }}.pdf');
        });
    });
</script>
<script>
    // DEBUG FUNCTIONS - Call these from browser console to test
    window.debugModal = function() {
        console.log('=== MODAL DEBUG INFO ===');
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const debugInfo = document.getElementById('debugInfo');
        
        console.log('Modal element:', modal);
        console.log('Modal image element:', modalImage);
        console.log('Debug info element:', debugInfo);
        
        if (modal) {
            console.log('Modal classes:', modal.className);
            console.log('Modal style display:', modal.style.display);
            console.log('Modal computed style:', window.getComputedStyle(modal).display);
        }
        
        return {
            modal: modal,
            modalImage: modalImage,
            debugInfo: debugInfo
        };
    };
    
    window.testModal = function(imageSrc = null) {
        const testImage = imageSrc || 'http://localhost/SFX-1/public/storage/order_images/bk9v793I6xeiHXxHY8gJr9Uaidf4b3BnbgSVMZaI.jpg';
        console.log('Testing modal with image:', testImage);
        openModal(testImage);
    };
</script>

<!-- Include editable fields scripts -->
@include('masterlist.debug-editable-fields')
