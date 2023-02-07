<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionLine;
use Auth;

class StudentController extends Controller
{
    public function index()
    {
        $lbb = [];
        $lbb['transactions'] = Transaction::where('customer_id', Auth::guard('customers')->id())->where('status','=',1)->get();
        
        $line_ids = [];
        $lbb['transaction_detail'] = [];
        foreach($lbb['transactions'] as $transaction){
          $line_ids[] = $transaction->code;
          $lbb['transaction_detail'][$transaction->code] = $transaction;
        }
        
        $lbb['transaction_line'] = TransactionLine::whereIn('transaction_code',$line_ids)->where('item_code','=','SCH')->simplePaginate(10);
        
        return view('frontend.admin.student.index')->with($lbb);
    }
}
