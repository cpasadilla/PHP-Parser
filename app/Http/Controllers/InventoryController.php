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
                // If previous balance is negative and IN is provided, treat IN as a reset point
                if ($previousBalance < 0 && $inValue > 0) {
                    $data['balance'] = $inValue - $outValue;
                } else {
                    $data['balance'] = $previousBalance + $inValue - $outValue;
                }
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
        // Recalculate onsite balances for this item
        $this->recalculateOnsiteBalances($data['item']);
        // Recalculate main balances for this item
        $this->recalculateBalances($data['item']);
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
        // and set the balance to exactly this amount (replacing any previous balance)
        $data['in'] = $data['balance'];
        // Balance stays as entered - it's the new starting point, not added to previous
        $data['onsite_in'] = $data['onsite_balance'];
        // Onsite balance stays as entered - it's the new starting point, not added to previous

        // Handle hollowblock sizes for starting balance
        if ($data['item'] === 'HOLLOWBLOCKS' && isset($data['hollowblock_size'])) {
            $size = $data['hollowblock_size'];

            // Set the appropriate hollowblock size fields - set to the starting balance amount
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

        // Recalculate onsite balances for this item
        $this->recalculateOnsiteBalances($data['item']);
        // Recalculate main balances for this item
        $this->recalculateBalances($data['item']);

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
            // For hollowblocks, get the latest entry for each size separately
            $latest4Inch = \App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
                ->where('hollowblock_size', '4_inch')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $latest5Inch = \App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
                ->where('hollowblock_size', '5_inch')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $latest6Inch = \App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
                ->where('hollowblock_size', '6_inch')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
            
            return [
                'balance' => $lastEntry ? $lastEntry->balance : 0,
                'onsite_balance' => $lastEntry ? $lastEntry->onsite_balance : 0,
                'hollowblock_4_inch_balance' => $latest4Inch ? $latest4Inch->hollowblock_4_inch_balance : 0,
                'hollowblock_5_inch_balance' => $latest5Inch ? $latest5Inch->hollowblock_5_inch_balance : 0,
                'hollowblock_6_inch_balance' => $latest6Inch ? $latest6Inch->hollowblock_6_inch_balance : 0,
            ];
        }
            
        return [
            'balance' => $lastEntry ? $lastEntry->balance : 0,
            'onsite_balance' => $lastEntry ? $lastEntry->onsite_balance : 0,
        ];
    }
    
    /**
     * Get the balance of the entry that comes before the given entry.
     */
    private function getPreviousEntryBalance($entry)
    {
        $previousEntry = \App\Models\InventoryEntry::where('item', $entry->item)
            ->where(function($query) use ($entry) {
                $query->where('date', '<', $entry->date)
                    ->orWhere(function($q) use ($entry) {
                        $q->where('date', '=', $entry->date)
                          ->where('created_at', '<', $entry->created_at);
                    });
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $previousEntry ? $previousEntry->balance : 0;
    }
    
    /**
     * Process hollowblock balances for separate size tracking.
     */
    private function processHollowblockBalances(&$data)
    {
        $currentBalances = $this->getCurrentBalances('HOLLOWBLOCKS');
        
        // Debug logging
        \Log::info('Processing Hollowblock Balances', [
            'data_received' => $data,
            'current_balances' => $currentBalances
        ]);
        
        // For HOLLOWBLOCKS updates, we need to handle the specific size being updated
        $hollowblockSize = $data['hollowblock_size'] ?? '';
        
        if ($hollowblockSize) {
            // Clear all size fields first
            $data['hollowblock_4_inch_in'] = 0;
            $data['hollowblock_4_inch_out'] = 0;
            $data['hollowblock_5_inch_in'] = 0;
            $data['hollowblock_5_inch_out'] = 0;
            $data['hollowblock_6_inch_in'] = 0;
            $data['hollowblock_6_inch_out'] = 0;
            
            // Set the values for the specific size being updated
            $inValue = floatval($data['in'] ?? 0);
            $outValue = floatval($data['out'] ?? 0);
            
            $sizeFieldIn = 'hollowblock_' . $hollowblockSize . '_in';
            $sizeFieldOut = 'hollowblock_' . $hollowblockSize . '_out';
            $sizeFieldBalance = 'hollowblock_' . $hollowblockSize . '_balance';
            
            $data[$sizeFieldIn] = $inValue;
            $data[$sizeFieldOut] = $outValue;
            
            // Check if a manual balance was provided for this specific size
            if (isset($data[$sizeFieldBalance]) && $data[$sizeFieldBalance] !== null && $data[$sizeFieldBalance] !== '') {
                // Use the manually provided balance
                $data[$sizeFieldBalance] = floatval($data[$sizeFieldBalance]);
                \Log::info('Using manual balance for ' . $hollowblockSize, ['balance' => $data[$sizeFieldBalance]]);
            } else {
                // Calculate balance based on previous balance + IN - OUT
                $currentSizeBalance = $currentBalances['hollowblock_' . $hollowblockSize . '_balance'] ?? 0;
                // If previous balance is negative and IN is provided, treat IN as a reset point
                if ($currentSizeBalance < 0 && $inValue > 0) {
                    $data[$sizeFieldBalance] = $inValue - $outValue;
                } else {
                    $data[$sizeFieldBalance] = $currentSizeBalance + $inValue - $outValue;
                }
                \Log::info('Calculated balance for ' . $hollowblockSize, ['balance' => $data[$sizeFieldBalance]]);
            }
            
            // Set balance fields for other sizes to their current values
            foreach (['4_inch', '5_inch', '6_inch'] as $size) {
                if ($size !== $hollowblockSize) {
                    $balanceField = 'hollowblock_' . $size . '_balance';
                    $data[$balanceField] = $currentBalances[$balanceField] ?? 0;
                }
            }
        } else {
            // Fallback to original logic if no specific size
            // Calculate balances for each size
            if (isset($data['hollowblock_4_inch_in']) || isset($data['hollowblock_4_inch_out'])) {
                $previousBalance = $currentBalances['hollowblock_4_inch_balance'];
                $inValue = floatval($data['hollowblock_4_inch_in'] ?? 0);
                $outValue = floatval($data['hollowblock_4_inch_out'] ?? 0);
                
                // If balance is provided directly, use it; otherwise calculate
                if (isset($data['hollowblock_4_inch_balance']) && $data['hollowblock_4_inch_balance'] !== null) {
                    $data['hollowblock_4_inch_balance'] = floatval($data['hollowblock_4_inch_balance']);
                } else {
                    // If previous balance is negative and IN is provided, treat IN as a reset point
                    if ($previousBalance < 0 && $inValue > 0) {
                        $data['hollowblock_4_inch_balance'] = $inValue - $outValue;
                    } else {
                        $data['hollowblock_4_inch_balance'] = $previousBalance + $inValue - $outValue;
                    }
                }
            }
            
            if (isset($data['hollowblock_5_inch_in']) || isset($data['hollowblock_5_inch_out'])) {
                $previousBalance = $currentBalances['hollowblock_5_inch_balance'];
                $inValue = floatval($data['hollowblock_5_inch_in'] ?? 0);
                $outValue = floatval($data['hollowblock_5_inch_out'] ?? 0);
                
                // If balance is provided directly, use it; otherwise calculate
                if (isset($data['hollowblock_5_inch_balance']) && $data['hollowblock_5_inch_balance'] !== null) {
                    $data['hollowblock_5_inch_balance'] = floatval($data['hollowblock_5_inch_balance']);
                } else {
                    // If previous balance is negative and IN is provided, treat IN as a reset point
                    if ($previousBalance < 0 && $inValue > 0) {
                        $data['hollowblock_5_inch_balance'] = $inValue - $outValue;
                    } else {
                        $data['hollowblock_5_inch_balance'] = $previousBalance + $inValue - $outValue;
                    }
                }
            }
            
            if (isset($data['hollowblock_6_inch_in']) || isset($data['hollowblock_6_inch_out'])) {
                $previousBalance = $currentBalances['hollowblock_6_inch_balance'];
                $inValue = floatval($data['hollowblock_6_inch_in'] ?? 0);
                $outValue = floatval($data['hollowblock_6_inch_out'] ?? 0);
                
                // If balance is provided directly, use it; otherwise calculate
                if (isset($data['hollowblock_6_inch_balance']) && $data['hollowblock_6_inch_balance'] !== null) {
                    $data['hollowblock_6_inch_balance'] = floatval($data['hollowblock_6_inch_balance']);
                } else {
                    // If previous balance is negative and IN is provided, treat IN as a reset point
                    if ($previousBalance < 0 && $inValue > 0) {
                        $data['hollowblock_6_inch_balance'] = $inValue - $outValue;
                    } else {
                        $data['hollowblock_6_inch_balance'] = $previousBalance + $inValue - $outValue;
                    }
                }
            }
        }
        
        // Calculate overall balance for hollowblocks (sum of all sizes)
        $totalBalance = ($data['hollowblock_4_inch_balance'] ?? 0) + 
                       ($data['hollowblock_5_inch_balance'] ?? 0) + 
                       ($data['hollowblock_6_inch_balance'] ?? 0);
        
        $totalIn = floatval($data['hollowblock_4_inch_in'] ?? 0) + 
                   floatval($data['hollowblock_5_inch_in'] ?? 0) + 
                   floatval($data['hollowblock_6_inch_in'] ?? 0);
        $totalOut = floatval($data['hollowblock_4_inch_out'] ?? 0) + 
                    floatval($data['hollowblock_5_inch_out'] ?? 0) + 
                    floatval($data['hollowblock_6_inch_out'] ?? 0);
        
        $data['in'] = $totalIn;
        $data['out'] = $totalOut;
        $data['balance'] = $totalBalance;
        
        \Log::info('Final processed hollowblock data', [
            'final_data' => $data
        ]);
    }

    /**
     * Update an existing inventory entry.
     */
    public function update(Request $request, $id)
    {
        $entry = \App\Models\InventoryEntry::findOrFail($id);

        // Debug logging - check what data is being received
        \Log::info('Inventory Update Request Data', [
            'request_all' => $request->all(),
            'onsite_date_received' => $request->input('onsite_date'),
            'updated_onsite_date_received' => $request->input('updated_onsite_date'),
            'has_onsite_date' => $request->has('onsite_date'),
            'has_updated_onsite_date' => $request->has('updated_onsite_date'),
            'user_id' => auth()->id(),
            'user_roles' => auth()->user()->roles ?? null
        ]);

        // Debug logging
        \Log::info('Inventory Update Request - HOLLOWBLOCK Debug', [
            'entry_id' => $id,
            'item' => $request->input('item'),
            'is_hollowblock' => $request->input('item') === 'HOLLOWBLOCKS',
            'hollowblock_size' => $request->input('hollowblock_size'),
            'balance_input' => $request->input('balance'),
            'hollowblock_4_inch_balance' => $request->input('hollowblock_4_inch_balance'),
            'hollowblock_5_inch_balance' => $request->input('hollowblock_5_inch_balance'),
            'hollowblock_6_inch_balance' => $request->input('hollowblock_6_inch_balance'),
            'all_request_data' => $request->all()
        ]);
        
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
            'updated_onsite_date' => 'nullable|date',
            'pickup_delivery_type' => 'nullable|string',
            'vat_type' => 'nullable|string',
            'hollowblock_size' => 'nullable|string',
            // Hollowblock specific fields
            'hollowblock_4_inch_in' => 'nullable|numeric',
            'hollowblock_4_inch_out' => 'nullable|numeric',
            'hollowblock_4_inch_balance' => 'nullable|numeric',
            'hollowblock_5_inch_in' => 'nullable|numeric',
            'hollowblock_5_inch_out' => 'nullable|numeric',
            'hollowblock_5_inch_balance' => 'nullable|numeric',
            'hollowblock_6_inch_in' => 'nullable|numeric',
            'hollowblock_6_inch_out' => 'nullable|numeric',
            'hollowblock_6_inch_balance' => 'nullable|numeric',
        ]);

        // Determine customer type
        if (str_starts_with($data['customer_id'], 'main-')) {
            $data['customer_type'] = 'main';
            $data['customer_id'] = str_replace('main-', '', $data['customer_id']);
        } else {
            $data['customer_type'] = 'sub';
            $data['customer_id'] = str_replace('sub-', '', $data['customer_id']);
        }
        
        // For UPDATE operations, we don't apply PER BAG conversion
        // because the stored values are already in cubic meters
        // The conversion only happens during CREATE operations
        
        // Handle HOLLOWBLOCKS - process separate size columns
        if ($data['item'] === 'HOLLOWBLOCKS') {
            $this->processHollowblockBalances($data);
        } else {
            // For non-hollowblock items, recalculate balance if not manually provided
            if (!isset($data['balance']) || $data['balance'] === null) {
                $previousBalance = $this->getPreviousEntryBalance($entry);
                $inValue = floatval($data['in'] ?? 0);
                $outValue = floatval($data['out'] ?? 0);
                // If previous balance is negative and IN is provided, treat IN as a reset point
                if ($previousBalance < 0 && $inValue > 0) {
                    $data['balance'] = $inValue - $outValue;
                } else {
                    $data['balance'] = $previousBalance + $inValue - $outValue;
                }
            }
        }

        // Respect manual amount toggle; otherwise calculate
        $isManual = $request->boolean('is_amount_manual') || (($data['pickup_delivery_type'] ?? '') === 'per_bag');
        if ($isManual) {
            $data['amount'] = isset($data['amount']) ? floatval($data['amount']) : ($entry->amount ?? 0);
        } else {
            $data['amount'] = $this->calculateAmount($data);
        }

        $entry->update($data);
        $entry->refresh();
        
        // Debug logging - check what was actually saved
        \Log::info('Inventory Update - After Save', [
            'entry_id' => $id,
            'onsite_date_before' => $entry->getOriginal('onsite_date'),
            'onsite_date_after' => $entry->onsite_date,
            'updated_onsite_date_before' => $entry->getOriginal('updated_onsite_date'),
            'updated_onsite_date_after' => $entry->updated_onsite_date,
            'data_being_saved' => $data,
            'all_entry_attributes' => $entry->getAttributes()
        ]);
        
        \Log::info('Inventory Update Complete - HOLLOWBLOCK Debug', [
            'entry_id' => $id,
            'final_balance' => $entry->balance,
            'final_hollowblock_4_inch_balance' => $entry->hollowblock_4_inch_balance,
            'final_hollowblock_5_inch_balance' => $entry->hollowblock_5_inch_balance,
            'final_hollowblock_6_inch_balance' => $entry->hollowblock_6_inch_balance,
            'data_processed' => $data
        ]);
        // Recalculate onsite balances for this item
        $this->recalculateOnsiteBalances($entry->item);
        // Recalculate main balances for this item
        $this->recalculateBalances($entry->item);
        return redirect()->route('inventory')->with('success', 'Inventory entry updated!');
    }

    /**
     * Recalculate ONSITE BALANCE for all entries of an item/category after a change.
     * For hollowblocks, recalculates onsite balances separately for each size.
     * Preserves the first entry's ONSITE BALANCE and applies the formula to all following entries.
     */
    private function recalculateOnsiteBalances($item)
    {
        if ($item === 'HOLLOWBLOCKS') {
            // For hollowblocks, recalculate onsite balances for each size separately
            $sizes = ['4_inch', '5_inch', '6_inch'];
            foreach ($sizes as $size) {
                $this->recalculateOnsiteBalancesForHollowblockSize($size);
            }
            return;
        }

        // For non-hollowblock items, use the original logic
        $entries = \App\Models\InventoryEntry::where('item', $item)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($entries->isEmpty()) return;

        // Always start from the first entry and propagate balances forward
        $lastBalance = null;
        foreach ($entries as $idx => $entry) {
            if ($idx === 0) {
                // Preserve the first entry's onsite_balance
                $lastBalance = $entry->onsite_balance;
                continue;
            }

            $actualOut = floatval($entry->actual_out ?? 0);
            $inValue = floatval($entry->in ?? 0);
            // If previous balance is negative and IN is provided, treat IN as a reset point
            if (floatval($lastBalance) < 0 && $inValue > 0) {
                $newBalance = $inValue - $actualOut;
            } else {
                $newBalance = floatval($lastBalance) - $actualOut + $inValue;
            }

            // Always update onsite_balance to match the formula
            if ($entry->onsite_balance != $newBalance) {
                $entry->onsite_balance = $newBalance;
                $entry->save();
            }
            $lastBalance = $newBalance;
        }
    }

    /**
     * Recalculate ONSITE BALANCE for a specific hollowblock size.
     */
    private function recalculateOnsiteBalancesForHollowblockSize($size)
    {
        $entries = \App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
            ->where('hollowblock_size', $size)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($entries->isEmpty()) return;

        // Always start from the first entry and propagate balances forward
        $lastBalance = null;
        foreach ($entries as $idx => $entry) {
            if ($idx === 0) {
                // Preserve the first entry's onsite_balance
                $lastBalance = $entry->onsite_balance;
                continue;
            }

            $actualOut = floatval($entry->actual_out ?? 0);
            $inValue = floatval($entry->in ?? 0);
            // If previous balance is negative and IN is provided, treat IN as a reset point
            if (floatval($lastBalance) < 0 && $inValue > 0) {
                $newBalance = $inValue - $actualOut;
            } else {
                $newBalance = floatval($lastBalance) - $actualOut + $inValue;
            }

            // Always update onsite_balance to match the formula
            if ($entry->onsite_balance != $newBalance) {
                $entry->onsite_balance = $newBalance;
                $entry->save();
            }
            $lastBalance = $newBalance;
        }
    }

    /**
     * Recalculate BALANCE for all entries of an item after a change.
     * For hollowblocks, recalculates balances separately for each size.
     * Preserves the first entry's BALANCE and applies the formula to all following entries.
     */
    private function recalculateBalances($item)
    {
        if ($item === 'HOLLOWBLOCKS') {
            // For hollowblocks, recalculate balances for each size separately
            $sizes = ['4_inch', '5_inch', '6_inch'];
            foreach ($sizes as $size) {
                $this->recalculateBalancesForHollowblockSize($size);
            }
            return;
        }

        // For non-hollowblock items, use the original logic
        $entries = \App\Models\InventoryEntry::where('item', $item)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($entries->isEmpty()) return;

        // Always start from the first entry and propagate balances forward
        $lastBalance = null;
        foreach ($entries as $idx => $entry) {
            if ($idx === 0) {
                // Preserve the first entry's balance
                $lastBalance = $entry->balance;
                continue;
            }

            $outValue = floatval($entry->out ?? 0);
            $inValue = floatval($entry->in ?? 0);
            // If previous balance is negative and IN is provided, treat IN as a reset point
            if (floatval($lastBalance) < 0 && $inValue > 0) {
                $newBalance = $inValue - $outValue;
            } else {
                $newBalance = floatval($lastBalance) - $outValue + $inValue;
            }

            // Always update balance to match the formula
            if ($entry->balance != $newBalance) {
                $entry->balance = $newBalance;
                $entry->save();
            }
            $lastBalance = $newBalance;
        }
    }

    /**
     * Recalculate BALANCE for a specific hollowblock size.
     */
    private function recalculateBalancesForHollowblockSize($size)
    {
        $entries = \App\Models\InventoryEntry::where('item', 'HOLLOWBLOCKS')
            ->where('hollowblock_size', $size)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($entries->isEmpty()) return;

        // Define the balance field name for this size
        $balanceField = 'hollowblock_' . $size . '_balance';

        // Always start from the first entry and propagate balances forward
        $lastBalance = null;
        foreach ($entries as $idx => $entry) {
            if ($idx === 0) {
                // Preserve the first entry's size-specific balance
                $lastBalance = $entry->$balanceField;
                continue;
            }

            $outValue = floatval($entry->out ?? 0);
            $inValue = floatval($entry->in ?? 0);
            // If previous balance is negative and IN is provided, treat IN as a reset point
            if (floatval($lastBalance) < 0 && $inValue > 0) {
                $newBalance = $inValue - $outValue;
            } else {
                $newBalance = floatval($lastBalance) - $outValue + $inValue;
            }

            // Always update the size-specific balance to match the formula
            if ($entry->$balanceField != $newBalance) {
                $entry->$balanceField = $newBalance;
                $entry->save();
            }
            $lastBalance = $newBalance;
        }
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
                    $priceMultiplier = ($vatType === 'with_vat') ? 80.08 : 71.50;
                } elseif ($hollowblockSize === '6_inch') {
                    $priceMultiplier = ($vatType === 'with_vat') ? 80.08 : 71.50;
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
