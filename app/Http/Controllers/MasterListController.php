<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Added for direct database queries
use Illuminate\Support\Facades\Storage; // Add this line
use Illuminate\Support\Facades\Auth; // Add this to access the authenticated user
use Illuminate\Pagination\LengthAwarePaginator; // Add this for custom pagination
use App\Models\Ship;
use App\Models\Customer; // Import the Customer model
use App\Models\SubAccount; // Import the SubAccount model
use App\Models\order; // Import the order model (lowercase)
use App\Models\PriceList;
use App\Models\voyage;
use App\Models\parcel; // Import the parcel model
use App\Models\OrderUpdateLog; // Add this at the top
use App\Models\OrderDeleteLog; // Add this for delete logging
use App\Models\ContainerReservation; // Import the ContainerReservation model
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;

class MasterListController extends Controller
{
    public function index(Request $request) {
        $ships = Ship::all(); // Retrieve all ships from the database
        return view('masterlist.index', compact('ships'));
    }

    public function store(Request $request) {
        $request->validate([
            'ship_number' => 'required|string|max:255|unique:ships,ship_number',
        ]);

        // Create a new ship record
        Ship::create([
            'ship_number' => $request->ship_number,
            'status' => 'READY', // Default status
        ]);

        return redirect()->back()->with('success', 'Ship added successfully!');
    }

    public function voyage($id) {
        $ship = Ship::findOrFail($id);
        $voyages = voyage::where('ship', $ship->ship_number)->orderBy('dock_number', 'desc')->orderBy('created_at', 'desc')->get();

        // Group voyages by dock number
        $voyagesByDock = [];
        
        foreach ($voyages as $voyage) {
            $dockNumber = $voyage->dock_number ?? 0; // Default to dock 0 for older records
            if (!isset($voyagesByDock[$dockNumber])) {
                $voyagesByDock[$dockNumber] = [];
            }
            $voyagesByDock[$dockNumber][] = $voyage;
        }
        
        // Sort dock numbers in descending order (newest dock first)
        krsort($voyagesByDock);
        
        // Get order counts for all voyages
        $allVoyageNumbers = $voyages->map(function($voyage) use ($ship) {
            if ($ship->ship_number == 'I' || $ship->ship_number == 'II') {
                return $voyage->v_num . '-' . $voyage->inOut;
            } else {
                return $voyage->v_num;
            }
        })->unique();
        
        $orderCounts = Order::where('shipNum', $ship->ship_number)
        ->selectRaw('shipNum, voyageNum, dock_number, COUNT(*) as total_orders')
        ->groupBy('shipNum', 'voyageNum', 'dock_number')
        ->get()
        ->mapWithKeys(function ($item) {
            return [ $item->voyageNum . '_dock_' . $item->dock_number => $item->total_orders ];
        });
        
        // Get origin and destination for each voyage
        $voyageRoutes = [];
        foreach ($voyages as $voyage) {
            if ($ship->ship_number == 'I' || $ship->ship_number == 'II') {
                $key = $voyage->v_num . '-' . $voyage->inOut;
            } else {
                $key = $voyage->v_num;
            }

            // Get the first order for this voyage and dock to extract origin and destination
            $firstOrder = Order::where('shipNum', $ship->ship_number)
                ->where('voyageNum', $key)
                ->where('dock_number', $voyage->dock_number ?? 0)
                ->first();
                
            $voyageRoutes[$key . '_dock_' . ($voyage->dock_number ?? 0)] = [
                'origin' => $firstOrder ? $firstOrder->origin : 'N/A',
                'destination' => $firstOrder ? $firstOrder->destination : 'N/A'
            ];
        }
        
        return view('masterlist.voyage_new', compact('ship', 'voyagesByDock', 'orderCounts', 'voyageRoutes'));
    }

    public function list(Request $request) {
        $orders = Order::all(); // Retrieve all orders, including the 'other' field
        return view('masterlist.list', compact('orders'));
    }

    public function customer(Request $request) {
        // Count customers and sub-accounts
        $customerCount = Customer::count();
        $subAccountCount = SubAccount::count();
        $totalCustomers = $customerCount + $subAccountCount;

        // Start building the query for customers
        $customerQuery = Customer::query();

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
        }

        // Paginate results (10 per page) and append search query
        $perPage = 10;
        $customers = $customerQuery->paginate($perPage)->appends($request->only('search'));

