<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <button onclick="window.location.href='{{ route('masterlist.parcel') }}';" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Item Management') }}
            </h2>
            <div class="flex items-center space-x-4">
                <button onclick="exportToPDF()" class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 transition duration-150 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export as PDF
                </button>
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Items: {{ $parcels->total() }}</span>
            </div>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <!-- Card Header with summary stats -->
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg dark:from-gray-800 dark:to-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Items Overview</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Management of all shipped items and parcels</p>
                </div>
                <div class="flex justify-end">
                    @if(request('ship') || request('voyage') || request('search') || request('container') || (request('per_page') && request('per_page') != 10))
                        <a href="{{ route('masterlist.parcel') }}" class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition duration-150 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Combined Search and Filter Section -->
        <div class="mb-8">
            <form method="GET" action="{{ route('masterlist.parcel') }}" class="space-y-6">
                <!-- Hidden input to preserve per_page setting -->
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                
                <!-- Search Bar (Keep Position) -->
                <div class="relative">
                    <div class="flex">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search items (separate multiple items with commas, e.g., rice 25kg, rice 50kg)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-l-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                        <button type="submit" class="px-6 py-3 text-white bg-blue-600 rounded-r-lg hover:bg-blue-700 transition duration-150 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="relative">
                        <label for="ship" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Ship</label>
                        <div class="relative">
                            <select name="ship" id="ship" class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Ships</option>
                                @foreach($ships as $ship)
                                    <option value="{{ $ship->ship_number }}" {{ request('ship') == $ship->ship_number ? 'selected' : '' }}>
                                        {{ $ship->ship_number }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <label for="voyage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Voyage</label>
                        <div class="relative">
                            <select name="voyage" id="voyage" class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">All Voyages</option>
                                @foreach($voyages as $voyage)
                                    <option value="{{ $voyage->display_voyage }}" {{ request('voyage') == $voyage->display_voyage ? 'selected' : '' }}>
                                        {{ $voyage->display_voyage }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="container" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Container</label>
                        <input type="text" name="container" id="container" value="{{ request('container') }}" placeholder="Enter container number" class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="relative">
                        <label for="per_page_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Items per page</label>
                        <div class="relative">
                            <select id="per_page_select" 
                                    class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer transition duration-150">
                                @foreach([10, 20, 50, 100, 'all'] as $option)
                                    <option value="{{ $option }}" {{ (request('per_page', 10) == $option) ? 'selected' : '' }}>
                                        {{ $option === 'all' ? 'All' : $option }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div><br>
            </form>
        </div>

        <!-- Active Filters Display -->
        @if(request('ship') || request('voyage') || request('search') || request('container') || (request('per_page') && request('per_page') != 10))
            <div class="mb-6 flex flex-wrap gap-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Filters:</span>
                @if(request('ship'))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        Ship: {{ request('ship') }}
                    </span>
                @endif
                @if(request('voyage'))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        Voyage: {{ request('voyage') }}
                    </span>
                @endif
                @if(request('container'))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        Container: {{ request('container') }}
                    </span>
                @endif
                @if(request('search'))
                    @php
                        $searchTerms = array_filter(explode(',', request('search')));
                    @endphp
                    @foreach($searchTerms as $term)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Search: "{{ trim($term) }}"
                        </span>
                    @endforeach
                @endif
                @if(request('per_page') && request('per_page') != 10)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                        Per page: {{ request('per_page') === 'all' ? 'All' : request('per_page') }}
                    </span>
                @endif
            </div>
        @endif

        <!-- Table Section with enhanced styling -->
        <form method="POST" action="{{ route('masterlist.parcel.update') }}" class="mb-4">
            @csrf
            @method('PUT')
            <div class="flex justify-end mb-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
                    Save Changes
                </button>
            </div>
            <div class="overflow-x-auto border border-gray-200 rounded-lg dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-200 dark:bg-dark-eval-0">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">#</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">DATE</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">SHIP</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">VOYAGE</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">
                            <a href="{{ route('masterlist.parcel', array_merge(request()->query(), [
                                'sort' => 'orders.orderId',
                                'direction' => request('sort') == 'orders.orderId' && request('direction') == 'asc' ? 'desc' : 'asc'
                            ])) }}" class="flex items-center group">
                                BL#
                                <span class="ml-1 text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400">
                                    @if(request('sort') == 'orders.orderId')
                                        @if(request('direction') == 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-0 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">SHIPPER</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">CONSIGNEE</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">CONTAINER#</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">ITEM CODE</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">QUANTITY</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">UNIT</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">
                            <a href="{{ route('masterlist.parcel', array_merge(request()->query(), [
                                'sort' => 'parcels.itemName',
                                'direction' => request('sort') == 'parcels.itemName' && request('direction') == 'asc' ? 'desc' : 'asc'
                            ])) }}" class="flex items-center group">
                                ITEM NAME
                                <span class="ml-1 text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400">
                                    @if(request('sort') == 'parcels.itemName')
                                        @if(request('direction') == 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-0 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">DESCRIPTION</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">RATE</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">FREIGHT</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">DOCUMENTS</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">KEY</th>
                        <th scope="col" class="px-6 py-4 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300 border-b dark:border-gray-700">CHECKER</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                    @forelse($parcels as $parcel)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300 text-center">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->order_date ? \Carbon\Carbon::parse($parcel->order_date)->format('F d, Y') : '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">{{ $parcel->shipNum }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->voyageNum }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $parcel->blNumber }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->shipperName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->recName }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->containerNum }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->itemId }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->unit }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->itemName }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $parcel->desc }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                ₱{{ number_format($parcel->itemPrice, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
                                ₱{{ number_format($parcel->total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <input type="text" name="documents[{{ $parcel->id }}]" value="{{ $parcel->documents }}"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <input type="text" name="key[{{ $parcel->id }}]" value="{{ $parcel->key }}"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $parcel->checkName ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span class="block mt-2">No item found</span>
                                    <span class="block text-sm text-gray-500">Try adjusting your search or filter to find what you're looking for.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </form>

        <!-- Enhanced Pagination -->
        <div class="mt-8 bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Pagination Info -->
                <div class="text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-700 px-3 py-2 rounded-md border dark:border-gray-600">
                    @if(request('per_page') === 'all')
                        <span class="font-medium">Total: {{ $parcels->total() }}</span> items
                    @else
                        Showing <span class="font-medium">{{ $parcels->firstItem() ?? 0 }}-{{ $parcels->lastItem() ?? 0 }}</span> 
                        of <span class="font-medium">{{ $parcels->total() }}</span> parcels
                    @endif
                </div>
                
                <!-- Pagination Links -->
                @if(request('per_page') !== 'all' && $parcels->hasPages())
                    <div class="pagination-wrapper">
                        {{ $parcels->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
            
            @if(request('per_page') === 'all' && $parcels->total() > 100)
                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Performance Notice:</strong> Displaying all {{ $parcels->total() }} items may affect page loading speed. Consider using pagination for better performance.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <br>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Store all voyages data for filtering
        const allVoyages = @json($voyages);
        
        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const shipSelect = document.getElementById('ship');
            const voyageSelect = document.getElementById('voyage');
            
            // Filter voyages based on selected ship
            function filterVoyagesByShip() {
                const selectedShip = shipSelect.value;
                
                // Clear current voyage options except "All Voyages"
                voyageSelect.innerHTML = '<option value="">All Voyages</option>';
                
                if (selectedShip) {
                    // Filter voyages for the selected ship
                    const filteredVoyages = allVoyages.filter(voyage => voyage.ship === selectedShip);
                    
                    // Add filtered voyages to the dropdown
                    filteredVoyages.forEach(voyage => {
                        const option = document.createElement('option');
                        option.value = voyage.display_voyage;
                        option.textContent = voyage.display_voyage;
                        
                        // Maintain selection if the voyage was previously selected
                        if (voyage.display_voyage === '{{ request("voyage") }}') {
                            option.selected = true;
                        }
                        
                        voyageSelect.appendChild(option);
                    });
                } else {
                    // If no ship selected, show all voyages
                    allVoyages.forEach(voyage => {
                        const option = document.createElement('option');
                        option.value = voyage.display_voyage;
                        option.textContent = voyage.display_voyage;
                        
                        // Maintain selection if the voyage was previously selected
                        if (voyage.display_voyage === '{{ request("voyage") }}') {
                            option.selected = true;
                        }
                        
                        voyageSelect.appendChild(option);
                    });
                }
            }
            
            // Filter voyages when ship selection changes
            shipSelect.addEventListener('change', function() {
                filterVoyagesByShip();
                
                // Auto-submit when ship is selected or cleared
                if (!document.querySelector('form').getAttribute('data-submitting')) {
                    this.form.submit();
                }
            });
            
            // Auto-submit when voyage is selected
            voyageSelect.addEventListener('change', function() {
                if (!document.querySelector('form').getAttribute('data-submitting')) {
                    this.form.submit();
                }
            });
            
            // Initialize voyage filter based on current ship selection
            filterVoyagesByShip();
            
            // Handle per_page dropdown change
            const perPageSelect = document.getElementById('per_page_select');
            if (perPageSelect) {
                perPageSelect.addEventListener('change', function() {
                    const currentUrl = new URL(window.location.href);
                    const params = new URLSearchParams(currentUrl.search);
                    
                    // Update per_page parameter
                    params.set('per_page', this.value);
                    
                    // Reset to page 1 when changing per_page
                    params.set('page', '1');
                    
                    // Redirect to new URL with updated parameters
                    window.location.href = currentUrl.pathname + '?' + params.toString();
                });
            }
            
            // Prevent double submission when using the search button
            document.querySelector('form').addEventListener('submit', function() {
                // Add a submission flag to prevent auto-submission
                this.setAttribute('data-submitting', 'true');
            });
        });
        
        // Custom styling for pagination
        document.addEventListener('DOMContentLoaded', function() {
            // Add custom styles for pagination wrapper
            const paginationWrapper = document.querySelector('.pagination-wrapper');
            if (paginationWrapper) {
                const paginationNav = paginationWrapper.querySelector('.pagination');
                if (paginationNav) {
                    paginationNav.style.margin = '0';
                    paginationNav.style.display = 'flex';
                    paginationNav.style.justifyContent = 'center';
                    
                    // Style pagination links
                    const pageLinks = paginationNav.querySelectorAll('.page-link');
                    pageLinks.forEach(function(link) {
                        link.style.color = '#4B5563';
                        link.style.backgroundColor = '#F9FAFB';
                        link.style.border = '1px solid #D1D5DB';
                        link.style.padding = '0.5rem 0.75rem';
                        link.style.transition = 'all 0.15s ease-in-out';
                    });
                    
                    // Style active pagination link
                    const activeLink = paginationNav.querySelector('.page-item.active .page-link');
                    if (activeLink) {
                        activeLink.style.backgroundColor = '#3B82F6';
                        activeLink.style.color = '#FFFFFF';
                        activeLink.style.borderColor = '#3B82F6';
                    }
                }
            }
        });

        function exportToPDF() {
            // Get the table element
            const table = document.querySelector('table');
            
            // Create a clone of the table to modify for PDF
            const tableClone = table.cloneNode(true);
            
            // Clean up the table clone to remove unnecessary styling classes
            tableClone.classList.remove(...tableClone.classList);
            tableClone.style.width = '100%';
            
            // Remove all classes from table cells to eliminate tailwind styling
            const allElements = tableClone.querySelectorAll('*');
            allElements.forEach(el => {
                el.removeAttribute('class');
                // Remove any attributes that might affect layout
                if (el.hasAttribute('style')) {
                    el.removeAttribute('style');
                }
            });
            
            // Simplify complex cells like the BL NUMBER header
            const complexHeaders = tableClone.querySelectorAll('th a');
            complexHeaders.forEach(link => {
                const headerText = link.textContent.trim();
                const thParent = link.closest('th');
                if (thParent) {
                    // Replace complex content with just the text
                    thParent.textContent = headerText;
                }
            });
            
            // Define the columns we want to keep (in order)
            const columnsToKeep = [
                '#',
                'DATE',
                'BL#',
                'SHIPPER',
                'CONSIGNEE',
                'CONTAINER#',
                'ITEM NAME',
                'DESCRIPTION',
                'QUANTITY',
                'DOCUMENTS',
                'KEY',
                'CHECKER'
            ];

            // Get header row and all cells
            const headerRow = tableClone.querySelector('thead tr');
            const bodyRows = tableClone.querySelectorAll('tbody tr');

            if (headerRow) {
                // Get all header cells
                const headerCells = Array.from(headerRow.querySelectorAll('th'));
                
                // Create a map of column indices we want to keep
                const columnIndices = columnsToKeep.map(columnName => {
                    return headerCells.findIndex(cell => {
                        const cellText = cell.textContent.trim();
                        return cellText === columnName || 
                               (columnName === 'BL#' && cellText === 'BL NUMBER');
                    });
                }).filter(index => index !== -1);

                // Remove all columns that are not in our keep list
                headerCells.forEach((cell, index) => {
                    if (!columnIndices.includes(index)) {
                        cell.remove();
                    }
                });

                // Remove unwanted columns from body rows
                bodyRows.forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    cells.forEach((cell, index) => {
                        if (!columnIndices.includes(index)) {
                            cell.remove();
                        }
                    });
                });
            }
            
            // Define custom column widths optimized for A4 landscape PDF
            const columnWidths = {
                '#': '30px',          // Minimal width for row numbers
                'DATE': '65px',       // Enough for date format
                'BL#': '70px',        // Compact but readable
                'BL NUMBER': '70px',  // Matching BL#
                'SHIPPER': '110px',   // Optimized for names
                'CONSIGNEE': '110px', // Matching SHIPPER
                'CONTAINER#': '80px',  // Just right for container numbers
                'ITEM NAME': '110px', // Balanced for item names
                'DESCRIPTION': '130px', // Slightly wider for descriptions
                'QUANTITY': '50px',    // Sufficient for numbers
                'DOCUMENTS': '65px',   // Compact document references
                'KEY': '55px',        // Minimal width needed
                'CHECKER': '70px'     // Space for checker name
            };
            
            // Apply custom widths to columns based on header text
            const headers = tableClone.querySelectorAll('thead th');
            headers.forEach((header, index) => {
                const headerText = header.textContent.trim();
                if (columnWidths[headerText]) {
                    header.style.width = columnWidths[headerText];
                    
                    // Apply the same width to all cells in this column
                    const colCells = tableClone.querySelectorAll(`tbody tr td:nth-child(${index + 1})`);
                    colCells.forEach(cell => {
                        cell.style.width = columnWidths[headerText];
                        
                        // Center align numeric data
                        if (headerText === 'QUANTITY') {
                            cell.style.textAlign = 'center';
                        }
                    });
                }
            });
            
            // Configure the PDF options
            const opt = {
                margin: [0.4, 0.2, 0.4, 0.2], // Balanced margins for better fit
                filename: 'ITEM LIST.pdf',
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy'],
                    after: '.pagebreak-after',
                    avoid: ['tr', 'td']
                },
                html2canvas: { 
                    scale: 2, // Adjusted for better balance of quality and size
                    useCORS: true,
                    allowTaint: true,
                    letterRendering: true,
                    removeContainer: true,
                    logging: false
                },
                jsPDF: { 
                    unit: 'in', 
                    format: 'a4', 
                    orientation: 'landscape',
                    compress: true,
                    hotfixes: ["px_scaling"],
                    textLayer: true
                }
            };

            // Create a temporary container for the table
            const container = document.createElement('div');
            container.appendChild(tableClone);

            // Add some basic styling for the PDF
            container.style.padding = '10px';
            container.style.fontSize = '12px';
            container.style.fontFamily = 'Arial, sans-serif';
            
            // Style the cloned table for better PDF appearance
            tableClone.style.width = '100%';
            tableClone.style.borderCollapse = 'collapse';
            tableClone.style.fontSize = '12px'; // Increased from 10px to 12px
            tableClone.style.border = '1px solid #666'; // Thinner and lighter outer border
            
            // Style table cells with more visible borders
            const allCells = tableClone.querySelectorAll('th, td');
            allCells.forEach(cell => {
                cell.style.border = '0.5px solid #999'; // Thinner and lighter borders
                cell.style.padding = '5px'; // Reduced padding to minimize unnecessary space
                cell.style.fontSize = '12px'; // Increased from 10px to 12px
                cell.style.wordWrap = 'break-word';
                cell.style.maxWidth = '150px'; // Limit cell width to prevent overflow
                cell.style.whiteSpace = 'normal'; // Allow text to wrap
                cell.style.verticalAlign = 'middle'; // Center content vertically
                cell.style.textAlign = 'left'; // Default alignment
            });
            
            // Custom column widths for specific columns
            // Find the index of BL NUMBER and QUANTITY columns after removing excluded columns
            const findColumnIndex = (headerText) => {
                const headers = Array.from(tableClone.querySelectorAll('thead th'));
                return headers.findIndex(th => th.textContent.trim().includes(headerText));
            };
            
            const blNumberColIndex = findColumnIndex('BL NUMBER');
            const quantityColIndex = findColumnIndex('QUANTITY');
            
            // Apply specific widths if columns are found
            if (blNumberColIndex !== -1) {
                const blNumberCells = tableClone.querySelectorAll(`thead th:nth-child(${blNumberColIndex + 1}), tbody td:nth-child(${blNumberColIndex + 1})`);
                blNumberCells.forEach(cell => {
                    cell.style.width = '80px'; // Reduced width for BL NUMBER
                    cell.style.maxWidth = '80px';
                });
            }
            
            if (quantityColIndex !== -1) {
                const quantityCells = tableClone.querySelectorAll(`thead th:nth-child(${quantityColIndex + 1}), tbody td:nth-child(${quantityColIndex + 1})`);
                quantityCells.forEach(cell => {
                    cell.style.width = '60px'; // Reduced width for QUANTITY
                    cell.style.maxWidth = '60px';
                    cell.style.textAlign = 'center'; // Center-align quantities for better readability
                });
            }
            
            // Remove any extra spacing and fix cell spacing
            tableClone.style.borderCollapse = 'collapse';
            tableClone.style.borderSpacing = '0';
            tableClone.style.tableLayout = 'fixed'; // Fixed layout for more consistent cell sizes
            
            // Style header cells with stronger borders and background
            const headerCells = tableClone.querySelectorAll('th');
            headerCells.forEach(cell => {
                cell.style.backgroundColor = '#f0f0f0';
                cell.style.fontWeight = 'bold';
                cell.style.fontSize = '12px';
                cell.style.textAlign = 'center';
                cell.style.borderBottom = '1px solid #000';
                cell.style.padding = '8px 6px';
                cell.style.whiteSpace = 'nowrap';
                
                // Align specific headers
                const headerText = cell.textContent.trim();
                if (headerText === 'DESCRIPTION') {
                    cell.style.textAlign = 'left';
                }
            });

            // Style body cells
            const bodyCells = tableClone.querySelectorAll('tbody td');
            bodyCells.forEach(cell => {
                const columnIndex = Array.from(cell.parentElement.children).indexOf(cell);
                const headerText = headerCells[columnIndex]?.textContent.trim();
                
                // Base styling
                cell.style.padding = '4px 5px';
                cell.style.fontSize = '10px';
                cell.style.border = '1px solid #000';
                cell.style.whiteSpace = 'normal';
                cell.style.overflow = 'hidden';
                cell.style.wordBreak = 'break-word';
                
                // Special alignment and styling for specific columns
                if (headerText === '#' || headerText === 'QUANTITY') {
                    cell.style.textAlign = 'center';
                    cell.style.whiteSpace = 'nowrap';
                } else if (headerText === 'DATE' || headerText === 'BL#' || headerText === 'BL NUMBER' || headerText === 'CONTAINER#') {
                    cell.style.whiteSpace = 'nowrap';
                } else if (headerText === 'DESCRIPTION' || headerText === 'ITEM NAME') {
                    cell.style.textAlign = 'left';
                    cell.style.maxWidth = columnWidths[headerText];
                }
                
                // Handle DOCUMENTS and KEY columns
                if (headerText === 'DOCUMENTS' || headerText === 'KEY') {
                    const input = cell.querySelector('input');
                    if (input) {
                        cell.textContent = input.value || '';
                    }
                    cell.style.whiteSpace = 'nowrap';
                }
            });
            
            // Add a title to the PDF
            const title = document.createElement('h2');
            
            // Build dynamic title based on filters
            let titleText = 'ITEM LIST';
            const shipFilter = '{{ request("ship") }}';
            const voyageFilter = '{{ request("voyage") }}';
            const searchFilter = '{{ request("search") }}';
            
            if (shipFilter && voyageFilter) {
                titleText = `ITEM LIST - ES ${shipFilter} VOY ${voyageFilter}`;
            } else if (shipFilter) {
                titleText = `ITEM LIST - ES ${shipFilter}`;
            } else if (voyageFilter) {
                titleText = `ITEM LIST - VOY ${voyageFilter}`;
            }
            
            // Add search terms to the title if present
            if (searchFilter) {
                const searchTerms = searchFilter.split(',').map(term => term.trim()).filter(term => term);
                if (searchTerms.length > 0) {
                    if (searchTerms.length === 1) {
                        titleText += ` - ${searchTerms[0].toUpperCase()}`;
                    } else {
                        // Join all search terms with uppercase formatting
                        const allTermsUpperCase = searchTerms.map(term => term.toUpperCase()).join(', ');
                        titleText += ` - ${allTermsUpperCase}`;
                    }
                }
            }
            
            title.textContent = titleText;
            title.style.textAlign = 'center';
            title.style.fontSize = '20px'; // Increased from 18px to 22px
            title.style.color = '#000'; // Darker color for better visibility
            title.style.marginTop = '0'; // Remove top margin
            title.style.marginBottom = '5px'; // Reduced space below title
            title.style.fontWeight = 'bold'; // Make title bold
            container.insertBefore(title, tableClone);

            // Add current date and filters info
            const infoDiv = document.createElement('div');
            infoDiv.style.marginBottom = '5px';
            infoDiv.style.fontSize = '12px'; // Increased from 11px to 12px
            infoDiv.style.color = '#333';
            
            container.insertBefore(infoDiv, tableClone);

            // Add border to empty cells to ensure all cells have visible borders
            const emptyCells = tableClone.querySelectorAll('td:empty');
            emptyCells.forEach(cell => {
                cell.innerHTML = '&nbsp;'; // Add non-breaking space to ensure borders display
            });

            // Add page numbers
            const pageCount = function(pdf, pageNumber, totalPages) {
                pdf.setFontSize(10); // Increased from 8px to 10px
                pdf.setTextColor(100);
               
            };

            // Generate the PDF
            html2pdf()
                .set(opt)
                .from(container)
                .toPdf() // Generate PDF object first
                .get('pdf') // Get the PDF objecta
                .then(function(pdf) {
                    const totalPages = pdf.internal.getNumberOfPages();
                    
                    // Add page numbers to each page
                    for (let i = 1; i <= totalPages; i++) {
                        pdf.setPage(i);
                        pageCount(pdf, i, totalPages);
                    }
                    
                    // Enable text layer for copying and set properties
                    pdf.setProperties({
                        title: titleText,
                        creator: 'SFX System',
                        subject: 'Item List',
                        keywords: 'items, list, inventory',
                        textLayer: true
                    });
                    
                    // Save the PDF with enhanced properties
                    pdf.save(opt.filename);
                })
                .catch(error => console.error('Error generating PDF:', error));
        }
    </script>
</x-app-layout>