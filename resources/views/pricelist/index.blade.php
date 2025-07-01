<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Price List') }}
            </h2>
        </div>
    </x-slot>

    <div class="flex items-center justify-between w-full">
        <!-- SEARCH FORM -->
        <form method="GET" class="w-full max-w-xl">
            <div class="flex">
                <input type="hidden" name="sort" value="{{ request('sort', 'item_code') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                <input type="text" name="search" placeholder="Search by Item Code, Item Name, Category"
                    class="w-full px-4 py-2 border rounded-l-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white">
                <button type="submit" class="px-4 py-2 text-white bg-emerald-500 rounded-r-md hover:bg-emerald-700">
                    SEARCH
                </button>
            </div>
        </form>
    </div>

    <br>



    @if (isset($searchMessage))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
            {{ $searchMessage }}
        </div>
    @endif

    <!-- Pagination Controls -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-700 dark:text-gray-300">Show:</span>
            <form method="GET" class="inline">
                <!-- Preserve current parameters -->
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'item_code') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                
                <select name="per_page" onchange="this.form.submit()" 
                        class="px-3 py-1 border rounded-md text-sm bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:ring focus:ring-indigo-200">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20</option>
                    <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    <option value="500" {{ request('per_page', 10) == 500 ? 'selected' : '' }}>500</option>
                    <option value="all" {{ request('per_page', 10) == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </form>
            <span class="text-sm text-gray-700 dark:text-gray-300">entries per page</span>
        </div>
        
        @if(request('per_page', 10) != 'all')
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} results
            </div>
        @else
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing all {{ $items->count() }} results
            </div>
        @endif
    </div>

    <div class="grid gap-4 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
        <div class="h-full p-6 overflow-hidden col-span-2 bg-white rounded-md shadow-md dark:bg-dark-eval-1 ">
            <table class="table-auto w-full border-collapse border border-gray-300 dark:border-gray-700">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">
                            <div class="flex items-center justify-center">
                                Item Code
                                <a href="{{ route('pricelist', ['sort' => 'item_code', 'direction' => ((request('sort', 'item_code') == 'item_code' && request('direction', 'asc') == 'asc') ? 'desc' : 'asc'), 'category' => request('category'), 'search' => request('search'), 'per_page' => request('per_page', 10)]) }}" class="ml-1">
                                    @if(request('sort', 'item_code') == 'item_code')
                                        @if(request('direction', 'asc') == 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                        </svg>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">
                            <div class="flex items-center justify-center">
                                Item Name
                                <a href="{{ route('pricelist', ['sort' => 'item_name', 'direction' => ((request('sort', 'item_code') == 'item_name' && request('direction', 'asc') == 'asc') ? 'desc' : 'asc'), 'category' => request('category'), 'search' => request('search'), 'per_page' => request('per_page', 10)]) }}" class="ml-1">
                                    @if(request('sort', 'item_code') == 'item_name')
                                        @if(request('direction', 'asc') == 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                        </svg>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">Category 
                            <div class="mb-4">
                                <form method="GET" action="{{ route('pricelist') }}">
                                    <input type="hidden" name="sort" value="{{ request('sort', 'item_code') }}">
                                    <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                                    <select id="categoryFilter" name="category" class="w-full p-2 border rounded-md font-small bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring focus:ring-indigo-200" onchange="this.form.submit()">
                                        <option value="">Show All</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">
                            <div class="flex items-center justify-center">
                                Price
                                <a href="{{ route('pricelist', ['sort' => 'price', 'direction' => ((request('sort', 'item_code') == 'price' && request('direction', 'asc') == 'asc') ? 'desc' : 'asc'), 'category' => request('category'), 'search' => request('search'), 'per_page' => request('per_page', 10)]) }}" class="ml-1">
                                    @if(request('sort', 'item_code') == 'price')
                                        @if(request('direction', 'asc') == 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4" />
                                        </svg>
                                    @endif
                                </a>
                            </div>
                        </th>
                        @if (Auth::user()->roles->roles == 'Admin')
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if ($items->isEmpty())
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">No item found.</td>
                        </tr>
                    @else
                        @foreach ($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">{{ $item->item_code }}</td>
                            <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">{{ $item->item_name }}</td>
                            <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">{{ $item->category }}</td>
                            <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-right">{{ $item->price ? number_format($item->price, 2) : 'N/A' }}</td>
                            @if (Auth::user()->roles->roles == 'Admin')
                            <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <button
                                        class="text-blue-600 hover:text-blue-900"
                                        x-data=" "
                                        x-on:click.prevent="$dispatch('open-modal', 'edit-{{$item->id}}')">
                                        <x-heroicon-o-pencil class="w-5 h-5" aria-hidden="true" />
                                    </button>
                                    <form action="{{ route('pricelist.destroy', ['id' => $item->id, 'page' => request('page', 1), 'category' => request('category'), 'per_page' => request('per_page', 10)]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this item?')">
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            @if(request('per_page', 10) != 'all')
                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
        @foreach ($items as $item)
            <x-modal name="edit-{{ $item->id }}" focusable>
                <form method="POST" action="{{ route('pricelist.update', $item->id) }}?page={{ request()->query('page', 1) }}&category={{ request()->query('category') }}&per_page={{ request()->query('per_page', 10) }}" class="p-6 bg-white dark:bg-[#313647] rounded-lg shadow-md">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden fields to preserve pagination and filters -->
                    <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                    <input type="hidden" name="category" value="{{ request()->query('category') }}">
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    <input type="hidden" name="per_page" value="{{ request()->query('per_page', 10) }}">

                    <!-- Header (Edit Item + Close Button) -->
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('EDIT ITEM') }}
                        </h2>
                        <button @click="$dispatch('close')" type="button"
                            class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-2xl">
                            &times;
                        </button>
                    </div>

                    <div class="grid gap-4">
                        <!-- ITEM NAME -->
                        <div class="space-y-2">
                            <x-form.label for="item_name" :value="__('Item Name')" class="text-gray-800 dark:text-white"/>
                            <x-form.input-with-icon-wrapper>
                                <x-slot name="icon">
                                    <x-heroicon-o-cube class="w-5 h-5" />
                                </x-slot>
                                <x-form.input
                                    withicon id="item_name"
                                    class="block w-full bg-white dark:bg-[#313647] focus:ring focus:ring-indigo-300"
                                    type="text"
                                    name="item_name"
                                    value="{{ $item->item_name }}"
                                    required
                                />
                            </x-form.input-with-icon-wrapper>
                        </div>

                        <!-- CATEGORY -->
                        <div class="space-y-2">
                            <x-form.label for="category" :value="__('Category')" />
                            <select id="category" name="category" class="block w-full px-4 py-2 bg-white dark:bg-[#313647] text-gray-800 dark:text-white rounded-lg focus:ring focus:ring-indigo-300">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ $item->category == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- PRICE -->
                        <div class="space-y-2">
                            <x-form.label for="price" :value="__('Price')" class="text-gray-800 dark:text-white"/>
                            <x-form.input-with-icon-wrapper>
                                <x-slot name="icon">
                                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                                </x-slot>
                                <x-form.input
                                withicon id="price"
                                class="block w-full bg-white dark:bg-[#313647] focus:ring focus:ring-indigo-300"
                                type="text"
                                name="price"
                                value="{{ $item->price }}"
                                required
                            />
                            </x-form.input-with-icon-wrapper>
                        </div>

                        <div>
                            <x-button class="justify-center w-full gap-2">
                                <x-heroicon-o-user-add class="w-6 h-6" />
                                <span>{{ __('Update') }}</span>
                            </x-button>
                        </div>
                    </div>
                </form>
            </x-modal>
        @endforeach
        
        @if(Auth::user()->hasPermission('pricelist', 'create'))
        <div class="p-6 my-3 overflow-hidden col-span-1 bg-white rounded-md shadow-md sm:max-w-md dark:bg-dark-eval-1">
            <div class="mb-1">
                <span class="font-bold">CREATE PRICE LIST</span>
            </div>
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />
            
            <!-- Add New Category Button -->
            @if (Auth::user()->roles->roles == 'Admin')
            <div class="mb-3">
                <button 
                    class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700"
                    x-data=" "
                    x-on:click.prevent="$dispatch('open-modal', 'add-category')">
                    Add New Category
                </button>
            </div>
            @endif

            <form method="POST" action="{{ route('pricelist.store') }}?page={{ request()->query('page', 1) }}&category={{ request()->query('category') }}&per_page={{ request()->query('per_page', 10) }}" id="priceListForm">
                @csrf
                <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                <input type="hidden" name="category" value="{{ request()->query('category') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'item_code') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
                <input type="hidden" name="per_page" value="{{ request()->query('per_page', 10) }}">
                <div class="grid gap-4">
                    <!-- Item Name -->
                    <div class="space-y-2">
                        <x-form.label
                            for="item_name"
                            :value="__('Item Name')"
                        />

                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-cube aria-hidden="true" class="w-5 h-5" />
                            </x-slot>

                            <x-form.input
                                withicon
                                id="item_name"
                                class="block w-full focus:ring focus:ring-indigo-300"
                                type="text"
                                name="item_name"
                                :value="old('item_name')"
                                required
                                autofocus
                                placeholder="{{ __('Item Name') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <!-- Category Dropdown -->
                    <div class="space-y-2">
                        <x-form.label for="category" :value="__('Category')" />

                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <!-- Icon can be enabled if needed -->
                            </x-slot>

                            <select
                                id="category"
                                class="block w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-300
                                    dark:bg-gray-800 dark:text-white dark:border-gray-600"
                                name="category"
                                required
                            >
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" class="dark:bg-gray-800 dark:text-white">
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                    </x-form.input-with-icon-wrapper>
                    </div>

                    <!-- Radio Button -->
                    <div class="space-y-2" hidden>
                        <x-form.label for="price_type" :value="__('Select Type')" />

                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" id="priceOption" name="price_type" value="price" checked>
                                <span class="ml-2">Price</span>
                            </label>

                            <label class="flex items-center">
                                <input type="radio" id="multiplierOption" name="price_type" value="multiplier">
                                <span class="ml-2">Multiplier</span>
                            </label>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="space-y-2">
                        <x-form.label
                            for="price"
                            :value="__('Price / Multiplier')"
                        />

                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-currency-dollar aria-hidden="true" class="w-5 h-5" />
                            </x-slot>

                            <x-form.input
                                withicon
                                id="price"
                                class="block w-full focus:ring focus:ring-indigo-300"
                                type="number"
                                name="price"
                                :value="old('price')"
                                required
                                step="0.01"
                                min="0"
                                placeholder="{{ __('Price / Multiplier') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>
                    <div>
                        <x-button class="justify-center w-full gap-2" id="registerPriceListBtn">
                            <x-heroicon-o-user-add class="w-6 h-6" aria-hidden="true" />
                            <span>{{ __('Register') }}</span>
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
    <br>

    @foreach ($items as $item)
        <x-modal name="delete-{{$item->id}}" focusable>
            <form method="POST" action="{{ route('pricelist.destroy', $item->id) }}?page={{ request()->query('page', 1) }}&category={{ request()->query('category') }}&per_page={{ request()->query('per_page', 10) }}" class="p-6 bg-white dark:bg-[#313647] rounded-lg shadow-md">
                @csrf
                @method('DELETE')

                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('Delete Item?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Are you sure you want to delete this item? This action cannot be undone.') }}
                </p>

                <div class="mt-4 p-3 bg-white dark:bg-[#313647] rounded-md">
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>Item Code:</strong> {{ $item->item_code }}
                    </p>
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>Item Name:</strong> {{ $item->item_name }}
                    </p>
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>Category:</strong> {{ $item->category }}
                    </p>
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>Price:</strong> {{ $item->price }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-button>

                    <x-button variant="danger" class="ml-3">
                        {{ __('Delete Item') }}
                    </x-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('editModal', (id) => ({
            isEditModalOpen: false,
            editItemId: id,
            editItemName: '',
            editCategory: '',
            editPrice: '',

            openEditModal(id) {
                const item = {!! json_encode($items->keyBy('id')) !!}[id];

                if (item) {
                    this.editItemId = id;
                    this.editItemName = item.item_name;
                    this.editCategory = item.category;
                    this.editPrice = item.price;
                    this.isEditModalOpen = true;

                    document.body.classList.add("overflow-hidden"); // Disable background interaction
                    setTimeout(() => {
                        document.getElementById('edit_item_name').focus();
                    }, 100);
                }
            },

            closeEditModal() {
                this.isEditModalOpen = false;
                document.body.classList.remove("overflow-hidden"); // Enable interaction
            }
        }));
    });