        // Return data to the view
        return view('masterlist.customer', [
            'customer' => $customers,
            'totalCustomers' => $totalCustomers,
            'searchMessage' => 'The search term "' . $request->search . '" did not match any records.'
        ]);
    }

    public function bl_list(Request $request, $customer_id) {
        // Fetch the main account
        $mainAccount = Customer::with('orders')->findOrFail($customer_id);

        // Fetch all sub-accounts connected to the main account
        $subAccounts = SubAccount::where('customer_id', $customer_id)->get();
        
        // Debug log start
        \Log::debug("Loading orders for sub-accounts of main account ID: {$customer_id}");
        
        // For each sub-account, manually load its orders
        foreach ($subAccounts as $subAccount) {
            // Log sub-account information for debugging
            $accountType = !empty($subAccount->company_name) ? 'Company' : 'Individual';
            $accountName = !empty($subAccount->company_name) ? 
                $subAccount->company_name : 
                $subAccount->first_name . ' ' . $subAccount->last_name;
            
            \Log::debug("Processing sub-account #{$subAccount->id} ({$accountType}): {$accountName}, Account Number: {$subAccount->sub_account_number}");

            // Get orders for sub-account based on origin rules
            $subAccountOrders = Order::where(function ($query) use ($subAccount) {
                $query->where(function ($subQuery) use ($subAccount) {
                    // Orders where sub-account is consignee and origin is Manila
                    $subQuery->where('recId', $subAccount->sub_account_number)
                             ->where('origin', 'Manila');
                })->orWhere(function ($subQuery) use ($subAccount) {
                    // Orders where sub-account is shipper and origin is Batanes
                    $subQuery->where('shipperId', $subAccount->sub_account_number)
                             ->where('origin', 'Batanes');
                });
            })->get();
            
            // Log how many orders were found
            \Log::debug("Found {$subAccountOrders->count()} orders for sub-account #{$subAccount->id}");
            
            // If it's a company and no orders found, check if there might be an issue with sub_account_number
            if (!empty($subAccount->company_name) && $subAccountOrders->count() === 0) {
                // Check both recName and shipperName as string matches for company name
                $potentialOrders = Order::where(function ($query) use ($subAccount) {
                    $query->where(function ($subQuery) use ($subAccount) {
                        // Try to match by company name in recName field
                        $subQuery->where('recName', 'LIKE', "%{$subAccount->company_name}%")
                             ->where('origin', 'Manila');
                    })->orWhere(function ($subQuery) use ($subAccount) {
                        // Try to match by company name in shipperName field
                        $subQuery->where('shipperName', 'LIKE', "%{$subAccount->company_name}%")
                             ->where('origin', 'Batanes');
                    });
                })->get();
                
                if ($potentialOrders->count() > 0) {
                    \Log::debug("Found {$potentialOrders->count()} potential orders for company {$subAccount->company_name} by name matching");
                    // Merge with any previously found orders
                    $subAccountOrders = $subAccountOrders->merge($potentialOrders);
                    
                    // For each order found by name, update its recId or shipperId to use the correct sub_account_number
                    foreach ($potentialOrders as $order) {
                        // Determine what to update based on origin
                        if ($order->origin === 'Manila') {
                            \Log::debug("Updating order #{$order->id} to set recId={$subAccount->sub_account_number}");
                            $order->recId = $subAccount->sub_account_number;
                        } else if ($order->origin === 'Batanes') {
                            \Log::debug("Updating order #{$order->id} to set shipperId={$subAccount->sub_account_number}");
                            $order->shipperId = $subAccount->sub_account_number;
                        }
                        $order->save();
                    }
                }
            }
            
            // Attach orders to the sub-account
            $subAccount->setRelation('orders', $subAccountOrders);
        }

        // Fetch orders for the main account (for backwards compatibility)
        $orders = Order::where(function ($query) use ($customer_id) {
            $query->where(function ($subQuery) use ($customer_id) {
                // Orders visible in Consignee account when origin is Manila
                $subQuery->where('recId', $customer_id)
                         ->where('origin', 'Manila');
            })->orWhere(function ($subQuery) use ($customer_id) {
                // Orders visible in Shipper account when origin is Batanes
                $subQuery->where('shipperId', $customer_id)
                         ->where('origin', 'Batanes');
            });
        })->paginate(10);

        // Pass data to the view
        return view('masterlist.bl_list', compact('mainAccount', 'subAccounts', 'orders'));
    }

    public function viewBl($shipNum, $voyageNum, $orderId) {
        // Fetch the order by ID
        $order = Order::findOrFail($orderId);

        // Fetch related parcels using the orderId
        $parcels = Parcel::where('orderId', $order->id)->get();
        // Pass the order and parcels to the view
        return view('masterlist.view-bl', compact('order', 'parcels'));
    }

    public function viewNoPriceBl($shipNum, $voyageNum, $orderId) {
        // Fetch the order by ID
        $order = Order::findOrFail($orderId);

        // Fetch related parcels using the orderId
        $parcels = Parcel::where('orderId', $order->id)->get();
        // Pass the order and parcels to the view
        return view('masterlist.view-no-price-bl', compact('order', 'parcels'));
    }

    public function container(Request $request) {
        $ships = Ship::all(); // Fetch all ships
        $customers = Customer::all(); // Fetch all customers
        $locations = \App\Models\locations::all(); // Fetch all locations
        $reservations = ContainerReservation::with('customer')
            ->orderBy('ship')
            ->orderBy('voyage')
            ->get()
            ->groupBy(['ship', 'voyage']); // Group reservations by ship and voyage

        return view('masterlist.container', compact('ships', 'customers', 'reservations', 'locations'));
    }

    public function reserveContainer(Request $request) {
        $validatedData = $request->validate([
            'ship' => 'required|string|max:255',
            'voyage' => 'required|string|max:255',
            'container_type' => 'required|in:10,20', // Updated field name
            'container_quantity' => 'required|integer|min:1', // Updated field name
            'container_name' => 'nullable|string|max:255', // Now nullable and not required
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'customer_id' => 'nullable|exists:customers,id', // Nullable
        ]);

        // Process container name for multiple entries (if provided)
        $containerName = isset($validatedData['container_name']) ? trim($validatedData['container_name']) : '';
        
        // Handle multiple container numbers if they are being entered with commas
        if (!empty($containerName) && strpos($containerName, ',') !== false) {
            // The input already has commas, so it's already in the correct format
            $containerNumbers = array_map('trim', explode(',', $containerName));
            \Log::info('Multiple container numbers detected in input', [
                'numbers' => $containerNumbers
            ]);
        } else {
            // Single container number or empty
            $containerNumbers = empty($containerName) ? [] : [$containerName];
        }
        
        // Special container handling - these get their own entry and don't get combined
        $isSpecialContainer = !empty($containerName) && (
            strpos($containerName, 'PADALA CONTAINER') !== false || 
            strpos($containerName, 'TEMPORARY CONTAINER') !== false
        );
        
        if (!empty($containerName) && !$isSpecialContainer) {
            // For regular containers, check for existing matching container reservations
            $existingContainer = ContainerReservation::where('ship', $validatedData['ship'])
                ->where('voyage', $validatedData['voyage'])
                ->where(function($query) use ($containerNumbers) {
                    foreach ($containerNumbers as $number) {
                        // Look for exact match or as part of a comma-separated list
                        $query->orWhere('containerName', 'LIKE', '%' . $number . '%');
                    }
                })
                ->first();
            
            if ($existingContainer) {
                // Check if all container numbers are already included in the existing container
                $existingNumbers = array_map('trim', explode(',', $existingContainer->containerName));
                $newNumbers = [];
                
                foreach ($containerNumbers as $number) {
                    if (!in_array($number, $existingNumbers)) {
                        $newNumbers[] = $number;
                    }
                }
                
                if (empty($newNumbers)) {
                    return redirect()->route('masterlist.container')
                        ->with('warning', 'All container numbers already exist for this ship and voyage.');
                }
                
                // Append only the new container numbers
                $updatedContainerName = $existingContainer->containerName . ', ' . implode(', ', $newNumbers);
                
                // Update the existing container record
                $existingContainer->update([
                    'containerName' => $updatedContainerName
                ]);
                
                return redirect()->route('masterlist.container')
                    ->with('success', 'Container numbers added to existing container record');
            }
        }
        
        // If no existing container match or it's a special container, create a new one
        ContainerReservation::create([
            'ship' => $validatedData['ship'],
            'voyage' => $validatedData['voyage'],
            'type' => $validatedData['container_type'],
            'quantity' => $validatedData['container_quantity'],
            'containerName' => $containerName ?? '', // Use empty string if null
            'origin' => $validatedData['origin'],
            'destination' => $validatedData['destination'],
            'customer_id' => $validatedData['customer_id'],
        ]);

        return redirect()->route('masterlist.container')->with('success', 'Container reserved successfully!');
    }

    public function updateReservation(Request $request)
    {
        $validatedData = $request->validate([
            'reservation_id' => 'required|exists:container_reservations,id',
            'type' => 'required|in:10,20',
            'containerName' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $reservation = ContainerReservation::findOrFail($validatedData['reservation_id']);
        $reservation->update($validatedData);

        return redirect()->route('masterlist.container')->with('success', 'Reservation updated successfully!');
    }

    public function deleteReservation($id) {
        $reservation = ContainerReservation::findOrFail($id);
        $reservation->delete();

        return redirect()->route('masterlist.container')->with('success', 'Reservation deleted successfully!');
    }

    public function destroy($id){
        // Find the ship by ID
        $ship = Ship::findOrFail($id);

        // Delete the ship
        $ship->delete();

        // Redirect back with a success message
        return redirect()->route('masterlist')->with('success', 'Ship deleted successfully!');
    }

    public function update(Request $request, $id) {
        $data = $request->input('status');
        $ship = Ship::findOrFail($id);
        $previousStatus = $ship->status;

        // Handle NEW DOCK status - triggers the dock period separation
        if ($data == 'NEW DOCK') {
            // When setting to NEW DOCK, mark all existing voyages as pre-dock
            // and create new voyages starting from 1
            $currentTimestamp = now();
            
            // Find the next dock number for this ship
            $maxDockNumber = voyage::where('ship', $ship->ship_number)
                ->max('dock_number') ?? -1;
            $newDockNumber = $maxDockNumber + 1;
            
            // Mark all existing voyages as belonging to their current dock number (if not already set)
            voyage::where('ship', $ship->ship_number)
                ->whereNull('dock_period')
                ->update([
                    'dock_period' => 'dock_' . $maxDockNumber . '_' . $currentTimestamp->timestamp,
                    'dock_number' => $maxDockNumber >= 0 ? $maxDockNumber : 0
                ]);
            
            // Mark all existing orders for this ship as belonging to the previous dock
            Order::where('shipNum', $ship->ship_number)
                ->whereNull('dock_period')
                ->update([
                    'dock_period' => 'dock_' . ($maxDockNumber >= 0 ? $maxDockNumber : 0) . '_' . $currentTimestamp->timestamp,
                    'dock_number' => $maxDockNumber >= 0 ? $maxDockNumber : 0
                ]);
            
            // Create new voyages starting from 1 for the new dock
            if ($id == '1' || $id == '2') {
                // For ships I and II - create both IN and OUT voyages starting from 1
                voyage::create([
                    'ship' => $ship->ship_number,
                    'v_num' => '1',
                    'lastStatus' => 'READY',
                    'lastUpdated' => $currentTimestamp,
                    'inOut' => 'IN',
                    'dock_period' => 'dock_' . $newDockNumber . '_' . $currentTimestamp->timestamp,
                    'dock_number' => $newDockNumber
                ]);

                voyage::create([
                    'ship' => $ship->ship_number,
                    'v_num' => '1',
                    'lastStatus' => 'READY',
                    'lastUpdated' => $currentTimestamp,
                    'inOut' => 'OUT',
                    'dock_period' => 'dock_' . $newDockNumber . '_' . $currentTimestamp->timestamp,
                    'dock_number' => $newDockNumber
                ]);
            } else {
                // For other ships - create single voyage starting from 1
                voyage::create([
                    'ship' => $ship->ship_number,
                    'v_num' => '1',
                    'lastStatus' => 'READY',
                    'lastUpdated' => $currentTimestamp,
                    'inOut' => '',
                    'dock_period' => 'dock_' . $newDockNumber . '_' . $currentTimestamp->timestamp,
                    'dock_number' => $newDockNumber
                ]);
            }
            
            // Automatically change status to CREATE BL after creating new voyages
            $data = 'CREATE BL';
        }
        else if ($data == 'NEW VOYAGE') {
            // Handle regular NEW VOYAGE status
            if ($id == '1' || $id == '2') {
                // Get latest voyage by v_num instead of updated_at
                $latestVoyageIn = voyage::where('ship', $ship->ship_number)
                    ->where('inOut', 'IN')
                    ->orderBy('v_num', 'desc')
                    ->first();

                $latestVoyageOut = voyage::where('ship', $ship->ship_number)
                    ->where('inOut', 'OUT')
                    ->orderBy('v_num', 'desc')
                    ->first();

                $newNumIn = $latestVoyageIn ? intval($latestVoyageIn->v_num) + 1 : 1;
                $newNumOut = $latestVoyageOut ? intval($latestVoyageOut->v_num) + 1 : 1;

                voyage::create([
                    'ship' => $ship->ship_number,
                    'v_num' => $newNumIn,
                    'lastStatus' => 'READY',
                    'lastUpdated' => now(),
                    'inOut' => 'IN'
                ]);

                voyage::create([
                    'ship' => $ship->ship_number,
                    'v_num' => $newNumOut,
                    'lastStatus' => 'READY',
                    'lastUpdated' => now(),
                    'inOut' => 'OUT'
                ]);
            } else {
                // For other ships
                $latestVoyage = voyage::where('ship', $ship->ship_number)
                    ->orderBy('v_num', 'desc')
                    ->first();

                $newNum = $latestVoyage ? intval($latestVoyage->v_num) + 1 : 1;

                voyage::create([
                    'ship' => $ship->ship_number,
                    'v_num' => $newNum,
                    'lastStatus' => 'READY',
                    'lastUpdated' => now(),
                    'inOut' => ''
                ]);
            }
        }

        $ship->status = $data;
        $ship->save();

        return redirect()->back()->with('success', 'Ship status updated successfully!');
    }

    public function editBL($orderId) {
        $order = Order::where('id', $orderId)->firstOrFail();

        $items = PriceList::all()->keyBy('item_code'); // Fetch all items from the PriceList model
        $parcels = parcel::where('orderId', $order->id)->get()->map(function ($item) use ($items) {
            // Use itemId to find matching item in PriceList since that's what's stored in the parcels table
            $priceListItem = $items[$item->itemId] ?? null;
            
            // Debug the lookup
            \Log::debug("Parcel item: {$item->itemId}, Category: " . ($priceListItem->category ?? 'Not found'));

            return [
                'itemCode' => $item->itemId ?? "",
                'itemName' => $item->itemName ?? "",
                'unit' => $item->unit ?? "",
                'category' => $priceListItem->category ?? "", // Use category from PriceList
                'weight' => $item->weight ?? " ",
                'value' => $item->value ?? " ",
                'length' => $item->length ?? " ",
                'width' => $item->width ?? " ",
                'height' => $item->height ?? " ",
                'multiplier' => $item->multiplier ?? "N/A",
                'price' => $item->itemPrice ?? 0,
                'description' => $item->desc ?? "",
                'quantity' => $item->quantity ?? 1,
                'total' => $item->total,
            ];
        });
        $total = $parcels->sum('total'); // Calculate the total of all parcels
        $lists = PriceList::all(); // Fetch all items from the PriceList model

        // Fetch all customers for the shipper and consignee dropdowns
        $customers = Customer::all();

        return view('masterlist.edit-bl', compact('order', 'parcels', 'lists', 'total', 'customers'));

    }

    public function updateBL(Request $request, $orderId) {
        $noValue = false;
        $checks = false; // Initialize $checks variable to prevent undefined variable error
        $data = $request->all();
        $origin = $data['origin'];
        $cart = json_decode($request->cartData);
        
        // Log the incoming request data for debugging
        Log::info('Update BL Request Data:', $data);
        Log::info('Cart Data:', ['cart' => $cart]);
        Log::info('Order ID parameter:', ['orderId' => $orderId]);

        // Validate cartTotal and value explicitly
        $request->validate([
            'cartTotal' => 'required|numeric',
            'value' => 'nullable|numeric',
        ]);

        // Validate the new fields
        $request->validate([
            'ship_no' => 'required|string|max:255',
            'voyage_no' => 'required|string|max:255',
            'container_no' => 'nullable|string|max:255',  // Changed from required to nullable
            'orderId' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'shipperName' => 'required|string|max:255',
            'shipperNum' => 'nullable|string|max:255',
            'recName' => 'required|string|max:255',
            'recNum' => 'nullable|string|max:255',
            'gatePass' => 'nullable|string|max:255',
            'remark' => 'nullable|string|max:255',
            'value' => 'nullable|numeric',
            'other' => 'nullable|numeric',
            'wharfage' => 'nullable|numeric',
            'checkerName' => 'nullable|string|max:255',
        ]);
        
        $voyageNum = $data['voyage_no'];
        $ship = $data['ship_no'];
        $container = $data['container_no'];
        $newOrderId = $data['orderId']; // Store the new order ID from the form
        
        // Fetch the existing order by ID - use the passed orderId parameter which is the database ID
        $order = Order::findOrFail($orderId);
        $existingOrderId = $order->orderId; // Store the existing order ID/BL number
        
        // Capture old values before any updates for change tracking
        $oldValues = [
            'orderId' => $order->orderId,
            'shipNum' => $order->shipNum,
            'voyageNum' => $order->voyageNum,
            'containerNum' => $order->containerNum,
            'origin' => $order->origin,
            'destination' => $order->destination,
            'shipperName' => $order->shipperName,
            'shipperNum' => $order->shipperNum,
            'recName' => $order->recName,
            'recNum' => $order->recNum,
            'gatePass' => $order->gatePass,
            'remark' => $order->remark,
            'value' => $order->value,
            'other' => $order->other,
            'freight' => $order->freight,
            'valuation' => $order->valuation,
            'discount' => $order->discount,
            'totalAmount' => $order->totalAmount,
            'checkName' => $order->checkName,
        ];
        
        Log::info('Existing order found with ID:', ['databaseId' => $order->id, 'displayOrderId' => $existingOrderId]);
        Log::info('Attempting to update to new Order ID:', ['newOrderId' => $newOrderId]);
        
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
                ->where('id', '!=', $orderId)  // Exclude current order
                ->first();
                
            if ($existingOrder) {
                // If there's another order with this container, apply zero freight
                // This means this is a subsequent use of the container
                $freight = 0;
                $checks = true;
            } else {
                // No other order with this container, apply normal freight
                $freight = $this->formatNumber($data['cartTotal']);
            }
            
            Log::info('Container reservation check on BL update:', [
                'container' => $container, 
                'inReservationList' => true,
                'existingOrderFound' => isset($existingOrder),
                'freight' => $freight
            ]);
        } else {
            // Container not in reservation list, apply normal freight
            $freight = $this->formatNumber($data['cartTotal']);
            
            Log::info('Container reservation check on BL update:', [
                'container' => $container, 
                'inReservationList' => false,
                'freight' => $freight
            ]);
        }

        $value = $this->formatNumber($data['value']);
        $other = $this->formatNumber($data['other']);
        $discount = 0; // Initialize discount

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
            // Calculate wharfage based on parcel categories
            $onlyGroceries = true;
            $hasGM019orGM020 = false;
            
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

            // Set wharfage to zero if both VALUE and FREIGHT are zero
            if (($value <= 0 && $freight <= 0) || $skipWharfage) {
                $wharfage = 0;
            } else {
                // Calculate wharfage based on whether parcels contain only groceries or not
                // Also check if the order contains GM-019 or GM-020 items
                if ($onlyGroceries || $hasGM019orGM020) {
                    $wharfage = $freight / 800 * 23; // Formula for GROCERIES only or when contains GM-019/GM-020
                } else {
                    $wharfage = $freight / 1200 * 23; // Formula for other items
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

        foreach ($cart as $item) {
            if ($item->category == 'FROZEN' || $item->category == 'PARCEL') {
                $noValue = true;
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

        // Log the calculated freight and valuation
        Log::info('Calculated Freight:', ['freight' => $freight]);
        Log::info('Calculated Valuation:', ['valuation' => $valuation]);

        // Update the order with the new data - update() instead of create()
        $order->update([
            "shipNum" => $data['ship_no'],
            "voyageNum" => $data['voyage_no'],
            "containerNum" => $data['container_no'],
            "orderId" => $newOrderId, // Use the new order ID from the form
            'origin' => $data['origin'],
            'destination' => $data['destination'],
            'shipperId' => $data['shipperId'],
            'shipperName' => $data['shipperName'],
            'shipperNum' => $data['shipperNum'] ?? '',
            'recId' => $data['consigneeId'],
            'recName' => $data['recName'],
            'recNum' => $data['recNum'] ?? '',
            "cargoType" => $data['cargoType'] ?? " ",
            "gatePass" => $data['gatePass'] ?? " ", // Fixed: Using 'gatePass' instead of 'gate_pass_no'
            "remark" => $data['remark'] ?? " ",
            "totalAmount" => $totalAmount,
            "value" => $value,
            "other" => $other,
            "wharfage" => $wharfage,
            "freight" => $freight,
            "originalFreight" => $this->formatNumber($data['cartTotal']),
            "discount" => $discount,
            "valuation" => $valuation,
            "checkName" => $request->checkerName,
        ]);

        Log::info('Order updated successfully with new Order ID:', ['newOrderId' => $newOrderId]);

        // Log the update in the order_update_logs table with field-specific information
        $userName = Auth::user()->fName . ' ' . Auth::user()->lName;
        
        // Helper function to log only changed fields
        $logFieldIfChanged = function($fieldName, $oldValue, $newValue) use ($orderId, $userName) {
            // Convert values to strings for comparison to handle null values properly
            $oldValStr = $oldValue === null ? '' : (string)$oldValue;
            $newValStr = $newValue === null ? '' : (string)$newValue;
            
            // Only log if the values are actually different
            if ($oldValStr !== $newValStr) {
                OrderUpdateLog::create([
                    'order_id' => $orderId,
                    'updated_by' => $userName,
                    'field_name' => $fieldName,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'action_type' => 'update'
                ]);
            }
        };

        // Log major field updates from the BL edit only if they changed
        $fieldsToCheck = [
            'orderId' => [$oldValues['orderId'], $newOrderId],
            'shipNum' => [$oldValues['shipNum'], $ship],
            'voyageNum' => [$oldValues['voyageNum'], $voyageNum],
            'containerNum' => [$oldValues['containerNum'], $container ?? ''],
            'origin' => [$oldValues['origin'], $origin],
            'destination' => [$oldValues['destination'], $data['destination']],
            'shipperName' => [$oldValues['shipperName'], $data['shipperName']],
            'shipperNum' => [$oldValues['shipperNum'], $data['shipperNum'] ?? ''],
            'recName' => [$oldValues['recName'], $data['recName']],
            'recNum' => [$oldValues['recNum'], $data['recNum'] ?? ''],
            'gatePass' => [$oldValues['gatePass'], $data['gatePass'] ?? ''],
            'remark' => [$oldValues['remark'], $data['remark'] ?? ''],
            'value' => [$oldValues['value'], $value],
            'other' => [$oldValues['other'], $other],
            'freight' => [$oldValues['freight'], $cartTotal],
            'valuation' => [$oldValues['valuation'], $valuation],
            'discount' => [$oldValues['discount'], $discount],
            'totalAmount' => [$oldValues['totalAmount'], $totalAmount],
            'checkName' => [$oldValues['checkName'], $request->checkerName ?? ''],
        ];

        foreach ($fieldsToCheck as $fieldName => [$oldValue, $newValue]) {
            $logFieldIfChanged($fieldName, $oldValue, $newValue);
        }

        // Delete existing parcels associated with the order
        Parcel::where('orderId', $order->id)->delete();

        // Decode the cart data and save the items to the database
        if ($request->filled('cartData')) {
            foreach ($cart as $item) {
                // Handle empty or zero values for weight and measurements
                $weight = !empty($item->weight) && $item->weight !== '0' && $item->weight !== '0.00' && $item->weight !== ' ' ? $item->weight : null;
                $length = !empty($item->length) && $item->length !== '0' && $item->length !== '0.00' && $item->length !== ' ' ? $item->length : null;
                $width = !empty($item->width) && $item->width !== '0' && $item->width !== '0.00' && $item->width !== ' ' ? $item->width : null;
                $height = !empty($item->height) && $item->height !== '0' && $item->height !== '0.00' && $item->height !== ' ' ? $item->height : null;
                $multiplier = $item->multiplier === "N/A" || empty($item->multiplier) || $item->multiplier === '0' || $item->multiplier === ' ' ? null : $item->multiplier;

                if ($checks == true){
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
                else{
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

        // Redirect back with a success message
        return redirect()->route('masterlist.view-bl', ['shipNum' => $ship, 'voyageNum' => $voyageNum, 'orderId' => $order->id])->with('success', 'BL updated successfully!');
    }

    public function searchCustomerDetails(Request $request) {
        $name = $request->input('name');

        // Search in Customers (Main Accounts)
        $customer = Customer::whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$name])
            ->orWhere('company_name', $name)
            ->first();

        // If not found in Customers, search in SubAccounts
        if (!$customer) {
            $subAccount = SubAccount::whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$name])
                ->orWhere('company_name', $name)
                ->first();

            if ($subAccount) {
                return response()->json([
                    'success' => true,
                    'id' => $subAccount->sub_account_number,
                    'phone' => $subAccount->phone ?? "", // Ensure phone is not null
                ]);
            }
        }

        if ($customer) {
            return response()->json([
                'success' => true,
                'id' => $customer->id,
                'phone' => $customer->phone ?? "", // Ensure phone is not null
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No matching record found.']);
    }

    public function voyageOrders(Request $request, $shipNum, $voyageNum) {
        $orders = Order::where('shipNum', $shipNum)
            ->where('voyageNum', $voyageNum)
            ->with('parcels') // Eager load parcels
            ->orderBy('orderId', 'asc') // Sort by orderId in ascending order
            ->get();

        return view('masterlist.list', compact('orders', 'shipNum', 'voyageNum'));
    }

    public function voyageOrdersById(Request $request, $voyageId) {
        // Get the specific voyage by ID
        $voyage = voyage::findOrFail($voyageId);
        
        // Determine the voyage key for orders lookup
        $ship = Ship::where('ship_number', $voyage->ship)->first();
        if ($ship && ($ship->ship_number == 'I' || $ship->ship_number == 'II')) {
            $voyageKey = $voyage->v_num . '-' . $voyage->inOut;
        } else {
            $voyageKey = $voyage->v_num;
        }
        
        // Get orders for this specific voyage and dock number
        $orders = Order::where('shipNum', $voyage->ship)
            ->where('voyageNum', $voyageKey)
            ->where('dock_number', $voyage->dock_number ?? 0)
            ->with('parcels')
            ->orderBy('orderId', 'asc')
            ->get();

        return view('masterlist.list', compact('orders'), [
            'shipNum' => $voyage->ship,
            'voyageNum' => $voyageKey
        ]);
    }

    public function updateOrderField(Request $request, $orderId) {
        $field = $request->field;
        
        Log::info('Received updateOrderField request:', [
            'orderId' => $orderId,
            'field' => $field,
            'value' => $request->value
        ]);

        // Different validation rules based on field type
        if ($field === 'containerNum' || $field === 'OR' || $field === 'AR' || $field === 'remark' || $field === 'checkName' || $field === 'cargoType') {
            $request->validate([
                'field' => 'required|string|in:OR,AR,image,containerNum,bir,freight,value,valuation,discount,other,wharfage,originalFreight,padlock_fee,remark,checkName,cargoType',
                'value' => 'nullable|string|max:255',
            ]);
        } else {
            $request->validate([
                'field' => 'required|string|in:OR,AR,image,containerNum,bir,freight,value,valuation,discount,other,wharfage,originalFreight,padlock_fee,remark,checkName,cargoType',
                'value' => 'nullable|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:9999',
                'date' => 'nullable|date',
            ]);
        }

        $order = Order::findOrFail($orderId);
        $field = $request->field;

        Log::info('Update Order Field Request:', $request->all());
        Log::info('Order Before Update:', $order->toArray());

        // Store old value for logging
        $oldValue = $order->$field ?? null;
        $newValue = $request->value;

        // Helper function to log field updates only if values changed
        $logFieldUpdate = function($fieldName, $oldVal, $newVal) use ($orderId) {
            // Convert values to strings for comparison to handle null values properly
            $oldValStr = $oldVal === null ? '' : (string)$oldVal;
            $newValStr = $newVal === null ? '' : (string)$newVal;
            
            // Only log if the values are actually different
            if ($oldValStr !== $newValStr) {
                OrderUpdateLog::create([
                    'order_id' => $orderId,
                    'updated_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                    'field_name' => $fieldName,
                    'old_value' => $oldVal,
                    'new_value' => $newVal,
                    'action_type' => 'update'
                ]);
            }
        };

        if ($field === 'discount') {
            $discountValue = $request->value;
            $originalTotal = $order->totalAmount + $order->discount; // Restore the original total by adding back the current discount

            $oldDiscountValue = $order->discount;
            $oldTotalValue = $order->totalAmount;

            if ($discountValue === null) {
                // Restore the total amount to its original value
                $order->totalAmount = $originalTotal;
                $order->discount = null;
            } else {
                // Deduct discount from the total amount
                $order->discount = $discountValue;
                $order->totalAmount = max(0, $originalTotal - $discountValue);
            }

            // Log the discount change
            $logFieldUpdate('discount', $oldDiscountValue, $order->discount);
            // Log the total amount change as well since it's affected
            $logFieldUpdate('totalAmount', $oldTotalValue, $order->totalAmount);
        } elseif ($field === 'bir') {
            $birValue = $request->value;
            $originalTotal = $order->totalAmount; 
            
            // If there's a current BIR value, add it back to get the true original total
            if ($order->bir !== null) {
                $originalTotal += $order->bir;
            }

            $oldBirValue = $order->bir;
            $oldTotalValue = $order->totalAmount;

            if ($birValue === null) {
                // Restore the total amount to its original value
                $order->totalAmount = $originalTotal;
                $order->bir = null;
            } else {
                // Deduct BIR from the total amount
                $order->bir = $birValue;
                $order->totalAmount = max(0, $originalTotal - $birValue);
            }
            
            // Log the BIR change
            $logFieldUpdate('bir', $oldBirValue, $order->bir);
            // Log the total amount change as well since it's affected
            $logFieldUpdate('totalAmount', $oldTotalValue, $order->totalAmount);
            
            // Save the changes
            $order->save();
            
            Log::info('Order After BIR Update:', [
                'bir' => $order->bir,
                'totalAmount' => $order->totalAmount,
                'originalTotal' => $originalTotal
            ]);
            
            // Return the new total to update the UI
            return response()->json([
                'success' => true, 
                'message' => 'BIR updated successfully!',
                'newTotal' => $order->totalAmount
            ]);
            
            $order->save();
            Log::info('Order After BIR Update:', $order->toArray());
            
            return response()->json([
                'success' => true,
                'message' => 'BIR updated successfully',
                'newTotal' => $order->totalAmount
            ]);
        } elseif ($field === 'containerNum') {
            $oldContainerValue = $order->containerNum;
            $newContainerValue = $request->value;
            
            // Log the container update
            \Log::info('Container Update', [
                'order_id' => $orderId,
                'old_value' => $oldContainerValue,
                'new_value' => $newContainerValue
            ]);
            
            // Store the current container number
            $order->containerNum = $newContainerValue;

            // Log the container change
            $logFieldUpdate('containerNum', $oldContainerValue, $newContainerValue);
            
            // If container value was removed (set to empty), restore the original values
            if (empty($newContainerValue) && !empty($oldContainerValue)) {
                // Restore original freight value (from originalFreight if available)
                if ($order->originalFreight !== null) {
                    $order->freight = $order->originalFreight;
                    
                    // Recalculate valuation based on original freight
                    $value = $order->value ?? 0;
                    $other = $order->other ?? 0;
                    $wharfage = $order->wharfage ?? 0;
                    $valuation = ($value + $order->freight) * 0.0075;
                    $order->valuation = $valuation;
                    $order->totalAmount = $order->freight + $valuation + $other + $wharfage;
                }
            }
            // If the container was changed (not just updated with the same value)
            elseif ($oldContainerValue !== $newContainerValue && !empty($newContainerValue)) {
                // Check for multiple containers
                $isMultipleContainer = strpos($newContainerValue, ',') !== false;
                $isSpecialContainer = strpos($newContainerValue, 'PADALA CONTAINER') !== false || 
                                      strpos($newContainerValue, 'TEMPORARY CONTAINER') !== false;
                                      
                // For normal containers, check if there's another order with this container in this voyage
                if (!$isMultipleContainer && !$isSpecialContainer) {
                    // First check if the container is in the reserved list
                    $containerInReservationList = ContainerReservation::where('ship', $order->shipNum)
                        ->where('voyage', $order->voyageNum)
                        ->where('containerName', $newContainerValue)
                        ->first();
                    
                    // Only proceed with zero freight if container is in reservation list
                    if ($containerInReservationList) {
                        // See if the new container already exists in other orders
                        $existingOrder = Order::where('containerNum', $newContainerValue)
                            ->where('shipNum', $order->shipNum)
                            ->where('voyageNum', $order->voyageNum)
                            ->where('id', '!=', $orderId)  // Exclude current order
                            ->first();
                        
                        // Set freight to 0 if this container already exists in another order
                        if ($existingOrder) {
                            $originalFreight = $order->freight;
                            $order->freight = 0;
                            
                            // Store the original freight if not already stored
                            if ($order->originalFreight === null) {
                                $order->originalFreight = $originalFreight;
                            }
                            
                            // Recalculate totals
                            $value = $order->value ?? 0;
                            $other = $order->other ?? 0;
                            $wharfage = 0; // Set wharfage to 0 for subsequent container use
                            $valuation = ($value + 0) * 0.0075; // Using 0 as freight
                            $order->valuation = $valuation;
                            $order->wharfage = $wharfage;
                            $order->totalAmount = $valuation + $other + $wharfage; // Updated total calculation
                        }
                    }
                    // Log container check results for debugging
                    \Log::info('Container in reservation check:', [
                        'orderId' => $orderId,
                        'containerNum' => $newContainerValue,
                        'isInReservation' => isset($containerInReservationList),
                        'hasExistingOrder' => isset($existingOrder),
                        'freightValue' => $order->freight
                    ]);
                }
                
                // For multiple containers or special containers, keep the original freight
                // unless we find a previous container in the list with the same first container number
                if ($isMultipleContainer) {
                    // Extract the first container number for comparison
                    $containerNumbers = array_map('trim', explode(',', $newContainerValue));
                    $firstContainer = $containerNumbers[0];
                    
                    // First check if the container is in the reserved list
                    $containerInReservationList = ContainerReservation::where('ship', $order->shipNum)
                        ->where('voyage', $order->voyageNum)
                        ->where('containerName', 'LIKE', $firstContainer . '%')
                        ->first();
                    
                    if ($containerInReservationList) {
                        $existingOrder = Order::where('containerNum', 'LIKE', $firstContainer . '%')
                            ->where('shipNum', $order->shipNum)
                            ->where('voyageNum', $order->voyageNum)
                            ->where('id', '!=', $orderId)  // Exclude current order
                            ->first();
                        
                        if ($existingOrder) {
                            $originalFreight = $order->freight;
                            $order->freight = 0;
                            
                            // Store the original freight if not already stored
                            if ($order->originalFreight === null) {
                                $order->originalFreight = $originalFreight;
                            }
                            
                            // Recalculate totals
                            $value = $order->value ?? 0;
                            $other = $order->other ?? 0;
                            $wharfage = 0; // Set wharfage to 0 for subsequent container use
                            $valuation = ($value + 0) * 0.0075;
                            $order->valuation = $valuation;
                            $order->wharfage = $wharfage;
                            $order->totalAmount = $valuation + $other + $wharfage; // Updated total calculation
                        }
                    }
                    // Log container check results for debugging
                    \Log::info('Multiple container in reservation check:', [
                        'orderId' => $orderId,
                        'firstContainer' => $firstContainer,
                        'isInReservation' => isset($containerInReservationList),
                        'hasExistingOrder' => isset($existingOrder),
                        'freightValue' => $order->freight
                    ]);
                }
                
                // Check if this ship and voyage combination should skip wharfage
                // This takes precedence over all other wharfage calculations
                if ($this->shouldSkipWharfageForShipVoyage($order->shipNum, $order->voyageNum)) {
                    \Log::info('Container update: Skipping wharfage due to ship/voyage exclusion rule', [
                        'ship' => $order->shipNum,
                        'voyage' => $order->voyageNum,
                        'orderId' => $orderId,
                        'containerNum' => $newContainerValue
                    ]);
                    $wharfage = 0;
                    $order->wharfage = $wharfage;
                    // Recalculate total
                    $value = $order->value ?? 0;
                    $other = $order->other ?? 0;
                    $valuation = ($value + $order->freight) * 0.0075;
                    $order->valuation = $valuation;
                    $order->totalAmount = $order->freight + $valuation + $other + $wharfage;
                }
            }
        } elseif ($field === 'originalFreight') {
            $oldOriginalFreight = $order->originalFreight;
            $oldFreight = $order->freight;
            $oldValuation = $order->valuation;
            $oldDiscount = $order->discount;
            $oldTotalAmount = $order->totalAmount;

            $order->originalFreight = $request->value;

            // Recalculate freight and totals based on the new originalFreight value
            $originalFreight = $request->value;
            $discount = $originalFreight * 0.05; // Calculate 5% discount
            $freight = $originalFreight - $discount; // Calculate freight after discount

            $order->freight = $freight;

            // Recalculate totals
            $value = $order->value ?? 0;
            $other = $order->other ?? 0;
            $wharfage = $order->wharfage ?? 0;
            $valuation = ($value + $freight) * 0.0075; // Valuation remains unaffected by the discount
            $total = $freight + $valuation + $other + $wharfage; // Updated total computation with wharfage

            $order->valuation = $valuation;
            $order->discount = $discount;
            $order->totalAmount = $total;

            // Log all the affected fields
            $logFieldUpdate('originalFreight', $oldOriginalFreight, $order->originalFreight);
            $logFieldUpdate('freight', $oldFreight, $order->freight);
            $logFieldUpdate('valuation', $oldValuation, $order->valuation);
            $logFieldUpdate('discount', $oldDiscount, $order->discount);
            $logFieldUpdate('totalAmount', $oldTotalAmount, $order->totalAmount);
        } elseif ($field === 'wharfage') {
            // --- FIX: Always update wharfage and recalculate totalAmount ---
            $oldWharfage = $order->wharfage;
            $oldValuation = $order->valuation;
            $oldTotalAmount = $order->totalAmount;

            $order->wharfage = $request->value;
            $freight = $order->freight ?? 0;
            $value = $order->value ?? 0;
            $other = $order->other ?? 0;
            $padlock_fee = $order->padlock_fee ?? 0;
            $valuation = ($freight + $value) * 0.0075;
            $total = $freight + $valuation + $other + $order->wharfage + $padlock_fee;
            $order->valuation = $valuation;
            $order->totalAmount = $total;

            // Log the changes only if they actually changed
            $logFieldUpdate('wharfage', $oldWharfage, $order->wharfage);
            if ($oldValuation != $order->valuation) {
                $logFieldUpdate('valuation', $oldValuation, $order->valuation);
            }
            $logFieldUpdate('totalAmount', $oldTotalAmount, $order->totalAmount);

            $order->save();
            Log::info('Order After Update:', $order->toArray());
            return response()->json([
                'success' => true,
                'newTotal' => $order->totalAmount,
                'message' => 'Wharfage updated and total recalculated.'
            ]);
        } elseif ($field === 'freight') {
            // --- FIX: When updating freight, recalculate wharfage and totalAmount ---
            $oldFreight = $order->freight;
            $oldWharfage = $order->wharfage;
            $oldValuation = $order->valuation;
            $oldTotalAmount = $order->totalAmount;

            $order->freight = $request->value;
            $freight = $order->freight ?? 0;
            $value = $order->value ?? 0;
            $other = $order->other ?? 0;
            $padlock_fee = $order->padlock_fee ?? 0;
            // Wharfage calculation rules
            $skipWharfage = false;
            // Check if this ship and voyage combination should skip wharfage
            if ($this->shouldSkipWharfageForShipVoyage($order->shipNum, $order->voyageNum)) {
                $skipWharfage = true;
            } else {
                // Check for reserved container logic
                if (!empty($order->containerNum)) {
                    $containerExists = \App\Models\ContainerReservation::where('ship', $order->shipNum)
                        ->where('voyage', $order->voyageNum)
                        ->where('containerName', $order->containerNum)
                        ->exists();
                    
                    if ($containerExists) {
                        $priorOrder = \App\Models\order::where('containerNum', $order->containerNum)
                            ->where('shipNum', $order->shipNum)
                            ->where('voyageNum', $order->voyageNum)
                            ->where('id', '!=', $orderId)
                            ->first();
                        
                        if ($priorOrder) {
                            $skipWharfage = true;
                        }
                    }
                    
                    // Log the check for debugging
                    \Log::info('Freight update container check:', [
                        'orderId' => $orderId,
                        'containerNum' => $order->containerNum,
                        'containerExists' => $containerExists,
                        'priorOrderExists' => isset($priorOrder),
                        'skipWharfage' => $skipWharfage
                    ]);
                }
            }
            if ($skipWharfage) {
                $wharfage = 0;
            } else {
                // Use formula: FREIGHT / 1200 * 23, min 11.20, if freight is zero then 11.20
                $wharfage = ($freight > 0) ? ($freight / 1200 * 23) : 11.20;
                if ($wharfage > 0 && $wharfage < 11.20) {
                    $wharfage = 11.20;
                }
            }
            $order->wharfage = $wharfage;
            $valuation = ($freight + $value) * 0.0075;
            $order->valuation = $valuation;
            $total = $freight + $valuation + $other + $wharfage + $padlock_fee;
            $order->totalAmount = $total;

            // Log the changes only if they actually changed
            $logFieldUpdate('freight', $oldFreight, $order->freight);
            if ($oldWharfage != $order->wharfage) {
                $logFieldUpdate('wharfage', $oldWharfage, $order->wharfage);
            }
            if ($oldValuation != $order->valuation) {
                $logFieldUpdate('valuation', $oldValuation, $order->valuation);
            }
            $logFieldUpdate('totalAmount', $oldTotalAmount, $order->totalAmount);

            $order->save();
            Log::info('Order After Update:', $order->toArray());
            return response()->json([
                'success' => true,
                'newTotal' => $order->totalAmount,
                'valuation' => $order->valuation,
                'wharfage' => $order->wharfage,
                'message' => 'Freight updated, wharfage recalculated, and total recalculated.'
            ]);
        } elseif (in_array($field, ['value', 'valuation', 'other', 'padlock_fee'])) {
            $oldValue = $order->$field;
            $oldCalculatedValuation = $order->valuation;
            $oldTotalAmount = $order->totalAmount;

            $order->$field = $request->value;
            $freight = $order->freight ?? 0;
            $value = $field === 'value' ? $request->value : ($order->value ?? 0);
            $other = $field === 'other' ? $request->value : ($order->other ?? 0);
            $wharfage = $order->wharfage ?? 0;
            $padlock_fee = $field === 'padlock_fee' ? $request->value : ($order->padlock_fee ?? 0);
            $valuation = ($freight + $value) * 0.0075;
            $order->valuation = $valuation;
            $total = $freight + $valuation + $other + $wharfage + $padlock_fee;
            $order->totalAmount = $total;

            // Log the primary field change
            $logFieldUpdate($field, $oldValue, $order->$field);
            // Log valuation change only if it was recalculated and actually changed
            if ($oldCalculatedValuation != $order->valuation) {
                $logFieldUpdate('valuation', $oldCalculatedValuation, $order->valuation);
            }
            // Log total amount change only if it actually changed
            if ($oldTotalAmount != $order->totalAmount) {
                $logFieldUpdate('totalAmount', $oldTotalAmount, $order->totalAmount);
            }

            $order->save();
            Log::info('Order After Update:', $order->toArray());
            return response()->json([
                'success' => true,
                'newTotal' => $order->totalAmount,
                'message' => ucfirst($field) . ' updated and total recalculated.'
            ]);
        } elseif ($field === 'image' && $request->hasFile('image')) {
            $oldImage = $order->image;
            $imagePath = $request->file('image')->store('order_images', 'public');
            $order->image = $imagePath;
            
            // Log the image change
            $logFieldUpdate('image', $oldImage, $imagePath);
        } elseif ($field === 'remark') {
            // Handle remark field update
            $oldRemark = $order->remark;
            $order->remark = $request->value;
            
            // Log the remark change
            $logFieldUpdate('remark', $oldRemark, $order->remark);
            
            $order->save();
            
            Log::info('Remark Updated:', [
                'orderId' => $orderId,
                'newRemark' => $request->value,
                'orderAfterSave' => $order->fresh()->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Remark updated successfully!'
            ]);
        } elseif ($field === 'checkName') {
            // Handle checker name update
            $oldCheckName = $order->checkName;
            $order->checkName = $request->value;
            
            // Log the checker name change
            $logFieldUpdate('checkName', $oldCheckName, $order->checkName);
            
            $order->save();
            
            Log::info('Checker Name Updated:', [
                'orderId' => $orderId,
                'newCheckerName' => $request->value
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Checker name updated successfully!'
            ]);
        } elseif ($field === 'cargoType') {
            // Handle cargo type update
            $oldCargoType = $order->cargoType;
            $order->cargoType = $request->value;
            
            // Log the cargo type change
            $logFieldUpdate('cargoType', $oldCargoType, $order->cargoType);
            
            $order->save();
            
            Log::info('Cargo Type Updated:', [
                'orderId' => $orderId,
                'newCargoType' => $request->value,
                'orderAfterSave' => $order->fresh()->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cargo type updated successfully!'
            ]);
        } else {
            $oldFieldValue = $order->$field;
            $oldBlStatus = $order->blStatus;
            $oldUpdatedBy = $order->updated_by;
            $oldUpdatedLocation = $order->updated_location;
            $oldOrArDate = $order->or_ar_date;

            $order->$field = $request->value;

            if (in_array($field, ['OR', 'AR'])) {
                $order->or_ar_date = $request->date ?? now();
                $order->updated_by = Auth::user()->fName . ' ' . Auth::user()->lName; // Set the updated_by field
                $order->updated_location = Auth::user()->location === 'MANILA' ? 'MANILA' : 'BATANES';

                if (empty($order->OR) && empty($order->AR)) {
                    $order->or_ar_date = null;
                    $order->blStatus = 'UNPAID';
                } else {
                    $order->blStatus = 'PAID';
                }

                // Log the OR/AR field change
                $logFieldUpdate($field, $oldFieldValue, $order->$field);
                
                // Log changes to related fields if they changed
                if ($oldBlStatus != $order->blStatus) {
                    $logFieldUpdate('blStatus', $oldBlStatus, $order->blStatus);
                }
                if ($oldUpdatedBy != $order->updated_by) {
                    $logFieldUpdate('updated_by', $oldUpdatedBy, $order->updated_by);
                }
                if ($oldUpdatedLocation != $order->updated_location) {
                    $logFieldUpdate('updated_location', $oldUpdatedLocation, $order->updated_location);
                }
                if ($oldOrArDate != $order->or_ar_date) {
                    $logFieldUpdate('or_ar_date', $oldOrArDate, $order->or_ar_date);
                }
            } else {
                // For other fields, just log the primary field change
                $logFieldUpdate($field, $oldFieldValue, $order->$field);
            }
        }

        // Clear updated_by and updated_location if blStatus is UNPAID
        $oldUpdatedByBeforeClear = $order->updated_by;
        $oldUpdatedLocationBeforeClear = $order->updated_location;
        
        if ($order->blStatus === 'UNPAID') {
            $order->updated_by = null;
            $order->updated_location = null;
            
            // Log these changes only if they actually changed
            if ($oldUpdatedByBeforeClear !== null && $oldUpdatedByBeforeClear !== '') {
                $logFieldUpdate('updated_by', $oldUpdatedByBeforeClear, null);
            }
            if ($oldUpdatedLocationBeforeClear !== null && $oldUpdatedLocationBeforeClear !== '') {
                $logFieldUpdate('updated_location', $oldUpdatedLocationBeforeClear, null);
            }
        }

        $order->save();

        Log::info('Order After Update:', $order->toArray());

        return response()->json(['success' => true, 'message' => 'Order field updated successfully!']);
    }

    public function updateBlStatus(Request $request, $orderId) {
        $request->validate([
            'blStatus' => 'nullable|string|in:,Unpaid,Paid',
        ]);

        $order = Order::findOrFail($orderId);
        $oldBlStatus = $order->blStatus;
        $order->blStatus = $request->blStatus;
        $order->save();

        // Log the BL status change only if it actually changed
        if ($oldBlStatus !== $order->blStatus) {
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                'field_name' => 'blStatus',
                'old_value' => $oldBlStatus,
                'new_value' => $order->blStatus,
                'action_type' => 'update'
            ]);
        }

        // Check if both OR and AR are empty
        return response()->json(['success' => true, 'message' => 'BL Status updated successfully!']);
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
            ->selectRaw("sub_account_number AS id,
                COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name,
                IFNULL(NULLIF(phone, ''), '') AS phone") // Ensure empty phone is returned as ''
            ->get();

        // Merge Main Accounts and SubAccounts
        $results = $customers->merge($subAccounts);

        return response()->json($results);
    }

    public function updateNoteField(Request $request, $orderId) {
        $request->validate([
            'note' => 'nullable|string|max:1000', // Validate the note input
        ]);

        $order = Order::findOrFail($orderId);
        $oldNote = $order->note;
        $order->note = $request->note; // Update the note field
        $order->save();

        // Log the note change only if it actually changed
        if ($oldNote !== $order->note) {
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                'field_name' => 'note',
                'old_value' => $oldNote,
                'new_value' => $order->note,
                'action_type' => 'update'
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Note updated successfully!', 'note' => $order->note]);
    }

    public function removeImage(Request $request, $orderId) {
        $order = Order::findOrFail($orderId);

        if ($order->image) {
            $oldImage = $order->image;
            
            // Delete the image file from storage
            Storage::disk('public')->delete($order->image);

            // Remove the image path from the database
            $order->image = null;
            $order->save();

            // Log the image removal
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                'field_name' => 'image',
                'old_value' => $oldImage,
                'new_value' => null,
                'action_type' => 'delete'
            ]);

            return response()->json(['success' => true, 'message' => 'Image removed successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'No image found to remove.']);
    }

    public function formatNumber($input) {
        // Remove commas from the input
        $number = str_replace(',', '', $input);

        // Convert to float and round to two decimal places
        return number_format((float)$number, 2, '.', '');
    }

    public function destroyOrder($orderId) {
        $order = Order::findOrFail($orderId);

        // Get associated parcels before deletion
        $parcels = Parcel::where('orderId', $order->id)->get();

        // Log the deletion before deleting the order
        $user = Auth::user();
        $deletedBy = $user ? $user->fName . ' ' . $user->lName : 'Unknown User';

        // Use the actual name fields from the order instead of looking up by ID
        $shipperName = 'Unknown';
        $consigneeName = 'Unknown';
        $totalAmount = 0;

        // Try to get shipper name
        if (!empty(trim($order->shipperName))) {
            $shipperName = trim($order->shipperName);
        } elseif (!empty(trim($order->shipperNum))) {
            // Fallback: try to lookup by ID if name is empty
            $shipper = Customer::find($order->shipperNum);
            if (!$shipper) {
                $shipper = SubAccount::find($order->shipperNum);
            }
            if ($shipper) {
                if ($shipper instanceof SubAccount) {
                    $shipperName = $shipper->getDisplayName();
                } else {
                    $shipperName = !empty($shipper->first_name) || !empty($shipper->last_name) 
                        ? trim($shipper->first_name . ' ' . $shipper->last_name)
                        : ($shipper->company_name ?? 'Unknown');
                }
            }
        }

        // Try to get consignee name
        if (!empty(trim($order->recName))) {
            $consigneeName = trim($order->recName);
        } elseif (!empty(trim($order->recNum))) {
            // Fallback: try to lookup by ID if name is empty
            $consignee = Customer::find($order->recNum);
            if (!$consignee) {
                $consignee = SubAccount::find($order->recNum);
            }
            if ($consignee) {
                if ($consignee instanceof SubAccount) {
                    $consigneeName = $consignee->getDisplayName();
                } else {
                    $consigneeName = !empty($consignee->first_name) || !empty($consignee->last_name) 
                        ? trim($consignee->first_name . ' ' . $consignee->last_name)
                        : ($consignee->company_name ?? 'Unknown');
                }
            }
        }

        // Try to get total amount
        if (!empty($order->totalAmount) && $order->totalAmount > 0) {
            $totalAmount = $order->totalAmount;
        } elseif (isset($order->total) && $order->total > 0) {
            $totalAmount = $order->total;
        }

        // Log what we found for debugging
        Log::info('Order deletion data:', [
            'order_id' => $order->id,
            'bl_number' => $order->orderId,
            'original_shipperName' => $order->shipperName,
            'original_recName' => $order->recName,
            'original_totalAmount' => $order->totalAmount,
            'resolved_shipperName' => $shipperName,
            'resolved_consigneeName' => $consigneeName,
            'resolved_totalAmount' => $totalAmount,
        ]);

        OrderDeleteLog::create([
            'order_id' => $order->id,
            'bl_number' => $order->orderId,
            'ship_name' => $order->shipNum,
            'voyage_number' => $order->voyageNum,
            'shipper_name' => $shipperName,
            'consignee_name' => $consigneeName,
            'total_amount' => $totalAmount,
            'deleted_by' => $deletedBy,
            'order_data' => $order->toArray(), // Store complete order data for restore
            'parcels_data' => $parcels->toArray(), // Store parcels data for restore
        ]);

        // Delete associated parcels
        Parcel::where('orderId', $order->id)->delete();

        // Delete the order
        $order->delete();

        return redirect()->back()->with('success', 'Order and associated parcels deleted successfully!');
    }

    public function updateOrderTotals(Request $request, $orderId) {
        $request->validate([
            'valuation' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        $order = Order::findOrFail($orderId);

        $oldValuation = $order->valuation;
        $oldDiscount = $order->discount;
        $oldTotalAmount = $order->totalAmount;

        $order->valuation = $request->valuation;
        $order->discount = $request->discount;
        $order->totalAmount = $request->total;

        $order->save();

        $userName = Auth::user()->fName . ' ' . Auth::user()->lName;

        // Log only the fields that actually changed
        if ($oldValuation != $order->valuation) {
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => $userName,
                'field_name' => 'valuation',
                'old_value' => $oldValuation,
                'new_value' => $order->valuation,
                'action_type' => 'update'
            ]);
        }

        if ($oldDiscount != $order->discount) {
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => $userName,
                'field_name' => 'discount',
                'old_value' => $oldDiscount,
                'new_value' => $order->discount,
                'action_type' => 'update'
            ]);
        }

        if ($oldTotalAmount != $order->totalAmount) {
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => $userName,
                'field_name' => 'totalAmount',
                'old_value' => $oldTotalAmount,
                'new_value' => $order->totalAmount,
                'action_type' => 'update'
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Order totals updated successfully!']);
    }

    public function updateVoyageStatus(Request $request, $id) {
        $voyage = voyage::findOrFail($id);
        $oldStatus = $voyage->lastStatus;
        $newStatus = $request->status;
        
        // Update the current voyage
        $voyage->lastStatus = $newStatus;
        $voyage->lastUpdated = now();
        $voyage->save();

        // Update all related voyages with the same v_num for the same ship
        voyage::where('ship', $voyage->ship)
              ->where('v_num', $voyage->v_num)
              ->where('id', '!=', $id)
              ->update([
                  'lastStatus' => $newStatus,
                  'lastUpdated' => now()
              ]);
              
        // If the status is changed to STOP, make sure no orders can be created for this voyage
        if ($newStatus == 'STOP') {
            // When stopping a voyage, check if there are newer voyages with READY status
            // If not, create a new voyage with READY status
            $ship = Ship::where('ship_number', $voyage->ship)->first();
            
            if ($ship) {
                // For ships I and II that have directional voyages
                if ($ship->ship_number == 'I' || $ship->ship_number == 'II') {
                    // Check if there are any newer voyages with READY status for this direction
                    $newerReadyVoyages = voyage::where('ship', $voyage->ship)
                        ->where('inOut', $voyage->inOut)
                        ->where('v_num', '>', $voyage->v_num)
                        ->where('lastStatus', 'READY')
                        ->count();
                        
                    if ($newerReadyVoyages == 0) {
                        // No newer READY voyages, so set the ship status to prompt for a new voyage
                        $ship->status = 'NEW VOYAGE';
                        $ship->save();
                    }
                } else {
                    // For other ships
                    $newerReadyVoyages = voyage::where('ship', $voyage->ship)
                        ->where('v_num', '>', $voyage->v_num)
                        ->where('lastStatus', 'READY')
                        ->count();
                        
                    if ($newerReadyVoyages == 0) {
                        // No newer READY voyages, so set the ship status to prompt for a new voyage
                        $ship->status = 'NEW VOYAGE';
                        $ship->save();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Voyage status updated successfully!');
    }

    public function parcel(Request $request)
    {
        // Get all available ships and voyages for filtering dropdowns
        $ships = Ship::orderBy('ship_number')->get();
        
        // Get voyages with directional information for ships I and II
        $voyages = voyage::select('v_num', 'ship', 'inOut')
            ->orderBy('ship')
            ->orderBy('v_num')
            ->orderBy('inOut')
            ->get()
            ->map(function($voyage) {
                // For ships I and II, combine voyage number with direction
                if ($voyage->ship == 'I' || $voyage->ship == 'II') {
                    $voyage->display_voyage = $voyage->v_num . '-' . $voyage->inOut;
                } else {
                    $voyage->display_voyage = $voyage->v_num;
                }
                return $voyage;
            });

        // Get sort field and direction
        $sortField = $request->get('sort', 'parcels.itemId'); // Default sort by itemId
        $sortDirection = $request->get('direction', 'asc'); // Default direction is ascending
        
        // Validate sort parameters
        $allowedSortFields = ['parcels.itemId', 'parcels.itemName', 'orders.orderId'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'parcels.itemId';
        }
        
        $allowedDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedDirections)) {
            $sortDirection = 'asc';
        }

        // Start query builder for parcels
        $query = parcel::select(
                'parcels.*', 
                'orders.shipNum', 
                'orders.voyageNum',
                'orders.orderId as blNumber',
                'orders.containerNum',
                'orders.shipperName',
                'orders.recName'
            )
            ->join('orders', 'parcels.orderId', '=', 'orders.id');

        // Apply ship filter if provided
        if ($request->filled('ship')) {
            $query->where('orders.shipNum', $request->ship);
        }

        // Apply voyage filter if provided
        if ($request->filled('voyage')) {
            $query->where('orders.voyageNum', $request->voyage);
        }

        // Apply container filter if provided
        if ($request->filled('container')) {
            $query->where('orders.containerNum', 'LIKE', '%' . $request->container . '%');
        }

        // Apply search filter if provided
        if ($request->filled('search')) {
            $searchTerms = array_filter(explode(',', $request->search)); // Split by commas and filter empty values
            
            if (!empty($searchTerms)) {
                $query->where(function($q) use ($searchTerms) {
                    foreach ($searchTerms as $index => $term) {
                        $term = trim($term); // Remove extra spaces
                        
                        if ($index === 0) {
                            // For the first term, start a new where group
                            $q->where(function($subQuery) use ($term) {
                                $subQuery->where('parcels.itemId', 'LIKE', "%{$term}%")
                                    ->orWhere('parcels.itemName', 'LIKE', "%{$term}%")
                                    ->orWhere('parcels.desc', 'LIKE', "%{$term}%")
                                    ->orWhere('orders.orderId', 'LIKE', "%{$term}%");
                            });
                        } else {
                            // For subsequent terms, use orWhere to create a union (OR) between groups
                            $q->orWhere(function($subQuery) use ($term) {
                                $subQuery->where('parcels.itemId', 'LIKE', "%{$term}%")
                                    ->orWhere('parcels.itemName', 'LIKE', "%{$term}%")
                                    ->orWhere('parcels.desc', 'LIKE', "%{$term}%")
                                    ->orWhere('orders.orderId', 'LIKE', "%{$term}%");
                            });
                        }
                    }
                });
            }
        }

        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Additional default sorting (apply after the main sort)
        if ($sortField !== 'parcels.itemId') {
            $query->orderBy('parcels.itemId', 'asc');
        }
        
        // Also sort by ship and voyage for better organization
        if (!in_array($sortField, ['orders.shipNum', 'orders.voyageNum'])) {
            $query->orderBy('orders.shipNum', 'asc')
                  ->orderBy('orders.voyageNum', 'asc');
        }

        // Handle pagination options
        $perPage = $request->get('per_page', 10);
        
        // Validate per_page parameter
        $allowedPerPage = [10, 20, 50, 100, 'all'];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        
        // Paginate results or get all if 'all' is selected
        if ($perPage === 'all') {
            $parcels = $query->get();
            // Create a custom paginator for 'all' option to maintain interface consistency
            // Use max(1, count) to avoid division by zero and handle empty results
            $count = $parcels->count();
            $parcels = new LengthAwarePaginator(
                $parcels,
                $count,
                max(1, $count), // Prevent division by zero
                1, // Current page
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        } else {
            $parcels = $query->paginate((int)$perPage)->withQueryString();
        }

        return view('masterlist.parcel', compact('parcels', 'ships', 'voyages', 'sortField', 'sortDirection', 'perPage'));
    }

    public function soa(Request $request)
    {
        // Fetch all customers for the dropdown in the add customer modal
        $customersList = Customer::select('id', 'first_name', 'last_name', 'company_name')->get();
        
        // Get all ships ordered by ship number
        $ships = Ship::orderBy('ship_number')->get();
        
        // Get all voyages for all ships
        $voyages = voyage::orderBy('v_num')->get();
        
        // Return data to the view
        return view('masterlist.soa', [
            'customers' => $customersList,
            'ships' => $ships,
            'voyages' => $voyages
        ]);
    }

    public function soa_list(Request $request)
    {
        $customer_id = $request->query('customer_id');
        
        // Get the main customer
        $customer = Customer::with('subAccounts')->findOrFail($customer_id);
        
        // Get sub-account IDs
        $subAccountIds = $customer->subAccounts->pluck('sub_account_number')->toArray();
        
        // Get all orders for main customer and sub-accounts with origin-based filtering
        $orders = Order::where(function($query) use ($customer_id, $subAccountIds) {
            $query->where(function($q) use ($customer_id, $subAccountIds) {
                // Orders where they are consignee and origin is Manila
                $q->where('origin', 'Manila')
                  ->where(function($sq) use ($customer_id, $subAccountIds) {
                      $sq->where('recId', $customer_id)
                         ->orWhereIn('recId', $subAccountIds);
                  });
            })->orWhere(function($q) use ($customer_id, $subAccountIds) {
                // Orders where they are shipper and origin is Batanes
                $q->where('origin', 'Batanes')
                  ->where(function($sq) use ($customer_id, $subAccountIds) {
                      $sq->where('shipperId', $customer_id)
                         ->orWhereIn('shipperId', $subAccountIds);
                  });
            });
        })
        ->orderBy('shipNum')
        ->orderBy('voyageNum')
        ->get()
        ->groupBy(['shipNum', 'voyageNum']);
        
        return view('masterlist.soa_list', [
            'customer' => $customer,
            'groupedOrders' => $orders
        ]);
    }

    /**
     * Activate the 1% interest calculation for a specific voyage and customer
     * 
     * @param Request $request
     * @param string $shipNum
     * @param string $voyageNum
     * @param int $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateInterest(Request $request, $shipNum, $voyageNum, $customerId)
    {
        try {
            \Log::info('Activating interest for ship, voyage, customer', [
                'ship' => $shipNum,
                'voyage' => $voyageNum,
                'customer' => $customerId
            ]);
            
            // Skip schema modification - rely on model properties instead
            // If the field doesn't exist in the database, we'll store it temporarily
            
            // Decode the voyage number
            $decodedVoyageNum = urldecode($voyageNum);
            
            // Get all orders for this ship/voyage and customer
            $orders = \App\Models\order::where('shipNum', $shipNum)
                ->whereRaw('voyageNum = ?', [$decodedVoyageNum])
                ->where(function($query) use ($customerId) {
                    $query->where('recId', $customerId)
                          ->orWhere('shipperId', $customerId);
                })
                ->get();
            
            \Log::info('Found orders for interest activation', [
                'count' => $orders->count()
            ]);
                
            // Set the interest start date for all orders
            $now = now();
            $updatedCount = 0;
            
            foreach ($orders as $order) {
                try {
                    $order->interest_start_date = $now;
                    $order->save();
                    $updatedCount++;
                } catch (\Exception $innerEx) {
                    \Log::warning("Couldn't update order {$order->orderId}: " . $innerEx->getMessage());
                    // Continue with other orders
                }
            }
            
            $responseMessage = "Interest calculation activated successfully for {$updatedCount} orders";
            \Log::info($responseMessage);
            
            return response()->json([
                'success' => true,
                'message' => $responseMessage,
                'start_date' => $now->format('Y-m-d H:i:s'),
                'orders_affected' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error activating interest calculation: ' . $e->getMessage();
            \Log::error('Interest Activation Error', [
                'Error' => $e->getMessage(),
                'Trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Deactivate the 1% interest calculation for a specific voyage and customer
     * 
     * @param Request $request
     * @param string $shipNum
     * @param string $voyageNum
     * @param int $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivateInterest(Request $request, $shipNum, $voyageNum, $customerId)
    {
        try {
            \Log::info('Deactivating interest calculation', [
                'ship' => $shipNum,
                'voyage' => $voyageNum,
                'customer_id' => $customerId
            ]);
            
            // Get sub-account IDs
            $customer = Customer::with('subAccounts')->findOrFail($customerId);
            $subAccountIds = $customer->subAccounts->pluck('sub_account_number')->toArray();
            
            // Find all orders for this customer/ship/voyage
            $orders = Order::where('shipNum', $shipNum)
                ->where('voyageNum', $voyageNum)
                ->where(function($query) use ($customerId, $subAccountIds) {
                    $query->where('recId', $customerId)
                          ->orWhere('shipperId', $customerId)
                          ->orWhereIn('recId', $subAccountIds)
                          ->orWhereIn('shipperId', $subAccountIds);
                })
                ->get();
            
            \Log::info('Found orders for interest deactivation', [
                'count' => $orders->count()
            ]);
                
            // Clear the interest start date for all orders
            $updatedCount = 0;
            
            foreach ($orders as $order) {
                try {
                    $order->interest_start_date = null;
                    $order->save();
                    $updatedCount++;
                } catch (\Exception $innerEx) {
                    \Log::warning("Couldn't update order {$order->orderId}: " . $innerEx->getMessage());
                    // Continue with other orders
                }
            }
            
            $responseMessage = "Interest calculation deactivated successfully for {$updatedCount} orders";
            \Log::info($responseMessage);
            
            return response()->json([
                'success' => true,
                'message' => $responseMessage,
                'orders_affected' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            $errorMessage = 'Error deactivating interest calculation: ' . $e->getMessage();
            \Log::error('Interest Deactivation Error', [
                'Error' => $e->getMessage(),
                'Trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    public function soa_temp(Request $request, $ship, $voyage, $customerId)
    {
        // Get the customer
        $customer = Customer::with('subAccounts')->findOrFail($customerId);
        
        // Get sub-account IDs
        $subAccountIds = $customer->subAccounts->pluck('sub_account_number')->toArray();
        
        // Decode the voyage number to handle 'IN' and 'OUT' properly
        $decodedVoyageNum = urldecode($voyage);
        
        // Log for debugging purposes
        \Log::info('SOA Temp Request', [
            'Original Voyage Num' => $voyage,
            'Decoded Voyage Num' => $decodedVoyageNum,
            'Ship' => $ship,
            'Customer ID' => $customerId
        ]);
        
        try {
            // Get all orders for this ship/voyage that belong to the customer or their sub-accounts
            // Use a raw where clause to ensure exact matching regardless of special characters
            $orders = Order::where('shipNum', $ship)
                ->whereRaw('voyageNum = ?', [$decodedVoyageNum])
                ->where(function($query) use ($customerId, $subAccountIds) {
                $query->where(function($q) use ($customerId, $subAccountIds) {
                    // Orders where they are consignee and origin is Manila
                    $q->where('origin', 'Manila')
                      ->where(function($sq) use ($customerId, $subAccountIds) {
                          $sq->where('recId', $customerId)
                             ->orWhereIn('recId', $subAccountIds);
                      });
                })->orWhere(function($q) use ($customerId, $subAccountIds) {
                    // Orders where they are shipper and origin is Batanes
                    $q->where('origin', 'Batanes')
                      ->where(function($sq) use ($customerId, $subAccountIds) {
                          $sq->where('shipperId', $customerId)
                             ->orWhereIn('shipperId', $subAccountIds);
                      });
                });
            })
            ->with('parcels') // Include parcels relationship
            ->get();
            
            \Log::info('SOA Temp Orders Found', [
                'Count' => $orders->count(), 
                'Orders' => $orders->pluck('id', 'orderId')->toArray()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('SOA Temp Error', [
                'Error' => $e->getMessage(),
                'Trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error loading Statement of Account: ' . $e->getMessage());
        }
        
        // Get the origin and destination from the first order
        $origin = $orders->first() ? $orders->first()->origin : '';
        $destination = $orders->first() ? $orders->first()->destination : '';
        
        return view('masterlist.soa_temp', [
            'orders' => $orders,
            'customer' => $customer,
            'ship' => $ship,
            'voyage' => $voyage,
            'origin' => $origin,
            'destination' => $destination
        ]);
    }

    public function soa_voy_temp(Request $request, $ship, $voyage)
    {
        // Decode the voyage number to handle 'IN' and 'OUT' properly
        $decodedVoyageNum = urldecode($voyage);
        
        // Log for debugging purposes
        \Log::info('SOA Voyage Temp Request', [
            'Original Voyage Num' => $voyage,
            'Decoded Voyage Num' => $decodedVoyageNum,
            'Ship' => $ship
        ]);
        
        try {
            // Get all orders for this ship/voyage
            $orders = Order::where('shipNum', $ship)
                ->whereRaw('voyageNum = ?', [$decodedVoyageNum])
                ->with(['shipper', 'receiver', 'parcels'])
                ->get();
            
            // Get the first order to determine origin and destination
            $firstOrder = $orders->first();
            $origin = $firstOrder ? $firstOrder->origin : 'Unknown';
            $destination = $firstOrder ? $firstOrder->destination : 'Unknown';
            
            // Calculate summary values
            $voyageFreight = $orders->sum('totalFreight');
            $voyageValuation = $orders->sum('valuationFee');
            $voyageTotal = $voyageFreight + $voyageValuation;
            
            // Group orders by customer for detailed display
            $groupedByCustomer = [];
            foreach ($orders as $order) {
                $customerId = null;
                $customer = null;
                
                if ($order->origin == 'Manila') {
                    $customerId = $order->recId;
                    $customer = $order->receiver;
                } else if ($order->origin == 'Batanes') {
                    $customerId = $order->shipperId;
                    $customer = $order->shipper;
                }
                
                if ($customer) {
                    $customerName = !empty($customer->company_name) ? 
                        $customer->company_name : 
                        $customer->first_name . ' ' . $customer->last_name;
                        
                    if (!isset($groupedByCustomer[$customerId])) {
                        $groupedByCustomer[$customerId] = [
                            'name' => $customerName,
                            'id' => $customerId,
                            'orders' => [],
                            'freight' => 0,
                            'valuation' => 0,
                            'padlock_fee' => 0,
                            'total' => 0
                        ];
                    }
                    
                    $groupedByCustomer[$customerId]['orders'][] = $order;
                    $groupedByCustomer[$customerId]['freight'] += $order->totalFreight;
                    $groupedByCustomer[$customerId]['valuation'] += $order->valuationFee;
                    $groupedByCustomer[$customerId]['padlock_fee'] = isset($groupedByCustomer[$customerId]['padlock_fee']) ? 
                        $groupedByCustomer[$customerId]['padlock_fee'] + ($order->padlock_fee ?? 0) : 
                        ($order->padlock_fee ?? 0);
                    $groupedByCustomer[$customerId]['total'] += ($order->totalFreight + $order->valuationFee + ($order->padlock_fee ?? 0));
                }
            }
            
            \Log::info('SOA Voyage Temp Orders Found', [
                'Count' => $orders->count(),
                'Origin' => $origin,
                'Destination' => $destination,
                'Total Freight' => $voyageFreight,
                'Total Valuation' => $voyageValuation
            ]);
            
            return view('masterlist.soa_voy_temp', [
                'orders' => $orders,
                'groupedByCustomer' => $groupedByCustomer,
                'ship' => $ship,
                'voyage' => $decodedVoyageNum,
                'origin' => $origin,
                'destination' => $destination,
                'voyageFreight' => $voyageFreight,
                'voyageValuation' => $voyageValuation,
                'voyageTotal' => $voyageTotal,
                'customer' => (object)[
                    'company_name' => 'All Customers',
                    'first_name' => '',
                    'last_name' => ''
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in SOA Voyage Temp', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error generating SOA: ' . $e->getMessage());
        }
    }

    public function resetInterest(Request $request)
    {
        // Just display the reset interest page - actual reset happens via JavaScript and artisan command
        return view('masterlist.reset_interest');
    }

    public function containerDetails(Request $request)
    {
        $ship = $request->query('ship');
        $voyage = $request->query('voyage');

        // Validate the input
        if (!$ship || !$voyage) {
            return redirect()->back()->with('error', 'Ship and voyage are required.');
        }

        // Get all containers for this ship and voyage
        $containers = ContainerReservation::where('ship', $ship)
            ->where('voyage', $voyage)
            ->get();
            
        // Log for debugging
        \Log::info('Container Details Request', [
            'ship' => $ship,
            'voyage' => $voyage,
            'containers_count' => $containers->count(),
            'container_names' => $containers->pluck('containerName')->toArray()
        ]);
        
        // Process registered container reservations 
        foreach ($containers as $container) {
            // Load orders with this container using our updated relationship method
            $container->load('orders.customer', 'orders.parcels');
        }
        
        // Now we need to handle orders with container numbers that don't match our registered containers
        // This might include:
        // 1. Special containers like "PADALA CONTAINER" or "TEMPORARY CONTAINER"
        // 2. Container numbers that were entered in orders but not in container reservations
        // 3. Cases where container numbers are entered differently in orders vs container reservations
        
        // First, get all registered container patterns for matching
        $knownContainerPatterns = [];
        $registeredContainerIds = [];
        foreach ($containers as $container) {
            $registeredContainerIds[] = $container->id;
            
            if (strpos($container->containerName, ',') !== false) {
                // For comma-separated container numbers, add each one as a pattern
                $parts = array_map('trim', explode(',', $container->containerName));
                foreach ($parts as $part) {
                    $knownContainerPatterns[] = $part;
                }
            } else {
                $knownContainerPatterns[] = $container->containerName;
            }
        }
        
        // Get all orders for this ship and voyage first
        $allOrders = \App\Models\order::where('shipNum', $ship)
            ->where('voyageNum', $voyage)
            ->whereNotNull('containerNum')
            ->where('containerNum', '!=', '')
            ->with(['customer', 'parcels'])
            ->get();
            
        // Filter out orders that are already associated with our registered containers
        $associatedOrderIds = [];
        foreach ($containers as $container) {
            foreach ($container->orders as $order) {
                $associatedOrderIds[] = $order->id;
            }
        }
        
        // Find orders that aren't yet associated with any container
        $unassociatedOrders = $allOrders->filter(function($order) use ($associatedOrderIds) {
            return !in_array($order->id, $associatedOrderIds);
        });
        
        \Log::info('Unassociated Orders Found', [
            'count' => $unassociatedOrders->count(),
            'order_ids' => $unassociatedOrders->pluck('id')->toArray()
        ]);
        
        // Process unassociated orders and create "virtual" containers for them
        $specialContainerGroups = [];
        
        foreach ($unassociatedOrders as $order) {
            // Define a grouping key for this order's container
            $groupKey = $order->containerNum;
            
            // Handle special cases with commas
            if (strpos($order->containerNum, ',') !== false) {
                // This is a comma-separated multi-container - use entire string as key
                // but also log the individual container numbers for debugging
                $containerParts = array_map('trim', explode(',', $order->containerNum));
                \Log::info('Multi-container entry found', [
                    'full_entry' => $order->containerNum,
                    'parts' => $containerParts,
                    'order_id' => $order->id
                ]);
            } 
            // Handle special container designations
            elseif (strpos(strtoupper($order->containerNum), 'PADALA CONTAINER') !== false || 
                    strpos(strtoupper($order->containerNum), 'TEMPORARY CONTAINER') !== false) {
                \Log::info('Special container designation found', [
                    'container' => $order->containerNum,
                    'order_id' => $order->id
                ]);
            }
            
            // Add to the appropriate group
            if (!isset($specialContainerGroups[$groupKey])) {
                $specialContainerGroups[$groupKey] = [];
            }
            $specialContainerGroups[$groupKey][] = $order;
        }
        
        // Log special containers found
        \Log::info('Special Container Groups', [
            'count' => count($specialContainerGroups),
            'container_names' => array_keys($specialContainerGroups)
        ]);
        
        // Create container objects for special containers
        foreach ($specialContainerGroups as $containerName => $orders) {
            $specialContainer = new ContainerReservation();
            $specialContainer->containerName = $containerName;
            $specialContainer->type = 'Special'; // Mark as special container
            $specialContainer->ship = $ship;
            $specialContainer->voyage = $voyage;
            $specialContainer->orders = collect($orders); // Convert to collection
            
            $containers->push($specialContainer);
        }

        // Get all cargo types for filtering
        $cargoTypes = [];
        foreach ($containers as $container) {
            foreach ($container->orders as $order) {
                if (!empty($order->cargoType) && !in_array($order->cargoType, $cargoTypes)) {
                    $cargoTypes[] = $order->cargoType;
                }
            }
        }

        return view('masterlist.container-details', compact('containers', 'ship', 'voyage', 'cargoTypes'));
    }

    // Helper function to check if wharfage should be skipped based on ship and voyage
    private function shouldSkipWharfageForShipVoyage($ship, $voyage) {
        // Normalize inputs to ensure consistent comparison
        $ship = trim(strtoupper($ship));
        $voyage = trim(strtoupper($voyage));
        
        // Log input values for debugging
        \Log::info('Checking if wharfage should be skipped:', [
            'ship' => $ship,
            'voyage' => $voyage
        ]);
        
        // Ship II Voyage 7,8,9,10 both IN and OUT
        if ($ship === 'II') {
            $excludedVoyages = ['7', '8', '9', '10'];
            foreach ($excludedVoyages as $excludedVoyage) {
                // Check if voyage starts with the excluded number (to catch both IN and OUT)
                if (preg_match('/^' . preg_quote($excludedVoyage, '/') . '($|\s|[^0-9])/', $voyage)) {
                    \Log::info('Skipping wharfage: Ship II with excluded voyage', [
                        'voyage' => $voyage,
                        'matched_pattern' => $excludedVoyage
                    ]);
                    return true;
                }
            }
        }
        
        // Ship III VOYAGE 30 TO 42
        if ($ship === 'III') {
            // Extract all numeric portions from the voyage string
            preg_match('/(\d+)/', $voyage, $matches);
            
            if (isset($matches[1])) {
                $voyageNumber = (int)$matches[1];
                \Log::info('Ship III voyage check', [
                    'original_voyage' => $voyage,
                    'extracted_number' => $voyageNumber,
                    'is_in_range' => ($voyageNumber >= 30 && $voyageNumber <= 42)
                ]);
                
                if ($voyageNumber >= 30 && $voyageNumber <= 42) {
                    \Log::info('Skipping wharfage: Ship III with voyage in range 30-42', [
                        'voyageNumber' => $voyageNumber
                    ]);
                    return true;
                }
            } else {
                \Log::warning('No voyage number found in string', ['voyage' => $voyage]);
            }
        }
        
        \Log::info('Not skipping wharfage for this ship/voyage combination');
        return false;
    }

    public function restoreOrder($deleteLogId) {
        $deleteLog = OrderDeleteLog::findOrFail($deleteLogId);
        
        // Check if already restored
        if ($deleteLog->restored_at) {
            return redirect()->back()->with('error', 'This order has already been restored!');
        }
        
        // Check if we have order data to restore
        if (!$deleteLog->order_data) {
            return redirect()->back()->with('error', 'Cannot restore order: No order data found in delete log!');
        }
        
        $user = Auth::user();
        $restoredBy = $user ? $user->fName . ' ' . $user->lName : 'Unknown User';
        
        try {
            // Restore the order
            $orderData = $deleteLog->order_data;
            
            // Ensure orderData is an array
            if (!is_array($orderData)) {
                return redirect()->back()->with('error', 'Cannot restore order: Invalid order data format!');
            }
            
            // Remove fields that shouldn't be copied
            unset($orderData['id']); // Remove the original ID to create a new one
            unset($orderData['created_at']); // Let Laravel set new timestamps
            unset($orderData['updated_at']);
            
            $restoredOrder = Order::create($orderData);
            
            // Restore the parcels
            if ($deleteLog->parcels_data && is_array($deleteLog->parcels_data)) {
                foreach ($deleteLog->parcels_data as $parcelData) {
                    if (is_array($parcelData)) {
                        // Remove fields that shouldn't be copied
                        unset($parcelData['id']); // Remove the original ID
                        unset($parcelData['created_at']);
                        unset($parcelData['updated_at']);
                        $parcelData['orderId'] = $restoredOrder->id; // Link to new order
                        
                        Parcel::create($parcelData);
                    }
                }
            }
            
            // Update the delete log to mark as restored
            $deleteLog->update([
                'restored_at' => now(),
                'restored_by' => $restoredBy,
                'restored_order_id' => $restoredOrder->id,
            ]);
            
            return redirect()->back()->with('success', 'Order restored successfully! New BL ID: ' . $restoredOrder->id);
            
        } catch (\Exception $e) {
            Log::error('Error restoring order:', [
                'delete_log_id' => $deleteLogId,
                'error' => $e->getMessage(),
                'order_data' => $deleteLog->order_data,
                'parcels_data' => $deleteLog->parcels_data,
            ]);
            
            return redirect()->back()->with('error', 'Error restoring order: ' . $e->getMessage());
        }
    }
}