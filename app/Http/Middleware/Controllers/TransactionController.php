<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('customer_id', Auth::guard('customers')->id())->simplePaginate(10);
        return view('frontend.admin.transaction.index', compact('transactions'));
    }
}
