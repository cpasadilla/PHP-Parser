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
}
