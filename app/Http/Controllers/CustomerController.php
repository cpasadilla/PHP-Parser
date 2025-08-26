<?php

namespace App\Http\Controllers;

use App\Models\ContainerReservation;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\locations;
use App\Models\order;
use App\Models\parcel;
use App\Models\PriceList;
use App\Models\Ship;
use App\Models\SubAccount;
use App\Models\voyage;

class CustomerController extends Controller {
    public function index(Request $request) {
        // Count customers and sub-accounts
        $customerCount = Customer::count();
        $subAccountCount = SubAccount::count();
        $totalCustomers = $customerCount + $subAccountCount;

        // Start building the query for customers
        $customerQuery = Customer::query();

        // Start building the query for sub-accounts
        $subAccountQuery = SubAccount::query();

        // Check for search input
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;

            // Search Customers
            $customerQuery->where(function ($q) use ($searchTerm) {
                $q->where('id', 'LIKE', "%$searchTerm%")
                  ->orWhere('first_name', 'LIKE', "%$searchTerm%")
                  ->orWhere('last_name', 'LIKE', "%$searchTerm%")
                  ->orWhere('company_name', 'LIKE', "%$searchTerm%")
                  ->orWhere('type', 'LIKE', "%$searchTerm%")
                  ->orWhere('share_holder', 'LIKE', "%$searchTerm%")
                  ->orWhere('phone', 'LIKE', "%$searchTerm%")
                  ->orWhere('email', 'LIKE', "%$searchTerm%");
            });

            // Search Sub-Accounts
            $subAccountQuery->where(function ($q) use ($searchTerm) {
                $q->where('customer_id', 'LIKE', "%$searchTerm%")
                  ->orWhere('first_name', 'LIKE', "%$searchTerm%")
                  ->orWhere('last_name', 'LIKE', "%$searchTerm%")
                  ->orWhere('company_name', 'LIKE', "%$searchTerm%")
                  ->orWhere('phone', 'LIKE', "%$searchTerm%");
            });
        }

        // Apply sorting
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');
        
        // Validate sort column to prevent SQL injection
        $allowedSortColumns = ['id', 'first_name', 'last_name', 'company_name'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'id';
        }
        
        // Validate sort direction
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';
        
        // Apply the sorting to both queries
        $customerQuery->orderBy($sortColumn, $sortDirection);
        $subAccountQuery->orderBy($sortColumn, $sortDirection);

        // Paginate results (10 per page)
        $perPage = 10;
        $customers = $customerQuery->paginate($perPage)->appends($request->only(['search', 'sort', 'direction']));
        $subAccounts = $subAccountQuery->paginate($perPage)->appends($request->only(['search', 'sort', 'direction']));

        // Check if both customers and sub-accounts are empty
        if ($customers->isEmpty() && $subAccounts->isEmpty()) {
            return view('customer.index', [
                'customer' => $customers,
                'subAccount' => $subAccounts,
                'totalCustomers' => $totalCustomers,
                'searchMessage' => 'The search term "' . $request->search . '" did not match any records.',
                'sortColumn' => $sortColumn,
                'sortDirection' => $sortDirection
            ]);
        }

