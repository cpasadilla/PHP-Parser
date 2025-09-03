<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    public function dailyCashCollectionTrading()
    {
        return view('accounting.daily-cash-collection.trading');
    }

    public function dailyCashCollectionShipping()
    {
        return view('accounting.daily-cash-collection.shipping');
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
}
