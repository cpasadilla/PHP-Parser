<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display the inventory index page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
    $entries = \App\Models\InventoryEntry::all();
    $customers = \App\Models\Customer::with('subAccounts')
        ->orderByRaw('CASE WHEN company_name IS NOT NULL AND company_name != "" THEN company_name ELSE CONCAT(first_name, " ", last_name) END')
        ->get();
    $subAccounts = \App\Models\SubAccount::with('mainAccount')
        ->orderByRaw('CASE WHEN company_name IS NOT NULL AND company_name != "" THEN company_name ELSE CONCAT(first_name, " ", last_name) END')
        ->get();
    return view('inventory.index', compact('entries', 'customers', 'subAccounts'));
    }

    /**
     * Store a new inventory entry.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'item' => 'required|string',
            'date' => 'required|date',
            'customer_id' => 'required',
            'amount' => 'nullable|numeric',
            'is_amount_manual' => 'nullable|boolean',
            'in' => 'nullable|numeric',
            'out' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
            'onsite_balance' => 'nullable|numeric',
            'pickup_delivery_type' => 'nullable|string',
            'vat_type' => 'nullable|string',
            'hollowblock_size' => 'nullable|string',
            // Hollowblock specific fields
            'hollowblock_4_inch_in' => 'nullable|numeric',
            'hollowblock_4_inch_out' => 'nullable|numeric',
            'hollowblock_5_inch_in' => 'nullable|numeric',
            'hollowblock_5_inch_out' => 'nullable|numeric',
            'hollowblock_6_inch_in' => 'nullable|numeric',
            'hollowblock_6_inch_out' => 'nullable|numeric',
        ]);
        
        // Determine customer type
        if (str_starts_with($data['customer_id'], 'main-')) {
            $data['customer_type'] = 'main';
            $data['customer_id'] = str_replace('main-', '', $data['customer_id']);
        } else {
            $data['customer_type'] = 'sub';
            $data['customer_id'] = str_replace('sub-', '', $data['customer_id']);
        }
        
        // Handle PER BAG conversion: 1 BAG = 0.028 cubic (BAG / 36)
        $pickupDeliveryType = $data['pickup_delivery_type'] ?? '';
        if ($pickupDeliveryType === 'per_bag' && isset($data['out']) && $data['out'] > 0) {
            $data['out_original_bags'] = $data['out']; // Store original bag count
            $data['out'] = $data['out'] / 36; // Convert bags to cubic (1 bag = 0.028 cubic)
        }
        
        // Handle HOLLOWBLOCKS - process separate size columns
        if ($data['item'] === 'HOLLOWBLOCKS') {
            $this->processHollowblockBalances($data);
        } else {
            // Get current balances for this item (non-hollowblock items)
            $currentBalances = $this->getCurrentBalances($data['item']);
            
            // Calculate new balance automatically if not provided
            if (!isset($data['balance']) || $data['balance'] === null) {
                $previousBalance = $currentBalances['balance'];
                $inValue = floatval($data['in'] ?? 0);
                $outValue = floatval($data['out'] ?? 0);
                $data['balance'] = $previousBalance + $inValue - $outValue;
            }
        }
        
        // On create via Add Inventory Entry, keep onsite fields blank so user/admin can set later
        $data['onsite_balance'] = null; // explicitly blank
        
        // Respect manual amount toggle; otherwise calculate
        $isManual = $request->boolean('is_amount_manual') || (($data['pickup_delivery_type'] ?? '') === 'per_bag');
        if ($isManual) {
            $data['amount'] = isset($data['amount']) ? floatval($data['amount']) : 0;
        } else {
            $data['amount'] = $this->calculateAmount($data);
        }
        
        // Do NOT auto-set onsite_date on create; leave it blank until explicitly set
        $data['onsite_date'] = null;
        
        // Initialize other fields, set actual_out to same value as out initially
        $data['or_ar'] = null;
        $data['dr_number'] = null;
        $data['onsite_in'] = $data['in']; // keep as provided or null
        $data['actual_out'] = null; // keep blank on create
        
        \App\Models\InventoryEntry::create($data);
        return redirect()->route('inventory')->with('success', 'Inventory entry saved!');
    }
    
    /**
     * Set starting balance for an item.
     */
    public function setStartingBalance(Request $request)
    {
        $data = $request->validate([
            'item' => 'required|string',
            'date' => 'required|date',
            'ship_number' => 'required|string',
            'voyage_number' => 'required|string',
            'balance' => 'required|numeric|min:0',
            'onsite_balance' => 'required|numeric|min:0',
            'hollowblock_size' => 'nullable|string',
            'hollowblock_4_inch_in' => 'nullable|numeric',
            'hollowblock_5_inch_in' => 'nullable|numeric',
            'hollowblock_6_inch_in' => 'nullable|numeric',
        ]);
        
        // For starting balance, we put the balance amount in the IN column
        // and leave customer fields empty
        $data['in'] = $data['balance'];
        $data['onsite_in'] = $data['onsite_balance'];
        
        // Handle hollowblock sizes for starting balance
        if ($data['item'] === 'HOLLOWBLOCKS' && isset($data['hollowblock_size'])) {
            $size = $data['hollowblock_size'];
            
            // Set the appropriate hollowblock size fields
            if ($size === '4_inch') {
                $data['hollowblock_4_inch_in'] = $data['balance'];
                $data['hollowblock_4_inch_balance'] = $data['balance'];
            } elseif ($size === '5_inch') {
                $data['hollowblock_5_inch_in'] = $data['balance'];
                $data['hollowblock_5_inch_balance'] = $data['balance'];
            } elseif ($size === '6_inch') {
                $data['hollowblock_6_inch_in'] = $data['balance'];
                $data['hollowblock_6_inch_balance'] = $data['balance'];
            }
        }
        
        // For starting balance, avoid FK/user coupling: use safe defaults to prevent DB NOT NULL errors
        // customer_id column may be non-nullable in some deployments. Use 0 as a sentinel value.
        $data['customer_id'] = 0;
        // customer_type column may be non-nullable; use empty string instead of null
        $data['customer_type'] = '';
        $data['is_starting_balance'] = true;
        
        \App\Models\InventoryEntry::create($data);
        return redirect()->route('inventory')->with('success', 'Starting balance set successfully!');
    }
    
    /**
     * Get current balances for an item.
     */
    private function getCurrentBalances($item)
    {
        $lastEntry = \App\Models\InventoryEntry::where('item', $item)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($item === 'HOLLOWBLOCKS') {
            return [
                'balance' => $lastEntry ? $lastEntry->balance : 0,
                'onsite_balance' => $lastEntry ? $lastEntry->onsite_balance : 0,
                'hollowblock_4_inch_balance' => $lastEntry ? $lastEntry->hollowblock_4_inch_balance : 0,
                'hollowblock_5_inch_balance' => $lastEntry ? $lastEntry->hollowblock_5_inch_balance : 0,
                'hollowblock_6_inch_balance' => $lastEntry ? $lastEntry->hollowblock_6_inch_balance : 0,
            ];
        }
            
        return [
            'balance' => $lastEntry ? $lastEntry->balance : 0,
            'onsite_balance' => $lastEntry ? $lastEntry->onsite_balance : 0,
        ];
    }
    
    /**
     * Process hollowblock balances for separate size tracking.
     */
    private function processHollowblockBalances(&$data)
    {
        $currentBalances = $this->getCurrentBalances('HOLLOWBLOCKS');
        
        // Calculate balances for each size
        if (isset($data['hollowblock_4_inch_in']) || isset($data['hollowblock_4_inch_out'])) {
            $previousBalance = $currentBalances['hollowblock_4_inch_balance'];
            $inValue = floatval($data['hollowblock_4_inch_in'] ?? 0);
            $outValue = floatval($data['hollowblock_4_inch_out'] ?? 0);
            $data['hollowblock_4_inch_balance'] = $previousBalance + $inValue - $outValue;
        }
        
        if (isset($data['hollowblock_5_inch_in']) || isset($data['hollowblock_5_inch_out'])) {
            $previousBalance = $currentBalances['hollowblock_5_inch_balance'];
            $inValue = floatval($data['hollowblock_5_inch_in'] ?? 0);
            $outValue = floatval($data['hollowblock_5_inch_out'] ?? 0);
            $data['hollowblock_5_inch_balance'] = $previousBalance + $inValue - $outValue;
        }
        
        if (isset($data['hollowblock_6_inch_in']) || isset($data['hollowblock_6_inch_out'])) {
            $previousBalance = $currentBalances['hollowblock_6_inch_balance'];
            $inValue = floatval($data['hollowblock_6_inch_in'] ?? 0);
            $outValue = floatval($data['hollowblock_6_inch_out'] ?? 0);
            $data['hollowblock_6_inch_balance'] = $previousBalance + $inValue - $outValue;
        }
        
        // Calculate overall balance for hollowblocks (sum of all sizes)
        $previousBalance = $currentBalances['balance'];
        $totalIn = floatval($data['hollowblock_4_inch_in'] ?? 0) + 
                   floatval($data['hollowblock_5_inch_in'] ?? 0) + 
                   floatval($data['hollowblock_6_inch_in'] ?? 0);
        $totalOut = floatval($data['hollowblock_4_inch_out'] ?? 0) + 
                    floatval($data['hollowblock_5_inch_out'] ?? 0) + 
                    floatval($data['hollowblock_6_inch_out'] ?? 0);
        
        $data['in'] = $totalIn;
        $data['out'] = $totalOut;
        $data['balance'] = $previousBalance + $totalIn - $totalOut;
    }

    /**
     * Update an existing inventory entry.
     */
    public function update(Request $request, $id)
    {
        $entry = \App\Models\InventoryEntry::findOrFail($id);
        
        $data = $request->validate([
            'item' => 'required|string',
            'date' => 'required|date',
            'customer_id' => 'required',
            'in' => 'nullable|numeric',
            'out' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'is_amount_manual' => 'nullable|boolean',
            'or_ar' => 'nullable|string',
            'dr_number' => 'nullable|string',
            'onsite_in' => 'nullable|numeric',
            'actual_out' => 'nullable|numeric',
            'onsite_balance' => 'nullable|numeric',
            'onsite_date' => 'nullable|date',
            'pickup_delivery_type' => 'nullable|string',
            'vat_type' => 'nullable|string',
            'hollowblock_size' => 'nullable|string',
            // Hollowblock specific fields
            'hollowblock_4_inch_in' => 'nullable|numeric',
            'hollowblock_4_inch_out' => 'nullable|numeric',
            'hollowblock_5_inch_in' => 'nullable|numeric',
            'hollowblock_5_inch_out' => 'nullable|numeric',
            'hollowblock_6_inch_in' => 'nullable|numeric',
            'hollowblock_6_inch_out' => 'nullable|numeric',
        ]);

        // Determine customer type
        if (str_starts_with($data['customer_id'], 'main-')) {
            $data['customer_type'] = 'main';
            $data['customer_id'] = str_replace('main-', '', $data['customer_id']);
        } else {
            $data['customer_type'] = 'sub';
            $data['customer_id'] = str_replace('sub-', '', $data['customer_id']);
        }
        
        // Handle PER BAG conversion: 1 BAG = 0.028 cubic (BAG / 36)
        $pickupDeliveryType = $data['pickup_delivery_type'] ?? '';
        if ($pickupDeliveryType === 'per_bag' && isset($data['out']) && $data['out'] > 0) {
            $data['out_original_bags'] = $data['out']; // Store original bag count
            $data['out'] = $data['out'] / 36; // Convert bags to cubic (1 bag = 0.028 cubic)
        }
        
        // Handle HOLLOWBLOCKS - process separate size columns
        if ($data['item'] === 'HOLLOWBLOCKS') {
            $this->processHollowblockBalances($data);
        }

        // Respect manual amount toggle; otherwise calculate
        $isManual = $request->boolean('is_amount_manual') || (($data['pickup_delivery_type'] ?? '') === 'per_bag');
        if ($isManual) {
            $data['amount'] = isset($data['amount']) ? floatval($data['amount']) : ($entry->amount ?? 0);
        } else {
            $data['amount'] = $this->calculateAmount($data);
        }

        // Check if user is admin for onsite_date editing
        $user = auth()->user();
        $isAdmin = $user->roles && in_array(strtoupper(trim($user->roles->roles)), ['ADMIN', 'ADMINISTRATOR']);
        
        // If not admin, keep the current onsite_date or set to current date if null
        if (!$isAdmin) {
            $data['onsite_date'] = $entry->onsite_date ?: now()->format('Y-m-d');
        }

        $entry->update($data);
        return redirect()->route('inventory')->with('success', 'Inventory entry updated!');
    }

    /**
     * Calculate amount based on item type and various conditions.
     */
    private function calculateAmount($data)
    {
        $item = $data['item'];
        $outValue = floatval($data['out'] ?? 0);
        $pickupDeliveryType = $data['pickup_delivery_type'] ?? '';
        $vatType = $data['vat_type'] ?? '';
        $hollowblockSize = $data['hollowblock_size'] ?? '';
        
        // Return 0 if no OUT value
        if ($outValue <= 0) {
            return 0;
        }
        
        $priceMultiplier = 0;
        
        switch ($item) {
            case 'SAND S1 M':
                if ($pickupDeliveryType === 'pickup_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4336.20 : 4015.00;
                } elseif ($pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4465.80 : 4135.00;
                } elseif ($pickupDeliveryType === 'delivered_stockpile') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4595.40 : 4255.00;
                }
                break;
                
            case 'VIBRO SAND':
                if ($pickupDeliveryType === 'pickup_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4681.80 : 4335.00;
                } elseif ($pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4811.40 : 4455.00;
                } elseif ($pickupDeliveryType === 'delivered_stockpile') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4941.00 : 4575.00;
                }
                break;
                
            case 'G1 CURRIMAO':
                if ($pickupDeliveryType === 'pickup_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4082.40 : 3780.00;
                } elseif ($pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4212.00 : 3900.00;
                } elseif ($pickupDeliveryType === 'delivered_stockpile') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4341.60 : 4020.00;
                }
                break;
                
            case 'G1 DAMORTIS':
                if ($pickupDeliveryType === 'pickup_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4336.20 : 4015.00;
                } elseif ($pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4465.80 : 4135.00;
                } elseif ($pickupDeliveryType === 'delivered_stockpile') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4595.40 : 4255.00;
                }
                break;
                
            case '3/4 GRAVEL':
                if ($pickupDeliveryType === 'pickup_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4514.40 : 4180.00;
                } elseif ($pickupDeliveryType === 'pickup_stockpile_delivered_pier') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4644.00 : 4300.00;
                } elseif ($pickupDeliveryType === 'delivered_stockpile') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 4773.60 : 4420.00;
                }
                break;
                
            case 'HOLLOWBLOCKS':
                if ($hollowblockSize === '4_inch') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 73.92 : 66.00;
                } elseif ($hollowblockSize === '5_inch') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 80.08 : 71.00;
                } elseif ($hollowblockSize === '6_inch') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 86.24 : 77.00;
                }
                break;
                
            default:
                return 0;
        }
        
        return $outValue * $priceMultiplier;
    }

    /**
     * Delete an inventory entry.
     */
    public function destroy($id)
    {
        try {
            $entry = \App\Models\InventoryEntry::findOrFail($id);
            $entry->delete();
            
            return redirect()->route('inventory')->with('success', 'Inventory entry deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('inventory')->with('error', 'Failed to delete inventory entry.');
        }
    }
}