        // Return data to the view
        return view('customer.index', [
            'customer' => $customers,
            'subAccount' => $subAccounts,
            'totalCustomers' => $totalCustomers,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection
        ]);
    }

    public function formatNumber($input) {
        // Remove commas from the input
        $number = str_replace(',', '', $input);

        // Convert to float and round to two decimal places
        return number_format((float)$number, 2, '.', '');
    }

    public function store(Request $request) {
        if ($request->account_type === 'main') {
            $validatedData = $request->validate([
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'company_name' => 'nullable|string|max:255',
                'type' => 'required|in:individual,company',
                'share_holder' => 'nullable|integer|in:0,1',
                'phone' => 'nullable|string|max:50', // Updated max length to accommodate multiple numbers
                'email' => 'nullable|email|unique:customers,email',
            ]);

            // Normalize the phone number
            $normalizedPhone = $this->normalizePhoneNumbers($request->phone);

            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'company_name' => $request->company_name,
                'type' => $request->type,
                'share_holder' => $request->share_holder ?? 0, // Default to 0 (No) if not provided
                'email' => $request->email,
                'phone' => $normalizedPhone,
            ]);

            if (!$customer) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Failed to create main account.']);
                }
                return redirect()->back()->with('error', 'Failed to create main account.');
            }

            // For AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Main account created successfully!',
                    'customer' => $customer
                ]);
            }

            // For regular form submissions
            $redirectParams = $request->only('search');
            if ($request->has('page')) {
                $redirectParams['page'] = $request->input('page', 1);
            }

            // Check if there's a custom redirect_to parameter
            if ($request->has('redirect_to')) {
                return redirect()->to($request->input('redirect_to'))->with('success', 'Main account created successfully!');
            }

            return redirect()->route('customer', $redirectParams)->with('success', 'Main account created successfully!');
        } else {
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'sub_first_name' => 'nullable|string|max:255',
                'sub_last_name' => 'nullable|string|max:255',
                'sub_company_name' => 'nullable|string|max:255',
                'sub_phone' => 'nullable|string|max:50', // Updated max length to accommodate multiple numbers
            ]);

            // Get the last sub-account number for the given customer_id
            $lastSubAccount = SubAccount::where('customer_id', $request->customer_id)
                ->orderBy('id', 'desc')
                ->first();

            // Initialize sub-account number
            $subAccountNumber = '';

            // Check if there is an existing sub-account
            if ($lastSubAccount) {
                // Extract the numeric part after the hyphen
                $parts = explode('-', $lastSubAccount->sub_account_number);

                if (count($parts) == 2) {
                    // If sub_account_number is like "1001-1", increment the second part
                    $secondPart = intval($parts[1]) + 1;
                    $subAccountNumber = $parts[0] . '-' . $secondPart;
                } else {
                    // If there's no hyphen, assume it's the first sub-account
                    $subAccountNumber = $lastSubAccount->sub_account_number . '-1';
                }
            } else {
                // If no sub-account exists, start from "customer_id-1"
                $subAccountNumber = $request->customer_id . '-1';
            }

            // Normalize the phone number
            $normalizedPhone = $this->normalizePhoneNumbers($request->sub_phone);

            $subAccount = SubAccount::create([
                'customer_id' => $request->customer_id,
                'sub_account_number' => $subAccountNumber,
                'first_name' => $request->sub_first_name,
                'last_name' => $request->sub_last_name,
                'company_name' => $request->sub_company_name,
                'phone' => $normalizedPhone,
            ]);

            if (!$subAccount) {
                return redirect()->back()->with('error', 'Failed to create sub-account.');
            }

            // Keep search parameter if it exists
            $redirectParams = $request->only('search');
            if ($request->has('page')) {
                $redirectParams['page'] = $request->input('page', 1);
            }

            // Check if there's a custom redirect_to parameter
            if ($request->has('redirect_to')) {
                return redirect()->to($request->input('redirect_to'))->with('success', 'Sub-account created successfully!');
            }

            return redirect()->route('customer', $redirectParams)
                ->with('success', 'Sub-account created successfully!');
        }
    }

    public function update(Request $request) {
        $request->validate([
            'id' => 'nullable|exists:customers,id',
            'fname' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'cname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50', // Allow multiple phone numbers
            'email' => 'nullable|email|max:255|unique:customers,email,' . $request->id,

            'sub_account_id' => 'nullable|exists:sub_accounts,id',
            'sub_first_name' => 'nullable|string|max:255',
            'sub_last_name' => 'nullable|string|max:255',
            'sub_company_name' => 'nullable|string|max:255',
            'sub_phone' => 'nullable|string|max:50', // Allow multiple phone numbers
        ]);

        // Normalize phone numbers
        $normalizedPhone = $this->normalizePhoneNumbers($request->phone);

        // Update Main Account (if ID is provided)
        if ($request->filled('id')) {
            $customer = Customer::findOrFail($request->id);
            $customer->update([
                'first_name' => $request->fname,
                'last_name' => $request->lname,
                'company_name' => $request->cname,
                'phone' => $normalizedPhone,
                'email' => $request->email,
            ]);
        }

        // Update Sub-Account (if sub_account_id is provided)
        if ($request->filled('sub_account_id')) {
            $subAccount = SubAccount::findOrFail($request->sub_account_id);
            $subAccount->update([
                'first_name' => $request->sub_first_name,
                'last_name' => $request->sub_last_name,
                'company_name' => $request->sub_company_name,
                'phone' => $this->normalizePhoneNumbers($request->sub_phone),
            ]);
        }

        return redirect()->route('customer', $request->only('page', 'search'))
            ->with('success', 'Customer and sub-account updated successfully!');
    }

    // Add a helper function to normalize phone numbers
    private function normalizePhoneNumbers($phone) {
        if (!$phone) {
            return null;
        }

        // Split multiple phone numbers by forward slashes
        $phoneNumbers = explode('/', $phone);

        // Trim each phone number
        $normalizedNumbers = array_map('trim', $phoneNumbers);

        // Join the trimmed numbers back with forward slashes
        return implode(' / ', $normalizedNumbers);
    }

    public function getSubAccounts($customerId) {
        // Retrieve sub-accounts related to the main account
        $subAccounts = SubAccount::where('customer_id', $customerId)->get();

        // Return them as a JSON response
        return response()->json($subAccounts);
    }

    public function destroy($id, Request $request) {
        $subAccount = SubAccount::find($id);

        if (!$subAccount) {
            // Preserve both page and search parameters
            $redirectParams = $request->only(['page', 'search']);
            return redirect()->route('customer', $redirectParams)
                ->with('error', 'Sub-account not found.');
        }

        $subAccount->delete();

        // Preserve both page and search parameters
        $redirectParams = $request->only(['page', 'search']);
        return redirect()->route('customer', $redirectParams)
            ->with('success', 'Sub-account deleted successfully.');
    }

    public function delete($id, Request $request) {
        $account = Customer::find($id);
        $sub = SubAccount::where('customer_id', $id)->get();

        if (!$account) {
            // Preserve both page and search parameters
            $redirectParams = $request->only(['page', 'search']);
            return redirect()->route('customer', $redirectParams)
                ->with('error', 'Customer account not found.');
        }

        foreach ($sub as $subs) {
            $subs->delete();
        }

        $account->delete();

        // Preserve both page and search parameters
        $redirectParams = $request->only(['page', 'search']);
        return redirect()->route('customer', $redirectParams)
            ->with('success', 'Customer account deleted successfully.');
    }

    public function details($id) {
        $consignee = Customer::where('id', $id)->get();
        $subs = SubAccount::where('customer_id', $id)->get();
        $customers = Customer::all(); // Fetch all customers from the database
        $call = $this->getCustomerDetails($id);

        return view('customer.info', compact('customers', 'consignee', 'subs'));
    }

    public function pass(Request $request) {
        $data = $request->all();

        // Store data permanently in session
        session()->put('order_data', $data);

        return redirect()->route('order');
    }

    public function order() {
        $info = session('order_data', []);

        if ($info['status'] == 'Shipper') {
            $id = $info['customer_id'];

            // Fix: Check both Customer and SubAccount
            $subCustomer = SubAccount::where('sub_account_number', $id)->first();
            $customer = Customer::where('id', $id)->first();

            if ($subCustomer) {
                // Check if both first_name and last_name are empty or null
                if (empty($subCustomer->first_name) && empty($subCustomer->last_name)) {
                    $cName = $subCustomer->company_name ?? 'Unknown';
                } else {
                    $cName = $subCustomer->first_name . ' ' . $subCustomer->last_name;
                }
                $cNum = $subCustomer->phone;
            } elseif ($customer) {
                // Check if both first_name and last_name are empty or null
                if (empty($customer->first_name) && empty($customer->last_name)) {
                    $cName = $customer->company_name ?? 'Unknown';
                } else {
                    $cName = $customer->first_name . ' ' . $customer->last_name;
                }
                $cNum = $customer->phone;
            } else {
                $cName = 'Unknown';
                $cNum = ''; // Removed "N/A"
            }

            $rId = $info['receiver_name'];
            $subReceiver = SubAccount::where('sub_account_number', $rId)->first();
            $mainReceiver = Customer::where('id', $rId)->first();

            if ($subReceiver) {
                // Check if both first_name and last_name are empty or null
                if (empty($subReceiver->first_name) && empty($subReceiver->last_name)) {
                    $rName = $subReceiver->company_name ?? 'Unknown';
                } else {
                    $rName = $subReceiver->first_name . ' ' . $subReceiver->last_name;
                }
                $rNum = $subReceiver->phone;
            } elseif ($mainReceiver) {
                // Check if both first_name and last_name are empty or null
                if (empty($mainReceiver->first_name) && empty($mainReceiver->last_name)) {
                    $rName = $mainReceiver->company_name ?? 'Unknown';
                } else {
                    $rName = $mainReceiver->first_name . ' ' . $mainReceiver->last_name;
                }
                $rNum = $mainReceiver->phone;
            } else {
                $rName = 'Unknown';
                $rNum = ''; // Removed "N/A"
            }
        } else {
            $rId = $info['customer_id'];
            $subReceiver = SubAccount::where('sub_account_number', $rId)->first();
            $customer = Customer::where('id', $rId)->first();

            if ($subReceiver) {
                // Check if both first_name and last_name are empty or null
                if (empty($subReceiver->first_name) && empty($subReceiver->last_name)) {
                    $rName = $subReceiver->company_name ?? 'Unknown';
                } else {
                    $rName = $subReceiver->first_name . ' ' . $subReceiver->last_name;
                }
                $rNum = $subReceiver->phone;
            } elseif ($customer) {
                // Check if both first_name and last_name are empty or null
                if (empty($customer->first_name) && empty($customer->last_name)) {
                    $rName = $customer->company_name ?? 'Unknown';
                } else {
                    $rName = $customer->first_name . ' ' . $customer->last_name;
                }
                $rNum = $customer->phone;
            } else {
                $rName = 'Unknown';
                $rNum = ''; // Removed "N/A"
            }

            $id = $info['receiver_name'];
            $subCustomer = SubAccount::where('sub_account_number', $id)->first();
            $mainCustomer = Customer::where('id', $id)->first();

            if ($subCustomer) {
                // Check if both first_name and last_name are empty or null
                if (empty($subCustomer->first_name) && empty($subCustomer->last_name)) {
                    $cName = $subCustomer->company_name ?? 'Unknown';
                } else {
                    $cName = $subCustomer->first_name . ' ' . $subCustomer->last_name;
                }
                $cNum = $subCustomer->phone;
            } elseif ($mainCustomer) {
                // Check if both first_name and last_name are empty or null
                if (empty($mainCustomer->first_name) && empty($mainCustomer->last_name)) {
                    $cName = $mainCustomer->company_name ?? 'Unknown';
                } else {
                    $cName = $mainCustomer->first_name . ' ' . $mainCustomer->last_name;
                }
                $cNum = $mainCustomer->phone;
            } else {
                $cName = 'Unknown';
                $cNum = ''; // Removed "N/A"
            }
        }

        $data = [
            'customer_id' => $id ?? '',
            'customer_name' => $cName ?? '',
            'customer_no' => $cNum ?? '',
            'receiver_id' => $rId ?? '',
            'receiver_name' => $rName ?? '',
            'receiver_no' => $rNum ?? '',
        ];

        $locations = locations::all();
        $lists = PriceList::all();
        $ships = Ship::all();
        return view('customer.order', compact('data', 'locations', 'lists', 'ships'));
    }

    public function location($location) {
        $capitalizedLocation = ucwords(strtolower($location));
        locations::create([
            'location' => $capitalizedLocation,
        ]);
        $locations = locations::all();

        return response()->json($locations);
    }

    public function pushOrder(Request $request) {
        $noValue = false;
        $checks = false; // Initialize to prevent undefined variable error
        $data = $request->all();
        $cart = json_decode($data['cartData'] ?? '[]');
        $ship = $data['ship_no'];
        $shipStatus = Ship::where('ship_number', $ship)->first();
        $origin = $data['origin'];
        $destination = $data['destination'] ?? null;
        $selectedVoyageGroup = $data['selected_voyage_group'] ?? null; // Get selected voyage group if provided
        $selectedVoyageNumber = $data['voyage_no'] ?? null; // Get the specific voyage number selected
        
        // Initialize variables that might not be set in all code paths
        $containerInReservationList = null;
        $existingOrder = null;

        // Use the specific voyage number if provided, otherwise fall back to automatic selection
        if ($selectedVoyageNumber) {
            $voyageNum = $selectedVoyageNumber;
        } else {
            // Fall back to automatic selection based on ship type
            if ($ship == "I" || $ship == "II") {
                $voyageNum = $this->shipOne($shipStatus, $ship, $origin, $selectedVoyageGroup);
            } else {
                $voyageNum = $this->shipThree($shipStatus, $ship, $selectedVoyageGroup);
            }
        }

        $orderId = $this->orderId($voyageNum, $ship);
        $container = $data['container_no'];
        
        // Check if the container number is already used in another order for the same ship and voyage
        // Only apply zero freight if this is a subsequent use of the container, not the first one
        $freight = $this->formatNumber($data['cartTotal'] ?? 0); // Default freight based on cart total
        $checks = false;
        
        if (!empty($container)) {
            // First check if the container exists in the container reservations for this ship and voyage
            $containerInReservationList = ContainerReservation::where('ship', $ship)
                ->where('voyage', $voyageNum)
                ->where('containerName', $container)
                ->first();
                
            if ($containerInReservationList) {
                // Only check for existing orders if the container is in the reservation list
                $existingOrder = Order::where('containerNum', $container)
                    ->where('shipNum', $ship)
                    ->where('voyageNum', $voyageNum)
                    ->first();
                    
                if ($existingOrder) {
                    // This is a subsequent use of the container, so apply zero freight
                    $freight = 0;
                    $checks = true;
                }
                // If no existing order found, this is the first use of the container,
                // so keep the normal freight calculation from cartTotal
            }
            // If container is not in the reservation list, use regular freight calculation
        }

        if ($origin == 'Manila') {
            $search = $data['consigneeId'];
        } else {
            $search = $data['shipperId'];
        }

        $customer = Customer::where('id', $search)->first();

        if (!$customer) {
            $customer = SubAccount::where('sub_account_number', $search)->first();
        }
        if (!$customer) {
            $subAccount = SubAccount::where('sub_account_number', $search)->first();
            if ($subAccount) {
                $customer = Customer::where('id', $subAccount->customer_id)->first();
            }
        }

        $discount = 0; // Initialize discount
        
        // Get value from the main field or the backup field
        $valueData = $data['value'] ?? $data['value_backup'] ?? 0;
        $value = $this->formatNumber($valueData);
        $other = $this->formatNumber($data['other'] ?? 0);
        
        // Check if the container is in the reservation list and if there's another order with this container
        $skipWharfage = false;
        // Check if this ship and voyage combination should skip wharfage
        if ($this->shouldSkipWharfageForShipVoyage($ship, $voyageNum)) {
            $skipWharfage = true;
            $wharfage = 0;
        } elseif ($containerInReservationList && $existingOrder) {
            // This is a subsequent use of the container, so don't charge wharfage
            $skipWharfage = true;
            $wharfage = 0;
        } else {
            // Check if parcels are only GROCERIES
            $onlyGroceries = true;
            $hasItems = false;
            $hasGM019orGM020 = false;
            
            // Check if $cart is not null before iterating
            if ($cart) {
                $hasItems = count($cart) > 0;
                foreach ($cart as $item) {
                    // Check if item is not GROCERIES
                    if ($item->category != 'GROCERIES') {
                        $onlyGroceries = false;
                    }
                    
                    // Check if the item is GM-019 or GM-020 (safely check if item_code property exists)
                    if (isset($item->item_code) && ($item->item_code == 'GM-019' || $item->item_code == 'GM-020')) {
                        $hasGM019orGM020 = true;
                    }
                    
                    // Also check itemCode property which might be used instead of item_code
                    if (isset($item->itemCode) && ($item->itemCode == 'GM-019' || $item->itemCode == 'GM-020')) {
                        $hasGM019orGM020 = true;
                    }
                }
            }
            
            // Set wharfage to zero if both VALUE and FREIGHT are zero
            if (($value <= 0 && $freight <= 0) || $skipWharfage) {
                $wharfage = 0;
            } else {
                // Use GROCERIES formula ONLY if all items are groceries and cart is not empty
                if ($onlyGroceries && $hasItems) {
                    $wharfage = $freight / 800 * 23;
                } else {
                    $wharfage = $freight / 1200 * 23;
                }
                // If FREIGHT is 0 (but VALUE is not 0), set wharfage to 11.20
                if ($freight == 0 && $value > 0) {
                    $wharfage = 11.20;
                } elseif ($wharfage > 0 && $wharfage < 11.20) {
                    $wharfage = 11.20;
                }
            }
            
            $wharfage = $this->formatNumber($wharfage);
        }
        
        // Check if $cart is not null before iterating
        $noValue = false;
        if ($cart) {
            foreach ($cart as $item) {
                // Items in FROZEN, PARCEL or AGGREGATES categories should have zero valuation
                if ($item->category == 'FROZEN' || $item->category == 'PARCEL' || $item->category == 'AGGREGATES') {
                    $noValue = true;
                }
            }
        }
        
        if ($noValue == true) {
            $valuation = 0;
        } else {
            $valuation = ($value + $freight) * 0.0075;
        }

        $totalAmount = $freight + $valuation + $other + $wharfage;
        $user = auth()->user();
        $userName = $user->fName . ' ' . $user->lName;

        $order = order::create([
            "orderId" => $orderId,
            "shipperId" => $data['shipperId'],
            "shipperName" => $data['shipper_name'],
            "shipperNum" => $data['shipper_contact'] ?? " ",
            "recId" => $data['consigneeId'],
            "recName" => $data['consignee_name'],
            "recNum" => $data['consignee_contact'] ?? " ",
            "origin" => $data['origin'],
            "destination" => $data['destination'],
            "shipNum" => $data['ship_no'],
            "voyageNum" => $voyageNum,
            "containerNum" => $data['container_no'],
            "cargoType" => $data['cargoType'] ?? " ",
            "gatePass" => $data['gate_pass_no'] ?? " ", // Removed "N/A"
            "checkName" => $data['checkerName'] ?? " ", // Default value
            "remark" => $data['remark'] ?? " ", // Removed "N/A"
            "totalAmount" => $totalAmount,
            "value" => $value,
            "other" => $other,
            "wharfage" => $wharfage,
            "freight" => $freight, // Save the discounted freight value
            "originalFreight" => $this->formatNumber($data['cartTotal']), // Save the original freight value
            "discount" => $discount, // Save the discount value
            "valuation" => $valuation,
            "orderCreated" => now(),
            "creator" => $userName,
        ]);

        // Only process cart items if cart is not empty
        if ($cart && !empty($cart)) {
            foreach ($cart as $item) {
                // Handle empty or zero values for weight and measurements
                $weight = !empty($item->weight) && $item->weight !== '0' && $item->weight !== '0.00' ? $item->weight : null;
                $length = !empty($item->length) && $item->length !== '0' && $item->length !== '0.00' ? $item->length : null;
                $width = !empty($item->width) && $item->width !== '0' && $item->width !== '0.00' ? $item->width : null;
                $height = !empty($item->height) && $item->height !== '0' && $item->height !== '0.00' ? $item->height : null;
                $multiplier = $item->multiplier == "N/A" || empty($item->multiplier) ? null : $item->multiplier;

                if (isset($checks) && $checks == true) {
                    $parcel = parcel::create([
                        'orderId' => $order->id,
                        'itemId' => $item->itemCode,
                        'itemName' => $item->itemName,
                        'itemPrice' => is_numeric($item->price) ? floatval($item->price) : 0,
                        'quantity' => is_numeric($item->quantity) ? floatval($item->quantity) : 0,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,
                        'multiplier' => $multiplier,
                        'desc' => $item->description,
                        'total' => 0,
                        'unit' => $item->unit,
                        'weight' => $weight,
                    ]);
                }
                else {
                    $parcel = parcel::create([
                        'orderId' => $order->id,
                        'itemId' => $item->itemCode,
                        'itemName' => $item->itemName,
                        'itemPrice' => is_numeric($item->price) ? floatval($item->price) : 0,
                        'quantity' => is_numeric($item->quantity) ? floatval($item->quantity) : 0,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,
                        'multiplier' => $multiplier,
                        'desc' => $item->description,
                        'total' => is_numeric($item->total) ? floatval($item->total) : 0,
                        'unit' => $item->unit,
                        'weight' => $weight,
                    ]);
                }
            }
        }

        session()->put('orderId', $order->id);

        return redirect()->route('customer.view-bl', ['orderId' => $orderId]);
    }

    public function viewbl(Request $request) {
        $info = session('orderId', []);
        $order = order::where('id', $info)->get();
        $parcels = parcel::where('orderId', $order->first()->id)->get(); // Ensure parcels are retrieved

        return view('customer.view-bl',compact('order', 'parcels'));
    }

    public function getCustomerDetails($id) {
        $customer = Customer::with('subAccounts')->find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $customerNames = [];
        $subPhone = [];
        $ids = [];

        // Ensure the main account name is always added
        if (!empty($customer->first_name) && !empty($customer->last_name)) {
            $mainName = $customer->first_name . ' ' . $customer->last_name;
        } elseif (!empty($customer->company_name)) {
            $mainName = $customer->company_name;
        } else {
            $mainName = "Unnamed Account"; // Fallback if no names exist
        }

        $customerNames[] = "(Main) " . $mainName;
        $subPhone[$customer->id] = $customer->phone;
        $ids["(Main) " . $mainName] = $customer->id;

        // Add sub-account names
        foreach ($customer->subAccounts as $sub) {
            if (!empty($sub->first_name) && !empty($sub->last_name)) {
                $name = "(Sub) " . $sub->first_name . ' ' . $sub->last_name;
                $customerNames[] = $name;
                $subPhone[ $sub->sub_account_number] = $sub->phone;
                $ids[$name] =  $sub->sub_account_number;
            } elseif (!empty($sub->company_name)) {
                $name = "(Sub) " . $sub->company_name;
                $customerNames[] = $name;
                $subPhone[ $sub->sub_account_number] = $sub->phone;
                $ids[$name] =  $sub->sub_account_number;

            }
        }

        return response()->json([
            'customer_id' => $customer->id,
            'customer_names' => $customerNames,
            'phone' => $customer->phone ?? " ",
            'sub' => $subPhone,
            'ids' => $ids,
        ]);
    }

    public function bl(Request $request) {
        $ships = Ship::where('status', '!=', 'DRYDOCKED')->get(); // Fetch available ships
        $locations = locations::all(); // Fetch locations
        $lists = PriceList::all();

        return view('customer.bl', compact('locations', 'lists', 'ships'));
    }

    public function searchCustomers(Request $request) {
        $query = $request->input('q');

        // Search in Customers (Main Accounts)
        $customers = Customer::where('first_name', 'LIKE', "%$query%")
                    ->orWhere('last_name', 'LIKE', "%$query%")
                    ->orWhere('company_name', 'LIKE', "%$query%")
                    ->selectRaw("id,
                        COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name,
                        IFNULL(NULLIF(phone, ''), '') AS phone") // Ensure empty phone is returned as ''
                    ->get();

        // Search in SubAccounts
        $subAccounts = SubAccount::where('first_name', 'LIKE', "%$query%")
                    ->orWhere('last_name', 'LIKE', "%$query%")
                    ->orWhere('company_name', 'LIKE', "%$query%")
                    ->selectRaw("sub_account_number,
                        COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name,
                        IFNULL(NULLIF(phone, ''), '') AS phone") // Ensure empty phone is returned as ''
                    ->get();

        // Merge Main Accounts and SubAccounts
        $results = $customers->merge($subAccounts);

        return response()->json($results);
    }

    public function shipOne($shipStatus, $ship, $origin, $selectedVoyageGroup = null) {
        if ($origin == 'MANILA'){
            $suffix = 'OUT'; // Going OUT from Manila
            $oppositeSuffix = 'IN';
        } else {
            $suffix = 'IN'; // Coming IN to Manila
            $oppositeSuffix = 'OUT';
        }

        // If a specific voyage group is selected, use that voyage
        if ($selectedVoyageGroup) {
            $selectedVoyage = voyage::where('ship', $ship)
                                  ->where('inOut', $suffix)
                                  ->where('voyage_group', $selectedVoyageGroup)
                                  ->where('lastStatus', 'READY')
                                  ->first();
            
            if ($selectedVoyage) {
                $selectedVoyage->update([
                    'lastUpdated' => now(),
                ]);
                
                $voyageNum = $selectedVoyage->v_num . '-' . $suffix;
                return $voyageNum;
            }
        }

        // Check for multiple READY voyages in the current direction (dual voyage scenario)
        $readyVoyages = voyage::where('ship', $ship)
                            ->where('inOut', $suffix)
                            ->where('lastStatus', 'READY')
                            ->where(function($query) {
                                $query->whereNull('dock_period')
                                ->orWhere('dock_period', 'like', 'post_dock_%');
                            })
                            ->orderBy('v_num', 'desc')
                            ->orderBy('is_primary', 'desc') // Primary voyages first
                            ->get();

        if ($readyVoyages->count() > 1) {
            // Multiple voyages available - use primary by default if no selection made
            $voyage = $readyVoyages->where('is_primary', true)->first() ?? $readyVoyages->first();
        } else {
            // Single voyage or no voyages - use existing logic
            $voyage = $readyVoyages->first();
        }

        if ($voyage) {
            // Use the selected/primary voyage
            $voyageNum = $voyage->v_num;
            
            // Keep it in READY status
            $voyage->update([
                'lastUpdated' => now(),
            ]);
            
            // Format the voyage number with the suffix for display
            $voyageNum = $voyageNum . '-'. $suffix;
            return $voyageNum;
        }

        // If no post-dock READY voyages, check for a matching voyage in the opposite direction (also post-dock)
        $oppositeVoyage = voyage::where('ship', $ship)
                              ->where('inOut', $oppositeSuffix)
                              ->where(function($query) {
                                $query->whereNull('dock_period')
                                ->orWhere('dock_period', 'like', 'post_dock_%');
                                })
                              ->latest('updated_at')
                              ->first();

        if ($oppositeVoyage) {
            // Get the numeric part of the opposite voyage
            $numericPart = preg_replace('/[^0-9]/', '', $oppositeVoyage->v_num);
            
            // Check if a matching voyage in this direction already exists
            $existingVoyage = voyage::where('ship', $ship)
                                  ->where('inOut', $suffix)
                                  ->where('v_num', $numericPart)
                                  ->where(function($query) {
                                    $query->whereNull('dock_period')
                                    ->orWhere('dock_period', 'like', 'post_dock_%');
                                  })
                                  ->first();
            
            if (!$existingVoyage) {
                // Create a matching voyage with the same numeric part
                $voyageNum = $numericPart;
                $voyage = voyage::create([
                    'ship' => $ship,
                    'v_num' => $voyageNum,
                    'lastStatus' => 'READY', // Always set to READY for new voyages
                    'lastUpdated' => now(),
                    'inOut' => $suffix,
                    'dock_period' => $oppositeVoyage->dock_period, // Use same dock period as opposite voyage
                ]);
            } else {
                // Use the existing matching voyage and set to READY
                $voyage = $existingVoyage;
                $voyageNum = $existingVoyage->v_num;
                
                $voyage->update([
                    'lastStatus' => 'READY', // Always set to READY when creating an order
                    'lastUpdated' => now(),
                ]);
            }
        } else {
            // No opposite voyage exists, look for the latest voyage in the current direction (post-dock first)
            $latestVoyage = voyage::where('ship', $ship)
                               ->where('inOut', $suffix)
                               ->where(function($query) {
                                    $query->whereNull('dock_period')
                                    ->orWhere('dock_period', 'like', 'post_dock_%');
                               })
                               ->orderBy('v_num', 'desc') // Order by voyage number, not timestamp
                               ->first();

            if (!$latestVoyage) {
                // No post-dock voyage in this direction exists yet, create the first one
                $voyageNum = '1';
                $voyage = voyage::create([
                    'ship' => $ship,
                    'v_num' => $voyageNum,
                    'lastStatus' => 'READY', // Always set to READY for new voyages
                    'lastUpdated' => now(),
                    'inOut' => $suffix,
                ]);
            } else {
                // Use the latest post-dock voyage in this direction
                $voyage = $latestVoyage;
                $voyageNum = $latestVoyage->v_num;
                
                $voyage->update([
                    'lastStatus' => 'READY', // Always set to READY when creating an order
                    'lastUpdated' => now(),
                ]);
            }
        }

        // Format the voyage number with the suffix for display
        $voyageNum = $voyageNum . '-'. $suffix;

        return $voyageNum;
    }

    public function shipThree($shipStatus,$ship, $selectedVoyageGroup = null) {
        // If a specific voyage group is selected, use that voyage
        if ($selectedVoyageGroup) {
            $selectedVoyage = voyage::where('ship', $ship)
                                  ->where('voyage_group', $selectedVoyageGroup)
                                  ->where('lastStatus', 'READY')
                                  ->first();
            
            if ($selectedVoyage) {
                $selectedVoyage->update([
                    'lastUpdated' => now(),
                ]);
                
                return $selectedVoyage->v_num;
            }
        }

        // Check for multiple READY voyages (dual voyage scenario)
        $readyVoyages = voyage::where('ship', $ship)
                           ->where('lastStatus', 'READY')
                           ->where(function($query) {
                               $query->whereNull('dock_period')
                               ->orWhere('dock_period', 'like', 'post_dock_%');
                           })
                           ->orderBy('v_num', 'desc')
                           ->orderBy('is_primary', 'desc') // Primary voyages first
                           ->get();

        if ($readyVoyages->count() > 1) {
            // Multiple voyages available - use primary by default if no selection made
            $voyage = $readyVoyages->where('is_primary', true)->first() ?? $readyVoyages->first();
            $voyageNum = $voyage->v_num;
            
            $voyage->update([
                'lastUpdated' => now(),
            ]);
            
            return $voyageNum;
        }

        // Single voyage or no voyages - use existing logic
        $readyVoyage = $readyVoyages->first();

        if ($readyVoyage) {
            // Use the existing READY voyage
            $voyage = $readyVoyage;
            $voyageNum = $readyVoyage->v_num;
            
            // Keep it in READY status
            $voyage->update([
                'lastUpdated' => now(),
            ]);
            
            return $voyageNum;
        }

        // If no post-dock READY voyages, look for the latest post-dock voyage
        $latestVoyage = voyage::where('ship', $ship)
                            ->where(function($query) {
                                $query->whereNull('dock_period')
                                      ->orWhere('dock_period', 'like', 'post_dock_%');
                            })
                            ->orderBy('v_num', 'desc') // Order by voyage number, not timestamp
                            ->first();

        if (!$latestVoyage) {
            // No post-dock voyages exist yet, create the first one
            $voyageNum = '1';
            $voyage = voyage::create([
                'ship' => $ship,
                'v_num' => $voyageNum,
                'lastStatus' => 'READY', // Always set to READY for new voyages
                'lastUpdated' => now(),
                'inOut' => '',
            ]);
        } else {
            // Use the latest post-dock voyage and set it to READY
            $voyage = $latestVoyage;
            $voyageNum = intval($latestVoyage->v_num);
            
            $voyage->update([
                'lastStatus' => 'READY', // Always set to READY when creating an order
                'lastUpdated' => now(),
            ]);
        }

        /*if ($shipStatus->status == 'NEW VOYAGE') {
            $voyageNum += 1;
            $shipStatus->update([
                'status' => 'CREATE BL' ,
            ]);
            $voyage = voyage::create([
                'ship' => $ship,
                'v_num' => $voyageNum,
                'lastStatus' => $shipStatus->status,
                'lastUpdated' => now(),
                'inOut' => '',
            ]);
        }*/

        return $voyageNum;
    }

    public function orderId($voyageNum, $ship) {
        // Get the highest orderId for this ship and voyage number, regardless of when it was updated
        $lastOrder = order::where('shipNum', $ship)
                       ->where('voyageNum', $voyageNum)
                       ->orderByRaw('CAST(orderId AS UNSIGNED) DESC')
                       ->first();

        if (!$lastOrder) {
            $orderId = 001;
        } else {
            $orderId = intval($lastOrder->orderId) + 1;
        }

        if ($orderId < 10) {
            $orderId = '00'.$orderId;
        } elseif ($orderId < 100) {
            $orderId = '0'.$orderId;
        }

        return $orderId;
    }

    public function searchForSOA(Request $request) 
    {
        $query = $request->get('query');
        
        $customers = Customer::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('company_name', 'LIKE', "%{$query}%")
            ->get();
            
        return response()->json($customers);
    }

    /**
     * Check if a container is a subsequent use for wharfage calculation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkContainerUsage(Request $request)
    {
        $container = $request->input('container');
        $ship = $request->input('ship');
        $voyage = $request->input('voyage');
        
        if (empty($container) || empty($ship) || empty($voyage)) {
            return response()->json(['skipWharfage' => false]);
        }
        
        // First check if the container exists in the container reservations for this ship and voyage
        $containerInReservationList = ContainerReservation::where('ship', $ship)
            ->where('voyage', $voyage)
            ->where('containerName', $container)
            ->first();
            
        if ($containerInReservationList) {
            // Only check for existing orders if the container is in the reservation list
            $existingOrder = Order::where('containerNum', $container)
                ->where('shipNum', $ship)
                ->where('voyageNum', $voyage)
                ->first();
                
            if ($existingOrder) {
                // This is a subsequent use of the container, so skip wharfage
                return response()->json(['skipWharfage' => true]);
            }
        }
        
        // If no existing order found or container is not in reservation list, don't skip wharfage
        return response()->json(['skipWharfage' => false]);
    }

    // Helper function to check if wharfage should be skipped based on ship and voyage
    private function shouldSkipWharfageForShipVoyage($ship, $voyage) {
        $ship = trim(strtoupper($ship));
        $voyage = trim(strtoupper($voyage));
        // Ship II Voyage 7,8,9,10 both IN and OUT
        if ($ship === 'II') {
            $excludedVoyages = ['7', '8', '9', '10'];
            foreach ($excludedVoyages as $excludedVoyage) {
                if (preg_match('/^' . preg_quote($excludedVoyage, '/') . '($|\s|[^0-9])/', $voyage)) {
                    return true;
                }
            }
        }
        // Ship III VOYAGE 30 TO 42
        if ($ship === 'III') {
            preg_match('/(\d+)/', $voyage, $matches);
            if (isset($matches[1])) {
                $voyageNumber = (int)$matches[1];
                if ($voyageNumber >= 30 && $voyageNumber <= 42) {
                    return true;
                }
            }
        }
        return false;
    }
    /**Add commentMore actions
     * Check if a ship is available for creating new orders
     * Ships in DRY DOCK status are not available for new orders
     * 
     * @param string $shipNumber
     * @return bool
     */
    private function isShipAvailableForOrders($shipNumber) {
        $ship = Ship::where('ship_number', $shipNumber)->first();
        
        if (!$ship) {
            return false;
        }
        
        // Ship is not available if it's in DRY DOCK status
        return $ship->status !== 'DRY DOCK';
    }

    /**
     * Get available voyages for a ship, considering dual voyage functionality
     * 
     * @param string $shipNumber
     * @param string $origin
     * @return array
     */
    public function getAvailableVoyages($shipNumber, $origin = null) {
        $voyages = [];
        
        if ($shipNumber == 'I' || $shipNumber == 'II') {
            // For ships I and II, we need to consider direction
            $suffix = ($origin == 'MANILA') ? 'OUT' : 'IN';
            
            $readyVoyages = voyage::where('ship', $shipNumber)
                                ->where('inOut', $suffix)
                                ->where('lastStatus', 'READY')
                                ->orderBy('v_num', 'desc')
                                ->orderBy('is_primary', 'desc')
                                ->get();
        } else {
            // For other ships
            $readyVoyages = voyage::where('ship', $shipNumber)
                                ->where('lastStatus', 'READY')
                                ->where(function($query) {
                                    $query->whereNull('dock_period')
                                    ->orWhere('dock_period', 'like', 'post_dock_%');
                                })
                                ->orderBy('v_num', 'desc')
                                ->orderBy('is_primary', 'desc')
                                ->get();
        }
        
        foreach ($readyVoyages as $voyage) {
            $label = 'Voyage ' . $voyage->v_num;
            if ($voyage->inOut) {
                $label .= '-' . $voyage->inOut;
            }
            
            // Add primary/secondary indicator if there are multiple voyages
            if ($readyVoyages->count() > 1) {
                $label .= ' (' . ($voyage->is_primary ? 'Primary' : 'Secondary') . ')';
            }
            
            $voyages[] = [
                'voyage_group' => $voyage->voyage_group,
                'v_num' => $voyage->v_num,
                'inOut' => $voyage->inOut,
                'is_primary' => $voyage->is_primary,
                'label' => $label,
                'display_name' => $voyage->v_num . ($voyage->inOut ? '-' . $voyage->inOut : '')
            ];
        }
        
        return $voyages;
    }

    /**
     * API endpoint to get available voyages for a ship
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableVoyagesApi(Request $request) {
        $shipNumber = $request->route('shipNum');
        $origin = $request->input('origin');
        $destination = $request->input('destination');
        
        if (!$shipNumber) {
            return response()->json(['success' => false, 'message' => 'Ship number is required'], 400);
        }
        
        $voyages = $this->getAvailableVoyagesWithRoute($shipNumber, $origin, $destination);
        
        return response()->json([
            'success' => true,
            'voyages' => $voyages,
            'message' => count($voyages) > 0 ? 'Voyages found' : 'No voyages available'
        ]);
    }
    
    /**
     * Get available voyages for a ship with route matching
     * 
     * @param string $shipNumber
     * @param string|null $origin
     * @param string|null $destination
     * @return array
     */
    public function getAvailableVoyagesWithRoute($shipNumber, $origin = null, $destination = null) {
        $voyages = [];
        
        // Get all READY voyages for this ship
        if ($shipNumber == 'I' || $shipNumber == 'II') {
            $readyVoyages = voyage::where('ship', $shipNumber)
                                ->where('lastStatus', 'READY')
                                ->where(function($query) {
                                    $query->whereNull('dock_period')
                                    ->orWhere('dock_period', 'like', 'post_dock_%');
                                })
                                ->orderBy('v_num', 'desc')
                                ->orderBy('is_primary', 'desc')
                                ->get();
        } else {
            $readyVoyages = voyage::where('ship', $shipNumber)
                                ->where('lastStatus', 'READY')
                                ->where(function($query) {
                                    $query->whereNull('dock_period')
                                    ->orWhere('dock_period', 'like', 'post_dock_%');
                                })
                                ->orderBy('v_num', 'desc')
                                ->orderBy('is_primary', 'desc')
                                ->get();
        }
        
        foreach ($readyVoyages as $voyage) {
            // Determine if this voyage matches the origin-destination route
            $matchesRoute = $this->doesVoyageMatchRoute($voyage, $origin, $destination, $shipNumber);
            
            $voyageNumber = $voyage->v_num;
            $label = 'Voyage ' . $voyage->v_num;
            
            // For Ships I and II, include direction and route info
            if ($voyage->inOut) {
                $voyageNumber .= '-' . $voyage->inOut;
                $label .= '-' . $voyage->inOut;
                
                // Add route description
                if ($voyage->inOut == 'IN') {
                    $routeDesc = ' (TO MANILA)';
                } else {
                    $routeDesc = ' (FROM MANILA)';
                }
                $label .= $routeDesc;
            }
            
            // Add primary/secondary indicator for dual voyages
            $sameDirectionVoyages = $readyVoyages->where('inOut', $voyage->inOut);
            if ($sameDirectionVoyages->count() > 1) {
                $label .= ' - ' . ($voyage->is_primary ? 'Primary' : 'Secondary');
            }
            
            // Add route match indicator for debugging
            if ($origin && $destination && !$matchesRoute) {
                $label .= ' [Different Route]';
            }
            
            $voyages[] = [
                'voyage_number' => $voyageNumber,
                'voyage_group' => $voyage->voyage_group,
                'v_num' => $voyage->v_num,
                'inOut' => $voyage->inOut,
                'is_primary' => $voyage->is_primary,
                'label' => $label,
                'matches_route' => $matchesRoute,
                'route_priority' => $matchesRoute ? 1 : 2 // Matching routes get higher priority
            ];
        }
        
        // Sort by route match first, then by priority
        usort($voyages, function($a, $b) {
            if ($a['route_priority'] != $b['route_priority']) {
                return $a['route_priority'] - $b['route_priority'];
            }
            return $b['is_primary'] - $a['is_primary']; // Primary first
        });
        
        return $voyages;
    }
    
    /**
     * Check if a voyage matches the origin-destination route
     * 
     * @param object $voyage
     * @param string|null $origin
     * @param string|null $destination
     * @param string $shipNumber
     * @return bool
     */
    private function doesVoyageMatchRoute($voyage, $origin, $destination, $shipNumber) {
        // If no origin/destination provided, can't match
        if (!$origin || !$destination) {
            return true; // Show all voyages if route not specified
        }
        
        // For Ships I and II with directional voyages
        if (($shipNumber == 'I' || $shipNumber == 'II') && $voyage->inOut) {
            // Manila is the hub - determine direction based on origin/destination
            $isFromManila = (strtoupper($origin) == 'MANILA');
            $isToManila = (strtoupper($destination) == 'MANILA');
            
            // Debug logging
            \Log::info("Route matching debug:", [
                'voyage' => $voyage->v_num . '-' . $voyage->inOut,
                'origin' => $origin,
                'destination' => $destination,
                'isFromManila' => $isFromManila,
                'isToManila' => $isToManila,
                'voyage_direction' => $voyage->inOut
            ]);
            
            if ($isFromManila && $voyage->inOut == 'OUT') {
                \Log::info("Match found: FROM MANILA (OUT voyage)");
                return true; // Going OUT from Manila
            }
            
            if ($isToManila && $voyage->inOut == 'IN') {
                \Log::info("Match found: TO MANILA (IN voyage)");
                return true; // Coming IN to Manila
            }
            
            \Log::info("No route match for this voyage");
            return false;
        }
        
        // For Ships III, IV, V - all voyages match (no directional constraint)
        return true;
    }
}