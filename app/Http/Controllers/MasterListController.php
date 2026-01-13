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
use App\Models\User; // Import the User model
use App\Models\order as Order; // Import the order model (lowercase) with alias
use App\Models\PriceList;
use App\Models\voyage;
use App\Models\parcel as Parcel; // Import the parcel model with alias
use App\Models\OrderUpdateLog; // Add this at the top
use App\Models\OrderDeleteLog; // Add this for delete logging
use App\Models\ContainerReservation; // Import the ContainerReservation model
use App\Models\SoaNumber; // Import the SoaNumber model
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

    public function updateParcels(Request $request)
    {
        try {
            DB::beginTransaction();

            $documents = $request->input('documents', []);
            $keys = $request->input('key', []);

            foreach ($documents as $id => $document) {
                $parcel = Parcel::find($id);
                if ($parcel) {
                    $parcel->documents = $document;
                    $parcel->key = $keys[$id] ?? null;
                    $parcel->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Parcels updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating parcels: ' . $e->getMessage());
        }

    }

    /**
     * Transfer (copy) an order (BL) to another ship and voyage.
     * This creates a new order record and copies parcels. It does NOT modify the original order
     * and does NOT create or modify SOA entries to avoid duplication in SOA.
     */
    public function transferOrder(Request $request, $orderId)
    {
        $request->validate([
            'target_ship' => 'required|string',
            'target_voyage' => 'required|string',
        ]);

        $original = Order::with('parcels')->findOrFail($orderId);

        DB::beginTransaction();
        try {
            // Create a copy of the order with updated ship/voyage and a reference to the original
            $data = $original->toArray();

            // Remove fields we don't want copied or that are auto-managed
            unset($data['id']);
            unset($data['created_at']);
            unset($data['updated_at']);

            // Update ship/voyage and clear fields related to SOA/payment identifiers so they won't affect original SOA
            $data['shipNum'] = $request->input('target_ship');
            $data['voyageNum'] = $request->input('target_voyage');

            // Get the dock_number from the target voyage
            $numericVoyage = preg_replace('/[^0-9]/', '', $request->input('target_voyage'));
            $targetVoyage = voyage::where('ship', $request->input('target_ship'))
                ->where('v_num', $numericVoyage ?: $request->input('target_voyage'))
                ->where('lastStatus', 'READY')
                ->orderBy('dock_number', 'desc')
                ->first();
            
            if ($targetVoyage) {
                $data['dock_number'] = $targetVoyage->dock_number ?? 0;
            }

            // Keep a trace to the original order (optional) by adding a transferred_from field if exists,
            // otherwise use remark to note transfer.
            if (in_array('transferred_from', $original->getFillable())) {
                $data['transferred_from'] = $original->id;
            } else {
                $data['remark'] = trim(($original->remark ?? '') . "\nTRANSFERRED FROM M/V EVERWIN STAR {$original->shipNum} VOYAGE NO. {$original->voyageNum}");
            }

            // Clear SOA-related fields so the new copy won't inherit payment associations
            $data['OR'] = null;
            $data['AR'] = null;
            $data['or_ar_date'] = null;
            $data['updated_by'] = null;
            $data['updated_location'] = null;
            // Don't set updated_by and updated_location during transfer - these should only be set when OR/AR are provided
            // $data['updated_by'] = Auth::user()->fName . ' ' . Auth::user()->lName;
            // $data['updated_location'] = $request->input('target_ship') . ' ' . $request->input('target_voyage');

            // Create the new BL (the copy retains the original amounts here)
            $copy = Order::create($data);

            // Copy parcels to the new BL
            foreach ($original->parcels as $parcel) {
                $p = $parcel->replicate();
                $p->orderId = $copy->id;
                $p->save();
            }

            // Update original order to note where it was transferred to
            $transferToInfo = "TRANSFERRED TO M/V EVERWIN STAR {$copy->shipNum} VOYAGE NO. {$copy->voyageNum}";

            // Preserve original ship/voyage explicitly to ensure they are not changed
            $originalShip = $original->shipNum;
            $originalVoyage = $original->voyageNum;

            // Reset all financial fields to zero on the original record
            $original->freight = 0;
            $original->valuation = 0;
            $original->value = 0;
            $original->wharfage = 0;
            $original->discount = 0;
            $original->bir = 0;
            $original->other = 0; // Ensure this matches your column name 'other'
            $original->totalAmount = 0;
            $original->originalFreight = 0; // Optional: zero this if you want to prevent recalculation logic

            if (in_array('transferred_to', $original->getFillable())) {
                $original->transferred_to = $copy->id;
            } else {
                $original->remark = trim(($original->remark ?? '') . "\n" . $transferToInfo);
            }

            // Reassign identifiers to prevent accidental movement and SAVE
            $original->shipNum = $originalShip;
            $original->voyageNum = $originalVoyage;
            $original->save();


            if (in_array('transferred_to', $original->getFillable())) {
                $original->transferred_to = $copy->id;
            } else {
                $original->remark = trim(($original->remark ?? '') . "\n" . $transferToInfo);
            }

            // Reassign preserved values to avoid accidental changes
            $original->shipNum = $originalShip;
            $original->voyageNum = $originalVoyage;
            $original->save();

            // Log the transfer on the original order as an update
            OrderUpdateLog::create([
                'order_id' => $original->id,
                'updated_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                'field_name' => 'transfer',
                'old_value' => null,
                'new_value' => $transferToInfo,
                'action_type' => 'update',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
            ]);

            // Log the transfer as an OrderUpdateLog entry
            OrderUpdateLog::create([
                'order_id' => $copy->id,
                'updated_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                'field_name' => 'transfer',
                'old_value' => null,
                'new_value' => "Transferred from order {$original->id} ({$original->shipNum}/{$original->voyageNum}) to {$copy->shipNum}/{$copy->voyageNum}",
                'action_type' => 'create',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Order transferred successfully', 'new_order_id' => $copy->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer Order Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Transfer failed: ' . $e->getMessage()], 500);
        }
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
        // Get all orders with parcels relationship and all necessary fields
        // Sort transferred orders (those with "TRANSFERRED FROM" in remark) to the top
        $orders = Order::with([
            'parcels' => function($query) {
                $query->select('id', 'orderId', 'itemName', 'quantity', 'unit', 'desc');
            },
            'gatePasses' => function($query) {
                $query->select('id', 'order_id', 'gate_pass_no', 'release_date')
                    ->orderBy('release_date', 'asc');
            },
            'gatePasses.items' => function($query) {
                $query->select('id', 'gate_pass_id', 'item_description', 'unit', 'released_quantity');
            }
        ])
        ->select([
            'id', 'orderId', 'shipNum', 'voyageNum', 'containerNum', 'cargoType',
            'shipperName', 'recName', 'checkName', 'remark', 'note', 'origin', 'destination',
            'blStatus', 'totalAmount', 'freight', 'valuation', 'wharfage', 'value', 'other',
            'bir', 'discount', 'originalFreight', 'padlock_fee', 'or_ar_date',
            'OR', 'AR', 'updated_by', 'updated_location', 'image', 'created_at', 'creator', 'bl_computed' // <-- Add this lineS
        ])
        ->orderByRaw("CASE WHEN remark LIKE '%TRANSFERRED FROM%' THEN 0 ELSE 1 END ASC")
        ->orderBy('orderId', 'asc')
        ->get();

        // Enhance each order with proper AR/OR display information
        $orders = $orders->map(function ($order) {
            $displayInfo = $this->getArOrDisplayInfo($order);
            $order->display_updated_by = $displayInfo['updated_by'];
            $order->display_updated_location = $displayInfo['updated_location'];
            $order->display_or_ar_date = $displayInfo['or_ar_date'];
            $order->last_updated_field = $displayInfo['last_updated_field'];
            
            // Add separate AR and OR display information
            $order->ar_display_info = $displayInfo['ar_display_info'];
            $order->or_display_info = $displayInfo['or_display_info'];
            
            return $order;
        });
        
        // Create filter data
        $filterData = [
            'uniqueOrderIds' => $orders->pluck('orderId')->unique()->sort()->values(),
            'uniqueContainers' => $orders->pluck('containerNum')->filter()->unique()->sort()->values(),
            'uniqueCargoTypes' => $orders->pluck('cargoType')->filter()->unique()->sort()->values(),
            'uniqueShippers' => $orders->pluck('shipperName')->filter()->unique()->sort()->values(),
            'uniqueConsignees' => $orders->pluck('recName')->filter()->unique()->sort()->values(),
            'uniqueCheckers' => $orders->pluck('checkName')->filter()->unique()->sort()->values(),
            'uniqueORs' => $orders->pluck('OR')->filter()->unique()->sort()->values(),
            'uniqueARs' => $orders->pluck('AR')->filter()->unique()->sort()->values(),
            'uniqueUpdatedBy' => $orders->pluck('display_updated_by')->filter()->unique()->sort()->values(),
        ];
        
        // Get parcels with pricelist for categories
        $parcels = Parcel::with(['pricelist' => function($query) {
            $query->select('id', 'item_code', 'category');
        }])->whereIn('orderId', $orders->pluck('id'))->get();
        
        // Get unique categories from all pricelists
        $uniqueCategories = PriceList::whereNotNull('category')->pluck('category')->unique()->sort()->values();
        
        // Get items by category from parcels
        $itemsByCategory = [];
        foreach ($uniqueCategories as $cat) {
            $items = $parcels->where('pricelist.category', $cat)->pluck('itemName')->unique()->sort()->values();
            $itemsByCategory[$cat] = $items;
        }
        
        // Get unique item names
        $uniqueItemNames = $parcels->pluck('itemName')->unique()->sort()->values();
        
        // Get order categories
        $orderCategories = $orders->mapWithKeys(function($order) use ($parcels) {
            $cats = $parcels->where('orderId', $order->id)->pluck('pricelist.category')->filter()->unique()->implode(',');
            return [$order->id => $cats];
        });
        
        // Add to filterData
        $filterData['uniqueCategories'] = $uniqueCategories;
        $filterData['itemsByCategory'] = $itemsByCategory;
        $filterData['uniqueItemNames'] = $uniqueItemNames;
        $filterData['orderCategories'] = $orderCategories;
        
        return view('masterlist.list', compact('orders', 'filterData'));
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

            // Get orders for sub-account - show all BLs where sub-account is either shipper OR consignee
            $subAccountOrders = Order::where(function ($query) use ($subAccount) {
                $query->where('recId', $subAccount->sub_account_number)
                      ->orWhere('shipperId', $subAccount->sub_account_number);
            })->get();
            
            // Log how many orders were found
            \Log::debug("Found {$subAccountOrders->count()} orders for sub-account #{$subAccount->id}");
            
            // If it's a company and no orders found, check if there might be an issue with sub_account_number
            if (!empty($subAccount->company_name) && $subAccountOrders->count() === 0) {
                // Check both recName and shipperName as string matches for company name
                $potentialOrders = Order::where(function ($query) use ($subAccount) {
                    $query->where('recName', 'LIKE', "%{$subAccount->company_name}%")
                          ->orWhere('shipperName', 'LIKE', "%{$subAccount->company_name}%");
                })->get();
                
                if ($potentialOrders->count() > 0) {
                    \Log::debug("Found {$potentialOrders->count()} potential orders for company {$subAccount->company_name} by name matching");
                    // Merge with any previously found orders
                    $subAccountOrders = $subAccountOrders->merge($potentialOrders);
                    
                    // For each order found by name, update its recId or shipperId to use the correct sub_account_number
                    foreach ($potentialOrders as $order) {
                        // Determine what to update based on which field matched
                        if (stripos($order->recName, $subAccount->company_name) !== false) {
                            \Log::debug("Updating order #{$order->id} to set recId={$subAccount->sub_account_number}");
                            $order->recId = $subAccount->sub_account_number;
                            $order->save();
                        }
                        if (stripos($order->shipperName, $subAccount->company_name) !== false) {
                            \Log::debug("Updating order #{$order->id} to set shipperId={$subAccount->sub_account_number}");
                            $order->shipperId = $subAccount->sub_account_number;
                            $order->save();
                        }
                    }
                }
            }
            
            // Attach orders to the sub-account
            $subAccount->setRelation('orders', $subAccountOrders);
        }

        // Fetch orders for the main account - show all BLs where customer is either shipper OR consignee
        $orders = Order::where(function ($query) use ($customer_id) {
            $query->where('recId', $customer_id)
                  ->orWhere('shipperId', $customer_id);
        })->paginate(10);

        // Pass data to the view
        return view('masterlist.bl_list', compact('mainAccount', 'subAccounts', 'orders'));
    }

    public function viewBl($shipNum, $voyageNum, $orderId) {
        // Fetch the order by ID with gate passes
        $order = Order::with(['gatePasses' => function($query) {
            $query->orderBy('release_date', 'asc');
        }])->findOrFail($orderId);

        // Enhance order with proper AR/OR display information
        $displayInfo = $this->getArOrDisplayInfo($order);
        $order->display_updated_by = $displayInfo['updated_by'];
        $order->display_updated_location = $displayInfo['updated_location'];
        $order->display_or_ar_date = $displayInfo['or_ar_date'];
        $order->last_updated_field = $displayInfo['last_updated_field'];
        
        // Add separate AR and OR display information
        $order->ar_display_info = $displayInfo['ar_display_info'];
        $order->or_display_info = $displayInfo['or_display_info'];

        // Fetch related parcels using the orderId
        $parcels = Parcel::where('orderId', $order->id)->get();
        // Determine display ship/voyage: if this order was transferred from another, show original
        $displayShip = $order->shipNum;
        $displayVoyage = $order->voyageNum;

        // If there's a transferred_from field, prefer it
        if (isset($order->transferred_from) && $order->transferred_from) {
            $orig = Order::find($order->transferred_from);
            if ($orig) {
                $displayShip = $orig->shipNum;
                $displayVoyage = $orig->voyageNum;
            }
        } else {
            // Try to parse remark for transfer note: "TRANSFERRED FROM ORDER ID: <id>"
            if (!empty($order->remark)) {
                if (preg_match('/TRANSFERRED FROM ORDER ID:\s*(\d+)/i', $order->remark, $m)) {
                    $origId = intval($m[1]);
                    $orig = Order::find($origId);
                    if ($orig) {
                        $displayShip = $orig->shipNum;
                        $displayVoyage = $orig->voyageNum;
                    }
                }
            }
        }

        // Pass the order, parcels and display values to the view
        return view('masterlist.view-bl', compact('order', 'parcels', 'displayShip', 'displayVoyage'));
    }

    public function viewNoPriceBl($shipNum, $voyageNum, $orderId) {
        // Fetch the order by ID
        $order = Order::findOrFail($orderId);

        // Fetch related parcels using the orderId
        $parcels = Parcel::where('orderId', $order->id)->get();
        // Determine display ship/voyage: if this order was transferred from another, show original
        $displayShip = $order->shipNum;
        $displayVoyage = $order->voyageNum;

        // If there's a transferred_from field, prefer it
        if (isset($order->transferred_from) && $order->transferred_from) {
            $orig = Order::find($order->transferred_from);
            if ($orig) {
                $displayShip = $orig->shipNum;
                $displayVoyage = $orig->voyageNum;
            }
        } else {
            // Try to parse remark for transfer note: "TRANSFERRED FROM ORDER ID: <id>"
            if (!empty($order->remark)) {
                if (preg_match('/TRANSFERRED FROM ORDER ID:\s*(\d+)/i', $order->remark, $m)) {
                    $origId = intval($m[1]);
                    $orig = Order::find($origId);
                    if ($orig) {
                        $displayShip = $orig->shipNum;
                        $displayVoyage = $orig->voyageNum;
                    }
                }
            }
        }

        // Pass the order, parcels and display values to the view
        return view('masterlist.view-no-price-bl', compact('order', 'parcels', 'displayShip', 'displayVoyage'));
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
        else if ($data == 'DUAL VOYAGE') {
            // Handle DUAL VOYAGE status - create a second voyage with the same number but different group
            if ($id == '1' || $id == '2') {
                // For ships I and II - handle both IN and OUT directions
                $directions = ['IN', 'OUT'];
                
                foreach ($directions as $direction) {
                    // Get the latest voyage for this direction
                    $latestVoyage = voyage::where('ship', $ship->ship_number)
                        ->where('inOut', $direction)
                        ->where('lastStatus', 'READY')
                        ->orderBy('v_num', 'desc')
                        ->first();
                    
                    if ($latestVoyage) {
                        // Check if there's already a secondary voyage for this number and direction
                        $existingSecondary = voyage::where('ship', $ship->ship_number)
                            ->where('inOut', $direction)
                            ->where('v_num', $latestVoyage->v_num)
                            ->where('is_primary', false)
                            ->first();
                        
                        if (!$existingSecondary) {
                            // Mark the existing voyage as primary
                            $latestVoyage->update([
                                'is_primary' => true,
                                'voyage_group' => 'primary_' . $latestVoyage->v_num . '_' . $direction
                            ]);
                            
                            // Create a secondary voyage with the same number
                            voyage::create([
                                'ship' => $ship->ship_number,
                                'v_num' => $latestVoyage->v_num,
                                'lastStatus' => 'READY',
                                'lastUpdated' => now(),
                                'inOut' => $direction,
                                'is_primary' => false,
                                'voyage_group' => 'secondary_' . $latestVoyage->v_num . '_' . $direction
                            ]);
                        }
                    }
                }
            } else {
                // For other ships (III, IV, V)
                $latestVoyage = voyage::where('ship', $ship->ship_number)
                    ->where('lastStatus', 'READY')
                    ->orderBy('v_num', 'desc')
                    ->first();
                
                if ($latestVoyage) {
                    // Check if there's already a secondary voyage for this number
                    $existingSecondary = voyage::where('ship', $ship->ship_number)
                        ->where('v_num', $latestVoyage->v_num)
                        ->where('is_primary', false)
                        ->first();
                    
                    if (!$existingSecondary) {
                        // Mark the existing voyage as primary
                        $latestVoyage->update([
                            'is_primary' => true,
                            'voyage_group' => 'primary_' . $latestVoyage->v_num
                        ]);
                        
                        // Create a secondary voyage with the same number
                        voyage::create([
                            'ship' => $ship->ship_number,
                            'v_num' => $latestVoyage->v_num,
                            'lastStatus' => 'READY',
                            'lastUpdated' => now(),
                            'inOut' => '',
                            'is_primary' => false,
                            'voyage_group' => 'secondary_' . $latestVoyage->v_num
                        ]);
                    }
                }
            }
            
            // Automatically change status to CREATE BL after creating dual voyages
            $data = 'CREATE BL';
        }

        $ship->status = $data;
        $ship->save();

        return redirect()->back()->with('success', 'Ship status updated successfully!');
    }

    public function editBL($orderId) {
        $user = auth()->user();
        
        // Check permissions explicitly, but allow direct access when using the debug route
        if (request()->route()->getName() !== 'masterlist.edit-bl-direct' && 
            !($user->hasSubpagePermission('masterlist', 'edit-bl', 'edit') || 
              $user->hasSubpagePermission('masterlist', 'list', 'edit'))) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit this BL.');
        }
        
        $order = Order::where('id', $orderId)->firstOrFail();

        $items = PriceList::all()->keyBy('item_code'); // Fetch all items from the PriceList model
        $parcels = parcel::where('orderId', $order->id)->get()->map(function ($item) use ($items) {
            // Use itemId to find matching item in PriceList since that's what's stored in the parcels table
            $priceListItem = $items[$item->itemId] ?? null;

            // Handle both old format (single measurements) and new format (measurements array)
            if (!empty($item->measurements) && is_array($item->measurements)) {
                // New format: measurements array - ensure rates and freights are calculated
                $measurements = array_map(function($measurement) {
                    if (!isset($measurement['rate']) || !isset($measurement['freight'])) {
                        $rate = ($measurement['length'] ?? 0) * ($measurement['width'] ?? 0) * ($measurement['height'] ?? 0) * ($measurement['multiplier'] ?? 0);
                        $measurement['rate'] = $rate;
                        $measurement['freight'] = $rate * ($measurement['quantity'] ?? 0);
                    }
                    return $measurement;
                }, $item->measurements);

                return [
                    'itemCode' => $item->itemId ?? "",
                    'itemName' => $item->itemName ?? "",
                    'unit' => $item->unit ?? "",
                    'category' => $priceListItem->category ?? "", // Use category from PriceList
                    'weight' => $item->weight ?? " ",
                    'value' => $item->value ?? " ",
                    'measurements' => $measurements, // Use measurements array with calculated rates/freights
                    'price' => $item->itemPrice ?? 0,
                    'description' => $item->desc ?? "",
                    'quantity' => $item->quantity ?? 1,
                    'total' => $item->total,
                ];
            } else {
                // Old format: single measurements
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
            }
        });
        $total = $parcels->sum('total'); // Calculate the total of all parcels
        $lists = PriceList::all(); // Fetch all items from the PriceList model

        // Fetch unique categories for the dropdown
        $uniqueCategories = PriceList::whereNotNull('category')->pluck('category')->unique()->sort()->values();

        // Fetch all customers for the shipper and consignee dropdowns
        $customers = Customer::all();

        return view('masterlist.edit-bl', compact('order', 'parcels', 'lists', 'total', 'customers', 'uniqueCategories'));

    }

    public function updateBL(Request $request, $orderId) {
        $noValue = false;
        $checks = false; // Initialize $checks variable to prevent undefined variable error
        $data = $request->all();
        $origin = $data['origin'];
        $cart = json_decode($request->cartData);
        
        // Log the incoming request data for debugging
        Log::info('Update BL Request Data:', $data);
        Log::info('Cart Data Count:', ['count' => count($cart)]);
        Log::info('Order ID parameter:', ['orderId' => $orderId]);

        // Validate cartTotal and value explicitly
        $request->validate([
            'cartTotal' => 'nullable|numeric',
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
                $freight = $this->formatNumber($data['cartTotal'] ?? 0);
            }
            
            Log::info('Container reservation check on BL update:', [
                'container' => $container, 
                'inReservationList' => true,
                'existingOrderFound' => isset($existingOrder),
                'freight' => $freight
            ]);
        } else {
            // Container not in reservation list, apply normal freight
            $freight = $this->formatNumber($data['cartTotal'] ?? 0);
            
            Log::info('Container reservation check on BL update:', [
                'container' => $container, 
                'inReservationList' => false,
                'freight' => $freight
            ]);
        }

        $value = $this->formatNumber($data['value']);
        $other = $this->formatNumber($data['other']);
        $discount = 0; // Initialize discount

        // Apply manual overrides BEFORE calculating wharfage
        // If front-end set a freight manual override, use that value instead of the computed one
        if (isset($data['freight_manual']) && $data['freight_manual'] == '1') {
            // Use provided freight (validate/format it)
            $manualFreight = isset($data['freight']) ? $data['freight'] : 0;
            $freight = $this->formatNumber($manualFreight);
            Log::info('Freight manual override detected. Using user value:', ['freight' => $freight]);
        }

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
            
            foreach ($cart as $item) {
                // Check if item is not GROCERIES (including empty/undefined categories)
                // Only consider it as GROCERIES if explicitly set to 'GROCERIES'
                $category = trim(strtoupper($item->category ?? ''));
                if ($category !== 'GROCERIES') {
                    $onlyGroceries = false;
                }
            }

            // Set wharfage to zero if both VALUE and FREIGHT are zero
            if (($value <= 0 && $freight <= 0) || $skipWharfage) {
                $wharfage = 0;
            } else {
                // Calculate wharfage based on whether parcels contain only groceries or not
                // Default to 1200 formula unless ALL items are GROCERIES
                if ($onlyGroceries) {
                    $wharfage = $freight / 800 * 23; // Only when ALL items are GROCERIES
                } else {
                    $wharfage = $freight / 1200 * 23; // Default formula for mixed or non-grocery items
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

        // If front-end set a wharfage manual override, use that value instead of the computed one
        // (the front-end sets a hidden input named 'wharfage_manual' = '1' when user edits wharfage)
        if (isset($data['wharfage_manual']) && $data['wharfage_manual'] == '1') {
            // Use provided wharfage (validate/format it)
            $manualWharfage = isset($data['wharfage']) ? $data['wharfage'] : 0;
            $wharfage = $this->formatNumber($manualWharfage);
            Log::info('Wharfage manual override detected. Using user value:', ['wharfage' => $wharfage]);
        }

        foreach ($cart as $item) {
            // Items in FROZEN, PARCEL or AGGREGATES categories should have zero valuation
            if (($item->category ?? '') == 'FROZEN' || ($item->category ?? '') == 'PARCEL' || ($item->category ?? '') == 'AGGREGATES') {
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
            "wharfage_manual" => isset($data['wharfage_manual']) && $data['wharfage_manual'] == '1',
            "freight" => $freight,
            "freight_manual" => isset($data['freight_manual']) && $data['freight_manual'] == '1',
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
                    'action_type' => 'update',
                    'updated_at' => \Carbon\Carbon::now('Asia/Manila')
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
            'freight' => [$oldValues['freight'], $freight],
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

                // Check if item has multiple measurements (new format) or single measurements (old format)
                if (property_exists($item, 'measurements') && isset($item->measurements) && is_array($item->measurements) && !empty($item->measurements)) {
                    // New format: multiple measurements
                    $measurements = $item->measurements;

                    if ($checks == true){
                        $parcel = parcel::create([
                            'orderId' => $order->id,
                            'itemId' => $item->itemCode ?? '',
                            'itemName' => $item->itemName ?? '',
                            'itemPrice' => is_numeric($item->price ?? 0) ? floatval($item->price) : 0,
                            'quantity' => is_numeric($item->quantity ?? 0) ? floatval($item->quantity) : 0,
                            'length' => null, // Not used in new format
                            'width' => null,  // Not used in new format
                            'height' => null, // Not used in new format
                            'multiplier' => null, // Not used in new format
                            'measurements' => $measurements, // Store measurements array
                            'desc' => $item->description ?? '',
                            'total' => 0,
                            'unit' => $item->unit ?? '',
                            'weight' => $weight,
                        ]);
                    }
                    else{
                        $parcel = parcel::create([
                            'orderId' => $order->id,
                            'itemId' => $item->itemCode ?? '',
                            'itemName' => $item->itemName ?? '',
                            'itemPrice' => is_numeric($item->price ?? 0) ? floatval($item->price) : 0,
                            'quantity' => is_numeric($item->quantity ?? 0) ? floatval($item->quantity) : 0,
                            'length' => null, // Not used in new format
                            'width' => null,  // Not used in new format
                            'height' => null, // Not used in new format
                            'multiplier' => null, // Not used in new format
                            'measurements' => $measurements, // Store measurements array
                            'desc' => $item->description ?? '',
                            'total' => is_numeric($item->total ?? 0) ? floatval($item->total) : 0,
                            'unit' => $item->unit ?? '',
                            'weight' => $weight,
                        ]);
                    }
                } else {
                    // Old format: single measurements
                    $length = !empty($item->length ?? null) && $item->length !== '0' && $item->length !== '0.00' && $item->length !== ' ' ? $item->length : null;
                    $width = !empty($item->width ?? null) && $item->width !== '0' && $item->width !== '0.00' && $item->width !== ' ' ? $item->width : null;
                    $height = !empty($item->height ?? null) && $item->height !== '0' && $item->height !== '0.00' && $item->height !== ' ' ? $item->height : null;
                    $multiplier = property_exists($item, 'multiplier') && isset($item->multiplier) && $item->multiplier !== "N/A" && !empty($item->multiplier) && $item->multiplier !== '0' && $item->multiplier !== ' ' ? $item->multiplier : null;

                    if ($checks == true){
                        $parcel = parcel::create([
                            'orderId' => $order->id,
                            'itemId' => $item->itemCode ?? '',
                            'itemName' => $item->itemName ?? '',
                            'itemPrice' => is_numeric($item->price ?? 0) ? floatval($item->price) : 0,
                            'quantity' => is_numeric($item->quantity ?? 0) ? floatval($item->quantity) : 0,
                            'length' => $length,
                            'width' => $width,
                            'height' => $height,
                            'multiplier' => $multiplier,
                            'measurements' => null, // Not used in old format
                            'desc' => $item->description ?? '',
                            'total' => 0,
                            'unit' => $item->unit ?? '',
                            'weight' => $weight,
                        ]);
                    }
                    else{
                        $parcel = parcel::create([
                            'orderId' => $order->id,
                            'itemId' => $item->itemCode ?? '',
                            'itemName' => $item->itemName ?? '',
                            'itemPrice' => is_numeric($item->price ?? 0) ? floatval($item->price) : 0,
                            'quantity' => is_numeric($item->quantity ?? 0) ? floatval($item->quantity) : 0,
                            'length' => $length,
                            'width' => $width,
                            'height' => $height,
                            'multiplier' => $multiplier,
                            'measurements' => null, // Not used in old format
                            'desc' => $item->description ?? '',
                            'total' => is_numeric($item->total ?? 0) ? floatval($item->total) : 0,
                            'unit' => $item->unit ?? '',
                            'weight' => $weight,
                        ]);
                    }
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
        // Get the voyage record to retrieve dock_number for filtering
        // The voyageNum might be in format "1-IN" for ships I and II, or just "1" for others
        // Extract numeric part first (e.g., "1-IN" -> "1")
        $numericPart = preg_replace('/[^0-9]/', '', $voyageNum);
        $voyageRecord = voyage::where('ship', $shipNum)
            ->where('v_num', $numericPart ?: $voyageNum)
            ->where('lastStatus', 'READY')
            ->orderBy('dock_number', 'desc')
            ->first();
        
        // Use the dock_number from the voyage, or default to 0
        $dockNumber = $voyageRecord ? ($voyageRecord->dock_number ?? 0) : 0;

        // Get ALL orders for the specific ship and voyage (removed pagination)
        $orders = Order::where('shipNum', $shipNum)
            ->where('voyageNum', $voyageNum)
            ->where('dock_number', $dockNumber)
            ->with(['parcels' => function($query) {
                $query->select('id', 'orderId', 'itemName', 'quantity', 'unit', 'desc');
            }])
            ->select([
                'id', 'orderId', 'shipNum', 'voyageNum', 'containerNum', 'cargoType',
                'shipperName', 'recName', 'checkName', 'remark', 'note', 'origin', 'destination',
                'blStatus', 'totalAmount', 'freight', 'valuation', 'wharfage', 'value', 'other',
                'bir', 'discount', 'originalFreight', 'padlock_fee', 'or_ar_date',
                'OR', 'AR', 'updated_by', 'updated_location', 'image', 'created_at', 'creator', 'bl_computed'
            ])
            ->orderBy('orderId', 'asc')
            ->get(); // Load ALL orders (removed pagination limit)

        // Enhance each order with proper AR/OR display information
        $orders = $orders->map(function ($order) {
            $displayInfo = $this->getArOrDisplayInfo($order);
            $order->display_updated_by = $displayInfo['updated_by'];
            $order->display_updated_location = $displayInfo['updated_location'];
            $order->display_or_ar_date = $displayInfo['or_ar_date'];
            $order->last_updated_field = $displayInfo['last_updated_field'];
            
            // Add separate AR and OR display information
            $order->ar_display_info = $displayInfo['ar_display_info'];
            $order->or_display_info = $displayInfo['or_display_info'];
            
            return $order;
        });

        // Use the same orders for filter data (no need for separate query)
        $allOrdersForFilters = $orders;

        $filterData = [
            'uniqueOrderIds' => $allOrdersForFilters->pluck('orderId')->unique()->sort()->values(),
            'uniqueContainers' => $allOrdersForFilters->pluck('containerNum')->filter()->unique()->sort()->values(),
            'uniqueCargoTypes' => $allOrdersForFilters->pluck('cargoType')->filter()->unique()->sort()->values(),
            'uniqueShippers' => $allOrdersForFilters->pluck('shipperName')->filter()->unique()->sort()->values(),
            'uniqueConsignees' => $allOrdersForFilters->pluck('recName')->filter()->unique()->sort()->values(),
            'uniqueCheckers' => $allOrdersForFilters->pluck('checkName')->filter()->unique()->sort()->values(),
            'uniqueORs' => $allOrdersForFilters->pluck('OR')->filter()->unique()->sort()->values(),
            'uniqueARs' => $allOrdersForFilters->pluck('AR')->filter()->unique()->sort()->values(),
            'uniqueUpdatedBy' => $allOrdersForFilters->pluck('display_updated_by')->filter()->unique()->sort()->values(),
            'uniqueUpdatedLocation' => $allOrdersForFilters->pluck('display_updated_location')->filter()->unique()->sort()->values(),
        ];

        // Get unique item names from parcels - use all orders for parcels
        $allParcels = $orders->flatMap->parcels;
        $filterData['uniqueItemNames'] = $allParcels->pluck('itemName')->filter()->unique()->sort()->values();

        // Get parcels with pricelist for categories
        $parcels = Parcel::with(['pricelist' => function($query) {
            $query->select('id', 'item_code', 'category');
        }])->whereIn('orderId', $orders->pluck('id'))->get();
        
        // Get unique categories from all pricelists
        $uniqueCategories = PriceList::whereNotNull('category')->pluck('category')->unique()->sort()->values();
        
        // Get items by category from parcels
        $itemsByCategory = [];
        foreach ($uniqueCategories as $cat) {
            $items = $parcels->where('pricelist.category', $cat)->pluck('itemName')->unique()->sort()->values();
            $itemsByCategory[$cat] = $items;
        }
        
        // Update unique item names from parcels
        $uniqueItemNames = $parcels->pluck('itemName')->unique()->sort()->values();
        
        // Get order categories
        $orderCategories = $orders->mapWithKeys(function($order) use ($parcels) {
            $cats = $parcels->where('orderId', $order->id)->pluck('pricelist.category')->filter()->unique()->implode(',');
            return [$order->id => $cats];
        });
        
        // Add to filterData
        $filterData['uniqueCategories'] = $uniqueCategories;
        $filterData['itemsByCategory'] = $itemsByCategory;
        $filterData['uniqueItemNames'] = $uniqueItemNames;
        $filterData['orderCategories'] = $orderCategories;

        return view('masterlist.list', compact('orders', 'shipNum', 'voyageNum', 'filterData'));
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
        
        // Get ALL orders for the specific voyage (removed pagination)
        $orders = Order::where('shipNum', $voyage->ship)
            ->where('voyageNum', $voyageKey)
            ->where('dock_number', $voyage->dock_number ?? 0)
            ->with(['parcels' => function($query) {
                $query->select('id', 'orderId', 'itemName', 'quantity', 'unit', 'desc');
            }])
            ->select([
                'id', 'orderId', 'shipNum', 'voyageNum', 'containerNum', 'cargoType',
                'shipperName', 'recName', 'checkName', 'remark', 'note', 'origin', 'destination',
                'blStatus', 'totalAmount', 'freight', 'valuation', 'wharfage', 'value', 'other',
                'bir', 'discount', 'originalFreight', 'padlock_fee', 'or_ar_date',
                'OR', 'AR', 'updated_by', 'updated_location', 'image', 'created_at', 'dock_number', 'creator', 'bl_computed'
            ])
            ->orderBy('orderId', 'asc')
            ->get(); // Load ALL orders (removed pagination limit)

        // Enhance each order with proper AR/OR display information
        $orders = $orders->map(function ($order) {
            $displayInfo = $this->getArOrDisplayInfo($order);
            $order->display_updated_by = $displayInfo['updated_by'];
            $order->display_updated_location = $displayInfo['updated_location'];
            $order->display_or_ar_date = $displayInfo['or_ar_date'];
            $order->last_updated_field = $displayInfo['last_updated_field'];
            
            // Add separate AR and OR display information
            $order->ar_display_info = $displayInfo['ar_display_info'];
            $order->or_display_info = $displayInfo['or_display_info'];
            
            return $order;
        });

        // Use the same orders for filter data (no need for separate query)
        $allOrdersForFilters = $orders;

        $filterData = [
            'uniqueOrderIds' => $allOrdersForFilters->pluck('orderId')->unique()->sort()->values(),
            'uniqueContainers' => $allOrdersForFilters->pluck('containerNum')->filter()->unique()->sort()->values(),
            'uniqueCargoTypes' => $allOrdersForFilters->pluck('cargoType')->filter()->unique()->sort()->values(),
            'uniqueShippers' => $allOrdersForFilters->pluck('shipperName')->filter()->unique()->sort()->values(),
            'uniqueConsignees' => $allOrdersForFilters->pluck('recName')->filter()->unique()->sort()->values(),
            'uniqueCheckers' => $allOrdersForFilters->pluck('checkName')->filter()->unique()->sort()->values(),
            'uniqueORs' => $allOrdersForFilters->pluck('OR')->filter()->unique()->sort()->values(),
            'uniqueARs' => $allOrdersForFilters->pluck('AR')->filter()->unique()->sort()->values(),
            'uniqueUpdatedBy' => $allOrdersForFilters->pluck('updated_by')->filter()->unique()->sort()->values(),
            'uniqueUpdatedLocation' => $allOrdersForFilters->pluck('updated_location')->filter()->unique()->sort()->values(),
        ];

        // Get unique item names from parcels - use all orders for parcels
        $allParcels = $orders->flatMap->parcels;
        $filterData['uniqueItemNames'] = $allParcels->pluck('itemName')->filter()->unique()->sort()->values();

        // Get parcels with pricelist for categories
        $parcels = Parcel::with(['pricelist' => function($query) {
            $query->select('id', 'item_code', 'category');
        }])->whereIn('orderId', $orders->pluck('id'))->get();
        
        // Get unique categories from all pricelists
        $uniqueCategories = PriceList::whereNotNull('category')->pluck('category')->unique()->sort()->values();
        
        // Get items by category from parcels
        $itemsByCategory = [];
        foreach ($uniqueCategories as $cat) {
            $items = $parcels->where('pricelist.category', $cat)->pluck('itemName')->unique()->sort()->values();
            $itemsByCategory[$cat] = $items;
        }
        
        // Update unique item names from parcels
        $uniqueItemNames = $parcels->pluck('itemName')->unique()->sort()->values();
        
        // Get order categories
        $orderCategories = $orders->mapWithKeys(function($order) use ($parcels) {
            $cats = $parcels->where('orderId', $order->id)->pluck('pricelist.category')->filter()->unique()->implode(',');
            return [$order->id => $cats];
        });
        
        // Add to filterData
        $filterData['uniqueCategories'] = $uniqueCategories;
        $filterData['itemsByCategory'] = $itemsByCategory;
        $filterData['uniqueItemNames'] = $uniqueItemNames;
        $filterData['orderCategories'] = $orderCategories;

        return view('masterlist.list', [
            'orders' => $orders,
            'shipNum' => $voyage->ship,
            'voyageNum' => $voyageKey,
            'filterData' => $filterData
        ]);
    }

    /**
     * Get the appropriate display information for AR/OR separately for each field
     */
    private function getArOrDisplayInfo($order)
    {
        // Get the latest AR update from the logs (only non-empty values)
        $latestArUpdate = OrderUpdateLog::where('order_id', $order->id)
            ->where('field_name', 'AR')
            ->whereNotNull('new_value')
            ->where('new_value', '!=', '')
            ->latest('updated_at')
            ->first();
            
        $latestOrUpdate = OrderUpdateLog::where('order_id', $order->id)
            ->where('field_name', 'OR')
            ->whereNotNull('new_value')
            ->where('new_value', '!=', '')
            ->latest('updated_at')
            ->first();

        // Prepare separate display info for AR and OR
        $arDisplayInfo = null;
        $orDisplayInfo = null;

        // Process AR display info - only if AR field has a value
        if ($latestArUpdate && !empty(trim($order->AR))) {
            $location = '';
            $actualValue = $latestArUpdate->new_value;
            
            if (strpos($latestArUpdate->new_value, '|LOCATION:') !== false) {
                $parts = explode('|LOCATION:', $latestArUpdate->new_value);
                $actualValue = $parts[0] ?? '';
                $location = $parts[1] ?? '';
            } else {
                $location = $this->extractLocationFromUser($latestArUpdate->updated_by);
            }

            $arDisplayInfo = [
                'updated_by' => $latestArUpdate->updated_by,
                'updated_location' => $location,
                'or_ar_date' => $latestArUpdate->updated_at,
                'field_name' => 'AR'
            ];
        }

        // Process OR display info - only if OR field has a value
        if ($latestOrUpdate && !empty(trim($order->OR))) {
            $location = '';
            $actualValue = $latestOrUpdate->new_value;
            
            if (strpos($latestOrUpdate->new_value, '|LOCATION:') !== false) {
                $parts = explode('|LOCATION:', $latestOrUpdate->new_value);
                $actualValue = $parts[0] ?? '';
                $location = $parts[1] ?? '';
            } else {
                $location = $this->extractLocationFromUser($latestOrUpdate->updated_by);
            }

            $orDisplayInfo = [
                'updated_by' => $latestOrUpdate->updated_by,
                'updated_location' => $location,
                'or_ar_date' => $latestOrUpdate->updated_at,
                'field_name' => 'OR'
            ];
        }

        // Determine overall display based on which field was updated more recently
        // Only consider updates where the corresponding field actually has a value
        $validArUpdate = $latestArUpdate && !empty(trim($order->AR)) ? $latestArUpdate : null;
        $validOrUpdate = $latestOrUpdate && !empty(trim($order->OR)) ? $latestOrUpdate : null;
        
        $latestUpdate = null;
        if ($validArUpdate && $validOrUpdate) {
            $latestUpdate = $validArUpdate->updated_at > $validOrUpdate->updated_at ? $validArUpdate : $validOrUpdate;
        } elseif ($validArUpdate) {
            $latestUpdate = $validArUpdate;
        } elseif ($validOrUpdate) {
            $latestUpdate = $validOrUpdate;
        }

        // Return comprehensive display information
        if ($latestUpdate) {
            $location = '';
            if (strpos($latestUpdate->new_value, '|LOCATION:') !== false) {
                $parts = explode('|LOCATION:', $latestUpdate->new_value);
                $location = $parts[1] ?? '';
            } else {
                $location = $this->extractLocationFromUser($latestUpdate->updated_by);
            }

            return [
                'updated_by' => $latestUpdate->updated_by,
                'updated_location' => $location,
                'or_ar_date' => $latestUpdate->updated_at,
                'last_updated_field' => $latestUpdate->field_name,
                'ar_display_info' => $arDisplayInfo,
                'or_display_info' => $orDisplayInfo
            ];
        }

        // If no valid updates found (both fields are empty), return empty display
        return [
            'updated_by' => null,
            'updated_location' => null,
            'or_ar_date' => null,
            'last_updated_field' => null,
            'ar_display_info' => null,
            'or_display_info' => null
        ];
    }

    /**
     * Extract location from user (temporary helper method)
     */
    private function extractLocationFromUser($updatedBy)
    {
        // This is a temporary solution. Ideally, you'd store location separately
        // For now, we'll try to get the location from the current user record
        $user = User::whereRaw("CONCAT(fName, ' ', lName) = ?", [$updatedBy])->first();
        return $user ? $user->location : '';
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
                'field' => 'required|string|in:OR,AR,image,containerNum,bir,freight,value,valuation,discount,other,wharfage,originalFreight,padlock_fee,ppa_manila,remark,checkName,cargoType',
                'value' => 'nullable|string|max:255',
            ]);
        } else {
            $request->validate([
                'field' => 'required|string|in:OR,AR,image,containerNum,bir,freight,value,valuation,discount,other,wharfage,originalFreight,padlock_fee,ppa_manila,remark,checkName,cargoType',
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
                $logData = [
                    'order_id' => $orderId,
                    'updated_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                    'field_name' => $fieldName,
                    'old_value' => $oldVal,
                    'new_value' => $newVal,
                    'action_type' => 'update',
                    'updated_at' => \Carbon\Carbon::now('Asia/Manila') // Explicitly set timezone
                ];

                // For AR/OR updates, store location info in the new_value field
                if (in_array($fieldName, ['AR', 'OR']) && !empty($newVal)) {
                    $logData['new_value'] = $newVal . '|LOCATION:' . Auth::user()->location;
                }

                OrderUpdateLog::create($logData);
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
                // Check if all parcels are GROCERIES
                $parcels = Parcel::where('orderId', $order->id)->get();
                $itemCodes = $parcels->pluck('itemId');
                $pricelists = PriceList::whereIn('item_code', $itemCodes)->get()->keyBy('item_code');
                $allGroceries = $parcels->isNotEmpty() && $parcels->every(function($parcel) use ($pricelists) {
                    $pricelist = $pricelists->get($parcel->itemId);
                    return $pricelist && strtoupper($pricelist->category ?? '') === 'GROCERIES';
                });
                $divisor = $allGroceries ? 800 : 1200;
                
                // Use formula: FREIGHT / divisor * 23, min 11.20, if freight is zero then 11.20
                $wharfage = ($freight > 0) ? ($freight / $divisor * 23) : 11.20;
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
        } elseif (in_array($field, ['value', 'valuation', 'other', 'padlock_fee', 'ppa_manila'])) {
            $oldValue = $order->$field;
            $oldCalculatedValuation = $order->valuation;
            $oldTotalAmount = $order->totalAmount;

            $order->$field = $request->value;
            $freight = $order->freight ?? 0;
            $value = $field === 'value' ? $request->value : ($order->value ?? 0);
            $other = $field === 'other' ? $request->value : ($order->other ?? 0);
            $wharfage = $order->wharfage ?? 0;
            $padlock_fee = $field === 'padlock_fee' ? $request->value : ($order->padlock_fee ?? 0);
            $ppa_manila = $field === 'ppa_manila' ? $request->value : ($order->ppa_manila ?? 0);
            $valuation = ($freight + $value) * 0.0075;
            $order->valuation = $valuation;
            $total = $freight + $valuation + $other + $wharfage + $padlock_fee + $ppa_manila;
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
            $newRemark = $request->value;
            
            Log::info('Remark Update Request:', [
                'orderId' => $orderId,
                'oldRemark' => $oldRemark,
                'newRemark' => $newRemark,
                'orderBeforeUpdate' => $order->toArray()
            ]);
            
            $order->remark = $newRemark;
            
            // Log the remark change
            $logFieldUpdate('remark', $oldRemark, $order->remark);
            
            $saveResult = $order->save();
            
            Log::info('Remark Update Result:', [
                'orderId' => $orderId,
                'saveResult' => $saveResult,
                'newRemark' => $newRemark,
                'orderAfterSave' => $order->fresh()->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Remark updated successfully!',
                'newValue' => $order->remark,
                'orderId' => $orderId
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

            $order->$field = $request->value;

            if (in_array($field, ['OR', 'AR'])) {
                // Determine the BL status based on whether either OR or AR has a value
                if (empty($order->OR) && empty($order->AR)) {
                    $order->blStatus = 'UNPAID';
                } else {
                    $order->blStatus = 'PAID';
                }

                // Log the OR/AR field change (this is what we'll use for display information)
                $logFieldUpdate($field, $oldFieldValue, $order->$field);

                // Log BL status change if it changed
                if ($oldBlStatus != $order->blStatus) {
                    $logFieldUpdate('blStatus', $oldBlStatus, $order->blStatus);
                }
            } else {
                // For other fields, just log the primary field change
                $logFieldUpdate($field, $oldFieldValue, $order->$field);
            }
        }

        $order->save();

        Log::info('Order After Update:', $order->toArray());

        // Return updated values for OR/AR fields
        if (in_array($field, ['OR', 'AR'])) {
            // Get the appropriate display information based on the latest update
            $displayInfo = $this->getArOrDisplayInfo($order);
            
            // Get field-specific display info
            $fieldSpecificInfo = null;
            if ($field === 'AR' && $displayInfo['ar_display_info']) {
                $fieldSpecificInfo = $displayInfo['ar_display_info'];
            } elseif ($field === 'OR' && $displayInfo['or_display_info']) {
                $fieldSpecificInfo = $displayInfo['or_display_info'];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Order field updated successfully!',
                'blStatus' => $order->blStatus,
                'field_type' => $field,
                // Return field-specific information if available, otherwise use general info
                'or_ar_date' => $fieldSpecificInfo ? 
                    \Carbon\Carbon::parse($fieldSpecificInfo['or_ar_date'])->setTimezone('Asia/Manila')->format('F d, Y h:i A') : 
                    ($displayInfo['or_ar_date'] ? \Carbon\Carbon::parse($displayInfo['or_ar_date'])->setTimezone('Asia/Manila')->format('F d, Y h:i A') : ''),
                'updated_by' => $fieldSpecificInfo ? 
                    $fieldSpecificInfo['updated_by'] : 
                    ($displayInfo['updated_by'] ?? ''),
                'updated_location' => $fieldSpecificInfo ? 
                    $fieldSpecificInfo['updated_location'] : 
                    ($displayInfo['updated_location'] ?? ''),
                // Include separate AR and OR display info for client-side handling
                'ar_display_info' => $displayInfo['ar_display_info'],
                'or_display_info' => $displayInfo['or_display_info']
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Order field updated successfully!']);
    }

    /**
     * Update SOA number for a specific customer, ship, and voyage combination
     */
    public function updateSoaNumber(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|integer',
                'ship' => 'required|string',
                'voyage' => 'required|string',
                'soa_number' => 'required|string|max:50'
            ]);

            $customerId = $request->customer_id;
            $ship = $request->ship;
            $voyage = urldecode($request->voyage);
            $soaNumber = $request->soa_number;

            // Check if an SOA number record already exists for this combination
            $soaRecord = SoaNumber::where('customer_id', $customerId)
                ->where('ship', $ship)
                ->where('voyage', $voyage)
                ->first();

            if ($soaRecord) {
                // Update existing record
                $soaRecord->update(['soa_number' => $soaNumber]);
            } else {
                // Create new record
                SoaNumber::create([
                    'customer_id' => $customerId,
                    'ship' => $ship,
                    'voyage' => $voyage,
                    'soa_number' => $soaNumber,
                    'year' => date('Y'),
                    'sequence' => 0 // Set to 0 for manually entered SOA numbers
                ]);
            }

            \Log::info('SOA Number Updated', [
                'customer_id' => $customerId,
                'ship' => $ship,
                'voyage' => $voyage,
                'soa_number' => $soaNumber
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SOA number updated successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('SOA Number Update Error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update SOA number: ' . $e->getMessage()
            ], 500);
        }
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
                'action_type' => 'update',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
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
                'action_type' => 'update',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
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
                'action_type' => 'delete',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
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
                'action_type' => 'update',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
            ]);
        }

        if ($oldDiscount != $order->discount) {
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => $userName,
                'field_name' => 'discount',
                'old_value' => $oldDiscount,
                'new_value' => $order->discount,
                'action_type' => 'update',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
            ]);
        }

        if ($oldTotalAmount != $order->totalAmount) {
            OrderUpdateLog::create([
                'order_id' => $orderId,
                'updated_by' => $userName,
                'field_name' => 'totalAmount',
                'old_value' => $oldTotalAmount,
                'new_value' => $order->totalAmount,
                'action_type' => 'update',
                'updated_at' => \Carbon\Carbon::now('Asia/Manila')
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

        // Get all available categories from price list for filtering dropdown
        $categories = PriceList::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

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
                'orders.recName',
                'orders.checkName',
                'orders.cargoType',
                'orders.created_at as order_date',
                'pricelists.category'
            )
            ->join('orders', 'parcels.orderId', '=', 'orders.id')
            ->leftJoin('pricelists', 'parcels.itemId', '=', 'pricelists.item_code');

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

        // Apply category filter if provided
        if ($request->filled('category')) {
            $query->where('pricelists.category', $request->category);
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

        return view('masterlist.parcel', compact('parcels', 'ships', 'voyages', 'categories', 'sortField', 'sortDirection', 'perPage'));
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
        
        if (!$customer_id) {
            return redirect()->route('masterlist.soa')->with('error', 'Customer ID is required.');
        }
        
        // Get the main customer
        $customer = Customer::with('subAccounts')->find($customer_id);
        
        if (!$customer) {
            return redirect()->route('masterlist.soa')->with('error', 'Customer not found.');
        }
        
        // Get sub-account IDs
        $subAccountIds = $customer->subAccounts->pluck('sub_account_number')->toArray();
        
        // Get all orders for main customer and sub-accounts - regardless of origin
        // Show all BLs where customer is either shipper OR consignee
        $orders = Order::where(function($query) use ($customer_id, $subAccountIds) {
            $query->where(function($q) use ($customer_id, $subAccountIds) {
                // Orders where they are consignee
                $q->where('recId', $customer_id)
                  ->orWhereIn('recId', $subAccountIds);
            })->orWhere(function($q) use ($customer_id, $subAccountIds) {
                // Orders where they are shipper
                $q->where('shipperId', $customer_id)
                  ->orWhereIn('shipperId', $subAccountIds);
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
            
            // Get the customer and sub-account IDs
            $customer = Customer::with('subAccounts')->findOrFail($customerId);
            $subAccountIds = $customer->subAccounts->pluck('sub_account_number')->toArray();
            
            // Decode the voyage number
            $decodedVoyageNum = urldecode($voyageNum);
            
            // Get all orders for this ship/voyage and customer
            $orders = \App\Models\order::where('shipNum', $shipNum)
                ->whereRaw('voyageNum = ?', [$decodedVoyageNum])
                ->where(function($query) use ($customerId, $subAccountIds) {
                    $query->where('recId', $customerId)
                          ->orWhere('shipperId', $customerId);
                    
                    // Also check sub-accounts
                    if (!empty($subAccountIds)) {
                        $query->orWhereIn('recId', $subAccountIds)
                              ->orWhereIn('shipperId', $subAccountIds);
                    }
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
        $customer = Customer::with('subAccounts')->find($customerId);
        
        if (!$customer) {
            return redirect()->route('masterlist.soa')->with('error', 'Customer not found.');
        }
        
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
            // Show all BLs where customer is either shipper OR consignee, regardless of origin
            $orders = Order::where('shipNum', $ship)
                ->whereRaw('voyageNum = ?', [$decodedVoyageNum])
                ->where(function($query) use ($customerId, $subAccountIds) {
                $query->where(function($q) use ($customerId, $subAccountIds) {
                    // Orders where they are consignee
                    $q->where('recId', $customerId)
                      ->orWhereIn('recId', $subAccountIds);
                })->orWhere(function($q) use ($customerId, $subAccountIds) {
                    // Orders where they are shipper
                    $q->where('shipperId', $customerId)
                      ->orWhereIn('shipperId', $subAccountIds);
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
        
        // Get existing SOA number if it exists
        $existingSoaNumber = SoaNumber::where('customer_id', $customerId)
            ->where('ship', $ship)
            ->where('voyage', $decodedVoyageNum)
            ->first();
            
        $soaNumber = $existingSoaNumber ? $existingSoaNumber->soa_number : '';
        
        return view('masterlist.soa_temp', [
            'orders' => $orders,
            'customer' => $customer,
            'ship' => $ship,
            'voyage' => $voyage,
            'origin' => $origin,
            'destination' => $destination,
            'soaNumber' => $soaNumber
        ]);
    }

    public function soa_custom(Request $request, $ship, $voyage, $customerId)
    {
        // Get the customer
        $customer = Customer::with('subAccounts')->find($customerId);
        
        if (!$customer) {
            return redirect()->route('masterlist.soa')->with('error', 'Customer not found.');
        }
        
        // Get sub-account IDs
        $subAccountIds = $customer->subAccounts->pluck('sub_account_number')->toArray();
        
        // Decode the voyage number to handle 'IN' and 'OUT' properly
        $decodedVoyageNum = urldecode($voyage);
        
        // Log for debugging purposes
        \Log::info('SOA Custom Request', [
            'Original Voyage Num' => $voyage,
            'Decoded Voyage Num' => $decodedVoyageNum,
            'Ship' => $ship,
            'Customer ID' => $customerId
        ]);
        
        try {
            // Get all orders for this ship/voyage that belong to the customer or their sub-accounts
            // Show all BLs where customer is either shipper OR consignee, regardless of origin
            $orders = Order::where('shipNum', $ship)
                ->whereRaw('voyageNum = ?', [$decodedVoyageNum])
                ->where(function($query) use ($customerId, $subAccountIds) {
                $query->where(function($q) use ($customerId, $subAccountIds) {
                    // Orders where they are consignee
                    $q->where('recId', $customerId)
                      ->orWhereIn('recId', $subAccountIds);
                })->orWhere(function($q) use ($customerId, $subAccountIds) {
                    // Orders where they are shipper
                    $q->where('shipperId', $customerId)
                      ->orWhereIn('shipperId', $subAccountIds);
                });
            })
            ->with('parcels') // Include parcels relationship
            ->get();
            
            \Log::info('SOA Custom Orders Found', [
                'Count' => $orders->count(), 
                'Orders' => $orders->pluck('id', 'orderId')->toArray()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('SOA Custom Error', [
                'Error' => $e->getMessage(),
                'Trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error loading Custom Statement of Account: ' . $e->getMessage());
        }
        
        // Get the origin and destination from the first order
        $origin = $orders->first() ? $orders->first()->origin : '';
        $destination = $orders->first() ? $orders->first()->destination : '';
        
        // Get existing SOA number for this customer, ship, and voyage combination
        $soaNumber = '';
        $soaRecord = SoaNumber::where('customer_id', $customerId)
            ->where('ship', $ship)
            ->where('voyage', $decodedVoyageNum)
            ->first();
        
        if ($soaRecord) {
            $soaNumber = $soaRecord->soa_number;
        }
        
        return view('masterlist.soa_custom', [
            'orders' => $orders,
            'customer' => $customer,
            'ship' => $ship,
            'voyage' => $voyage,
            'origin' => $origin,
            'destination' => $destination,
            'soaNumber' => $soaNumber
        ]);
    }

    /**
     * Generate SOA for Government - Per BL
     */
    public function soa_custom_per_bl(Request $request, $ship, $voyage, $customerId, $orderId)
    {
        // Get the customer
        $customer = Customer::with('subAccounts')->find($customerId);
        
        if (!$customer) {
            return redirect()->route('masterlist.soa')->with('error', 'Customer not found.');
        }
        
        // Decode the voyage number to handle 'IN' and 'OUT' properly
        $decodedVoyageNum = urldecode($voyage);
        
        // Log for debugging purposes
        \Log::info('SOA Custom Per BL Request', [
            'Original Voyage Num' => $voyage,
            'Decoded Voyage Num' => $decodedVoyageNum,
            'Ship' => $ship,
            'Customer ID' => $customerId,
            'Order ID' => $orderId
        ]);
        
        try {
            // Get the specific order
            $order = Order::where('id', $orderId)
                ->where('shipNum', $ship)
                ->whereRaw('voyageNum = ?', [$decodedVoyageNum])
                ->with('parcels') // Include parcels relationship
                ->first();
            
            if (!$order) {
                return redirect()->back()->with('error', 'Order not found.');
            }
            
            // Enhance order with display information for AR/OR
            $displayInfo = $this->getArOrDisplayInfo($order);
            $order->display_updated_by = $displayInfo['updated_by'];
            $order->display_updated_location = $displayInfo['updated_location'];
            $order->display_or_ar_date = $displayInfo['or_ar_date'];
            $order->last_updated_field = $displayInfo['last_updated_field'];
            $order->ar_display_info = $displayInfo['ar_display_info'];
            $order->or_display_info = $displayInfo['or_display_info'];
            
            // Wrap single order in collection to maintain compatibility with view
            $orders = collect([$order]);
            
            \Log::info('SOA Custom Per BL Order Found', [
                'Order ID' => $order->id,
                'BL Number' => $order->orderId
            ]);
            
        } catch (\Exception $e) {
            \Log::error('SOA Custom Per BL Error', [
                'Error' => $e->getMessage(),
                'Trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error loading Custom Statement of Account: ' . $e->getMessage());
        }
        
        // Get the origin and destination from the order
        $origin = $order->origin ?? '';
        $destination = $order->destination ?? '';
        
        // Get existing SOA number for this customer, ship, voyage, and order combination
        $soaNumber = '';
        $soaRecord = SoaNumber::where('customer_id', $customerId)
            ->where('ship', $ship)
            ->where('voyage', $decodedVoyageNum)
            ->where('order_id', $orderId)
            ->first();
        
        if ($soaRecord) {
            $soaNumber = $soaRecord->soa_number;
        }
        
        return view('masterlist.soa_custom_per_bl', [
            'orders' => $orders,
            'customer' => $customer,
            'ship' => $ship,
            'voyage' => $voyage,
            'origin' => $origin,
            'destination' => $destination,
            'soaNumber' => $soaNumber,
            'orderId' => $orderId
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

    public function blListAll(Request $request) {
        // Get all orders with pagination
        $orders = Order::orderBy('created_at', 'desc')
            ->paginate(20);

        // Get all customers for filtering
        $customers = Customer::select('id', 'first_name', 'last_name', 'company_name')
            ->orderBy('first_name')
            ->get();

        // Get all ships for filtering
        $ships = Ship::orderBy('ship_number')->get();

        // Pass data to the view
        return view('masterlist.bl_list_all', compact('orders', 'customers', 'ships'));
    }

    public function toggleBlComputed(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            
            // Find the order
            $order = Order::find($orderId);
            
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }
            
            // Toggle the bl_computed status
            $order->bl_computed = !$order->bl_computed;
            $order->save();
            
            // Log the update
            Log::info('BL Computed toggled', [
                'order_id' => $orderId,
                'bl_computed' => $order->bl_computed,
                'updated_by' => Auth::id(),
            ]);
            
            return response()->json([
                'success' => true,
                'bl_computed' => $order->bl_computed,
                'message' => 'BL Computed status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling BL Computed status', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);
            
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }

    public function markBlComputed(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            
            // Find the order
            $order = Order::find($orderId);
            
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }
            
            // Mark as computed
            $order->bl_computed = true;
            $order->save();
            
            // Log the update
            Log::info('BL marked as computed', [
                'order_id' => $orderId,
                'updated_by' => Auth::id(),
            ]);
            
            return response()->json([
                'success' => true,
                'bl_computed' => $order->bl_computed,
                'message' => 'BL marked as computed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking BL as computed', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);
            
            return response()->json(['success' => false, 'message' => 'Error marking BL as computed: ' . $e->getMessage()], 500);
        }
    }
}