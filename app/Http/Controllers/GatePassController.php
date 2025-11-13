<?php

namespace App\Http\Controllers;

use App\Models\GatePass;
use App\Models\GatePassItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GatePassController extends Controller
{
    /**
     * Check if user has permission to access gate pass module
     */
    private function checkPermission($operation = 'access')
    {
        if ($operation === 'access') {
            if (!Auth::user()->hasPagePermission('gatepass')) {
                abort(403, 'Unauthorized access to Gate Pass module.');
            }
        } else {
            if (!Auth::user()->hasPermission('gatepass', $operation)) {
                abort(403, "Unauthorized to {$operation} gate passes.");
            }
        }
    }

    /**
     * Display a listing of gate passes
     */
    public function index(Request $request)
    {
        $this->checkPermission('access');

        $query = GatePass::with(['order', 'items']);

        // Filter by BL number if provided
        if ($request->has('bl_number') && $request->bl_number) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('orderId', $request->bl_number);
            });
        }

        // Filter by container number if provided
        if ($request->has('container') && $request->container) {
            $query->where('container_number', 'like', '%' . $request->container . '%');
        }

        // Filter by gate pass number if provided
        if ($request->has('gate_pass_no') && $request->gate_pass_no) {
            $query->where('gate_pass_no', 'like', '%' . $request->gate_pass_no . '%');
        }

        $gatePasses = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('gatepass.index', compact('gatePasses'));
    }

    /**
     * Show the form for creating a new gate pass
     */
    public function create(Request $request)
    {
        $this->checkPermission('create');

        $orderId = $request->get('order_id');
        $order = null;
        $previousGatePasses = collect();

        if ($orderId) {
            $order = Order::with(['parcels', 'gatePasses.items'])->findOrFail($orderId);
            $previousGatePasses = $order->gatePasses;
        }

        return view('gatepass.create', compact('order', 'previousGatePasses'));
    }

    /**
     * Store a newly created gate pass in storage
     */
    public function store(Request $request)
    {
        $this->checkPermission('create');

        $validated = $request->validate([
            'gate_pass_no' => 'required|string',
            'order_id' => 'required|exists:orders,id',
            'container_number' => 'nullable|string',
            'shipper_name' => 'nullable|string',
            'consignee_name' => 'nullable|string',
            'checker_notes' => 'nullable|string',
            'checker_name' => 'nullable|string',
            'receiver_name' => 'nullable|string',
            'release_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.total_quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.released_quantity' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($validated['order_id']);

            // Create the gate pass
            $user = Auth::user();
            $createdByName = trim(($user->fName ?? '') . ' ' . ($user->lName ?? ''));
            
            $gatePass = GatePass::create([
                'gate_pass_no' => $validated['gate_pass_no'],
                'order_id' => $validated['order_id'],
                'container_number' => $validated['container_number'] ?? $order->containerNum ?? '',
                'shipper_name' => $validated['shipper_name'] ?? $order->shipperName ?? '',
                'consignee_name' => $validated['consignee_name'] ?? $order->recName ?? '',
                'checker_notes' => $validated['checker_notes'],
                'checker_name' => $validated['checker_name'],
                'receiver_name' => $validated['receiver_name'],
                'release_date' => $validated['release_date'],
                'created_by' => Auth::id(),
                'created_by_name' => $createdByName ?: 'Unknown User',
            ]);

            // Create gate pass items
            foreach ($validated['items'] as $item) {
                $remainingQuantity = $item['total_quantity'] - $item['released_quantity'];
                
                GatePassItem::create([
                    'gate_pass_id' => $gatePass->id,
                    'item_description' => $item['item_description'],
                    'total_quantity' => $item['total_quantity'],
                    'unit' => $item['unit'],
                    'released_quantity' => $item['released_quantity'],
                    'remaining_quantity' => $remainingQuantity,
                ]);
            }

            DB::commit();

            return redirect()->route('gatepass.show', $gatePass->id)
                ->with('success', 'Gate Pass created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create gate pass: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified gate pass (for printing)
     */
    public function show($id)
    {
        $this->checkPermission('access');

        $gatePass = GatePass::with(['order', 'items'])->findOrFail($id);
        return view('gatepass.show', compact('gatePass'));
    }

    /**
     * Show the form for editing the specified gate pass
     */
    public function edit($id)
    {
        $this->checkPermission('edit');

        $gatePass = GatePass::with(['order', 'items'])->findOrFail($id);
        return view('gatepass.edit', compact('gatePass'));
    }

    /**
     * Update the specified gate pass in storage
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');

        $gatePass = GatePass::findOrFail($id);

        $validated = $request->validate([
            'gate_pass_no' => 'required|string',
            'checker_notes' => 'nullable|string',
            'checker_name' => 'nullable|string',
            'receiver_name' => 'nullable|string',
            'release_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.total_quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.released_quantity' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Update gate pass
            $gatePass->update([
                'gate_pass_no' => $validated['gate_pass_no'],
                'checker_notes' => $validated['checker_notes'],
                'checker_name' => $validated['checker_name'],
                'receiver_name' => $validated['receiver_name'],
                'release_date' => $validated['release_date'],
            ]);

            // Delete old items and create new ones
            $gatePass->items()->delete();

            foreach ($validated['items'] as $item) {
                $remainingQuantity = $item['total_quantity'] - $item['released_quantity'];
                
                GatePassItem::create([
                    'gate_pass_id' => $gatePass->id,
                    'item_description' => $item['item_description'],
                    'total_quantity' => $item['total_quantity'],
                    'unit' => $item['unit'],
                    'released_quantity' => $item['released_quantity'],
                    'remaining_quantity' => $remainingQuantity,
                ]);
            }

            DB::commit();

            return redirect()->route('gatepass.show', $gatePass->id)
                ->with('success', 'Gate Pass updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update gate pass: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified gate pass from storage
     */
    public function destroy($id)
    {
        $this->checkPermission('delete');

        try {
            $gatePass = GatePass::findOrFail($id);
            $orderId = $gatePass->order_id;
            $gatePass->delete();

            return redirect()->route('gatepass.index')
                ->with('success', 'Gate Pass deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete gate pass: ' . $e->getMessage()]);
        }
    }

    /**
     * Display summary report for a specific BL and Container
     */
    public function summary(Request $request)
    {
        $this->checkPermission('access');

        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return back()->withErrors(['error' => 'Order ID is required']);
        }

        $order = Order::with(['parcels', 'gatePasses' => function($query) {
            $query->orderBy('release_date', 'asc');
        }, 'gatePasses.items'])->findOrFail($orderId);
        
        // Calculate total released and remaining quantities
        $summary = $this->calculateReleaseSummary($order);

        return view('gatepass.summary', compact('order', 'summary'));
    }

    /**
     * Calculate release summary for an order
     */
    private function calculateReleaseSummary($order)
    {
        $summary = [];

        // Helper to normalize keys so minor text differences won't break matching
        $normalizeKey = function ($description, $unit) {
            $desc = strtolower(trim(preg_replace('/\s+/', ' ', (string) $description)));
            $unt  = strtolower(trim(preg_replace('/\s+/', ' ', (string) $unit)));
            return $desc . '|' . $unt;
        };

        // Get all parcels from the order
        foreach ($order->parcels as $parcel) {
            $key = $normalizeKey($parcel->itemName, $parcel->unit);
            
            if (!isset($summary[$key])) {
                $summary[$key] = [
                    'item_description' => $parcel->itemName,
                    'unit' => $parcel->unit,
                    'total_quantity' => 0,
                    'released_quantity' => 0,
                    'remaining_quantity' => 0,
                ];
            }

            $summary[$key]['total_quantity'] += $parcel->quantity;
        }

        // Calculate released quantities from all gate passes
        foreach ($order->gatePasses as $gatePass) {
            foreach ($gatePass->items as $item) {
                $key = $normalizeKey($item->item_description, $item->unit);

                // If the exact key isn't present due to label differences, try a more lenient match
                if (!isset($summary[$key])) {
                    // Attempt fallback: find first matching parcel key with same normalized description regardless of unit
                    $descOnly = strtolower(trim(preg_replace('/\s+/', ' ', (string) $item->item_description)));
                    foreach ($summary as $sumKey => $sumRow) {
                        $sumDescNorm = strtolower(trim(preg_replace('/\s+/', ' ', (string) $sumRow['item_description'])));
                        if ($sumDescNorm === $descOnly) { $key = $sumKey; break; }
                    }
                }

                if (isset($summary[$key])) {
                    $summary[$key]['released_quantity'] += $item->released_quantity;
                }
            }
        }

        // Calculate remaining quantities
        foreach ($summary as $key => &$item) {
            $item['remaining_quantity'] = $item['total_quantity'] - $item['released_quantity'];
        }

        return array_values($summary);
    }

    /**
     * Get release summary data for AJAX requests
     */
    public function getReleaseSummary($orderId)
    {
        $this->checkPermission('access');

        $order = Order::with(['parcels', 'gatePasses.items'])->findOrFail($orderId);
        $summary = $this->calculateReleaseSummary($order);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'orderId' => $order->orderId,
                'containerNum' => $order->containerNum,
                'shipperName' => $order->shipperName,
                'recName' => $order->recName,
            ],
            'summary' => $summary,
        ]);
    }

    /**
     * Display list of ships with voyages
     */
    public function unreleasedShips()
    {
        $this->checkPermission('access');

        // Get all distinct ships with their voyages (exclude M/V SAVER STAR)
        $shipsData = Order::select('shipNum', 'voyageNum', 'origin', 'destination')
            ->where('shipNum', '!=', 'SAVER')
            ->distinct()
            ->orderBy('shipNum', 'asc')
            ->orderBy('voyageNum', 'desc')
            ->get()
            ->groupBy('shipNum');

        return view('gatepass.unreleased-ships', compact('shipsData'));
    }

    /**
     * Display all items (released and unreleased) for a specific ship and voyage
     */
    public function unreleasedByVoyage($shipNum, $voyageNum)
    {
        $this->checkPermission('access');

        // Get all orders for this ship and voyage with their gate passes
        $orders = Order::with(['parcels', 'gatePasses.items'])
            ->where('shipNum', $shipNum)
            ->where('voyageNum', $voyageNum)
            ->orderBy('orderId', 'asc')
            ->get();

        // Calculate release status for each order
        $allOrders = [];
        
        foreach ($orders as $order) {
            $releaseInfo = $this->calculateOrderReleaseStatus($order);
            
            // Include ALL orders (both released and unreleased)
            $allOrders[] = [
                'order' => $order,
                'releaseStatus' => $releaseInfo['status'],
                'unreleasedItems' => $releaseInfo['unreleased_items'],
                'summary' => $releaseInfo['summary'],
            ];
        }

        return view('gatepass.unreleased-voyage', compact('shipNum', 'voyageNum', 'allOrders'));
    }

    /**
     * Calculate release status for an order
     */
    private function calculateOrderReleaseStatus($order)
    {
        if ($order->gatePasses->count() === 0) {
            return [
                'status' => 'NOT RELEASED',
                'unreleased_items' => $order->parcels->count(),
                'summary' => $this->calculateReleaseSummary($order),
            ];
        }

        // Calculate total vs released
        $totalItems = [];
        $releasedItems = [];

        $normalizeKey = function ($description, $unit) {
            $desc = strtolower(trim(preg_replace('/\s+/', ' ', (string) $description)));
            $unt  = strtolower(trim(preg_replace('/\s+/', ' ', (string) $unit)));
            return $desc . '|' . $unt;
        };
        
        // Get total from parcels
        foreach ($order->parcels as $parcel) {
            $key = $normalizeKey($parcel->itemName, $parcel->unit);
            if (!isset($totalItems[$key])) {
                $totalItems[$key] = 0;
            }
            $totalItems[$key] += $parcel->quantity;
        }
        
        // Get released from gate passes
        foreach ($order->gatePasses as $gatePass) {
            foreach ($gatePass->items as $item) {
                $key = $normalizeKey($item->item_description, $item->unit);
                if (!isset($releasedItems[$key])) {
                    $releasedItems[$key] = 0;
                }

                // If no perfect key match exists in totals due to minor differences, try to align by description only
                if (!array_key_exists($key, $totalItems)) {
                    $descOnly = strtolower(trim(preg_replace('/\s+/', ' ', (string) $item->item_description)));
                    foreach (array_keys($totalItems) as $tKey) {
                        [$tDescNorm] = explode('|', $tKey, 2);
                        if ($tDescNorm === $descOnly) { $key = $tKey; break; }
                    }
                }

                $releasedItems[$key] = ($releasedItems[$key] ?? 0) + $item->released_quantity;
            }
        }
        
        // Determine status
        $allReleased = true;
        $unreleasedCount = 0;
        
        foreach ($totalItems as $key => $totalQty) {
            $releasedQty = $releasedItems[$key] ?? 0;
            if ($releasedQty < $totalQty) {
                $allReleased = false;
                $unreleasedCount++;
            }
        }
        
        $status = $allReleased ? 'RELEASED' : 'PARTIAL RELEASED';
        
        return [
            'status' => $status,
            'unreleased_items' => $unreleasedCount,
            'summary' => $this->calculateReleaseSummary($order),
        ];
    }
}
