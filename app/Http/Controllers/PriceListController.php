<?php

namespace App\Http\Controllers;
use App\Models\PriceList;
use App\Models\Category;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;

class PriceListController extends Controller
{
    public function index(Request $request) {
        // Get categories from database
        $categories = Category::getAllCategories();

        // Start building the query
        $query = PriceList::query();

        // Check for search input
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;

            // Add conditions for item_code, item_name, and category
            $query->where(function ($q) use ($searchTerm) {
                $q->where('item_code', 'LIKE', "%$searchTerm%")
                  ->orWhere('item_name', 'LIKE', "%$searchTerm%")
                  ->orWhere('category', 'LIKE', "%$searchTerm%");
            });
        }

        // Add category filter functionality
        if ($request->has('category') && $request->category != '') {
            $categoryFilter = $request->category;
            $query->where('category', $categoryFilter);
        }
        
        // Get sort parameters - default to item_code and asc if not specified
        $sortColumn = $request->input('sort', 'item_code');
        $sortDirection = $request->input('direction', 'asc');
        
        // Validate sort column to prevent SQL injection
        $allowedSortColumns = ['item_code', 'item_name', 'category', 'price'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'item_code';  // Default sort column
        }
        
        // Apply the sorting
        $query->orderBy($sortColumn, $sortDirection);

        // Get per_page parameter, default to 10
        $perPage = $request->input('per_page', 10);
        
        // Validate per_page to prevent abuse
        $allowedPerPage = [10, 20, 25, 50, 100, 500];
        if ($perPage === 'all') {
            // Get all items without pagination
            $items = $query->get();
            // Create a mock paginator for consistency
            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $items->count(),
                $items->count(),
                1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        } else {
            if (!in_array((int)$perPage, $allowedPerPage)) {
                $perPage = 10; // Default per page
            }
            $items = $query->paginate($perPage);
        }
        
        // Append query parameters to pagination links
        $items->appends([
            'search' => $request->search, 
            'category' => $request->category,
            'sort' => $sortColumn,
            'direction' => $sortDirection,
            'per_page' => $perPage
        ]);

        // Check if no results were found
        if ($items->isEmpty() && $request->has('search')) {
            return view('pricelist.index', [
                'items' => $items,
                'categories' => $categories,
                'searchMessage' => 'The search term "' . $request->search . '" did not match any records.',
                'perPage' => $perPage
            ]);
        }

        // Pass the data to the Blade view
        return view('pricelist.index', compact('items', 'categories', 'sortColumn', 'sortDirection', 'perPage'));
    }

    public function store(Request $request) {
        // Get category prefixes from database
        $categoryPrefixes = Category::getAllPrefixes();

        // Check if category exists in prefixes
        if (!array_key_exists($request->category, $categoryPrefixes)) {
            return back()->withErrors(['category' => 'Invalid category selected.']);
        }

        // Validate the incoming data
        $request->validate([
            'item_name' => 'required',
            'category' => 'required|string',
            'unit' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'multiplier' => 'nullable|numeric|min:0',
        ]);

        // Get the prefix for the selected category
        $prefix = $categoryPrefixes[$request->category];

        // Find the latest item_code for this category
        $latestItem = PriceList::where('item_code', 'LIKE', "$prefix-%")
                            ->orderBy('item_code', 'desc')
                            ->first();

        // Determine the next item code
        if ($latestItem) {
            // Extract the numeric part and increment it
            preg_match('/\d+/', $latestItem->item_code, $matches);
            $newNumber = isset($matches[0]) ? intval($matches[0]) + 1 : 1;
        } else {
            $newNumber = 1; // Start at 001 if no item exists
        }

        // Format the item code (e.g., GM-001)
        $newItemCode = sprintf('%s-%03d', $prefix, $newNumber);
        if($request->price_type == 'multiplier'){
            $price = null;
            $multiplier = $request->price;
        }else{
            $price = $request->price;
            $multiplier = null;
        }
        // Create a new item
        $item = PriceList::create([
            'item_code' => $newItemCode,
            'item_name' => $request->item_name,
            'category' => $request->category,
            'unit' => $request->unit,
            'price' => $price,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'multiplier' => $multiplier,
        ]);

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item created successfully with Item Code: ' . $newItemCode,
                'item' => $item
            ]);
        }
        
        // For regular form submissions, preserve the current page, category, and sort parameters in the redirect
        $page = $request->input('page', 1);
        $category = $request->input('category');
        $sort = $request->input('sort', 'item_code');
        $direction = $request->input('direction', 'asc');
        $perPage = $request->input('per_page', 10);
        
        // Build the redirect with the proper parameters
        return redirect()->route('pricelist', [
            'page' => $page,
            'category' => $category,
            'sort' => $sort,
            'direction' => $direction,
            'per_page' => $perPage
        ])->with('success', 'Item created successfully with Item Code: ' . $newItemCode);
    }

    public function destroy($id, Request $request) {
        $item = PriceList::findOrFail($id);
        $item->delete();

        // Preserve the current page, category, and sort parameters in the redirect
        return redirect()->route('pricelist', [
            'page' => $request->query('page', 1),
            'category' => $request->query('category'),
            'sort' => $request->query('sort', 'item_code'),
            'direction' => $request->query('direction', 'asc'),
            'per_page' => $request->query('per_page', 10)
        ])->with('success', 'Item deleted successfully.');
    }

    public function update(Request $request, $id) {
        // Validate the incoming data
        $request->validate([
            'item_name' => 'required',
            'category' => 'required|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Find the item by ID
        $item = PriceList::findOrFail($id);

        // Update the item
        $item->update([
            'item_name' => $request->item_name,
            'category' => $request->category,
            'price' => $request->price,
        ]);

        // Get the page, category, and search from input parameters, as they're passed in the form
        $page = $request->input('page', 1);
        $category = $request->input('category');
        $search = $request->input('search');
        $sort = $request->input('sort', 'item_code');
        $direction = $request->input('direction', 'asc');
        $perPage = $request->input('per_page', 10);

        // Redirect back with a success message, preserving the current page, category filter, search term and sort parameters
        return redirect()->route('pricelist', [
            'page' => $page,
            'category' => $category,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'per_page' => $perPage
        ])->with('success', 'Item updated successfully.');
    }

    public function storeCategory(Request $request) {
        // Validate the category name
        $request->validate([
            'category_name' => 'required|string|max:50|unique:categories,name',
        ]);

        // Get the new category name
        $categoryName = strtoupper($request->category_name);

        // Generate a prefix for the new category (first two letters)
        $prefix = strtoupper(substr(str_replace(' ', '', $categoryName), 0, 2));
        
        // Make sure prefix is unique
        $existingPrefixes = Category::pluck('prefix')->toArray();
        $counter = 1;
        $originalPrefix = $prefix;
        while (in_array($prefix, $existingPrefixes)) {
            $prefix = $originalPrefix . $counter;
            $counter++;
        }

        // Create the new category
        Category::create([
            'name' => $categoryName,
            'prefix' => $prefix,
            'is_default' => false,
        ]);

        // Get the page, category, and sort parameters from the request
        $page = $request->input('page', 1);
        $category = $request->input('category');
        $sort = $request->input('sort', 'item_code');
        $direction = $request->input('direction', 'asc');
        $perPage = $request->input('per_page', 10);

        // Redirect back to the price list page, preserving all parameters
        return redirect()->route('pricelist', [
            'page' => $page,
            'category' => $category,
            'sort' => $sort,
            'direction' => $direction,
            'per_page' => $perPage
        ])->with('success', 'Category "' . $categoryName . '" added successfully.');
    }
}