</script>

<!-- Add Category Modal -->
<x-modal name="add-category" focusable>
    <form method="POST" action="{{ route('pricelist.category.store') }}?page={{ request()->query('page', 1) }}&category={{ request()->query('category') }}&per_page={{ request()->query('per_page', 10) }}" class="p-6 bg-white dark:bg-[#313647] rounded-lg shadow-md">
        @csrf
        
        <!-- Hidden fields to preserve pagination and filters -->
        <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
        <input type="hidden" name="category" value="{{ request()->query('category') }}">
        <input type="hidden" name="sort" value="{{ request('sort', 'item_code') }}">
        <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
        <input type="hidden" name="per_page" value="{{ request()->query('per_page', 10) }}">
        
        <!-- Header (Add New Category + Close Button) -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ __('Add New Category') }}
            </h2>
            <button @click="$dispatch('close')" type="button"
                class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-2xl">
                &times;
            </button>
        </div>

        <div class="grid gap-4">
            <!-- Category Name -->
            <div class="space-y-2">
                <x-form.label for="category_name" :value="__('Category Name')" class="text-gray-800 dark:text-white"/>
                <x-form.input-with-icon-wrapper>
                    <x-slot name="icon">
                        <x-heroicon-o-tag class="w-5 h-5" />
                    </x-slot>                            <x-form.input
                                withicon id="category_name"
                                class="block w-full bg-white dark:bg-[#313647] focus:ring focus:ring-indigo-300"
                                type="text"
                                name="category_name"
                                required
                                autofocus
                                placeholder="{{ __('Enter new category name') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                        @error('category_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Category names will be automatically capitalized.
                        </p>
            </div>

            <div>
                <x-button class="justify-center w-full gap-2 bg-blue-600 hover:bg-blue-700">
                    <x-heroicon-o-plus-circle class="w-6 h-6" />
                    <span>{{ __('Add Category') }}</span>
                </x-button>
            </div>
        </div>
    </form>
</x-modal>

<style>
    .modal-overlay {
        z-index: 150; /* Ensure the modal is above everything */
        background-color: #313647 !important;
    }
</style>

<!-- Add real-time price list update functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle the price list form submission
        const priceListForm = document.getElementById('priceListForm');
        
        if (priceListForm) {
            priceListForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                // Submit the form via AJAX
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
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
                        // Reset the form
                        priceListForm.reset();
                        
                        // Create the new item object to broadcast
                        const newItem = {
                            item_code: data.item.item_code,
                            item_name: data.item.item_name,
                            category: data.item.category,
                            price: data.item.price,
                            multiplier: data.item.multiplier || null
                        };
                        
                        // Broadcast the new item to all tabs via localStorage
                        localStorage.setItem('newPriceListItem', JSON.stringify({
                            timestamp: new Date().getTime(),
                            item: newItem
                        }));
                        
                        // Reload the current page to reflect changes
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error submitting price list form:', error);
                });
            });
        }
    });
</script>
