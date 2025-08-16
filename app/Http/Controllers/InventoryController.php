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
            'in' => 'nullable|numeric',
            'out' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
            'onsite_balance' => 'nullable|numeric',
            'pickup_delivery_type' => 'nullable|string',
            'vat_type' => 'nullable|string',
            'hollowblock_size' => 'nullable|string',
        ]);
        
        // Determine customer type
        if (str_starts_with($data['customer_id'], 'main-')) {
            $data['customer_type'] = 'main';
            $data['customer_id'] = str_replace('main-', '', $data['customer_id']);
        } else {
            $data['customer_type'] = 'sub';
            $data['customer_id'] = str_replace('sub-', '', $data['customer_id']);
        }
        
        // Get current balances for this item
        $currentBalances = $this->getCurrentBalances($data['item']);
        
        // Calculate new balance automatically if not provided
        if (!isset($data['balance']) || $data['balance'] === null) {
            $previousBalance = $currentBalances['balance'];
            $inValue = floatval($data['in'] ?? 0);
            $outValue = floatval($data['out'] ?? 0);
            $data['balance'] = $previousBalance + $inValue - $outValue;
        }
        
        // Calculate new onsite balance automatically if not provided
        if (!isset($data['onsite_balance']) || $data['onsite_balance'] === null) {
            $previousOnsiteBalance = $currentBalances['onsite_balance'];
            $inValue = floatval($data['in'] ?? 0);
            $outValue = floatval($data['out'] ?? 0);
            $data['onsite_balance'] = $previousOnsiteBalance + $inValue - $outValue;
        }
        
        // Calculate amount based on item type and conditions
        $data['amount'] = $this->calculateAmount($data);
        
        // Set automatic onsite_date to current date for new entries
        $data['onsite_date'] = now()->format('Y-m-d');
        
        // Initialize other fields, set actual_out to same value as out initially
        $data['or_ar'] = null;
        $data['dr_number'] = null;
        $data['onsite_in'] = $data['in']; // Copy IN to onsite_in
        $data['actual_out'] = $data['out']; // Copy OUT to actual_out
        
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
        ]);
        
        // For starting balance, we put the balance amount in the IN column
        // and leave customer fields empty
        $data['in'] = $data['balance'];
        $data['onsite_in'] = $data['onsite_balance'];
        
        // Don't set customer_id and customer_type for starting balance
        $data['customer_id'] = null;
        $data['customer_type'] = null;
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
            
        return [
            'balance' => $lastEntry ? $lastEntry->balance : 0,
            'onsite_balance' => $lastEntry ? $lastEntry->onsite_balance : 0,
        ];
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
            'or_ar' => 'nullable|string',
            'dr_number' => 'nullable|string',
            'onsite_in' => 'nullable|numeric',
            'actual_out' => 'nullable|numeric',
            'onsite_balance' => 'nullable|numeric',
            'onsite_date' => 'nullable|date',
            'pickup_delivery_type' => 'nullable|string',
            'vat_type' => 'nullable|string',
            'hollowblock_size' => 'nullable|string',
        ]);

        // Determine customer type
        if (str_starts_with($data['customer_id'], 'main-')) {
            $data['customer_type'] = 'main';
            $data['customer_id'] = str_replace('main-', '', $data['customer_id']);
        } else {
            $data['customer_type'] = 'sub';
            $data['customer_id'] = str_replace('sub-', '', $data['customer_id']);
        }

        // Calculate amount based on item type and conditions
        $data['amount'] = $this->calculateAmount($data);

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
