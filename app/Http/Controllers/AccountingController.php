<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyCashCollectionEntry;
use App\Models\Customer;
use App\Models\SubAccount;

class AccountingController extends Controller
{
    /**
     * Display the accounting index page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('accounting.index');
    }

    // Daily Cash Collection Reports
    public function dailyCashCollectionTrading(Request $request)
    {
        $query = DailyCashCollectionEntry::where('type', 'trading');
        
        // Filter by date if provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('entry_date', $request->date);
        }
        
        $entries = $query->orderBy('entry_date', 'desc')->get();
        $selectedDate = $request->date;
        
        return view('accounting.daily-cash-collection.trading', compact('entries', 'selectedDate'));
    }

    public function dailyCashCollectionShipping()
    {
        $entries = DailyCashCollectionEntry::where('type', 'shipping')
            ->orderBy('entry_date', 'desc')
            ->get();
            
        return view('accounting.daily-cash-collection.shipping', compact('entries'));
    }

    public function dailyCashCollectionTradingPrint(Request $request)
    {
        $query = DailyCashCollectionEntry::where('type', 'trading');
        
        // Filter by date if provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('entry_date', $request->date);
        }
        
        $entries = $query->orderBy('entry_date', 'asc')->get();
        $selectedDate = $request->date;
            
        return view('accounting.daily-cash-collection.trading-print', compact('entries', 'selectedDate'));
    }

    public function dailyCashCollectionShippingPrint()
    {
        $entries = DailyCashCollectionEntry::where('type', 'shipping')
            ->orderBy('entry_date', 'asc')
            ->get();
            
        return view('accounting.daily-cash-collection.shipping-print', compact('entries'));
    }

    // Monthly Cash Receipt Journals
    public function monthlyCashReceiptTrading()
    {
        return view('accounting.monthly-cash-receipt.trading');
    }

    public function monthlyCashReceiptShipping()
    {
        return view('accounting.monthly-cash-receipt.shipping');
    }

    // Financial Statement Trading
    public function financialStatementTradingPreTrialBalance()
    {
        return view('accounting.financial-statement.trading.pre-trial-balance');
    }

    public function financialStatementTradingTrialBalance()
    {
        return view('accounting.financial-statement.trading.trial-balance');
    }

    public function financialStatementTradingBalanceSheet()
    {
        return view('accounting.financial-statement.trading.balance-sheet');
    }

    public function financialStatementTradingIncomeStatement()
    {
        return view('accounting.financial-statement.trading.income-statement');
    }

    public function financialStatementTradingWorkSheet()
    {
        return view('accounting.financial-statement.trading.work-sheet');
    }

    public function financialStatementTradingWorkingTrialBalance()
    {
        return view('accounting.financial-statement.trading.working-trial-balance');
    }

    // Financial Statement Shipping
    public function financialStatementShippingPreTrialBalance()
    {
        return view('accounting.financial-statement.shipping.pre-trial-balance');
    }

    public function financialStatementShippingTrialBalance()
    {
        return view('accounting.financial-statement.shipping.trial-balance');
    }

    public function financialStatementShippingBalanceSheet()
    {
        return view('accounting.financial-statement.shipping.balance-sheet');
    }

    public function financialStatementShippingIncomeStatement()
    {
        return view('accounting.financial-statement.shipping.income-statement');
    }

    public function financialStatementShippingAdminExpenses()
    {
        return view('accounting.financial-statement.shipping.admin-expenses');
    }

    public function financialStatementShippingEverwinStar1()
    {
        return view('accounting.financial-statement.shipping.everwin-star-1');
    }

    public function financialStatementShippingEverwinStar2()
    {
        return view('accounting.financial-statement.shipping.everwin-star-2');
    }

    public function financialStatementShippingEverwinStar3()
    {
        return view('accounting.financial-statement.shipping.everwin-star-3');
    }

    public function financialStatementShippingEverwinStar4()
    {
        return view('accounting.financial-statement.shipping.everwin-star-4');
    }

    public function financialStatementShippingEverwinStar5()
    {
        return view('accounting.financial-statement.shipping.everwin-star-5');
    }

    public function financialStatementShippingWorkSheet()
    {
        return view('accounting.financial-statement.shipping.work-sheet');
    }

    public function financialStatementShippingWorkingTrialBalance()
    {
        return view('accounting.financial-statement.shipping.working-trial-balance');
    }

    // General Journal
    public function generalJournalFullyDepreciatedPpe()
    {
        return view('accounting.general-journal.fully-depreciated-ppe');
    }

    public function generalJournalScheduleDepreciation()
    {
        return view('accounting.general-journal.schedule-depreciation');
    }

    public function generalJournalIndex()
    {
        return view('accounting.general-journal.index');
    }

    public function generalJournalCheckDisbursementTrading()
    {
        return view('accounting.general-journal.check-disbursement.trading');
    }

    public function generalJournalCheckDisbursementShipping()
    {
        return view('accounting.general-journal.check-disbursement.shipping');
    }

    public function generalJournalCashDisbursement()
    {
        return view('accounting.general-journal.cash-disbursement');
    }

    // Additional Accounting Reports
    public function breakdownOfReceivables()
    {
        return view('accounting.breakdown-of-receivables');
    }

    public function cashOnHandRegister()
    {
        return view('accounting.cash-on-hand-register');
    }

    // Daily Cash Collection CRUD Methods
    public function storeCashCollectionEntry(Request $request)
    {
        $request->validate([
            'type' => 'required|in:trading,shipping',
            'entry_date' => 'required|date',
            'dccr_number' => 'nullable|string|max:255',
            'customer_name' => 'required|string|max:255',
            'ar' => 'nullable|string|max:255',
            'or' => 'nullable|string|max:255',
            'gravel_sand' => 'nullable|numeric|min:0',
            'chb' => 'nullable|numeric|min:0',
            'other_income_cement' => 'nullable|numeric|min:0',
            'other_income_df' => 'nullable|numeric|min:0',
            'others' => 'nullable|numeric|min:0',
            'interest' => 'nullable|numeric|min:0',
            'vessel' => 'nullable|string|max:255',
            'container_parcel' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'remark' => 'nullable|string'
        ]);

        // Calculate total for trading entries
        $total = 0;
        if ($request->type === 'trading') {
            $total = ($request->gravel_sand ?? 0) + 
                    ($request->chb ?? 0) + 
                    ($request->other_income_cement ?? 0) + 
                    ($request->other_income_df ?? 0) + 
                    ($request->others ?? 0) + 
                    ($request->interest ?? 0);
        } else {
            $total = $request->total ?? 0;
        }

        DailyCashCollectionEntry::create([
            'type' => $request->type,
            'entry_date' => $request->entry_date,
            'dccr_number' => $request->dccr_number,
            'ar' => $request->ar,
            'or' => $request->or,
            'customer_name' => $request->customer_name,
            'customer_id' => $request->customer_id,
            'gravel_sand' => $request->gravel_sand ?? 0,
            'chb' => $request->chb ?? 0,
            'other_income_cement' => $request->other_income_cement ?? 0,
            'other_income_df' => $request->other_income_df ?? 0,
            'others' => $request->others ?? 0,
            'interest' => $request->interest ?? 0,
            'vessel' => $request->vessel,
            'container_parcel' => $request->container_parcel,
            'payment_method' => $request->payment_method,
            'status' => $request->status,
            'total' => $total,
            'remark' => $request->remark
        ]);

        return response()->json(['success' => true, 'message' => 'Entry created successfully']);
    }

    public function updateCashCollectionEntry(Request $request, $id)
    {
        $entry = DailyCashCollectionEntry::findOrFail($id);

        $request->validate([
            'entry_date' => 'required|date',
            'dccr_number' => 'nullable|string|max:255',
            'customer_name' => 'required|string|max:255',
            'ar' => 'nullable|string|max:255',
            'or' => 'nullable|string|max:255',
            'gravel_sand' => 'nullable|numeric|min:0',
            'chb' => 'nullable|numeric|min:0',
            'other_income_cement' => 'nullable|numeric|min:0',
            'other_income_df' => 'nullable|numeric|min:0',
            'others' => 'nullable|numeric|min:0',
            'interest' => 'nullable|numeric|min:0',
            'vessel' => 'nullable|string|max:255',
            'container_parcel' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'remark' => 'nullable|string'
        ]);

        // Calculate total for trading entries
        $total = 0;
        if ($entry->type === 'trading') {
            $total = ($request->gravel_sand ?? 0) + 
                    ($request->chb ?? 0) + 
                    ($request->other_income_cement ?? 0) + 
                    ($request->other_income_df ?? 0) + 
                    ($request->others ?? 0) + 
                    ($request->interest ?? 0);
        } else {
            $total = $request->total ?? 0;
        }

        $entry->update([
            'entry_date' => $request->entry_date,
            'dccr_number' => $request->dccr_number,
            'ar' => $request->ar,
            'or' => $request->or,
            'customer_name' => $request->customer_name,
            'customer_id' => $request->customer_id,
            'gravel_sand' => $request->gravel_sand ?? 0,
            'chb' => $request->chb ?? 0,
            'other_income_cement' => $request->other_income_cement ?? 0,
            'other_income_df' => $request->other_income_df ?? 0,
            'others' => $request->others ?? 0,
            'interest' => $request->interest ?? 0,
            'vessel' => $request->vessel,
            'container_parcel' => $request->container_parcel,
            'payment_method' => $request->payment_method,
            'status' => $request->status,
            'total' => $total,
            'remark' => $request->remark
        ]);

        return response()->json(['success' => true, 'message' => 'Entry updated successfully']);
    }

    public function getCashCollectionEntry($id)
    {
        $entry = DailyCashCollectionEntry::findOrFail($id);
        return response()->json(['success' => true, 'entry' => $entry]);
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->input('q');

        // Search in Customers (Main Accounts)
        $customers = Customer::where('first_name', 'LIKE', "%$query%")
            ->orWhere('last_name', 'LIKE', "%$query%")
            ->orWhere('company_name', 'LIKE', "%$query%")
            ->selectRaw("id,
                COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name,
                IFNULL(NULLIF(phone, ''), '') AS phone")
            ->get();

        // Search in SubAccounts
        $subAccounts = SubAccount::where('first_name', 'LIKE', "%$query%")
            ->orWhere('last_name', 'LIKE', "%$query%")
            ->orWhere('company_name', 'LIKE', "%$query%")
            ->selectRaw("sub_account_number AS id,
                COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name,
                IFNULL(NULLIF(phone, ''), '') AS phone")
            ->get();

        // Merge the results
        $allCustomers = $customers->merge($subAccounts);

        return response()->json($allCustomers);
    }
}
