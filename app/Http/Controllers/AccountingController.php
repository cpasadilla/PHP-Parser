<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyCashCollectionEntry;
use App\Models\Customer;
use App\Models\SubAccount;
use App\Models\DailyReportSettings;

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

    public function dailyCashCollectionShipping(Request $request)
    {
        $query = DailyCashCollectionEntry::where('type', 'shipping');
        
        // Filter by date if provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('entry_date', $request->date);
        }
        
        $entries = $query->orderBy('entry_date', 'desc')->get();
        $selectedDate = $request->date;
        
        return view('accounting.daily-cash-collection.shipping', compact('entries', 'selectedDate'));
    }

    public function dailyCashCollectionTradingPrint(Request $request)
    {
        $query = DailyCashCollectionEntry::where('type', 'trading');
        
        // Filter by date if provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('entry_date', $request->date);
        }
        
        $entries = $query->orderBy('entry_date', 'asc')->get();
        $selectedDate = $request->date ?? date('Y-m-d');
        
        // Get report settings for the selected date
        $reportSettings = DailyReportSettings::where('report_date', $selectedDate)
            ->where('report_type', 'trading')
            ->first();
            
        return view('accounting.daily-cash-collection.trading-print', compact('entries', 'selectedDate', 'reportSettings'));
    }

    public function dailyCashCollectionShippingPrint(Request $request)
    {
        $query = DailyCashCollectionEntry::where('type', 'shipping');
        
        // Filter by date if provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('entry_date', $request->date);
        }
        
        $entries = $query->orderBy('entry_date', 'asc')->get();
        $selectedDate = $request->date ?? date('Y-m-d');
        
        // Get report settings for the selected date
        $reportSettings = DailyReportSettings::where('report_date', $selectedDate)
            ->where('report_type', 'shipping')
            ->first();
            
        return view('accounting.daily-cash-collection.shipping-print', compact('entries', 'selectedDate', 'reportSettings'));
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
            'mv_everwin_star_1' => 'nullable|numeric|min:0',
            'mv_everwin_star_2' => 'nullable|numeric|min:0',
            'mv_everwin_star_3' => 'nullable|numeric|min:0',
            'mv_everwin_star_4' => 'nullable|numeric|min:0',
            'mv_everwin_star_5' => 'nullable|numeric|min:0',
            'mv_everwin_star_1_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_2_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_3_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_4_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_5_other' => 'nullable|numeric|min:0',
            'wharfage_payables' => 'nullable|numeric|min:0',
            'remark' => 'nullable|string'
        ]);

        // Calculate total
        $total = 0;
        if ($request->type === 'trading') {
            $total = ($request->gravel_sand ?? 0) + 
                    ($request->chb ?? 0) + 
                    ($request->other_income_cement ?? 0) + 
                    ($request->other_income_df ?? 0) + 
                    ($request->others ?? 0) + 
                    ($request->interest ?? 0);
        } else {
            // Calculate total for shipping entries
            $total = ($request->mv_everwin_star_1 ?? 0) + 
                    ($request->mv_everwin_star_2 ?? 0) + 
                    ($request->mv_everwin_star_3 ?? 0) + 
                    ($request->mv_everwin_star_4 ?? 0) + 
                    ($request->mv_everwin_star_5 ?? 0) + 
                    ($request->mv_everwin_star_1_other ?? 0) + 
                    ($request->mv_everwin_star_2_other ?? 0) + 
                    ($request->mv_everwin_star_3_other ?? 0) + 
                    ($request->mv_everwin_star_4_other ?? 0) + 
                    ($request->mv_everwin_star_5_other ?? 0) + 
                    ($request->wharfage_payables ?? 0) + 
                    ($request->interest ?? 0);
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
            'mv_everwin_star_1' => $request->mv_everwin_star_1 ?? 0,
            'mv_everwin_star_2' => $request->mv_everwin_star_2 ?? 0,
            'mv_everwin_star_3' => $request->mv_everwin_star_3 ?? 0,
            'mv_everwin_star_4' => $request->mv_everwin_star_4 ?? 0,
            'mv_everwin_star_5' => $request->mv_everwin_star_5 ?? 0,
            'mv_everwin_star_1_other' => $request->mv_everwin_star_1_other ?? 0,
            'mv_everwin_star_2_other' => $request->mv_everwin_star_2_other ?? 0,
            'mv_everwin_star_3_other' => $request->mv_everwin_star_3_other ?? 0,
            'mv_everwin_star_4_other' => $request->mv_everwin_star_4_other ?? 0,
            'mv_everwin_star_5_other' => $request->mv_everwin_star_5_other ?? 0,
            'wharfage_payables' => $request->wharfage_payables ?? 0,
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
            'mv_everwin_star_1' => 'nullable|numeric|min:0',
            'mv_everwin_star_2' => 'nullable|numeric|min:0',
            'mv_everwin_star_3' => 'nullable|numeric|min:0',
            'mv_everwin_star_4' => 'nullable|numeric|min:0',
            'mv_everwin_star_5' => 'nullable|numeric|min:0',
            'mv_everwin_star_1_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_2_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_3_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_4_other' => 'nullable|numeric|min:0',
            'mv_everwin_star_5_other' => 'nullable|numeric|min:0',
            'wharfage_payables' => 'nullable|numeric|min:0',
            'remark' => 'nullable|string'
        ]);

        // Calculate total
        $total = 0;
        if ($entry->type === 'trading') {
            $total = ($request->gravel_sand ?? 0) + 
                    ($request->chb ?? 0) + 
                    ($request->other_income_cement ?? 0) + 
                    ($request->other_income_df ?? 0) + 
                    ($request->others ?? 0) + 
                    ($request->interest ?? 0);
        } else {
            // Calculate total for shipping entries
            $total = ($request->mv_everwin_star_1 ?? 0) + 
                    ($request->mv_everwin_star_2 ?? 0) + 
                    ($request->mv_everwin_star_3 ?? 0) + 
                    ($request->mv_everwin_star_4 ?? 0) + 
                    ($request->mv_everwin_star_5 ?? 0) + 
                    ($request->mv_everwin_star_1_other ?? 0) + 
                    ($request->mv_everwin_star_2_other ?? 0) + 
                    ($request->mv_everwin_star_3_other ?? 0) + 
                    ($request->mv_everwin_star_4_other ?? 0) + 
                    ($request->mv_everwin_star_5_other ?? 0) + 
                    ($request->wharfage_payables ?? 0) + 
                    ($request->interest ?? 0);
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
            'mv_everwin_star_1' => $request->mv_everwin_star_1 ?? 0,
            'mv_everwin_star_2' => $request->mv_everwin_star_2 ?? 0,
            'mv_everwin_star_3' => $request->mv_everwin_star_3 ?? 0,
            'mv_everwin_star_4' => $request->mv_everwin_star_4 ?? 0,
            'mv_everwin_star_5' => $request->mv_everwin_star_5 ?? 0,
            'mv_everwin_star_1_other' => $request->mv_everwin_star_1_other ?? 0,
            'mv_everwin_star_2_other' => $request->mv_everwin_star_2_other ?? 0,
            'mv_everwin_star_3_other' => $request->mv_everwin_star_3_other ?? 0,
            'mv_everwin_star_4_other' => $request->mv_everwin_star_4_other ?? 0,
            'mv_everwin_star_5_other' => $request->mv_everwin_star_5_other ?? 0,
            'wharfage_payables' => $request->wharfage_payables ?? 0,
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
        
        if (empty($query)) {
            return response()->json([]);
        }

        // Search in Customers (Main Accounts)
        $customers = Customer::where(function($q) use ($query) {
                // Search for exact matches first
                $q->where('first_name', 'LIKE', "{$query}%")
                  ->orWhere('last_name', 'LIKE', "{$query}%")
                  ->orWhere('company_name', 'LIKE', "{$query}%")
                  // Then search for matches anywhere in the string
                  ->orWhere('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('company_name', 'LIKE', "%{$query}%");
            })
            ->selectRaw("id,
                COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name,
                IFNULL(NULLIF(phone, ''), '') AS phone,
                type")
            ->limit(50) // Limit for better performance
            ->get();

        // Search in SubAccounts
        $subAccounts = SubAccount::where(function($q) use ($query) {
                $q->where('first_name', 'LIKE', "{$query}%")
                  ->orWhere('last_name', 'LIKE', "{$query}%")
                  ->orWhere('company_name', 'LIKE', "{$query}%")
                  ->orWhere('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhere('company_name', 'LIKE', "%{$query}%");
            })
            ->selectRaw("sub_account_number AS id,
                COALESCE(NULLIF(company_name, ''), CONCAT(first_name, ' ', last_name)) AS name,
                IFNULL(NULLIF(phone, ''), '') AS phone,
                'subaccount' AS type")
            ->limit(50)
            ->get();

        // Merge the results
        $allCustomers = $customers->merge($subAccounts);

        // Remove duplicates and sort by relevance
        $uniqueCustomers = $allCustomers->unique('name')->values();

        return response()->json($uniqueCustomers);
    }

    // Daily Report Settings Methods
    public function getReportSettings(Request $request)
    {
        $reportDate = $request->input('report_date');
        $reportType = $request->input('report_type');

        $settings = DailyReportSettings::where('report_date', $reportDate)
            ->where('report_type', $reportType)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function storeReportSettings(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'report_type' => 'required|in:trading,shipping',
            'dccr_number' => 'nullable|string|max:255',
            'add_collection' => 'nullable|numeric|min:0',
            'collected_by_name' => 'nullable|string|max:255',
            'collected_by_title' => 'nullable|string|max:255'
        ]);

        // Find existing record or create new one
        $settings = DailyReportSettings::firstOrNew([
            'report_date' => $request->report_date,
            'report_type' => $request->report_type
        ]);

        // Only update fields that are provided in the request
        if ($request->has('dccr_number')) {
            $settings->dccr_number = $request->dccr_number;
        }
        
        if ($request->has('add_collection')) {
            $settings->add_collection = $request->add_collection;
        }
        
        if ($request->has('collected_by_name')) {
            $settings->collected_by_name = $request->collected_by_name;
        }
        
        if ($request->has('collected_by_title')) {
            $settings->collected_by_title = $request->collected_by_title;
        }

        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully',
            'data' => $settings
        ]);
    }
}
