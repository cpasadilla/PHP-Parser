<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SaverStarShip;
use App\Models\Customer;
use App\Models\SubAccount;
use App\Models\User;
use App\Models\order as Order;
use App\Models\PriceList;
use App\Models\parcel as Parcel;
use App\Models\OrderUpdateLog;
use App\Models\OrderDeleteLog;
use App\Models\locations;

class SaverStarController extends Controller
{
    // Masterlist index - show all Saver Star voyages
    public function index(Request $request) {
        $ship = SaverStarShip::first();
        
        if (!$ship) {
            return view('saverstar.index', ['ship' => null, 'voyages' => collect()]);
        }
        
        // Get unique voyage numbers from orders where shipNum = 'SAVER'
        $voyages = Order::where('shipNum', 'SAVER')
            ->select('voyageNum', DB::raw('MIN(id) as order_id'), DB::raw('COUNT(*) as bl_count'))
            ->groupBy('voyageNum')
            ->orderBy('voyageNum', 'desc')
            ->get();
        
        return view('saverstar.index', compact('ship', 'voyages'));
    }

    // Store new Saver Star ship
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        SaverStarShip::create([
            'name' => $request->name,
            'status' => 'READY',
        ]);

        return redirect()->back()->with('success', 'Saver Star ship added successfully!');
    }

    // Update ship status
    public function update(Request $request, $id) {
        $ship = SaverStarShip::findOrFail($id);
        $ship->status = $request->status;
        $ship->save();

        return redirect()->back()->with('success', 'Ship status updated successfully!');
    }

    // Delete ship
    public function destroy($id) {
        $ship = SaverStarShip::findOrFail($id);
        $ship->delete();

        return redirect()->back()->with('success', 'Ship deleted successfully!');
    }
    
    // Delete voyage and all its associated orders
    public function deleteVoyage($voyageNum) {
        try {
            DB::beginTransaction();
            
            // Get all orders for this voyage
            $orders = Order::where('shipNum', 'SAVER')
                ->where('voyageNum', $voyageNum)
                ->get();
            
            foreach ($orders as $order) {
                // Delete associated parcels
                Parcel::where('orderId', $order->id)->delete();
                
                // Log deletion
                OrderDeleteLog::create([
                    'order_id' => $order->id,
                    'deleted_by' => Auth::user()->fName . ' ' . Auth::user()->lName,
                    'reason' => 'Voyage deleted',
                    'deleted_at' => \Carbon\Carbon::now('Asia/Manila')
                ]);
                
                // Delete the order
                $order->delete();
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Voyage and all associated BLs deleted successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Voyage Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting voyage: ' . $e->getMessage());
        }
    }

    // Show BL creation form
    public function showBlForm($id) {
        $ship = SaverStarShip::findOrFail($id);
        $locations = locations::all();
        $lists = PriceList::all();
        
        // For Saver Star, we don't need ships list since ship is fixed
        // But we pass an empty collection to avoid blade errors
        $ships = collect();

        return view('saverstar.bl', compact('ship', 'locations', 'lists', 'ships'));
    }

    // Direct BL creation - automatically gets the first (and only) Saver Star ship
    public function createBl() {
        $ship = SaverStarShip::first();
        
        if (!$ship) {
            return redirect()->route('saverstar.index')->with('error', 'No Saver Star ship found. Please contact administrator.');
        }
        
        if ($ship->status != 'CREATE BL') {
            return redirect()->route('saverstar.index')->with('error', 'Cannot create BL. Ship status is: ' . $ship->status);
        }
        
        $locations = locations::all();
        $lists = PriceList::all();
        $ships = collect();

        return view('saverstar.bl', compact('ship', 'locations', 'lists', 'ships'));
    }
    
    // Show voyage details (list of all BLs for a specific voyage)
    public function voyageList($voyageNum) {
        // Get all orders for this voyage
        $orders = Order::with(['parcels' => function($query) {
            $query->select('id', 'orderId', 'itemName', 'quantity', 'unit', 'desc');
        }])
        ->where('shipNum', 'SAVER')
        ->where('voyageNum', $voyageNum)
        ->select([
            'id', 'orderId', 'shipNum', 'voyageNum', 'containerNum', 'cargoType',
            'shipperName', 'recName', 'checkName', 'remark', 'note', 'origin', 'destination',
            'blStatus', 'totalAmount', 'freight', 'valuation', 'wharfage', 'value', 'other',
            'bir', 'discount', 'originalFreight', 'padlock_fee', 'or_ar_date',
               'OR', 'AR', 'updated_by', 'updated_location', 'image', 'created_at', 'creator', 'bl_computed'
        ])
        ->orderBy('orderId', 'asc')
        ->get();
        
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
        ];
        
        $shipNum = 'SAVER';
        
        return view('saverstar.list', compact('orders', 'filterData', 'shipNum', 'voyageNum'));
    }

    // Store BL (Order)
    public function storeBl(Request $request) {
        try {
            DB::beginTransaction();

            // Create order with manual voyage number
            $order = Order::create([
                'shipNum' => 'SAVER', // Identifier for Saver Star
                'voyageNum' => $request->input('voyage_number'), // Manual input
                'customerId' => $request->input('customerId'),
                'subAccountId' => $request->input('subAccountId'),
                'POD' => $request->input('POD'),
                'packages' => $request->input('packages'),
                'orderDate' => $request->input('orderDate'),
                'remark' => $request->input('remark'),
                // Add other fields as needed
            ]);

            // Create parcels if provided
            if ($request->has('parcels')) {
                foreach ($request->input('parcels') as $parcelData) {
                    Parcel::create([
                        'orderId' => $order->id,
                        'description' => $parcelData['description'] ?? '',
                        'quantity' => $parcelData['quantity'] ?? 0,
                        // Add other parcel fields as needed
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('saverstar.index')->with('success', 'BL created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Saver Star BL Creation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating BL: ' . $e->getMessage());
        }
    }
}
