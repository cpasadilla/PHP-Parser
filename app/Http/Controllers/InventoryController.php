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
    $customers = \App\Models\Customer::with('subAccounts')->get();
    $subAccounts = \App\Models\SubAccount::with('mainAccount')->get();
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
            'in' => 'nullable|numeric',
            'out' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'or_ar' => 'nullable|string',
            'dr_number' => 'nullable|string',
            'onsite_in' => 'nullable|numeric',
            'actual_out' => 'nullable|numeric',
            'onsite_balance' => 'nullable|numeric',
        ]);
        // Determine customer type
        if (str_starts_with($data['customer_id'], 'main-')) {
            $data['customer_type'] = 'main';
            $data['customer_id'] = str_replace('main-', '', $data['customer_id']);
        } else {
            $data['customer_type'] = 'sub';
            $data['customer_id'] = str_replace('sub-', '', $data['customer_id']);
        }
        \App\Models\InventoryEntry::create($data);
        return redirect()->route('inventory')->with('success', 'Inventory entry saved!');
    }
}
